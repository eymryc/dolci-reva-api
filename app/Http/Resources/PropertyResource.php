<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PropertyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'owner_id' => $this->owner_id,
            'category_id' => $this->category_id,
            'name' => $this->name,
            'description' => $this->description,
            'address' => $this->address,
            'state' => $this->state,
            'street' => $this->street,
            'city' => $this->city,
            'country' => $this->country,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'max_guests' => $this->max_guests,
            'bedrooms' => $this->bedrooms,
            'bathrooms' => $this->bathrooms,
            'piece_number' => $this->piece_number,
            'price' => $this->price,
            'type' => $this->type,
            'rental_type' => $this->rental_type,
            
             // Relations
            'amenities' => $this->whenLoaded('amenities', function () {
                return $this->amenities->pluck('id');
            }),

            'images' => $this->whenLoaded('images', function () {
                return $this->images;
            }),
        ];
    }
}
