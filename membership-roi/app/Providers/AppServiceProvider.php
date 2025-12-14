<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Wallet;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        User::created(function (User $user): void {
            Wallet::firstOrCreate(['user_id' => $user->id], ['balance' => 0]);
        });
    }
}
