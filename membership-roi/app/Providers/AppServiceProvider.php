<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Wallet;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

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
        User::creating(function (User $user): void {
            if ($user->invite_code) {
                return;
            }

            // Generate a stable, immutable invite code.
            // (Unique index allows multiple NULLs, but we always set it for new users.)
            do {
                $code = Str::upper(Str::random(10));
            } while (User::query()->where('invite_code', $code)->exists());

            $user->invite_code = $code;
        });

        User::created(function (User $user): void {
            Wallet::firstOrCreate(['user_id' => $user->id], ['balance' => 0]);
        });
    }
}
