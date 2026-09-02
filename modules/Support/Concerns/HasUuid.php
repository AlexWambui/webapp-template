<?php

namespace Modules\Support\Concerns;

use Illuminate\Support\Str;

trait HasUuid
{
    /**
     * Boot the trait
     */

    protected static function bootHasUuid()
    {
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }
}
