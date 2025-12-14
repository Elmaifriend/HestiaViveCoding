<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasColor;

enum AnnouncementType: string implements HasLabel, HasColor
{
    case News = 'news';
    case Alert = 'alert';
    case Maintenance = 'maintenance';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::News => 'News',
            self::Alert => 'Alert',
            self::Maintenance => 'Maintenance',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::News => 'info',
            self::Alert => 'danger',
            self::Maintenance => 'warning',
        };
    }
}
