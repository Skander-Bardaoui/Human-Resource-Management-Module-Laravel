<?php

use App\Models\Conges;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Carbon::setTestNow(Carbon::parse('2026-10-19 09:00:00', 'Africa/Tunis'));
});

afterEach(function () {
    Carbon::setTestNow();
});

it('renders real balances, history and team overlaps', function () {
    [$user, $employee] = collaboratorAccount(['token_leave_days' => 12, 'leave_rights' => 30]);

    collabLeave($employee, '2026-10-27', '2026-10-28');

    $colleague = Employee::factory()->create(['department' => 'it', 'status' => 'actif']);
    collabLeave($colleague, '2026-10-22', '2026-10-23', ['status' => 'approved']);

    $outsider = Employee::factory()->create(['department' => 'finance', 'status' => 'actif']);
    collabLeave($outsider, '2026-10-22', '2026-10-23', ['status' => 'approved']);

    $response = $this->withCookies(tokenCookiesFor($user))->get(route('collaborator.conges'));

    $response->assertOk()->assertSee('Exercice 2026');

    expect($response->viewData('remaining'))->toBe(12)
        ->and($response->viewData('acquired'))->toBe(30)
        ->and($response->viewData('usageRate'))->toBe(40)
        ->and($response->viewData('statusCounts')['pending'])->toBe(1)
        ->and($response->viewData('teamOverlap')['count'])->toBe(1);
});

it('stores a whole-day leave request excluding Sunday from the working days', function () {
    [$user, $employee] = collaboratorAccount();

    $response = $this->withCookies(tokenCookiesFor($user))->post(route('collaborator.conges.store'), [
        'type' => 'annuel',
        'start_date' => '2026-10-19',
        'end_date' => '2026-10-25',
        'reason' => 'Vacances en famille.',
    ]);

    $response->assertRedirect(route('collaborator.conges'))->assertSessionHas('success');

    $leave = Conges::first();

    expect($leave)->not->toBeNull()
        ->and($leave->employee_id)->toBe($employee->id)
        ->and($leave->status)->toBe('pending')
        ->and($leave->working_days)->toBe(6)
        ->and($leave->reason)->toBe('Vacances en famille.');
});

it('rejects a start date in the past', function () {
    [$user] = collaboratorAccount();

    $this->withCookies(tokenCookiesFor($user))
        ->post(route('collaborator.conges.store'), [
            'type' => 'annuel',
            'start_date' => '2026-10-18',
            'end_date' => '2026-10-20',
            'reason' => 'Trop tard.',
        ])
        ->assertSessionHasErrors('start_date');

    expect(Conges::count())->toBe(0);
});

it('rejects a range without any working day', function () {
    [$user] = collaboratorAccount();

    $this->withCookies(tokenCookiesFor($user))
        ->post(route('collaborator.conges.store'), [
            'type' => 'annuel',
            'start_date' => '2026-10-25',
            'end_date' => '2026-10-25',
            'reason' => 'Un dimanche.',
        ])
        ->assertSessionHasErrors('end_date');
});

it('rejects overlapping pending or approved requests', function () {
    [$user, $employee] = collaboratorAccount();
    collabLeave($employee, '2026-10-21', '2026-10-22');

    $this->withCookies(tokenCookiesFor($user))
        ->post(route('collaborator.conges.store'), [
            'type' => 'annuel',
            'start_date' => '2026-10-22',
            'end_date' => '2026-10-23',
            'reason' => 'Chevauchement.',
        ])
        ->assertSessionHasErrors('start_date');

    expect(Conges::count())->toBe(1);
});

it('rejects a request exceeding the remaining annual balance', function () {
    [$user] = collaboratorAccount(['token_leave_days' => 2]);

    $this->withCookies(tokenCookiesFor($user))
        ->post(route('collaborator.conges.store'), [
            'type' => 'annuel',
            'start_date' => '2026-10-19',
            'end_date' => '2026-10-25',
            'reason' => 'Trop long.',
        ])
        ->assertSessionHasErrors('type');

    expect(Conges::count())->toBe(0);
});

it('stores an attachment and scopes its download to the owner', function () {
    Storage::fake('local');

    [$user, $employee] = collaboratorAccount();

    $this->withCookies(tokenCookiesFor($user))
        ->post(route('collaborator.conges.store'), [
            'type' => 'maladie',
            'start_date' => '2026-10-20',
            'end_date' => '2026-10-20',
            'reason' => 'Certificat médical.',
            'attachment' => UploadedFile::fake()->create('certificat.pdf', 80, 'application/pdf'),
        ])
        ->assertSessionHasNoErrors();

    $leave = Conges::first();

    expect($leave->attachment_path)->not->toBeNull();
    Storage::disk('local')->assertExists($leave->attachment_path);

    $this->withCookies(tokenCookiesFor($user))
        ->get(route('collaborator.conges.attachment', $leave))
        ->assertOk()
        ->assertDownload('certificat.pdf');

    [$otherUser] = collaboratorAccount();

    $this->withCookies(tokenCookiesFor($otherUser))
        ->get(route('collaborator.conges.attachment', $leave))
        ->assertNotFound();
});

it('cancels a pending request and stops an approved future one with balance restore', function () {
    [$user, $employee] = collaboratorAccount(['token_leave_days' => 20]);

    $pending = collabLeave($employee, '2026-10-27', '2026-10-28');

    $this->withCookies(tokenCookiesFor($user))
        ->patch(route('collaborator.conges.cancel', $pending))
        ->assertSessionHas('success');

    $pending->refresh();

    expect($pending->status)->toBe('cancelled')
        ->and($pending->decision_note)->toContain('Annulée par le collaborateur');

    $employee->update(['token_leave_days' => 18]);

    $approved = collabLeave($employee, '2026-11-03', '2026-11-04', ['status' => 'approved']);

    $this->withCookies(tokenCookiesFor($user))
        ->patch(route('collaborator.conges.cancel', $approved))
        ->assertSessionHas('success');

    expect($approved->fresh()->status)->toBe('cancelled')
        ->and($employee->fresh()->token_leave_days)->toBe(20);
});

it('stops an approved leave in progress and restores only unused days', function () {
    Carbon::setTestNow(Carbon::parse('2026-10-21 10:00:00', 'Africa/Tunis'));

    [$user, $employee] = collaboratorAccount(['token_leave_days' => 15]);

    $leave = collabLeave($employee, '2026-10-19', '2026-10-23', [
        'status' => 'approved',
        'working_days' => 5,
    ]);

    $this->withCookies(tokenCookiesFor($user))
        ->patch(route('collaborator.conges.cancel', $leave))
        ->assertSessionHas('success');

    $leave->refresh();

    expect($leave->status)->toBe('approved')
        ->and($leave->end_date->toDateString())->toBe('2026-10-20')
        ->and($leave->working_days)->toBe(2)
        ->and($leave->decision_note)->toContain('Congé arrêté par le collaborateur')
        ->and($employee->fresh()->token_leave_days)->toBe(18);
});

it('renders the stop confirmation popup for an approved leave', function () {
    [$user, $employee] = collaboratorAccount();

    collabLeave($employee, '2026-10-27', '2026-10-28', ['status' => 'approved']);

    $this->withCookies(tokenCookiesFor($user))
        ->get(route('collaborator.conges'))
        ->assertOk()
        ->assertSee('id="stop-leave-dialog"', false)
        ->assertSee('data-stop-leave', false)
        ->assertSee('data-mode="future"', false)
        ->assertSee('data-days="2"', false);
});

it('refuses to stop an approved leave that already ended', function () {
    [$user, $employee] = collaboratorAccount();

    $leave = collabLeave($employee, '2026-10-05', '2026-10-06', ['status' => 'approved']);

    $this->withCookies(tokenCookiesFor($user))
        ->patch(route('collaborator.conges.cancel', $leave))
        ->assertSessionHas('error');

    expect($leave->fresh()->status)->toBe('approved');
});

it('refuses to cancel another employee leave', function () {
    [$user] = collaboratorAccount();
    [, $otherEmployee] = collaboratorAccount();

    $foreign = collabLeave($otherEmployee, '2026-10-27', '2026-10-28');

    $this->withCookies(tokenCookiesFor($user))
        ->patch(route('collaborator.conges.cancel', $foreign))
        ->assertNotFound();

    expect($foreign->fresh()->status)->toBe('pending');
});
