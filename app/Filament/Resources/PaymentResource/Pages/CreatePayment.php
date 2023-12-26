<?php

namespace App\Filament\Resources\PaymentResource\Pages;

use App\Enums\ApplicationStatus;
use App\Enums\ReleaseStatus;
use App\Filament\Resources\PaymentResource;
use App\Models\Customer;
use App\Models\CustomerApplication;
use App\Models\CustomerPaymentAccount;
use App\Models\Payment;
use App\Models\User;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Notifications;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Carbon;
use Livewire\Component;

class CreatePayment extends CreateRecord
{
    protected static string $resource = PaymentResource::class;

    protected function getCancelFormAction(): Action
    {
        return Action::make('cancel')
            ->requiresConfirmation()
            ->action(function(){
                redirect($this->previousUrl);
            })
            ->label(__('filament-panels::resources/pages/create-record.form.actions.cancel.label'))
            // ->url($this->previousUrl ?? static::getResource()::getUrl())
            ->color('info');
    }

    protected function afterCreate(): void
    {
        // runs after creation of record.
    }


    protected function mutateFormDataBeforeCreate(array $data): array
    {

        if(auth()->user()::class == Customer::class){
            $data['author_id'] = auth()->user()->id;
        }
        if(auth()->user()::class == User::class){
            //checks branch_id and online application
            $data['author_id'] = auth()->user()->id;
            $data['branch_id'] = auth()->user()->branch_id;
        }
        $account = CustomerPaymentAccount::find($data['customer_payment_account_id'])->first();
        $application = CustomerApplication::find($account->customer_application_id);
        $application->application_status = ApplicationStatus::ACTIVE_STATUS->value;
        if($account->payment_status == 'downpayment')//Initial Payment (Down payment)
        {
            // sets status to active
            $account->status = 'active';
            $account->payment_status = 'monthly';
            // calculates monthly payments
            $account->monthly_payment = Payment::calculateAmountMonthlyPayment(
                    $account->original_amount,
                    $account->down_payment,
                    $account->term,
                    $account->monthly_interest, // monthly interest rate
            );
            // sets the values for monthly
        }
        $account->remaining_balance -= $data["payment_amount"]; 
        $account->due_date = $account->calculateDueDate(Carbon::createFromFormat(config('app.date_format'), $data['due_date']));
        $account->save();
        $application->save();
        return $data;
    }
}
