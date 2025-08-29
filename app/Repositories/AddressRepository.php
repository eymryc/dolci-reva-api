<?php

namespace App\Repositories;

use App\Models\Address;
use Illuminate\Support\Facades\Auth;

class AddressRepository
{
    /**
     * @var Address
     */
    protected Address $address;

    /**
     * Address constructor.
     *
     * @param Address $address
     */
    public function __construct(Address $address)
    {
        $this->address = $address;
    }

    /**
     * Get all address.
     *
     * @return Address $address
     */
    public function all()
    {
        return $this->address->get();
    }

    /**
     * Get all address with pagination.
     *
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function paginate(int $perPage = 15)
    {
        return $this->address->paginate($perPage);  
    }

    /**
     * Get address by id
     *
     * @param $id
     * @return mixed
     */
    public function getById(int $id)
    {
        return $this->address->find($id);
    }

    /**
     * Save Address
     *
     * @param $data
     * @return Address
     */
    public function save(array $data)
    {   

        //
        // dd($data);
        // Assure that the user_id is set to the currently authenticated user's ID
        $data['user_id'] = Auth::id();

        // Create a new Address instance and save it
        return Address::create($data);
    }

    /**
     * Update Address
     *
     * @param $data
     * @return Address
     */
    public function update(array $data, int $id)
    {   
        // Ensure that the user_id is set to the currently authenticated user's ID
        $data['user_id'] = Auth::id();

        // Find the address by ID and update it
        $address = $this->address->find($id);
        if (!$address) {
            throw new \Exception('Address not found');
        }
        $address->update($data);

        // Return the updated address
        return $address;
    }

    /**
     * Delete Address
     *
     * @param $data
     * @return Address
     */
    public function delete(int $id)
    {
        $address = $this->address->find($id);
        $address->delete();
        return $address;
    }
}
