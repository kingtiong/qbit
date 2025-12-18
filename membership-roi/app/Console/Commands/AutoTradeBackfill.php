<?php

namespace App\Console\Commands;

use App\Models\Setting;
use App\Models\User;
use App\Services\AutoTradeSimulator;
use App\Services\BusinessTime;
use Carbon\Carbon;
use Illuminate\Console\Command;

class AutoTradeBackfill extends Command
{
    protected $signature = 'autotrade:backfill
        {--days=90 : Number of days to backfill (max 180).}
        {--user_id= : Backfill only one user ID.}
        {--max-users=0 : Limit number of users processed (0 = all).}';

    protected $description = 'Backfill simulated auto-trade history for past days (UTC+8).';

    public function handle(): int
    {
        $days = (int) $this->option('days');
        $days = max(1, min(180, $days));

        $userId = $this->option('user_id');
        $maxUsers = (int) $this->option('max-users');
        $maxUsers = max(0, $maxUsers);

        $fund = (float) Setting::getValue('autotrade.fund_usdt', '570000');
        $targetPct = (float) Setting::getValue('autotrade.daily_profit_pct', '1.5');
        $fund = max(0, $fund);
        $targetPct = max(0, min(10, $targetPct));

        if ($fund <= 0) {
            $this->info('Backfill skipped: fund_usdt <= 0');
            return Command::SUCCESS;
        }

        $symbols = $this->topSymbols();
        $today = BusinessTime::today();
        $start = $today->copy()->subDays($days - 1);

        $usersProcessed = 0;
        $created = 0;

        $query = User::query()->select('id')->orderBy('id');
        if ($userId !== null && $userId !== '') {
            $query->where('id', (int) $userId);
        }

        $query->chunkById(200, function ($chunk) use ($start, $days, $today, $fund, $targetPct, $symbols, $maxUsers, &$usersProcessed, &$created): void {
            foreach ($chunk as $u) {
                if ($maxUsers > 0 && $usersProcessed >= $maxUsers) {
                    return;
                }
                $usersProcessed++;

                for ($i = 0; $i < $days; $i++) {
                    $day = $start->copy()->addDays($i);
                    $nowForPastDay = $day->copy()->addDays(2);
                    $created += AutoTradeSimulator::ensureTradesUpToNow((int) $u->id, $day, $fund, $targetPct, $symbols, $nowForPastDay, 50);
                }
            }
        });

        $this->info("Backfill done. Users: {$usersProcessed}. Trades created: {$created}.");
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

