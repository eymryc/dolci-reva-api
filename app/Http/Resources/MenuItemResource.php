<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MenuItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'menu_id' => $this->menu_id,
            'name' => $this->name,
            'description' => $this->description,
            'category' => $this->category,
            'price' => $this->price,
            'is_vegetarian' => $this->is_vegetarian,
            'is_vegan' => $this->is_vegan,
            'is_gluten_free' => $this->is_gluten_free,
            'position' => $this->position,
            'image_url' => $this->image_url,
        ];
    }
}
