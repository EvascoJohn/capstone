<?php

namespace App\Filament\Resources\CustomerApplicationResource\Pages;

use App\Enums\ApplicationStatus;
use App\Filament\Resources\CustomerApplicationResource;
use App\Traits\ExportToExcelTrait;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use pxlrbt\FilamentExcel\Actions\Pages\ExportAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class ListCustomerApplications extends ListRecords
{
    use ExportToExcelTrait;

    protected static string $resource = CustomerApplicationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            $this->export('Customer-Application'),
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            null => Tab::make('All'),
            'pending' => Tab::make()->query(fn ($query) => $query->where('application_status', ApplicationStatus::PENDING_STATUS->value)),
            'approved' => Tab::make()->query(fn ($query) => $query->where('application_status', ApplicationStatus::APPROVED_STATUS->value)),
            'active' => Tab::make()->query(fn ($query) => $query->where('application_status', ApplicationStatus::ACTIVE_STATUS->value)),
            'repo' => Tab::make()->query(fn ($query) => $query->where('application_status',  ApplicationStatus::REPO_STATUS->value)),
            'closed' => Tab::make()->query(fn ($query) => $query->where('application_status',  ApplicationStatus::CLOSED_STATUS->value)),
            'rejected' => Tab::make()->query(fn ($query) => $query->where('application_status', ApplicationStatus::REJECTED_STATUS->value)),
        ];
    }

}
