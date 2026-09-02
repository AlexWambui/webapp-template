<?php

namespace Modules\Product\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductHomePageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'price' => $this->price,
            'stock' => 30,
            'sku' => $this->sku,
            'category_name' => $this->category_name,
            'description' => $this->description,
            'is_new' => (bool) $this->is_new,
            'is_featured' => (bool) $this->is_featured,
            'is_active' => (bool) $this->is_active,
            'thumbnail_url' => $this->thumbnail_url
        ];
    }
}