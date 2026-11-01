<?php

declare(strict_types=1);

namespace App\Data;

use App\Enums\BudgetStatus;
use App\Models\Department;
use App\Models\Employee;
use App\Support\Money;
use Illuminate\Support\Collection;

final class DepartmentSummary
{
    /**
     * @param  Collection<int, Employee>  $avatars
     */
    public function __construct(
        public readonly Department $department,
        public readonly int $headcount,
        public readonly Collection $avatars,
        public readonly Money $gross,
        public readonly Money $budget,
        public readonly BudgetStatus $budgetStatus,
        public readonly ?int $usagePercent,
    ) {
    }

    public function variance(): ?Money
    {
        if ($this->budgetStatus === BudgetStatus::Undefined) {
            return null;
        }

        return $this->budget->minus($this->gross);
    }

    public function canViewTeam(): bool
    {
        return array_key_exists($this->department->slug, Employee::DEPARTMENTS);
    }
}
