<?php

declare(strict_types=1);

namespace App\Services;

use App\Data\DepartmentSummary;
use App\Enums\BudgetStatus;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Payslip;
use App\Support\Money;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

final class DepartmentService
{
    public function paginate(array $filters, string $period, int $perPage = 9): LengthAwarePaginator
    {
        $departments = $this->query($filters)->paginate($perPage)->withQueryString();
        $grossBySlug = $this->grossByDepartment($period);
        $avatars = $this->avatarsFor($departments->getCollection()->pluck('slug')->all());

        $departments->through(
            fn (Department $department) => $this->summary($department, $grossBySlug, $avatars)
        );

        return $departments;
    }

    /**
     * @return Collection<int, DepartmentSummary>
     */
    public function summaries(array $filters, string $period): Collection
    {
        $departments = $this->query($filters)->get();
        $grossBySlug = $this->grossByDepartment($period);

        return $departments->map(
            fn (Department $department) => $this->summary($department, $grossBySlug, [])
        );
    }

    /**
     * @return array{
     *     departmentCount: int,
     *     managerCount: int,
     *     activeEmployees: int,
     *     attachedHeadcount: int,
     *     unassigned: int,
     *     totalGross: Money,
     *     totalBudget: Money,
     *     definedBudgets: int,
     *     conforming: int,
     *     conformityRate: int|null,
     *     payslipCount: int
     * }
     */
    public function totals(string $period): array
    {
        $departments = Department::query()
            ->withCount('activeEmployees')
            ->get(['id', 'slug', 'name', 'manager_id', 'monthly_budget']);
        $grossBySlug = $this->grossByDepartment($period);
        $activeEmployees = Employee::query()->where('status', '!=', 'inactif')->count();
        $attachedHeadcount = (int) $departments->sum('active_employees_count');
        $definedBudgets = 0;
        $conforming = 0;

        foreach ($departments as $department) {
            $budget = Money::fromDatabase($department->monthly_budget);

            if ($budget->isZero()) {
                continue;
            }

            $definedBudgets++;

            if (! ($grossBySlug[$department->slug] ?? Money::zero())->greaterThan($budget)) {
                $conforming++;
            }
        }

        return [
            'departmentCount' => $departments->count(),
            'managerCount' => $departments->whereNotNull('manager_id')->count(),
            'activeEmployees' => $activeEmployees,
            'attachedHeadcount' => $attachedHeadcount,
            'unassigned' => max(0, $activeEmployees - $attachedHeadcount),
            'totalGross' => Money::sum(
                $departments->map(fn (Department $department) => $grossBySlug[$department->slug] ?? Money::zero())
            ),
            'totalBudget' => Money::sum(
                $departments->map(fn (Department $department) => Money::fromDatabase($department->monthly_budget))
            ),
            'definedBudgets' => $definedBudgets,
            'conforming' => $conforming,
            'conformityRate' => $definedBudgets > 0
                ? (int) round($conforming / $definedBudgets * 100)
                : null,
            'payslipCount' => Payslip::query()->where('period', $period)->count(),
        ];
    }

    public function create(array $data): Department
    {
        return Department::create([
            'slug' => $this->uniqueSlug($data['name']),
            'name' => trim($data['name']),
            'description' => filled($data['description'] ?? null) ? trim($data['description']) : null,
            'manager_id' => $data['manager_id'] ?? null,
            'code' => filled($data['code'] ?? null) ? trim($data['code']) : null,
            'monthly_budget' => $data['monthly_budget'] ?? 0,
        ]);
    }

    public function update(Department $department, array $data): void
    {
        $payload = ['name' => trim($data['name'])];

        if (array_key_exists('description', $data)) {
            $payload['description'] = filled($data['description']) ? trim($data['description']) : null;
        }

        if (array_key_exists('manager_id', $data)) {
            $payload['manager_id'] = $data['manager_id'];
        }

        if (array_key_exists('code', $data)) {
            $payload['code'] = filled($data['code']) ? trim($data['code']) : null;
        }

        if (array_key_exists('monthly_budget', $data)) {
            $payload['monthly_budget'] = $data['monthly_budget'] ?? 0;
        }

        $department->update($payload);
    }

    private function query(array $filters): Builder
    {
        $query = Department::query()
            ->with('manager:id,name,email,position,avatar_url,status')
            ->withCount(['activeEmployees', 'employees']);

        $term = trim((string) ($filters['q'] ?? ''));

        if ($term !== '') {
            $query->search($term);
        }

        $sort = $filters['sort'] ?? 'headcount';
        $direction = in_array($filters['direction'] ?? null, ['asc', 'desc'], true)
            ? $filters['direction']
            : 'desc';

        match ($sort) {
            'name' => $query->orderBy('name', $direction),
            'budget' => $query->orderBy('monthly_budget', $direction),
            default => $query->orderBy('active_employees_count', $direction),
        };

        if ($sort !== 'name') {
            $query->orderBy('name');
        }

        return $query;
    }

    /**
     * @return array<string, Money>
     */
    private function grossByDepartment(string $period): array
    {
        return Payslip::query()
            ->join('employees', 'employees.id', '=', 'payslips.employee_id')
            ->where('payslips.period', $period)
            ->where('employees.status', '!=', 'inactif')
            ->selectRaw('employees.department as department_key, SUM(payslips.base_salary + payslips.prime) as gross')
            ->groupBy('employees.department')
            ->pluck('gross', 'department_key')
            ->map(fn ($gross) => Money::fromDatabase($gross))
            ->all();
    }

    /**
     * @param  array<string, Money>  $grossBySlug
     * @param  array<string, Collection<int, Employee>>  $avatars
     */
    private function summary(Department $department, array $grossBySlug, array $avatars): DepartmentSummary
    {
        $gross = $grossBySlug[$department->slug] ?? Money::zero();
        $budget = Money::fromDatabase($department->monthly_budget);
        $status = BudgetStatus::fromAmounts($gross, $budget);

        return new DepartmentSummary(
            department: $department,
            headcount: (int) $department->active_employees_count,
            avatars: collect($avatars[$department->slug] ?? []),
            gross: $gross,
            budget: $budget,
            budgetStatus: $status,
            usagePercent: $status === BudgetStatus::Undefined ? null : $gross->percentageOf($budget),
        );
    }

    /**
     * @param  array<int, string>  $slugs
     * @return array<string, Collection<int, Employee>>
     */
    private function avatarsFor(array $slugs): array
    {
        if ($slugs === []) {
            return [];
        }

        return Employee::query()
            ->whereIn('department', $slugs)
            ->where('status', '!=', 'inactif')
            ->orderBy('name')
            ->get(['id', 'name', 'avatar_url', 'department'])
            ->groupBy('department')
            ->map(fn (Collection $employees) => $employees->take(3)->values())
            ->all();
    }

    private function uniqueSlug(string $name): string
    {
        $base = Str::slug($name) ?: 'departement';
        $slug = $base;
        $suffix = 2;

        while (Department::query()->where('slug', $slug)->exists()) {
            $slug = $base.'-'.$suffix;
            $suffix++;
        }

        return $slug;
    }
}
