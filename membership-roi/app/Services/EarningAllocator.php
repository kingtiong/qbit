<?php

namespace App\Services;

use App\Models\Investment;
use App\Models\InvestmentEarning;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

final class EarningAllocator
{
    /**
     * Allocate an earning to a user's oldest active investments (packages) first.
     *
     * Returns the credited amount (may be less than requested due to caps).
     */
    public static function creditToOldestInvestments(
        int $userId,
        string $amount,
        string $source,
        Carbon $date,
        array $meta = [],
        ?string $txType = null,
    ): string {
        if (bccomp($amount, '0', 2) <= 0) {
            return '0.00';
        }

        return DB::transaction(function () use ($userId, $amount, $source, $date, $meta, $txType): string {
            /** @var \Illuminate\Database\Eloquent\Collection<int, Investment> $investments */
            $investments = Investment::query()
                ->where('user_id', $userId)
                ->where('status', 'active')
                // FIFO: deduct from the oldest purchased package first.
                // We intentionally prefer created_at/id over started_on because started_on is a business date
                // and can be backfilled/edited; payout ordering should follow purchase chronology.
                ->orderBy('created_at')
                ->orderBy('id')
                ->lockForUpdate()
                ->get();

            // Find eligible investments (remaining > 0)
            $eligible = [];
            foreach ($investments as $inv) {
                $max = $inv->max_return_amount ?? '0.00';
                $earned = $inv->total_earned ?? '0.00';
                $remaining = bcsub((string) $max, (string) $earned, 2);
                if (bccomp($remaining, '0', 2) > 0) {
                    $eligible[] = [$inv, $remaining];
                } else {
                    // Auto-cap stale rows
                    if (!$inv->capped_on) {
                        $inv->forceFill([
                            'status' => 'closed',
                            'capped_on' => $date->toDateString(),
                            'capped_reason' => 'max_return_reached',
                        ])->save();
                    }
                }
            }

            if (count($eligible) === 0) {
                // For sponsor/network style rewards, caller treats this as forfeited.
                return '0.00';
            }

            $remainingToAllocate = $amount;
            $allocatedTotal = '0.00';
            $allocations = [];

            foreach ($eligible as [$inv, $invRemaining]) {
                if (bccomp($remainingToAllocate, '0', 2) <= 0) {
                    break;
                }

                $alloc = (bccomp($remainingToAllocate, $invRemaining, 2) === 1) ? $invRemaining : $remainingToAllocate;
                $remainingToAllocate = bcsub($remainingToAllocate, $alloc, 2);
                $allocatedTotal = bcadd($allocatedTotal, $alloc, 2);
                $allocations[] = [$inv, $alloc];
            }

            if (bccomp($allocatedTotal, '0', 2) <= 0) {
                return '0.00';
            }

            // All earnings/commissions credit into the Quant Wallet (commission wallet type).
            $wallet = Wallet::forUser($userId, Wallet::TYPE_COMMISSION);
            $tx = WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'type' => $txType ?? $source,
                'amount' => $allocatedTotal,
                'meta' => $meta,
                'occurred_on' => $date->toDateString(),
            ]);

            foreach ($allocations as [$inv, $alloc]) {
                InvestmentEarning::create([
                    'investment_id' => $inv->id,
                    'user_id' => $userId,
                    'date' => $date->toDateString(),
                    'source' => $source,
                    'amount' => $alloc,
                    'wallet_transaction_id' => $tx->id,
                    'meta' => $meta,
                ]);

                $inv->increment('total_earned', $alloc);

                $newEarned = bcadd((string) $inv->total_earned, '0.00', 2);
                $max = (string) ($inv->max_return_amount ?? '0.00');
                if (bccomp($newEarned, $max, 2) >= 0) {
                    $inv->forceFill([
                        'status' => 'closed',
                        'capped_on' => $date->toDateString(),
                        'capped_reason' => 'max_return_reached',
                    ])->save();
                }
            }

            $wallet->increment('balance', $allocatedTotal);

            return $allocatedTotal;
        });
    }

    /**
     * Credit daily QOS to a single investment (caps on that investment only).
     */
    public static function creditDailyQosToInvestment(
        Investment $investment,
        string $amount,
        Carbon $date,
        array $meta = [],
    ): string {
        if (bccomp($amount, '0', 2) <= 0) {
            return '0.00';
        }

        return DB::transaction(function () use ($investment, $amount, $date, $meta): string {
            /** @var Investment $inv */
            $inv = Investment::query()->whereKey($investment->id)->lockForUpdate()->firstOrFail();

            $max = (string) ($inv->max_return_amount ?? '0.00');
            $earned = (string) ($inv->total_earned ?? '0.00');
            $remaining = bcsub($max, $earned, 2);
            if (bccomp($remaining, '0', 2) <= 0) {
                if (!$inv->capped_on) {
                    $inv->forceFill([
                        'status' => 'closed',
                        'capped_on' => $date->toDateString(),
                        'capped_reason' => 'max_return_reached',
                    ])->save();
                }
                return '0.00';
            }

            $alloc = (bccomp($amount, $remaining, 2) === 1) ? $remaining : $amount;

            $exists = InvestmentEarning::query()
                ->where('investment_id', $inv->id)
                ->whereDate('date', $date)
                ->where('source', 'qos_daily')
                ->exists();

            if ($exists) {
                $inv->forceFill(['last_accrued_on' => $date->toDateString()])->save();
                return '0.00';
            }

            // Daily QOS is treated as an earning (commission wallet).
            $wallet = Wallet::forUser($inv->user_id, Wallet::TYPE_COMMISSION);
            $tx = WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'type' => 'qos_daily',
                'amount' => $alloc,
                'meta' => array_merge($meta, ['investment_id' => $inv->id]),
                'occurred_on' => $date->toDateString(),
            ]);

            InvestmentEarning::create([
                'investment_id' => $inv->id,
                'user_id' => $inv->user_id,
                'date' => $date->toDateString(),
                'source' => 'qos_daily',
                'amount' => $alloc,
                'wallet_transaction_id' => $tx->id,
                'meta' => $meta,
            ]);

            $wallet->increment('balance', $alloc);
            $inv->increment('total_earned', $alloc);
            $inv->forceFill(['last_accrued_on' => $date->toDateString()])->save();

            $newEarned = bcadd((string) $inv->total_earned, '0.00', 2);
            if (bccomp($newEarned, $max, 2) >= 0) {
                $inv->forceFill([
                    'status' => 'closed',
                    'capped_on' => $date->toDateString(),
                    'capped_reason' => 'max_return_reached',
                ])->save();
            }

            return $alloc;
        });
    }
}
