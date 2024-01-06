<?php

namespace App\Filament\TestPanel\Resources\CustomerApplicationResource\Pages;

use App\Filament\TestPanel\Resources\CustomerApplicationResource;
use Filament\Actions;
use App\Enums;
use Filament\Resources\Pages\ListRecords;

class ListCustomerApplications extends ListRecords
{
    protected static string $resource = CustomerApplicationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                    ->label("Send Application"),
        ];
    }
    public function getTabs(): array
    {
        return [
            'pending' => ListRecords\Tab::make()->query(fn ($query) => $query->where('application_status', Enums\ApplicationStatus::PENDING_STATUS->value)),
            'approved' => ListRecords\Tab::make()->query(fn ($query) => $query->where('application_status', Enums\ApplicationStatus::APPROVED_STATUS->value)),
            'active' => ListRecords\Tab::make()->query(fn ($query) => $query->where('application_status', Enums\ApplicationStatus::ACTIVE_STATUS->value)),
            'resubmission' => ListRecords\Tab::make()->query(fn ($query) => $query->where('application_status',  Enums\ApplicationStatus::RESUBMISSION_STATUS->value)),
            'closed' => ListRecords\Tab::make()->query(fn ($query) => $query->where('application_status',  Enums\ApplicationStatus::CLOSED_STATUS->value)),
            'rejected' => ListRecords\Tab::make()->query(fn ($query) => $query->where('application_status', Enums\ApplicationStatus::REJECTED_STATUS->value)),
        ];
    }
}