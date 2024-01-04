<?php

namespace App\Observers;

use App\Mail\CustomerApplicationMail;
use App\Models\AuditLog;
use App\Models\CustomerApplication;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Mail;

class CustomerApplicationObserver
{
    /**
     * Handle the CustomerApplication "created" event.
     */
    public function created(CustomerApplication $customerApplication): void
    {
        // dd($customerApplication->customer);
        AuditLog::query()->create([
                "user_id" => auth()->id(),
                "operation" => "create",
                "model" => class_basename($customerApplication),
                "new_details" => "",
                "old_details" => "",
                "record_id" => $customerApplication->id,
        ]);

        Mail::to($customerApplication->applicant_email)
        ->send(new CustomerApplicationMail(
            body:   "    I hope this message finds you well. We're thrilled to share some fantastic news with you – your motorcycle application has been successfully reviewed and approved!

    This approval brings you one step closer to hitting the open road on the motorcycle of your dreams. We appreciate the trust you've placed in us and are committed to providing you with an exceptional experience.

    Congratulations once again on the approval of your motorcycle loan! We look forward to helping you embark on thrilling adventures in your new ride.

    Thanks,<br>", 
            customerName: $customerApplication->applicant_full_name,
            title:  "# Exciting News: Your Motorcycle Loan Application Has Been Approved!",
        ));
    }

    /**
     * Handle the CustomerApplication "updating" event.
     */
    public function updating(CustomerApplication $customerApplication): void
    {

        $old = CustomerApplication::query()->find($customerApplication->id)->first();
        $details = [
           "old" => $old->getAttributes(),
           "new" => $customerApplication->getAttributes(),
        ];

        $changes = [];
   
        foreach ($details["new"] as $key => $value) {
            if (!array_key_exists($key, $details["old"]) || $details["old"][$key] !== $value) {
                $changes[$key] = $value;
            }
        }
        
        $changedValuesInOldArray = [];
        
        foreach ($changes as $key => $value) {
            if (array_key_exists($key, $details["old"])) {
                $changedValuesInOldArray[$key] = $details["old"][$key];
            }
        }

        // checks if approved, rejected or resub.
        Mail::to($customerApplication->applicant_email)
        ->send(new CustomerApplicationMail(
            body: "I hope this message finds you well. We're thrilled to share some fantastic news with you – your motorcycle application has been successfully reviewed and approved!\n\nThis approval brings you one step closer to hitting the open road on the motorcycle of your dreams. We appreciate the trust you've placed in us and are committed to providing you with an exceptional experience.\n\nCongratulations once again on the approval of your motorcycle loan! We look forward to helping you embark on thrilling adventures in your new ride.\n\nThanks,", 
            customerName: $customerApplication->applicant_full_name,
            title:  "# Exciting News: Your Motorcycle Loan Application Has Been Approved!",
        ));
    }

    /**
     * Handle the CustomerApplication "deleted" event.
     */
    public function deleted(CustomerApplication $customerApplication): void
    {
        //
        AuditLog::query()->create([
            "user_id" => auth()->id(),
            "operation" => "deleted",
            "model" => class_basename($customerApplication),
            "new_details" => "",
            "old_details" => "",
            "record_id" => $customerApplication->id,
    ]);

    }

    /**
     * Handle the CustomerApplication "restored" event.
     */
    public function restored(CustomerApplication $customerApplication): void
    {
        //
        AuditLog::query()->create([
            "user_id" => auth()->id(),
            "operation" => "restored",
            "model" => class_basename($customerApplication),
            "new_details" => "",
            "old_details" => "",
            "record_id" => $customerApplication->id,
        ]);
    }

    /**
     * Handle the CustomerApplication "force deleted" event.
     */
    public function forceDeleted(CustomerApplication $customerApplication): void
    {
        //
        AuditLog::query()->create([
            "user_id" => auth()->id(),
            "operation" => "deleted",
            "model" => class_basename($customerApplication),
            "new_details" => "",
            "old_details" => "",
            "record_id" => $customerApplication->id,
        ]);
    }
}
