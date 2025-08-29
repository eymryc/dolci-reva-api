<?php
namespace App\Services;

use App\Models\MenuItem;
use App\Repositories\MenuItemRepository;
use Exception;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class MenuItemService
{
	/**
     * @var MenuItemRepository $menuItemRepository
     */
    protected $menuItemRepository;

    /**
     * DummyClass constructor.
     *
     * @param MenuItemRepository $menuItemRepository
     */
    public function __construct(MenuItemRepository $menuItemRepository)
    {
        $this->menuItemRepository = $menuItemRepository;
    }

    /**
     * Get all menuItemRepository.
     *
     * @return String
     */
    public function getAll()
    {
        return $this->menuItemRepository->all();
    }

    /**
     * Get menuItemRepository by id.
     *
     * @param $id
     * @return String
     */
    public function getById(int $id)
    {
        return $this->menuItemRepository->getById($id);
    }

    /**
     * Validate menuItemRepository data.
     * Store to DB if there are no errors.
     *
     * @param array $data
     * @return String
     */
    public function save(array $data)
    {
        return $this->menuItemRepository->save($data);
    }

    /**
     * Update menuItemRepository data
     * Store to DB if there are no errors.
     *
     * @param array $data
     * @return String
     */
    public function update(array $data, int $id)
    {
        DB::beginTransaction();
        try {
            $menuItemRepository = $this->menuItemRepository->update($data, $id);
            DB::commit();
            return $menuItemRepository;
        } catch (Exception $e) {
            DB::rollBack();
            report($e);
            throw new InvalidArgumentException('Unable to update post data');
        }
    }

    /**
     * Delete menuItemRepository by id.
     *
     * @param $id
     * @return String
     */
    public function deleteById(int $id)
    {
        DB::beginTransaction();
        try {
            $menuItemRepository = $this->menuItemRepository->delete($id);
            DB::commit();
            return $menuItemRepository;
        } catch (Exception $e) {
            DB::rollBack();
            report($e);
            throw new InvalidArgumentException('Unable to delete post data');
        }
    }

}
