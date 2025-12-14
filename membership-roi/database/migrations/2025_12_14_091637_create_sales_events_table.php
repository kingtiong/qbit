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
        Schema::create('sales_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('currency', 10)->default('USDT');
            $table->decimal('amount', 14, 2);
            $table->string('type')->default('investment_purchase'); // investment_purchase, etc.
            $table->date('occurred_on'); // in UTC+8 business date
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['occurred_on', 'type']);
            $table->index(['user_id', 'occurred_on']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_events');
    }
};
