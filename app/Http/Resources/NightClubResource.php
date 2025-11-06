<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NightClubResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'owner_id' => $this->owner_id,
            'name' => $this->name,
            'description' => $this->description,
            'address' => $this->address,
            'city' => $this->city,
            'country' => $this->country,
            'opening_hours' => $this->opening_hours,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'is_active' => $this->is_active,
            
            // Champs spécifiques aux night clubs
            'age_restriction' => $this->age_restriction,
            'parking' => $this->parking,
            
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            
            // Relations
            'amenities' => $this->whenLoaded('amenities', function () {
                return $this->amenities->map(function ($amenity) {
                    return [
                        'id' => $amenity->id,
                        'name' => $amenity->name
                    ];
                });
            }),

            // Media Library - Images
            'main_image_url' => $this->main_image_url,
            'main_image_thumb_url' => $this->main_image_thumb_url,
            'gallery_images' => $this->gallery_images,
            'all_images' => $this->all_images,

            'areas' => $this->whenLoaded('areas', function () {
                return $this->areas->map(function ($area) {
                    return [
                        'id' => $area->id,
                        'area_name' => $area->area_name,
                        'location' => $area->location,
                        'area_type' => $area->area_type,
                        'is_active' => $area->is_active,
                        'minimum_spend' => $area->minimum_spend,
                        'table_fee' => $area->table_fee,
                        'amenities' => $this->whenLoaded('areas.amenities', function () use ($area) {
                            return $area->amenities->map(function ($amenity) {
                                return [
                                    'id' => $amenity->id,
                                    'name' => $amenity->name
                                ];
                            });
                        }),
                        'display_name' => $area->display_name,
                        'location_description' => $area->location_description,
                        'type_description' => $area->type_description,
                        'minimum_spend_formatted' => $area->minimum_spend_formatted,
                        'table_fee_formatted' => $area->table_fee_formatted,
                        'total_cost_formatted' => $area->total_cost_formatted,
                        'amenities_string' => $area->amenities_string
                    ];
                });
            }),

            'owner' => $this->whenLoaded('owner', function () {
                return [
                    'id' => $this->owner->id,
                    'first_name' => $this->owner->first_name,
                    'last_name' => $this->owner->last_name,
                    'email' => $this->owner->email
                ];
            }),
        ];
    }
}