<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasColor;

enum ProductStatus: string implements HasLabel, HasColor
{
    case Active = 'active';
    case Sold = 'sold';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Active => 'Active',
            self::Sold => 'Sold',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::Active => 'success',
            self::Sold => 'gray',
        };
    }
}
