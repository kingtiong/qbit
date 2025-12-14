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
        Schema::create('investment_packages', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->string('currency', 10)->default('USDT');
            $table->decimal('amount', 14, 2);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['currency', 'amount']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('investment_packages');
    }
};
