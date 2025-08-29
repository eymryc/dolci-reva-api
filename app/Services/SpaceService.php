<?php
namespace App\Services;

use App\Models\Space;
use App\Repositories\SpaceRepository;
use Exception;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class SpaceService
{
	/**
     * @var SpaceRepository $spaceRepository
     */
    protected $spaceRepository;

    /**
     * DummyClass constructor.
     *
     * @param SpaceRepository $spaceRepository
     */
    public function __construct(SpaceRepository $spaceRepository)
    {
        $this->spaceRepository = $spaceRepository;
    }

    /**
     * Get all spaceRepository.
     *
     * @return String
     */
    public function getAll()
    {
        return $this->spaceRepository->all();
    }

    /**
     * Get spaceRepository with pagination.
     *
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\Paginator
     */
    public function getAllWithPagination(int $perPage = 15)
    {
        return $this->spaceRepository->paginate($perPage);
    }

    /**
     * Get spaceRepository by id.
     *
     * @param $id
     * @return String
     */
    public function getById(int $id)
    {
        return $this->spaceRepository->getById($id);
    }

    /**
     * Validate spaceRepository data.
     * Store to DB if there are no errors.
     *
     * @param array $data
     * @return String
     */
    public function save(array $data)
    {
        return $this->spaceRepository->save($data);
    }

    /**
     * Update spaceRepository data
     * Store to DB if there are no errors.
     *
     * @param array $data
     * @return String
     */
    public function update(array $data, int $id)
    {
        DB::beginTransaction();
        try {
            $spaceRepository = $this->spaceRepository->update($data, $id);
            DB::commit();
            return $spaceRepository;
        } catch (Exception $e) {
            DB::rollBack();
            report($e);
            throw new InvalidArgumentException('Unable to update post data');
        }
    }

    /**
     * Delete spaceRepository by id.
     *
     * @param $id
     * @return String
     */
    public function deleteById(int $id)
    {
        DB::beginTransaction();
        try {
            $spaceRepository = $this->spaceRepository->delete($id);
            DB::commit();
            return $spaceRepository;
        } catch (Exception $e) {
            DB::rollBack();
            report($e);
            throw new InvalidArgumentException('Unable to delete post data');
        }
    }

}
