<?php

namespace App\Repositories;

use App\Models\Commission;

class CommissionRepository
{
    /**
     * @var Commission
     */
    protected Commission $commission;

    /**
     * Commission constructor.
     *
     * @param Commission $commission
     */
    public function __construct(Commission $commission)
    {
        $this->commission = $commission;
    }

    /**
     * Get all commission.
     *
     * @return Commission $commission
     */
    public function all()
    {
        return $this->commission->get();
    }
    
    /**
     * Get all commission with pagination.
     *
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function paginate(int $perPage = 15)
    {
        return $this->commission->paginate($perPage);
    }

    /**
     * Get commission by id
     *
     * @param $id
     * @return mixed
     */
    public function getById(int $id)
    {
        return $this->commission->find($id);
    }

    /**
     * Save Commission
     *
     * @param $data
     * @return Commission
     */
    public function save(array $data)
    {   
        // Si la nouvelle commission est active, désactiver toutes les autres
        if (isset($data['is_active']) && $data['is_active'] === true) {
            Commission::where('is_active', true)
                ->whereNull('deleted_at')
                ->update(['is_active' => false]);
        }
        
        return Commission::create($data);
    }

    /**
     * Update Commission
     *
     * @param $data
     * @return Commission
     */
    public function update(array $data, int $id)
    {
        $commission = $this->commission->find($id);
        
        // Si cette commission est activée, désactiver toutes les autres
        if (isset($data['is_active']) && $data['is_active'] === true) {
            Commission::where('is_active', true)
                ->whereNull('deleted_at')
                ->where('id', '!=', $id) // Exclure la commission actuelle
                ->update(['is_active' => false]);
        }
        
        $commission->update($data);
        return $commission;
    }

    /**
     * Delete Commission
     *
     * @param $data
     * @return Commission
     */
    public function delete(int $id)
    {
        $commission = $this->commission->find($id);
        $commission->delete();
        return $commission;
    }

    /**
     * Get the last commission
     *
     * @return Commission|null
     */
    public function getLastCommission()
    {
        return $this->commission->where("deleted_at", null)->where("is_active",true)->latest()->first();
    }
}
