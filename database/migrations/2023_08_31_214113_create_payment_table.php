<?php

use App\Enums;
use App\Models\CustomerApplication;
use App\Models\CustomerPaymentAccount;
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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->enum('payment_status', Enums\PaymentStatus::values())->nullable();
            $table->string('payment_type')->nullable();
            $table->enum('payment_is', Enums\PaymentStatus::values())->nullable();
            $table->double('payment_amount')->nullable();
            $table->integer('term_covered')->nullable();
            $table->double('amount_to_be_paid')->nullable();
            $table->double('rebate')->nullable();
            $table->bigInteger('author_id')->nullable();
            $table->bigInteger('branch_id')->nullable();
            $table->timestamps();
        });
    }

    
    protected $casts = [
        'payment_status'            =>  Enums\PaymentStatus::class,
        'customer_is'               =>  Enums\PaymentStatus::class,
    ];

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
