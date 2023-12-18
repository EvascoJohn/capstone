<?php

namespace App\Models;

use App\Enums\ApplicationStatus;
use App\Enums\ReleaseStatus;
use App\Models\Scopes\CustomerApplicationScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;


class CustomerApplication extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    protected $fillable = 
    [
        'id',
        'application_status',
        'reject_note',
        'resubmission_note',
        'release_status',

        "account_id",
        
        'preffered_unit_status',
        'plan',
        'assumed_by_id',

        //mutate data here
        'branch_id', 
        'author_id',
        'application_type',

        //Unit
        'unit_model_id',
        'unit_term',
        'unit_ttl_dp',
        'unit_srp',
        'unit_monthly_amort_fin',

        //Applicant Information
        'applicant_firstname',
        'applicant_middlename',
        'applicant_lastname',
        'applicant_birthday',

        'applicant_civil_status',
        'applicant_present_address',
        'applicant_previous_address',
        'applicant_provincial_address',
        'applicant_lived_there',
        'applicant_email',
        'applicant_house',
        'applicant_valid_id',
        'applicant_telephone',
        'applicant_full_name',
        'applicant_fullname_with_id',

        //Applicant Employment
        'applicant_present_business_employer',
        'applicant_position',
        'applicant_how_long_job_or_business',
        'applicant_business_address',
        'applicant_nature_of_business',
        'applicant_previous_employer',
        'applicant_previous_employer_position',
        'applicant_how_long_prev_job_or_business',

        'applicant_present_business_employer',
        'applicant_position',
        'applicant_how_long_job_or_business',
        'applicant_business_address',
        'applicant_nature_of_business',
        'applicant_previous_employer',
        'applicant_previous_employer_position',
        'applicant_how_long_prev_job_or_business',

        //Co owner Information
        'co_owner_firstname',
        'co_owner_middlename',
        'co_owner_lastname',
        'co_owner_email',
        'co_owner_birthday',
        'co_owner_mobile_number',
        'co_owner_address',
        'co_owner_valid_id',
        
        //Spouse Information
        'spouse_firstname',
        'spouse_middlename',
        'spouse_lastname',
        'spouse_birthday',
        'spouse_present_address',
        'spouse_provincial_address',
        'spouse_telephone',
        'spouse_valid_id',

        //Spouse Employer
        'spouse_employer',
        'spouse_position',
        'spouse_how_long_job_business',
        'spouse_business_address',
        'spouse_nature_of_business',

        //Educational Attainment
        'educational_attainment',

        //Dependents
        'dependents',

        //Bank
        'bank_references',
        'credit_references',

        //Financials
        'applicants_basic_monthly_salary',
        'applicants_allowance_commission',
        'applicants_deductions',
        'applicants_net_monthly_income',

        'spouses_basic_monthly_salary',
        'spouse_allowance_commision',
        'spouse_deductions',
        'spouse_net_monthly_income',

        //Personal References
        'personal_references',

        //Personal & Real Estate Properties
        'properties',

        //Applicant's Income
        'applicants_basic_monthly_salary',
        'applicants_allowance_commission',
        'applicants_deductions',
        'applicants_net_monthly_income',


        //Spouse's Income
        'spouses_basic_monthly_salary',
        'spouse_allowance_commision',
        'spouse_deductions',
        'spouse_net_monthly_income',

        //Other Income
        'other_income',

        //Gross Monthly Income
        'gross_monthly_income',

        //Total Expenses
        'living_expenses',
        'education',
        'transportation',
        'rental',
        'utilities',
        'monthly_amortization',
        'other_expenses',
        'total_expenses',

        //Net Income
        'net_monthly_income',
        'proof_of_income_image'


    ];

    protected $casts = [
        'application_status'        =>  ApplicationStatus::class,
        'properties'                => 'json',
        'applicant_valid_id'        => 'json',
        'spouse_valid_id'           => 'json',
        'co_owner_valid_id'         => 'json',
        'proof_of_income_image'     => 'json',
        'personal_references'       => 'json',
        'bank_references'           => 'json',
        'credit_references'         => 'json',
        'educational_attainment'    => 'json',
        'dependents'                => 'json',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new CustomerApplicationScope);
    }

    public function assignAccount(): void
    {
        //assigns an account to the customer application.
        $payment_status = null;
        if($this->plan == 'cash'){
            $payment_status = "cash-payment";
        }
        else if($this->plan == 'installment'){
            $payment_status = "downpayment";
        }
        else{
            $payment_status = 'uni-payment';
        }
        $new_account = CustomerPaymentAccount::create([
            $this->unit_srp,                            // remaining_balance
            $this->plan,                                // plan_type
            0.00,                                       // monthly_interest

            $this->unit_monthly_amort,                  // monthly_payment
            $this->term,                                // term
            $this->term,                                // term_left

            $this->application_status,                  // account status
            $payment_status,                            // payment_status
            $this->unit_srp,                            // remaining_balance                                            // original_amount

            null                                        // unit_release_id
        ]);
        $new_account->save();
        $this->acount_id = $new_account->id;
    }

    public static function getSearchApplicationsReadyForPayment(string $search): Builder
    {
        //returns a query builder for getting all the un-released applications.
        //Criteria:
        // If the application is Released.
        // If the applicaton is approved.
        return static::query()
                    ->where('application_status', ApplicationStatus::APPROVED_STATUS->value)
                    ->where(function ($query) use ($search) {
                        $query->where('applicant_firstname', 'like', '%' . $search . '%')
                            ->orWhere('applicant_lastname', 'like', '%' . $search . '%')
                            ->orWhere('id', 'like', '%' . $search . '%');
                    });
    }

    public static function searchApprovedApplicationsWithNoAccounts(string $search): Builder
    {
        //returns a query builder for getting all the un-released applications.
        //Criteria:
        // If the application is Released.
        // If the applicaton is approved.
        return static::query()
                    ->where('application_status', ApplicationStatus::APPROVED_STATUS->value)
                    ->where('account_id', null)
                    ->where(function ($query) use ($search) {
                        $query->where('applicant_firstname', 'like', '%' . $search . '%')
                            ->orWhere('applicant_lastname', 'like', '%' . $search . '%')
                            ->orWhere('id', 'like', '%' . $search . '%');
                    });
    }

    public static function getSearchApplicationsWithAccounts(string $search): Builder
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

    public static function getApplicationsReadyForRelease(): Builder
    {
        return static::query()
                    ->where('application_status', ApplicationStatus::ACTIVE_STATUS->value)
                    ->where('released_status', ReleaseStatus::UN_RELEASED->value);
    }

    public function hasMonthlyPayment(): bool
    {
        if($this->unit_amort_fin == null || $this->unit_amort_fin <= 0.00){
            return false;
        }
        return true;
    }

    public function releasesApplication(array $data = null): array
    {
        $this->due_date = $this->calculateDueDate(Carbon::now ());
        $data["application_status"] = ApplicationStatus::ACTIVE_STATUS->value;
        $data["release_status"] = ReleaseStatus::RELEASED->value;
        $this->release();
        dd($this->attributes);
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

        // Format the due date as 'd-m-Y'
        $dueDateFormatted = $dueDate->format(config('app.date_format'));
        return $dueDateFormatted;

    }

    public function setStatusTo(ApplicationStatus $status): void
    {
        $this->application_status = $status;
        $this->save();
    }

    public function getStatus(): ApplicationStatus|null
    {
        if($this->application_status != null)
        {

            return $this->application_status;
        }
        return null;
    }

    public function release()
    {
        //gets the associated unit and marks it as owned.
        $unit = Unit::query()->where('id', $this->units_id)->first();
        $unit->customer_application_id = $this->id;
        $unit->save();
    }
    
    public function branches():BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function customerApplication(): BelongsTo
    {
        return $this->belongsTo(CustomerApplication::class,'assumed_by_id');
    }

    public function calculateTotalPayment(): int
    {
        return $this->payments()->count();
    }

    public function calculateTotalAmountOfPayment(): float
    {
        return $this->payments()->sum();
    }

    public function payments():HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function unitModel():BelongsTo
    {
        return $this->belongsTo(UnitModel::class);
    }

    public function units():BelongsTo
    {
        return $this->belongsTo(Unit::class, 'units_id');
    }

    public function customerPaymentAccount(): HasOne
    {
        return $this->hasOne(CustomerPaymentAccount::class, 'id');
    }

}