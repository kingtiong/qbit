<?php

namespace Database\Seeders;

use App\Models\GbpTier;
use App\Services\GbpTierPlan;
use Illuminate\Database\Seeder;

class GbpTierSeeder extends Seeder
{
    public function run(): void
    {
        // QBP plan (renamed in UI): 25 tiers, tier 1 = 3340 units.
        $plan = GbpTierPlan::generate(31000, 25, 300, 1.2, 0.9, 3340);

        foreach ($plan as $row) {
            $tierNum = (int) $row['tier'];
            $unitPrice = (int) $row['unit_price'];
            $totalUnits = (int) $row['total_units'];

            /** @var GbpTier|null $existing */
            $existing = GbpTier::query()->where('tier', $tierNum)->first();

            if (!$existing) {
                GbpTier::create([
                    'tier' => $tierNum,
                    'unit_price' => $unitPrice,
                    'total_units' => $totalUnits,
                    'sold_units' => 0,
                    'is_active' => true,
                ]);
                continue;
            }

            // IMPORTANT: never reset sold_units in production.
            // If the plan changes, ensure total_units cannot drop below already sold.
            $sold = (int) $existing->sold_units;
            $existing->forceFill([
                'unit_price' => $unitPrice,
                'total_units' => max($totalUnits, $sold),
                // Explicitly preserve sold_units to avoid accidental resets during reseeds.
                'sold_units' => $sold,
                'is_active' => true,
            ])->save();
        }

        // Deactivate any tiers above 25 (legacy from previous 31-tier plan).
        GbpTier::query()
            ->where('tier', '>', 25)
            ->update(['is_active' => false]);
    }
}

