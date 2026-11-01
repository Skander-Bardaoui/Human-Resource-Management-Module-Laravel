<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class PayrollPeriodRequest extends FormRequest
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
            'period' => ['required', 'date_format:Y-m'],
        ];
    }

    public function period(): string
    {
        return (string) $this->validated('period');
    }

    public function messages(): array
    {
        return [
            'period.required' => 'La période de paie est obligatoire.',
            'period.date_format' => 'La période de paie doit être au format AAAA-MM.',
        ];
    }

    public function attributes(): array
    {
        return [
            'period' => 'période de paie',
        ];
    }
}
