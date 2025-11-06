<?php

namespace App\Repositories;

use App\Models\Restaurant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\UploadedFile;

class RestaurantRepository
{
    /**
     * @var Restaurant
     */
    protected Restaurant $restaurant;

    public function __construct(Restaurant $restaurant)
    {
        $this->restaurant = $restaurant;
    }

    /**
     * Get all restaurants.
     */
    public function all()
    {
        return $this->restaurant->with(['owner', 'tables', 'media', 'amenities'])->get();
    }

    /**
     * Get all restaurants with pagination.
     */
    public function paginate(int $perPage = 15)
    {
        return $this->restaurant->with(['owner', 'tables', 'media', 'amenities'])->paginate($perPage);
    }

    /**
     * Get restaurant by id.
     */
    public function getById(int $id)
    {
        return $this->restaurant->with(['owner', 'tables', 'media', 'amenities'])->find($id);
    }

    /**
     * Save restaurant.
     */
    public function save(array $data)
    {
        // Add owner_id from authenticated user
        $data['owner_id'] = Auth::id();

        // Separate images and amenities from main data
        $images = $data['images'] ?? [];
        $amenities = $data['amenities'] ?? [];

        // Remove images and amenities from main data
        unset($data['images'], $data['amenities']);

        // Create the restaurant
        $restaurant = Restaurant::create($data);

        // Handle images with Media Library
        if (is_array($images) && count($images) > 0 && collect($images)->filter()->isNotEmpty()) {
            foreach ($images as $index => $image) {
                if ($image instanceof UploadedFile) {
                    // First image goes to 'images' collection (main image)
                    // Others go to 'gallery' collection
                    $collection = $index === 0 ? 'images' : 'gallery';
                    $restaurant->addMediaFromRequest("images.{$index}")
                        ->toMediaCollection($collection);
                }
            }
        }

        // Handle amenities
        if (is_array($amenities) && count($amenities) > 0) {
            $restaurant->amenities()->sync(array_values($amenities));
        }

        return $restaurant->load('owner', 'tables', 'media', 'amenities');
    }

    /**
     * Update restaurant.
     */
    public function update(array $data, int $id)
    {
        $restaurant = $this->restaurant->find($id);

        if (!$restaurant) {
            return null;
        }

        // Separate images and amenities from main data
        $images = $data['images'] ?? null;
        $amenities = $data['amenities'] ?? null;

        // Remove images and amenities from main data
        unset($data['images'], $data['amenities']);

        // Update the restaurant
        $restaurant->update($data);

        // Handle images if provided with Media Library
        if (is_array($images) && count($images) > 0 && collect($images)->filter()->isNotEmpty()) {
            foreach ($images as $index => $image) {
                if ($image instanceof UploadedFile) {
                    // First image goes to 'images' collection (main image)
                    // Others go to 'gallery' collection
                    $collection = $index === 0 ? 'images' : 'gallery';
                    $restaurant->addMediaFromRequest("images.{$index}")
                        ->toMediaCollection($collection);
                }
            }
        }

        // Handle amenities if provided
        if ($amenities !== null) {
            $restaurant->amenities()->sync(array_values($amenities));
        }

        return $restaurant->load('owner', 'tables', 'media', 'amenities');
    }

    /**
     * Delete restaurant by id.
     */
    public function delete(int $id)
    {
        $restaurant = $this->restaurant->find($id);
        if ($restaurant) {
            return $restaurant->delete();
        }
        return false;
    }


    /**
     * Get available restaurants.
     */
    public function getAvailable()
    {
        return $this->restaurant->with(['owner', 'tables', 'media', 'amenities'])
            ->where('is_active', true)
            ->get();
    }

    /**
     * Get available tables for a restaurant on a specific date and time.
     */
    public function getAvailableTables(int $restaurantId, string $date, string $time, int $guests)
    {
        $restaurant = $this->getById($restaurantId);
        
        if (!$restaurant) {
            return collect();
        }

        return $restaurant->getAvailableTables($date, $time, $guests);
    }

    /**
     * Add media to restaurant using Media Library
     */
    public function addMedia(int $restaurantId, UploadedFile $file, string $collection = 'gallery')
    {
        $restaurant = $this->restaurant->findOrFail($restaurantId);
        return $restaurant->addMediaFromRequest('file')
            ->toMediaCollection($collection);
    }

    /**
     * Clear media collection for restaurant
     */
    public function clearMediaCollection(int $restaurantId, string $collection)
    {
        $restaurant = $this->restaurant->findOrFail($restaurantId);
        $restaurant->clearMediaCollection($collection);
        return true;
    }

    /**
     * Get restaurant with media data
     */
    public function getWithMedia(int $id)
    {
        return $this->restaurant->with(['owner', 'tables', 'media', 'amenities'])
            ->findOrFail($id);
    }
}
