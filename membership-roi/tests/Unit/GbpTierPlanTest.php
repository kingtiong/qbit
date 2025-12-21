<?php

namespace Tests\Unit;

use App\Services\GbpTierPlan;
use Tests\TestCase;

class GbpTierPlanTest extends TestCase
{
    public function test_generates_31_tiers_summing_to_31000_units_with_integer_prices(): void
    {
        $plan = GbpTierPlan::generate(31000, 31, 300, 1.2, 0.9);

        $this->assertCount(31, $plan);

        $sum = 0;
        $prevPrice = null;
        $prevUnits = null;

        foreach ($plan as $idx => $row) {
            $this->assertSame($idx + 1, $row['tier']);
            $this->assertIsInt($row['unit_price']);
            $this->assertIsInt($row['total_units']);
            $this->assertGreaterThan(0, $row['unit_price']);
            $this->assertGreaterThan(0, $row['total_units']);

            if ($prevPrice !== null) {
                $this->assertGreaterThan($prevPrice, $row['unit_price']);
            }
            if ($prevUnits !== null) {
                // Rounding may cause small ties, but should never increase tier-to-tier.
                $this->assertLessThanOrEqual($prevUnits, $row['total_units']);
            }

            $prevPrice = $row['unit_price'];
            $prevUnits = $row['total_units'];
            $sum += $row['total_units'];
        }

        $this->assertSame(31000, $sum);
    }
}

