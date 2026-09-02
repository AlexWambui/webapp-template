<?php

namespace Modules\User\Enums;

enum UserStatuses: int
{
    case INACTIVE = 0;
    case ACTIVE = 1;
    case SUSPENDED = 2;

    public function label(): string
    {
        return match($this) {
            self::INACTIVE => 'Inactive',
            self::ACTIVE => 'Active',
            self::SUSPENDED => 'Suspended'
        };
    }

    public static function labels():array
    {
        $labels = [];

        foreach(self::cases() as $status) {
            $labels[$status->value] = $status->label();
        }

        return $labels;
    }
}
