<?php

namespace App\Console\Commands;

use App\Models\Setting;
use App\Models\User;
use App\Services\AutoTradeSimulator;
use App\Services\BusinessTime;
use Carbon\Carbon;
use Illuminate\Console\Command;

class AutoTradeTick extends Command
{
    /**
     * @var string
     */
    protected $signature = 'autotrade:tick {--date= : Business date (YYYY-MM-DD) in UTC+8. Defaults to today.} {--max-creates=5 : Max trades to create per user per run.}';

    /**
     * @var string
     */
    protected $description = 'Generate incremental simulated auto-trades at random 1–10 minute intervals (UTC+8).';

    public function handle(): int
    {
        $date = BusinessTime::dateFromOption($this->option('date'), defaultYesterday: false);
        $now = Carbon::now(BusinessTime::TZ)->startOfMinute();

        $fund = (float) Setting::getValue('autotrade.fund_usdt', '1000');
        $targetPct = (float) Setting::getValue('autotrade.daily_profit_pct', '1.5');
        $fund = max(0, $fund);
        $targetPct = max(0, min(10, $targetPct));

        $maxCreates = (int) $this->option('max-creates');
        $maxCreates = max(1, min(20, $maxCreates));

        if ($fund <= 0) {
            $this->info('AutoTrade tick skipped: fund_usdt <= 0');
            return Command::SUCCESS;
        }

        $symbols = $this->topSymbols();

        $users = 0;
        $created = 0;

        User::query()
            ->select('id')
            ->orderBy('id')
            ->chunkById(500, function ($chunk) use ($date, $now, $fund, $targetPct, $symbols, $maxCreates, &$users, &$created): void {
                foreach ($chunk as $u) {
                    $users++;
                    $created += AutoTradeSimulator::ensureTradesUpToNow(
                        (int) $u->id,
                        $date,
                        $fund,
                        $targetPct,
                        $symbols,
                        $now,
                        $maxCreates,
                    );
                }
            });

        $this->info("AutoTrade tick done. Users checked: {$users}. Trades created: {$created}.");
        return Command::SUCCESS;
    }

    /**
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
}

