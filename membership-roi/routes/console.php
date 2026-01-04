<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Legacy ROI rate accrual (older module)
Schedule::command('roi:accrue')->dailyAt('00:10');
Schedule::command('qos:distribute')->dailyAt('00:10')->timezone(\App\Services\BusinessTime::TZ);
// Poll frequently so deposits appear quickly on the wallet page.
Schedule::command('deposits:poll')->everyMinute();
Schedule::command('autotrade:tick')->everyMinute()->timezone(\App\Services\BusinessTime::TZ);
