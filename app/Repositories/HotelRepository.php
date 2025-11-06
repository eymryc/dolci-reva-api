<?php

namespace App\Repositories;

use App\Models\Hotel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;

class HotelRepository
{
    /**
     * @var Hotel
     */
    protected $hotel;

    public function __construct(Hotel $hotel)
    {
        $this->hotel = $hotel;
    }

    /**
     * Get all hotels.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all(int $perPage = 15)
    {
        return $this->hotel->with(['owner', 'hotelRooms', 'media', 'amenities'])->paginate($perPage);
    }

    /**
     * Get all hotels with pagination.
     *
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function paginate(int $perPage = 15)
    {
        return $this->hotel->with(['owner', 'hotelRooms', 'media', 'amenities'])->paginate($perPage);
    }

    /**
     * Get hotel by id.
     *
     * @param int $id
     * @return Hotel|null
     */
    public function getById(int $id)
    {
        return $this->hotel->with(['owner', 'hotelRooms', 'media', 'amenities'])->find($id);
    }

    /**
     * Save hotel.
     *
     * @param array $data
     * @return Hotel
     */
    public function save(array $data)
    {
        // Ajouter l'owner_id depuis l'utilisateur authentifié
        $data['owner_id'] = Auth::id();

        // Séparer les images et amenities des données principales
        $images = $data['images'] ?? [];
        $amenities = $data['amenities'] ?? [];

        // Retirer les images et amenities du tableau de données
        unset($data['images'], $data['amenities']);

        // Créer l'hôtel
        $hotel = Hotel::create($data);

        // Gérer les images avec Media Library
        if (is_array($images) && count($images) > 0 && collect($images)->filter()->isNotEmpty()) {
            foreach ($images as $index => $image) {
                if ($image instanceof UploadedFile) {
                    // First image goes to 'images' collection (main image)
                    // Others go to 'gallery' collection
                    $collection = $index === 0 ? 'images' : 'gallery';
                    $hotel->addMediaFromRequest("images.{$index}")
                        ->toMediaCollection($collection);
                }
            }
        }

        // Gérer les amenities
        if (is_array($amenities) && count($amenities) > 0) {
            $hotel->amenities()->sync(array_values($amenities));
        }

        return $hotel->load('owner', 'hotelRooms', 'media', 'amenities');
    }

    /**
     * Update hotel.
     *
     * @param array $data
     * @param int $id
     * @return Hotel|null
     */
    public function update(array $data, int $id)
    {
        $hotel = $this->hotel->find($id);

        if (!$hotel) {
            return null;
        }

        // Séparer les images et amenities des données principales
        $images = $data['images'] ?? null;
        $amenities = $data['amenities'] ?? null;

        // Retirer les images et amenities du tableau de données
        unset($data['images'], $data['amenities']);

        // Mettre à jour l'hôtel
        $hotel->update($data);

        // Gérer les images si fournies avec Media Library
        if (is_array($images) && count($images) > 0 && collect($images)->filter()->isNotEmpty()) {
            foreach ($images as $index => $image) {
                if ($image instanceof UploadedFile) {
                    // First image goes to 'images' collection (main image)
                    // Others go to 'gallery' collection
                    $collection = $index === 0 ? 'images' : 'gallery';
                    $hotel->addMediaFromRequest("images.{$index}")
                        ->toMediaCollection($collection);
                }
            }
        }

        // Gérer les amenities si fournies
        if ($amenities !== null) {
            $hotel->amenities()->sync(array_values($amenities));
        }

        return $hotel->load('owner', 'hotelRooms', 'media', 'amenities');
    }

    /**
     * Delete hotel by id.
     *
     * @param int $id
     * @return bool
     */
    public function deleteById(int $id)
    {
        $hotel = $this->hotel->find($id);
        if (!$hotel) {
            throw new \Exception('Hotel not found');
        }
        
        return $hotel->delete();
    }

    /**
     * Delete hotel (alias for deleteById)
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id)
    {
        return $this->deleteById($id);
    }

    /**
     * Get hotels by owner.
     *
     * @param int $ownerId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getByOwner(int $ownerId)
    {
        return $this->hotel->with(['owner', 'hotelRooms', 'media', 'amenities'])
            ->where('owner_id', $ownerId)
            ->get();
    }

    /**
     * Get available hotels.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAvailable()
    {
        return $this->hotel->with(['owner', 'hotelRooms', 'media', 'amenities'])
            ->get();
    }

    /**
     * Search hotels by criteria.
     *
     * @param array $criteria
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function search(array $criteria)
    {
        $query = $this->hotel->with(['owner', 'hotelRooms', 'media']);

        if (isset($criteria['city'])) {
            $query->where('city', 'like', '%' . $criteria['city'] . '%');
        }

        if (isset($criteria['country'])) {
            $query->where('country', 'like', '%' . $criteria['country'] . '%');
        }

        if (isset($criteria['star_rating'])) {
            $query->where('star_rating', $criteria['star_rating']);
        }

        return $query->get();
    }

    /**
     * Add media to hotel using Media Library
     *
     * @param int $hotelId
     * @param UploadedFile $file
     * @param string $collection
     * @return \Spatie\MediaLibrary\MediaCollections\Models\Media
     */
    public function addMedia(int $hotelId, UploadedFile $file, string $collection = 'gallery')
    {
        $hotel = $this->hotel->findOrFail($hotelId);
        return $hotel->addMediaFromRequest('file')
            ->toMediaCollection($collection);
    }

    /**
     * Clear media collection for hotel
     *
     * @param int $hotelId
     * @param string $collection
     * @return bool
     */
    public function clearMediaCollection(int $hotelId, string $collection)
    {
        $hotel = $this->hotel->findOrFail($hotelId);
        $hotel->clearMediaCollection($collection);
        return true;
    }

    /**
     * Get hotel with media data
     *
     * @param int $id
     * @return Hotel
     */
    public function getWithMedia(int $id)
    {
        return $this->hotel->with(['owner', 'hotelRooms', 'media', 'amenities'])
            ->findOrFail($id);
    }

}
