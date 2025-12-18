<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('auto_trades', function (Blueprint $table) {
            $table->string('side', 8)->nullable()->after('pair'); // long / short
            $table->string('risk_level', 16)->nullable()->after('side'); // low / medium / high

            // Simulated market metadata (for richer UI)
            $table->decimal('liquidity_range_low', 18, 8)->nullable()->after('risk_level');
            $table->decimal('liquidity_range_high', 18, 8)->nullable()->after('liquidity_range_low');
            $table->decimal('pool_size_usd', 18, 2)->nullable()->after('liquidity_range_high');
            $table->unsignedSmallInteger('fee_tier_bps')->nullable()->after('pool_size_usd'); // e.g. 5/30/100

            $table->index(['user_id', 'trade_date', 'side']);
        });
    }

    public function down(): void
    {
        Schema::table('auto_trades', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'trade_date', 'side']);
            $table->dropColumn([
                'side',
                'risk_level',
                'liquidity_range_low',
                'liquidity_range_high',
                'pool_size_usd',
                'fee_tier_bps',
            ]);
        });
    }
};

