<?php

namespace Modules\Order\Enums;

enum OrderStatusEnum: string
{
    case PENDING = 'pending';
    case CONFIRMED = 'confirmed';
    case PROCESSING = 'processing';
    case READY_FOR_PICKUP = 'ready_for_pickup';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';
    case REFUNDED = 'refunded';
    case RETURNED = 'returned';
    
    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Pending',
            self::CONFIRMED => 'Confirmed',
            self::PROCESSING => 'Processing',
            self::READY_FOR_PICKUP => 'Ready for Pickup',
            self::COMPLETED => 'Completed',
            self::CANCELLED => 'Cancelled',
            self::REFUNDED => 'Refunded',
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
        return $this === self::COMPLETED;
    }
}