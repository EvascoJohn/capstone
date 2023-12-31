<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IncomingUnit extends Model
{
    use HasFactory;

    protected $fillable = [
        'incoming_quantity'
    ];

    public function unit():BelongsTo{
        return $this->belongsTo(Unit::class);
    }

}