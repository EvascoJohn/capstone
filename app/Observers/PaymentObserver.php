<?php

namespace App\Observers;

use App\Events\PaymentMade;
use App\Models\Payment;

class PaymentObserver
{
    public function created(Payment $payment): void
    {
        event(new PaymentMade($payment));
    }
}
