<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use App\Http\Resources\CategoryResource;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'first_name'    => $this->first_name,
            'last_name'     => $this->last_name,
            'phone'         => $this->phone,
            'email'         => $this->email,
            'type'          => $this->type,
            'categories'    => CategoryResource::collection($this->whenLoaded('categories')),
        ];
    }
}
