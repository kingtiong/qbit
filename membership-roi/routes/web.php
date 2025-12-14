<?php

use App\Http\Controllers\Admin\RoiRateController;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\DepositsController;
use App\Http\Controllers\Admin\InvestmentsAdminController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Admin\WithdrawalsController;
use App\Http\Controllers\InvestmentController;
use App\Http\Controllers\InvestmentPackageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WalletController;
use Illuminate\Support\Facades\Route;

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
    Route::post('/investments', [InvestmentController::class, 'store'])->name('investments.store');

    Route::get('/wallet', [WalletController::class, 'index'])->name('wallet.index');
    Route::post('/wallet/deposit/request', [WalletController::class, 'requestDepositAddress'])->name('wallet.deposit.request');
    Route::put('/wallet/payout-address', [WalletController::class, 'updatePayoutAddress'])->name('wallet.payout.update');
    Route::post('/wallet/withdraw', [WalletController::class, 'requestWithdrawal'])->name('wallet.withdraw.request');
});

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'verified', 'admin'])
    ->group(function () {
        Route::get('/roi-rates', [RoiRateController::class, 'edit'])->name('roi_rates.edit');
        Route::post('/roi-rates', [RoiRateController::class, 'upsert'])->name('roi_rates.upsert');

        Route::get('/users', [UsersController::class, 'index'])->name('users.index');
        Route::get('/deposits', [DepositsController::class, 'index'])->name('deposits.index');
        Route::get('/withdrawals', [WithdrawalsController::class, 'index'])->name('withdrawals.index');
        Route::post('/withdrawals/{withdrawal}/approve', [WithdrawalsController::class, 'approve'])->name('withdrawals.approve');
        Route::post('/withdrawals/{withdrawal}/reject', [WithdrawalsController::class, 'reject'])->name('withdrawals.reject');
        Route::post('/withdrawals/{withdrawal}/paid', [WithdrawalsController::class, 'markPaid'])->name('withdrawals.paid');
        Route::get('/investments', [InvestmentsAdminController::class, 'index'])->name('investments.index');
        Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
        Route::post('/settings/deposit-addresses', [SettingsController::class, 'addDepositAddress'])->name('settings.deposit_addresses.add');
        Route::post('/settings/deposit-addresses/{depositAddress}/toggle', [SettingsController::class, 'toggleDepositAddress'])->name('settings.deposit_addresses.toggle');
    });

// Admin login (requested URL)
Route::prefix('quantumbitv9')
    ->name('quantumbitv9.')
    ->middleware('guest')
    ->group(function () {
        Route::get('/login', [AdminAuthController::class, 'create'])->name('login');
        Route::post('/login', [AdminAuthController::class, 'store'])->name('login.store');
    });

Route::prefix('quantumbitv9')
    ->name('quantumbitv9.')
    ->middleware(['auth', 'admin'])
    ->group(function () {
        Route::post('/logout', [AdminAuthController::class, 'destroy'])->name('logout');
    });

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
