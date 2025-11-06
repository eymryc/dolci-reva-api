<?php

namespace App\Repositories;

use App\Models\NightClub;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\UploadedFile;

class NightClubRepository
{
    /**
     * @var NightClub
     */
    protected NightClub $nightClub;

    public function __construct(NightClub $nightClub)
    {
        $this->nightClub = $nightClub;
    }

    /**
     * Get all night clubs.
     */
    public function all()
    {
        return $this->nightClub->with(['owner', 'areas.amenities', 'media', 'amenities'])->get();
    }

    /**
     * Get all night clubs with pagination.
     */
    public function paginate(int $perPage = 15)
    {
        return $this->nightClub->with(['owner', 'areas.amenities', 'media', 'amenities'])->paginate($perPage);
    }

    /**
     * Get night club by id.
     */
    public function getById(int $id)
    {
        return $this->nightClub->with(['owner', 'areas.amenities', 'media', 'amenities'])->find($id);
    }

    /**
     * Save night club.
     */
    public function save(array $data)
    {
        // Add owner_id from authenticated user
        $data['owner_id'] = Auth::id();

        // Separate images, amenities and area_amenities from main data
        $images = $data['images'] ?? [];
        $amenities = $data['amenities'] ?? [];
        $areaAmenities = $data['area_amenities'] ?? [];

        // Remove images, amenities and area_amenities from main data
        unset($data['images'], $data['amenities'], $data['area_amenities']);

        // Create the night club
        $nightClub = NightClub::create($data);

        // Handle images with Media Library
        if (is_array($images) && count($images) > 0 && collect($images)->filter()->isNotEmpty()) {
            foreach ($images as $index => $image) {
                if ($image instanceof UploadedFile) {
                    // First image goes to 'images' collection (main image)
                    // Others go to 'gallery' collection
                    $collection = $index === 0 ? 'images' : 'gallery';
                    $nightClub->addMediaFromRequest("images.{$index}")
                        ->toMediaCollection($collection);
                }
            }
        }

        // Handle amenities
        if (is_array($amenities) && count($amenities) > 0) {
            $nightClub->amenities()->sync(array_values($amenities));
        }

        // Handle area amenities
        if (is_array($areaAmenities) && count($areaAmenities) > 0) {
            foreach ($areaAmenities as $areaAmenity) {
                if (isset($areaAmenity['area_id']) && isset($areaAmenity['amenities'])) {
                    $area = $nightClub->areas()->find($areaAmenity['area_id']);
                    if ($area) {
                        $area->amenities()->sync(array_values($areaAmenity['amenities']));
                    }
                }
            }
        }

        return $nightClub->load('owner', 'areas.amenities', 'media', 'amenities');
    }

    /**
     * Update night club.
     */
    public function update(array $data, int $id)
    {
        $nightClub = $this->nightClub->find($id);

        if (!$nightClub) {
            return null;
        }

        // Separate images, amenities and area_amenities from main data
        $images = $data['images'] ?? null;
        $amenities = $data['amenities'] ?? null;
        $areaAmenities = $data['area_amenities'] ?? null;

        // Remove images, amenities and area_amenities from main data
        unset($data['images'], $data['amenities'], $data['area_amenities']);

        // Update the night club
        $nightClub->update($data);

        // Handle images if provided with Media Library
        if (is_array($images) && count($images) > 0 && collect($images)->filter()->isNotEmpty()) {
            foreach ($images as $index => $image) {
                if ($image instanceof UploadedFile) {
                    // First image goes to 'images' collection (main image)
                    // Others go to 'gallery' collection
                    $collection = $index === 0 ? 'images' : 'gallery';
                    $nightClub->addMediaFromRequest("images.{$index}")
                        ->toMediaCollection($collection);
                }
            }
        }

        // Handle amenities if provided
        if ($amenities !== null) {
            $nightClub->amenities()->sync(array_values($amenities));
        }

        // Handle area amenities if provided
        if ($areaAmenities !== null && is_array($areaAmenities) && count($areaAmenities) > 0) {
            foreach ($areaAmenities as $areaAmenity) {
                if (isset($areaAmenity['area_id']) && isset($areaAmenity['amenities'])) {
                    $area = $nightClub->areas()->find($areaAmenity['area_id']);
                    if ($area) {
                        $area->amenities()->sync(array_values($areaAmenity['amenities']));
                    }
                }
            }
        }

        return $nightClub->load('owner', 'areas.amenities', 'media', 'amenities');
    }

    /**
     * Delete night club by id.
     */
    public function delete(int $id)
    {
        $nightClub = $this->nightClub->find($id);
        if ($nightClub) {
            return $nightClub->delete();
        }
        return false;
    }


    /**
     * Get available night clubs.
     */
    public function getAvailable()
    {
        return $this->nightClub->with(['owner', 'areas.amenities', 'media', 'amenities'])
            ->where('is_active', true)
            ->get();
    }

    /**
     * Get available areas for a night club on a specific date and time.
     */
    public function getAvailableAreas(int $nightClubId, string $date, string $time, int $guests)
    {
        $nightClub = $this->getById($nightClubId);
        
        if (!$nightClub) {
            return collect();
        }

        return $nightClub->getAvailableAreas($date, $time, $guests);
    }

    /**
     * Get recommended areas for a night club.
     */
    public function getRecommendedAreas(int $nightClubId, string $date, string $time, int $guests, string $preference = null)
    {
        $nightClub = $this->getById($nightClubId);
        
        if (!$nightClub) {
            return collect();
        }

        return $nightClub->getRecommendedAreas($date, $time, $guests, $preference);
    }

    /**
     * Add media to night club using Media Library
     */
    public function addMedia(int $nightClubId, UploadedFile $file, string $collection = 'gallery')
    {
        $nightClub = $this->nightClub->findOrFail($nightClubId);
        return $nightClub->addMediaFromRequest('file')
            ->toMediaCollection($collection);
    }

    /**
     * Clear media collection for night club
     */
    public function clearMediaCollection(int $nightClubId, string $collection)
    {
        $nightClub = $this->nightClub->findOrFail($nightClubId);
        $nightClub->clearMediaCollection($collection);
        return true;
    }

    /**
     * Get night club with media data
     */
    public function getWithMedia(int $id)
    {
        return $this->nightClub->with(['owner', 'areas.amenities', 'media', 'amenities'])
            ->findOrFail($id);
    }
}
