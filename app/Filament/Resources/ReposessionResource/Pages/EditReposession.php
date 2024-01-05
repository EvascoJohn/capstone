<?php

namespace App\Filament\Resources\ReposessionResource\Pages;

use App\Enums\ApplicationStatus;
use App\Filament\Resources\ReposessionResource;
use App\Models\CustomerApplication;
use App\Models\CustomerPaymentAccount;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditReposession extends EditRecord
{

    protected static ?string $navigationLabel = 'Repossession';
    protected static ?string $modelLabel = "Repossession";
    protected static string $resource = ReposessionResource::class;

    // protected static ?string $model = CustomerPaymentAccount::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        CustomerApplication::where('id', $data['customer_application_id'])
            ->update(['application_status' => ApplicationStatus::CLOSED_STATUS]);

        CustomerPaymentAccount::where('id', $data['id'])
            ->update(['customer_application_id' => $data['assumed_by_id']]);

        $property = [
            "old" => [
                "customer_application_id" => $data['customer_application_id']
            ],
            "attributes" => [
                "customer_application_id" => $data['assumed_by_id']
            ]
        ];

        activity('Repossession')
            ->event('repossessed')
            ->performedOn(new CustomerPaymentAccount)
            ->subject($data['id'])
            ->withProperties($property)
            ->log('repossessed');

        return $data;
    }
}
