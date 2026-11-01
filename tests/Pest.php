<?php

use App\Models\AttendanceRecord;
use App\Models\Conges;
use App\Models\Employee;
use App\Models\User;
use App\Services\TokenService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "uses()" function to bind a different classes or traits.
|
*/

uses(TestCase::class, RefreshDatabase::class)->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function tokenCookiesFor(User $user, bool $remember = false): array
{
    $tokens = app(TokenService::class)->issueTokens($user, $remember);

    return [
        'access_token' => $tokens['access_token'],
        'refresh_token' => $tokens['refresh_token'],
    ];
}

/**
 * @return array{0: User, 1: Employee}
 */
function collaboratorAccount(array $employeeOverrides = [], array $userOverrides = []): array
{
    $employee = Employee::factory()->create(array_merge([
        'status' => 'actif',
        'department' => 'it',
        'hire_date' => '2021-01-15',
        'token_leave_days' => 20,
        'leave_rights' => 30,
        'token_sick_leave_days' => 15,
    ], $employeeOverrides));

    $user = User::factory()->create(array_merge([
        'email' => mb_strtoupper($employee->email),
        'role' => 'collaborator',
    ], $userOverrides));

    return [$user, $employee];
}

function collabAttendance(Employee $employee, string $date, array $attributes = []): AttendanceRecord
{
    return AttendanceRecord::create(array_merge([
        'employee_id' => $employee->id,
        'work_date' => $date,
        'check_in' => '08:30:00',
        'check_out' => '17:30:00',
        'break_minutes' => 60,
        'mode' => 'biometric',
        'status' => 'present',
    ], $attributes));
}

function collabLeave(Employee $employee, string $start, string $end, array $attributes = []): Conges
{
    return Conges::create(array_merge([
        'employee_id' => $employee->id,
        'employee_name' => $employee->name,
        'type' => 'annuel',
        'status' => 'pending',
        'start_date' => $start,
        'end_date' => $end,
        'working_days' => Conges::workingDaysFor($start, $end),
        'reason' => 'Raison de test.',
    ], $attributes));
}
