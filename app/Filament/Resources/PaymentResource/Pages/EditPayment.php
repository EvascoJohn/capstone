<?php

namespace App\Filament\Resources\PaymentResource\Pages;

use App\Filament\Resources\CustomerPaymentAccountResource;
use App\Filament\Resources\PaymentResource;
use App\Models\CustomerApplication;
use App\Models\Payment;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Carbon;

class EditPayment extends EditRecord
{

    protected static string $resource = PaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\DeleteAction::make()
            //     ->before(
            //         function(){
            //             $this_payment = $this->getRecord();
            //             $customer_application = $this_payment->customerApplication;
            //             $total_payments = $customer_application->calculateTotalPayments();

            //             //checks the sum with the new payment if greater than unit_srp 
            //             if(($total_payments + $this_payment->payment_amount) < $customer_application->unit_srp){
            //                 $customer_application->application_status = "active";
            //                 $customer_application->due_date = Carbon::createFromFormat('Y-m-d', $customer_application->due_date)->subDays(30)->toDateString();
            //             }
            //             elseif(($total_payments + $this_payment->payment_amount) >= $customer_application->unit_srp){
            //                 $customer_application->application_status = "closed";
            //             }
            //             $customer_application->save();
            //         }
            //     ),
        ];
    }
}
