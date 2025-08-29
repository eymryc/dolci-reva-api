<?php
namespace App\Services;

use App\Models\Activity;
use App\Repositories\ActivityRepository;
use Exception;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class ActivityService
{
	/**
     * @var ActivityRepository $activityRepository
     */
    protected $activityRepository;

    /**
     * DummyClass constructor.
     *
     * @param ActivityRepository $activityRepository
     */
    public function __construct(ActivityRepository $activityRepository)
    {
        $this->activityRepository = $activityRepository;
    }

    /**
     * Get all activityRepository.
     *
     * @return String
     */
    public function getAll()
    {
        return $this->activityRepository->all();
    }

    /**
     * Get activityRepository with pagination.
     *
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\Paginator
     */
    public function getAllWithPagination(int $perPage = 15)
    {
        return $this->activityRepository->paginate($perPage);
    }


    /**
     * Get activityRepository by id.
     *
     * @param $id
     * @return String
     */
    public function getById(int $id)
    {
        return $this->activityRepository->getById($id);
    }

    /**
     * Validate activityRepository data.
     * Store to DB if there are no errors.
     *
     * @param array $data
     * @return String
     */
    public function save(array $data)
    {
        return $this->activityRepository->save($data);
    }

    /**
     * Update activityRepository data
     * Store to DB if there are no errors.
     *
     * @param array $data
     * @return String
     */
    public function update(array $data, int $id)
    {
        DB::beginTransaction();
        try {
            $activityRepository = $this->activityRepository->update($data, $id);
            DB::commit();
            return $activityRepository;
        } catch (Exception $e) {
            DB::rollBack();
            report($e);
            throw new InvalidArgumentException('Unable to update post data');
        }
    }

    /**
     * Delete activityRepository by id.
     *
     * @param $id
     * @return String
     */
    public function deleteById(int $id)
    {
        DB::beginTransaction();
        try {
            $activityRepository = $this->activityRepository->delete($id);
            DB::commit();
            return $activityRepository;
        } catch (Exception $e) {
            DB::rollBack();
            report($e);
            throw new InvalidArgumentException('Unable to delete post data');
        }
    }

}
