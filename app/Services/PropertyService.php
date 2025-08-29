<?php
namespace App\Services;

use App\Models\Property;
use App\Repositories\PropertyRepository;
use Exception;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class PropertyService
{
	/**
     * @var PropertyRepository $propertyRepository
     */
    protected $propertyRepository;

    /**
     * DummyClass constructor.
     *
     * @param PropertyRepository $propertyRepository
     */
    public function __construct(PropertyRepository $propertyRepository)
    {
        $this->propertyRepository = $propertyRepository;
    }

    /**
     * Get all propertyRepository.
     *
     * @return String
     */
    public function getAll()
    {
        return $this->propertyRepository->all();
    }

    /**
     * Get propertyRepository with pagination.
     *
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\Paginator
     */
    public function getAllWithPagination(int $perPage = 15)
    {
        return $this->propertyRepository->paginate($perPage);
    }

    /**
     * Get propertyRepository by id.
     *
     * @param $id
     * @return String
     */
    public function getById(int $id)
    {
        return $this->propertyRepository->getById($id);
    }

    /**
     * Validate propertyRepository data.
     * Store to DB if there are no errors.
     *
     * @param array $data
     * @return String
     */
    public function save(array $data)
    {   
        return $this->propertyRepository->save($data);
    }

    /**
     * Update propertyRepository data
     * Store to DB if there are no errors.
     *
     * @param array $data
     * @return String
     */
    public function update(array $data, int $id)
    {
        DB::beginTransaction();
        try {
            $propertyRepository = $this->propertyRepository->update($data, $id);
            DB::commit();
            return $propertyRepository;
        } catch (Exception $e) {
            DB::rollBack();
            report($e);
            throw new InvalidArgumentException('Unable to update post data');
        }
    }

    /**
     * Delete propertyRepository by id.
     *
     * @param $id
     * @return String
     */
    public function deleteById(int $id)
    {
        DB::beginTransaction();
        try {
            $propertyRepository = $this->propertyRepository->delete($id);
            DB::commit();
            return $propertyRepository;
        } catch (Exception $e) {
            DB::rollBack();
            report($e);
            throw new InvalidArgumentException('Unable to delete post data');
        }
    }

}
