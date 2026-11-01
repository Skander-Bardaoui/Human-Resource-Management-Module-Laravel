<?php

use App\Models\Conges;
use App\Models\Employee;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

function adminLeaveUser(): User
{
    return User::factory()->create([
        'role' => 'admin',
        'email_verified_at' => now(),
    ]);
}

function leaveEmployee(array $overrides = []): Employee
{
    return Employee::factory()->create(array_merge([
        'status' => 'actif',
        'department' => 'it',
        'token_leave_days' => 30,
        'leave_rights' => 30,
    ], $overrides));
}

function makeLeave(Employee $employee, array $overrides = []): Conges
{
    $start = $overrides['start_date'] ?? '2026-10-08';
    $end = $overrides['end_date'] ?? '2026-10-10';

    return Conges::create(array_merge([
        'employee_id' => $employee->id,
        'employee_name' => $employee->name,
        'type' => 'annuel',
        'status' => 'pending',
        'start_date' => $start,
        'end_date' => $end,
        'working_days' => Conges::workingDaysFor($start, $end),
        'reason' => 'Raison de test.',
    ], $overrides));
}

beforeEach(function () {
    Carbon::setTestNow('2026-10-21 09:00:00');
});

afterEach(function () {
    Carbon::setTestNow();
});

it('renders the leave dashboard with dynamic KPIs and rows', function () {
    $admin = adminLeaveUser();
    $employee = leaveEmployee(['name' => 'Mohamed Ben Salem', 'position' => 'Ingénieur DevOps', 'token_leave_days' => 18]);
    makeLeave($employee, ['reason' => 'Vacances annuelles.']);

    $response = $this->withCookies(tokenCookiesFor($admin))->get(route('admin.leaves'));

    $response->assertOk()
        ->assertSee('Gestion des Congés & Absences')
        ->assertSee('Mohamed Ben Salem')
        ->assertSee('Vacances annuelles.')
        ->assertSee('Octobre 2026')
        ->assertSee('Nouvelle Demande de Congé')
        ->assertSee('En Attente')
        ->assertSee('Approuver')
        ->assertSee('Refuser');

    expect($response->viewData('pendingCount'))->toBe(1);
    expect($response->viewData('leaves')->total())->toBe(1);
});

it('protects the leave dashboard from guests and collaborators', function () {
    $this->get(route('admin.leaves'))->assertRedirect(route('login'));

    $collaborator = User::factory()->create(['role' => 'collaborator', 'email_verified_at' => now()]);

    $this->withCookies(tokenCookiesFor($collaborator))
        ->get(route('admin.leaves'))
        ->assertRedirect(route('dashboard'));
});

it('creates a leave request with working days and an attachment', function () {
    Storage::fake('local');

    $admin = adminLeaveUser();
    $employee = leaveEmployee(['name' => 'Yasmine Trabelsi']);

    $response = $this->withCookies(tokenCookiesFor($admin))
        ->post(route('admin.leaves.store'), [
            'employee_id' => $employee->id,
            'type' => 'annuel',
            'start_date' => '2026-10-05',
            'end_date' => '2026-10-11',
            'reason' => 'Congé annuel en famille.',
            'attachment' => UploadedFile::fake()->create('certificat.pdf', 120, 'application/pdf'),
        ]);

    $response->assertRedirect(route('admin.leaves', ['month' => '2026-10']))
        ->assertSessionHas('success');

    $leave = Conges::first();

    expect($leave->status)->toBe('pending');
    expect($leave->employee_id)->toBe($employee->id);
    expect($leave->working_days)->toBe(6);
    expect($leave->attachment_name)->toBe('certificat.pdf');
    Storage::disk('local')->assertExists($leave->attachment_path);
});

it('rejects a leave request ending before it starts', function () {
    $admin = adminLeaveUser();
    $employee = leaveEmployee();

    $response = $this->withCookies(tokenCookiesFor($admin))
        ->post(route('admin.leaves.store'), [
            'employee_id' => $employee->id,
            'type' => 'annuel',
            'start_date' => '2026-10-12',
            'end_date' => '2026-10-09',
            'reason' => 'Dates invalides.',
        ]);

    $response->assertSessionHasErrors('end_date');
    expect(Conges::count())->toBe(0);
});

it('approves a pending annual request and decrements the balance', function () {
    $admin = adminLeaveUser();
    $employee = leaveEmployee(['token_leave_days' => 20]);
    $leave = makeLeave($employee, ['working_days' => 3]);

    $response = $this->withCookies(tokenCookiesFor($admin))
        ->patch(route('admin.leaves.approve', $leave));

    $response->assertRedirect()->assertSessionHas('success');

    $leave->refresh();
    expect($leave->status)->toBe('approved');
    expect($leave->reviewed_by)->toBe($admin->id);
    expect($leave->reviewed_at)->not->toBeNull();
    expect($employee->fresh()->token_leave_days)->toBe(17);
});

it('rejects a request without touching the balance', function () {
    $admin = adminLeaveUser();
    $employee = leaveEmployee(['token_leave_days' => 20]);
    $leave = makeLeave($employee);

    $response = $this->withCookies(tokenCookiesFor($admin))
        ->patch(route('admin.leaves.reject', $leave), ['decision_note' => 'Pic d\'activité.']);

    $response->assertRedirect()->assertSessionHas('success');

    $leave->refresh();
    expect($leave->status)->toBe('rejected');
    expect($leave->reviewed_by)->toBe($admin->id);
    expect($leave->decision_note)->toBe('Pic d\'activité.');
    expect($employee->fresh()->token_leave_days)->toBe(20);
});

it('does not let an already processed request be approved again', function () {
    $admin = adminLeaveUser();
    $employee = leaveEmployee(['token_leave_days' => 20]);
    $leave = makeLeave($employee, ['status' => 'approved', 'working_days' => 3]);

    $this->withCookies(tokenCookiesFor($admin))
        ->patch(route('admin.leaves.approve', $leave))
        ->assertSessionHas('error');

    expect($employee->fresh()->token_leave_days)->toBe(20);
});

it('stops an approved future leave and restores the annual balance', function () {
    $admin = adminLeaveUser();
    $employee = leaveEmployee(['token_leave_days' => 17]);
    $leave = makeLeave($employee, [
        'status' => 'approved',
        'start_date' => '2026-10-26',
        'end_date' => '2026-10-28',
        'working_days' => 3,
    ]);

    $this->withCookies(tokenCookiesFor($admin))
        ->patch(route('admin.leaves.stop', $leave))
        ->assertSessionHas('success');

    $leave->refresh();

    expect($leave->status)->toBe('cancelled')
        ->and($leave->reviewed_by)->toBe($admin->id)
        ->and($leave->decision_note)->toContain('Annulée par l\'administration')
        ->and($employee->fresh()->token_leave_days)->toBe(20);
});

it('stops an approved leave in progress and keeps the consumed days', function () {
    $admin = adminLeaveUser();
    $employee = leaveEmployee(['token_leave_days' => 15]);
    $leave = makeLeave($employee, [
        'status' => 'approved',
        'start_date' => '2026-10-19',
        'end_date' => '2026-10-23',
        'working_days' => 5,
    ]);

    $this->withCookies(tokenCookiesFor($admin))
        ->patch(route('admin.leaves.stop', $leave))
        ->assertSessionHas('success');

    $leave->refresh();

    expect($leave->status)->toBe('approved')
        ->and($leave->end_date->toDateString())->toBe('2026-10-20')
        ->and($leave->working_days)->toBe(2)
        ->and($leave->reviewed_by)->toBe($admin->id)
        ->and($employee->fresh()->token_leave_days)->toBe(18);
});

it('only stops approved leaves that have not ended yet', function () {
    $admin = adminLeaveUser();
    $employee = leaveEmployee(['token_leave_days' => 20]);

    $pending = makeLeave($employee);
    $ended = makeLeave($employee, [
        'status' => 'approved',
        'start_date' => '2026-10-05',
        'end_date' => '2026-10-07',
        'working_days' => 3,
    ]);

    $cookies = tokenCookiesFor($admin);

    $this->withCookies($cookies)
        ->patch(route('admin.leaves.stop', $pending))
        ->assertSessionHas('error');

    $this->withCookies($cookies)
        ->patch(route('admin.leaves.stop', $ended))
        ->assertSessionHas('error');

    expect($pending->fresh()->status)->toBe('pending')
        ->and($ended->fresh()->status)->toBe('approved')
        ->and($employee->fresh()->token_leave_days)->toBe(20);
});

it('renders the stop confirmation popup for approved leaves', function () {
    $admin = adminLeaveUser();
    $employee = leaveEmployee(['name' => 'Nadia Bouazizi']);
    makeLeave($employee, [
        'status' => 'approved',
        'start_date' => '2026-10-26',
        'end_date' => '2026-10-28',
        'working_days' => 3,
    ]);

    $this->withCookies(tokenCookiesFor($admin))
        ->get(route('admin.leaves', ['month' => '2026-10']))
        ->assertOk()
        ->assertSee('id="stop-leave-dialog"', false)
        ->assertSee('data-stop-leave', false)
        ->assertSee('data-employee="Nadia Bouazizi"', false)
        ->assertSee('data-days="3"', false)
        ->assertSee('data-mode="future"', false);
});

it('filters requests by search, type, status, department and month', function () {
    $admin = adminLeaveUser();
    $itEmployee = leaveEmployee(['name' => 'Anis Gharbi', 'department' => 'it']);
    $financeEmployee = leaveEmployee(['name' => 'Fatma Ghorbel', 'department' => 'finance']);

    $annual = makeLeave($itEmployee, ['reason' => 'Raison annuelle unique.']);
    $sick = makeLeave($financeEmployee, [
        'type' => 'maladie',
        'status' => 'approved',
        'working_days' => 2,
        'reason' => 'Raison maladie unique.',
    ]);
    $nextMonth = makeLeave($itEmployee, [
        'start_date' => '2026-11-04',
        'end_date' => '2026-11-05',
        'working_days' => 2,
        'reason' => 'Raison novembre unique.',
    ]);

    $cookies = tokenCookiesFor($admin);
    $ids = fn (array $params) => collect(
        $this->withCookies($cookies)->get(route('admin.leaves', $params))->viewData('leaves')->items()
    )->pluck('id')->all();

    expect($ids(['q' => 'unique', 'month' => '2026-10']))->toEqualCanonicalizing([$annual->id, $sick->id]);
    expect($ids(['type' => 'maladie', 'month' => '2026-10']))->toBe([$sick->id]);
    expect($ids(['status' => 'pending', 'month' => '2026-10']))->toBe([$annual->id]);
    expect($ids(['department' => 'finance', 'month' => '2026-10']))->toBe([$sick->id]);
    expect($ids(['month' => '2026-10']))->toEqualCanonicalizing([$annual->id, $sick->id]);
    expect($ids(['month' => '2026-11']))->toBe([$nextMonth->id]);
});

it('exports the filtered requests as CSV', function () {
    $admin = adminLeaveUser();
    $employee = leaveEmployee(['name' => 'Mehdi Zghal', 'cin' => '09123456']);
    makeLeave($employee, ['status' => 'approved', 'reason' => 'Congé validé.']);

    $response = $this->withCookies(tokenCookiesFor($admin))
        ->get(route('admin.leaves.export', ['month' => '2026-10', 'status' => 'approved']));

    $response->assertOk();
    expect($response->headers->get('content-type'))->toContain('text/csv');
    expect($response->headers->get('content-disposition'))->toContain('conges-2026-10-21.csv');

    $csv = $response->streamedContent();

    expect($csv)->toContain('Collaborateur')
        ->toContain('Mehdi Zghal')
        ->toContain('09123456')
        ->toContain('Approuvé')
        ->toContain('Congé validé.');
});

it('renders the team calendar view', function () {
    $admin = adminLeaveUser();
    $employee = leaveEmployee(['name' => 'Sonia Mezghani']);
    makeLeave($employee, ['status' => 'approved']);

    $response = $this->withCookies(tokenCookiesFor($admin))
        ->get(route('admin.leaves', ['display' => 'calendar', 'month' => '2026-10']));

    $response->assertOk()
        ->assertSee("Planning d'Équipe — Octobre 2026", false)
        ->assertSee('Sonia Mezghani')
        ->assertSee('Revenir à la vue tableau');
});

it('renders the details drawer for a request', function () {
    $admin = adminLeaveUser();
    $employee = leaveEmployee(['name' => 'Karim Chaabane', 'email' => 'karim@example.tn']);
    $leave = makeLeave($employee, ['reason' => 'Démarche administrative.']);

    $response = $this->withCookies(tokenCookiesFor($admin))
        ->get(route('admin.leaves', ['view' => $leave->id, 'month' => '2026-10']));

    $response->assertOk()
        ->assertSee('Détails de la demande')
        ->assertSee('Démarche administrative.')
        ->assertSee('karim@example.tn')
        ->assertSee('Validation RH')
        ->assertSee('Approuver');
});

it('downloads a stored attachment and 404s when missing', function () {
    Storage::fake('local');

    $admin = adminLeaveUser();
    $employee = leaveEmployee();
    $leave = makeLeave($employee, [
        'attachment_path' => 'leave-documents/certificat.pdf',
        'attachment_name' => 'certificat.pdf',
    ]);

    Storage::disk('local')->put('leave-documents/certificat.pdf', '%PDF-1.4 fake');

    $this->withCookies(tokenCookiesFor($admin))
        ->get(route('admin.leaves.attachment', $leave))
        ->assertOk()
        ->assertDownload('certificat.pdf');

    $withoutFile = makeLeave($employee, [
        'start_date' => '2026-10-12',
        'end_date' => '2026-10-13',
        'working_days' => 2,
        'attachment_path' => 'leave-documents/missing.pdf',
        'attachment_name' => 'missing.pdf',
    ]);

    $this->withCookies(tokenCookiesFor($admin))
        ->get(route('admin.leaves.attachment', $withoutFile))
        ->assertNotFound();
});
