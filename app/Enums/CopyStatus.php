<?php

namespace App\Enums;

enum CopyStatus: string
{
    case Available = 'available';
    case Reserved = 'reserved';
    case Maintenance = 'maintenance';

    public function label(): string
    {
        return match ($this) {
            self::Available => 'Available',
            self::Reserved => 'Reserved',
            self::Maintenance => 'Maintenance',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Available => 'green',
            self::Reserved => 'blue',
            self::Maintenance => 'yellow',
        };
    }
}
