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
        Schema::create('partnership_packages', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('level')->unique(); // 1..6
            $table->string('code')->unique(); // QBP1..QBP6
            $table->string('currency', 10)->default('USDT');
            $table->decimal('amount', 14, 2);

            // Percentage of sales paid in sponsor-group differential chain for holders of this level.
            $table->decimal('group_percent', 6, 3); // 0.02..0.07

            // Global pool denominator for this level (per your spec: 665/365/215/115/55/20)
            $table->unsignedInteger('global_denom');

            // Maximum holders allowed for this level.
            $table->unsignedInteger('holder_limit');

            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partnership_packages');
    }
};
