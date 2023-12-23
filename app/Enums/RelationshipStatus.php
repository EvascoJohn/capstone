<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum RelationshipStatus:string implements HasLabel
{
    case HUSBAND        =   "Husband";
    case SPOUSE         =   "Spouse";
    case FATHER         =   "Father";
    case MOTHER         =   "Mother";
    case RELATIVE       =   "Relative";
    case BROTHER        =   "Brother";
    case SISTER         =   "Sister";

    public function getLabel(): ?string
    {
        return match ($this) {
            self::HUSBAND   => 'Husband',
            self::SPOUSE    => 'Spouse',
            self::FATHER    => 'Father',
            self::MOTHER    => 'Mother',
            self::RELATIVE  => 'Relative',
            self::BROTHER   => 'Brother',
            self::SISTER    => 'Sister'
        };
    }

	public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

}