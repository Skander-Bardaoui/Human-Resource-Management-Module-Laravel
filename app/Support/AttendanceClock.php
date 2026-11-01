<?php

namespace App\Support;

use Carbon\Carbon;
use Carbon\CarbonInterface;

class AttendanceClock
{
    public static function now(): Carbon
    {
        return Carbon::now(self::timezone());
    }

    public static function today(): Carbon
    {
        return self::now()->startOfDay();
    }

    /**
     * @param  CarbonInterface|string  $date
     */
    public static function combine($date, string $time): Carbon
    {
        $date = Carbon::parse($date, self::timezone())->toDateString();

        return Carbon::createFromFormat('Y-m-d H:i', $date.' '.substr($time, 0, 5), self::timezone());
    }

    public static function timezone(): string
    {
        return (string) config('hrm.timezone', 'Africa/Tunis');
    }
}
