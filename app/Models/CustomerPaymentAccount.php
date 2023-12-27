<?php

namespace App\Models;


use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CustomerPaymentAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_application_id',  // [big-int] repference to the customer application.
        'remaining_balance',        // [float] original amount - payments.
        'due_date',                 // the next due of the payment
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

    public function calculateDueDate($releaseDate)
    {
        // Convert the input release date to a Carbon instance
        $releaseDate = Carbon::parse($releaseDate); 

        // Set the initial due date to 31 (maximum possible date)
        $dueDate = Carbon::createFromDate(null, null, 31);

        // Check the release date range and update the due date accordingly
        if ($releaseDate->day >= 1 && $releaseDate->day <= 9) {
            $dueDate->day(9);
        } elseif ($releaseDate->day > 9 && $releaseDate->day <= 16) {
            $dueDate->day(16);
        } elseif ($releaseDate->day > 16) {
            // If the release date is after the 16th, set due date to 30 (or 28)
            $dueDate->day($dueDate->daysInMonth);
        }

        $dueDate->addMonth();

        // Format the due date as 'd-m-Y'
        $dueDateFormatted = $dueDate->format(config('app.date_format'));
        return $dueDateFormatted;

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
