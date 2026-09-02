<?php

namespace Modules\User\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Illuminate\Support\Facades\Storage;
use App\Concerns\HasUuid;
use Modules\User\Enums\UserRoles;
use Modules\User\Enums\UserStatuses;
use Illuminate\Database\Eloquent\Builder;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable;
    use HasUuid;
    
    protected $guarded = [];

    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
    ];

    protected $appends = [
        'role_label',
        'status_label',
        'image_url',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_confirmed_at' => 'datetime',
            'role' => UserRoles::class,
            'status' => UserStatuses::class
        ];
    }

    public function hasRole(string $role_name): bool
    {
        // Convert string role name to enum value
        foreach (UserRoles::cases() as $role) {
            if (strtolower($role->name) === strtolower($role_name)) {
                return $this->role->value === $role->value;
            }
        }
        return false;
    }

    public function hasAnyRole(array $role_names): bool
    {
        foreach ($role_names as $role_name) {
            if ($this->hasRole($role_name)) {
                return true;
            }
        }

        return false;
    }

    public function isActive(): bool
    {
        return $this->status === UserStatuses::ACTIVE;
    }

    public function getIsActiveAttribute(): bool
    {
        return $this->isActive();
    }

    public function getRoleLabelAttribute(): string
    {
        return $this->role->label();
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->status->label();
    }

    public function getImageUrlAttribute(): ?string
    {
        if ($this->image && Storage::disk('public')->exists('users/' . $this->image)) {
            return asset('storage/users/' . $this->image);
        }
        
        return asset('assets/images/default-image.png');
    }

    public function scopeFilterByRole(Builder $query, string|int|null $role): Builder
    {
        // If role is empty string or null, don't filter
        if ($role === null || $role === '' || $role === 'null') {
            return $query;
        }

        // Handle numeric values
        if (is_numeric($role)) {
            return $query->where('role', (int) $role);
        }

        // Handle string labels (for direct label filtering)
        $roleEnum = UserRoles::tryFromLabel($role);
        if ($roleEnum) {
            return $query->where('role', $roleEnum->value);
        }

        return $query;
    }

    public function scopeOrderByRolePriority(Builder $query): Builder
    {
        return $query->orderByRaw(
            "CASE
                WHEN role = ? THEN 1
                WHEN role = ? THEN 2
                WHEN role = ? THEN 3
                WHEN role = ? THEN 4
                ELSE 5
            END ASC",
            [
                UserRoles::SUPER_ADMIN->value,
                UserRoles::ADMIN->value,
                UserRoles::CASHIER->value,
                UserRoles::CUSTOMER->value,
            ]
        )->orderBy('name');
    }

    public function scopeSearch(Builder $query, string $term): Builder
    {
        return $query->where(function($query) use ($term) {
            $query->where('name', 'LIKE', "%{$term}%")
                ->orWhere('email', 'LIKE', "%{$term}%");
        });
    }
}
