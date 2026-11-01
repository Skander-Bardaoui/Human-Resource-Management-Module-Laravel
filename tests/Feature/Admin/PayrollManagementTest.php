<?php

use App\Models\Employee;
use App\Models\Payslip;
use App\Models\User;
use App\Support\Money;
use App\Support\PayrollCalculator;
use Illuminate\Support\Facades\DB;

function payrollAdminUser(): User
{
    return User::factory()->create([
        'role' => 'admin',
        'email_verified_at' => now(),
    ]);
}

function payrollEmployee(array $overrides = []): Employee
{
    return Employee::factory()->create(array_merge([
        'department' => 'it',
        'status' => 'actif',
        'family_status' => 'celibataire',
        'children' => 0,
        'bank_account' => 'BIAT 08 001 1234567890123 45',
    ], $overrides));
}

function payrollPeriod(): string
{
    return now('Africa/Tunis')->format('Y-m');
}

function payrollPayslip(
    Employee $employee,
    int $baseMillimes,
    int $primeMillimes = 0,
    string $status = 'ready',
    ?string $period = null
): Payslip {
    $amounts = PayrollCalculator::forEmployee(
        $employee,
        Money::fromMillimes($baseMillimes),
        Money::fromMillimes($primeMillimes)
    );

    return Payslip::create([
        'employee_id' => $employee->id,
        'period' => $period ?? payrollPeriod(),
        'base_salary' => $amounts['base_salary']->toDecimalString(),
        'prime' => $amounts['prime']->toDecimalString(),
        'transport_allowance' => $amounts['transport_allowance']->toDecimalString(),
        'cnss' => $amounts['cnss']->toDecimalString(),
        'taxable_base' => $amounts['taxable_base']->toDecimalString(),
        'irpp' => $amounts['irpp']->toDecimalString(),
        'css' => $amounts['css']->toDecimalString(),
        'net' => $amounts['net']->toDecimalString(),
        'status' => $status,
        'paid_at' => $status === 'paid' ? now() : null,
        'bank' => 'BIAT',
        'rib' => '08 001 1234567890123 45',
    ]);
}

it('renders database-backed payroll totals without unsupported claims', function () {
    $admin = payrollAdminUser();
    $alpha = payrollEmployee(['name' => 'Alpha Martin']);
    $beta = payrollEmployee(['name' => 'Beta Sonia', 'department' => 'rh']);
    payrollEmployee(['name' => 'Sonia Inactive', 'status' => 'inactif']);

    payrollPayslip($alpha, 1000000);
    payrollPayslip($beta, 2000000, 0, 'paid');

    $response = $this->withCookies(tokenCookiesFor($admin))
        ->get(route('admin.payroll', ['month' => payrollPeriod()]));

    $response->assertOk()
        ->assertSee('Gestion de la Paie & Salaires')
        ->assertSee('Paramétrage de paie 2026.1')
        ->assertSee('Alpha Martin')
        ->assertSee('Beta Sonia')
        ->assertSee('3 000,000 TND')
        ->assertSee('979,653')
        ->assertSee('Payé')
        ->assertDontSee('Conforme Code du Travail')
        ->assertDontSee('Validé DAF')
        ->assertDontSee('SEPA')
        ->assertDontSee('Télétransmis')
        ->assertDontSee('Certifié')
        ->assertDontSee('Cat. ')
        ->assertDontSee('40h / Semaine');

    $response->assertViewHas('activeEmployees', 2)
        ->assertViewHas('payslipCount', 2)
        ->assertViewHas('missingCount', 0)
        ->assertViewHas('periodClosed', false)
        ->assertViewHas('netTotal', fn (Money $total) => $total->millimes() === 2622292);
});

it('filters and selects payslips on the server', function () {
    $admin = payrollAdminUser();
    $alpha = payrollEmployee(['name' => 'Alpha Martin', 'department' => 'it']);
    $beta = payrollEmployee(['name' => 'Beta Sonia', 'department' => 'rh']);
    $alphaSlip = payrollPayslip($alpha, 1000000);
    payrollPayslip($beta, 2000000, 0, 'paid');

    $this->withCookies(tokenCookiesFor($admin))
        ->get(route('admin.payroll', ['month' => payrollPeriod(), 'q' => 'Beta']))
        ->assertOk()
        ->assertViewHas('payslips', fn ($payslips) => $payslips->total() === 1
            && $payslips->first()->employee->name === 'Beta Sonia');

    $this->withCookies(tokenCookiesFor($admin))
        ->get(route('admin.payroll', ['month' => payrollPeriod(), 'department' => 'rh']))
        ->assertOk()
        ->assertViewHas('payslips', fn ($payslips) => $payslips->total() === 1
            && $payslips->first()->employee->name === 'Beta Sonia');

    $this->withCookies(tokenCookiesFor($admin))
        ->get(route('admin.payroll', ['month' => payrollPeriod(), 'status' => 'paid']))
        ->assertOk()
        ->assertViewHas('payslips', fn ($payslips) => $payslips->total() === 1
            && $payslips->first()->employee->name === 'Beta Sonia');

    $this->withCookies(tokenCookiesFor($admin))
        ->get(route('admin.payroll', ['month' => payrollPeriod(), 'view' => $alphaSlip->id]))
        ->assertOk()
        ->assertViewHas('selectedPayslip', fn (?Payslip $payslip) => $payslip?->is($alphaSlip) === true);

    $this->withCookies(tokenCookiesFor($admin))
        ->get(route('admin.payroll', [
            'month' => '2026-13',
            'view' => 'abc',
            'department' => 'unknown',
            'status' => 'unknown',
            'q' => str_repeat('a', 140),
        ]))
        ->assertOk()
        ->assertSessionHasNoErrors()
        ->assertViewHas('period', payrollPeriod());
});

it('shows an honest empty state before generation', function () {
    $admin = payrollAdminUser();
    payrollEmployee();

    $response = $this->withCookies(tokenCookiesFor($admin))
        ->get(route('admin.payroll', ['month' => payrollPeriod()]));

    $response->assertOk()
        ->assertSee('Aucun bulletin pour')
        ->assertSee('Générer les bulletins')
        ->assertSee('Aucun bulletin sélectionné')
        ->assertDontSee('Journal CSV')
        ->assertViewHas('payslipCount', 0)
        ->assertViewHas('missingCount', 1);
});

it('generates only missing payslips and is idempotent', function () {
    $admin = payrollAdminUser();
    $returning = payrollEmployee(['name' => 'Retour Ancien']);
    $newcomer = payrollEmployee(['name' => 'Nouveau Collaborateur', 'department' => 'it']);
    $inactive = payrollEmployee(['name' => 'Inactif', 'status' => 'inactif']);
    $previousPeriod = now('Africa/Tunis')->subMonthNoOverflow()->format('Y-m');

    payrollPayslip($returning, 1500000, 120000, 'paid', $previousPeriod);

    $this->withCookies(tokenCookiesFor($admin))
        ->post(route('admin.payroll.generate'), ['period' => payrollPeriod()])
        ->assertRedirect(route('admin.payroll', ['month' => payrollPeriod()]))
        ->assertSessionHas('success', fn (string $message) => str_contains($message, '2 bulletin(s) généré(s)'));

    $returningSlip = Payslip::query()
        ->where('employee_id', $returning->id)
        ->where('period', payrollPeriod())
        ->firstOrFail();
    $newcomerSlip = Payslip::query()
        ->where('employee_id', $newcomer->id)
        ->where('period', payrollPeriod())
        ->firstOrFail();

    expect($returningSlip->base_salary)->toBe('1500.000')
        ->and($returningSlip->prime)->toBe('120.000')
        ->and($returningSlip->status)->toBe('ready')
        ->and($newcomerSlip->base_salary)->toBe('3400.000')
        ->and($newcomerSlip->prime)->toBe('0.000')
        ->and(Payslip::query()->where('employee_id', $inactive->id)->exists())->toBeFalse();

    $this->withCookies(tokenCookiesFor($admin))
        ->post(route('admin.payroll.generate'), ['period' => payrollPeriod()])
        ->assertRedirect(route('admin.payroll', ['month' => payrollPeriod()]))
        ->assertSessionHas('success', fn (string $message) => str_contains($message, 'déjà générés'));

    expect(Payslip::query()->where('period', payrollPeriod())->count())->toBe(2);
});

it('refuses to close an incomplete or empty cycle', function () {
    $admin = payrollAdminUser();
    $alpha = payrollEmployee(['name' => 'Alpha Martin']);
    $beta = payrollEmployee(['name' => 'Beta Sonia']);

    $this->withCookies(tokenCookiesFor($admin))
        ->post(route('admin.payroll.close'), ['period' => payrollPeriod()])
        ->assertSessionHas('error', fn (string $message) => str_contains($message, 'Aucun bulletin'));

    $alphaSlip = payrollPayslip($alpha, 1000000);

    $this->withCookies(tokenCookiesFor($admin))
        ->post(route('admin.payroll.close'), ['period' => payrollPeriod()])
        ->assertSessionHas('error', fn (string $message) => str_contains($message, 'Clôture impossible : 1'));

    expect($alphaSlip->fresh()->status)->toBe('ready');

    $payrollSlip = payrollPayslip($beta, 2000000);

    $this->withCookies(tokenCookiesFor($admin))
        ->post(route('admin.payroll.close'), ['period' => payrollPeriod()])
        ->assertSessionHas('success', fn (string $message) => str_contains($message, '2 bulletin(s) marqué(s) payé(s)'));

    expect($alphaSlip->fresh()->status)->toBe('paid')
        ->and($alphaSlip->fresh()->paid_at)->not->toBeNull()
        ->and($payrollSlip->fresh()->status)->toBe('paid');

    $this->withCookies(tokenCookiesFor($admin))
        ->post(route('admin.payroll.close'), ['period' => payrollPeriod()])
        ->assertSessionHas('error', fn (string $message) => str_contains($message, 'déjà clôturé'));
});

it('exports a formula-safe payroll journal', function () {
    $admin = payrollAdminUser();
    $employee = payrollEmployee(['name' => '=CMD', 'cin' => '=1+1', 'cnss_number' => '@x']);
    payrollPayslip($employee, 1000000);

    $response = $this->withCookies(tokenCookiesFor($admin))
        ->get(route('admin.payroll.export.journal', ['month' => payrollPeriod()]));

    $response->assertOk()
        ->assertDownload('journal-paie-'.payrollPeriod().'.csv');

    $content = $response->streamedContent();

    expect($content)->toStartWith("\xEF\xBB\xBF")
        ->and($content)->toContain('Collaborateur;Matricule;CIN')
        ->and($content)->toContain("'=CMD")
        ->and($content)->toContain("'=1+1")
        ->and($content)->toContain("'@x")
        ->and($content)->toContain('1000,000')
        ->and($content)->toContain('CNSS salariale (9,18 %)');

    $this->withCookies(tokenCookiesFor($admin))
        ->get(route('admin.payroll.export.journal', ['month' => payrollPeriod(), 'q' => 'introuvable']))
        ->assertRedirect(route('admin.payroll', ['month' => payrollPeriod()]))
        ->assertSessionHas('error', fn (string $message) => str_contains($message, 'Aucun bulletin'));
});

it('exports cnss contributions with database totals', function () {
    $admin = payrollAdminUser();
    $alpha = payrollEmployee();
    $beta = payrollEmployee();
    payrollPayslip($alpha, 1000000);
    payrollPayslip($beta, 2000000);

    $response = $this->withCookies(tokenCookiesFor($admin))
        ->get(route('admin.payroll.export.cnss', ['month' => payrollPeriod()]));

    $response->assertOk()
        ->assertDownload('cnss-'.payrollPeriod().'.csv');

    $content = $response->streamedContent();

    expect($content)->toStartWith("\xEF\xBB\xBF")
        ->and($content)->toContain('CNSS salariale (9,18 %)')
        ->and($content)->toContain('CNSS patronale (17,37 %)')
        ->and($content)->toContain('TOTAL')
        ->and($content)->toContain('3000,000')
        ->and($content)->toContain('521,100');
});

it('exports only ready transfers with a usable rib', function () {
    $admin = payrollAdminUser();
    $ready = payrollEmployee(['name' => 'Pret Virement']);
    $noRib = payrollEmployee(['name' => 'Sans Rib']);
    $paid = payrollEmployee(['name' => 'Deja Paye']);

    payrollPayslip($ready, 1000000);
    payrollPayslip($noRib, 1000000)->update(['rib' => '—']);
    payrollPayslip($paid, 1000000, 0, 'paid');

    $response = $this->withCookies(tokenCookiesFor($admin))
        ->get(route('admin.payroll.export.bank', ['month' => payrollPeriod()]));

    $response->assertOk()
        ->assertDownload('virements-'.payrollPeriod().'.csv');

    $content = $response->streamedContent();

    expect($content)->toContain('Pret Virement')
        ->and($content)->toContain('979.653')
        ->and($content)->toContain('PAIE-'.payrollPeriod().'-MAT-')
        ->and($content)->not->toContain('Sans Rib')
        ->and($content)->not->toContain('Deja Paye');
});

it('reports when no transfer is ready', function () {
    $admin = payrollAdminUser();
    $paid = payrollEmployee(['name' => 'Deja Paye']);
    payrollPayslip($paid, 1000000, 0, 'paid');

    $this->withCookies(tokenCookiesFor($admin))
        ->get(route('admin.payroll.export.bank', ['month' => payrollPeriod()]))
        ->assertRedirect(route('admin.payroll', ['month' => payrollPeriod()]))
        ->assertSessionHas('error', fn (string $message) => str_contains($message, 'Aucun virement'));
});

it('downloads a neutral payslip pdf backed by database amounts', function () {
    $admin = payrollAdminUser();
    $employee = payrollEmployee(['name' => 'Nadia Karray']);
    $payslip = payrollPayslip($employee, 1000000);

    $response = $this->withCookies(tokenCookiesFor($admin))
        ->get(route('admin.payroll.payslip', $payslip));

    $response->assertOk()
        ->assertHeader('Content-Type', 'application/pdf');

    $content = $response->getContent();

    expect($content)->toStartWith('%PDF')
        ->and($content)->toContain('Carthage Tech Solutions SARL')
        ->and($content)->toContain('979,653')
        ->and($content)->not->toContain('Conforme au Code du Travail')
        ->and($content)->not->toContain('SIGNE ELECTRONIQUEMENT')
        ->and($content)->not->toContain('Aucune signature manuscrite');
});

it('keeps the payroll page query count constant as payslips grow', function () {
    $admin = payrollAdminUser();
    $employee = payrollEmployee();
    payrollPayslip($employee, 1000000);

    DB::enableQueryLog();
    DB::flushQueryLog();

    $this->withCookies(tokenCookiesFor($admin))
        ->get(route('admin.payroll', ['month' => payrollPeriod()]))
        ->assertOk();

    $smallCount = count(DB::getQueryLog());

    for ($index = 0; $index < 20; $index++) {
        $other = payrollEmployee();
        payrollPayslip($other, 1000000);
    }

    DB::flushQueryLog();

    $this->withCookies(tokenCookiesFor($admin))
        ->get(route('admin.payroll', ['month' => payrollPeriod()]))
        ->assertOk();

    $largeCount = count(DB::getQueryLog());

    expect($largeCount)->toBe($smallCount);

    DB::disableQueryLog();
});

it('guards payroll routes with the admin login', function () {
    $this->get(route('admin.payroll'))->assertRedirect(route('login'));
    $this->post(route('admin.payroll.generate'), ['period' => payrollPeriod()])
        ->assertRedirect(route('login'));
});
