<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use App\Models;
use Spatie\MediaLibrary\InteractsWithMedia;
use Filament\Support\RawJs;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class UnitModel extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    protected $fillable = [
        'model_name',
        'image_file',
        'down_payment_amount',
        'price',
        'body_type',
        'engine_type',
        'displacement',
        'engine_oil',
        'starting_system',
        'transmission',
        'fuel_tank_capacity',
        'net_weight',
        'dimension',
        'colors',
        'description',
    ];

    protected $casts = [
        "colors" => "json",
    ];

    public function unit():HasMany
    {
        return $this->hasMany(Unit::class, 'unit_model_id', 'id');
    }

    public function customerApplication():HasMany
    {
        return $this->hasMany(Models\CustomerApplication::class);
    }

}
