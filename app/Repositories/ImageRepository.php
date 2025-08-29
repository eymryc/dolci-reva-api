<?php
namespace App\Repositories;

use App\Models\Image;

class ImageRepository
{
	 /**
     * @var Image
     */
    protected Image $image;

    /**
     * Image constructor.
     *
     * @param Image $image
     */
    public function __construct(Image $image)
    {
        $this->image = $image;
    }

    /**
     * Get all image.
     *
     * @return Image $image
     */
    public function all()
    {
        return $this->image->get();
    }

     /**
     * Get image by id
     *
     * @param $id
     * @return mixed
     */
    public function getById(int $id)
    {
        return $this->image->find($id);
    }

    /**
     * Save Image
     *
     * @param $data
     * @return Image
     */
     public function save(array $data)
    {
        return Image::create($data);
    }

     /**
     * Update Image
     *
     * @param $data
     * @return Image
     */
    public function update(array $data, int $id)
    {
        $image = $this->image->find($id);
        $image->update($data);
        return $image;
    }

    /**
     * Delete Image
     *
     * @param $data
     * @return Image
     */
   	 public function delete(int $id)
    {
        $image = $this->image->find($id);
        $image->delete();
        return $image;
    }
}
