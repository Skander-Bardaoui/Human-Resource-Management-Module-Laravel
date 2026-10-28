<?php

use App\Models\AttendanceRecord;
use App\Models\Employee;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

function attendanceAdminUser(): User
{
    return User::factory()->create([
        'role' => 'admin',
        'email_verified_at' => now(),
    ]);
}

function attendanceEmployee(array $overrides = []): Employee
{
    return Employee::factory()->create(array_merge([
        'department' => 'it',
        'status' => 'actif',
    ], $overrides));
}

function attendanceToday(): Carbon
{
    return AttendanceRecord::currentDate();
}

function attendanceYesterday(): Carbon
{
    return AttendanceRecord::currentDate()->subDay();
}

it('renders database-backed KPIs without unsupported operational claims', function () {
    $admin = attendanceAdminUser();
    $present = attendanceEmployee(['name' => 'Amine Présent']);
    $late = attendanceEmployee(['name' => 'Leïla Retard']);
    attendanceEmployee(['name' => 'Sonia Inactive', 'status' => 'inactif']);

    AttendanceRecord::factory()->forDate(attendanceToday())->create([
        'employee_id' => $present->id,
    ]);
    AttendanceRecord::factory()->forDate(attendanceToday())->late(25)->create([
        'employee_id' => $late->id,
    ]);

    $response = $this->withCookies(tokenCookiesFor($admin))
        ->get(route('admin.attendance', ['date' => attendanceToday()->toDateString()]));

    $response->assertOk()
        ->assertSee('Suivi du Pointage & Présence')
        ->assertSee('Registre RH')
        ->assertSee('2 / 2')
        ->assertSee('Amine Présent')
        ->assertSee('Leïla Retard')
        ->assertSee('MAT-'.str_pad((string) $present->id, 4, '0', STR_PAD_LEFT))
        ->assertSee('Retard non justifié')
        ->assertDontSee('Direct Biométrique')
        ->assertDontSee('Terminal Lac 2 En Ligne')
        ->assertDontSee('Rappels envoyés')
        ->assertDontSee('Taux légal')
        ->assertDontSee('Synchronisation passerelle');

    $response->assertViewHas('activeEmployees', 2)
        ->assertViewHas('presentCount', 2)
        ->assertViewHas('lateCount', 1)
        ->assertViewHas('absenceCount', 0);
});

it('filters the roster and opens an employee history', function () {
    $admin = attendanceAdminUser();
    $alpha = attendanceEmployee(['name' => 'Alpha Martin', 'department' => 'it']);
    $beta = attendanceEmployee(['name' => 'Beta Sonia', 'department' => 'rh']);

    AttendanceRecord::factory()->forDate(attendanceToday())->absent()->create([
        'employee_id' => $alpha->id,
    ]);
    AttendanceRecord::factory()->forDate(attendanceToday())->create([
        'employee_id' => $beta->id,
    ]);

    $historyDate = attendanceToday()->copy()->startOfMonth();

    if ($historyDate->isSameDay(attendanceToday())) {
        $historyDate->addDay();
    }

    AttendanceRecord::factory()->forDate($historyDate)->create([
        'employee_id' => $alpha->id,
    ]);

    $this->withCookies(tokenCookiesFor($admin))
        ->get(route('admin.attendance', ['date' => attendanceToday()->toDateString(), 'q' => 'Alpha']))
        ->assertOk()
        ->assertViewHas('records', fn ($records) => $records->total() === 1
            && $records->getCollection()->first()->employee->name === 'Alpha Martin');

    $this->withCookies(tokenCookiesFor($admin))
        ->get(route('admin.attendance', ['date' => attendanceToday()->toDateString(), 'status' => 'absent']))
        ->assertOk()
        ->assertViewHas('records', fn ($records) => $records->total() === 1
            && $records->getCollection()->first()->employee->name === 'Alpha Martin');

    $this->withCookies(tokenCookiesFor($admin))
        ->get(route('admin.attendance', ['date' => attendanceToday()->toDateString(), 'department' => 'rh']))
        ->assertOk()
        ->assertViewHas('records', fn ($records) => $records->total() === 1
            && $records->getCollection()->first()->employee->name === 'Beta Sonia');

    $history = $this->withCookies(tokenCookiesFor($admin))
        ->get(route('admin.attendance', [
            'date' => attendanceToday()->toDateString(),
            'employee' => $alpha->id,
            'q' => 'Alpha',
        ]));

    $history->assertOk()
        ->assertSee('Historique des pointages')
        ->assertSee('Revenir au jour')
        ->assertViewHas('historyEmployee', fn (?Employee $employee) => $employee?->is($alpha) === true)
        ->assertViewHas('records', fn ($records) => $records->total() === 2);
});

it('falls back safely when filter values are malformed', function () {
    $admin = attendanceAdminUser();

    $response = $this->withCookies(tokenCookiesFor($admin))->get(route('admin.attendance', [
        'date' => '2026-13-40',
        'employee' => 'abc',
        'status' => 'unknown',
        'department' => 'unknown',
        'q' => str_repeat('a', 140),
    ]));

    $response->assertOk()
        ->assertSessionHasNoErrors()
        ->assertViewHas('selectedDate', fn (Carbon $date) => $date->toDateString() === attendanceToday()->toDateString());
});

it('records a manual punch and traces the administrator in the notes', function () {
    $admin = attendanceAdminUser();
    $employee = attendanceEmployee(['name' => 'Nadia Karray']);
    $date = attendanceYesterday()->toDateString();

    $this->withCookies(tokenCookiesFor($admin))
        ->post(route('admin.attendance.punch'), [
            'employee_id' => $employee->id,
            'date' => $date,
            'time' => '09:00',
            'direction' => 'in',
            'motif' => 'forgot',
            'comment' => 'Badge oublié au domicile.',
        ])
        ->assertRedirect(route('admin.attendance', ['date' => $date]))
        ->assertSessionHas('success', 'Pointage enregistré pour Nadia Karray.');

    $record = AttendanceRecord::query()
        ->where('employee_id', $employee->id)
        ->whereDate('work_date', $date)
        ->firstOrFail();

    expect($record->check_in)->toBe('09:00:00')
        ->and($record->status)->toBe('late')
        ->and($record->mode)->toBe('manual')
        ->and($record->notes)->toContain($admin->name)
        ->and($record->notes)->toContain('Oubli de badge')
        ->and($record->notes)->toContain('Badge oublié au domicile.');

    $this->withCookies(tokenCookiesFor($admin))
        ->post(route('admin.attendance.punch'), [
            'employee_id' => $employee->id,
            'date' => $date,
            'time' => '17:30',
            'direction' => 'out',
            'motif' => 'forgot',
        ])
        ->assertRedirect(route('admin.attendance', ['date' => $date]));

    expect($record->fresh()->check_out)->toBe('17:30:00');
});

it('is idempotent when the same punch is submitted twice', function () {
    $admin = attendanceAdminUser();
    $employee = attendanceEmployee();
    $date = attendanceYesterday()->toDateString();

    $record = AttendanceRecord::factory()->forDate($date)->create([
        'employee_id' => $employee->id,
        'check_in' => '08:20:00',
        'status' => 'present',
        'mode' => 'biometric',
        'notes' => 'Note initiale.',
        'anomaly_ignored' => true,
    ]);

    $this->withCookies(tokenCookiesFor($admin))
        ->post(route('admin.attendance.punch'), [
            'employee_id' => $employee->id,
            'date' => $date,
            'time' => '08:20',
            'direction' => 'in',
            'motif' => 'mission',
            'comment' => 'Doublon.',
        ])
        ->assertRedirect(route('admin.attendance', ['date' => $date]))
        ->assertSessionHas('success', fn (string $message) => str_contains($message, 'Aucune modification'));

    $record->refresh();

    expect($record->notes)->toBe('Note initiale.')
        ->and($record->mode)->toBe('biometric')
        ->and($record->anomaly_ignored)->toBeTrue();
});

it('rejects invalid punch sequences, inactive employees, leave days and future dates', function () {
    $admin = attendanceAdminUser();
    $employee = attendanceEmployee();
    $inactive = attendanceEmployee(['status' => 'inactif']);
    $onLeave = attendanceEmployee();
    $date = attendanceYesterday()->toDateString();

    $record = AttendanceRecord::factory()->forDate($date)->create([
        'employee_id' => $employee->id,
        'check_in' => '09:00:00',
        'check_out' => '17:30:00',
        'status' => 'late',
    ]);

    $this->withCookies(tokenCookiesFor($admin))
        ->post(route('admin.attendance.punch'), [
            'employee_id' => $employee->id,
            'date' => $date,
            'time' => '08:30',
            'direction' => 'out',
            'motif' => 'forgot',
        ])
        ->assertSessionHasErrors(['time'], null, 'punch');

    expect($record->fresh()->check_out)->toBe('17:30:00');

    $this->withCookies(tokenCookiesFor($admin))
        ->post(route('admin.attendance.punch'), [
            'employee_id' => $employee->id,
            'date' => $date,
            'time' => '18:00',
            'direction' => 'in',
            'motif' => 'forgot',
        ])
        ->assertSessionHasErrors(['time'], null, 'punch');

    AttendanceRecord::factory()->forDate($date)->onLeave()->create([
        'employee_id' => $onLeave->id,
    ]);

    $this->withCookies(tokenCookiesFor($admin))
        ->post(route('admin.attendance.punch'), [
            'employee_id' => $onLeave->id,
            'date' => $date,
            'time' => '09:00',
            'direction' => 'in',
            'motif' => 'mission',
        ])
        ->assertSessionHasErrors(['employee_id'], null, 'punch');

    $this->withCookies(tokenCookiesFor($admin))
        ->post(route('admin.attendance.punch'), [
            'employee_id' => $inactive->id,
            'date' => $date,
            'time' => '09:00',
            'direction' => 'in',
            'motif' => 'mission',
        ])
        ->assertSessionHasErrors(['employee_id'], null, 'punch');

    $this->withCookies(tokenCookiesFor($admin))
        ->post(route('admin.attendance.punch'), [
            'employee_id' => $employee->id,
            'date' => attendanceToday()->copy()->addDay()->toDateString(),
            'time' => '09:00',
            'direction' => 'in',
            'motif' => 'mission',
        ])
        ->assertSessionHasErrors(['date'], null, 'punch');
});

it('treats a missing checkout as an anomaly only for past dates', function () {
    $admin = attendanceAdminUser();
    $employee = attendanceEmployee();

    AttendanceRecord::factory()->forDate(attendanceToday())->missingCheckout()->create([
        'employee_id' => $employee->id,
    ]);

    $this->withCookies(tokenCookiesFor($admin))
        ->get(route('admin.attendance', ['date' => attendanceToday()->toDateString()]))
        ->assertOk()
        ->assertViewHas('anomalyCount', 0)
        ->assertDontSee('Départ non pointé');

    AttendanceRecord::factory()->forDate(attendanceYesterday())->missingCheckout()->create([
        'employee_id' => $employee->id,
    ]);

    $this->withCookies(tokenCookiesFor($admin))
        ->get(route('admin.attendance', ['date' => attendanceYesterday()->toDateString()]))
        ->assertOk()
        ->assertViewHas('anomalyCount', 1)
        ->assertSee('Départ non pointé')
        ->assertSee('Régulariser à 17:30');
});

it('regularizes, justifies and ignores anomalies with an audit trail', function () {
    $admin = attendanceAdminUser();
    $missing = attendanceEmployee(['name' => 'Missing Checkout']);
    $early = attendanceEmployee(['name' => 'Early Exit']);
    $late = attendanceEmployee(['name' => 'Late Arrival']);
    $date = attendanceYesterday()->toDateString();

    $missingRecord = AttendanceRecord::factory()->forDate($date)->missingCheckout()->create([
        'employee_id' => $missing->id,
    ]);
    $earlyRecord = AttendanceRecord::factory()->forDate($date)->earlyExit()->create([
        'employee_id' => $early->id,
    ]);
    $lateRecord = AttendanceRecord::factory()->forDate($date)->late(20)->create([
        'employee_id' => $late->id,
    ]);

    $this->withCookies(tokenCookiesFor($admin))
        ->post(route('admin.attendance.anomalies.resolve', $missingRecord))
        ->assertSessionHas('success', 'Départ régularisé à 17:30 pour Missing Checkout.');

    $missingRecord->refresh();
    expect($missingRecord->check_out)->toBe('17:30:00')
        ->and($missingRecord->anomaly_ignored)->toBeTrue()
        ->and($missingRecord->notes)->toContain('Départ régularisé');

    $this->withCookies(tokenCookiesFor($admin))
        ->post(route('admin.attendance.anomalies.justify', $earlyRecord), [
            'note' => 'Bon de sortie signé par la direction.',
        ])
        ->assertSessionHas('success', 'Justificatif enregistré pour Early Exit.');

    $earlyRecord->refresh();
    expect($earlyRecord->anomaly_ignored)->toBeTrue()
        ->and($earlyRecord->notes)->toContain($admin->name)
        ->and($earlyRecord->notes)->toContain('Bon de sortie signé par la direction.');

    $this->withCookies(tokenCookiesFor($admin))
        ->post(route('admin.attendance.anomalies.ignore', $lateRecord))
        ->assertSessionHas('success', 'Anomalie ignorée pour Late Arrival.');

    expect($lateRecord->fresh()->anomaly_ignored)->toBeTrue();
});

it('validates the justification note before closing an anomaly', function () {
    $admin = attendanceAdminUser();
    $employee = attendanceEmployee();
    $date = attendanceYesterday()->toDateString();

    $record = AttendanceRecord::factory()->forDate($date)->earlyExit()->create([
        'employee_id' => $employee->id,
    ]);

    $this->withCookies(tokenCookiesFor($admin))
        ->post(route('admin.attendance.anomalies.justify', $record), ['note' => 'ok'])
        ->assertSessionHasErrors(['note'], null, 'justify');

    expect($record->fresh()->anomaly_ignored)->toBeFalse();
});

it('processes every untreated anomaly of a date in one bulk action', function () {
    $admin = attendanceAdminUser();
    $missing = attendanceEmployee();
    $late = attendanceEmployee();
    $ignored = attendanceEmployee();
    $date = attendanceYesterday()->toDateString();

    $missingRecord = AttendanceRecord::factory()->forDate($date)->missingCheckout()->create([
        'employee_id' => $missing->id,
    ]);
    $lateRecord = AttendanceRecord::factory()->forDate($date)->late(20)->create([
        'employee_id' => $late->id,
    ]);
    $alreadyIgnored = AttendanceRecord::factory()->forDate($date)->missingCheckout()->ignored()->create([
        'employee_id' => $ignored->id,
    ]);

    $this->withCookies(tokenCookiesFor($admin))
        ->post(route('admin.attendance.anomalies.resolveAll'), ['date' => $date])
        ->assertSessionHas('success', '2 anomalie(s) de pointage traitée(s).');

    expect($missingRecord->fresh()->check_out)->toBe('17:30:00')
        ->and($lateRecord->fresh()->anomaly_ignored)->toBeTrue()
        ->and($alreadyIgnored->fresh()->anomaly_ignored)->toBeTrue();
});

it('computes overtime from worked minutes and the configured threshold', function () {
    $admin = attendanceAdminUser();
    $employeeA = attendanceEmployee();
    $employeeB = attendanceEmployee();
    $date = attendanceToday();

    AttendanceRecord::factory()->forDate($date->copy()->startOfMonth())->create([
        'employee_id' => $employeeA->id,
        'check_in' => '08:00:00',
        'check_out' => '18:00:00',
        'break_minutes' => 60,
        'status' => 'present',
    ]);
    AttendanceRecord::factory()->forDate($date->copy()->startOfMonth()->addDay())->create([
        'employee_id' => $employeeB->id,
        'check_in' => '08:00:00',
        'check_out' => '18:30:00',
        'break_minutes' => 30,
        'status' => 'present',
    ]);

    $response = $this->withCookies(tokenCookiesFor($admin))
        ->get(route('admin.attendance', ['date' => $date->toDateString()]));

    $response->assertOk()
        ->assertViewHas('overtimeHours', 3.0)
        ->assertViewHas('overtimeAgents', 2)
        ->assertSee('3,0 h');
});

it('builds the weekly chart from working days excluding Sunday', function () {
    $admin = attendanceAdminUser();
    $employee = attendanceEmployee();
    $date = attendanceToday();
    $weekStart = $date->copy()->startOfWeek(Carbon::MONDAY);

    AttendanceRecord::factory()->forDate($weekStart)->create(['employee_id' => $employee->id]);
    AttendanceRecord::factory()->forDate($weekStart->copy()->addDays(5))->create(['employee_id' => $employee->id]);
    AttendanceRecord::factory()->forDate($weekStart->copy()->addDays(6))->create(['employee_id' => $employee->id]);

    $response = $this->withCookies(tokenCookiesFor($admin))
        ->get(route('admin.attendance', ['date' => $date->toDateString()]));

    $response->assertOk()->assertViewHas('weekDays', function (array $days) {
        $labels = array_column($days, 'label');

        return count($days) === 6
            && str_starts_with($labels[0], 'Lun')
            && str_starts_with($labels[5], 'Sam');
    });
});

it('exports a formula-safe CSV', function () {
    $admin = attendanceAdminUser();
    $employee = attendanceEmployee(['name' => '=CMD']);
    $date = attendanceYesterday();

    AttendanceRecord::factory()->forDate($date)->create([
        'employee_id' => $employee->id,
        'notes' => '=2+2',
    ]);

    $response = $this->withCookies(tokenCookiesFor($admin))
        ->get(route('admin.attendance.export', ['date' => $date->toDateString()]));

    $response->assertOk()
        ->assertDownload('pointages-'.$date->toDateString().'.csv');

    $content = $response->streamedContent();

    expect($content)->toStartWith("\xEF\xBB\xBF")
        ->and($content)->toContain('Date;Collaborateur;Matricule')
        ->and($content)->toContain("'=CMD")
        ->and($content)->toContain("'=2+2");
});

it('keeps the attendance page query count constant as records grow', function () {
    $admin = attendanceAdminUser();
    $employee = attendanceEmployee(['name' => 'Premier Collaborateur']);
    $date = attendanceToday();

    AttendanceRecord::factory()->forDate($date)->create(['employee_id' => $employee->id]);

    DB::enableQueryLog();
    DB::flushQueryLog();

    $this->withCookies(tokenCookiesFor($admin))
        ->get(route('admin.attendance', ['date' => $date->toDateString()]))
        ->assertOk();

    $smallCount = count(DB::getQueryLog());

    for ($index = 0; $index < 20; $index++) {
        $other = attendanceEmployee();
        AttendanceRecord::factory()->forDate($date)->create(['employee_id' => $other->id]);
    }

    DB::flushQueryLog();

    $this->withCookies(tokenCookiesFor($admin))
        ->get(route('admin.attendance', ['date' => $date->toDateString()]))
        ->assertOk();

    $largeCount = count(DB::getQueryLog());

    expect($largeCount)->toBe($smallCount);

    DB::disableQueryLog();
});

it('guards attendance routes with the admin login', function () {
    $this->get(route('admin.attendance'))->assertRedirect(route('login'));
});

it('applies configured attendance rules for late arrivals, early exits and overtime', function () {
    $lateEmployee = attendanceEmployee();
    $earlyEmployee = attendanceEmployee();
    $overtimeEmployee = attendanceEmployee();

    $late = AttendanceRecord::factory()->forDate(attendanceYesterday())->late(20)->create([
        'employee_id' => $lateEmployee->id,
    ]);

    expect($late->lateMinutes())->toBe(20)
        ->and($late->isLateArrival())->toBeTrue();

    config(['attendance.late_tolerance_minutes' => 30]);

    expect($late->isLateArrival())->toBeFalse();

    $early = AttendanceRecord::factory()->forDate(attendanceYesterday())->earlyExit()->create([
        'employee_id' => $earlyEmployee->id,
    ]);

    expect($early->workedMinutes())->toBe(360)
        ->and($early->hasEarlyExit())->toBeTrue();

    config(['attendance.minimum_full_day_minutes' => 300]);

    expect($early->hasEarlyExit())->toBeFalse();

    $overtime = AttendanceRecord::factory()->forDate(attendanceYesterday())->create([
        'employee_id' => $overtimeEmployee->id,
        'check_in' => '08:00:00',
        'check_out' => '18:00:00',
        'break_minutes' => 60,
    ]);

    expect($overtime->workedMinutes())->toBe(540)
        ->and($overtime->overtimeMinutes())->toBe(60);

    config(['attendance.overtime_threshold_minutes' => 600]);

    expect($overtime->overtimeMinutes())->toBe(0);
});
