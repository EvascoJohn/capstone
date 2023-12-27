<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum PaymentStatus:string implements HasLabel
{
    case CURRENT = "Current";
    case DELINQUENT = "Delinquent";
    case ADVANCED = "Advanced";
    case OVERDUE = "Overdue";

    public function getLabel(): ?string
    {
        return match ($this) {
            self::CURRENT => "Current",
            self::DELINQUENT => "Delinquent",
            self::ADVANCED => "Advanced",
            self::OVERDUE => "Overdue",
        };
    }
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}