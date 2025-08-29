<?php

namespace App\Repositories;

use App\Models\Wallet;
use App\Models\Withdrawal;
use App\Enums\WithdrawalEnum;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class WithdrawalRepository
{
    /**
     * @var Withdrawal
     */
    protected Withdrawal $withdrawal;

    /**
     * Withdrawal constructor.
     *
     * @param Withdrawal $withdrawal
     */
    public function __construct(Withdrawal $withdrawal)
    {
        $this->withdrawal = $withdrawal;
    }

    /**
     * Get all withdrawal.
     *
     * @return Withdrawal $withdrawal
     */
    public function all()
    {
        return $this->withdrawal->get();
    }

    /**
     * Get all withdrawal with pagination.
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function paginate(int $perPage = 15)
    {
        return $this->withdrawal->paginate($perPage);
    }

    /**
     * Get withdrawal by id
     *
     * @param $id
     * @return mixed
     */
    public function getById(int $id)
    {
        return $this->withdrawal->find($id);
    }

    /**
     * Save Withdrawal
     *
     * @param $data
     * @return Withdrawal
     */
    public function save(array $data)
    {

        // Get the authenticated user ID
        $data['user_id'] = Auth::id();
        $data['status'] =  WithdrawalEnum::PENDING->value; 


        // Find user connected Wallet
        $wallet = Wallet::where('user_id', $data['user_id'])->first();

        // Check if the wallet exists
        if ($wallet) {

            // Check if the wallet has enough balance
            if ($wallet->balance >= $data['amount']) {

                // dd($data);
                // Create a new withdrawal record
                $withdrawal = Withdrawal::create($data);

                // Credit
                $wallet->transactions()->create([
                    'type' => 'DEBIT',
                    'amount' =>  $data['amount'],
                    'reason' => 'Retrait #' . $withdrawal->id,
                ]);

                // Deduct the amount from the wallet balance
                $wallet->balance -= $data['amount'];
                $wallet->save();

                // Return the created withdrawal
                return $withdrawal;
            } else {
                return throw new \Exception('Wallet not found or insufficient balance.');
            }
        } // If the wallet does not exist or has insufficient balance, handle accordingly
        else {
            return throw new \Exception('Wallet not found ');
        }
    }

    /**
     * Update Withdrawal
     *
     * @param $data
     * @return Withdrawal
     */
    public function update(array $data, int $id)
    {
        $withdrawal = $this->withdrawal->find($id);
        $withdrawal->update($data);
        return $withdrawal;
    }

    /**
     * Delete Withdrawal
     *
     * @param $data
     * @return Withdrawal
     */
    public function delete(int $id)
    {
        $withdrawal = $this->withdrawal->find($id);
        $withdrawal->delete();
        return $withdrawal;
    }
}
