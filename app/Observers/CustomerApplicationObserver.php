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
        ->send(new CustomerApplicationMail("Application Has been created"));
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

        Mail::to($customerApplication->applicant_email)->send(new CustomerApplicationMail("Application has been been reviewed"));
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
