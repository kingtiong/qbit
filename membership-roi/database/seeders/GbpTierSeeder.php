<?php

namespace Database\Seeders;

use App\Models\GbpTier;
use App\Services\GbpTierPlan;
use Illuminate\Database\Seeder;

class GbpTierSeeder extends Seeder
{
    public function run(): void
    {
        $plan = GbpTierPlan::generate(31000, 31, 300, 1.2, 0.9);

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
    }
}

