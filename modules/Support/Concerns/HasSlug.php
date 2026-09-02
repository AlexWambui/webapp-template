<?php

namespace Modules\Support\Concerns;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin Model
 */
trait HasSlug
{
    protected static function bootHasSlug()
    {
        static::creating(function ($model) {
            $model->slug = $model->generateUniqueSlug($model->name);
        });

        static::updating(function ($model) {
            if ($model->isDirty('name')) {
                $model->slug = $model->generateUniqueSlug($model->name);
            }
        });
    }

    /**
     * Generate a unique slug for the model
     */
    protected function generateUniqueSlug($name)
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $count = 1;

        while ($this->slugExists($slug)) {
            $slug = $originalSlug . '-' . $count++;
        }

        return $slug;
    }

    /**
     * Check if a slug already exists
     */
    protected function slugExists($slug)
    {
        $query = static::where('slug', $slug);
        
        if ($this->exists) {
            $query->where('id', '!=', $this->id);
        }
        
        return $query->exists();
    }

    /**
     * Preview what the slug will be for a given name
     */
    public static function previewSlug($name, $excludeId = null)
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $count = 1;

        $query = static::where('slug', $slug);
        
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        
        while ($query->exists()) {
            $slug = $originalSlug . '-' . $count++;
            $query = static::where('slug', $slug);
            
            if ($excludeId) {
                $query->where('id', '!=', $excludeId);
            }
        }

        return $slug;
    }
}