<?php

namespace App\Filament\TestPanel\Resources\CustomerApplicationResource\Pages;

use App\Enums\ApplicationStatus;
use App\Filament\TestPanel\Resources\CustomerApplicationResource;
use Filament\Actions;
use App\Enums;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateCustomerApplication extends CreateRecord
{
    protected static string $resource = CustomerApplicationResource::class;

    protected function getCreatedNotification(): ?Notification
    {
        $title = "Application has been created!";

        if (blank($title)) {
            return null;
        }
        return Notification::make()
            ->success()
            ->title($title);
    }

    protected function getFormActions(): array
    {
        return [
            $this->getCancelFormAction(),
        ];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['applicaton_status'] = ApplicationStatus::PENDING_STATUS->value;
        $data['application_type'] = Enums\ApplicationType::ONLINE->value;
        $data['author_id'] = auth()->user()->id;
        return $data;
    }
}
