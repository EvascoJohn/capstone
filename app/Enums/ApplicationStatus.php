<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

/*

*/

enum ApplicationStatus:string implements HasLabel
{
    case PENDING_STATUS         = "Pending";
    case ACTIVE_STATUS          = "Active";
    case APPROVED_STATUS        = "Approved";
    case REJECTED_STATUS        = "Reject";
    case CLOSED_STATUS          = "Closed";
    case RESUBMISSION_STATUS    = "Resubmission";
    case REPO_STATUS    =  "Reposession";

    public function getLabel(): ?string
    {
        return match ($this) {

            self::PENDING_STATUS => 'Pending',
            self::ACTIVE_STATUS => 'Active',
            self::APPROVED_STATUS => 'Approved',
            self::REJECTED_STATUS => 'Rejected',
            self::RESUBMISSION_STATUS => 'Resubmission',
            self::REPO_STATUS => 'Reposession',
            self::CLOSED_STATUS => 'Closed'
        };
    }

	public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

}