<?php

declare(strict_types=1);

namespace App\Support;

use InvalidArgumentException;

final class Money
{
    private function __construct(private readonly int $millimes)
    {
    }

    public static function zero(): self
    {
        return new self(0);
    }

    public static function fromMillimes(int $millimes): self
    {
        return new self($millimes);
    }

    public static function fromDatabase(int|float|string|null $value): self
    {
        if ($value === null || $value === '') {
            return self::zero();
        }

        if (is_int($value)) {
            return new self($value * 1000);
        }

        if (is_float($value)) {
            return self::fromDatabase(number_format($value, 3, '.', ''));
        }

        $value = trim($value);

        if (preg_match('/^(-?)(\d+)(?:\.(\d{1,3}))?$/', $value, $matches) !== 1) {
            throw new InvalidArgumentException("Montant invalide : {$value}");
        }

        $millimes = ((int) $matches[2]) * 1000
            + (int) str_pad($matches[3] ?? '', 3, '0', STR_PAD_RIGHT);

        return new self($matches[1] === '-' ? -$millimes : $millimes);
    }

    public static function sum(iterable $amounts): self
    {
        $total = 0;

        foreach ($amounts as $amount) {
            if (! $amount instanceof self) {
                throw new InvalidArgumentException('Somme de montants invalide.');
            }

            $total += $amount->millimes;
        }

        return new self($total);
    }

    public function millimes(): int
    {
        return $this->millimes;
    }

    public function plus(self $amount): self
    {
        return new self($this->millimes + $amount->millimes);
    }

    public function minus(self $amount): self
    {
        return new self($this->millimes - $amount->millimes);
    }

    public function isZero(): bool
    {
        return $this->millimes === 0;
    }

    public function isPositive(): bool
    {
        return $this->millimes > 0;
    }

    public function isNegative(): bool
    {
        return $this->millimes < 0;
    }

    public function greaterThan(self $amount): bool
    {
        return $this->millimes > $amount->millimes;
    }

    public function greaterThanOrEqual(self $amount): bool
    {
        return $this->millimes >= $amount->millimes;
    }

    public function lessThan(self $amount): bool
    {
        return $this->millimes < $amount->millimes;
    }

    public function equals(self $amount): bool
    {
        return $this->millimes === $amount->millimes;
    }

    public function abs(): self
    {
        return new self(abs($this->millimes));
    }

    public function format(int $decimals = 3, bool $signed = false): string
    {
        $decimals = max(0, min(3, $decimals));
        $factor = 10 ** (3 - $decimals);
        $units = intdiv(abs($this->millimes) + intdiv($factor, 2), $factor);

        if ($this->millimes < 0) {
            $units = -$units;
        }

        $negative = $units < 0;
        $absolute = abs($units);
        $divisor = 10 ** $decimals;
        $whole = intdiv($absolute, $divisor);
        $fraction = $absolute % $divisor;
        $formatted = self::groupThousands($whole);

        if ($decimals > 0) {
            $formatted .= ','.str_pad((string) $fraction, $decimals, '0', STR_PAD_LEFT);
        }

        $prefix = $negative ? '-' : ($signed && $units > 0 ? '+' : '');

        return $prefix.$formatted;
    }

    public function toCsv(int $decimals = 3): string
    {
        return str_replace(' ', '', $this->format($decimals));
    }

    public function toDecimalString(): string
    {
        $absolute = abs($this->millimes);
        $whole = intdiv($absolute, 1000);
        $fraction = $absolute % 1000;
        $prefix = $this->millimes < 0 ? '-' : '';

        return $prefix.$whole.'.'.str_pad((string) $fraction, 3, '0', STR_PAD_LEFT);
    }

    public function percentageOf(self $base): ?int
    {
        if ($base->millimes <= 0) {
            return null;
        }

        $numerator = $this->millimes * 100;
        $denominator = $base->millimes;

        if ($numerator >= 0) {
            return intdiv($numerator + intdiv($denominator, 2), $denominator);
        }

        return -intdiv(-$numerator + intdiv($denominator, 2), $denominator);
    }

    private static function groupThousands(int $value): string
    {
        $digits = (string) $value;
        $groups = [];

        while (strlen($digits) > 3) {
            array_unshift($groups, substr($digits, -3));
            $digits = substr($digits, 0, -3);
        }

        array_unshift($groups, $digits);

        return implode(' ', $groups);
    }
}
