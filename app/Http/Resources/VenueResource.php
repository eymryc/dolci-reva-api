<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VenueResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'owner_id' => $this->owner_id,
            'address_id' => $this->address_id,
            'category_id' => $this->category_id,
            'name' => $this->name,
            'description' => $this->description,
            'type' => $this->type,
            'capacity' => $this->capacity,
            'deleted_at' => $this->deleted_at,
        ];
    }
}
