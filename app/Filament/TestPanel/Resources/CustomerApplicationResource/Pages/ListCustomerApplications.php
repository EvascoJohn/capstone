<?php

namespace App\Filament\TestPanel\Resources\CustomerApplicationResource\Pages;

use App\Filament\TestPanel\Resources\CustomerApplicationResource;
use Filament\Actions;
use App\Enums;
use Filament\Resources\Components\Tab;
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
            null => Tab::make('All'),
            'pending' => Tab::make()->query(fn ($query) => $query->where('application_status', Enums\ApplicationStatus::PENDING_STATUS->value)),
            'approved' => Tab::make()->query(fn ($query) => $query->where('application_status', Enums\ApplicationStatus::APPROVED_STATUS->value)),
            'active' => Tab::make()->query(fn ($query) => $query->where('application_status', Enums\ApplicationStatus::ACTIVE_STATUS->value)),
            'resubmission' => Tab::make()->query(fn ($query) => $query->where('application_status',  Enums\ApplicationStatus::RESUBMISSION_STATUS->value)),
            'closed' => Tab::make()->query(fn ($query) => $query->where('application_status',  Enums\ApplicationStatus::CLOSED_STATUS->value)),
            'rejected' => Tab::make()->query(fn ($query) => $query->where('application_status', Enums\ApplicationStatus::REJECTED_STATUS->value)),
        ];
    }
}