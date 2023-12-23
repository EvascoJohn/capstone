<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum PlanStatus:string implements HasLabel
{
    case INSTALLMENT    =   "Installment";
    case CASH           =   "Cash";

    public function getLabel(): ?string
    {
        return match ($this) {
            self::INSTALLMENT   => 'Installment',
            self::CASH          => 'Cash',
        };
    }

	public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

}