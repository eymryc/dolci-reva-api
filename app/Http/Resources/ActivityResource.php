<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ActivityResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'organizer_id' => $this->organizer_id,
            'address_id' => $this->address_id,
            'title' => $this->title,
            'description' => $this->description,
            'type' => $this->type,
            'duration_minutes' => $this->duration_minutes,
            'equipment_provided' => $this->equipment_provided,
            'price_per_person' => $this->price_per_person,
            'deleted_at' => $this->deleted_at,
        ];
    }
}
