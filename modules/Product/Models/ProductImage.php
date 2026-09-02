<?php

namespace Modules\Product\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductImage extends Model
{
    protected $guarded = [];

    protected $appends = ['image_url', 'thumbnail_url'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function getImageUrlAttribute(): string
    {
        if (!$this->name) {
            return asset('images/default-image.png');
        }

        return asset("storage/products/{$this->name}");
    }

    public function getThumbnailUrlAttribute(): string
    {
        if (!$this->name) {
            return asset('images/default-image.png');
        }

        return asset("storage/products/{$this->name}");
    }
}