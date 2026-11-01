<?php

use App\Models\AdvanceRequest;
use App\Models\Payslip;
use Carbon\Carbon;
use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;

beforeEach(function () {
    Carbon::setTestNow(Carbon::parse('2026-10-19 09:00:00', 'Africa/Tunis'));
});

afterEach(function () {
    Carbon::setTestNow();
});

function collabPayslip(int $employeeId, string $period, float $net, string $status = 'ready'): Payslip
{
    return Payslip::create([
        'employee_id' => $employeeId,
        'period' => $period,
        'base_salary' => 3000,
        'prime' => 100,
        'transport_allowance' => 100,
        'cnss' => 280,
        'taxable_base' => 2820,
        'irpp' => 350,
        'css' => 30,
        'net' => $net,
        'status' => $status,
        'bank' => 'BIAT',
        'rib' => '123456789',
    ]);
}

it('lists only the collaborator payslips filtered by year', function () {
    [$user, $employee] = collaboratorAccount();
    [, $otherEmployee] = collaboratorAccount();

    $own = collabPayslip($employee->id, '2025-12', 2850.5);
    $foreign = collabPayslip($otherEmployee->id, '2025-12', 5500);

    $response = $this->withCookies(tokenCookiesFor($user))
        ->get(route('collaborator.payslips', ['annee' => 2025]));

    $response->assertOk();

    $list = $response->viewData('list');

    expect($list)->toHaveCount(1)
        ->and($list->first()->id)->toBe($own->id)
        ->and($response->viewData('selected')->id)->toBe($own->id)
        ->and($response->viewData('years')->contains(2026))->toBeTrue();

    $this->withCookies(tokenCookiesFor($user))
        ->get(route('collaborator.payslips', ['bulletin' => $foreign->id]))
        ->assertNotFound();
});

it('downloads only owned payslips', function () {
    [$user, $employee] = collaboratorAccount();
    [, $otherEmployee] = collaboratorAccount();

    $own = collabPayslip($employee->id, '2025-11', 2800);
    $foreign = collabPayslip($otherEmployee->id, '2025-11', 5100);

    $response = $this->withCookies(tokenCookiesFor($user))
        ->get(route('collaborator.payslips.download', $own));

    $response->assertOk();
    expect($response->headers->get('Content-Type'))->toContain('application/pdf');

    $this->withCookies(tokenCookiesFor($user))
        ->get(route('collaborator.payslips.download', $foreign))
        ->assertNotFound();
});

it('enforces the minimum advance amount', function () {
    [$user, $employee] = collaboratorAccount();
    collabPayslip($employee->id, '2026-10', 3000);

    $this->withCookies(tokenCookiesFor($user))
        ->post(route('collaborator.payslips.advance'), ['amount' => 90])
        ->assertSessionHasErrors('amount');

    expect(AdvanceRequest::count())->toBe(0);
});

it('shows inline feedback and returns to the advance form after a validation failure', function () {
    [$user, $employee] = collaboratorAccount();
    collabPayslip($employee->id, '2026-10', 3000);

    $errors = (new ViewErrorBag)->put('default', new MessageBag([
        'amount' => ['Le montant minimum est de 100 TND.'],
    ]));

    $response = $this->withCookies(tokenCookiesFor($user))
        ->withSession([
            'errors' => $errors,
            '_old_input' => ['form' => 'advance', 'amount' => 90],
        ])
        ->get(route('collaborator.payslips', ['annee' => 2026]));

    $response->assertOk()
        ->assertSee('Le montant minimum est de 100 TND.', false)
        ->assertSee('name="form" value="advance"', false)
        ->assertSee('hrm-invalid', false)
        ->assertSee('acompte-section', false)
        ->assertSee("getElementById('montant-acompte')", false);
});

it('enforces the advance ceiling relative to the latest net', function () {
    [$user, $employee] = collaboratorAccount();
    collabPayslip($employee->id, '2026-10', 3000);

    $this->withCookies(tokenCookiesFor($user))
        ->post(route('collaborator.payslips.advance'), ['amount' => 950])
        ->assertSessionHasErrors('amount');

    expect(AdvanceRequest::count())->toBe(0);
});

it('stores one advance request per open period', function () {
    [$user, $employee] = collaboratorAccount();
    collabPayslip($employee->id, '2026-10', 3000);

    $this->withCookies(tokenCookiesFor($user))
        ->post(route('collaborator.payslips.advance'), [
            'amount' => 300,
            'reason' => 'Frais de scolarité.',
        ])
        ->assertRedirect(route('collaborator.payslips', ['annee' => 2026]))
        ->assertSessionHas('success');

    $advance = AdvanceRequest::first();

    expect($advance->employee_id)->toBe($employee->id)
        ->and($advance->period)->toBe('2026-10')
        ->and($advance->status)->toBe('pending')
        ->and((float) $advance->amount)->toBe(300.0);

    $this->withCookies(tokenCookiesFor($user))
        ->post(route('collaborator.payslips.advance'), ['amount' => 200])
        ->assertSessionHas('error');

    expect(AdvanceRequest::count())->toBe(1);
});

it('refuses an advance when no payslip exists', function () {
    [$user] = collaboratorAccount();

    $this->withCookies(tokenCookiesFor($user))
        ->post(route('collaborator.payslips.advance'), ['amount' => 150])
        ->assertSessionHas('error');

    expect(AdvanceRequest::count())->toBe(0);
});
