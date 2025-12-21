<?php

namespace Tests\Unit;

use App\Models\Investment;
use App\Models\InvestmentEarning;
use App\Models\InvestmentPackage;
use App\Models\User;
use App\Services\EarningAllocator;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EarningAllocatorTest extends TestCase
{
    use RefreshDatabase;

    public function test_credit_to_oldest_investments_uses_fifo_by_purchase_time(): void
    {
        $user = User::factory()->create();

        $pkg = InvestmentPackage::query()->create([
            'label' => 'Test Package',
            'currency' => 'USDT',
            'amount' => '100.00',
            'is_active' => true,
        ]);

        // Oldest purchased investment, but with a later started_on (e.g., edited/backfilled business date).
        $invOld = Investment::query()->create([
            'user_id' => $user->id,
            'investment_package_id' => $pkg->id,
            'currency' => 'USDT',
            'amount' => '100.00',
            'status' => 'active',
            'started_on' => '2025-12-20',
            'total_earned' => '90.00',
            'max_return_amount' => '100.00',
        ]);
        $invOld->forceFill([
            'created_at' => Carbon::parse('2025-12-01 00:00:00'),
            'updated_at' => Carbon::parse('2025-12-01 00:00:00'),
        ])->save();

        // Newer purchase, but with an earlier started_on.
        $invNew = Investment::query()->create([
            'user_id' => $user->id,
            'investment_package_id' => $pkg->id,
            'currency' => 'USDT',
            'amount' => '100.00',
            'status' => 'active',
            'started_on' => '2025-12-01',
            'total_earned' => '0.00',
            'max_return_amount' => '100.00',
        ]);
        $invNew->forceFill([
            'created_at' => Carbon::parse('2025-12-10 00:00:00'),
            'updated_at' => Carbon::parse('2025-12-10 00:00:00'),
        ])->save();

        // Allocate 50: should fill remaining 10 on the oldest purchase, then 40 to the newer one.
        $credited = EarningAllocator::creditToOldestInvestments(
            $user->id,
            '50.00',
            'direct_sponsor',
            Carbon::parse('2025-12-21'),
            ['test' => true],
        );

        $this->assertSame('50.00', $credited);

        $earnings = InvestmentEarning::query()->orderBy('id')->get();
        $this->assertCount(2, $earnings);

        $this->assertSame($invOld->id, $earnings[0]->investment_id);
        $this->assertSame('10.00', (string) $earnings[0]->amount);

        $this->assertSame($invNew->id, $earnings[1]->investment_id);
        $this->assertSame('40.00', (string) $earnings[1]->amount);
    }
}

