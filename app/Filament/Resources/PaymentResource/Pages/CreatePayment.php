<?php

namespace App\Filament\Resources\PaymentResource\Pages;

use App\Enums\ApplicationStatus;
use App\Enums\ReleaseStatus;
use App\Filament\Resources\PaymentResource;
use App\Models\CustomerApplication;
use App\Models\Payment;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Notifications;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Carbon;

class CreatePayment extends CreateRecord
{
    protected static string $resource = PaymentResource::class;

    protected function afterCreate(): void
    {
        // runs after creation of record.
    }


    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $application = CustomerApplication::find($data['customer_application_id'])->get();
        if($application->hasMonthlyPayment() == false)//Initial Payment (Down payment)
        {
            $application->unit_amort_fin = Payment::calculateAmountMonthlyPayment(
                    $application->unit_srp,
                    $application->unit_ttl_dp,
                    $application->unit_term,
                    0.0, // monthly interest rate
            );
        }
        $application->save();
        return $data;
    }

    protected function beforeCreate(): void
    {

    }
}
