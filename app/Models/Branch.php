<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Branch extends Model
{

    use HasFactory, LogsActivity;

    public $fillable = [
        'brgyCode',
        'regCode',
        'provCode',
        'citymunCode',
        'street_name',
        'full_address',
    ];

    public function refRegions():BelongsTo{
        return $this->belongsTo(RefRegion::class, 'regCode', 'regCode');
    }

    public function units():HasMany{
        return $this->hasMany(Unit::class, 'branch_id', 'id');
    }

    public function refProvinces():BelongsTo{
        return $this->belongsTo(RefProvince::class, 'provCode', 'provCode');
    }

    public function refMunicipalities():BelongsTo{
        return $this->belongsTo(RefMunicipality::class, 'citymunCode', 'citymunCode');
    }

    public function refBarangays():BelongsTo{
        return $this->belongsTo(RefBarangay::class, 'brgyCode', 'brgyCode');
    }

    public function users(): HasMany{
        return $this->hasMany(User::class);
    }

    public function customerApplications():HasMany{
        return $this->hasMany(CustomerApplication::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->useLogName('Branch')->logAll()->logOnlyDirty();
    }
}
