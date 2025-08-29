<?php
namespace App\Repositories;

use App\Models\Category;

class CategoryRepository
{
	 /**
     * @var Category
     */
    protected Category $category;

    /**
     * Category constructor.
     *
     * @param Category $category
     */
    public function __construct(Category $category)
    {
        $this->category = $category;
    }

    /**
     * Get all category.
     *
     * @return Category $category
     */
    public function all()
    {
        return $this->category->get();
    }

    /**
     * Get all category with pagination.
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function paginate(int $perPage = 15)
    {
        return $this->category->paginate($perPage);
    }

     /**
     * Get category by id
     *
     * @param $id
     * @return mixed
     */
    public function getById(int $id)
    {
        return $this->category->find($id);
    }

    /**
     * Save Category
     *
     * @param $data
     * @return Category
     */
     public function save(array $data)
    {   
        return Category::create($data);
    }

     /**
     * Update Category
     *
     * @param $data
     * @return Category
     */
    public function update(array $data, int $id)
    {
        $category = $this->category->find($id);
        $category->update($data);
        return $category;
    }

    /**
     * Delete Category
     *
     * @param $data
     * @return Category
     */
   	 public function delete(int $id)
    {
        $category = $this->category->find($id);
        $category->delete();
        return $category;
    }
}
