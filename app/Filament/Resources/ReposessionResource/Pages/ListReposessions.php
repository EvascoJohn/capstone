<?php

namespace App\Filament\Resources\ReposessionResource\Pages;

use App\Enums\ApplicationStatus;
use App\Filament\Resources\ReposessionResource;
use App\Traits\ExportToExcelTrait;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use pxlrbt\FilamentExcel\Actions\Pages\ExportAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class ListReposessions extends ListRecords
{
    use ExportToExcelTrait;

    protected static string $resource = ReposessionResource::class;

    public function getHeaderActions(): array
    {
        return [
            $this->export('Repossessions'),

            // Taking this query.
            // "Accounts" => ListRecords\Tab::make()->query(fn ($query) => $query->where('payment_status', 'monthly')),
        ];
    }
}