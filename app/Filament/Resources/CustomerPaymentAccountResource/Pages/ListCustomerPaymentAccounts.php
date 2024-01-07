<?php

namespace App\Filament\Resources\CustomerPaymentAccountResource\Pages;

use App\Filament\Resources\CustomerPaymentAccountResource;
use App\Traits\ExportToExcelTrait;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use pxlrbt\FilamentExcel\Actions\Pages\ExportAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class ListCustomerPaymentAccounts extends ListRecords
{
    use ExportToExcelTrait;
    protected static string $resource = CustomerPaymentAccountResource::class;
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            $this->export('Customer-Payments'),
        ];
    }

    public function getTabs(): array
    {
        return [

            'active' => ListRecords\Tab::make()->query(fn ($query) 
                => $query->where('term_left', '>' , 0)),
            'closed' => ListRecords\Tab::make()->query(fn ($query) 
                => $query->where('term_left', '<=',0)),
        ];
    }

}
