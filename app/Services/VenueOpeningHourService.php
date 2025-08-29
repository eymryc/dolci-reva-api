<?php
namespace App\Services;

use App\Models\VenueOpeningHour;
use App\Repositories\VenueOpeningHourRepository;
use Exception;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class VenueOpeningHourService
{
	/**
     * @var VenueOpeningHourRepository $venueOpeningHourRepository
     */
    protected $venueOpeningHourRepository;

    /**
     * DummyClass constructor.
     *
     * @param VenueOpeningHourRepository $venueOpeningHourRepository
     */
    public function __construct(VenueOpeningHourRepository $venueOpeningHourRepository)
    {
        $this->venueOpeningHourRepository = $venueOpeningHourRepository;
    }

    /**
     * Get all venueOpeningHourRepository.
     *
     * @return String
     */
    public function getAll()
    {
        return $this->venueOpeningHourRepository->all();
    }

    /**
     * Get venueOpeningHourRepository by id.
     *
     * @param $id
     * @return String
     */
    public function getById(int $id)
    {
        return $this->venueOpeningHourRepository->getById($id);
    }

    /**
     * Validate venueOpeningHourRepository data.
     * Store to DB if there are no errors.
     *
     * @param array $data
     * @return String
     */
    public function save(array $data)
    {
        return $this->venueOpeningHourRepository->save($data);
    }

    /**
     * Update venueOpeningHourRepository data
     * Store to DB if there are no errors.
     *
     * @param array $data
     * @return String
     */
    public function update(array $data, int $id)
    {
        DB::beginTransaction();
        try {
            $venueOpeningHourRepository = $this->venueOpeningHourRepository->update($data, $id);
            DB::commit();
            return $venueOpeningHourRepository;
        } catch (Exception $e) {
            DB::rollBack();
            report($e);
            throw new InvalidArgumentException('Unable to update post data');
        }
    }

    /**
     * Delete venueOpeningHourRepository by id.
     *
     * @param $id
     * @return String
     */
    public function deleteById(int $id)
    {
        DB::beginTransaction();
        try {
            $venueOpeningHourRepository = $this->venueOpeningHourRepository->delete($id);
            DB::commit();
            return $venueOpeningHourRepository;
        } catch (Exception $e) {
            DB::rollBack();
            report($e);
            throw new InvalidArgumentException('Unable to delete post data');
        }
    }

}
