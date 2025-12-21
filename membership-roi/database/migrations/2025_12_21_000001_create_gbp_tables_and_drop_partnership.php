<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Remove legacy QBP/Partnership tables (old GBP logic).
        Schema::dropIfExists('partnership_positions');
        Schema::dropIfExists('partnership_packages');

        Schema::create('gbp_tiers', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('tier')->unique(); // 1..31
            $table->unsignedInteger('unit_price'); // USDT per unit (integer; no cents)
            $table->unsignedInteger('total_units');
            $table->unsignedInteger('sold_units')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['is_active', 'tier']);
        });

        Schema::create('gbp_purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('gbp_tier_id')->constrained('gbp_tiers')->restrictOnDelete();

            $table->unsignedInteger('units');
            $table->unsignedInteger('unit_price'); // snapshotted at purchase time
            $table->unsignedBigInteger('total_amount'); // units * unit_price (integer)

            $table->foreignId('wallet_transaction_id')->nullable()->constrained('wallet_transactions')->nullOnDelete();
            $table->timestamp('purchased_at');
            $table->timestamps();

            $table->index(['user_id', 'purchased_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gbp_purchases');
        Schema::dropIfExists('gbp_tiers');
    }
};

