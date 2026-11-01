<?php

use App\Models\Employee;
use App\Support\Money;
use App\Support\PayrollCalculator;

uses(Tests\TestCase::class);

function calculatorEmployee(array $overrides = []): Employee
{
    return new Employee(array_merge([
        'family_status' => 'celibataire',
        'children' => 0,
    ], $overrides));
}

it('computes gross to net in integer millimes', function () {
    $amounts = PayrollCalculator::forEmployee(
        calculatorEmployee(),
        Money::fromMillimes(1000000),
        Money::zero()
    );

    expect($amounts['gross']->millimes())->toBe(1000000)
        ->and($amounts['cnss']->millimes())->toBe(91800)
        ->and($amounts['taxable_base']->millimes())->toBe(908200)
        ->and($amounts['irpp']->millimes())->toBe(19465)
        ->and($amounts['css']->millimes())->toBe(9082)
        ->and($amounts['transport_allowance']->millimes())->toBe(100000)
        ->and($amounts['net']->millimes())->toBe(979653)
        ->and($amounts['employer_cnss']->millimes())->toBe(173700);
});

it('includes the prime in the cotisable base and applies family parts', function () {
    $single = PayrollCalculator::forEmployee(
        calculatorEmployee(),
        Money::fromMillimes(1000000),
        Money::fromMillimes(200000)
    );
    $family = PayrollCalculator::forEmployee(
        calculatorEmployee(['family_status' => 'marie', 'children' => 2]),
        Money::fromMillimes(1000000),
        Money::fromMillimes(200000)
    );

    expect($single['gross']->millimes())->toBe(1200000)
        ->and($single['cnss']->millimes())->toBe(110160)
        ->and($single['taxable_base']->millimes())->toBe(1089840)
        ->and($family['cnss']->millimes())->toBe($single['cnss']->millimes())
        ->and($family['irpp']->millimes())->toBe(0)
        ->and($family['net']->millimes())->toBeGreaterThan($single['net']->millimes());
});

it('applies progressive irpp brackets at their limits', function () {
    expect(PayrollCalculator::monthlyIrpp(Money::fromMillimes(0), 2)->millimes())->toBe(0)
        ->and(PayrollCalculator::monthlyIrpp(Money::fromMillimes(416000), 2)->millimes())->toBe(0)
        ->and(PayrollCalculator::monthlyIrpp(Money::fromMillimes(908200), 2)->millimes())->toBe(19465)
        ->and(PayrollCalculator::monthlyIrpp(Money::fromMillimes(908200), 5)->millimes())->toBe(0);
});

it('derives family half parts from the employee profile', function () {
    expect(PayrollCalculator::familyHalfParts(calculatorEmployee()))->toBe(2)
        ->and(PayrollCalculator::familyHalfParts(calculatorEmployee(['family_status' => 'marie'])))->toBe(3)
        ->and(PayrollCalculator::familyHalfParts(calculatorEmployee(['children' => 2])))->toBe(4)
        ->and(PayrollCalculator::familyHalfParts(calculatorEmployee(['family_status' => 'marie', 'children' => 5])))->toBe(6);
});

it('reads rates and thresholds from the versioned configuration', function () {
    config([
        'payroll.cnss.employee_rate_bp' => 500,
        'payroll.css_rate_bp' => 200,
        'payroll.transport_allowance_millimes' => 50000,
    ]);

    $amounts = PayrollCalculator::forEmployee(
        calculatorEmployee(),
        Money::fromMillimes(1000000),
        Money::zero()
    );

    expect($amounts['cnss']->millimes())->toBe(50000)
        ->and($amounts['css']->millimes())->toBe(19000)
        ->and($amounts['transport_allowance']->millimes())->toBe(50000);
});
