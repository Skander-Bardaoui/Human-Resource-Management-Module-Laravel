<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AttendanceJustificationRequest extends FormRequest
{
    protected $errorBag = 'justify';

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if (is_string($this->input('note'))) {
            $this->merge(['note' => trim($this->input('note'))]);
        }
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'note' => ['required', 'string', 'min:5', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'note.required' => 'Saisissez le justificatif avant de valider.',
            'note.min' => 'Le justificatif doit contenir au moins 5 caractères.',
            'note.max' => 'Le justificatif ne peut pas dépasser 500 caractères.',
        ];
    }

    public function attributes(): array
    {
        return [
            'note' => 'justificatif',
        ];
    }
}
