<?php

use App\Support\Money;

it('parses database amounts into integer millimes', function () {
    expect(Money::fromDatabase('1234.567')->millimes())->toBe(1234567)
        ->and(Money::fromDatabase('12.5')->millimes())->toBe(12500)
        ->and(Money::fromDatabase('12')->millimes())->toBe(12000)
        ->and(Money::fromDatabase('0.050')->millimes())->toBe(50)
        ->and(Money::fromDatabase('-12.500')->millimes())->toBe(-12500)
        ->and(Money::fromDatabase(1500)->millimes())->toBe(1500000)
        ->and(Money::fromDatabase(1500.125)->millimes())->toBe(1500125)
        ->and(Money::fromDatabase(null)->millimes())->toBe(0)
        ->and(Money::fromDatabase('')->millimes())->toBe(0);
});

it('rejects malformed monetary input', function () {
    expect(fn () => Money::fromDatabase('12,5'))->toThrow(InvalidArgumentException::class)
        ->and(fn () => Money::fromDatabase('abc'))->toThrow(InvalidArgumentException::class);
});

it('formats amounts in French notation without floats', function () {
    $amount = Money::fromMillimes(1234567);

    expect($amount->format(3))->toBe('1 234,567')
        ->and($amount->format(2))->toBe('1 234,57')
        ->and($amount->format(1))->toBe('1 234,6')
        ->and($amount->format(0))->toBe('1 235')
        ->and($amount->format(3, true))->toBe('+1 234,567')
        ->and(Money::fromMillimes(-1234567)->format(3))->toBe('-1 234,567')
        ->and(Money::fromMillimes(50)->format(3))->toBe('0,050')
        ->and(Money::fromMillimes(1000000000)->format(3))->toBe('1 000 000,000');
});

it('exports decimal strings and csv-safe amounts', function () {
    expect(Money::fromMillimes(1234567)->toDecimalString())->toBe('1234.567')
        ->and(Money::fromMillimes(50)->toDecimalString())->toBe('0.050')
        ->and(Money::fromMillimes(-50)->toDecimalString())->toBe('-0.050')
        ->and(Money::fromMillimes(1234567)->toCsv())->toBe('1234,567')
        ->and(Money::fromMillimes(-1234500)->toCsv())->toBe('-1234,500');
});

it('performs exact arithmetic and comparisons', function () {
    $ten = Money::fromDatabase('10.500');
    $two = Money::fromDatabase('2.250');

    expect($ten->plus($two)->toDecimalString())->toBe('12.750')
        ->and($ten->minus($two)->toDecimalString())->toBe('8.250')
        ->and($ten->minus(Money::fromDatabase('12.750'))->isNegative())->toBeTrue()
        ->and($ten->minus(Money::fromDatabase('12.750'))->abs()->toDecimalString())->toBe('2.250')
        ->and(Money::sum([$ten, $two, Money::zero()])->toDecimalString())->toBe('12.750')
        ->and(Money::sum([])->isZero())->toBeTrue()
        ->and($ten->greaterThan($two))->toBeTrue()
        ->and($ten->greaterThanOrEqual($ten))->toBeTrue()
        ->and($two->lessThan($ten))->toBeTrue()
        ->and($ten->equals(Money::fromDatabase('10.500')))->toBeTrue()
        ->and($ten->isPositive())->toBeTrue();
});

it('computes rounded usage percentages', function () {
    expect(Money::fromDatabase('150')->percentageOf(Money::fromDatabase('100')))->toBe(150)
        ->and(Money::fromDatabase('33.333')->percentageOf(Money::fromDatabase('100')))->toBe(33)
        ->and(Money::fromDatabase('0')->percentageOf(Money::fromDatabase('100')))->toBe(0)
        ->and(Money::fromDatabase('-50')->percentageOf(Money::fromDatabase('100')))->toBe(-50)
        ->and(Money::fromDatabase('50')->percentageOf(Money::zero()))->toBeNull();
});
