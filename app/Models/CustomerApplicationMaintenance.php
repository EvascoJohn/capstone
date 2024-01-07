<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerApplicationMaintenance extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'monthly_amortizations',
    ];

    protected $casts = [
        'monthly_amortizations' => 'json',
    ];

}