<?php

namespace App\Models\PaymentAccountObserverModels;

use App\Models;
use Illuminate\Database\Eloquent\Model;

class PaymentAccountHelper
{
    private $payment;
    public function __construct($payment){
        $this->payment = $payment;
    }

    public function updateRemainingBalance()
    {
        $customerPaymentAccount = $this->payment->customerPaymentAccount;
        $payments = Models\Payment::where('customer_payment_account_id', $customerPaymentAccount->id)->get();
        $totalAmountPaid = $payments->sum('amount_to_be_paid');
        $customerPaymentAccount->remaining_balance -= $totalAmountPaid;
    }
    public function updatePaymentStatus()
    {
        $customerPaymentAccount = $this->payment->customerPaymentAccount;
        
    }
    public function updateTermLeft()
    {
        //..
    }
    public function getCustomerPaymentAccount(): Model
    {
        return $this->payment->customerPaymentAccount;
    }
}
