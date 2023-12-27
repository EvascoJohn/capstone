<?php

namespace App\Filament\Resources\PaymentResource\Pages;

use App\Filament\Resources\PaymentResource;
use Filament\Actions;
use App\Enums;
use Filament\Resources\Pages\ListRecords;

class ListPayments extends ListRecords
{
    protected static string $resource = PaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'Instllaments' => ListRecords\Tab::make()->query(fn ($query) => $query->where('plan_type',  Enums\PlanStatus::INSTALLMENT->value)),
            'Cash' => ListRecords\Tab::make()->query(fn ($query) => $query->where('plan_type', Enums\PlanStatus::CASH->value)),
        ];
    }
}