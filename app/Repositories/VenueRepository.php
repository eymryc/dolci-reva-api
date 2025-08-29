<?php

namespace App\Repositories;

use App\Models\Venue;
use App\Enums\VenueEnum;
use Illuminate\Support\Facades\Auth;

class VenueRepository
{
    /**
     * @var Venue
     */
    protected Venue $venue;

    /**
     * Venue constructor.
     *
     * @param Venue $venue
     */
    public function __construct(Venue $venue)
    {
        $this->venue = $venue;
    }

    /**
     * Get all venue.
     *
     * @return Venue $venue
     */
    public function all()
    {
        return $this->venue->get();
    }

    /**
     * Get all venue with pagination.
     *
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function paginate(int $perPage = 15)
    {
        return $this->venue->paginate($perPage);
    }

    /**
     * Get venue by id
     *
     * @param $id
     * @return mixed
     */
    public function getById(int $id)
    {
        return $this->venue->find($id);
    }

    /**
     * Save Venue
     *
     * @param $data
     * @return Venue
     */
    public function save(array $data)
    {
        // Assure that the user_id is set to the currently authenticated user's ID
        $data['owner_id'] = Auth::id();

        // Convert the type to a string if it's an enum
        $data['type'] = $data['type']->value;

        // Extract opening hours from the data if they exist
        $openingHours = $data['opening_hours'] ?? [];
        unset($data['opening_hours']);

        // Debugging line to check the data being saved
        // dd($data);

        // Create a new Venue instance and save it
        $venue = Venue::create($data);

        // Debugging line to check the data being saved
        // dd($venue);

        //
        if (!empty($openingHours)) {
            $venue->openingHours()->createMany($openingHours);
        }

        // Return the created venue
        return $venue;
    }

    /**
     * Update Venue
     *
     * @param $data
     * @return Venue
     */
    public function update(array $data, int $id)
    {
        // $venue = $this->venue->find($id);
        // $venue->update($data);
        // return $venue;

        $venue = $this->venue->find($id);

        // Convert the type to a string if it's an enum
        $data['type'] = $data['type']->value;

        // Gérer les horaires d'ouverture
        $openingHours = $data['opening_hours'] ?? null;
        unset($data['opening_hours']);

        $venue->update($data);

        if ($openingHours !== null) {
            // Supprime les anciens horaires
            $venue->openingHours()->delete();
            // Ajoute les nouveaux
            $venue->openingHours()->createMany($openingHours);
        }

        return $venue;
    }

    /**
     * Delete Venue
     *
     * @param $data
     * @return Venue
     */
    public function delete(int $id)
    {
        $venue = $this->venue->find($id);
        $venue->delete();
        return $venue;
    }
}
