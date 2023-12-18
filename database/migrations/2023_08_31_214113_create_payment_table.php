<?php

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
            $table->foreignIdFor(CustomerPaymentAccount::class);
            $table->string('payment_status')->nullable();
            $table->string('payment_type')->nullable();
            $table->decimal('payment_amount')->nullable();
            $table->bigInteger('author_id')->nullable();
            $table->bigInteger('branch_id')->nullable();
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
