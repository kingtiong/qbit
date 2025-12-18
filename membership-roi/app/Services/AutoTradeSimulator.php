<?php

namespace App\Services;

use App\Models\AutoTrade;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

final class AutoTradeSimulator
{
    /**
     * Ensure simulated trades exist up to "now" for the given business day.
     *
     * Trades are created gradually at random 1–10 minute gaps (deterministic per user/day),
     * and only once a trade is "closed" (closed_at <= now) so trade history looks realistic.
     *
     * @param  string[]  $symbols
     */
    public static function ensureTradesUpToNow(
        int $userId,
        Carbon $businessDay,
        float $fund,
        float $targetPct,
        array $symbols,
        ?Carbon $now = null,
        int $maxCreates = 5,
    ): int {
        $n = max(1, count($symbols));
        if ($fund <= 0 || $n <= 0) {
            return 0;
        }

        $now = ($now ?? Carbon::now(BusinessTime::TZ))->copy()->timezone(BusinessTime::TZ);
        $day = $businessDay->copy()->timezone(BusinessTime::TZ)->startOfDay();
        $dateStr = $day->toDateString();

        // Which trades already exist today?
        $existingSymbols = AutoTrade::query()
            ->where('user_id', $userId)
            ->whereDate('trade_date', $dateStr)
            ->pluck('symbol')
            ->map(fn ($s) => strtoupper((string) $s))
            ->all();
        $existingSet = array_fill_keys($existingSymbols, true);

        $ordered = self::orderedSymbols($userId, $dateStr, $symbols);
        $schedule = self::scheduleForDay($userId, $day, $ordered);
        $moves = self::movesForDay($userId, $dateStr, $ordered, $targetPct);
        // Win-rate bias: 70%..80% profitable trades (deterministic per user/day).
        $winBias = 0.70 + (self::u01($userId, $dateStr, 'win_bias') * 0.10);

        $usdPerTrade = $fund / $n;
        $anchors = self::priceAnchors();
        $poolAnchorsUsd = self::poolAnchorsUsd();

        $created = 0;

        DB::transaction(function () use (
            $userId,
            $dateStr,
            $ordered,
            $existingSet,
            $schedule,
            $moves,
            $usdPerTrade,
            $anchors,
            $poolAnchorsUsd,
            $now,
            $maxCreates,
            $winBias,
            &$created,
        ): void {
            foreach ($ordered as $idx => $symbol) {
                if ($created >= $maxCreates) {
                    break;
                }

                $symbol = strtoupper((string) $symbol);
                if (isset($existingSet[$symbol])) {
                    continue;
                }

                [$openAt, $closeAt] = $schedule[$idx];
                if ($closeAt->greaterThan($now)) {
                    // Not due yet.
                    continue;
                }

                $entry = self::entryPrice($userId, $dateStr, $symbol, $idx, $anchors);
                $move = (float) ($moves[$idx] ?? 0.0);
                $side = self::sideForMove($userId, $dateStr, $symbol, $idx, $move, $winBias);
                $ret = ($side === 'LONG') ? $move : -$move;
                $exit = max(0.0001, $entry * (1.0 + $ret));

                $qty = $entry > 0 ? ($usdPerTrade / $entry) : 0.0;
                $pnl = $usdPerTrade * $ret;

                $riskLevel = match (true) {
                    abs($ret) >= 0.05 => 'HIGH',
                    abs($ret) >= 0.025 => 'MEDIUM',
                    default => 'LOW',
                };

                $width = match ($riskLevel) {
                    'HIGH' => 0.20,
                    'MEDIUM' => 0.12,
                    default => 0.07,
                };

                $liqLow = max(0.0001, $entry * (1.0 - $width));
                $liqHigh = max(0.0001, $entry * (1.0 + $width));

                $poolAnchor = (float) ($poolAnchorsUsd[$symbol] ?? 100000000);
                $poolJitter = 0.75 + (self::u01($userId, $dateStr, "pool:{$symbol}:{$idx}") * 0.50); // 0.75..1.25
                $poolSizeUsd = $poolAnchor * $poolJitter;
                $feeTierBps = [5, 30, 100][(int) (self::hashInt($userId, $dateStr, "fee:{$symbol}:{$idx}") % 3)];

                AutoTrade::query()->create([
                    'user_id' => $userId,
                    'trade_date' => $dateStr,
                    'symbol' => $symbol,
                    'pair' => 'USDT',
                    'side' => $side,
                    'risk_level' => $riskLevel,
                    'liquidity_range_low' => $liqLow,
                    'liquidity_range_high' => $liqHigh,
                    'pool_size_usd' => $poolSizeUsd,
                    'fee_tier_bps' => $feeTierBps,
                    'buy_price' => $entry,
                    'sell_price' => $exit,
                    'qty' => $qty,
                    'pnl' => $pnl,
                    'pnl_pct' => $ret * 100.0,
                    'opened_at' => $openAt,
                    'closed_at' => $closeAt,
                ]);

                $existingSet[$symbol] = true;
                $created++;
            }
        });

        return $created;
    }

    /**
     * @param  string[]  $symbols
     * @return string[]
     */
    private static function orderedSymbols(int $userId, string $dateStr, array $symbols): array
    {
        $symbols = array_values(array_map(fn ($s) => strtoupper((string) $s), $symbols));
        $seed = self::hashInt($userId, $dateStr, 'order');

        usort($symbols, function (string $a, string $b) use ($userId, $dateStr, $seed): int {
            $ha = self::hashInt($userId, $dateStr, "o:{$seed}:{$a}");
            $hb = self::hashInt($userId, $dateStr, "o:{$seed}:{$b}");
            return $ha <=> $hb;
        });

        return $symbols;
    }

    /**
     * @param  string[]  $orderedSymbols
     * @return array<int, array{0: Carbon, 1: Carbon}>
     */
    private static function scheduleForDay(int $userId, Carbon $day, array $orderedSymbols): array
    {
        $schedule = [];
        $t = $day->copy();

        foreach ($orderedSymbols as $idx => $symbol) {
            $gapMin = (self::hashInt($userId, $day->toDateString(), "gap:{$idx}") % 10) + 1; // 1..10
            $durMin = (self::hashInt($userId, $day->toDateString(), "dur:{$idx}") % 176) + 5; // 5..180

            $open = $t->copy()->addMinutes($gapMin);
            $close = $open->copy()->addMinutes($durMin);

            $schedule[$idx] = [$open, $close];
            $t = $open;
        }

        return $schedule;
    }

    /**
     * Build deterministic underlying market moves for the whole day, then shift the mean
     * toward the target daily return per trade (still allowing losses).
     *
     * @param  string[]  $orderedSymbols
     * @return float[]
     */
    private static function movesForDay(int $userId, string $dateStr, array $orderedSymbols, float $targetPct): array
    {
        $n = max(1, count($orderedSymbols));
        $baseReturn = max(-0.05, min(0.10, $targetPct / 100.0));

        $raw = [];
        for ($i = 0; $i < $n; $i++) {
            // [-0.06, +0.08] with a small drift; deterministic
            $u = self::u01($userId, $dateStr, "move:{$i}");
            $m = (($u - 0.5) * 0.14) + 0.005;
            $m = max(-0.06, min(0.08, $m));
            $raw[] = $m;
        }

        $mean = array_sum($raw) / $n;
        $shift = $baseReturn - $mean;

        return array_map(fn (float $m) => max(-0.06, min(0.08, $m + $shift)), $raw);
    }

    /**
     * @return array<string, float>
     */
    private static function priceAnchors(): array
    {
        return [
            'BTC' => 98000, 'ETH' => 3400, 'SOL' => 200, 'BNB' => 650, 'XRP' => 2.1,
            'ADA' => 0.95, 'DOGE' => 0.35, 'TRX' => 0.27, 'AVAX' => 45, 'LINK' => 22,
            'DOT' => 8.5, 'TON' => 6.2, 'MATIC' => 0.75, 'LTC' => 110, 'BCH' => 430,
            'ATOM' => 11.5, 'XLM' => 0.28, 'APT' => 12.0, 'SUI' => 3.5, 'NEAR' => 6.8,
        ];
    }

    /**
     * @return array<string, float>
     */
    private static function poolAnchorsUsd(): array
    {
        return [
            'BTC' => 5000000000, 'ETH' => 4500000000, 'SOL' => 1600000000, 'BNB' => 1200000000, 'XRP' => 900000000,
            'ADA' => 650000000, 'DOGE' => 600000000, 'TRX' => 550000000, 'AVAX' => 420000000, 'LINK' => 380000000,
            'DOT' => 300000000, 'TON' => 280000000, 'MATIC' => 260000000, 'LTC' => 240000000, 'BCH' => 200000000,
            'ATOM' => 180000000, 'XLM' => 160000000, 'APT' => 140000000, 'SUI' => 130000000, 'NEAR' => 120000000,
        ];
    }

    private static function entryPrice(int $userId, string $dateStr, string $symbol, int $idx, array $anchors): float
    {
        $anchor = (float) ($anchors[$symbol] ?? 10);
        // Jitter 0.9..1.1 deterministically
        $j = 0.9 + (self::u01($userId, $dateStr, "j:{$symbol}:{$idx}") * 0.2);
        return max(0.0001, $anchor * $j);
    }

    private static function sideForMove(int $userId, string $dateStr, string $symbol, int $idx, float $move, float $winBias): string
    {
        // Determine "correct" side (aligned with move direction) to bias win rate.
        // If move > 0: LONG is profitable. If move < 0: SHORT is profitable.
        $alignedSide = ($move >= 0) ? 'LONG' : 'SHORT';
        $oppositeSide = ($alignedSide === 'LONG') ? 'SHORT' : 'LONG';

        $u = self::u01($userId, $dateStr, "side:{$symbol}:{$idx}");
        return ($u < $winBias) ? $alignedSide : $oppositeSide;
    }

    private static function hashInt(int $userId, string $dateStr, string $tag): int
    {
        // crc32 returns unsigned in PHP but can be signed int; normalize to 0..2^32-1
        $v = crc32($userId.':'.$dateStr.':'.$tag);
        return (int) sprintf('%u', $v);
    }

    private static function u01(int $userId, string $dateStr, string $tag): float
    {
        $h = self::hashInt($userId, $dateStr, $tag);
        return ($h % 1000000) / 1000000.0;
    }
}

