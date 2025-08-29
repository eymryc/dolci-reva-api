<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoomResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'property_id' => $this->property_id,
            'name' => $this->name,
            'description' => $this->description,
            'max_guests' => $this->max_guests,
            'price' => $this->price,
            'type' => $this->type,
            'standing' => $this->standing,
            'is_available' => $this->is_available,
            'is_active' => $this->is_active,
            'deleted_at' => $this->deleted_at,
        ];
    }
}
