<?php

namespace App\Models\PaymentAccountObserverModels;

use App\Models;
use Illuminate\Database\Eloquent\Model;

class PaymentAccountHelper
{
    private $payment;
    private $customerPaymentAccount;
    public function __construct($payment){
        $this->payment = $payment;
        $this->customerPaymentAccount = $payment->customerPaymentAccount;
    }

    public function updateRemainingBalance()
    {
        $payments = Models\Payment::where('customer_payment_account_id', $this->customerPaymentAccount->id)->get();
        $totalAmountPaid = $payments->sum('amount_to_be_paid');
        $calc = ($this->customerPaymentAccount->original_amount - $totalAmountPaid);
        $this->customerPaymentAccount->remaining_balance = $calc;
    }
    public function updatePaymentStatus()
    {
        $this->customerPaymentAccount->payment_status = 'monthly';
    }
    public function updateTermLeft()
    {
        $this->customerPaymentAccount->term_left -= $this->payment->term_covered;
    }
    public function getCustomerPaymentAccount(): Model
    {
        return $this->customerPaymentAccount;
    }
}
