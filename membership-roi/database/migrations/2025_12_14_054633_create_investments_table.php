<?php

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
        Schema::create('investments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('investment_package_id')->constrained('investment_packages')->restrictOnDelete();

            $table->string('currency', 10)->default('USDT');
            $table->decimal('amount', 14, 2);

            $table->string('status')->default('active'); // active|closed
            $table->date('started_on');
            $table->date('last_accrued_on')->nullable();
            $table->decimal('total_earned', 14, 2)->default(0);
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['status', 'last_accrued_on']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('investments');
    }
};
