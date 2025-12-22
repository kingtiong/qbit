<?php

use App\Http\Controllers\Admin\RoiRateController;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\DepositsController;
use App\Http\Controllers\Admin\InvestmentsAdminController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Admin\WithdrawalsController;
use App\Http\Controllers\Admin\WalletAdjustmentsController;
use App\Http\Controllers\InvestmentController;
use App\Http\Controllers\InvestmentPackageController;
use App\Http\Controllers\AutoTradeController;
use App\Http\Controllers\GbpController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WalletController;
use Illuminate\Support\Facades\Route;

Route::middleware('locale')->group(function () {

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/invite/{code}', function (string $code) {
    session(['invite_code' => strtoupper($code)]);
    return redirect()->route('register', ['invite' => strtoupper($code)]);
})->middleware('guest')->name('invite.link');

Route::get('/dashboard', [InvestmentController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/packages', [InvestmentPackageController::class, 'index'])->name('packages.index');
    // GBP (tiered sale). Keep /qbp as backward-compatible alias.
    Route::get('/gbp', [GbpController::class, 'index'])->name('gbp.index');
    Route::post('/gbp/purchase', [GbpController::class, 'purchase'])->name('gbp.purchase');
    Route::get('/qbp', fn () => redirect()->route('gbp.index'))->name('qbp.index');
    Route::post('/qbp/purchase', fn () => redirect()->route('gbp.purchase'))->name('qbp.purchase');
    Route::get('/autotrade', [AutoTradeController::class, 'index'])->name('autotrade.index');
    Route::post('/investments', [InvestmentController::class, 'store'])->name('investments.store');

    Route::get('/wallet', [WalletController::class, 'index'])->name('wallet.index');
    Route::post('/wallet/deposit/request', [WalletController::class, 'requestDepositAddress'])->name('wallet.deposit.request');
    Route::put('/wallet/payout-address', [WalletController::class, 'updatePayoutAddress'])->name('wallet.payout.update');
    Route::post('/wallet/withdraw', [WalletController::class, 'requestWithdrawal'])->name('wallet.withdraw.request');
});

// Admin area is fully separated under /quantumbitv9 with its own guard/session.
Route::prefix('quantumbitv9')->group(function () {
    Route::middleware('guest:admin')->group(function () {
        Route::get('/login', [AdminAuthController::class, 'create'])->name('admin.login');
        Route::post('/login', [AdminAuthController::class, 'store'])->name('admin.login.store');
    });

    Route::middleware('auth:admin')->group(function () {
        Route::post('/logout', [AdminAuthController::class, 'destroy'])->name('admin.logout');

        Route::get('/users', [UsersController::class, 'index'])->name('admin.users.index');
        Route::get('/deposits', [DepositsController::class, 'index'])->name('admin.deposits.index');
        Route::get('/withdrawals', [WithdrawalsController::class, 'index'])->name('admin.withdrawals.index');
        Route::post('/withdrawals/{withdrawal}/approve', [WithdrawalsController::class, 'approve'])->name('admin.withdrawals.approve');
        Route::post('/withdrawals/{withdrawal}/reject', [WithdrawalsController::class, 'reject'])->name('admin.withdrawals.reject');
        Route::post('/withdrawals/{withdrawal}/paid', [WithdrawalsController::class, 'markPaid'])->name('admin.withdrawals.paid');
        Route::get('/investments', [InvestmentsAdminController::class, 'index'])->name('admin.investments.index');
        Route::get('/wallet-adjustments', [WalletAdjustmentsController::class, 'index'])->name('admin.wallet_adjustments.index');
        Route::post('/wallet-adjustments', [WalletAdjustmentsController::class, 'store'])->name('admin.wallet_adjustments.store');
        Route::get('/settings', [SettingsController::class, 'index'])->name('admin.settings.index');
        Route::post('/settings/deposit-addresses', [SettingsController::class, 'addDepositAddress'])->name('admin.settings.deposit_addresses.add');
        Route::post('/settings/deposit-addresses/{depositAddress}/toggle', [SettingsController::class, 'toggleDepositAddress'])->name('admin.settings.deposit_addresses.toggle');
        Route::post('/settings/autotrade', [SettingsController::class, 'updateAutoTrade'])->name('admin.settings.autotrade.update');
        Route::get('/roi-rates', [RoiRateController::class, 'edit'])->name('admin.roi_rates.edit');
        Route::post('/roi-rates', [RoiRateController::class, 'upsert'])->name('admin.roi_rates.upsert');
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

});
