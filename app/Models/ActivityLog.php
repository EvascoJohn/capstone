<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        "id", 
        "log_name", 
        "description", 
        "subject_type", 
        "event", 
        "subject_id",
        "causer_type",
        "causer_id",
        "properties_id",
        "batch_uuid",
    ];

    protected $casts = [
        "properties" => "json",
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'causer_id', 'id');
    }
}
