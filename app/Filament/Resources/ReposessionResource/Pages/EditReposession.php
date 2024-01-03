<?php

namespace App\Filament\Resources\ReposessionResource\Pages;

use App\Filament\Resources\ReposessionResource;
use App\Models\CustomerPaymentAccount;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditReposession extends EditRecord
{

    protected static ?string $navigationLabel = 'Repossession';
    protected static ?string $modelLabel = "Repossession";
    protected static string $resource = ReposessionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $assumed_by_id = $data['assumed_by_id'];
        $account_id = $data['id'];                                                          // gets the account that is being passed to a new application.
        $account = CustomerPaymentAccount::query()->where("id", $account_id)->first();
        $account->customer_application_id = $assumed_by_id;                                 // passing the account to another application.
        $account->update();
        return $data;
    }

}
