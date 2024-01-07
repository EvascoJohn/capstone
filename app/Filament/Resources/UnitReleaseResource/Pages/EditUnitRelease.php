<?php

namespace App\Filament\Resources\UnitReleaseResource\Pages;

use App\Enums\ApplicationStatus;
use App\Models;
use App\Filament\Resources\UnitReleaseResource\Pages;
use App\Enums\ReleaseStatus;
use App\Filament\Resources\UnitReleaseResource;
use App\Models\CustomerApplication;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditUnitRelease extends EditRecord
{
    protected static string $resource = UnitReleaseResource::class;

    protected function getRedirectUrl(): ?string
    {
        return Pages\ListUnitReleases::getUrl();
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $customer_application = new CustomerApplication;
        $due_date = $customer_application->calculateDueDate(Carbon::now());
        $data["due_date"] = $due_date;
        $data["release_status"] = ReleaseStatus::RELEASED->value;
        Models\Unit::query()->where("id", $data['units_id'])
            ->update([
                'customer_application_id' => $this->record->id,
                'released_status' => $data["release_status"]
            ]);

        Models\CustomerPaymentAccount::where("customer_application_id", $data['id'])
            ->update(['unit_release_id' => $data['units_id']]);

        $property = [
            "old" => [
                "release_status" => $data['release_status']
            ],
            "attributes" => [
                "release_status" => ReleaseStatus::RELEASED
            ]
        ];

        activity('Unit Release')
            ->event('released')
            ->performedOn($customer_application)
            ->withProperties($property)
            ->log('released');

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
