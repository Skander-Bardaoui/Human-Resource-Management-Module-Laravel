<?php

use App\Models\DocumentRequest;
use App\Models\Employee;
use App\Models\Payslip;
use Carbon\Carbon;

beforeEach(function () {
    Carbon::setTestNow(Carbon::parse('2026-10-19 08:30:00', 'Africa/Tunis'));
});

afterEach(function () {
    Carbon::setTestNow();
});

it('renders real leave, activity and payslip data', function () {
    [$user, $employee] = collaboratorAccount([
        'token_leave_days' => 12,
        'leave_rights' => 30,
    ]);

    collabLeave($employee, '2026-03-02', '2026-03-06', ['status' => 'approved']);
    collabLeave($employee, '2026-11-02', '2026-11-03');

    DocumentRequest::create([
        'employee_id' => $employee->id,
        'type' => 'attestation_salaire',
        'purpose' => 'Dossier bancaire',
        'status' => 'pending',
    ]);

    $response = $this->withCookies(tokenCookiesFor($user))->get(route('dashboard'));

    $response->assertOk()->assertSee('Bonjour');

    expect($response->viewData('leaveRemaining'))->toBe(12)
        ->and($response->viewData('leaveTaken'))->toBe(5)
        ->and($response->viewData('leaveAcquired'))->toBe(30)
        ->and($response->viewData('pendingCount'))->toBe(2)
        ->and($response->viewData('punchState'))->toBe('not_started')
        ->and($response->viewData('latestPayslip'))->toBeInstanceOf(Payslip::class)
        ->and(collect($response->viewData('activity'))->pluck('title'))->toContain('Demande de Congé Annuel Payé');

    expect($response->viewData('activity'))->toHaveCount(3);
});

it('derives the punch state and elapsed time from the open session', function () {
    [$user, $employee] = collaboratorAccount();
    collabAttendance($employee, '2026-10-19', [
        'check_in' => '08:00:00',
        'check_out' => null,
        'break_minutes' => 0,
        'status' => 'present',
    ]);

    $response = $this->withCookies(tokenCookiesFor($user))->get(route('dashboard'));

    $response->assertOk();

    expect($response->viewData('punchState'))->toBe('working')
        ->and($response->viewData('elapsedSeconds'))->toBe(30 * 60);
});

it('marks the day as on leave when an approved leave covers today', function () {
    [$user, $employee] = collaboratorAccount();
    collabLeave($employee, '2026-10-19', '2026-10-20', ['status' => 'approved']);

    $response = $this->withCookies(tokenCookiesFor($user))->get(route('dashboard'));

    $response->assertOk();

    expect($response->viewData('punchState'))->toBe('leave');
});

it('builds the team grid from the same department only', function () {
    [$user, $employee] = collaboratorAccount(['name' => 'Self Employee', 'department' => 'it']);

    $colleague = Employee::factory()->create(['name' => 'Colleague It', 'department' => 'it', 'status' => 'actif']);
    $outsider = Employee::factory()->create(['name' => 'Outsider Finance', 'department' => 'finance', 'status' => 'actif']);

    $response = $this->withCookies(tokenCookiesFor($user))->get(route('dashboard'));

    $response->assertOk();

    $names = collect($response->viewData('team')['rows'])->map(fn (array $row) => $row['employee']->name);

    expect($names)->toContain('Self Employee')
        ->and($names)->toContain($colleague->name)
        ->and($names)->not->toContain($outsider->name);
});
