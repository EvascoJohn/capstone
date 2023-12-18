<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class UnitStocksOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make("Current Unit Stocks", '123'),
        ];
    }
}
