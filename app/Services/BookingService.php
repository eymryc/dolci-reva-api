<?php
namespace App\Services;

use App\Models\Booking;
use App\Repositories\BookingRepository;
use Exception;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class BookingService
{
	/**
     * @var BookingRepository $bookingRepository
     */
    protected $bookingRepository;

    /**
     * DummyClass constructor.
     *
     * @param BookingRepository $bookingRepository
     */
    public function __construct(BookingRepository $bookingRepository)
    {
        $this->bookingRepository = $bookingRepository;
    }

    /**
     * Get all bookingRepository.
     *
     * @return String
     */
    public function getAll()
    {
        return $this->bookingRepository->all();
    }

    /**
     * Get bookingRepository with pagination.
     *
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\Paginator
     */
    public function getAllWithPagination(int $perPage = 15)
    {
        return $this->bookingRepository->paginate($perPage);
    }

    /**
     * Get bookingRepository by id.
     *
     * @param $id
     * @return String
     */
    public function getById(int $id)
    {
        return $this->bookingRepository->getById($id);
    }

    /**
     * Validate bookingRepository data.
     * Store to DB if there are no errors.
     *
     * @param array $data
     * @return String
     */
    public function save(array $data)
    {
        return $this->bookingRepository->save($data);
    }

    /**
     * Update bookingRepository data
     * Store to DB if there are no errors.
     *
     * @param array $data
     * @return String
     */
    public function update(array $data, int $id)
    {
        DB::beginTransaction();
        try {
            $bookingRepository = $this->bookingRepository->update($data, $id);
            DB::commit();
            return $bookingRepository;
        } catch (Exception $e) {
            DB::rollBack();
            report($e);
            throw new InvalidArgumentException('Unable to update post data');
        }
    }

    /**
     * Delete bookingRepository by id.
     *
     * @param $id
     * @return String
     */
    public function deleteById(int $id)
    {
        DB::beginTransaction();
        try {
            $bookingRepository = $this->bookingRepository->delete($id);
            DB::commit();
            return $bookingRepository;
        } catch (Exception $e) {
            DB::rollBack();
            report($e);
            throw new InvalidArgumentException('Unable to delete post data');
        }
    }

}
