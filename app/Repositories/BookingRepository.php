<?php

namespace App\Repositories;

use Carbon\Carbon;
use App\Models\Room;
use App\Models\Space;
use App\Models\Wallet;
use App\Models\Booking;
use App\Models\Activity;
use App\Models\Property;
use App\Enums\BookingEnumStatus;
use App\Services\BookingService;
use App\Services\CommissionService;
use Illuminate\Support\Facades\Auth;
use App\Enums\BookingEnumPaymentStatus;

class BookingRepository
{

    /**
     * @var Booking
     */
    protected Booking $booking;

    /**
     * @var CommissionService
     */
    protected CommissionService $commissionService;

    /**
     * Booking constructor.
     *
     * @param Booking $booking
     */
    public function __construct(Booking $booking, CommissionService $commissionService)
    {
        $this->booking = $booking;
        $this->commissionService = $commissionService;
    }

    /**
     * Get all booking.
     *
     * @return Booking $booking
     */
    public function all()
    {
        return $this->booking->get();
    }

    /**
     * Get all booking with pagination.
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function paginate(int $perPage = 15)
    {
        return $this->booking->paginate($perPage);
    }

    /**
     * Get booking by id
     *
     * @param $id
     * @return mixed
     */
    public function getById(int $id)
    {
        return $this->booking->find($id);
    }

    /**
     * Save Booking
     *
     * @param $data
     * @return Booking
     */
    public function save(array $data)
    {

        // Get user connected ID
        $customerID = Auth::id();

        // create a new instance of Booking

        // Debug
        //
        // dd($data);
        //
        $startDate = $data['start_date'] ?? null;
        $endDate = $data['end_date'] ?? null;


        // Debug
        //
        // dd($startDate);

        // Format the dates to store in the database
        $data['start_date'] = $startDate ? Carbon::parse($startDate) : null;
        $data['end_date']   = $endDate   ? Carbon::parse($endDate)   : null;


        // Check if the bookingModelInstance type is set and get the corresponding model
        if (isset($data['room_id'])) {

            // Get the room by ID
            $bookingModelInstance = Room::find($data['room_id']);

            // Get the owner ID from the room
            $ownerID = $bookingModelInstance->property->owner_id;
        } elseif (isset($data['property_id'])) {

            // Get the property by ID
            $bookingModelInstance = Property::find($data['property_id']);

            // Get the owner ID from the property
            $ownerID = $bookingModelInstance->owner_id;
        } elseif (isset($data['activity_id'])) {

            // Get the activity by ID
            $bookingModelInstance = Activity::find($data['activity_id']);

            // Get the owner ID from the activity
            $ownerID = $bookingModelInstance->property->owner_id;
        } elseif (isset($data['space_id'])) {

            // Get the space by ID
            $bookingModelInstance = Space::find($data['space_id']);

            // Get the owner ID from the space
            $ownerID = $bookingModelInstance->property->owner_id;
        } else {

            // Handle error or set default values
            throw new \Exception('Invalid bookingModelInstance type');
        }

        // dd($bookingModelInstance);
        // $ownerID = $bookingModelInstance->property->owner_id;

        //Debug
        //
        // dd($ownerID);
        // dd($room, $property, $activity, $space);


        // Calculate total price based on start and end dates
        if ($data['start_date'] && $data['end_date']) {

            // Initialize CommissionService to get the commission
            // $commissionServices = new CommissionService;
            // Get active commission
            $commission = $this->commissionService->getLastCommission();

            // Debug
            // dd($commission);
            // Days number
            $days = (int) $data['start_date']->diffInDays($data['end_date']);

            // dd($days);

            // Calculate the total price based on the bookingModelInstance type and days
            $data['total_price'] =  $days * $bookingModelInstance->price;

            // Calculate the commission amount
            $data['commission_amount'] = $commission ? ($data['total_price'] * $commission->commission / 100) : 0;

            // Calculate the owner amount
            $data['owner_amount'] = $data['total_price'] - $data['commission_amount'];
        } else {
            // If no dates are provided, set default values
            $data['total_price'] = 0;
            $data['owner_amount'] = 0;
            $data['commission_amount'] = 0;
        }

        // Format the dates to store in the database
        // Ensure the dates are in the correct format for the database
        $data['start_date'] = $startDate ? Carbon::parse($startDate)->format('Y-m-d H:i:s') : null;
        $data['end_date']   = $endDate   ? Carbon::parse($endDate)->format('Y-m-d H:i:s')   : null;

        // Set the customer and owner IDs
        $data['customer_id'] = $customerID;
        $data['owner_id'] = $customerID;

        // Debug
        //
        // dd($data);
        // dd();

        // Data to save
        $save = [
            'customer_id'          => $data['customer_id'],
            'owner_id'             => $data['owner_id'],
            'start_date'           => $data['start_date'],
            'end_date'             => $data['end_date'],
            'total_price'          => $data['total_price'],
            'commission_amount'    => $data['commission_amount'] ?? null,
            'owner_amount'         => $data['owner_amount'] ?? null,
            'status'               => BookingEnumStatus::EN_ATTENTE->value,
            'payment_status'       => BookingEnumPaymentStatus::EN_ATTENTE->value,
        ];

        // Create the booking with the bookingModelInstance relationship
        $booking = $bookingModelInstance->bookings()->create($save);


        // Create owner wallet and credit the amount
        $wallet = Wallet::firstOrCreate(['user_id' => $data['owner_id']]);

        // Credit the owner's wallet with the owner amount
        $wallet->increment('balance',  $data['owner_amount']);

        // Credit
        $wallet->transactions()->create([
            'type' => 'CREDIT',
            'amount' =>  $data['owner_amount'],
            'reason' => 'Réservation #' . $booking->id,
        ]);


        // return Booking::create($data);
    }

    /**
     * Update Booking
     *
     * @param $data
     * @return Booking
     */
    public function update(array $data, int $id)
    {
        $booking = $this->booking->find($id);
        $booking->update($data);
        return $booking;
    }

    /**
     * Delete Booking
     *
     * @param $data
     * @return Booking
     */
    public function delete(int $id)
    {
        $booking = $this->booking->find($id);
        $booking->delete();
        return $booking;
    }
}
