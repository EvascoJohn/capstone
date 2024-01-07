<?php

namespace App\Filament\Resources\CustomerApplicationResource\Pages;

use App\Filament\Resources\CustomerApplicationResource;
use App\Models\CustomerApplication;
use App\Models\CustomerPaymentAccount;
use Filament\Actions;
use Filament\Notifications\Notification;
use App\Enums;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Filament\Support\Enums\MaxWidth;

class CreateCustomerApplication extends CreateRecord
{

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
            $this->getCancelCustomFormAction(),
        ];
    }

    protected function getCancelCustomFormAction(): Action
    {
        return Action::make('cancel')
            ->label(__('filament-panels::resources/pages/create-record.form.actions.cancel.label'))
            ->url($this->previousUrl ?? static::getResource()::getUrl())
            ->color('gray')
            ->requiresConfirmation();
    }

    protected static string $resource = CustomerApplicationResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['branch_id'] = auth()->user()->branch_id;
        $data['author_id'] = auth()->user()->id;
        $data['application_type'] = Enums\ApplicationType::WALK_IN->value;
        return $data;
    }

    protected function handleRecordCreation(array $data): Model
    {
        return static::getModel()::create($data);
    }

}