<?php

namespace App\Services;

final class RankRules
{
    /**
     * Direct sponsor commission based on sponsor rank.
     * B = 5%, A/S/SS/SSS = 10%
     */
    public static function directSponsorPercent(string $rank): string
    {
        return match (strtoupper($rank)) {
            'B' => '0.05',
            'A', 'S', 'SS', 'SSS' => '0.10',
            default => '0.00',
        };
    }

    /** Ranking bonus percent by rank (max 22%). */
    public static function rankingBonusPercent(string $rank): string
    {
        return match (strtoupper($rank)) {
            'A' => '0.10',
            'S' => '0.13',
            'SS' => '0.17',
            'SSS' => '0.22',
            default => '0.00',
        };
    }

    /** Same-rank bonus percent by rank. */
    public static function sameRankBonusPercent(string $rank): string
    {
        return match (strtoupper($rank)) {
            'A' => '0.06',
            'S' => '0.08',
            'SS' => '0.10',
            'SSS' => '0.10',
            default => '0.00',
        };
    }

    /** Over-ranking bonus percent by rank (when downline rank is higher). */
    public static function overRankingBonusPercent(string $rank): string
    {
        return match (strtoupper($rank)) {
            'A' => '0.02',
            'S' => '0.03',
            'SS' => '0.05',
            'SSS' => '0.10',
            default => '0.00',
        };
    }

    /** Simple rank order for comparing ranks. */
    public static function rankOrder(string $rank): int
    {
        return match (strtoupper($rank)) {
            'B' => 1,
            'A' => 2,
            'S' => 3,
            'SS' => 4,
            'SSS' => 5,
            default => 0,
        };
    }
}
