<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Only set defaults if missing; do not override admin-configured values.
        $now = now();

        $existsFund = DB::table('settings')->where('key', 'autotrade.fund_usdt')->exists();
        if (!$existsFund) {
            DB::table('settings')->insert([
                'key' => 'autotrade.fund_usdt',
                'value' => '570000.00',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $existsPct = DB::table('settings')->where('key', 'autotrade.daily_profit_pct')->exists();
        if (!$existsPct) {
            DB::table('settings')->insert([
                'key' => 'autotrade.daily_profit_pct',
                'value' => '1.50',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        // Non-destructive: leave settings as-is.
    }
};

