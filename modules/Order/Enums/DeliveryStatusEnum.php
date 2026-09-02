<?php

namespace Modules\Order\Enums;

enum DeliveryStatusEnum: string
{
    case PENDING = 'pending';
    case PICKED_UP = 'picked_up';
    case IN_TRANSIT = 'in_transit';
    case OUT_FOR_DELIVERY = 'out_for_delivery';
    case DELIVERED = 'delivered';
    case DELIVERY_FAILED = 'delivery_failed';
    case RETURNED = 'returned';
    
    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Pending',
            self::PICKED_UP => 'Picked Up',
            self::IN_TRANSIT => 'In Transit',
            self::OUT_FOR_DELIVERY => 'Out for Delivery',
            self::DELIVERED => 'Delivered',
            self::DELIVERY_FAILED => 'Delivery Failed',
            self::RETURNED => 'Returned',
        };
    }

    public static function labels(): array
    {
        $labels = [];

        foreach(self::cases() as $status) {
            $labels[$status->value] = $status->label();
        }

        return $labels;
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    
    public static function fromLabel(string $label): ?self
    {
        foreach (self::cases() as $case) {
            if ($case->label() === $label) {
                return $case;
            }
        }
        return null;
    }

    public static function random(): self
    {
        return self::cases()[array_rand(self::cases())];
    }

    public function isPending(): bool
    {
        return $this === self::PENDING;
    }

    public function isCompleted(): bool
    {
        return $this === self::DELIVERED;
    }
}