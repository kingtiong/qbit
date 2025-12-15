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
        $baseReturn = $targetPct / 100.0;

        // Create returns with some losses but average ~= baseReturn.
        $raw = [];
        for ($i = 0; $i < $n; $i++) {
            // Range roughly [-0.8%, +3.2%] around 1.5% target, clamped.
            $r = ($baseReturn) + (((mt_rand(0, 1000) / 1000) - 0.5) * 0.02);
            $r = max(-0.02, min(0.04, $r));
            $raw[] = $r;
        }

        // Adjust mean to exactly baseReturn (keep variation).
        $mean = array_sum($raw) / $n;
        $shift = $baseReturn - $mean;
        $returns = array_map(fn ($r) => max(-0.02, min(0.04, $r + $shift)), $raw);

        // Price anchors (very rough)
        $anchors = [
            'BTC' => 98000, 'ETH' => 3400, 'SOL' => 200, 'BNB' => 650, 'XRP' => 2.1,
            'ADA' => 0.95, 'DOGE' => 0.35, 'TRX' => 0.27, 'AVAX' => 45, 'LINK' => 22,
            'DOT' => 8.5, 'TON' => 6.2, 'MATIC' => 0.75, 'LTC' => 110, 'BCH' => 430,
            'ATOM' => 11.5, 'XLM' => 0.28, 'APT' => 12.0, 'SUI' => 3.5, 'NEAR' => 6.8,
        ];

        DB::transaction(function () use ($userId, $dateStr, $symbols, $returns, $usdPerTrade, $anchors): void {
            foreach ($symbols as $idx => $symbol) {
                $anchor = (float) ($anchors[$symbol] ?? 10);
                $jitter = 0.9 + (mt_rand(0, 2000) / 10000); // 0.9..1.1
                $buy = max(0.0001, $anchor * $jitter);
                $ret = (float) ($returns[$idx] ?? 0.0);
                $sell = $buy * (1.0 + $ret);
                $qty = $buy > 0 ? ($usdPerTrade / $buy) : 0;
                $pnl = $usdPerTrade * $ret;

                AutoTrade::create([
                    'user_id' => $userId,
                    'trade_date' => $dateStr,
                    'symbol' => $symbol,
                    'pair' => 'USDT',
                    'buy_price' => $buy,
                    'sell_price' => $sell,
                    'qty' => $qty,
                    'pnl' => $pnl,
                    'pnl_pct' => $ret * 100.0,
                    'opened_at' => now()->subMinutes(mt_rand(10, 120)),
                    'closed_at' => now()->subMinutes(mt_rand(0, 9)),
                ]);
            }
        });
    }
}

