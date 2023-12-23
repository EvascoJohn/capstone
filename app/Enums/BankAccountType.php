<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum BankAccountType:string implements HasLabel
{
    case TIME_DEPOSIT       =   "Time Deposit";
    case SAVINGS            =   "Savings";

    public function getLabel(): ?string
    {
        return match ($this) {

            self::TIME_DEPOSIT   => 'Time Deposit',
            self::SAVINGS    => 'Savings',
        };
    }

	public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

}