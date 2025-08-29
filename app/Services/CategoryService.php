<?php
namespace App\Services;

use App\Models\Category;
use App\Repositories\CategoryRepository;
use Exception;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class CategoryService
{
	/**
     * @var CategoryRepository $categoryRepository
     */
    protected $categoryRepository;

    /**
     * DummyClass constructor.
     *
     * @param CategoryRepository $categoryRepository
     */
    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * Get all categoryRepository.
     *
     * @return String
     */
    public function getAll()
    {
        return $this->categoryRepository->all();
    }

    /**
     * Get categoryRepository with pagination.
     *
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\Paginator
     */
    public function getAllWithPagination(int $perPage = 15)
    {
        return $this->categoryRepository->paginate($perPage);
    }

    /**
     * Get categoryRepository by id.
     *
     * @param $id
     * @return String
     */
    public function getById(int $id)
    {
        return $this->categoryRepository->getById($id);
    }

    /**
     * Validate categoryRepository data.
     * Store to DB if there are no errors.
     *
     * @param array $data
     * @return String
     */
    public function save(array $data)
    {
        return $this->categoryRepository->save($data);
    }

    /**
     * Update categoryRepository data
     * Store to DB if there are no errors.
     *
     * @param array $data
     * @return String
     */
    public function update(array $data, int $id)
    {
        DB::beginTransaction();
        try {
            $categoryRepository = $this->categoryRepository->update($data, $id);
            DB::commit();
            return $categoryRepository;
        } catch (Exception $e) {
            DB::rollBack();
            report($e);
            throw new InvalidArgumentException('Unable to update post data');
        }
    }

    /**
     * Delete categoryRepository by id.
     *
     * @param $id
     * @return String
     */
    public function deleteById(int $id)
    {
        DB::beginTransaction();
        try {
            $categoryRepository = $this->categoryRepository->delete($id);
            DB::commit();
            return $categoryRepository;
        } catch (Exception $e) {
            DB::rollBack();
            report($e);
            throw new InvalidArgumentException('Unable to delete post data');
        }
    }

}
