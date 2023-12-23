<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum RealEstateType:string implements HasLabel
{
    case HOUSE = "House";
    case LOT = "Lot";

    public function getLabel(): ?string
    {
        return match ($this) {
            self::LOT => "Lot",
            self::HOUSE => "House",
        };
    }

	public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

}