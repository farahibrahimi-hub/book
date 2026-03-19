<?php

namespace App\Enums;

enum ReservationStatus: string
{
    case Active = 'active';
    case Returned = 'returned';
    case Late = 'late';
    case Expired = 'expired';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Active => 'Active',
            self::Returned => 'Returned',
            self::Late => 'Late',
            self::Expired => 'Expired',
            self::Cancelled => 'Cancelled',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Active => 'blue',
            self::Returned => 'green',
            self::Late => 'red',
            self::Expired => 'gray',
            self::Cancelled => 'yellow',
        };
    }

    public function isTerminal(): bool
    {
        return in_array($this, [self::Returned, self::Expired, self::Cancelled]);
    }
}
