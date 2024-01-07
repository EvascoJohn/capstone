<?php

namespace App\Observers;

use App\Models\AuditLog;
use App\Models\Unit;

class UnitObserver
{
    /**
     * Handle the Unit "created" event.
     */
    public function created(Unit $unit): void
    {
        AuditLog::query()->create([
            "user_id" => auth()->id(),
            "operation" => "create",
            "model" => class_basename($unit),
            "new_details" => "",
            "old_details" => "",
            "record_id" => $unit->id,
        ]);
    }

    /**
     * Handle the Unit "updated" event.
     */

     public function updating(Unit $unit): void
     {
         $old = Unit::query()->find($unit->id)->first();
         $details = [
            "old" => $old->getAttributes(),
            "new" => $unit->getAttributes(),
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

         AuditLog::query()->create([
             "user_id" => auth()->id(),
             "operation" => "update",
             "model" => class_basename($unit),
             "new_details" => $changes,
             "old_details" => $changedValuesInOldArray,
             "record_id" => $unit->id,
         ]);
     }


    /**
     * Handle the Unit "deleted" event.
     */
    public function deleted(Unit $unit): void
    {
        //
        AuditLog::query()->create([
            "user_id" => auth()->id(),
            "operation" => "deleted",
            "model" => class_basename($unit),
            "details" => "Deleted Unit",
            "record_id" => $unit->id,
            "new_details" => "",
            "old_details" => "",
        ]);
    }

    /**
     * Handle the Unit "restored" event.
     */
    public function restored(Unit $unit): void
    {
        //
        AuditLog::query()->create([
            "user_id" => auth()->id(),
            "operation" => "restored",
            "model" => class_basename($unit)
        ]);
    }

    /**
     * Handle the Unit "force deleted" event.
     */
    public function forceDeleted(Unit $unit): void
    {
        //
        AuditLog::query()->create([
            "user_id" => auth()->id(),
            "operation" => "deleted",
            "model" => class_basename($unit)
        ]);
    }
}
