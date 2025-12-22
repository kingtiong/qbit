<?php

namespace Tests\Feature;

use App\Models\GbpPurchase;
use App\Models\GbpTier;
use App\Models\InvestmentPackage;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Investment;
use App\Services\BusinessTime;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GbpPurchaseTest extends TestCase
{
    use RefreshDatabase;

    public function test_purchase_splits_across_tiers_and_debits_integer_amounts(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $wallet = Wallet::forUser($user->id, Wallet::TYPE_REGISTERED);
        $wallet->forceFill(['balance' => '1000000.00'])->save();

        // Tier 1: 2 units at 300; Tier 2: 10 units at 360
        $t1 = GbpTier::create([
            'tier' => 1,
            'unit_price' => 300,
            'total_units' => 2,
            'sold_units' => 0,
            'is_active' => true,
        ]);
        $t2 = GbpTier::create([
            'tier' => 2,
            'unit_price' => 360,
            'total_units' => 10,
            'sold_units' => 0,
            'is_active' => true,
        ]);

        $resp = $this->actingAs($user)->post(route('qbp.purchase'), [
            'units' => 3,
        ]);

        $resp->assertSessionHas('status');

        $t1->refresh();
        $t2->refresh();
        $this->assertSame(2, (int) $t1->sold_units);
        $this->assertSame(1, (int) $t2->sold_units);

        $purchases = GbpPurchase::query()->orderBy('id')->get();
        $this->assertCount(2, $purchases);

        $this->assertSame($user->id, $purchases[0]->user_id);
        $this->assertSame($t1->id, $purchases[0]->gbp_tier_id);
        $this->assertSame(2, (int) $purchases[0]->units);
        $this->assertSame(300, (int) $purchases[0]->unit_price);
        $this->assertSame(600, (int) $purchases[0]->total_amount);

        $this->assertSame($t2->id, $purchases[1]->gbp_tier_id);
        $this->assertSame(1, (int) $purchases[1]->units);
        $this->assertSame(360, (int) $purchases[1]->unit_price);
        $this->assertSame(360, (int) $purchases[1]->total_amount);

        $wallet->refresh();
        // 1000000 - (600 + 360) = 999040.00
        $this->assertSame('999040.00', number_format((float) $wallet->balance, 2, '.', ''));
    }

    public function test_sponsor_gets_10_percent_commission_only_if_sponsor_bought_gbp(): void
    {
        $sponsor = User::factory()->create(['email_verified_at' => now()]);
        $downline = User::factory()->create([
            'email_verified_at' => now(),
            'sponsor_id' => $sponsor->id,
        ]);

        $pkg = InvestmentPackage::query()->create([
            'label' => 'Test QPU',
            'currency' => 'USDT',
            'amount' => '1.00',
            'is_active' => true,
        ]);

        // Sponsor needs an active investment to receive credits via allocator (cap logic).
        // Give sponsor a simple investment with plenty of remaining cap.
        Investment::query()->create([
            'user_id' => $sponsor->id,
            'investment_package_id' => $pkg->id,
            'currency' => 'USDT',
            'amount' => '100.00',
            'status' => 'active',
            'started_on' => BusinessTime::today()->toDateString(),
            'total_earned' => '0.00',
            'max_return_amount' => '100000.00',
        ]);

        $downlineWallet = Wallet::forUser($downline->id, Wallet::TYPE_REGISTERED);
        $downlineWallet->forceFill(['balance' => '10000.00'])->save();

        // Minimal tier setup: 1 unit at 301 => total 301 => 10% rounds to 30 (integer-only)
        GbpTier::create([
            'tier' => 1,
            'unit_price' => 301,
            'total_units' => 10,
            'sold_units' => 0,
            'is_active' => true,
        ]);

        // Case 1: sponsor has NOT bought GBP -> no commission
        $this->actingAs($downline)->post(route('qbp.purchase'), ['units' => 1]);
        $sponsorCommissionWallet = Wallet::forUser($sponsor->id, Wallet::TYPE_COMMISSION);
        $sponsorCommissionWallet->refresh();
        $this->assertSame('0.00', number_format((float) $sponsorCommissionWallet->balance, 2, '.', ''));

        // Sponsor buys at least 1 GBP (create a purchase record).
        $tier = GbpTier::query()->firstOrFail();
        GbpPurchase::create([
            'user_id' => $sponsor->id,
            'gbp_tier_id' => $tier->id,
            'units' => 1,
            'unit_price' => 301,
            'total_amount' => 301,
            'wallet_transaction_id' => null,
            'purchased_at' => now(),
        ]);

        // Case 2: sponsor now qualifies -> commission paid
        $this->actingAs($downline)->post(route('qbp.purchase'), ['units' => 1]);
        $sponsorCommissionWallet->refresh();
        $this->assertSame('30.00', number_format((float) $sponsorCommissionWallet->balance, 2, '.', ''));
    }
}

