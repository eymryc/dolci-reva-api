<?php
namespace App\Services;

use App\Models\Room;
use App\Repositories\RoomRepository;
use Exception;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class RoomService
{
	/**
     * @var RoomRepository $roomRepository
     */
    protected $roomRepository;

    /**
     * DummyClass constructor.
     *
     * @param RoomRepository $roomRepository
     */
    public function __construct(RoomRepository $roomRepository)
    {
        $this->roomRepository = $roomRepository;
    }

    /**
     * Get all roomRepository.
     *
     * @return String
     */
    public function getAll()
    {
        return $this->roomRepository->all();
    }

    /**
     * Get roomRepository with pagination.
     *
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\Paginator
     */
    public function getAllWithPagination(int $perPage = 15)
    {
        return $this->roomRepository->paginate($perPage);
    }

    /**
     * Get roomRepository by id.
     *
     * @param $id
     * @return String
     */
    public function getById(int $id)
    {
        return $this->roomRepository->getById($id);
    }

    /**
     * Validate roomRepository data.
     * Store to DB if there are no errors.
     *
     * @param array $data
     * @return String
     */
    public function save(array $data)
    {
        return $this->roomRepository->save($data);
    }

    /**
     * Update roomRepository data
     * Store to DB if there are no errors.
     *
     * @param array $data
     * @return String
     */
    public function update(array $data, int $id)
    {
        DB::beginTransaction();
        try {
            $roomRepository = $this->roomRepository->update($data, $id);
            DB::commit();
            return $roomRepository;
        } catch (Exception $e) {
            DB::rollBack();
            report($e);
            throw new InvalidArgumentException('Unable to update post data');
        }
    }

    /**
     * Delete roomRepository by id.
     *
     * @param $id
     * @return String
     */
    public function deleteById(int $id)
    {
        DB::beginTransaction();
        try {
            $roomRepository = $this->roomRepository->delete($id);
            DB::commit();
            return $roomRepository;
        } catch (Exception $e) {
            DB::rollBack();
            report($e);
            throw new InvalidArgumentException('Unable to delete post data');
        }
    }

}
