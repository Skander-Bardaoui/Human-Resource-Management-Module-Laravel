<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Operational timezone
    |--------------------------------------------------------------------------
    |
    | The application runs on UTC, but HR operations (punch clock, "today",
    | leave windows) follow Tunisia local time. Collaborator endpoints resolve
    | dates through App\Support\AttendanceClock using this timezone.
    |
    */

    'timezone' => env('HRM_TIMEZONE', 'Africa/Tunis'),

    'late_threshold' => '08:45',

    'expected_daily_minutes' => (int) env('HRM_EXPECTED_DAILY_MINUTES', 480),

    'regularization_window_days' => 7,

    'telework_monthly_quota' => 8,

    'advance_max_ratio' => 0.30,

    'advance_min_amount' => 100.0,

];
