<?php

use App\Models\Branch;
use App\Enums\UnitStatus;
use App\Models\CustomerApplication;
use App\Models\UnitModel;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Branch::class)->nullable();
            $table->foreignIdFor(UnitModel::class);
            $table->string('chassis_number')->nullable()
                    ->unique();
            $table->string('frame_number')
                    ->unique();
            $table->string('engine_number')
                    ->unique();
            $table->foreignIdFor(CustomerApplication::class)
                    ->nullable();
            $table->enum('status', UnitStatus::values());
            $table->string('notes')
                    ->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        Schema::dropIfExists('units');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
};
