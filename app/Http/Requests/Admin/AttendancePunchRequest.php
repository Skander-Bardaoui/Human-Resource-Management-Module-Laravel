<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use App\Models\AttendanceRecord;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class AttendancePunchRequest extends FormRequest
{
    protected $errorBag = 'punch';

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if (is_string($this->input('comment'))) {
            $this->merge(['comment' => trim($this->input('comment'))]);
        }
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'employee_id' => [
                'bail',
                'required',
                'integer',
                Rule::exists('employees', 'id')->whereNot('status', 'inactif'),
            ],
            'date' => ['required', 'date_format:Y-m-d'],
            'time' => ['required', 'date_format:H:i'],
            'direction' => ['required', Rule::in(array_keys(AttendanceRecord::DIRECTIONS))],
            'motif' => ['required', Rule::in(array_keys(AttendanceRecord::MOTIFS))],
            'comment' => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * @return array<int, callable>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if ($validator->errors()->isNotEmpty()) {
                    return;
                }

                $timezone = (string) config('attendance.timezone', 'Africa/Tunis');
                $today = Carbon::now($timezone)->startOfDay();
                $date = Carbon::createFromFormat('!Y-m-d', (string) $this->input('date'), $timezone);

                if ($date->gt($today)) {
                    $validator->errors()->add('date', 'La date du pointage ne peut pas être dans le futur.');

                    return;
                }

                if (! $date->isSameDay($today)) {
                    return;
                }

                $time = AttendanceRecord::timeToMinutes((string) $this->input('time'));
                $now = AttendanceRecord::timeToMinutes(Carbon::now($timezone)->format('H:i'));

                if ($time !== null && $now !== null && $time > $now) {
                    $validator->errors()->add('time', "L'heure du pointage ne peut pas être dans le futur.");
                }
            },
        ];
    }

    public function messages(): array
    {
        return [
            'employee_id.required' => 'Sélectionnez le collaborateur concerné.',
            'employee_id.exists' => 'Le collaborateur sélectionné est introuvable ou inactif.',
            'date.required' => 'La date du pointage est obligatoire.',
            'date.date_format' => 'La date du pointage est invalide.',
            'time.required' => "L'heure du pointage est obligatoire.",
            'time.date_format' => "L'heure du pointage est invalide.",
            'direction.required' => 'Sélectionnez le sens du pointage.',
            'direction.in' => 'Le sens du pointage sélectionné est invalide.',
            'motif.required' => 'Le motif de la saisie manuelle est obligatoire.',
            'motif.in' => 'Le motif sélectionné est invalide.',
            'comment.max' => 'Le commentaire ne peut pas dépasser 500 caractères.',
        ];
    }

    public function attributes(): array
    {
        return [
            'employee_id' => 'collaborateur',
            'date' => 'date',
            'time' => 'heure',
            'direction' => 'sens du pointage',
            'motif' => 'motif',
            'comment' => 'commentaire',
        ];
    }
}
