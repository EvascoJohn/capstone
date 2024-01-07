<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum PaymentStatus:string implements HasLabel
{
    case CURRENT = "Current";
    case DELINQUENT = "Delinquent";
    case ADVANCED = "Advanced";
    case OVERDUE = "Overdue";
    case DOWN_PAYMENT = "Down Payment";
    case CASH = "Cash Payment";
    case MONTHLY = "Monthly";

    public function getLabel(): ?string
    {
        return match ($this) {
            self::CURRENT => "Current",
            self::DELINQUENT => "Delinquent",
            self::ADVANCED => "Advanced",
            self::OVERDUE => "Overdue",
            self::DOWN_PAYMENT => "Down Payment",
            self::CASH => "Cash Payment",
            self::MONTHLY => "Monthly",
        };
    }
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}