<?php

namespace App\Console\Commands;

use App\Models\Investment;
use App\Models\InvestmentPackage;
use App\Models\User;
use App\Services\BusinessTime;
use App\Services\EarningAllocator;
use App\Services\RankRules;
use Illuminate\Console\Command;

class QosDistributeDaily extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'qos:distribute {--date= : Business date (YYYY-MM-DD) in UTC+8. Defaults to yesterday.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Distribute daily QOS and sponsor/rank rewards (UTC+8), respecting max-return caps.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $date = BusinessTime::dateFromOption($this->option('date'));

        $this->info("Distributing daily QOS for {$date->toDateString()} (".BusinessTime::TZ.")...");

        $investments = Investment::query()
            ->where('status', 'active')
            ->whereDate('started_on', '<=', $date)
            ->with(['package', 'user'])
            ->orderBy('id')
            ->get();

        $qosCredited = '0.00';
        $directCredited = '0.00';
        $rankCredited = '0.00';
        $sameRankCredited = '0.00';
        $overRankCredited = '0.00';

        foreach ($investments as $inv) {
            if (!$inv->package) {
                continue;
            }

            /** @var InvestmentPackage $pkg */
            $pkg = $inv->package;

            $daily = (string) ($pkg->daily_qos_amount ?? '0.00');
            if (bccomp($daily, '0', 2) <= 0) {
                continue;
            }

            $credited = EarningAllocator::creditDailyQosToInvestment(
                $inv,
                $daily,
                $date,
                [
                    'package_code' => $pkg->code,
                    'package_label' => $pkg->label,
                ],
            );

            if (bccomp($credited, '0', 2) <= 0) {
                continue;
            }

            $qosCredited = bcadd($qosCredited, $credited, 2);

            /** @var User|null $downline */
            $downline = $inv->user;
            if (!$downline || !$downline->sponsor_id) {
                continue;
            }

            // Build sponsor chain (direct sponsor upward)
            $chain = [];
            $current = $downline->sponsor;
            $safety = 0;
            while ($current && $safety < 50) {
                $chain[] = $current;
                $current = $current->sponsor;
                $safety++;
            }

            // 1) Direct sponsor commission (based on sponsor rank; paid on downline QOS)
            $directSponsor = $chain[0] ?? null;
            if ($directSponsor) {
                $pct = RankRules::directSponsorPercent((string) $directSponsor->rank);
                $amt = bcmul($credited, $pct, 2);
                $paid = EarningAllocator::creditToOldestInvestments(
                    $directSponsor->id,
                    $amt,
                    'direct_sponsor',
                    $date,
                    [
                        'downline_user_id' => $downline->id,
                        'downline_qos' => $credited,
                        'percent' => $pct,
                        'date' => $date->toDateString(),
                    ],
                );
                $directCredited = bcadd($directCredited, $paid, 2);
            }

            // 2) Ranking bonus differential up the chain (max 22%)
            $prevBonus = '0.00';
            $prevRank = (string) ($downline->rank ?? 'B');
            foreach ($chain as $idx => $upline) {
                $uplineRank = (string) ($upline->rank ?? 'B');
                $uplineBonus = RankRules::rankingBonusPercent($uplineRank);

                // Differential ranking bonus vs the max already paid below.
                $diff = bcsub($uplineBonus, $prevBonus, 5);
                if (bccomp($diff, '0', 5) > 0) {
                    $amt = bcmul($credited, $diff, 2);
                    $paid = EarningAllocator::creditToOldestInvestments(
                        $upline->id,
                        $amt,
                        'rank_bonus',
                        $date,
                        [
                            'downline_user_id' => $downline->id,
                            'downline_qos' => $credited,
                            'percent' => $diff,
                            'upline_rank' => $uplineRank,
                            'date' => $date->toDateString(),
                        ],
                    );
                    $rankCredited = bcadd($rankCredited, $paid, 2);
                    $prevBonus = $uplineBonus;
                }

                // Same-rank bonus (approx: paid on downline QOS when same rank)
                if (strtoupper($uplineRank) === strtoupper($prevRank)) {
                    $sr = RankRules::sameRankBonusPercent($uplineRank);
                    $amt = bcmul($credited, $sr, 2);
                    $paid = EarningAllocator::creditToOldestInvestments(
                        $upline->id,
                        $amt,
                        'same_rank_bonus',
                        $date,
                        [
                            'downline_user_id' => $downline->id,
                            'downline_qos' => $credited,
                            'percent' => $sr,
                            'upline_rank' => $uplineRank,
                            'date' => $date->toDateString(),
                        ],
                    );
                    $sameRankCredited = bcadd($sameRankCredited, $paid, 2);
                }

                // Over-ranking bonus (when downline rank is higher than upline rank)
                if (RankRules::rankOrder($prevRank) > RankRules::rankOrder($uplineRank)) {
                    $or = RankRules::overRankingBonusPercent($uplineRank);
                    $amt = bcmul($credited, $or, 2);
                    $paid = EarningAllocator::creditToOldestInvestments(
                        $upline->id,
                        $amt,
                        'over_rank_bonus',
                        $date,
                        [
                            'downline_user_id' => $downline->id,
                            'downline_qos' => $credited,
                            'percent' => $or,
                            'upline_rank' => $uplineRank,
                            'date' => $date->toDateString(),
                        ],
                    );
                    $overRankCredited = bcadd($overRankCredited, $paid, 2);
                }

                $prevRank = $uplineRank;

                if (bccomp($prevBonus, '0.22', 5) >= 0) {
                    break;
                }
            }
        }

        $this->info("QOS credited: {$qosCredited}");
        $this->info("Direct sponsor credited: {$directCredited}");
        $this->info("Rank bonus credited: {$rankCredited}");
        $this->info("Same-rank credited: {$sameRankCredited}");
        $this->info("Over-rank credited: {$overRankCredited}");

        return Command::SUCCESS;
    }
}
