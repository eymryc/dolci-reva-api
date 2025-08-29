<?php

namespace App\Repositories;

use App\Models\Property;
use Illuminate\Support\Str;
use App\Services\FileService;
use Illuminate\Support\Facades\Auth;

class PropertyRepository
{
    /**
     * @var Property
     */
    protected Property $property;

    /**
     * Property constructor.
     *
     * @param Property $property
     */
    public function __construct(Property $property)
    {
        $this->property = $property;
    }

    /**
     * Get all property.
     *
     * @return Property $property
     */
    public function all()
    {
        return $this->property->get();
    }

    /**
     * Get all property with pagination.
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
     public function paginate(int $perPage = 15)
    {
        return $this->property->paginate($perPage);
    }

    /**
     * Get property by id
     *
     * @param $id
     * @return mixed
     */
    public function getById(int $id)
    {
        return $this->property->with('amenities', 'images')->find($id);
    }

    /**
     * Save Property
     *
     * @param $data
     * @return Property
     */
    public function save(array $data)
    {
        // Assuming you have a FileService for handling file uploads
        $fileService = new FileService();

        // Assure that the user_id is set to the currently authenticated user's ID
        $data['owner_id'] = Auth::id();

        // Extract images and amenities from the data array
        $images = $data['images'] ?? [];

        // Extract amenities from the data array
        $amenities = $data['amenities'] ?? [];

        // Remove images from the data array
        unset($data['images']);

        // Remove images and amenities from the data array
        unset($data['amenities']);

        // Create the property
        $property = Property::create($data);


        // If images are provided, upload them
        // and associate them with the property
        if (is_array($images) && count($images) > 0 && collect($images)->filter()->isEmpty() === false) {

            // Formate le titre pour le nom du dossier
            $slugTitle = Str::slug($data['name'] ?? 'property-' . $property->id);

            // Crée un nom de dossier général pour l'utilisateur connecté
            $folderName = "user_{$data['owner_id']}/properties/{$slugTitle}";

            // dd($folderName);
            // Upload multiple
            $paths = $fileService->uploadMultiple($images, $folderName);
            // dd($paths);

            // Attach images to the property
            foreach ($paths as $path) {
                $property->images()->create([
                    'path' => "storage/" . $path,
                ]);
            }
        }

        // Attach images to the property
        if (is_array($amenities)) {
            // Sync amenities with the property
            // Assuming amenities are stored as an array of IDs
            $property->amenities()->sync(array_values($amenities));
        }

        // Return the created property
        return $property;
    }

    /**
     * Update Property
     *
     * @param $data
     * @return Property
     */
    // public function update(array $data, int $id)
    // {
    //     $property = $this->property->find($id);
    //     $property->update($data);
    //     return $property;
    // }
    public function update(array $data, int $id)
    {
        // Assuming you have a FileService for handling file uploads
        $fileService = new FileService();

        // Extract images and amenities from the data array
        $images = $data['images'] ?? [];
        $amenities = $data['amenities'] ?? [];

        // Remove images and amenities from the data array
        unset($data['images']);
        unset($data['amenities']);

        // Find the property by ID
        $property = $this->property->find($id);

        // Update the property with the provided data
        $property->update($data);

        // If images are provided, upload them
        if (is_array($images) && count($images) > 0 && collect($images)->filter()->isEmpty() === false) {
            // Formate le titre pour le nom du dossier
            $slugTitle = Str::slug($data['name'] ?? 'property-' . $property->id);

            // Crée un nom de dossier général pour l'utilisateur connecté
            $folderName = "user_{$data['owner_id']}/properties/{$slugTitle}";

            // Upload multiple
            $paths = $fileService->uploadMultiple($images, $folderName);

            // Attach images to the property
            foreach ($paths as $path) {
                $property->images()->create([
                    'path' => "storage/" . $path,
                ]);
            }
        }

        // Sync amenities with the property
        if (is_array($amenities)) {
            $property->amenities()->sync(array_values($amenities));
        }

        return $property;
    }

    /**
     * Delete Property
     *
     * @param $data
     * @return Property
     */
    public function delete(int $id)
    {
        $property = $this->property->find($id);
        $property->delete();
        return $property;
    }
}
