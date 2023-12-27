<?php

use App\Models;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('customer_payment_accounts', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(Models\CustomerApplication::class)->nullable();
            $table->float('remaining_balance')->default(0.00);
            $table->string('plan_type')->nullable();

            $table->float('monthly_interest')->default(0.00);
            $table->float('monthly_payment')->default(0.00);
            $table->float('down_payment')->default(0.00);
            $table->integer('term')->default(0);
            $table->string('due_date')->nullable();

            $table->integer('term_left')->default(0);
            $table->string('status')->nullable();
            $table->string('payment_status')->nullable();

            $table->float('original_amount')->default(0.0);
            $table->bigInteger('unit_release_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_payment_accounts');
    }
};
