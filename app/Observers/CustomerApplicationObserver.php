<?php

namespace App\Observers;

use App\Enums;
use App\Mail\CustomerApplicationApproved;
use App\Mail\CustomerApplicationReject;
use App\Mail\CustomerApplicationResubmission;
use App\Mail\CustomerApplicationSubmitted;
use App\Models\CustomerApplication;
use Illuminate\Support\Facades\Mail;

class CustomerApplicationObserver
{
    /**
     * Handle the CustomerApplication "created" event.
     */

    public function created(CustomerApplication $customerApplication): void
    {
        Mail::to($customerApplication->applicant_email)
                ->send(new CustomerApplicationSubmitted($customerApplication->applicant_firstname));
    }

    /**
     * Handle the CustomerApplication "updating" event.
     */
    public function updating(CustomerApplication $customerApplication): void
    {
        // checks if approved, rejected or resub.
        if($customerApplication->application_status->value == Enums\ApplicationStatus::APPROVED_STATUS->value){
            Mail::to($customerApplication->applicant_email)
                    ->send(new CustomerApplicationApproved(
                        $customerApplication->applicant_firstname,
                        $customerApplication->unitModel->model_name,
                        $customerApplication->unit_term,
                        $customerApplication->unit_monthly_amort_fin,
                        $customerApplication->unitModel->down_payment_amount,
                    ));
        }
        else if ($customerApplication->application_status->value == Enums\ApplicationStatus::REJECTED_STATUS->value)
        {
            Mail::to($customerApplication->applicant_email)
                    ->send(new CustomerApplicationReject($customerApplication->applicant_firstname));
        }
        else if($customerApplication->application_status->value == Enums\ApplicationStatus::RESUBMISSION_STATUS->value)
        {
            Mail::to($customerApplication->applicant_email)
                    ->send(new CustomerApplicationResubmission($customerApplication->applicant_firstname));
        }
    }

    /**
     * Handle the CustomerApplication "deleted" event.
     */

    /**
     * Handle the CustomerApplication "restored" event.
     */
    public function restored(CustomerApplication $customerApplication): void
    {

    }

    /**
     * Handle the CustomerApplication "force deleted" event.
     */
    public function forceDeleted(CustomerApplication $customerApplication): void
    {
        //
    }
}
