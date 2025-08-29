<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VenueOpeningHourResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'venue_id' => $this->venue_id,
            'day' => $this->day,
            'open' => $this->open,
            'close' => $this->close,
            'deleted_at' => $this->deleted_at,
        ];
    }
}
