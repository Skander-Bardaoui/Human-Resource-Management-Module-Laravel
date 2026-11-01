<?php

namespace Database\Factories;

use App\Models\Employee;
use App\Models\Payslip;
use App\Support\Money;
use App\Support\PayrollCalculator;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payslip>
 */
class PayslipFactory extends Factory
{
    protected $model = Payslip::class;

    public function definition(): array
    {
        $base = Money::fromMillimes($this->faker->numberBetween(800, 4000) * 1000);
        $prime = Money::fromMillimes($this->faker->numberBetween(0, 500) * 1000);
        $profile = new Employee([
            'family_status' => 'celibataire',
            'children' => 0,
        ]);
        $amounts = PayrollCalculator::forEmployee($profile, $base, $prime);

        return [
            'employee_id' => Employee::factory(),
            'period' => now((string) config('payroll.timezone', 'Africa/Tunis'))->format('Y-m'),
            'base_salary' => $amounts['base_salary']->toDecimalString(),
            'prime' => $amounts['prime']->toDecimalString(),
            'transport_allowance' => $amounts['transport_allowance']->toDecimalString(),
            'cnss' => $amounts['cnss']->toDecimalString(),
            'taxable_base' => $amounts['taxable_base']->toDecimalString(),
            'irpp' => $amounts['irpp']->toDecimalString(),
            'css' => $amounts['css']->toDecimalString(),
            'net' => $amounts['net']->toDecimalString(),
            'status' => 'ready',
            'paid_at' => null,
            'bank' => 'BIAT',
            'rib' => $this->faker->numerify('## ### ########### ##'),
        ];
    }

    public function forPeriod(string $period): static
    {
        return $this->state(fn () => ['period' => $period]);
    }

    public function paid(): static
    {
        return $this->state(fn () => [
            'status' => 'paid',
            'paid_at' => Carbon::now((string) config('payroll.timezone', 'Africa/Tunis')),
        ]);
    }

    public function review(): static
    {
        return $this->state(fn () => [
            'status' => 'review',
            'paid_at' => null,
        ]);
    }
}
