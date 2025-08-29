<?php
namespace App\Repositories;

use App\Models\Menu;

class MenuRepository
{
	 /**
     * @var Menu
     */
    protected Menu $menu;

    /**
     * Menu constructor.
     *
     * @param Menu $menu
     */
    public function __construct(Menu $menu)
    {
        $this->menu = $menu;
    }

    /**
     * Get all menu.
     *
     * @return Menu $menu
     */
    public function all()
    {
        return $this->menu->get();
    }

     /**
     * Get menu by id
     *
     * @param $id
     * @return mixed
     */
    public function getById(int $id)
    {
        return $this->menu->find($id);
    }

    /**
     * Save Menu
     *
     * @param $data
     * @return Menu
     */
     public function save(array $data)
    {
        return Menu::create($data);
    }

     /**
     * Update Menu
     *
     * @param $data
     * @return Menu
     */
    public function update(array $data, int $id)
    {
        $menu = $this->menu->find($id);
        $menu->update($data);
        return $menu;
    }

    /**
     * Delete Menu
     *
     * @param $data
     * @return Menu
     */
   	 public function delete(int $id)
    {
        $menu = $this->menu->find($id);
        $menu->delete();
        return $menu;
    }
}
