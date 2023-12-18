<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerPaymentAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_application_id',  // [big-int] reference to the customer application.
        'remaining_balance',        // [float] original amount - payments.
        'plan_type',                // [cash, installament].
        'monthly_interest',         // 5% (0.05).
        'monthly_payment',          // float.
        'down_payment',             // float.
        'term',                     // 12-36 months.
        'status',                   // [pending ,active, closed].
        'payment_status',           // [dp, cash, monthly].
        'original_amount',          // [float] price of the unit (references the customer application).
        'unit_release_id',          // reference to the unit release containing [unit_id, date_realeased].
    ];



    public static function getActiveAccounts(string $search): Builder
    {
        //returns a query builder for getting all the un-released applications.
        //Criteria:
        // If the application is Released.
        // If the applicaton is approved.
        return static::query()
                    ->whereNotNull('account_id')
                    ->where(function ($query) use ($search) {
                        $query->where('applicant_firstname', 'like', '%' . $search . '%')
                            ->orWhere('applicant_lastname', 'like', '%' . $search . '%')
                            ->orWhere('id', 'like', '%' . $search . '%');
                    });
    }

    public static function getClosedAccounts(string $search): Builder
    {
        //returns a query builder for getting all the un-released applications.
        //Criteria:
        // If the application is Released.
        // If the applicaton is approved.
        return static::query()
                    ->where(function ($query) use ($search) {
                        $query->where('applicant_firstname', 'like', '%' . $search . '%')
                            ->orWhere('applicant_lastname', 'like', '%' . $search . '%')
                            ->orWhere('id', 'like', '%' . $search . '%');
                    });
    }

    public static function getPendingAccounts(string $search): Builder
    {
        //returns a query builder for getting all the un-released applications.
        //Criteria:
        // If the application is Released.
        // If the applicaton is approved.
        return static::query()
                    ->where(function ($query) use ($search) {
                        $query->where('applicant_firstname', 'like', '%' . $search . '%')
                            ->orWhere('applicant_lastname', 'like', '%' . $search . '%')
                            ->orWhere('id', 'like', '%' . $search . '%');
                    });
    }

    public function customerApplication(): BelongsTo
    {
        return $this->belongsTo(CustomerApplication::class, 'customer_application_id');
    }

}
