<?php

namespace App\Http\Controllers;

use App\Models\AutoTrade;
use App\Models\Setting;
use App\Services\BusinessTime;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AutoTradeController extends Controller
{
    /**
     * Top 20 symbols (static list for simulation).
     *
     * Keep short tickers in alphabet as requested.
     *
     * @return string[]
     */
    private function topSymbols(): array
    {
        return [
            'BTC', 'ETH', 'SOL', 'BNB', 'XRP',
            'ADA', 'DOGE', 'TRX', 'AVAX', 'LINK',
            'DOT', 'TON', 'MATIC', 'LTC', 'BCH',
            'ATOM', 'XLM', 'APT', 'SUI', 'NEAR',
        ];
    }

    public function index(): View
    {
        $user = Auth::user();
        $today = BusinessTime::today();

        $fund = (float) Setting::getValue('autotrade.fund_usdt', '1000');
        $targetPct = (float) Setting::getValue('autotrade.daily_profit_pct', '1.5');
        $fund = max(0, $fund);
        $targetPct = max(0, min(10, $targetPct));

        $symbols = $this->topSymbols();

        // Ensure today's simulated trades exist for this user.
        $todayCount = AutoTrade::query()
            ->where('user_id', $user->id)
            ->whereDate('trade_date', $today)
            ->count();

        if ($todayCount === 0 && $fund > 0) {
            $this->generateDailyTrades($user->id, $today, $fund, $targetPct, $symbols);
        }

        $trades = AutoTrade::query()
            ->where('user_id', $user->id)
            ->orderByDesc('trade_date')
            ->orderBy('symbol')
            ->limit(300)
            ->get();

        $todayTrades = $trades->filter(fn ($t) => $t->trade_date?->toDateString() === $today->toDateString());
        $todayPnl = (float) $todayTrades->sum('pnl');
        $targetPnl = $fund * ($targetPct / 100.0);

        return view('autotrade.index', [
            'symbols' => $symbols,
            'fund' => $fund,
            'targetPct' => $targetPct,
            'targetPnl' => $targetPnl,
            'todayPnl' => $todayPnl,
            'trades' => $trades,
            'today' => $today,
        ]);
    }

    private function generateDailyTrades(int $userId, Carbon $date, float $fund, float $targetPct, array $symbols): void
    {
        $dateStr = $date->toDateString();
        $n = max(1, count($symbols));
        $usdPerTrade = $fund / $n;
        $baseReturn = max(-0.05, min(0.10, $targetPct / 100.0));

        // Create market moves with wins & losses. We'll later shift the mean toward the target,
        // but keep enough variance so trades are not all green even when target is higher.
        $rawMoves = [];
        for ($i = 0; $i < $n; $i++) {
            // Approx move range [-6%, +8%] (allows losses; avoids "all 100% profit" look).
            $m = (((mt_rand(0, 10000) / 10000) - 0.5) * 0.14); // -0.07..+0.07
            // Add a slight positive drift (small) so we can still hit positive targets.
            $m += 0.005;
            $m = max(-0.06, min(0.08, $m));
            $rawMoves[] = $m;
        }

        // Shift mean toward baseReturn, but keep within bounds.
        $mean = array_sum($rawMoves) / $n;
        $shift = $baseReturn - $mean;
        $moves = array_map(fn ($m) => max(-0.06, min(0.08, $m + $shift)), $rawMoves);

        // Price anchors (very rough)
        $anchors = [
            'BTC' => 98000, 'ETH' => 3400, 'SOL' => 200, 'BNB' => 650, 'XRP' => 2.1,
            'ADA' => 0.95, 'DOGE' => 0.35, 'TRX' => 0.27, 'AVAX' => 45, 'LINK' => 22,
            'DOT' => 8.5, 'TON' => 6.2, 'MATIC' => 0.75, 'LTC' => 110, 'BCH' => 430,
            'ATOM' => 11.5, 'XLM' => 0.28, 'APT' => 12.0, 'SUI' => 3.5, 'NEAR' => 6.8,
        ];

        $poolAnchorsUsd = [
            'BTC' => 5000000000, 'ETH' => 4500000000, 'SOL' => 1600000000, 'BNB' => 1200000000, 'XRP' => 900000000,
            'ADA' => 650000000, 'DOGE' => 600000000, 'TRX' => 550000000, 'AVAX' => 420000000, 'LINK' => 380000000,
            'DOT' => 300000000, 'TON' => 280000000, 'MATIC' => 260000000, 'LTC' => 240000000, 'BCH' => 200000000,
            'ATOM' => 180000000, 'XLM' => 160000000, 'APT' => 140000000, 'SUI' => 130000000, 'NEAR' => 120000000,
        ];

        DB::transaction(function () use ($userId, $date, $dateStr, $symbols, $moves, $usdPerTrade, $anchors, $poolAnchorsUsd): void {
            foreach ($symbols as $idx => $symbol) {
                $anchor = (float) ($anchors[$symbol] ?? 10);
                $jitter = 0.9 + (mt_rand(0, 2000) / 10000); // 0.9..1.1
                $entry = max(0.0001, $anchor * $jitter);

                // Side + market move
                $side = (mt_rand(1, 100) <= 55) ? 'LONG' : 'SHORT';
                $move = (float) ($moves[$idx] ?? 0.0); // underlying market move
                $ret = ($side === 'LONG') ? $move : -$move; // short profits when market drops
                $exit = $entry * (1.0 + $ret);
                $qty = $entry > 0 ? ($usdPerTrade / $entry) : 0;
                $pnl = $usdPerTrade * $ret;

                // Times: within the selected business day (UTC+8)
                $openAt = $date->copy()->setTime(mt_rand(0, 22), mt_rand(0, 59), mt_rand(0, 59));
                $closeAt = $openAt->copy()->addMinutes(mt_rand(5, 180));

                // Risk / liquidity metadata (simulated)
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
                $poolJitter = 0.75 + (mt_rand(0, 5000) / 10000); // 0.75..1.25
                $poolSizeUsd = $poolAnchor * $poolJitter;
                $feeTierBps = [5, 30, 100][mt_rand(0, 2)];

                AutoTrade::create([
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
            }
        });
    }
}

