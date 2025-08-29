<?php
namespace App\Repositories;

use App\Models\VenueOpeningHour;

class VenueOpeningHourRepository
{
	 /**
     * @var VenueOpeningHour
     */
    protected VenueOpeningHour $venueOpeningHour;

    /**
     * VenueOpeningHour constructor.
     *
     * @param VenueOpeningHour $venueOpeningHour
     */
    public function __construct(VenueOpeningHour $venueOpeningHour)
    {
        $this->venueOpeningHour = $venueOpeningHour;
    }

    /**
     * Get all venueOpeningHour.
     *
     * @return VenueOpeningHour $venueOpeningHour
     */
    public function all()
    {
        return $this->venueOpeningHour->get();
    }

     /**
     * Get venueOpeningHour by id
     *
     * @param $id
     * @return mixed
     */
    public function getById(int $id)
    {
        return $this->venueOpeningHour->find($id);
    }

    /**
     * Save VenueOpeningHour
     *
     * @param $data
     * @return VenueOpeningHour
     */
     public function save(array $data)
    {
        return VenueOpeningHour::create($data);
    }

     /**
     * Update VenueOpeningHour
     *
     * @param $data
     * @return VenueOpeningHour
     */
    public function update(array $data, int $id)
    {
        $venueOpeningHour = $this->venueOpeningHour->find($id);
        $venueOpeningHour->update($data);
        return $venueOpeningHour;
    }

    /**
     * Delete VenueOpeningHour
     *
     * @param $data
     * @return VenueOpeningHour
     */
   	 public function delete(int $id)
    {
        $venueOpeningHour = $this->venueOpeningHour->find($id);
        $venueOpeningHour->delete();
        return $venueOpeningHour;
    }
}
