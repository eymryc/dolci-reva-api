<?php
namespace App\Services;

use App\Models\Menu;
use App\Repositories\MenuRepository;
use Exception;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class MenuService
{
	/**
     * @var MenuRepository $menuRepository
     */
    protected $menuRepository;

    /**
     * DummyClass constructor.
     *
     * @param MenuRepository $menuRepository
     */
    public function __construct(MenuRepository $menuRepository)
    {
        $this->menuRepository = $menuRepository;
    }

    /**
     * Get all menuRepository.
     *
     * @return String
     */
    public function getAll()
    {
        return $this->menuRepository->all();
    }

    /**
     * Get menuRepository by id.
     *
     * @param $id
     * @return String
     */
    public function getById(int $id)
    {
        return $this->menuRepository->getById($id);
    }

    /**
     * Validate menuRepository data.
     * Store to DB if there are no errors.
     *
     * @param array $data
     * @return String
     */
    public function save(array $data)
    {
        return $this->menuRepository->save($data);
    }

    /**
     * Update menuRepository data
     * Store to DB if there are no errors.
     *
     * @param array $data
     * @return String
     */
    public function update(array $data, int $id)
    {
        DB::beginTransaction();
        try {
            $menuRepository = $this->menuRepository->update($data, $id);
            DB::commit();
            return $menuRepository;
        } catch (Exception $e) {
            DB::rollBack();
            report($e);
            throw new InvalidArgumentException('Unable to update post data');
        }
    }

    /**
     * Delete menuRepository by id.
     *
     * @param $id
     * @return String
     */
    public function deleteById(int $id)
    {
        DB::beginTransaction();
        try {
            $menuRepository = $this->menuRepository->delete($id);
            DB::commit();
            return $menuRepository;
        } catch (Exception $e) {
            DB::rollBack();
            report($e);
            throw new InvalidArgumentException('Unable to delete post data');
        }
    }

}
