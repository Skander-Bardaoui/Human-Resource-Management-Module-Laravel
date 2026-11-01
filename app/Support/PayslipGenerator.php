<?php

namespace App\Support;

use App\Models\Employee;
use App\Models\Payslip;

class PayslipGenerator
{
    public static function ensureFor(Employee $employee, string $period): Payslip
    {
        $existing = Payslip::forEmployee($employee)->where('period', $period)->first();

        if ($existing !== null) {
            return $existing;
        }

        $amounts = PayrollCalculator::forEmployee(
            $employee,
            SalaryDefaults::baseFor($employee),
            SalaryDefaults::primeFor($employee)
        );

        [$bank, $rib] = SalaryDefaults::bankDetails($employee->bank_account);

        return Payslip::create(array_merge($amounts, [
            'employee_id' => $employee->id,
            'period' => $period,
            'status' => 'ready',
            'bank' => $bank,
            'rib' => $rib,
        ]));
    }

    public static function ensureForPeriod(string $period): int
    {
        $created = 0;

        Employee::where('status', '!=', 'inactif')->get()->each(function (Employee $employee) use ($period, &$created) {
            if (Payslip::forEmployee($employee)->where('period', $period)->exists()) {
                return;
            }

            self::ensureFor($employee, $period);
            $created++;
        });

        return $created;
    }
}
