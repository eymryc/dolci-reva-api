<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TimeSlotResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'activity_id' => $this->activity_id,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'max_participants' => $this->max_participants,
            'deleted_at' => $this->deleted_at,
        ];
    }
}
