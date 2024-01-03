<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum UnitStatus:string implements HasLabel
{
    case REPOSSESSION = "Repossession";
    case BRAND_NEW = "Brand new";
    case DEPO = "Depo";

    public function getLabel(): ?string
    {
        return match ($this) {
            self::REPOSSESSION => "Repossession",
            self::BRAND_NEW => "Brand new",
            self::DEPO => "Depo",
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }


}