<?php

namespace App\Filament\TestPanel\Resources\CustomerApplicationResource\Pages;

use App\Enums;
use App\Filament\TestPanel\Resources\CustomerApplicationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCustomerApplication extends EditRecord
{
    protected static string $resource = CustomerApplicationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['application_status'] = Enums\ApplicationStatus::PENDING_STATUS->value;
            $date['resubmission_id'] = null;
        return $data;
    }

    protected function getRedirectUrl(): string
{
    return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
}

}
