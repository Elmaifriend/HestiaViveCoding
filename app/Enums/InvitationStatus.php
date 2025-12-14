<?php

namespace App\Enums;

enum InvitationStatus: string
{
    case Active = 'active';
    case Used = 'used';
    case Expired = 'expired';

    public function label(): string
    {
        return match ($this) {
            self::Active => 'Active',
            self::Used => 'Used',
            self::Expired => 'Expired',
        };
    }
}
