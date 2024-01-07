<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMaintenance extends Model
{
    use HasFactory;

    protected $fillable = [
        'online_rebate',
        'walk_in_rebate',
    ];

}
