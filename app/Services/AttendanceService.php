<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AttendanceRecord;
use App\Models\Conges;
use App\Models\Employee;
use App\Models\User;
use App\Support\CsvExport;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class AttendanceService
{
    /**
     * @param  array{q: string, department: string, status: string}  $filters
     * @return array<string, mixed>
     */
    public function overview(Carbon $date, ?int $employeeId, array $filters): array
    {
        $historyEmployee = $employeeId !== null
            ? Employee::query()->find($employeeId, ['id', 'name', 'email', 'position', 'department', 'avatar_url', 'status'])
            : null;

        if ($historyEmployee === null) {
            $employeeId = null;
        }

        $records = $this->filteredQuery($date, $employeeId, $filters)
            ->orderByDesc('work_date')
            ->orderBy(Employee::select('name')->whereColumn('employees.id', 'attendance_records.employee_id'))
            ->paginate(8)
            ->appends(array_filter([
                'date' => $date->toDateString(),
                'employee' => $employeeId,
                'q' => $filters['q'],
                'department' => $filters['department'],
                'status' => $filters['status'],
            ], fn ($value) => $value !== null && $value !== ''));

        $dayRecords = AttendanceRecord::query()
            ->with('employee:id,name,email,position,department,avatar_url,status')
            ->whereDate('work_date', $date->toDateString())
            ->get();

        $activeEmployees = Employee::query()->where('status', '!=', 'inactif')->count();
        $presentCount = $dayRecords->whereIn('status', ['present', 'late', 'remote'])->count();
        $presenceRate = $activeEmployees > 0
            ? min(100, (int) round($presentCount / $activeEmployees * 100))
            : 0;

        $lateRecords = $dayRecords->where('status', 'late');
        $lateCount = $lateRecords->count();
        $averageLate = $lateCount > 0
            ? (int) round($lateRecords->avg(fn (AttendanceRecord $record) => $record->lateMinutes()))
            : 0;

        $absenceCount = $dayRecords->where('status', 'absent')->count();

        $monthRecords = AttendanceRecord::query()
            ->whereBetween('work_date', [
                $date->copy()->startOfMonth()->toDateString(),
                $date->copy()->endOfMonth()->toDateString(),
            ])
            ->get(['employee_id', 'work_date', 'check_in', 'check_out', 'break_minutes', 'status']);

        $overtimeMinutes = (int) $monthRecords->sum(
            fn (AttendanceRecord $record) => $record->overtimeMinutes()
        );
        $overtimeAgents = $monthRecords
            ->filter(fn (AttendanceRecord $record) => $record->overtimeMinutes() > 0)
            ->pluck('employee_id')
            ->unique()
            ->count();

        $week = $this->weekBreakdown($date, $activeEmployees);
        $anomalies = $this->anomalies($dayRecords);

        $ignoredAnomalyCount = $dayRecords
            ->filter(fn (AttendanceRecord $record) => $record->anomaly_ignored && $record->hasAnomalySignal())
            ->count();

        $lastUpdatedAt = $dayRecords->max('updated_at');
        $lastUpdatedAt = $lastUpdatedAt
            ? Carbon::parse($lastUpdatedAt)->setTimezone($this->timezone())
            : null;

        $employees = Employee::query()
            ->where('status', '!=', 'inactif')
            ->orderBy('name')
            ->get(['id', 'name', 'position', 'department', 'avatar_url', 'status']);

        return [
            'records' => $records,
            'selectedDate' => $date,
            'dateLabel' => ucfirst($date->copy()->locale('fr')->translatedFormat('l j F Y')),
            'previousDate' => $date->copy()->subDay(),
            'nextDate' => $date->copy()->addDay(),
            'today' => AttendanceRecord::currentDate(),
            'employeeId' => $employeeId,
            'historyEmployee' => $historyEmployee,
            'employees' => $employees,
            'activeEmployees' => $activeEmployees,
            'presentCount' => $presentCount,
            'presenceRate' => $presenceRate,
            'lateCount' => $lateCount,
            'averageLate' => $averageLate,
            'absenceCount' => $absenceCount,
            'overtimeHours' => round($overtimeMinutes / 60, 1),
            'overtimeAgents' => $overtimeAgents,
            'overtimeLabel' => ucfirst($date->copy()->locale('fr')->translatedFormat('F Y')),
            'weekDays' => $week['days'],
            'weekLabel' => $week['label'],
            'anomalies' => $anomalies,
            'anomalyCount' => count($anomalies),
            'ignoredAnomalyCount' => $ignoredAnomalyCount,
            'lastUpdatedAt' => $lastUpdatedAt,
            'rules' => [
                'scheduleStart' => substr((string) config('attendance.schedule.start', '08:30'), 0, 5),
                'scheduleEnd' => substr(AttendanceRecord::scheduleEndTime(), 0, 5),
                'lateTolerance' => AttendanceRecord::lateToleranceMinutes(),
                'minimumFullDay' => AttendanceRecord::minimumFullDayMinutes(),
                'overtimeThreshold' => AttendanceRecord::overtimeThresholdMinutes(),
                'timezone' => $this->timezone(),
            ],
        ];
    }

    /**
     * @param  array{employee_id: int, date: string, time: string, direction: string, motif: string, comment?: string|null}  $data
     * @return array{record: AttendanceRecord, employee: Employee, changed: bool}
     */
    public function punch(array $data, User $admin): array
    {
        $employee = Employee::query()->findOrFail($data['employee_id']);

        if ($employee->status === 'inactif') {
            $this->punchError('employee_id', 'Ce collaborateur est inactif : aucun pointage ne peut être saisi.');
        }

        $timezone = $this->timezone();
        $date = Carbon::createFromFormat('!Y-m-d', $data['date'], $timezone);

        return DB::transaction(function () use ($data, $admin, $employee, $date, $timezone) {
            $record = AttendanceRecord::query()
                ->where('employee_id', $employee->id)
                ->whereDate('work_date', $date->toDateString())
                ->first();

            if ($record === null) {
                $record = new AttendanceRecord([
                    'employee_id' => $employee->id,
                    'work_date' => $date->toDateString(),
                ]);
            }

            if ($record->exists && $record->status === 'leave') {
                $this->punchError('employee_id', 'Ce collaborateur est en congé à cette date. Le pointage ne peut pas être saisi.');
            }

            $field = match ($data['direction']) {
                'in' => 'check_in',
                'out' => 'check_out',
                'pause_in' => 'break_start',
                'pause_out' => 'break_end',
            };
            $time = $data['time'].':00';
            $previous = $record->getAttribute($field);

            if ($previous !== null && AttendanceRecord::timeToMinutes($previous) === AttendanceRecord::timeToMinutes($time)) {
                return ['record' => $record, 'employee' => $employee, 'changed' => false];
            }

            $this->guardTimeSequence($record, $data['direction'], $time);

            $record->setAttribute($field, $time);
            $record->mode = 'manual';

            if ($data['direction'] === 'in') {
                $record->status = $record->status === 'remote'
                    ? 'remote'
                    : ($record->lateMinutes() > AttendanceRecord::lateToleranceMinutes() ? 'late' : 'present');
            }

            if (in_array($data['direction'], ['pause_in', 'pause_out'], true)) {
                $record->break_minutes = $this->breakMinutes($record);
            }

            $note = ($previous !== null ? 'Correction' : 'Ajout')
                .' manuel du '.Carbon::now($timezone)->format('d/m/Y H:i')
                .' par '.$admin->name
                .' — '.AttendanceRecord::MOTIFS[$data['motif']];

            if (filled($data['comment'] ?? null)) {
                $note .= ' • '.trim($data['comment']);
            }

            $record->notes = $this->appendNote($record->notes, $note);
            $record->anomaly_ignored = false;
            $record->save();

            return ['record' => $record, 'employee' => $employee, 'changed' => true];
        });
    }

    public function justify(AttendanceRecord $record, string $note, User $admin): void
    {
        $record->anomaly_ignored = true;
        $record->notes = $this->appendNote(
            $record->notes,
            'Justificatif du '.Carbon::now($this->timezone())->format('d/m/Y H:i')
                .' par '.$admin->name.' : '.trim($note)
        );
        $record->save();
    }

    public function ignore(AttendanceRecord $record): void
    {
        $record->anomaly_ignored = true;
        $record->save();
    }

    public function resolve(AttendanceRecord $record): bool
    {
        $regularized = false;

        if ($record->hasMissingCheckout()) {
            $end = AttendanceRecord::scheduleEndTime();
            $record->check_out = $end;
            $record->notes = $this->appendNote(
                $record->notes,
                'Départ régularisé à '.substr($end, 0, 5).' par la RH.'
            );
            $regularized = true;
        }

        $record->anomaly_ignored = true;
        $record->save();

        return $regularized;
    }

    public function resolveAll(Carbon $date): int
    {
        return DB::transaction(function () use ($date) {
            $records = AttendanceRecord::query()
                ->whereDate('work_date', $date->toDateString())
                ->where('anomaly_ignored', false)
                ->get()
                ->filter(fn (AttendanceRecord $record) => $record->hasAnomalySignal());

            foreach ($records as $record) {
                if ($record->hasMissingCheckout()) {
                    $end = AttendanceRecord::scheduleEndTime();
                    $record->check_out = $end;
                    $record->notes = $this->appendNote(
                        $record->notes,
                        'Départ régularisé à '.substr($end, 0, 5).' par la RH (traitement groupé).'
                    );
                }

                $record->anomaly_ignored = true;
                $record->save();
            }

            return $records->count();
        });
    }

    /**
     * @param  array{q: string, department: string, status: string}  $filters
     * @return Collection<int, array<int, string|int>>
     */
    public function exportRows(Carbon $date, ?int $employeeId, array $filters): Collection
    {
        return $this->filteredQuery($date, $employeeId, $filters)
            ->orderByDesc('work_date')
            ->orderBy('employee_id')
            ->get()
            ->map(fn (AttendanceRecord $record) => [
                (string) $record->work_date?->format('d/m/Y'),
                CsvExport::text($record->employee?->name),
                'MAT-'.str_pad((string) $record->employee_id, 4, '0', STR_PAD_LEFT),
                CsvExport::text($record->employee?->departmentLabel()),
                $record->checkInShort() ?? '—',
                $record->checkOutShort() ?? '—',
                $record->break_minutes,
                $record->workedLabel(),
                CsvExport::text($record->modeLabel()),
                CsvExport::text($record->statusLabel()),
                CsvExport::text($record->notes),
            ]);
    }

    /**
     * @param  array{q: string, department: string, status: string}  $filters
     */
    public function filteredQuery(Carbon $date, ?int $employeeId, array $filters): Builder
    {
        return AttendanceRecord::query()
            ->with('employee')
            ->when(
                $employeeId !== null,
                fn (Builder $query) => $query
                    ->where('employee_id', $employeeId)
                    ->whereBetween('work_date', [
                        $date->copy()->startOfMonth()->toDateString(),
                        $date->copy()->endOfMonth()->toDateString(),
                    ]),
                fn (Builder $query) => $query->whereDate('work_date', $date->toDateString())
            )
            ->when(($filters['q'] ?? '') !== '', function (Builder $query) use ($filters) {
                $term = '%'.$filters['q'].'%';

                $query->whereHas('employee', function (Builder $employee) use ($term) {
                    $employee->where('name', 'like', $term)
                        ->orWhere('email', 'like', $term)
                        ->orWhere('cin', 'like', $term)
                        ->orWhere('cnss_number', 'like', $term)
                        ->orWhere('position', 'like', $term);
                });
            })
            ->when(
                array_key_exists((string) ($filters['department'] ?? ''), Employee::DEPARTMENTS),
                fn (Builder $query) => $query->whereHas(
                    'employee',
                    fn (Builder $employee) => $employee->where('department', $filters['department'])
                )
            )
            ->when(
                array_key_exists((string) ($filters['status'] ?? ''), AttendanceRecord::STATUSES),
                fn (Builder $query) => $query->where('status', $filters['status'])
            );
    }

    public function resolveDate(?string $value): Carbon
    {
        $timezone = $this->timezone();

        if ($value !== null && $value !== '') {
            try {
                $date = Carbon::createFromFormat('!Y-m-d', $value, $timezone);

                if ($date !== false) {
                    return $date->startOfDay();
                }
            } catch (\Throwable) {
                // La valeur invalide est ignorée et remplacée par le jour courant.
            }
        }

        return Carbon::now($timezone)->startOfDay();
    }

    /**
     * @return array{label: string, days: array<int, array<string, mixed>>}
     */
    private function weekBreakdown(Carbon $date, int $activeEmployees): array
    {
        $weekStart = $date->copy()->startOfWeek(Carbon::MONDAY)->startOfDay();
        $weekEnd = $weekStart->copy()->addDays(6);
        $today = AttendanceRecord::currentDate()->toDateString();

        $days = collect(range(0, 6))
            ->map(fn (int $offset) => $weekStart->copy()->addDays($offset))
            ->filter(fn (Carbon $day) => Conges::workingDaysFor($day, $day) === 1)
            ->values();

        $records = AttendanceRecord::query()
            ->whereBetween('work_date', [$weekStart->toDateString(), $weekEnd->toDateString()])
            ->get(['id', 'employee_id', 'work_date', 'status'])
            ->groupBy(fn (AttendanceRecord $record) => $record->work_date->toDateString());

        return [
            'label' => 'Semaine du '.ucfirst($weekStart->copy()->locale('fr')->translatedFormat('j F Y')),
            'days' => $days->map(function (Carbon $day) use ($records, $activeEmployees, $today) {
                $dayRecords = $records->get($day->toDateString(), collect());
                $present = $dayRecords->whereIn('status', ['present', 'late', 'remote'])->count();
                $late = $dayRecords->where('status', 'late')->count();
                $isFuture = $day->toDateString() > $today;

                $rate = ! $isFuture && $activeEmployees > 0
                    ? min(100, (int) round($present / $activeEmployees * 100))
                    : null;
                $lateRate = ! $isFuture && $activeEmployees > 0
                    ? min(100, (int) round($late / $activeEmployees * 100))
                    : 0;

                return [
                    'label' => $this->shortWeekday($day).' '.$day->day,
                    'fullLabel' => ucfirst($day->copy()->locale('fr')->translatedFormat('l j F')),
                    'date' => $day,
                    'rate' => $rate,
                    'primaryHeight' => $rate !== null ? max(0, $rate - $lateRate) : 0,
                    'lateHeight' => $lateRate,
                    'isToday' => $day->toDateString() === $today,
                    'isFuture' => $isFuture,
                ];
            })->all(),
        ];
    }

    /**
     * @param  Collection<int, AttendanceRecord>  $dayRecords
     * @return array<int, array<string, mixed>>
     */
    private function anomalies(Collection $dayRecords): array
    {
        $scheduleStart = substr((string) config('attendance.schedule.start', '08:30'), 0, 5);

        return $dayRecords
            ->filter(fn (AttendanceRecord $record) => $record->isAnomaly())
            ->sortByDesc(fn (AttendanceRecord $record) => $record->hasMissingCheckout() ? 2 : ($record->isLateArrival() ? 1 : 0))
            ->map(function (AttendanceRecord $record) use ($scheduleStart) {
                $employeeName = $record->employee?->name ?? 'Collaborateur supprimé';

                if ($record->hasMissingCheckout()) {
                    return [
                        'record' => $record,
                        'title' => 'Départ non pointé',
                        'icon' => 'logout',
                        'tone' => 'bg-error-container text-error',
                        'action' => 'resolve',
                        'actionLabel' => 'Régulariser à '.substr(AttendanceRecord::scheduleEndTime(), 0, 5),
                        'subtitle' => $employeeName.' • '.$record->work_date?->format('d/m/Y'),
                        'detail' => 'Aucune heure de départ enregistrée.',
                    ];
                }

                if ($record->isLateArrival()) {
                    return [
                        'record' => $record,
                        'title' => 'Retard non justifié',
                        'icon' => 'schedule',
                        'tone' => 'bg-secondary-container text-secondary',
                        'action' => 'justify',
                        'actionLabel' => 'Justifier',
                        'subtitle' => $employeeName.' • +'.$record->lateMinutes().' min',
                        'detail' => 'Arrivée à '.($record->checkInShort() ?? '—').' (référence '.$scheduleStart.').',
                    ];
                }

                return [
                    'record' => $record,
                    'title' => 'Sortie anticipée',
                    'icon' => 'hourglass_top',
                    'tone' => 'bg-secondary-container text-secondary',
                    'action' => 'justify',
                    'actionLabel' => 'Justifier',
                    'subtitle' => $employeeName.' • '.($record->checkOutShort() ?? '—'),
                    'detail' => 'Temps effectué : '.$record->workedLabel().'.',
                ];
            })
            ->values()
            ->all();
    }

    private function guardTimeSequence(AttendanceRecord $record, string $direction, string $time): void
    {
        $in = AttendanceRecord::timeToMinutes($record->check_in);
        $out = AttendanceRecord::timeToMinutes($record->check_out);
        $breakStart = AttendanceRecord::timeToMinutes($record->break_start);
        $breakEnd = AttendanceRecord::timeToMinutes($record->break_end);
        $value = AttendanceRecord::timeToMinutes($time);

        if ($value === null) {
            $this->punchError('time', "L'heure du pointage est invalide.");
        }

        switch ($direction) {
            case 'in':
                if ($out !== null && $value >= $out) {
                    $this->punchError('time', "L'heure d'arrivée doit précéder l'heure de départ.");
                }

                if ($breakStart !== null && $value >= $breakStart) {
                    $this->punchError('time', "L'heure d'arrivée doit précéder le début de pause.");
                }

                if ($breakEnd !== null && $value >= $breakEnd) {
                    $this->punchError('time', "L'heure d'arrivée doit précéder la fin de pause.");
                }
                break;

            case 'out':
                if ($in === null) {
                    $this->punchError('time', "Enregistrez d'abord l'heure d'arrivée avant le départ.");
                }

                if ($value <= $in) {
                    $this->punchError('time', "L'heure de départ doit être postérieure à l'arrivée.");
                }

                if ($breakStart !== null && $value < $breakStart) {
                    $this->punchError('time', "L'heure de départ doit être postérieure au début de pause.");
                }

                if ($breakEnd !== null && $value < $breakEnd) {
                    $this->punchError('time', "L'heure de départ doit être postérieure à la fin de pause.");
                }
                break;

            case 'pause_in':
                if ($in === null) {
                    $this->punchError('time', "Enregistrez d'abord l'heure d'arrivée.");
                }

                if ($value <= $in) {
                    $this->punchError('time', "Le début de pause doit être postérieur à l'arrivée.");
                }

                if ($out !== null && $value >= $out) {
                    $this->punchError('time', 'Le début de pause doit précéder le départ.');
                }

                if ($breakEnd !== null && $value >= $breakEnd) {
                    $this->punchError('time', 'Le début de pause doit précéder la fin de pause.');
                }
                break;

            case 'pause_out':
                if ($breakStart === null) {
                    $this->punchError('time', "Enregistrez d'abord le début de pause.");
                }

                if ($value <= $breakStart) {
                    $this->punchError('time', 'La fin de pause doit être postérieure au début de pause.');
                }

                if ($in !== null && $value <= $in) {
                    $this->punchError('time', "La fin de pause doit être postérieure à l'arrivée.");
                }

                if ($out !== null && $value > $out) {
                    $this->punchError('time', 'La fin de pause doit précéder ou coïncider avec le départ.');
                }
                break;
        }
    }

    private function breakMinutes(AttendanceRecord $record): int
    {
        $start = AttendanceRecord::timeToMinutes($record->break_start);
        $end = AttendanceRecord::timeToMinutes($record->break_end);

        return $start !== null && $end !== null && $end > $start ? $end - $start : 0;
    }

    private function appendNote(?string $existing, string $note): string
    {
        return trim(($existing ? $existing.' — ' : '').$note);
    }

    private function punchError(string $field, string $message): never
    {
        throw ValidationException::withMessages([$field => $message])->errorBag('punch');
    }

    private function shortWeekday(Carbon $day): string
    {
        return ucfirst(mb_substr($day->copy()->locale('fr')->translatedFormat('l'), 0, 3));
    }

    private function timezone(): string
    {
        return (string) config('attendance.timezone', 'Africa/Tunis');
    }
}
