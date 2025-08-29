<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ImageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'imageable_type' => $this->imageable_type,
            'imageable_id' => $this->imageable_id,
            'path' => $this->path,
            'deleted_at' => $this->deleted_at,
        ];
    }
}
