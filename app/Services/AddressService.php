<?php
namespace App\Services;

use App\Models\Address;
use App\Repositories\AddressRepository;
use Exception;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class AddressService
{
	/**
     * @var AddressRepository $addressRepository
     */
    protected $addressRepository;

    /**
     * DummyClass constructor.
     *
     * @param AddressRepository $addressRepository
     */
    public function __construct(AddressRepository $addressRepository)
    {
        $this->addressRepository = $addressRepository;
    }

    /**
     * Get all addressRepository.
     *
     * @return String
     */
    public function getAll()
    {
        return $this->addressRepository->all();
    }

    /**
     * Get addressRepository with pagination.
     *
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\Paginator
     */
    public function getAllWithPagination(int $perPage = 15)
    {
        return $this->addressRepository->paginate($perPage);
    }

    /**
     * Get addressRepository by id.
     *
     * @param $id
     * @return String
     */
    public function getById(int $id)
    {
        return $this->addressRepository->getById($id);
    }

    /**
     * Validate addressRepository data.
     * Store to DB if there are no errors.
     *
     * @param array $data
     * @return String
     */
    public function save(array $data)
    {
        return $this->addressRepository->save($data);
    }

    /**
     * Update addressRepository data
     * Store to DB if there are no errors.
     *
     * @param array $data
     * @return String
     */
    public function update(array $data, int $id)
    {
        DB::beginTransaction();
        try {
            $addressRepository = $this->addressRepository->update($data, $id);
            DB::commit();
            return $addressRepository;
        } catch (Exception $e) {
            DB::rollBack();
            report($e);
            throw new InvalidArgumentException('Unable to update post data');
        }
    }

    /**
     * Delete addressRepository by id.
     *
     * @param $id
     * @return String
     */
    public function deleteById(int $id)
    {
        DB::beginTransaction();
        try {
            $addressRepository = $this->addressRepository->delete($id);
            DB::commit();
            return $addressRepository;
        } catch (Exception $e) {
            DB::rollBack();
            report($e);
            throw new InvalidArgumentException('Unable to delete post data');
        }
    }

}
