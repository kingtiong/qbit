<?php

use App\Http\Controllers\Admin\RoiRateController;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\InvestmentController;
use App\Http\Controllers\InvestmentPackageController;
use App\Http\Controllers\ProfileController;
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
});

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'verified', 'admin'])
    ->group(function () {
        Route::get('/roi-rates', [RoiRateController::class, 'edit'])->name('roi_rates.edit');
        Route::post('/roi-rates', [RoiRateController::class, 'upsert'])->name('roi_rates.upsert');
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
