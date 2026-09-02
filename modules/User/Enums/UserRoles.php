<?php

namespace Modules\User\Enums;

enum UserRoles: int
{
    case SUPER_ADMIN = 0;
    case ADMIN = 1;
    case CASHIER = 2;
    case CUSTOMER = 3;

    public function label(): string
    {
        return match($this) {
            self::SUPER_ADMIN => 'Super Admin',
            self::ADMIN => 'Admin',
            self::CASHIER => 'Cashier',
            self::CUSTOMER => 'Customer',
        };
    }

    public static function labels():array
    {
        $labels = [];

        foreach(self::cases() as $role) {
            $labels[$role->value] = $role->label();
        }

        return $labels;
    }

    public static function tryFromLabel(string $label): ?self
    {
        foreach (self::cases() as $role) {
            if (strtolower($role->label()) === strtolower(trim($label))) {
                return $role;
            }
        }
        return null;
    }
}
