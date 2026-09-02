<?php

namespace Modules\Product\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductIndexPageResource extends JsonResource
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
            'sku' => $this->sku,
            'price' => $this->price,
            'cost_price' => $this->cost_price,
            'has_discount' => $this->has_discount,
            'discount_display' => $this->discount_display,
            'discounted_price' => $this->discounted_price,
            'thumbnail_url' => $this->thumbnail_url,
            'category_name' => $this->category_name,
            'description' => $this->description,
            'stock' => 30, // TODO: use accurate stock
            'track_inventory' => (bool) $this->track_inventory,
            'low_stock_threshold' => $this->low_stock_threshold,
            'is_featured' => (bool) $this->is_featured,
            'is_new' => (bool) $this->is_new,
            'is_active' => (bool) $this->is_active,
        ];
    }
}