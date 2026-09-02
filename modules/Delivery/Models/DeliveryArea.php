<?php

namespace Modules\Delivery\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Delivery\Models\DeliveryLocation;
use App\Concerns\HasUuid;
use App\Concerns\HasSlug;


class DeliveryArea extends Model
{
    use HasUuid, HasSlug;

    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function location(): BelongsTo
    {
        return $this->belongsTo(DeliveryLocation::class, 'delivery_location_id');
    }
}