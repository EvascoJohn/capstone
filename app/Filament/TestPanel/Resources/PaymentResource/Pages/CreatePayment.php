<?php

namespace App\Filament\TestPanel\Resources\PaymentResource\Pages;



use App\Filament\TestPanel\Resources\PaymentResource;
use App\Models\CustomerApplication;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Notifications;
use Filament\Resources\Pages\CreateRecord;
use Filament\Forms\Components;
use App\Filament\TestPanel\Pages\CreateCustomerPayment;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class CreatePayment extends CreateRecord
{
    // protected static string $view = 'create-customer-payment';

    protected function getCreatedNotification(): ?Notification
    {
        $title = "Payment Succesful!";

        if (blank($title)) {
            return null;
        }
        return Notification::make()
            ->success()
            ->title($title);
    }

    protected static string $resource = PaymentResource::class;

    function paymongo($customer_application){
        return redirect()->route('paymongo', $customer_application->id);

    }

    protected function beforeCreate(): void
    {
        //gets the customer application object.
        $customer_application = CustomerApplication::query()
                ->where('id', $this->data['customer_application_id'])
                ->first();
        $this_payment = $this->data; //a dictionary
        $prev_due = $customer_application->due_date;
        $new_due =  Carbon::parse(Carbon::createFromFormat('Y-m-d', $prev_due)
                    ->addDays(30))->toDateString(); //calculates the due to a month forward.

            //calculate the sum with the payment amount, make a notif when greater then or eq to unit srp
            if(($customer_application->calculateTotalPayments() + $this_payment["payment_amount"]) >= $customer_application->unit_srp
                && $customer_application->application_status == "active")
            {
                Notifications\Notification::make()
                        ->title("This account is complete!")
                        ->body("This Account is now closed.")
                        ->warning()
                        ->persistent()
                        ->send();
                $customer_application->application_status = 'closed';
                $customer_application->due_date = null;
                $customer_application->save();
            }

            if($customer_application->application_status == "active")
            {
                $this->paymongo($customer_application);
            }

    }

}
