<?php

namespace App\Services;

use Carbon\Carbon;

final class BusinessTime
{
    public const TZ = 'Asia/Singapore'; // UTC+8

    public static function today(): Carbon
    {
        return Carbon::now(self::TZ)->startOfDay();
    }

    public static function yesterday(): Carbon
    {
        return self::today()->copy()->subDay();
    }

    public static function dateFromOption(?string $dateOption, bool $defaultYesterday = true): Carbon
    {
        if ($dateOption) {
            return Carbon::parse($dateOption, self::TZ)->startOfDay();
        }

        return $defaultYesterday ? self::yesterday() : self::today();
    }
}
