<?php

declare(strict_types=1);

namespace App\Support;

use App\Models\Employee;

final class PayrollCalculator
{
    /**
     * @return array{
     *     base_salary: Money,
     *     prime: Money,
     *     transport_allowance: Money,
     *     gross: Money,
     *     cnss: Money,
     *     employer_cnss: Money,
     *     taxable_base: Money,
     *     irpp: Money,
     *     css: Money,
     *     net: Money
     * }
     */
    public static function forEmployee(Employee $employee, Money $base, Money $prime): array
    {
        $gross = $base->plus($prime);
        $cnss = self::employeeCnss($gross);
        $taxable = $gross->minus($cnss);
        $irpp = self::monthlyIrpp($taxable, self::familyHalfParts($employee));
        $css = self::css($taxable);
        $transport = self::transportAllowance();
        $net = $gross
            ->plus($transport)
            ->minus($cnss)
            ->minus($irpp)
            ->minus($css);

        return [
            'base_salary' => $base,
            'prime' => $prime,
            'transport_allowance' => $transport,
            'gross' => $gross,
            'cnss' => $cnss,
            'employer_cnss' => self::employerCnss($gross),
            'taxable_base' => $taxable,
            'irpp' => $irpp,
            'css' => $css,
            'net' => $net,
        ];
    }

    public static function employeeCnss(Money $gross): Money
    {
        return Money::fromMillimes(self::applyRate(
            $gross->millimes(),
            (int) config('payroll.cnss.employee_rate_bp', 918)
        ));
    }

    public static function employerCnss(Money $gross): Money
    {
        return Money::fromMillimes(self::applyRate(
            $gross->millimes(),
            (int) config('payroll.cnss.employer_rate_bp', 1737)
        ));
    }

    public static function css(Money $taxable): Money
    {
        return Money::fromMillimes(self::applyRate(
            $taxable->millimes(),
            (int) config('payroll.css_rate_bp', 100)
        ));
    }

    public static function transportAllowance(): Money
    {
        return Money::fromMillimes((int) config('payroll.transport_allowance_millimes', 100000));
    }

    public static function monthlyIrpp(Money $monthlyTaxable, int $halfParts = 2): Money
    {
        $deductionPerPart = (int) config('payroll.irpp.annual_deduction_per_part_millimes', 5000000);
        $annualDeduction = intdiv($deductionPerPart * max(0, $halfParts), 2);
        $annual = max(0, $monthlyTaxable->millimes() * 12 - $annualDeduction);
        $numerator = 0;
        $previous = 0;

        foreach (config('payroll.irpp.brackets', []) as $bracket) {
            $limit = $bracket['up_to_millimes'] ?? null;
            $ceiling = $limit === null ? $annual : min($annual, (int) $limit);
            $slice = max(0, $ceiling - $previous);

            if ($slice > 0) {
                $numerator += $slice * (int) $bracket['rate_bp'];
            }

            if ($limit === null || $annual <= (int) $limit) {
                break;
            }

            $previous = (int) $limit;
        }

        $annualTax = intdiv($numerator + 5000, 10000);

        return Money::fromMillimes(intdiv($annualTax + 6, 12));
    }

    public static function familyHalfParts(Employee $employee): int
    {
        $halfParts = 2;

        if ($employee->family_status === 'marie') {
            $halfParts += (int) config('payroll.family.spouse_half_parts', 1);
        }

        $children = min(
            (int) config('payroll.family.max_child_half_parts', 3),
            max(0, (int) $employee->children) * (int) config('payroll.family.child_half_parts', 1)
        );

        return $halfParts + $children;
    }

    public static function rateLabel(int $basisPoints): string
    {
        return number_format($basisPoints / 100, 2, ',', ' ').' %';
    }

    private static function applyRate(int $amount, int $basisPoints): int
    {
        if ($amount <= 0 || $basisPoints <= 0) {
            return 0;
        }

        return intdiv($amount * $basisPoints + 5000, 10000);
    }
}
