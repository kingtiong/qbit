<?php

use App\Http\Controllers\Admin\RoiRateController;
use App\Http\Controllers\InvestmentController;
use App\Http\Controllers\InvestmentPackageController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

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

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
