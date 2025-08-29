<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AddressResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'address' => $this->address,
            'state' => $this->state,
            'street' => $this->street,
            'postal_code' => $this->postal_code,
            'city' => $this->city,
            'country' => $this->country,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'place_id' => $this->place_id,
            'user_id' => $this->user_id,
            'deleted_at' => $this->deleted_at,
        ];
    }
}
