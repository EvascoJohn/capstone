<?php

namespace App\Models;

use App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Resubmissions extends Model
{
    use HasFactory;

    protected $fillable = [
        'sections_visible',
        'customer_application_id',
    ];

    public function customerApplication(): BelongsTo
    {
        return $this->belongsTo(Models\CustomerApplication::class);
    }

}
