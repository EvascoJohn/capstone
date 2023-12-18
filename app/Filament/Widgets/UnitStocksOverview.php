<?php

namespace App\Filament\Widgets;

use App\Enums\ApplicationStatus;
use App\Enums\UnitStatus;
use App\Filament\Resources\UnitResource\Pages\ListUnits;
use Filament\Widgets\Concerns\InteractsWithPageTable;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class UnitStocksOverview extends BaseWidget
{

    use InteractsWithPageTable;

    protected static ?string $pollingInterval = null;

    protected function getTablePage(): string
    {
        return ListUnits::class;
    }

    protected function getStats(): array
    {
        return [
            Stat::make('Total Unit', $this->getPageTableQuery()->count())
                    ->description('Total products in the inventory.'),
            Stat::make('Brand New Unit', $this->getPageTableRecords()->where('status', UnitStatus::BRAND_NEW->value)->count())
                    ->description('Total products that are brand new in the inventory.'),
            Stat::make('Reposession Unit', $this->getPageTableRecords()->where('status', UnitStatus::REPOSESSION->value)->count())
                    ->description('Total products that are in repo new in the inventory.'),
        ];
    }

}
