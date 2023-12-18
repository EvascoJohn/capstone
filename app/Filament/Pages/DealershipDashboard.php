<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\UnitStocksOverview;
use Filament;
use Filament\Widgets;
use Filament\Pages\Page;
use Filament\Pages\Dashboard as BasePage;

use App\Filament\Widgets\CustomerDues;
use App\Filament\Widgets\AmortizationRevenueSummary;
use Filament\Resources\Pages\ManageRecords;

class DealershipDashboard extends BasePage
{
    protected static ?string $navigationIcon = 'heroicon-o-bars-4';

    protected static string $view = 'filament.pages.dealership-dashboard';

    protected function getHeaderActions(): array
    {
        return [
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            AmortizationRevenueSummary::class,
            UnitStocksOverview::class,
            CustomerDues::class,
        ];
    }

}