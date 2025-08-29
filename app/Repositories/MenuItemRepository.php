<?php
namespace App\Repositories;

use App\Models\MenuItem;

class MenuItemRepository
{
	 /**
     * @var MenuItem
     */
    protected MenuItem $menuItem;

    /**
     * MenuItem constructor.
     *
     * @param MenuItem $menuItem
     */
    public function __construct(MenuItem $menuItem)
    {
        $this->menuItem = $menuItem;
    }

    /**
     * Get all menuItem.
     *
     * @return MenuItem $menuItem
     */
    public function all()
    {
        return $this->menuItem->get();
    }

     /**
     * Get menuItem by id
     *
     * @param $id
     * @return mixed
     */
    public function getById(int $id)
    {
        return $this->menuItem->find($id);
    }

    /**
     * Save MenuItem
     *
     * @param $data
     * @return MenuItem
     */
     public function save(array $data)
    {
        return MenuItem::create($data);
    }

     /**
     * Update MenuItem
     *
     * @param $data
     * @return MenuItem
     */
    public function update(array $data, int $id)
    {
        $menuItem = $this->menuItem->find($id);
        $menuItem->update($data);
        return $menuItem;
    }

    /**
     * Delete MenuItem
     *
     * @param $data
     * @return MenuItem
     */
   	 public function delete(int $id)
    {
        $menuItem = $this->menuItem->find($id);
        $menuItem->delete();
        return $menuItem;
    }
}
