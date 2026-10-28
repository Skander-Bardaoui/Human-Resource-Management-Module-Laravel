<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DepartmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $values = [];

        foreach (['name', 'code', 'description'] as $field) {
            if (! $this->has($field)) {
                continue;
            }

            $value = $this->input($field);
            $values[$field] = is_string($value) ? trim($value) : $value;
        }

        $this->merge($values);
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $department = $this->route('department');

        return [
            'name' => [
                'required',
                'string',
                'max:120',
                Rule::unique('departments', 'name')->ignore($department?->id),
            ],
            'manager_id' => ['nullable', 'integer', 'exists:employees,id'],
            'code' => ['nullable', 'string', 'max:50'],
            'monthly_budget' => ['nullable', 'numeric', 'min:0', 'max:999999999.999', 'decimal:0,3'],
            'description' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'nom du département',
            'manager_id' => 'responsable',
            'code' => 'code analytique',
            'monthly_budget' => 'budget mensuel',
            'description' => 'description',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Le nom du département est obligatoire.',
            'name.unique' => 'Un département porte déjà ce nom.',
            'name.max' => 'Le nom du département ne peut pas dépasser 120 caractères.',
            'manager_id.exists' => 'Le responsable sélectionné est introuvable.',
            'code.max' => 'Le code analytique ne peut pas dépasser 50 caractères.',
            'monthly_budget.numeric' => 'Le budget mensuel doit être un montant valide.',
            'monthly_budget.min' => 'Le budget mensuel ne peut pas être négatif.',
            'monthly_budget.max' => 'Le budget mensuel dépasse la limite autorisée.',
            'monthly_budget.decimal' => 'Le budget mensuel accepte au maximum trois décimales.',
            'description.max' => 'La description ne peut pas dépasser 1 000 caractères.',
        ];
    }
}
