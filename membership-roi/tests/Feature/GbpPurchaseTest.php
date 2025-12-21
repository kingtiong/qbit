<?php

namespace Tests\Feature;

use App\Models\GbpPurchase;
use App\Models\GbpTier;
use App\Models\User;
use App\Models\Wallet;
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

        $resp = $this->actingAs($user)->post(route('gbp.purchase'), [
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
}

