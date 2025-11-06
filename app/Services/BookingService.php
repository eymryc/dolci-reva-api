<?php
namespace App\Services;

use Exception;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Booking;
use App\Models\Residence;
use InvalidArgumentException;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\DB;
use App\Repositories\BookingRepository;

class BookingService
{
	/**
     * @var BookingRepository $bookingRepository
     */
    protected $bookingRepository;
    
    /**
     * @var AvailabilityService $availabilityService
     */
    protected $availabilityService;
    
    /**
     * @var PricingService $pricingService
     */
    protected $pricingService;
    
    /**
     * @var NotificationService $notificationService
     */
    protected $notificationService;

    /**
     * @var PaystackService $paystackService
     */
    protected $paystackService;

    /**
     * Constructor.
     *
     * @param BookingRepository $bookingRepository
     * @param AvailabilityService $availabilityService
     * @param PricingService $pricingService
     * @param NotificationService $notificationService
     * @param PaystackService $paystackService
     */
    public function __construct(
        BookingRepository $bookingRepository,
        AvailabilityService $availabilityService,
        PricingService $pricingService,
        NotificationService $notificationService,
        PaystackService $paystackService
    ) {
        $this->bookingRepository = $bookingRepository;
        $this->availabilityService = $availabilityService;
        $this->pricingService = $pricingService;
        $this->notificationService = $notificationService;
        $this->paystackService = $paystackService;
    }

    /**
     * Get all bookingRepository.
     *
     * @return String
     */
    public function getAll()
    {
        return $this->bookingRepository->all();
    }

    /**
     * Get bookingRepository with pagination.
     *
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\Paginator
     */
    public function getAllWithPagination(int $perPage = 15)
    {
        return $this->bookingRepository->paginate($perPage);
    }

    /**
     * Get bookingRepository by id.
     *
     * @param $id
     * @return String
     */
    public function getById(int $id)
    {
        return $this->bookingRepository->getById($id);
    }

    /**
     * Create a new residence booking.
     *
     * @param array $data
     * @param int $residenceId
     * @return Booking
     */
    public function saveResidenceBooking(array $data, int $residenceId)
    {
        DB::beginTransaction();
        try {

            // Get the residence by ID
            $residence = Residence::findOrFail($residenceId);
            
            // Préparer les données de réservation
            $totalPrice = $this->calculatePrice($residence, $data['start_date'], $data['end_date']);
            $commissionAmount = $this->calculateCommission($totalPrice);
            $ownerAmount = $totalPrice - $commissionAmount;

            // Data
            $bookingData = [
                'customer_id'           => $data['customer_id'],
                'owner_id'              => $residence->owner_id,
                'bookable_type'         => 'App\\Models\\Residence',
                'bookable_id'           => $residenceId,
                'start_date'            => $data['start_date'],
                'end_date'              => $data['end_date'],
                'guests'                => $data['guests'],
                'notes'                 => $data['notes'] ?? null,
                'booking_reference'     => $this->generateBookingReference(),
                'total_price'           => $totalPrice,
                'commission_amount'     => $commissionAmount,
                'owner_amount'          => $ownerAmount,
                'status'                => 'EN_ATTENTE',
                'payment_status'        => 'EN_ATTENTE'
            ];
            
            // Save the booking
            $booking = $this->bookingRepository->save($bookingData);
            
            // Ensure the owner has a wallet and create it if it doesn't exist
            $this->ensureOwnerWallet($residence->owner_id, $ownerAmount, $booking->id);
            
            // Commit the transaction
            DB::commit();

            // Initialize Paystack payment
            $customer = User::findOrFail($data['customer_id']);
            $paymentUrl = $this->initializePaymentForBooking($booking, $customer->email, $totalPrice);

            // Return the booking with payment URL
            return [
                'booking' => $booking,
                'payment_url' => $paymentUrl
            ];
            
        } catch (Exception $e) {
            DB::rollBack();
            report($e);
            throw new InvalidArgumentException('Unable to create residence booking: ' . $e->getMessage());
        }
    }

    /**
     * Create a new hotel booking.
     *
     * @param array $data
     * @param int $hotelId
     * @return Booking
     */
    public function saveHotelBooking(array $data, int $hotelId)
    {
        DB::beginTransaction();
        try {
            $hotel = \App\Models\Hotel::findOrFail($hotelId);
            
            // Préparer les données de réservation
            $totalPrice = $this->calculateHotelPrice($hotel, $data['start_date'], $data['end_date']);
            $commissionAmount = $this->calculateCommission($totalPrice);
            $ownerAmount = $totalPrice - $commissionAmount;
            
            $bookingData = [
                'customer_id' => $data['customer_id'],
                'owner_id' => $hotel->owner_id,
                'bookable_type' => 'App\\Models\\Hotel',
                'bookable_id' => $hotelId,
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'guests' => $data['guests'],
                'notes' => $data['notes'] ?? null,
                'booking_reference' => $this->generateBookingReference(),
                'total_price' => $totalPrice,
                'commission_amount' => $commissionAmount,
                'owner_amount' => $ownerAmount,
                'status' => 'EN_ATTENTE',
                'payment_status' => 'EN_ATTENTE'
            ];
            
            // Créer la réservation
            $booking = $this->bookingRepository->save($bookingData);
            
            // Créer/initialiser le wallet du owner s'il n'en a pas
            $this->ensureOwnerWallet($hotel->owner_id, $ownerAmount, $booking->id);
            
            DB::commit();

            // Initialize Paystack payment
            $customer = \App\Models\User::findOrFail($data['customer_id']);
            $paymentUrl = $this->initializePaymentForBooking($booking, $customer->email, $totalPrice);

            // Return the booking with payment URL
            return [
                'booking' => $booking,
                'payment_url' => $paymentUrl
            ];
            
        } catch (Exception $e) {
            DB::rollBack();
            report($e);
            throw new InvalidArgumentException('Unable to create hotel booking: ' . $e->getMessage());
        }
    }

    /**
     * Confirm a booking.
     *
     * @param array $data
     * @param int $bookingId
     * @return Booking
     */
    public function confirmBooking(array $data, int $bookingId)
    {
        DB::beginTransaction();
        try {
            $booking = $this->bookingRepository->getById($bookingId);
            
            if (!$booking) {
                throw new InvalidArgumentException('Booking not found');
            }
            
            // Vérifier que la réservation peut être confirmée
            if ($booking->status !== 'EN_ATTENTE') {
                throw new InvalidArgumentException('Cette réservation ne peut pas être confirmée.');
            }
            
            // Mettre à jour la réservation
            $updateData = [
                'status' => 'CONFIRME',
                'confirmed_at' => now(),
                'notes' => $data['notes'] ?? $booking->notes
            ];
            
            $booking = $this->bookingRepository->update($updateData, $bookingId);
            
            // Mettre à jour le statut de disponibilité de la résidence
            if ($booking->bookable_type === 'App\\Models\\Residence') {
                $residence = $booking->bookable;
                if ($residence) {
                    $residence->update(['is_available' => false]);
                }
            }
            
            DB::commit();
            return $booking;
            
        } catch (Exception $e) {
            DB::rollBack();
            report($e);
            throw new InvalidArgumentException('Unable to confirm booking: ' . $e->getMessage());
        }
    }

    /**
     * Cancel a booking.
     *
     * @param array $data
     * @param int $bookingId
     * @return Booking
     */
    public function cancelBooking(array $data, int $bookingId)
    {
        DB::beginTransaction();
        try {
            $booking = $this->bookingRepository->getById($bookingId);
            
            if (!$booking) {
                throw new InvalidArgumentException('Booking not found');
            }
            
            // Vérifier que la réservation peut être annulée
            if (in_array($booking->status, ['ANNULE', 'COMPLETE'])) {
                throw new InvalidArgumentException('Cette réservation ne peut pas être annulée.');
            }
            
            // Mettre à jour la réservation
            $updateData = [
                'status' => 'ANNULE',
                'cancelled_at' => now(),
                'cancellation_reason' => $data['cancellation_reason']
            ];
            
            $booking = $this->bookingRepository->update($updateData, $bookingId);
            
            // Remettre la résidence disponible si elle était confirmée
            if ($booking->bookable_type === 'App\\Models\\Residence' && $booking->status === 'CONFIRME') {
                $residence = $booking->bookable;
                if ($residence) {
                    $residence->update(['is_available' => true]);
                }
            }
            
            DB::commit();
            return $booking;
            
        } catch (Exception $e) {
            DB::rollBack();
            report($e);
            throw new InvalidArgumentException('Unable to cancel booking: ' . $e->getMessage());
        }
    }

    /**
     * Complete a booking.
     *
     * @param array $data
     * @param int $bookingId
     * @return Booking
     */
    public function completeBooking(array $data, int $bookingId)
    {
        DB::beginTransaction();
        try {
            $booking = $this->bookingRepository->getById($bookingId);
            
            if (!$booking) {
                throw new InvalidArgumentException('Booking not found');
            }
            
            // Vérifier que la réservation peut être terminée
            if ($booking->status !== 'CONFIRME') {
                throw new InvalidArgumentException('Seules les réservations confirmées peuvent être terminées.');
            }
            
            // Mettre à jour la réservation
            $updateData = [
                'status' => 'COMPLETE',
                'notes' => $data['notes'] ?? $booking->notes
            ];
            
            $booking = $this->bookingRepository->update($updateData, $bookingId);
            
            // Remettre la résidence disponible
            if ($booking->bookable_type === 'App\\Models\\Residence') {
                $residence = $booking->bookable;
                if ($residence) {
                    $residence->update(['is_available' => true]);
                }
            }
            
            DB::commit();
            return $booking;
            
        } catch (Exception $e) {
            DB::rollBack();
            report($e);
            throw new InvalidArgumentException('Unable to complete booking: ' . $e->getMessage());
        }
    }
    
    /**
     * Generate a unique booking reference
     */
    private function generateBookingReference(): string
    {
        do {
            $reference = 'BK' . strtoupper(uniqid());
        } while (Booking::where('booking_reference', $reference)->exists());

        return $reference;
    }

    /**
     * Calculate price for residence booking
     */
    private function calculatePrice($residence, string $startDate, string $endDate): float
    {
        $start = new \DateTime($startDate);
        $end = new \DateTime($endDate);
        $nights = $start->diff($end)->days;
        
        return $residence->price * $nights;
    }

    /**
     * Calculate price for hotel booking
     */
    private function calculateHotelPrice($hotel, string $startDate, string $endDate): float
    {
        $start = new \DateTime($startDate);
        $end = new \DateTime($endDate);
        $nights = $start->diff($end)->days;
        
        // Supposons que l'hôtel a un prix de base par nuit
        $basePrice = $hotel->price ?? 100.00; // Prix par défaut si pas défini
        
        return $basePrice * $nights;
    }

    /**
     * Get bookable item by type and id
     */
    private function getBookable($type, $id)
    {
        $modelClass = "App\\Models\\{$type}";
        
        if (!class_exists($modelClass)) {
            throw new InvalidArgumentException("Invalid bookable type: {$type}");
        }
        
        $bookable = $modelClass::find($id);
        
        if (!$bookable) {
            throw new InvalidArgumentException("Bookable item not found");
        }
        
        return $bookable;
    }

    /**
     * Update bookingRepository data
     * Store to DB if there are no errors.
     *
     * @param array $data
     * @return String
     */
    public function update(array $data, int $id)
    {
        DB::beginTransaction();
        try {
            $bookingRepository = $this->bookingRepository->update($data, $id);
            DB::commit();
            return $bookingRepository;
        } catch (Exception $e) {
            DB::rollBack();
            report($e);
            throw new InvalidArgumentException('Unable to update post data');
        }
    }

    /**
     * Delete bookingRepository by id.
     *
     * @param $id
     * @return String
     */
    public function deleteById(int $id)
    {
        DB::beginTransaction();
        try {
            $bookingRepository = $this->bookingRepository->delete($id);
            DB::commit();
            return $bookingRepository;
        } catch (Exception $e) {
            DB::rollBack();
            report($e);
            throw new InvalidArgumentException('Unable to delete post data');
        }
    }

    /**
     * Create a new restaurant booking.
     *
     * @param array $data
     * @param int $restaurantId
     * @return Booking
     */
    public function saveRestaurantBooking(array $data, int $restaurantId)
    {
        DB::beginTransaction();
        try {
            $restaurant = \App\Models\Restaurant::findOrFail($restaurantId);
            
            // Préparer les données de réservation
            $totalPrice = $this->calculateRestaurantPrice($restaurant, $data);
            $commissionAmount = $this->calculateCommission($totalPrice);
            $ownerAmount = $totalPrice - $commissionAmount;
            
            $bookingData = [
                'customer_id' => $data['customer_id'],
                'owner_id' => $restaurant->owner_id,
                'bookable_type' => 'App\\Models\\Restaurant',
                'bookable_id' => $restaurantId,
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'guests' => $data['guests'],
                'notes' => $data['notes'] ?? null,
                'booking_reference' => $this->generateBookingReference(),
                'total_price' => $totalPrice,
                'commission_amount' => $commissionAmount,
                'owner_amount' => $ownerAmount,
                'status' => 'CONFIRME',
                'payment_status' => 'EN_ATTENTE'
            ];
            
            // Créer la réservation
            $booking = $this->bookingRepository->save($bookingData);
            
            // Créer/initialiser le wallet du owner s'il n'en a pas
            $this->ensureOwnerWallet($restaurant->owner_id, $ownerAmount, $booking->id);
            
            // Attacher les tables si spécifiées
            if (isset($data['restaurant_table_ids']) && is_array($data['restaurant_table_ids'])) {
                $booking->restaurantTables()->sync($data['restaurant_table_ids']);
            }
            
            DB::commit();

            // Initialize Paystack payment
            $customer = \App\Models\User::findOrFail($data['customer_id']);
            $paymentUrl = $this->initializePaymentForBooking($booking, $customer->email, $totalPrice);

            // Return the booking with payment URL
            return [
                'booking' => $booking,
                'payment_url' => $paymentUrl
            ];
            
        } catch (Exception $e) {
            DB::rollBack();
            report($e);
            throw new InvalidArgumentException('Unable to create restaurant booking: ' . $e->getMessage());
        }
    }

    /**
     * Create a new lounge booking.
     *
     * @param array $data
     * @param int $loungeId
     * @return Booking
     */
    public function saveLoungeBooking(array $data, int $loungeId)
    {
        DB::beginTransaction();
        try {
            $lounge = \App\Models\Lounge::findOrFail($loungeId);
            
            // Préparer les données de réservation
            $totalPrice = $this->calculateLoungePrice($lounge, $data);
            $commissionAmount = $this->calculateCommission($totalPrice);
            $ownerAmount = $totalPrice - $commissionAmount;
            
            $bookingData = [
                'customer_id' => $data['customer_id'],
                'owner_id' => $lounge->owner_id,
                'bookable_type' => 'App\\Models\\Lounge',
                'bookable_id' => $loungeId,
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'guests' => $data['guests'],
                'notes' => $data['notes'] ?? null,
                'booking_reference' => $this->generateBookingReference(),
                'total_price' => $totalPrice,
                'commission_amount' => $commissionAmount,
                'owner_amount' => $ownerAmount,
                'status' => 'CONFIRME',
                'payment_status' => 'EN_ATTENTE'
            ];
            
            // Créer la réservation
            $booking = $this->bookingRepository->save($bookingData);
            
            // Créer/initialiser le wallet du owner s'il n'en a pas
            $this->ensureOwnerWallet($lounge->owner_id, $ownerAmount, $booking->id);
            
            // Attacher les tables si spécifiées
            if (isset($data['lounge_table_ids']) && is_array($data['lounge_table_ids'])) {
                $booking->loungeTables()->sync($data['lounge_table_ids']);
            }
            
            DB::commit();

            // Initialize Paystack payment
            $customer = \App\Models\User::findOrFail($data['customer_id']);
            $paymentUrl = $this->initializePaymentForBooking($booking, $customer->email, $totalPrice);

            // Return the booking with payment URL
            return [
                'booking' => $booking,
                'payment_url' => $paymentUrl
            ];
            
        } catch (Exception $e) {
            DB::rollBack();
            report($e);
            throw new InvalidArgumentException('Unable to create lounge booking: ' . $e->getMessage());
        }
    }

    /**
     * Create a new night club booking.
     *
     * @param array $data
     * @param int $nightClubId
     * @return Booking
     */
    public function saveNightClubBooking(array $data, int $nightClubId)
    {
        DB::beginTransaction();
        try {
            $nightClub = \App\Models\NightClub::findOrFail($nightClubId);
            
            // Préparer les données de réservation
            $totalPrice = $this->calculateNightClubPrice($nightClub, $data);
            $commissionAmount = $this->calculateCommission($totalPrice);
            $ownerAmount = $totalPrice - $commissionAmount;
            
            $bookingData = [
                'customer_id' => $data['customer_id'],
                'owner_id' => $nightClub->owner_id,
                'bookable_type' => 'App\\Models\\NightClub',
                'bookable_id' => $nightClubId,
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'guests' => $data['guests'],
                'notes' => $data['notes'] ?? null,
                'booking_reference' => $this->generateBookingReference(),
                'total_price' => $totalPrice,
                'commission_amount' => $commissionAmount,
                'owner_amount' => $ownerAmount,
                'status' => 'CONFIRME',
                'payment_status' => 'EN_ATTENTE'
            ];
            
            // Créer la réservation
            $booking = $this->bookingRepository->save($bookingData);
            
            // Créer/initialiser le wallet du owner s'il n'en a pas
            $this->ensureOwnerWallet($nightClub->owner_id, $ownerAmount, $booking->id);
            
            // Attacher les zones si spécifiées
            if (isset($data['night_club_area_ids']) && is_array($data['night_club_area_ids'])) {
                $booking->nightClubAreas()->sync($data['night_club_area_ids']);
            }
            
            DB::commit();

            // Initialize Paystack payment
            $customer = \App\Models\User::findOrFail($data['customer_id']);
            $paymentUrl = $this->initializePaymentForBooking($booking, $customer->email, $totalPrice);

            // Return the booking with payment URL
            return [
                'booking' => $booking,
                'payment_url' => $paymentUrl
            ];
            
        } catch (Exception $e) {
            DB::rollBack();
            report($e);
            throw new InvalidArgumentException('Unable to create night club booking: ' . $e->getMessage());
        }
    }

    /**
     * Calculate restaurant booking price.
     *
     * @param Restaurant $restaurant
     * @param array $data
     * @return float
     */
    private function calculateRestaurantPrice($restaurant, array $data)
    {
        // Logique de calcul des prix pour restaurant
        // Pour l'instant, prix fixe basé sur le nombre d'invités
        $basePrice = 25.00; // Prix par personne
        return $basePrice * $data['guests'];
    }

    /**
     * Calculate lounge booking price.
     *
     * @param Lounge $lounge
     * @param array $data
     * @return float
     */
    private function calculateLoungePrice($lounge, array $data)
    {
        // Logique de calcul des prix pour lounge
        $basePrice = 30.00; // Prix par personne
        return $basePrice * $data['guests'];
    }

    /**
     * Calculate night club booking price.
     *
     * @param NightClub $nightClub
     * @param array $data
     * @return float
     */
    private function calculateNightClubPrice($nightClub, array $data)
    {
        // Logique de calcul des prix pour night club
        $basePrice = 35.00; // Prix par personne
        return $basePrice * $data['guests'];
    }

    /**
     * Calculate commission amount based on total price.
     *
     * @param float $totalPrice
     * @return float
     */
    private function calculateCommission(float $totalPrice): float
    {
        $commissionService = app(\App\Services\CommissionService::class);
        $commission = $commissionService->getLastCommission();
        
        if (!$commission) {
            return 0;
        }
        
        return $totalPrice * ($commission->commission / 100);
    }

    /**
     * Initialize Paystack payment for a booking.
     *
     * @param Booking $booking
     * @param string $customerEmail
     * @param float $amount
     * @return string|null
     */
    private function initializePaymentForBooking(Booking $booking, string $customerEmail, float $amount): ?string
    {
        try {
            $paymentData = [
                'email' => $customerEmail,
                'amount' => $amount,
                'reference' => $this->paystackService->generateReference(),
                'callback_url' => config('app.url') . '/api/payments/callback',
                'metadata' => [
                    'user_id' => $booking->customer_id,
                    'booking_id' => $booking->id,
                    'booking_reference' => $booking->booking_reference,
                ],
                'currency' => 'XOF',
            ];

            $paystackResponse = $this->paystackService->initializeTransaction($paymentData);

            if ($paystackResponse['status'] && isset($paystackResponse['data']['authorization_url'])) {
                return $paystackResponse['data']['authorization_url'];
            }

            return null;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to initialize Paystack payment for booking #' . $booking->id . ': ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Ensure owner has a wallet, create it if it doesn't exist.
     * Note: The wallet will be credited only after payment confirmation via webhook.
     *
     * @param int $ownerId
     * @param float $ownerAmount
     * @param int $bookingId
     * @return void
     */
    private function ensureOwnerWallet(int $ownerId, float $ownerAmount, int $bookingId): void
    {
        // Créer ou récupérer le wallet du owner
        // Le wallet sera crédité uniquement après confirmation du paiement via webhook
        Wallet::firstOrCreate(
            ['user_id' => $ownerId],
            ['balance' => 0]
        );
    }

}
