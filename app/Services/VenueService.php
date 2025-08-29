<?php
namespace App\Services;

use App\Models\Venue;
use App\Repositories\VenueRepository;
use Exception;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class VenueService
{
	/**
     * @var VenueRepository $venueRepository
     */
    protected $venueRepository;

    /**
     * DummyClass constructor.
     *
     * @param VenueRepository $venueRepository
     */
    public function __construct(VenueRepository $venueRepository)
    {
        $this->venueRepository = $venueRepository;
    }

    /**
     * Get all venueRepository.
     *
     * @return String
     */
    public function getAll()
    {
        return $this->venueRepository->all();
    }


    /**
     * Get venueRepository with pagination.
     *
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\Paginator
     */
    public function getAllWithPagination(int $perPage = 15)
    {
        return $this->venueRepository->paginate($perPage);
    }

    /**
     * Get venueRepository by id.
     *
     * @param $id
     * @return String
     */
    public function getById(int $id)
    {
        return $this->venueRepository->getById($id);
    }

    /**
     * Validate venueRepository data.
     * Store to DB if there are no errors.
     *
     * @param array $data
     * @return String
     */
    public function save(array $data)
    {
        return $this->venueRepository->save($data);
    }

    /**
     * Update venueRepository data
     * Store to DB if there are no errors.
     *
     * @param array $data
     * @return String
     */
    public function update(array $data, int $id)
    {
        DB::beginTransaction();
        try {
            $venueRepository = $this->venueRepository->update($data, $id);
            DB::commit();
            return $venueRepository;
        } catch (Exception $e) {
            DB::rollBack();
            report($e);
            throw new InvalidArgumentException('Unable to update post data');
        }
    }

    /**
     * Delete venueRepository by id.
     *
     * @param $id
     * @return String
     */
    public function deleteById(int $id)
    {
        DB::beginTransaction();
        try {
            $venueRepository = $this->venueRepository->delete($id);
            DB::commit();
            return $venueRepository;
        } catch (Exception $e) {
            DB::rollBack();
            report($e);
            throw new InvalidArgumentException('Unable to delete post data');
        }
    }

}
