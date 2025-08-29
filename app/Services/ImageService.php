<?php
namespace App\Services;

use App\Models\Image;
use App\Repositories\ImageRepository;
use Exception;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class ImageService
{
	/**
     * @var ImageRepository $imageRepository
     */
    protected $imageRepository;

    /**
     * DummyClass constructor.
     *
     * @param ImageRepository $imageRepository
     */
    public function __construct(ImageRepository $imageRepository)
    {
        $this->imageRepository = $imageRepository;
    }

    /**
     * Get all imageRepository.
     *
     * @return String
     */
    public function getAll()
    {
        return $this->imageRepository->all();
    }

    /**
     * Get imageRepository by id.
     *
     * @param $id
     * @return String
     */
    public function getById(int $id)
    {
        return $this->imageRepository->getById($id);
    }

    /**
     * Validate imageRepository data.
     * Store to DB if there are no errors.
     *
     * @param array $data
     * @return String
     */
    public function save(array $data)
    {
        return $this->imageRepository->save($data);
    }

    /**
     * Update imageRepository data
     * Store to DB if there are no errors.
     *
     * @param array $data
     * @return String
     */
    public function update(array $data, int $id)
    {
        DB::beginTransaction();
        try {
            $imageRepository = $this->imageRepository->update($data, $id);
            DB::commit();
            return $imageRepository;
        } catch (Exception $e) {
            DB::rollBack();
            report($e);
            throw new InvalidArgumentException('Unable to update post data');
        }
    }

    /**
     * Delete imageRepository by id.
     *
     * @param $id
     * @return String
     */
    public function deleteById(int $id)
    {
        DB::beginTransaction();
        try {
            $imageRepository = $this->imageRepository->delete($id);
            DB::commit();
            return $imageRepository;
        } catch (Exception $e) {
            DB::rollBack();
            report($e);
            throw new InvalidArgumentException('Unable to delete post data');
        }
    }

}
