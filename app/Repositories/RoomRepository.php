<?php

namespace App\Repositories;

use App\Models\Room;
use Illuminate\Support\Str;
use App\Services\FileService;
use Illuminate\Support\Facades\Auth;

class RoomRepository
{
    /**
     * @var Room
     */
    protected Room $room;

    /**
     * Room constructor.
     *
     * @param Room $room
     */
    public function __construct(Room $room)
    {
        $this->room = $room;
    }

    /**
     * Get all room.
     *
     * @return Room $room
     */
    public function all()
    {
        return $this->room->get();
    }

    /**
     * Get all room with pagination.
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function paginate(int $perPage = 15)
    {
        return $this->room->paginate($perPage);
    }

    /**
     * Get room by id
     *
     * @param $id
     * @return mixed
     */
    public function getById(int $id)
    {
        return $this->room->find($id);
    }

    /**
     * Save Room
     *
     * @param $data
     * @return Room
     */
    public function save(array $data)
    {
        // Assuming you have a FileService for handling file uploads
        $fileService = new FileService();

        // Assure that the user_id is set to the currently authenticated user's ID
        $data['owner_id'] = Auth::id();

        // Gestion des images et amenities
        $images = $data['images'] ?? [];
        $amenities = $data['amenities'] ?? [];

        // Set default values for is_available and is_active if not provided
        // This is to ensure that these fields are always set
        // and to avoid null values in the database
        // You can adjust the default values as per your requirements
        // For example, you might want to set is_available to 1 (true) and
        // is_active to 1 (true) if they are not provided in the request
        // This ensures that the room is available and active by default
        // You can also set these values based on your business logic
        // For example, if a room is created, it is usually available and active
        // unless specified otherwise
        $data['is_available'] = $data['is_available'] ?? 1;
        $data['is_active'] = $data['is_active'] ?? 1;



        // Remove images and amenities from the data array
        // as they will be handled separately
        // and are not part of the Room model attributes
        unset($data['images'], $data['amenities']);

        // Convert enums en string si besoin
        if (isset($data['type']) && is_object($data['type'])) {
            $data['type'] = $data['type']->value;
        }
        if (isset($data['standing']) && is_object($data['standing'])) {
            $data['standing'] = $data['standing']->value;
        }

        // DEBUG
        //
        // dd($images, $amenities);
        // dd(collect($images)->filter()->isEmpty());

        // Create the room
        $room = Room::create($data);

        // If images are provided, upload them
        // and associate them with the property
        if (is_array($images) && count($images) > 0 && collect($images)->filter()->isEmpty() === false) {

            // dd(collect($images)->filter()->isEmpty());

            // Formate le titre pour le nom du dossier
            $slugTitle = Str::slug($data['name']);

            // Crée un nom de dossier général pour l'utilisateur connecté
            $folderName = "user_{$data['owner_id']}/properties/rooms/{$slugTitle}";

            // Debug
            //
            // dd($folderName);

            // Upload multiple
            $paths = $fileService->uploadMultiple($images, $folderName);

            // Attach images to the property
            foreach ($paths as $path) {
                $room->images()->create([
                    'path' => "storage/" . $path,
                ]);
            }
        }

        // Attach images to the property
        if (is_array($amenities)) {
            // Sync amenities with the property
            // Assuming amenities are stored as an array of IDs
            $room->amenities()->sync(array_values($amenities));
        }

        // Return the created room
        return $room;
    }

    /**
     * Update Room
     *
     * @param $data
     * @return Room
     */
    public function update(array $data, int $id)
    {
        $room = $this->room->find($id);
        $room->update($data);
        return $room;
    }

    /**
     * Delete Room
     *
     * @param $data
     * @return Room
     */
    public function delete(int $id)
    {
        $room = $this->room->find($id);
        $room->delete();
        return $room;
    }
}
