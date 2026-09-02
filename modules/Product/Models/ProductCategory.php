<?php

namespace Modules\Product\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Concerns\HasUuid;
use App\Concerns\HasSlug;

class ProductCategory extends Model
{
    use HasUuid, HasSlug;

    protected $guarded = [];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'product_category_id');
    }
}