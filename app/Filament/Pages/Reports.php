<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\AmortizationRevenueSummary;
use App\Filament\Widgets\CustomerDues;
use App\Filament\Widgets\UnitStocksOverview;
use Filament\Support\Enums\MaxWidth;
use Filament\Pages\Page;

class Reports extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-bar';

    protected static string $view = 'filament.pages.reports';

    protected function getHeaderWidgets(): array
    {
        return [
            AmortizationRevenueSummary::class,
            UnitStocksOverview::class,
            CustomerDues::class,
        ];
    }

}
