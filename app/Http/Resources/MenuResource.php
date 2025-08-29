<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MenuResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'venue_id' => $this->venue_id,
            'name' => $this->name,
            'description' => $this->description,
            'type' => $this->type,
            'is_active' => $this->is_active,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'deleted_at' => $this->deleted_at,
        ];
    }
}
