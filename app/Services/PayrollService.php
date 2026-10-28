<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Employee;
use App\Models\Payslip;
use App\Support\BankAccount;
use App\Support\CsvExport;
use App\Support\FrenchNumber;
use App\Support\Money;
use App\Support\PayrollCalculator;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

final class PayrollService
{
    /**
     * @param  array{q: string, department: string, status: string}  $filters
     * @return array<string, mixed>
     */
    public function overview(string $period, array $filters, ?int $viewId): array
    {
        $periodDate = $this->periodDate($period);
        $periodPayslips = Payslip::query()
            ->with('employee:id,name,email,position,department,avatar_url,status,cin,cnss_number,family_status,children')
            ->forPeriod($period)
            ->get();

        $payslips = $this->filteredQuery($period, $filters)
            ->orderBy(Employee::select('name')->whereColumn('employees.id', 'payslips.employee_id'))
            ->paginate(8)
            ->withQueryString();

        $selectedPayslip = $viewId !== null
            ? $periodPayslips->firstWhere('id', $viewId)
            : null;
        $selectedPayslip ??= $payslips->first();

        $activeIds = Employee::query()
            ->where('status', '!=', 'inactif')
            ->pluck('id');
        $coveredIds = $periodPayslips->pluck('employee_id');
        $activeEmployees = $activeIds->count();
        $coveredEmployees = $activeIds->intersect($coveredIds)->count();
        $missingCount = $activeIds->diff($coveredIds)->count();

        $grossTotal = Money::sum($periodPayslips->map(fn (Payslip $payslip) => $payslip->gross()));
        $netTotal = Money::sum($periodPayslips->map(fn (Payslip $payslip) => $payslip->netAmount()));
        $cnssTotal = Money::sum($periodPayslips->map(fn (Payslip $payslip) => $payslip->cnssAmount()));
        $employerCnssTotal = Money::sum($periodPayslips->map(fn (Payslip $payslip) => $payslip->employerCnss()));
        $irppTotal = Money::sum($periodPayslips->map(fn (Payslip $payslip) => $payslip->irppAmount()));
        $cssTotal = Money::sum($periodPayslips->map(fn (Payslip $payslip) => $payslip->cssAmount()));

        $previousPeriod = $periodDate->copy()->subMonthNoOverflow()->format('Y-m');
        $previousNet = Money::sum(
            Payslip::query()
                ->forPeriod($previousPeriod)
                ->pluck('net')
                ->map(fn ($value) => Money::fromDatabase($value))
        );
        $growthRate = $previousNet->isPositive()
            ? $netTotal->percentageOf($previousNet)
            : null;

        $readyCount = $periodPayslips->where('status', 'ready')->count();
        $reviewCount = $periodPayslips->where('status', 'review')->count();
        $paidCount = $periodPayslips->where('status', 'paid')->count();
        $payslipCount = $periodPayslips->count();
        $periodClosed = $payslipCount > 0 && $paidCount === $payslipCount;

        $deadlineDay = min(
            max(1, (int) config('payroll.payment_deadline_day', 28)),
            $periodDate->daysInMonth
        );
        $nextMonth = $periodDate->copy()->addMonthNoOverflow();
        $irppDay = min(
            max(1, (int) config('payroll.irpp_declaration_day', 28)),
            $nextMonth->daysInMonth
        );

        return [
            'payslips' => $payslips,
            'selectedPayslip' => $selectedPayslip,
            'selectedPayload' => $selectedPayslip ? $this->payload($selectedPayslip) : null,
            'period' => $period,
            'periodLabel' => ucfirst($periodDate->copy()->locale('fr')->translatedFormat('F Y')),
            'periodStart' => $periodDate,
            'periodEnd' => $periodDate->copy()->endOfMonth(),
            'cycleId' => 'TN-PAY-'.$periodDate->format('Y-m'),
            'deadline' => $periodDate->copy()->day($deadlineDay),
            'irppDeadline' => $nextMonth->day($irppDay),
            'filters' => $filters,
            'grossTotal' => $grossTotal,
            'netTotal' => $netTotal,
            'cnssTotal' => $cnssTotal,
            'employerCnssTotal' => $employerCnssTotal,
            'irppTotal' => $irppTotal,
            'cssTotal' => $cssTotal,
            'growthRate' => $growthRate,
            'previousMonthShort' => ucfirst($periodDate->copy()->subMonthNoOverflow()->locale('fr')->translatedFormat('M')),
            'activeEmployees' => $activeEmployees,
            'coveredEmployees' => $coveredEmployees,
            'missingCount' => $missingCount,
            'coverageRate' => $activeEmployees > 0
                ? (int) round($coveredEmployees / $activeEmployees * 100)
                : null,
            'readyCount' => $readyCount,
            'reviewCount' => $reviewCount,
            'paidCount' => $paidCount,
            'payslipCount' => $payslipCount,
            'periodClosed' => $periodClosed,
            'version' => (string) config('payroll.version', '—'),
            'company' => [
                'name' => (string) config('payroll.company.name', '—'),
                'cnss' => (string) config('payroll.company.cnss_number', '—'),
            ],
            'rates' => [
                'employeeCnss' => PayrollCalculator::rateLabel((int) config('payroll.cnss.employee_rate_bp', 918)),
                'employerCnss' => PayrollCalculator::rateLabel((int) config('payroll.cnss.employer_rate_bp', 1737)),
                'css' => PayrollCalculator::rateLabel((int) config('payroll.css_rate_bp', 100)),
                'transport' => PayrollCalculator::transportAllowance(),
                'annualDeduction' => Money::fromMillimes((int) config('payroll.irpp.annual_deduction_per_part_millimes', 5000000)),
            ],
            'brackets' => $this->brackets(),
        ];
    }

    /**
     * @return array{created: int, skipped: int, employees: int}
     */
    public function generate(string $period): array
    {
        return DB::transaction(function () use ($period) {
            $employees = Employee::query()
                ->where('status', '!=', 'inactif')
                ->orderBy('id')
                ->get();

            $existing = Payslip::query()
                ->forPeriod($period)
                ->pluck('employee_id');
            $missing = $employees->whereNotIn('id', $existing->all())->values();

            $latest = Payslip::query()
                ->whereIn('employee_id', $missing->pluck('id')->all())
                ->orderByDesc('period')
                ->orderByDesc('id')
                ->get(['employee_id', 'base_salary', 'prime'])
                ->unique('employee_id')
                ->keyBy('employee_id');

            foreach ($missing as $employee) {
                $previous = $latest->get($employee->id);
                $base = $previous
                    ? Money::fromDatabase($previous->base_salary)
                    : $this->defaultBase($employee);
                $prime = $previous
                    ? Money::fromDatabase($previous->prime)
                    : Money::zero();
                $amounts = PayrollCalculator::forEmployee($employee, $base, $prime);
                [$bank, $rib] = BankAccount::parse($employee->bank_account);

                Payslip::create([
                    'employee_id' => $employee->id,
                    'period' => $period,
                    'base_salary' => $amounts['base_salary']->toDecimalString(),
                    'prime' => $amounts['prime']->toDecimalString(),
                    'transport_allowance' => $amounts['transport_allowance']->toDecimalString(),
                    'cnss' => $amounts['cnss']->toDecimalString(),
                    'taxable_base' => $amounts['taxable_base']->toDecimalString(),
                    'irpp' => $amounts['irpp']->toDecimalString(),
                    'css' => $amounts['css']->toDecimalString(),
                    'net' => $amounts['net']->toDecimalString(),
                    'status' => 'ready',
                    'bank' => $bank,
                    'rib' => $rib,
                ]);
            }

            return [
                'created' => $missing->count(),
                'skipped' => $employees->count() - $missing->count(),
                'employees' => $employees->count(),
            ];
        });
    }

    /**
     * @return array{reason: string, updated: int, missing: int}
     */
    public function close(string $period): array
    {
        return DB::transaction(function () use ($period) {
            $payslips = Payslip::query()->forPeriod($period)->get(['employee_id', 'status']);

            if ($payslips->isEmpty()) {
                return ['reason' => 'empty', 'updated' => 0, 'missing' => 0];
            }

            $activeIds = Employee::query()
                ->where('status', '!=', 'inactif')
                ->pluck('id');
            $missing = $activeIds->diff($payslips->pluck('employee_id'))->count();

            if ($missing > 0) {
                return ['reason' => 'missing', 'updated' => 0, 'missing' => $missing];
            }

            if ($payslips->every(fn (Payslip $payslip) => $payslip->status === 'paid')) {
                return ['reason' => 'already', 'updated' => 0, 'missing' => 0];
            }

            $updated = Payslip::query()
                ->forPeriod($period)
                ->where('status', '!=', 'paid')
                ->update([
                    'status' => 'paid',
                    'paid_at' => Carbon::now((string) config('payroll.timezone', 'Africa/Tunis')),
                ]);

            return ['reason' => 'closed', 'updated' => $updated, 'missing' => 0];
        });
    }

    /**
     * @param  array{q: string, department: string, status: string}  $filters
     * @return Collection<int, array<int, string|int>>
     */
    public function journalRows(string $period, array $filters): Collection
    {
        return $this->exportQuery($period, $filters)
            ->get()
            ->map(fn (Payslip $payslip) => [
                CsvExport::text($payslip->employee?->name),
                $payslip->matricule(),
                CsvExport::text($payslip->employee?->cin),
                CsvExport::text($payslip->employee?->cnss_number),
                CsvExport::text($payslip->employee?->departmentLabel()),
                $payslip->baseSalary()->toCsv(),
                $payslip->primeAmount()->toCsv(),
                $payslip->transportAllowance()->toCsv(),
                $payslip->gross()->toCsv(),
                $payslip->cnssAmount()->toCsv(),
                $payslip->taxableBase()->toCsv(),
                $payslip->irppAmount()->toCsv(),
                $payslip->cssAmount()->toCsv(),
                $payslip->netAmount()->toCsv(),
                CsvExport::text($payslip->bank),
                CsvExport::text($payslip->rib),
                CsvExport::text($payslip->statusLabel()),
            ]);
    }

    /**
     * @param  array{q: string, department: string, status: string}  $filters
     * @return Collection<int, array<int, string|int>>
     */
    public function cnssRows(string $period, array $filters): Collection
    {
        $payslips = $this->exportQuery($period, $filters)->get();

        $rows = $payslips->map(fn (Payslip $payslip) => [
            $payslip->matricule(),
            CsvExport::text($payslip->employee?->name),
            CsvExport::text($payslip->employee?->cin),
            CsvExport::text($payslip->employee?->cnss_number),
            $payslip->gross()->toCsv(),
            $payslip->cnssAmount()->toCsv(),
            $payslip->employerCnss()->toCsv(),
            CsvExport::text($payslip->statusLabel()),
        ]);

        $rows->push([
            'TOTAL',
            '',
            '',
            '',
            Money::sum($payslips->map(fn (Payslip $payslip) => $payslip->gross()))->toCsv(),
            Money::sum($payslips->map(fn (Payslip $payslip) => $payslip->cnssAmount()))->toCsv(),
            Money::sum($payslips->map(fn (Payslip $payslip) => $payslip->employerCnss()))->toCsv(),
            '',
        ]);

        return $rows;
    }

    /**
     * @param  array{q: string, department: string, status: string}  $filters
     * @return Collection<int, array<int, string>>
     */
    public function bankRows(string $period, array $filters): Collection
    {
        return $this->exportQuery($period, $filters)
            ->where('status', 'ready')
            ->get()
            ->filter(fn (Payslip $payslip) => BankAccount::isUsable($payslip->rib))
            ->values()
            ->map(fn (Payslip $payslip) => [
                CsvExport::text($payslip->employee?->name),
                $payslip->matricule(),
                CsvExport::text($payslip->bank),
                CsvExport::text($payslip->rib),
                $payslip->netAmount()->toDecimalString(),
                'PAIE-'.$period.'-'.$payslip->matricule(),
            ]);
    }

    public function periodDate(string $period): Carbon
    {
        return Carbon::createFromFormat(
            '!Y-m',
            $period,
            (string) config('payroll.timezone', 'Africa/Tunis')
        )->startOfMonth();
    }

    public function periodLabel(string $period): string
    {
        return ucfirst($this->periodDate($period)->copy()->locale('fr')->translatedFormat('F Y'));
    }

    /**
     * @param  array{q: string, department: string, status: string}  $filters
     */
    public function filteredQuery(string $period, array $filters): Builder
    {
        return Payslip::query()
            ->with('employee')
            ->forPeriod($period)
            ->when($filters['q'] !== '', function (Builder $query) use ($filters) {
                $term = '%'.$filters['q'].'%';

                $query->whereHas('employee', function (Builder $employee) use ($term) {
                    $employee->where('name', 'like', $term)
                        ->orWhere('cin', 'like', $term)
                        ->orWhere('cnss_number', 'like', $term)
                        ->orWhere('email', 'like', $term);
                });
            })
            ->when(
                array_key_exists($filters['department'], Employee::DEPARTMENTS),
                fn (Builder $query) => $query->whereHas(
                    'employee',
                    fn (Builder $employee) => $employee->where('department', $filters['department'])
                )
            )
            ->when(
                array_key_exists($filters['status'], Payslip::STATUSES),
                fn (Builder $query) => $query->where('status', $filters['status'])
            );
    }

    private function exportQuery(string $period, array $filters): Builder
    {
        return $this->filteredQuery($period, $filters)
            ->orderBy(Employee::select('name')->whereColumn('employees.id', 'payslips.employee_id'));
    }

    private function defaultBase(Employee $employee): Money
    {
        $grid = (array) config('payroll.salary_grid_millimes', []);

        return Money::fromMillimes((int) (
            $grid[$employee->department]
            ?? config('payroll.default_base_salary_millimes', 2800000)
        ));
    }

    /**
     * @return array<int, array{label: string, rate: string}>
     */
    private function brackets(): array
    {
        $brackets = [];
        $previous = 0;

        foreach (config('payroll.irpp.brackets', []) as $bracket) {
            $limit = $bracket['up_to_millimes'] ?? null;
            $label = $limit === null
                ? 'Au-delà de '.Money::fromMillimes($previous)->format(0).' TND'
                : ($previous === 0
                    ? "Jusqu'à ".Money::fromMillimes((int) $limit)->format(0).' TND'
                    : 'De '.Money::fromMillimes($previous)->format(0)
                        .' à '.Money::fromMillimes((int) $limit)->format(0).' TND');

            $brackets[] = [
                'label' => $label,
                'rate' => PayrollCalculator::rateLabel((int) $bracket['rate_bp']),
            ];

            if ($limit === null) {
                break;
            }

            $previous = (int) $limit;
        }

        return $brackets;
    }

    /**
     * @return array<string, string|int>
     */
    private function payload(Payslip $payslip): array
    {
        $employee = $payslip->employee;
        $net = $payslip->netAmount();

        return [
            'id' => $payslip->id,
            'name' => $employee?->name ?? 'Collaborateur',
            'matricule' => $payslip->matricule(),
            'cin' => $employee?->cin ?: '—',
            'role' => $employee?->position ?: '—',
            'department' => $employee?->departmentLabel() ?? '—',
            'base' => $payslip->baseSalary()->format(3).' TND',
            'prime' => '+'.$payslip->primeAmount()->format(3).' TND',
            'transport' => '+'.$payslip->transportAllowance()->format(3).' TND',
            'cnss' => '-'.$payslip->cnssAmount()->format(3).' TND',
            'taxBase' => $payslip->taxableBase()->format(3).' TND',
            'irpp' => '-'.$payslip->irppAmount()->format(3).' TND',
            'css' => '-'.$payslip->cssAmount()->format(3).' TND',
            'net' => $net->format(3),
            'words' => FrenchNumber::amountInWords((float) $net->toDecimalString()),
            'bank' => $payslip->bank ?: '—',
            'rib' => $payslip->rib ? 'RIB : '.$payslip->rib : 'RIB : —',
            'downloadUrl' => route('admin.payroll.payslip', $payslip),
        ];
    }
}
