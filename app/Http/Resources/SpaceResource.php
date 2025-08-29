<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SpaceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'venue_id' => $this->venue_id,
            'name' => $this->name,
            'type' => $this->type,
            'min_guests' => $this->min_guests,
            'max_guests' => $this->max_guests,
            'is_hourly_rate' => $this->is_hourly_rate,
            'deleted_at' => $this->deleted_at,
        ];
    }
}
