<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasColor;

enum GateEntryStatus: string implements HasLabel, HasColor
{
    case Pending = 'pending';
    case Approved = 'approved';
    case Denied = 'denied';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::Approved => 'Approved',
            self::Denied => 'Denied',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::Pending => 'warning',
            self::Approved => 'success',
            self::Denied => 'danger',
        };
    }
}
