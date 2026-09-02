<?php

namespace Modules\ContactMessage\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Concerns\HasUuid;

class ContactMessage extends Model
{
    use HasUuid;

    protected $guarded = [];

    protected $casts = [
        'is_read' => 'boolean',
        'is_resolved' => 'boolean'
    ];

    public function scopeSearch(Builder $query, $search): Builder
    {
        if (!$search) {
            return $query;
        }

        $searchTerm = strtolower($search);
        
        return $query->where(function (Builder $q) use ($searchTerm) {
            $q->whereRaw('LOWER(name) LIKE ?', ["%{$searchTerm}%"])
                ->orWhereRaw('LOWER(phone_number) LIKE ?', ["%{$searchTerm}%"])
                ->orWhereRaw('LOWER(message) LIKE ?', ["%{$searchTerm}%"]);
        });
    }

    public function scopeResolved(Builder $query, $status = null): Builder
    {
        if ($status === null) {
            return $query;
        }

        return $query->where('is_resolved', $status === 'resolved');
    }

}