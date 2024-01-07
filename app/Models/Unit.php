<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Unit extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = 
    [
        'id',
        'branch_id',
        'unit_model_id',
        'customer_application_id',
        'engine_number',
        'frame_number',
        'chassis_number',
        'status',
        'notes',
    ];

    public static function getUnitsWithAvailableStock(): array
    {
        $unitModels = DB::table('unit_models')
            ->join('units', 'unit_models.id', '=', 'units.unit_model_id')
            ->select('unit_models.id', 'unit_models.model_name', DB::raw('SUM(units.id) as total_stock'))
            ->groupBy('unit_models.id', 'unit_models.model_name')
            ->havingRaw('SUM(units.id) > 0') // Ensure there is available stock
            ->get();
    
        $result = [];
    
        foreach ($unitModels as $unitModel) {
            // Only include units with non-zero total_stock
            if ($unitModel->total_stock > 0) {
                $result[$unitModel->id] = $unitModel->model_name;
            }
        }
    
        return $result;
    }
    

    public static function getAvailableStatusOnUnit(?int $unit_model_id): array
    {
        $unitStatuses = DB::table('unit_models')
            ->join('units', 'unit_models.id', '=', 'units.unit_model_id')
            ->select('units.status')
            ->where('unit_models.id', '=', $unit_model_id) // Filter by the given unit_model_id
            ->groupBy('units.status')
            ->havingRaw('SUM(units.id) > 0') // Ensure there is available stock
            ->distinct()
            ->get();
    
        $result = [];
    
        foreach ($unitStatuses as $unitStatus) {
            $status = $unitStatus->status;
            $result[$status] = $status;
        }
    
        return $result;
    }

    public static function getStockBasedOnUnitAndStatus(?int $unit_model_id, ?string $preferred_unit): int
    {
        if($unit_model_id != null && $preferred_unit != null){
            $unit_stock = Models\Unit::query()->where(
                'status', $preferred_unit
            )->where('unit_model_id', $unit_model_id)->get()->count();
            return $unit_stock;
        }
        return 0;
    }

    

    public function assignToCustomerApplication(int $customerApplicationId): void
    {
        $this->attributes['customer_application_id'] = $customerApplicationId;
        $this->save();
    }

    public function getStock():int {
        $count = Unit::where('model_name', 'ducati')->count();
        return $count;
    }

    public function unitModel():BelongsTo{
        return $this->belongsTo(UnitModel::class);
    }

    public function branch():BelongsTo{
        return $this->belongsTo(Branch::class);
    }

    public function customerApplication(): HasMany{
        return $this->hasMany(CustomerApplication::class);
    }

    public function incomingUnit(): HasMany{
        return $this->hasMany(IncomingUnit::class);
    }

    public function outgoingUnit(): HasMany{
        return $this->hasMany(OutgoingUnit::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->useLogName('Unit')->logAll()->logOnlyDirty();
    }

}
