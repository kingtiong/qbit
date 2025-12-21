<?php

namespace App\Services;

/**
 * Generates the 31-tier GBP sale plan:
 * - Total supply: 31,000 units
 * - Tier count: 31
 * - Tier 1 price: 300 USDT per unit
 * - Price +20% each tier
 * - Units -10% each tier
 *
 * All prices and unit counts are integers (no cents).
 */
final class GbpTierPlan
{
    /**
     * @return array<int, array{tier:int, unit_price:int, total_units:int}>
     */
    public static function generate(
        int $totalUnits = 31000,
        int $tiers = 31,
        int $startPrice = 300,
        float $priceMultiplier = 1.2,
        float $unitMultiplier = 0.9,
    ): array {
        if ($totalUnits <= 0 || $tiers <= 0) {
            return [];
        }

        // Compute the base (tier1) units as a geometric series:
        // base * (1 - r^n) / (1 - r) = totalUnits
        $r = $unitMultiplier;
        $den = 1.0 - pow($r, $tiers);
        $base = $den <= 0 ? ($totalUnits / $tiers) : ($totalUnits * (1.0 - $r) / $den);

        // Raw units per tier (float), then convert to ints with "largest remainder" so sum is exact.
        $raw = [];
        $floors = [];
        $remainders = [];
        $sumFloors = 0;

        for ($i = 1; $i <= $tiers; $i++) {
            $v = $base * pow($r, $i - 1);
            $raw[$i] = $v;
            $f = (int) floor($v);
            $floors[$i] = $f;
            $remainders[$i] = $v - $f;
            $sumFloors += $f;
        }

        $remaining = $totalUnits - $sumFloors;
        // Distribute remaining units to tiers with largest fractional parts (tie => earlier tier).
        arsort($remainders);
        foreach ($remainders as $tier => $_frac) {
            if ($remaining <= 0) {
                break;
            }
            $floors[(int) $tier] += 1;
            $remaining--;
        }

        // Prices: integer progression where each tier is ~+20% vs previous.
        $plan = [];
        $price = $startPrice;
        for ($i = 1; $i <= $tiers; $i++) {
            $units = (int) ($floors[$i] ?? 0);
            if ($units < 0) {
                $units = 0;
            }

            $plan[] = [
                'tier' => $i,
                'unit_price' => (int) $price,
                'total_units' => $units,
            ];

            $next = (int) round($price * $priceMultiplier);
            $price = max($price + 1, $next); // keep strictly increasing
        }

        // Safety: ensure exact total.
        $sum = 0;
        foreach ($plan as $row) {
            $sum += $row['total_units'];
        }
        if ($sum !== $totalUnits && count($plan) > 0) {
            // Patch any drift into tier 1 (should rarely happen).
            $plan[0]['total_units'] += ($totalUnits - $sum);
        }

        return $plan;
    }
}

