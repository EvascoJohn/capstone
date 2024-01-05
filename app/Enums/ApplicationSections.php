<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum ApplicationSections:string implements HasLabel
{
    case APPLICANT = "Applicant Information Section";
    case EDUCATION = "Education Attainment Section";
    case REFERENCES = "Applicant References Section";
    case EMPLOYMENT = "Employment Section";
    case MONTHLY_INCOME = "Statement Of Monthly Income";

    public function getLabel(): ?string
    {
        return match ($this) {
            self::APPLICANT => "Applicant Information Section",
            self::EDUCATION => "Educational Attainment Section",
            self::REFERENCES => "Applicant References Section",
            self::EMPLOYMENT => "Employment Section",
            self::MONTHLY_INCOME => "Statement Of Monthly Income",
        };
    }

	public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

}