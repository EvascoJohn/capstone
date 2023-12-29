<?php

namespace App\Listeners;

use App\Events\PaymentMade;
use App\Models\PaymentAccountObserverModels\PaymentAccountHelper;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UpdateCustomerPaymentAccount
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(PaymentMade $event): void
    {
        $payment = $event->payment;
        $paymentAccountHelper = new PaymentAccountHelper($payment);
        $paymentAccountHelper->updateRemainingBalance();
        $paymentAccountHelper->updatePaymentStatus();
        $paymentAccountHelper->updateTermLeft();
        $paymentAccountHelper->getCustomerPaymentAccount()->save();
    }
}
