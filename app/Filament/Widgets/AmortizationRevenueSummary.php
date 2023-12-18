<?php

namespace App\Filament\Widgets;

use App\Models\Payment;
use Filament\Widgets\ChartWidget;

class AmortizationRevenueSummary extends ChartWidget
{
    protected static ?string $heading = 'Chart';
    public ?string $filter = 'today';

    protected function getData(): array
    {

        $activeFilter = $this->filter;

        return [
            'datasets' => [
                [
                    'label' => 'Monthly Revenue',
                    'data' => Payment::calculateMonthlyPayments(),
                ],
            ],
            'labels' => [
                            'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
                            'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'
                        ]
        ];
    }


    protected function getFilters(): ?array
    {
        return [
            'month' => 'Month',
        ];
    }
    
    protected function getType(): string
    {
        return 'bar';
    }
}
