<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class DepartmentIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'q' => ['nullable', 'string', 'max:100'],
            'sort' => ['nullable', 'in:name,headcount,budget'],
            'direction' => ['nullable', 'in:asc,desc'],
        ];
    }

    /**
     * @return array{q: string, sort: string, direction: string}
     */
    public function filters(): array
    {
        $validated = $this->validated();

        return [
            'q' => trim((string) ($validated['q'] ?? '')),
            'sort' => $validated['sort'] ?? 'headcount',
            'direction' => $validated['direction'] ?? 'desc',
        ];
    }

    public function messages(): array
    {
        return [
            'q.max' => 'La recherche ne peut pas dépasser 100 caractères.',
            'sort.in' => 'Le critère de tri sélectionné est invalide.',
            'direction.in' => "L'ordre de tri sélectionné est invalide.",
        ];
    }
}
