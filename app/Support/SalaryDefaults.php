<?php

namespace App\Support;

use App\Models\Employee;

class SalaryDefaults
{
    public const BASE_SALARIES = [
        'm.bensalem@carthagetech.tn' => 4200,
        'y.trabelsi@carthagetech.tn' => 3600,
        'k.chaabane@carthagetech.tn' => 3100,
        'n.karray@carthagetech.tn' => 3400,
        'm.zghal@carthagetech.tn' => 3200,
        's.mezghani@carthagetech.tn' => 2850,
        'a.mansour@carthagetech.tn' => 3800,
        'f.ghorbel@carthagetech.tn' => 2600,
    ];

    public const PRIMES = [
        'm.bensalem@carthagetech.tn' => 350,
        'y.trabelsi@carthagetech.tn' => 180,
        'k.chaabane@carthagetech.tn' => 600,
        'n.karray@carthagetech.tn' => 250,
        'm.zghal@carthagetech.tn' => 150,
        's.mezghani@carthagetech.tn' => 120,
        'a.mansour@carthagetech.tn' => 400,
        'f.ghorbel@carthagetech.tn' => 200,
    ];

    public static function baseFor(Employee $employee): float
    {
        $email = strtolower((string) $employee->email);

        return (float) (self::BASE_SALARIES[$email] ?? self::defaultBase($employee));
    }

    public static function primeFor(Employee $employee): float
    {
        $email = strtolower((string) $employee->email);

        return (float) (self::PRIMES[$email] ?? 0);
    }

    public static function defaultBase(Employee $employee): int
    {
        return match ($employee->department) {
            'it' => 3400,
            'finance' => 3200,
            'produit' => 3100,
            'commerce' => 2900,
            'rh' => 2800,
            default => 2800,
        };
    }

    public static function bankDetails(?string $account): array
    {
        $account = trim((string) $account);

        if ($account === '' || strtolower($account) === 'non renseigné') {
            return ['—', '—'];
        }

        $parts = preg_split('/\s+/', $account, 2);
        $short = strtoupper($parts[0] ?? '');
        $bank = match ($short) {
            'AMEN' => 'Amen Bank',
            'BIAT' => 'BIAT',
            'UIB' => 'UIB',
            'ATB' => 'ATB',
            'BH' => 'BH Bank',
            'STB' => 'STB',
            default => $parts[0] ?? '—',
        };

        return [$bank, $parts[1] ?? '—'];
    }
}
