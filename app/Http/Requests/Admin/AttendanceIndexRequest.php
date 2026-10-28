<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use App\Models\AttendanceRecord;
use App\Models\Employee;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AttendanceIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $date = $this->query('date');

        if (is_string($date) && $date !== '' && ! $this->isValidDate($date)) {
            $this->query->remove('date');
        }

        $employee = $this->query('employee');

        if ($employee !== null && $employee !== '' && (! is_scalar($employee) || filter_var((string) $employee, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) === false)) {
            $this->query->remove('employee');
        }

        $department = $this->query('department');

        if ($department !== null && $department !== '' && (! is_string($department) || ! array_key_exists($department, Employee::DEPARTMENTS))) {
            $this->query->remove('department');
        }

        $status = $this->query('status');

        if ($status !== null && $status !== '' && (! is_string($status) || ! array_key_exists($status, AttendanceRecord::STATUSES))) {
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
            'date' => ['nullable', 'date_format:Y-m-d'],
            'employee' => ['nullable', 'integer', 'min:1'],
            'q' => ['nullable', 'string', 'max:100'],
            'department' => ['nullable', Rule::in(array_keys(Employee::DEPARTMENTS))],
            'status' => ['nullable', Rule::in(array_keys(AttendanceRecord::STATUSES))],
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
            'status' => array_key_exists($status, AttendanceRecord::STATUSES) ? $status : '',
        ];
    }

    public function dateValue(): ?string
    {
        $date = $this->validated('date');

        return is_string($date) ? $date : null;
    }

    public function employeeId(): ?int
    {
        $employeeId = $this->validated('employee');

        return $employeeId !== null ? (int) $employeeId : null;
    }

    public function messages(): array
    {
        return [
            'date.date_format' => 'La date sélectionnée est invalide.',
            'employee.integer' => 'Le collaborateur sélectionné est invalide.',
            'q.max' => 'La recherche ne peut pas dépasser 100 caractères.',
            'department.in' => 'Le département sélectionné est invalide.',
            'status.in' => 'Le statut sélectionné est invalide.',
        ];
    }

    private function isValidDate(string $value): bool
    {
        if (preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $value, $matches) !== 1) {
            return false;
        }

        return checkdate((int) $matches[2], (int) $matches[3], (int) $matches[1]);
    }
}
