<?php

namespace App\Models\PaymentAccountObserverModels;

use App\Enums\ApplicationStatus;
use App\Models;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class PaymentAccountHelper
{
    private $payment, $customerApplication,  $customerPaymentAccount;

    public function __construct($payment){
        $this->payment = $payment;
        $this->customerPaymentAccount = $payment->customerPaymentAccount;
        $this->customerApplication = $this->customerPaymentAccount->customerApplication;
    }

    public function updateRemainingBalance()
    {
        $payments = Models\Payment::where('customer_payment_account_id', $this->customerPaymentAccount->id)->get();
        $totalAmountPaid = $payments->sum('amount_to_be_paid');
        $calc = ($this->customerPaymentAccount->original_amount - $totalAmountPaid);
        $this->customerPaymentAccount->remaining_balance = $calc;
    }

    public function updatePaymentAccountDueDate()
    {
        // Sets initial due date for the payment account by taking the current time.
        if($this->customerPaymentAccount->payment_status == 'down payment'){
            $current_date = Carbon::now();
            $newDueDate = Models\Payment::calculateDueDate($current_date);
            $this->customerPaymentAccount->due_date = $newDueDate;
        }
        // Takes the existing due date and recalculate the next due.
        else if($this->customerPaymentAccount->payment_status == 'monthly'){
            $current_date = $this->customerPaymentAccount->due_date;
            $newDueDate = Models\Payment::calculateDueDate($current_date);
            $this->customerPaymentAccount->due_date = $newDueDate;
        }
    }

    public function updatePaymentStatus()
    {
        $this->customerPaymentAccount->payment_status = 'monthly';
    }

    public function updateCustomerApplicationStatus()
    {
        if($this->customerPaymentAccount->payment_status == 'down payment')
        {
            $this->customerApplication->application_status = ApplicationStatus::ACTIVE_STATUS;
            $this->customerPaymentAccount->status = $this->customerApplication->application_status;
        }
        else if($this->customerPaymentAccount->payment_status == 'cash')
        {
            $this->customerApplication->application_status = ApplicationStatus::CLOSED_STATUS;
            $this->customerPaymentAccount->status = $this->customerApplication->application_status;
        }
    }

    public function updateTermLeft()
    {
        $this->customerPaymentAccount->term_left -= $this->payment->term_covered;
        if($this->customerPaymentAccount->term_left == 0){
            $this->customerApplication->application_status = ApplicationStatus::CLOSED_STATUS;
            $this->customerPaymentAccount->status = $this->customerApplication->application_status;
        }
    }

    public function getCustomerApplication(): Model
    {
        return $this->customerApplication;
    }

    public function getCustomerPaymentAccount(): Model
    {
        return $this->customerPaymentAccount;
    }
}
