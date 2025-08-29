<?php
namespace App\Services;

use App\Models\Amenity;
use App\Repositories\AmenityRepository;
use Exception;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class AmenityService
{
	/**
     * @var AmenityRepository $amenityRepository
     */
    protected $amenityRepository;

    /**
     * DummyClass constructor.
     *
     * @param AmenityRepository $amenityRepository
     */
    public function __construct(AmenityRepository $amenityRepository)
    {
        $this->amenityRepository = $amenityRepository;
    }

    /**
     * Get all amenityRepository.
     *
     * @return String
     */
    public function getAll()
    {
        return $this->amenityRepository->all();
    }
    /**
     * Get all amenityRepository with pagination.
     *
     * @return String
     */
    public function getAllWithPagination()
    {
        return $this->amenityRepository->getAllWithPagination();
    }

    /**
     * Get amenityRepository by id.
     *
     * @param $id
     * @return String
     */
    public function getById(int $id)
    {
        return $this->amenityRepository->getById($id);
    }

    /**
     * Validate amenityRepository data.
     * Store to DB if there are no errors.
     *
     * @param array $data
     * @return String
     */
    public function save(array $data)
    {
        return $this->amenityRepository->save($data);
    }


     /**
     * Validate amenityRepository data.
     * Store to DB if there are no errors.
     *
     * @param array $data
     * @return String
     */
    public function firstOrCreate(array $data)
    {
        return $this->amenityRepository->firstOrCreate($data);
    }

    /**
     * Update amenityRepository data
     * Store to DB if there are no errors.
     *
     * @param array $data
     * @return String
     */
    public function update(array $data, int $id)
    {
        DB::beginTransaction();
        try {
            $amenityRepository = $this->amenityRepository->update($data, $id);
            DB::commit();
            return $amenityRepository;
        } catch (Exception $e) {
            DB::rollBack();
            report($e);
            throw new InvalidArgumentException('Unable to update post data');
        }
    }

    /**
     * Delete amenityRepository by id.
     *
     * @param $id
     * @return String
     */
    public function deleteById(int $id)
    {
        DB::beginTransaction();
        try {
            $amenityRepository = $this->amenityRepository->delete($id);
            DB::commit();
            return $amenityRepository;
        } catch (Exception $e) {
            DB::rollBack();
            report($e);
            throw new InvalidArgumentException('Unable to delete post data');
        }
    }

}
