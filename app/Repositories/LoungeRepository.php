<?php

namespace App\Repositories;

use App\Models\Lounge;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\UploadedFile;

class LoungeRepository
{
    /**
     * @var Lounge
     */
    protected Lounge $lounge;

    public function __construct(Lounge $lounge)
    {
        $this->lounge = $lounge;
    }

    /**
     * Get all lounges.
     */
    public function all()
    {
        return $this->lounge->with(['owner', 'tables', 'media', 'amenities'])->get();
    }

    /**
     * Get all lounges with pagination.
     */
    public function paginate(int $perPage = 15)
    {
        return $this->lounge->with(['owner', 'tables', 'media', 'amenities'])->paginate($perPage);
    }

    /**
     * Get lounge by id.
     */
    public function getById(int $id)
    {
        return $this->lounge->with(['owner', 'tables', 'media', 'amenities'])->find($id);
    }

    /**
     * Save lounge.
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

        // Create the lounge
        $lounge = Lounge::create($data);

        // Handle images with Media Library
        if (is_array($images) && count($images) > 0 && collect($images)->filter()->isNotEmpty()) {
            foreach ($images as $index => $image) {
                if ($image instanceof UploadedFile) {
                    // First image goes to 'images' collection (main image)
                    // Others go to 'gallery' collection
                    $collection = $index === 0 ? 'images' : 'gallery';
                    $lounge->addMediaFromRequest("images.{$index}")
                        ->toMediaCollection($collection);
                }
            }
        }

        // Handle amenities
        if (is_array($amenities) && count($amenities) > 0) {
            $lounge->amenities()->sync(array_values($amenities));
        }

        return $lounge->load('owner', 'tables', 'media', 'amenities');
    }

    /**
     * Update lounge.
     */
    public function update(array $data, int $id)
    {
        $lounge = $this->lounge->find($id);

        if (!$lounge) {
            return null;
        }

        // Separate images and amenities from main data
        $images = $data['images'] ?? null;
        $amenities = $data['amenities'] ?? null;

        // Remove images and amenities from main data
        unset($data['images'], $data['amenities']);

        // Update the lounge
        $lounge->update($data);

        // Handle images if provided with Media Library
        if (is_array($images) && count($images) > 0 && collect($images)->filter()->isNotEmpty()) {
            foreach ($images as $index => $image) {
                if ($image instanceof UploadedFile) {
                    // First image goes to 'images' collection (main image)
                    // Others go to 'gallery' collection
                    $collection = $index === 0 ? 'images' : 'gallery';
                    $lounge->addMediaFromRequest("images.{$index}")
                        ->toMediaCollection($collection);
                }
            }
        }

        // Handle amenities if provided
        if ($amenities !== null) {
            $lounge->amenities()->sync(array_values($amenities));
        }

        return $lounge->load('owner', 'tables', 'media', 'amenities');
    }

    /**
     * Delete lounge by id.
     */
    public function delete(int $id)
    {
        $lounge = $this->lounge->find($id);
        if ($lounge) {
            return $lounge->delete();
        }
        return false;
    }


    /**
     * Get available lounges.
     */
    public function getAvailable()
    {
        return $this->lounge->with(['owner', 'tables', 'media', 'amenities'])
            ->where('is_active', true)
            ->get();
    }

    /**
     * Get available tables for a lounge on a specific date and time.
     */
    public function getAvailableTables(int $loungeId, string $date, string $time, int $guests)
    {
        $lounge = $this->getById($loungeId);
        
        if (!$lounge) {
            return collect();
        }

        return $lounge->getAvailableTables($date, $time, $guests);
    }

    /**
     * Get recommended tables for a lounge.
     */
    public function getRecommendedTables(int $loungeId, string $date, string $time, int $guests, string $preference = null)
    {
        $lounge = $this->getById($loungeId);
        
        if (!$lounge) {
            return collect();
        }

        return $lounge->getRecommendedTables($date, $time, $guests, $preference);
    }

    /**
     * Add media to lounge using Media Library
     */
    public function addMedia(int $loungeId, UploadedFile $file, string $collection = 'gallery')
    {
        $lounge = $this->lounge->findOrFail($loungeId);
        return $lounge->addMediaFromRequest('file')
            ->toMediaCollection($collection);
    }

    /**
     * Clear media collection for lounge
     */
    public function clearMediaCollection(int $loungeId, string $collection)
    {
        $lounge = $this->lounge->findOrFail($loungeId);
        $lounge->clearMediaCollection($collection);
        return true;
    }

    /**
     * Get lounge with media data
     */
    public function getWithMedia(int $id)
    {
        return $this->lounge->with(['owner', 'tables', 'media', 'amenities'])
            ->findOrFail($id);
    }
}
