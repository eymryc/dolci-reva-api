<?php

namespace App\Repositories;

use App\Models\Amenity;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AmenityRepository
{
    /**
     * @var Amenity
     */
    protected Amenity $amenity;

    /**
     * Amenity constructor.
     *
     * @param Amenity $amenity
     */
    public function __construct(Amenity $amenity)
    {
        $this->amenity = $amenity;
    }

    /**
     * Get all amenity.
     *
     * @return Amenity $amenity
     */
    public function all()
    {
        return $this->amenity->get();
    }

    /**
     * Get all amenity with pagination.
     *
     * @return Amenity $amenity
     */
    public function getAllWithPagination()
    {
        return $this->amenity->paginate(10);
    }

    /**
     * Get amenity by id
     *
     * @param $id
     * @return mixed
     */
    public function getById(int $id)
    {
        return $this->amenity->find($id);
    }

    /**
     * Save Amenity
     *
     * @param $data
     * @return Amenity
     */
    public function save(array $data)
    {
        return Amenity::create($data);
    }


    /**
     * firstOrCreate Amenity
     *
     * @param $data
     * @return Amenity
     */
    public function firstOrCreate(array $data)
    {
        // If the amenity already exists, we return it
        $response = Amenity::firstOrCreate($data);

        // Find the authenticated user
        $user = User::find(Auth::id()); 

        // $user->amenities()->attach($response->id); // ou sync(), detach(), etc.
        // Alternatively, you can use sync() to ensure no duplicates
        $user->amenities()->sync([$response->id]);

        // If the amenity was created, it will be returned
        return $response;
    }

    /**
     * Update Amenity
     *
     * @param $data
     * @return Amenity
     */
    public function update(array $data, int $id)
    {
        $amenity = $this->amenity->find($id);
        $amenity->update($data);
        return $amenity;
    }

    /**
     * Delete Amenity
     *
     * @param $data
     * @return Amenity
     */
    public function delete(int $id)
    {
        $amenity = $this->amenity->find($id);
        $amenity->delete();
        return $amenity;
    }
}
