<?php
namespace App\Services;

use App\Models\TimeSlot;
use App\Repositories\TimeSlotRepository;
use Exception;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class TimeSlotService
{
	/**
     * @var TimeSlotRepository $timeSlotRepository
     */
    protected $timeSlotRepository;

    /**
     * DummyClass constructor.
     *
     * @param TimeSlotRepository $timeSlotRepository
     */
    public function __construct(TimeSlotRepository $timeSlotRepository)
    {
        $this->timeSlotRepository = $timeSlotRepository;
    }

    /**
     * Get all timeSlotRepository.
     *
     * @return String
     */
    public function getAll()
    {
        return $this->timeSlotRepository->all();
    }

    /**
     * Get timeSlotRepository with pagination.
     *
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\Paginator
     */
    public function getAllWithPagination(int $perPage = 15)
    {
        return $this->timeSlotRepository->paginate($perPage);
    }

    /**
     * Get timeSlotRepository by id.
     *
     * @param $id
     * @return String
     */
    public function getById(int $id)
    {
        return $this->timeSlotRepository->getById($id);
    }

    /**
     * Validate timeSlotRepository data.
     * Store to DB if there are no errors.
     *
     * @param array $data
     * @return String
     */
    public function save(array $data)
    {
        return $this->timeSlotRepository->save($data);
    }

    /**
     * Update timeSlotRepository data
     * Store to DB if there are no errors.
     *
     * @param array $data
     * @return String
     */
    public function update(array $data, int $id)
    {
        DB::beginTransaction();
        try {
            $timeSlotRepository = $this->timeSlotRepository->update($data, $id);
            DB::commit();
            return $timeSlotRepository;
        } catch (Exception $e) {
            DB::rollBack();
            report($e);
            throw new InvalidArgumentException('Unable to update post data');
        }
    }

    /**
     * Delete timeSlotRepository by id.
     *
     * @param $id
     * @return String
     */
    public function deleteById(int $id)
    {
        DB::beginTransaction();
        try {
            $timeSlotRepository = $this->timeSlotRepository->delete($id);
            DB::commit();
            return $timeSlotRepository;
        } catch (Exception $e) {
            DB::rollBack();
            report($e);
            throw new InvalidArgumentException('Unable to delete post data');
        }
    }

}
