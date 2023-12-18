<?php

namespace App\Filament\Resources\CustomerPaymentAccountResource\Pages;

use App\Filament\Resources\CustomerPaymentAccountResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateCustomerPaymentAccount extends CreateRecord
{
    protected static string $resource = CustomerPaymentAccountResource::class;

    protected function beforeCreate(): void
    {
        dd($this);
    }
}