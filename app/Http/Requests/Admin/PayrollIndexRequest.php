<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use App\Models\Employee;
use App\Models\Payslip;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PayrollIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $month = $this->query('month');

        if ($month !== null && $month !== '' && (! is_string($month) || ! $this->isValidPeriod($month))) {
            $this->query->remove('month');
        }

        $view = $this->query('view');

        if ($view !== null && $view !== '' && (! is_scalar($view) || filter_var((string) $view, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) === false)) {
            $this->query->remove('view');
        }

        $department = $this->query('department');

        if ($department !== null && $department !== '' && (! is_string($department) || ! array_key_exists($department, Employee::DEPARTMENTS))) {
            $this->query->remove('department');
        }

        $status = $this->query('status');

        if ($status !== null && $status !== '' && (! is_string($status) || ! array_key_exists($status, Payslip::STATUSES))) {
            $this->query->remove('status');
        }

        $term = $this->query('q');

        if ($term !== null && ! is_string($term)) {
            $this->query->remove('q');
        } elseif (is_string($term) && mb_strlen($term) > 100) {
            $this->query->set('q', mb_substr($term, 0, 100));
        }
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'month' => ['nullable', 'date_format:Y-m'],
            'view' => ['nullable', 'integer', 'min:1'],
            'q' => ['nullable', 'string', 'max:100'],
            'department' => ['nullable', Rule::in(array_keys(Employee::DEPARTMENTS))],
            'status' => ['nullable', Rule::in(array_keys(Payslip::STATUSES))],
        ];
    }

    /**
     * @return array{q: string, department: string, status: string}
     */
    public function filters(): array
    {
        $validated = $this->validated();
        $department = (string) ($validated['department'] ?? '');
        $status = (string) ($validated['status'] ?? '');

        return [
            'q' => trim((string) ($validated['q'] ?? '')),
            'department' => array_key_exists($department, Employee::DEPARTMENTS) ? $department : '',
            'status' => array_key_exists($status, Payslip::STATUSES) ? $status : '',
        ];
    }

    public function period(): string
    {
        $month = $this->validated('month');

        return is_string($month) && $this->isValidPeriod($month)
            ? $month
            : Carbon::now((string) config('payroll.timezone', 'Africa/Tunis'))->format('Y-m');
    }

    public function viewId(): ?int
    {
        $view = $this->validated('view');

        return $view !== null ? (int) $view : null;
    }

    public function messages(): array
    {
        return [
            'month.date_format' => 'Le mois sélectionné est invalide.',
            'view.integer' => 'Le bulletin sélectionné est invalide.',
            'q.max' => 'La recherche ne peut pas dépasser 100 caractères.',
            'department.in' => 'Le département sélectionné est invalide.',
            'status.in' => 'Le statut sélectionné est invalide.',
        ];
    }

    private function isValidPeriod(string $value): bool
    {
        return preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $value) === 1;
    }
}
