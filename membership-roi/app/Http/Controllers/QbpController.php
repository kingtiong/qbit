<?php

namespace App\Http\Controllers;

use App\Models\PartnershipPosition;
use App\Models\PartnershipPackage;
use App\Models\Wallet;
use App\Services\BusinessTime;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class QbpController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        $registeredWallet = Wallet::forUser($user->id, Wallet::TYPE_REGISTERED);
        $commissionWallet = Wallet::forUser($user->id, Wallet::TYPE_COMMISSION);

        $qbpPackages = PartnershipPackage::query()
            ->where('is_active', true)
            ->orderBy('level')
            ->get();

        $myActivePackageIds = PartnershipPosition::query()
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->pluck('partnership_package_id')
            ->all();

        return view('qbp.index', [
            'user' => $user,
            'registeredWallet' => $registeredWallet,
            'commissionWallet' => $commissionWallet,
            'qbpPackages' => $qbpPackages,
            'myActivePackageIds' => $myActivePackageIds,
        ]);
    }

    public function purchase(Request $request, PartnershipPackage $partnershipPackage): RedirectResponse
    {
        $user = Auth::user();

        if (!$partnershipPackage->is_active) {
            return back()->with('status', 'This QBP package is not active.');
        }

        // Purchases use the Registered Wallet (USDT).
        $wallet = Wallet::forUser($user->id, Wallet::TYPE_REGISTERED);
        $amount = number_format((float) $partnershipPackage->amount, 2, '.', '');

        try {
            DB::transaction(function () use ($user, $partnershipPackage, $wallet, $amount): void {
                // Lock wallet row
                $lockedWallet = Wallet::query()->whereKey($wallet->id)->lockForUpdate()->firstOrFail();
                if (bccomp((string) $lockedWallet->balance, (string) $amount, 2) < 0) {
                    abort(422, 'Insufficient Registered Wallet balance.');
                }

                // Enforce holder limits
                $activeCount = PartnershipPosition::query()
                    ->where('partnership_package_id', $partnershipPackage->id)
                    ->where('status', 'active')
                    ->lockForUpdate()
                    ->count();

                if ($activeCount >= (int) $partnershipPackage->holder_limit) {
                    abort(422, 'Holder limit reached for '.$partnershipPackage->code.'.');
                }

                // Create position if user doesn't already have it
                $pos = PartnershipPosition::firstOrCreate(
                    ['user_id' => $user->id, 'partnership_package_id' => $partnershipPackage->id],
                    ['status' => 'active', 'purchased_at' => Carbon::now()],
                );

                if ($pos->wasRecentlyCreated === false && $pos->status === 'active') {
                    abort(422, 'You already own this QBP position.');
                }

                if ($pos->status !== 'active') {
                    $pos->forceFill(['status' => 'active', 'purchased_at' => Carbon::now()])->save();
                }

                // Debit wallet + record transaction
                $lockedWallet->transactions()->create([
                    'type' => 'qbp_purchase_debit',
                    'amount' => bcmul($amount, '-1', 2),
                    'meta' => [
                        'partnership_package_id' => $partnershipPackage->id,
                        'code' => $partnershipPackage->code,
                        'level' => $partnershipPackage->level,
                    ],
                    'occurred_on' => BusinessTime::today()->toDateString(),
                ]);

                $lockedWallet->decrement('balance', $amount);
            });
        } catch (\Throwable $e) {
            // abort() throws HttpException; keep message in status.
            $msg = $e->getMessage() ?: 'Unable to purchase QBP right now.';
            return back()->with('status', $msg);
        }

        return back()->with('status', 'QBP purchased: '.$partnershipPackage->code);
    }
}

