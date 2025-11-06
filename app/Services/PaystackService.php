<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class PaystackService
{
    protected string $secretKey;
    protected string $publicKey;
    protected string $baseUrl;

    public function __construct()
    {
        $this->secretKey = config('services.paystack.secret_key');
        $this->publicKey = config('services.paystack.public_key');
        $this->baseUrl = config('services.paystack.url', 'https://api.paystack.co');
    }

    /**
     * Initialize a payment transaction
     *
     * @param array $data
     * @return array
     * @throws Exception
     */
    public function initializeTransaction(array $data): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/transaction/initialize', [
                'email' => $data['email'],
                'amount' => $data['amount'] * 100, // Convert to centimes (Paystack uses smallest currency unit)
                'reference' => $data['reference'] ?? $this->generateReference(),
                'callback_url' => $data['callback_url'] ?? null,
                'metadata' => $data['metadata'] ?? [],
                'currency' => $data['currency'] ?? 'XOF',
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            throw new Exception('Paystack API Error: ' . $response->body());
        } catch (Exception $e) {
            Log::error('Paystack Initialize Transaction Error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Verify a payment transaction
     *
     * @param string $reference
     * @return array
     * @throws Exception
     */
    public function verifyTransaction(string $reference): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
                'Content-Type' => 'application/json',
            ])->get($this->baseUrl . '/transaction/verify/' . $reference);

            if ($response->successful()) {
                return $response->json();
            }

            throw new Exception('Paystack API Error: ' . $response->body());
        } catch (Exception $e) {
            Log::error('Paystack Verify Transaction Error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get transaction details
     *
     * @param string $reference
     * @return array
     * @throws Exception
     */
    public function getTransaction(string $reference): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
                'Content-Type' => 'application/json',
            ])->get($this->baseUrl . '/transaction/' . $reference);

            if ($response->successful()) {
                return $response->json();
            }

            throw new Exception('Paystack API Error: ' . $response->body());
        } catch (Exception $e) {
            Log::error('Paystack Get Transaction Error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * List all transactions
     *
     * @param array $params
     * @return array
     * @throws Exception
     */
    public function listTransactions(array $params = []): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
                'Content-Type' => 'application/json',
            ])->get($this->baseUrl . '/transaction', $params);

            if ($response->successful()) {
                return $response->json();
            }

            throw new Exception('Paystack API Error: ' . $response->body());
        } catch (Exception $e) {
            Log::error('Paystack List Transactions Error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Verify webhook signature
     *
     * @param string $payload
     * @param string $signature
     * @return bool
     */
    public function verifyWebhookSignature(string $payload, string $signature): bool
    {
        $computedSignature = hash_hmac('sha512', $payload, $this->secretKey);
        return hash_equals($computedSignature, $signature);
    }

    /**
     * Generate a unique reference
     *
     * @return string
     */
    public function generateReference(): string
    {
        return 'TXN_' . time() . '_' . uniqid();
    }

    /**
     * Get public key
     *
     * @return string
     */
    public function getPublicKey(): string
    {
        return $this->publicKey;
    }
}

