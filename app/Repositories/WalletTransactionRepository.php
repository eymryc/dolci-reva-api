<?php
namespace App\Repositories;

use App\Models\WalletTransaction;

class WalletTransactionRepository
{
	 /**
     * @var WalletTransaction
     */
    protected WalletTransaction $walletTransaction;

    /**
     * WalletTransaction constructor.
     *
     * @param WalletTransaction $walletTransaction
     */
    public function __construct(WalletTransaction $walletTransaction)
    {
        $this->walletTransaction = $walletTransaction;
    }

    /**
     * Get all walletTransaction.
     *
     * @return WalletTransaction $walletTransaction
     */
    public function all()
    {
        return $this->walletTransaction->get();
    }

    /**
     * Get all walletTransaction with pagination.
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function paginate(int $perPage = 15)
    {
        return $this->walletTransaction->paginate($perPage);
    }

     /**
     * Get walletTransaction by id
     *
     * @param $id
     * @return mixed
     */
    public function getById(int $id)
    {
        return $this->walletTransaction->find($id);
    }

    /**
     * Save WalletTransaction
     *
     * @param $data
     * @return WalletTransaction
     */
     public function save(array $data)
    {   

        return WalletTransaction::create($data);
    }

     /**
     * Update WalletTransaction
     *
     * @param $data
     * @return WalletTransaction
     */
    public function update(array $data, int $id)
    {
        $walletTransaction = $this->walletTransaction->find($id);
        $walletTransaction->update($data);
        return $walletTransaction;
    }

    /**
     * Delete WalletTransaction
     *
     * @param $data
     * @return WalletTransaction
     */
   	 public function delete(int $id)
    {
        $walletTransaction = $this->walletTransaction->find($id);
        $walletTransaction->delete();
        return $walletTransaction;
    }
}
