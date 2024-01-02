<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\AmortizationRevenueSummary;
use App\Filament\Widgets\CustomerDues;
use App\Filament\Widgets\PaymentsTable;
use App\Filament\Widgets\UnitStocksOverview;
use App\Filament\Widgets\UnitStocksTable;
use Filament\Support\Enums\MaxWidth;
use Filament\Pages\Page;

class Reports extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-bar';

    protected static string $view = 'filament.pages.reports';

    protected function getHeaderWidgets(): array
    {
        return [
            CustomerDues::class,
            UnitStocksTable::class,
            PaymentsTable::class,
        ];
    }

}
