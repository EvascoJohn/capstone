<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

/*

*/

enum ApplicationType:string implements HasLabel
{
    case WALK_IN        =   "Walk-in";
    case ONLINE          =  "Online";

    public function getLabel(): ?string
    {
        return match ($this) 
        {
            self::WALK_IN => 'Pending',
            self::ONLINE => 'Active',
        };
    }

	public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

}