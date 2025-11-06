<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use App\Models\Wallet;
use App\Models\Booking;
use Illuminate\Http\Request;
use App\Models\WalletTransaction;
use App\Services\PaystackService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\PaymentVerifyRequest;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\PaymentInitializeRequest;

class PaymentController extends Controller
{
    protected PaystackService $paystackService;

    public function __construct(PaystackService $paystackService)
    {
        $this->paystackService = $paystackService;
    }

    /**
     * Initialize a payment transaction
     *
     * @param PaymentInitializeRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function initialize(PaymentInitializeRequest $request)
    {
        try {
            $user = Auth::user();
            $data = $request->validated();

            // Generate reference
            $reference = $this->paystackService->generateReference();

            // Prepare metadata
            $metadata = [
                'user_id' => $user->id,
                'user_email' => $user->email,
            ];

            // If booking_id is provided, add it to metadata
            if (isset($data['booking_id'])) {
                $booking = Booking::findOrFail($data['booking_id']);
                $metadata['booking_id'] = $booking->id;
                $metadata['booking_reference'] = $booking->booking_reference;
                
                // Use booking amount if not provided
                if (!isset($data['amount'])) {
                    $data['amount'] = $booking->total_price;
                }
            }

            // Initialize transaction with Paystack
            $paymentData = [
                'email' => $data['email'] ?? $user->email,
                'amount' => $data['amount'],
                'reference' => $reference,
                'callback_url' => $data['callback_url'] ?? null,
                'metadata' => $metadata,
                'currency' => $data['currency'] ?? 'XOF',
            ];

            $paystackResponse = $this->paystackService->initializeTransaction($paymentData);

            if ($paystackResponse['status']) {
                return response()->json([
                    'status' => Response::HTTP_OK,
                    'success' => true,
                    'message' => 'Payment initialized successfully',
                    'data' => [
                        'authorization_url' => $paystackResponse['data']['authorization_url'],
                        'access_code' => $paystackResponse['data']['access_code'],
                        'reference' => $paystackResponse['data']['reference'],
                        'public_key' => $this->paystackService->getPublicKey(),
                    ],
                ], Response::HTTP_OK);
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to initialize payment',
            ], Response::HTTP_BAD_REQUEST);

        } catch (\Exception $e) {
            Log::error('Payment Initialize Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while initializing payment',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Verify a payment transaction
     *
     * @param PaymentVerifyRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function verify(PaymentVerifyRequest $request)
    {
        DB::beginTransaction();
        try {
            $reference = $request->input('reference');
            $user = Auth::user();

            // Verify transaction with Paystack
            $paystackResponse = $this->paystackService->verifyTransaction($reference);

            if (!$paystackResponse['status']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment verification failed',
                ], Response::HTTP_BAD_REQUEST);
            }

            $transactionData = $paystackResponse['data'];
            $status = $transactionData['status'];
            $amount = $transactionData['amount'] / 100; // Convert from kobo to naira
            $metadata = $transactionData['metadata'] ?? [];

            // Check if transaction was successful
            if ($status === 'success') {
                // Check if this is a payment for a booking
                if (isset($metadata['booking_id'])) {
                    $booking = Booking::find($metadata['booking_id']);
                    
                    if ($booking) {
                        // Check if already processed
                        $existingTransaction = WalletTransaction::where('reason', 'LIKE', '%' . $reference . '%')
                            ->where('reason', 'LIKE', '%Booking #' . $booking->id . '%')
                            ->first();

                        if ($existingTransaction) {
                            DB::rollBack();
                            return response()->json([
                                'success' => false,
                                'message' => 'This transaction has already been processed',
                            ], Response::HTTP_BAD_REQUEST);
                        }

                        // Update booking payment status
                        $booking->update(['payment_status' => 'PAYE']);

                        // Credit the owner's wallet (not the customer's wallet)
                        if ($booking->owner_id && $booking->owner_amount > 0) {
                            $ownerWallet = Wallet::firstOrCreate(
                                ['user_id' => $booking->owner_id],
                                ['balance' => 0]
                            );

                            // Check if owner wallet was already credited for this booking
                            $ownerTransaction = WalletTransaction::where('wallet_id', $ownerWallet->id)
                                ->where('reason', 'LIKE', '%Réservation #' . $booking->id . '%')
                                ->where('type', 'CREDIT')
                                ->first();

                            if (!$ownerTransaction) {
                                $ownerWallet->increment('balance', $booking->owner_amount);

                                $ownerWallet->transactions()->create([
                                    'type' => 'CREDIT',
                                    'amount' => $booking->owner_amount,
                                    'reason' => 'Réservation #' . $booking->id . ' - Paystack Payment Reference: ' . $reference,
                                ]);
                            }
                        }

                        DB::commit();

                        return response()->json([
                            'status' => Response::HTTP_OK,
                            'success' => true,
                            'message' => 'Payment verified and booking confirmed successfully',
                            'data' => [
                                'reference' => $reference,
                                'amount' => $amount,
                                'status' => $status,
                                'booking_id' => $booking->id,
                                'booking_status' => $booking->payment_status,
                                'transaction_data' => $transactionData,
                            ],
                        ], Response::HTTP_OK);
                    }
                }

                // If not a booking payment, credit customer's wallet (for wallet top-ups)
                $wallet = Wallet::firstOrCreate(
                    ['user_id' => $user->id],
                    ['balance' => 0]
                );

                // Check if this transaction has already been processed
                $existingTransaction = WalletTransaction::where('reason', 'LIKE', '%' . $reference . '%')
                    ->where('wallet_id', $wallet->id)
                    ->first();

                if ($existingTransaction) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'This transaction has already been processed',
                    ], Response::HTTP_BAD_REQUEST);
                }

                // Credit wallet
                $wallet->increment('balance', $amount);

                // Create wallet transaction
                $wallet->transactions()->create([
                    'type' => 'CREDIT',
                    'amount' => $amount,
                    'reason' => 'Paystack Payment - Reference: ' . $reference,
                ]);

                DB::commit();

                return response()->json([
                    'status' => Response::HTTP_OK,
                    'success' => true,
                    'message' => 'Payment verified and wallet credited successfully',
                    'data' => [
                        'reference' => $reference,
                        'amount' => $amount,
                        'status' => $status,
                        'wallet_balance' => $wallet->fresh()->balance,
                        'transaction_data' => $transactionData,
                    ],
                ], Response::HTTP_OK);
            }

            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Payment verification failed: ' . $status,
                'data' => [
                    'reference' => $reference,
                    'status' => $status,
                ],
            ], Response::HTTP_BAD_REQUEST);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payment Verify Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while verifying payment',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Handle Paystack webhook
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function webhook(Request $request)
    {
        try {
            $payload = $request->getContent();
            $signature = $request->header('X-Paystack-Signature');

            // Verify webhook signature
            if (!$this->paystackService->verifyWebhookSignature($payload, $signature)) {
                Log::warning('Invalid Paystack webhook signature');
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid signature',
                ], Response::HTTP_UNAUTHORIZED);
            }

            $event = json_decode($payload, true);

            // Handle different event types
            switch ($event['event']) {
                case 'charge.success':
                    $this->handleSuccessfulCharge($event['data']);
                    break;

                case 'charge.failed':
                    $this->handleFailedCharge($event['data']);
                    break;

                case 'transfer.success':
                    $this->handleSuccessfulTransfer($event['data']);
                    break;

                default:
                    Log::info('Unhandled Paystack webhook event: ' . $event['event']);
            }

            return response()->json([
                'success' => true,
                'message' => 'Webhook processed successfully',
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            Log::error('Paystack Webhook Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing webhook',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Handle successful charge event
     *
     * @param array $data
     * @return void
     */
    private function handleSuccessfulCharge(array $data): void
    {
        DB::beginTransaction();
        try {
            $reference      = $data['reference'];
            $amount         = $data['amount'] / 100; // Convert from kobo
            $metadata       = $data['metadata'] ?? [];
            $customerEmail  = $data['customer']['email'] ?? null;

            // Find user by email or metadata
            $userId = $metadata['user_id'] ?? null;
            if (!$userId && $customerEmail) {
                $user = User::where('email', $customerEmail)->first();
                $userId = $user?->id;
            }

            if (!$userId) {
                Log::warning('Could not find user for Paystack charge: ' . $reference);
                DB::rollBack();
                return;
            }

            // Check if this is a payment for a booking
            if (isset($metadata['booking_id'])) {
                $booking = Booking::find($metadata['booking_id']);
                
                if ($booking) {
                    // Check if already processed
                    $existingTransaction = WalletTransaction::where('reason', 'LIKE', '%' . $reference . '%')
                        ->where('reason', 'LIKE', '%Booking #' . $booking->id . '%')
                        ->first();

                    if ($existingTransaction) {
                        Log::info('Paystack charge already processed for booking: ' . $reference);
                        DB::rollBack();
                        return;
                    }

                    // Update booking payment status
                    $booking->update(['payment_status' => 'PAYE']);

                    // Credit the owner's wallet (not the customer's wallet)
                    if ($booking->owner_id && $booking->owner_amount > 0) {
                        $ownerWallet = Wallet::firstOrCreate(
                            ['user_id' => $booking->owner_id],
                            ['balance' => 0]
                        );

                        // Check if owner wallet was already credited for this booking
                        $ownerTransaction = WalletTransaction::where('wallet_id', $ownerWallet->id)
                            ->where('reason', 'LIKE', '%Réservation #' . $booking->id . '%')
                            ->where('type', 'CREDIT')
                            ->first();

                        if (!$ownerTransaction) {
                            $ownerWallet->increment('balance', $booking->owner_amount);

                            $ownerWallet->transactions()->create([
                                'type' => 'CREDIT',
                                'amount' => $booking->owner_amount,
                                'reason' => 'Réservation #' . $booking->id . ' - Paystack Payment Reference: ' . $reference,
                            ]);

                            Log::info('Owner wallet credited for booking #' . $booking->id . ': ' . $booking->owner_amount);
                        }
                    }

                    DB::commit();
                    Log::info('Paystack charge processed successfully for booking #' . $booking->id . ': ' . $reference);
                    return;
                }
            }

            // If not a booking payment, credit customer's wallet (for wallet top-ups)
            $wallet = Wallet::firstOrCreate(
                ['user_id' => $userId],
                ['balance' => 0]
            );

            // Check if already processed
            $existingTransaction = WalletTransaction::where('reason', 'LIKE', '%' . $reference . '%')
                ->where('wallet_id', $wallet->id)
                ->first();

            if ($existingTransaction) {
                Log::info('Paystack charge already processed: ' . $reference);
                DB::rollBack();
                return;
            }

            // Credit wallet
            $wallet->increment('balance', $amount);

            // Create transaction
            $wallet->transactions()->create([
                'type' => 'CREDIT',
                'amount' => $amount,
                'reason' => 'Paystack Payment - Reference: ' . $reference,
            ]);

            DB::commit();
            Log::info('Paystack charge processed successfully (wallet top-up): ' . $reference);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error handling successful charge: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Handle failed charge event
     *
     * @param array $data
     * @return void
     */
    private function handleFailedCharge(array $data): void
    {
        $reference = $data['reference'];
        $metadata = $data['metadata'] ?? [];

        // Update booking payment status if applicable
        if (isset($metadata['booking_id'])) {
            $booking = Booking::find($metadata['booking_id']);
            if ($booking) {
                $booking->update(['payment_status' => 'ECHEC']);
            }
        }

        Log::info('Paystack charge failed: ' . $reference);
    }

    /**
     * Handle successful transfer event
     *
     * @param array $data
     * @return void
     */
    private function handleSuccessfulTransfer(array $data): void
    {
        // Handle withdrawal/transfer success
        Log::info('Paystack transfer successful: ' . ($data['reference'] ?? 'N/A'));
    }
}

