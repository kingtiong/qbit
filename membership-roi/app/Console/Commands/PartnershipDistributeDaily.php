<?php

namespace App\Console\Commands;

use App\Models\PartnershipPackage;
use App\Models\PartnershipPosition;
use App\Models\SalesEvent;
use App\Models\User;
use App\Services\BusinessTime;
use App\Services\EarningAllocator;
use Illuminate\Console\Command;

class PartnershipDistributeDaily extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'partnership:distribute {--date= : Business date (YYYY-MM-DD) in UTC+8. Defaults to yesterday.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Distribute partnership node rewards: 7% group differential + 3% global pooled (UTC+8).';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $date = BusinessTime::dateFromOption($this->option('date'));
        $this->info('Distributing partnership rewards for '.$date->toDateString().' ('.BusinessTime::TZ.')...');

        $sales = SalesEvent::query()
            ->whereDate('occurred_on', $date)
            ->where('type', 'investment_purchase')
            ->orderBy('id')
            ->get();

        $totalSales = '0.00';
        foreach ($sales as $s) {
            $totalSales = bcadd($totalSales, (string) $s->amount, 2);
        }

        $this->info("Total sales: {$totalSales}");

        $globalPool = bcmul($totalSales, '0.03', 2);
        $this->info("Global pool (3%): {$globalPool}");

        $groupPaid = '0.00';
        $globalPaid = '0.00';

        $packages = PartnershipPackage::query()
            ->where('is_active', true)
            ->orderBy('level')
            ->get()
            ->keyBy('level');

        $activePositions = PartnershipPosition::query()
            ->where('status', 'active')
            ->with('package')
            ->get();

        // user_id => max level
        $userMaxLevel = [];
        foreach ($activePositions as $pos) {
            $lvl = (int) ($pos->package?->level ?? 0);
            if ($lvl <= 0) {
                continue;
            }
            $userMaxLevel[$pos->user_id] = max($userMaxLevel[$pos->user_id] ?? 0, $lvl);
        }

        // Group differential distribution per sale.
        foreach ($sales as $sale) {
            $buyer = User::query()->with('sponsor')->find($sale->user_id);
            if (!$buyer || !$buyer->sponsor_id) {
                continue;
            }

            $currentPercent = '0.00';
            $upline = $buyer->sponsor;
            $safety = 0;

            while ($upline && $safety < 50 && bccomp($currentPercent, '0.07', 3) < 0) {
                $lvl = (int) ($userMaxLevel[$upline->id] ?? 0);

                if ($lvl > 0) {
                    /** @var PartnershipPackage|null $pkg */
                    $pkg = $packages->get($lvl);
                    $uplinePercent = (string) ($pkg?->group_percent ?? '0.00');

                    // Forfeit if upline has no active ROI package.
                    $hasActiveInvestment = \App\Models\Investment::query()
                        ->where('user_id', $upline->id)
                        ->where('status', 'active')
                        ->exists();

                    if ($hasActiveInvestment && bccomp($uplinePercent, $currentPercent, 3) > 0) {
                        $diff = bcsub($uplinePercent, $currentPercent, 3);
                        $amt = bcmul((string) $sale->amount, $diff, 2);

                        $paid = EarningAllocator::creditToOldestInvestments(
                            $upline->id,
                            $amt,
                            'partnership_group',
                            $date,
                            [
                                'sale_event_id' => $sale->id,
                                'buyer_user_id' => $buyer->id,
                                'buyer_amount' => (string) $sale->amount,
                                'percent' => $diff,
                                'upline_level' => $lvl,
                                'date' => $date->toDateString(),
                            ],
                        );

                        $groupPaid = bcadd($groupPaid, $paid, 2);
                        $currentPercent = $uplinePercent;
                    }
                }

                $upline = $upline->sponsor;
                $safety++;
            }
        }

        // Global pool distribution (poolPerLevel = globalPool/6; pool i pays poolPerLevel/global_denom_i per qualified holder (level>=i)).
        $poolPerLevel = bcdiv($globalPool, '6', 2);

        $usersWithActiveInvestments = \App\Models\Investment::query()
            ->where('status', 'active')
            ->select('user_id')
            ->distinct()
            ->pluck('user_id')
            ->all();
        $activeInvestmentSet = array_fill_keys($usersWithActiveInvestments, true);

        for ($i = 1; $i <= 6; $i++) {
            /** @var PartnershipPackage|null $pkg */
            $pkg = $packages->get($i);
            if (!$pkg) {
                continue;
            }

            $perShareRaw = bcdiv($poolPerLevel, (string) $pkg->global_denom, 8);
            $perShare = bcadd($perShareRaw, '0', 2);

            if (bccomp($perShare, '0', 2) <= 0) {
                continue;
            }

            foreach ($userMaxLevel as $userId => $lvl) {
                if ($lvl < $i) {
                    continue;
                }
                if (!isset($activeInvestmentSet[$userId])) {
                    continue;
                }

                $paid = EarningAllocator::creditToOldestInvestments(
                    (int) $userId,
                    $perShare,
                    'partnership_global',
                    $date,
                    [
                        'pool_level' => $i,
                        'pool_amount' => $poolPerLevel,
                        'per_share' => $perShareRaw,
                        'global_denom' => $pkg->global_denom,
                        'date' => $date->toDateString(),
                    ],
                );

                $globalPaid = bcadd($globalPaid, $paid, 2);
            }
        }

        $this->info("Partnership group paid: {$groupPaid}");
        $this->info("Partnership global paid: {$globalPaid}");

        return Command::SUCCESS;
    }
}
