<?php

namespace App\Http\Controllers;

use App\Models\AutoTrade;
use App\Models\Setting;
use App\Services\AutoTradeSimulator;
use App\Services\BusinessTime;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
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

        // Drip trades in over the day (1–10 minute random gaps). If the scheduler isn't running,
        // this will still "catch up" on page load.
        if ($fund > 0) {
            AutoTradeSimulator::ensureTradesUpToNow($user->id, $today, $fund, $targetPct, $symbols);
        }

        $trades = AutoTrade::query()
            ->where('user_id', $user->id)
            ->orderByDesc('trade_date')
            ->orderByDesc('closed_at')
            ->orderBy('symbol')
            ->limit(300)
            ->get();

        $todayTrades = $trades->filter(fn ($t) => $t->trade_date?->toDateString() === $today->toDateString());
        $todayPnl = (float) $todayTrades->sum('pnl');
        $targetPnl = $fund * ($targetPct / 100.0);

        // Analytics (based on last 300 trades)
        $wins = $trades->where('pnl', '>', 0)->count();
        $losses = $trades->where('pnl', '<', 0)->count();
        $total = $trades->count();
        $winRate = $total > 0 ? ($wins / $total) * 100.0 : 0.0;
        $longs = $trades->where('side', 'LONG')->count();
        $shorts = $trades->where('side', 'SHORT')->count();
        $best = $trades->sortByDesc('pnl')->first();
        $worst = $trades->sortBy('pnl')->first();

        $avgHoldMin = 0.0;
        if ($total > 0) {
            $sumMin = 0.0;
            $cnt = 0;
            foreach ($trades as $t) {
                if ($t->opened_at && $t->closed_at) {
                    $sumMin += max(0, $t->opened_at->diffInSeconds($t->closed_at)) / 60.0;
                    $cnt++;
                }
            }
            $avgHoldMin = $cnt > 0 ? ($sumMin / $cnt) : 0.0;
        }

        // Daily P&L series (last 30 days)
        $byDate = $trades
            ->groupBy(fn ($t) => $t->trade_date?->toDateString())
            ->map(fn ($g) => (float) $g->sum('pnl'));

        $labels = [];
        $series = [];
        for ($i = 29; $i >= 0; $i--) {
            $d = $today->copy()->subDays($i)->toDateString();
            $labels[] = $d;
            $series[] = (float) ($byDate[$d] ?? 0.0);
        }

        return view('autotrade.index', [
            'symbols' => $symbols,
            'fund' => $fund,
            'targetPct' => $targetPct,
            'targetPnl' => $targetPnl,
            'todayPnl' => $todayPnl,
            'trades' => $trades,
            'today' => $today,
            'tz' => BusinessTime::TZ,
            'analytics' => [
                'total' => $total,
                'wins' => $wins,
                'losses' => $losses,
                'win_rate' => $winRate,
                'longs' => $longs,
                'shorts' => $shorts,
                'avg_hold_min' => $avgHoldMin,
                'best' => $best,
                'worst' => $worst,
                'daily_labels' => $labels,
                'daily_pnl' => $series,
            ],
        ]);
    }
}

