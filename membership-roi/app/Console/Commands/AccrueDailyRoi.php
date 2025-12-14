<?php

namespace App\Console\Commands;

use App\Models\Investment;
use App\Models\RoiEarning;
use App\Models\RoiRate;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AccrueDailyRoi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'roi:accrue {--date= : Date (YYYY-MM-DD) to accrue for. Defaults to yesterday.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Accrue daily ROI for active investments using admin-set rates.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $endDate = $this->option('date')
            ? Carbon::parse((string) $this->option('date'))->startOfDay()
            : Carbon::yesterday();

        $this->info("Accruing ROI up to {$endDate->toDateString()}...");

        $investments = Investment::query()
            ->active()
            ->whereDate('started_on', '<=', $endDate)
            ->orderBy('id')
            ->get();

        $credited = 0;
        $skippedMissingRate = 0;

        foreach ($investments as $investment) {
            $nextAccrueOn = $investment->last_accrued_on
                ? $investment->last_accrued_on->copy()->addDay()
                : $investment->started_on->copy()->addDay();

            while ($nextAccrueOn->lessThanOrEqualTo($endDate)) {
                $rate = RoiRate::query()
                    ->whereDate('date', $nextAccrueOn)
                    ->first();

                if (!$rate) {
                    $skippedMissingRate++;
                    $this->warn("Missing ROI rate for {$nextAccrueOn->toDateString()} (investment #{$investment->id}).");
                    break;
                }

                $already = RoiEarning::query()
                    ->where('investment_id', $investment->id)
                    ->whereDate('date', $nextAccrueOn)
                    ->exists();

                if ($already) {
                    $investment->forceFill(['last_accrued_on' => $nextAccrueOn->toDateString()])->save();
                    $nextAccrueOn->addDay();
                    continue;
                }

                DB::transaction(function () use ($investment, $rate, $nextAccrueOn, &$credited): void {
                    $wallet = Wallet::firstOrCreate(['user_id' => $investment->user_id], ['balance' => 0]);

                    // Casted decimals can come out as strings; use bcmath for stable money math.
                    $earningAmount = bcmul((string) $investment->amount, (string) $rate->rate, 2);

                    $tx = WalletTransaction::create([
                        'wallet_id' => $wallet->id,
                        'type' => 'roi_credit',
                        'amount' => $earningAmount,
                        'meta' => [
                            'investment_id' => $investment->id,
                            'date' => $nextAccrueOn->toDateString(),
                            'rate' => (string) $rate->rate,
                        ],
                        'occurred_on' => $nextAccrueOn->toDateString(),
                    ]);

                    RoiEarning::create([
                        'investment_id' => $investment->id,
                        'user_id' => $investment->user_id,
                        'date' => $nextAccrueOn->toDateString(),
                        'rate' => $rate->rate,
                        'amount' => $earningAmount,
                        'wallet_transaction_id' => $tx->id,
                    ]);

                    $wallet->increment('balance', $earningAmount);
                    $investment->increment('total_earned', $earningAmount);
                    $investment->forceFill(['last_accrued_on' => $nextAccrueOn->toDateString()])->save();

                    $credited++;
                });

                $nextAccrueOn->addDay();
            }
        }

        $this->info("Done. Credited: {$credited}. Missing-rate skips: {$skippedMissingRate}.");
        return Command::SUCCESS;
    }
}
