<?php

use App\Models\AttendanceRecord;
use Carbon\Carbon;
use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;

beforeEach(function () {
    Carbon::setTestNow(Carbon::parse('2026-10-19 08:15:00', 'Africa/Tunis'));
});

afterEach(function () {
    Carbon::setTestNow();
});

it('renders the attendance board with server-seeded values', function () {
    [$user, $employee] = collaboratorAccount();

    collabAttendance($employee, '2026-10-20');

    $response = $this->withCookies(tokenCookiesFor($user))->get(route('collaborator.attendance'));

    $response->assertOk()->assertSee('Mon Pointage &amp; Suivi du Temps', false);

    expect($response->viewData('punchState'))->toBe('not_started')
        ->and($response->viewData('weekExpectedMinutes'))->toBe(2400)
        ->and($response->viewData('journal'))->toHaveCount(1);
});

it('records an on-time arrival from the portal', function () {
    [$user, $employee] = collaboratorAccount();

    $this->withCookies(tokenCookiesFor($user))
        ->post(route('collaborator.attendance.punch-in'))
        ->assertSessionHas('success');

    $record = AttendanceRecord::first();

    expect($record->employee_id)->toBe($employee->id)
        ->and($record->check_in)->toBe('08:15:00')
        ->and($record->check_out)->toBeNull()
        ->and($record->mode)->toBe('web')
        ->and($record->status)->toBe('present');
});

it('marks an arrival after 08:45 as late and refuses a second arrival', function () {
    Carbon::setTestNow(Carbon::parse('2026-10-19 09:10:00', 'Africa/Tunis'));

    [$user] = collaboratorAccount();

    $this->withCookies(tokenCookiesFor($user))
        ->post(route('collaborator.attendance.punch-in'))
        ->assertSessionHas('success');

    expect(AttendanceRecord::first()->status)->toBe('late');

    $this->withCookies(tokenCookiesFor($user))
        ->post(route('collaborator.attendance.punch-in'))
        ->assertSessionHas('error');

    expect(AttendanceRecord::count())->toBe(1);
});

it('accumulates break time between pause start and pause end', function () {
    [$user] = collaboratorAccount();

    $cookies = tokenCookiesFor($user);

    $this->withCookies($cookies)->post(route('collaborator.attendance.punch-in'));

    Carbon::setTestNow(Carbon::parse('2026-10-19 12:00:00', 'Africa/Tunis'));
    $this->withCookies($cookies)->post(route('collaborator.attendance.pause-start'))->assertSessionHas('success');

    Carbon::setTestNow(Carbon::parse('2026-10-19 12:30:00', 'Africa/Tunis'));
    $this->withCookies($cookies)->post(route('collaborator.attendance.pause-end'))->assertSessionHas('success');

    Carbon::setTestNow(Carbon::parse('2026-10-19 17:30:00', 'Africa/Tunis'));
    $this->withCookies($cookies)->post(route('collaborator.attendance.punch-out'))->assertSessionHas('success');

    $record = AttendanceRecord::first();

    expect($record->break_minutes)->toBe(30)
        ->and($record->check_out)->toBe('17:30:00')
        ->and($record->workedMinutes())->toBe(525);
});

it('closes an open break when punching out', function () {
    [$user] = collaboratorAccount();

    $cookies = tokenCookiesFor($user);

    $this->withCookies($cookies)->post(route('collaborator.attendance.punch-in'));

    Carbon::setTestNow(Carbon::parse('2026-10-19 12:00:00', 'Africa/Tunis'));
    $this->withCookies($cookies)->post(route('collaborator.attendance.pause-start'));

    Carbon::setTestNow(Carbon::parse('2026-10-19 12:20:00', 'Africa/Tunis'));
    $this->withCookies($cookies)->post(route('collaborator.attendance.punch-out'));

    $record = AttendanceRecord::first();

    expect($record->break_minutes)->toBe(20)
        ->and($record->check_out)->toBe('12:20:00');
});

it('blocks clocking in on an approved leave day', function () {
    [$user, $employee] = collaboratorAccount();
    collabLeave($employee, '2026-10-19', '2026-10-19', ['status' => 'approved']);

    $this->withCookies(tokenCookiesFor($user))
        ->post(route('collaborator.attendance.punch-in'))
        ->assertSessionHas('error');

    expect(AttendanceRecord::count())->toBe(0);
});

it('declares telework for a valid date', function () {
    [$user, $employee] = collaboratorAccount();

    $this->withCookies(tokenCookiesFor($user))
        ->post(route('collaborator.attendance.telework'), [
            'date' => '2026-10-20',
            'note' => 'Connexion fibre.',
        ])
        ->assertSessionHas('success');

    $record = AttendanceRecord::whereDate('work_date', '2026-10-20')->first();

    expect($record)->not->toBeNull()
        ->and($record->employee_id)->toBe($employee->id)
        ->and($record->status)->toBe('remote')
        ->and($record->mode)->toBe('web')
        ->and($record->notes)->toContain('Connexion fibre');
});

it('enforces the monthly telework quota', function () {
    [$user, $employee] = collaboratorAccount();

    foreach (['2026-10-01', '2026-10-02', '2026-10-05', '2026-10-06', '2026-10-07', '2026-10-08', '2026-10-09', '2026-10-12'] as $date) {
        collabAttendance($employee, $date, [
            'check_in' => null,
            'check_out' => null,
            'break_minutes' => 0,
            'mode' => 'web',
            'status' => 'remote',
        ]);
    }

    $this->withCookies(tokenCookiesFor($user))
        ->post(route('collaborator.attendance.telework'), ['date' => '2026-10-19'])
        ->assertSessionHas('error');

    expect(AttendanceRecord::count())->toBe(8);
});

it('blocks telework on a day covered by an approved leave', function () {
    [$user, $employee] = collaboratorAccount();
    collabLeave($employee, '2026-10-20', '2026-10-20', ['status' => 'approved']);

    $this->withCookies(tokenCookiesFor($user))
        ->post(route('collaborator.attendance.telework'), ['date' => '2026-10-20'])
        ->assertSessionHas('error');

    expect(AttendanceRecord::count())->toBe(0);
});

it('regularizes a missing arrival inside the allowed window', function () {
    [$user, $employee] = collaboratorAccount();

    $this->withCookies(tokenCookiesFor($user))
        ->post(route('collaborator.attendance.regularize'), [
            'date' => '2026-10-16',
            'time' => '08:00',
            'direction' => 'in',
            'reason' => 'oubli',
            'note' => 'Badge oublié.',
        ])
        ->assertSessionHas('success');

    $record = AttendanceRecord::whereDate('work_date', '2026-10-16')->first();

    expect($record)->not->toBeNull()
        ->and($record->check_in)->toBe('08:00:00')
        ->and($record->anomaly_ignored)->toBeTrue()
        ->and($record->notes)->toStartWith('[Régularisation collaborateur]');

    $this->withCookies(tokenCookiesFor($user))
        ->post(route('collaborator.attendance.regularize'), [
            'date' => '2026-10-10',
            'time' => '08:00',
            'direction' => 'in',
            'reason' => 'oubli',
        ])
        ->assertSessionHas('error');

    expect(AttendanceRecord::whereDate('work_date', '2026-10-10')->exists())->toBeFalse();
});

it('refuses to overwrite an existing arrival during regularization', function () {
    [$user, $employee] = collaboratorAccount();
    collabAttendance($employee, '2026-10-16', ['check_in' => '08:30:00', 'check_out' => '17:30:00']);

    $this->withCookies(tokenCookiesFor($user))
        ->post(route('collaborator.attendance.regularize'), [
            'date' => '2026-10-16',
            'time' => '07:00',
            'direction' => 'in',
            'reason' => 'oubli',
        ])
        ->assertSessionHas('error');

    expect(AttendanceRecord::whereDate('work_date', '2026-10-16')->first()->check_in)->toBe('08:30:00');
});

it('reopens the telework modal with inline feedback after a validation failure', function () {
    [$user] = collaboratorAccount();

    $errors = (new ViewErrorBag)->put('default', new MessageBag([
        'date' => ['Le champ date est obligatoire.'],
    ]));

    $response = $this->withCookies(tokenCookiesFor($user))
        ->withSession([
            'errors' => $errors,
            '_old_input' => ['form' => 'telework', 'date' => '2026-10-20'],
        ])
        ->get(route('collaborator.attendance'));

    $response->assertOk()
        ->assertSee('Le champ date est obligatoire.', false)
        ->assertSee('name="form" value="telework"', false)
        ->assertSee('name="form" value="regularize"', false)
        ->assertSee('hrm-invalid', false)
        ->assertSee('openModal("telework-modal")', false)
        ->assertDontSee('openModal("regularize-modal")', false);
});

it('reopens the regularization modal with inline feedback after a validation failure', function () {
    [$user] = collaboratorAccount();

    $errors = (new ViewErrorBag)->put('default', new MessageBag([
        'time' => ['Le champ heure est obligatoire.'],
    ]));

    $response = $this->withCookies(tokenCookiesFor($user))
        ->withSession([
            'errors' => $errors,
            '_old_input' => ['form' => 'regularize', 'direction' => 'out'],
        ])
        ->get(route('collaborator.attendance'));

    $response->assertOk()
        ->assertSee('Le champ heure est obligatoire.', false)
        ->assertSee('hrm-invalid', false)
        ->assertSee('openModal("regularize-modal")', false)
        ->assertDontSee('openModal("telework-modal")', false);
});

it('exports only the collaborator own attendance as CSV', function () {
    [$user, $employee] = collaboratorAccount();
    [, $otherEmployee] = collaboratorAccount();

    collabAttendance($employee, '2026-10-15', ['notes' => 'NOTE PROPRE']);
    collabAttendance($otherEmployee, '2026-10-15', ['notes' => 'NOTE ETRANGERE']);

    $response = $this->withCookies(tokenCookiesFor($user))
        ->get(route('collaborator.attendance.export', ['periode' => 'month']));

    $response->assertOk();

    $content = $response->streamedContent();

    expect($content)->toContain('NOTE PROPRE')
        ->and($content)->not->toContain('NOTE ETRANGERE')
        ->and($content)->toStartWith("\xEF\xBB\xBF");
});
