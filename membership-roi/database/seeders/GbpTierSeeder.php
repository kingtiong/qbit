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
            GbpTier::updateOrCreate(
                ['tier' => (int) $row['tier']],
                [
                    'unit_price' => (int) $row['unit_price'],
                    'total_units' => (int) $row['total_units'],
                    'sold_units' => 0,
                    'is_active' => true,
                ],
            );
        }
    }
}

