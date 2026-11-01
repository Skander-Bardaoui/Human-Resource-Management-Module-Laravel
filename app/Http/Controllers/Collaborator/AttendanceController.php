<?php

namespace App\Http\Controllers\Collaborator;

use App\Models\AttendanceRecord;
use App\Models\Conges;
use App\Support\AttendanceClock;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AttendanceController extends CollaboratorController
{
    private const MOTIFS = [
        'oubli' => 'Oubli de badgeage',
        'panne' => 'Panne du terminal',
        'mission' => 'Mission externe',
        'autre' => 'Autre justification',
    ];

    public function index(Request $request)
    {
        $employee = $this->employee($request);
        $today = AttendanceClock::today();
        $period = $request->query('periode') === 'month' ? 'month' : 'week';

        [$rangeStart, $rangeEnd] = $this->range($period, $today);

        $records = AttendanceRecord::forEmployee($employee)
            ->whereBetween('work_date', [$rangeStart->toDateString(), $rangeEnd->toDateString()])
            ->orderByDesc('work_date')
            ->get();

        $todayRecord = AttendanceRecord::forEmployee($employee)->whereDate('work_date', $today)->first();
        $leaveToday = Conges::forEmployee($employee)
            ->where('status', 'approved')
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->first();

        $punchState = $this->punchState($todayRecord, $leaveToday);

        $weekStart = $today->copy()->startOfWeek(Carbon::MONDAY);
        $weekEnd = $weekStart->copy()->addDays(6);
        $weekRecords = AttendanceRecord::forEmployee($employee)
            ->whereBetween('work_date', [$weekStart->toDateString(), $weekEnd->toDateString()])
            ->get();
        $weekWorked = (int) $weekRecords->sum(fn (AttendanceRecord $record) => $record->liveWorkedMinutes());
        $weekExpected = (int) config('hrm.expected_daily_minutes') * 5;
        $weekPercent = $weekExpected > 0 ? (int) min(100, round($weekWorked / $weekExpected * 100)) : 0;

        $monthStart = $today->copy()->startOfMonth();
        $monthRecords = AttendanceRecord::forEmployee($employee)
            ->whereBetween('work_date', [$monthStart->toDateString(), $today->toDateString()])
            ->get();
        $expectedSoFar = max(1, Conges::workingDaysFor($monthStart, $today));
        $presentSoFar = $monthRecords->whereIn('status', ['present', 'late', 'remote'])->count();
        $presenceRate = (int) min(100, round($presentSoFar / $expectedSoFar * 100));
        $lateCount = $monthRecords->where('status', 'late')->count();
        $arrivals = $monthRecords->filter(fn (AttendanceRecord $record) => $record->check_in !== null)
            ->map(fn (AttendanceRecord $record) => $this->minutesOf($record->check_in));
        $averageArrival = $arrivals->isNotEmpty() ? (int) round($arrivals->avg()) : null;
        $overtimeMinutes = (int) $monthRecords->sum(fn (AttendanceRecord $record) => max(0, $record->workedMinutes() - (int) config('hrm.expected_daily_minutes')));

        $teleworkUsed = AttendanceRecord::forEmployee($employee)
            ->where('status', 'remote')
            ->whereBetween('work_date', [$monthStart->toDateString(), $monthStart->copy()->endOfMonth()->toDateString()])
            ->count();
        $teleworkQuota = (int) config('hrm.telework_monthly_quota');

        $anomalies = AttendanceRecord::forEmployee($employee)
            ->whereBetween('work_date', [$today->copy()->subDays(14)->toDateString(), $today->toDateString()])
            ->where('anomaly_ignored', false)
            ->whereNotNull('check_in')
            ->whereNull('check_out')
            ->orderByDesc('work_date')
            ->get();

        $journal = $records->map(fn (AttendanceRecord $record) => $this->journalRow($record, $today))->all();

        return view('collaborator.attendance', [
            'employee' => $employee,
            'today' => $today,
            'period' => $period,
            'rangeStart' => $rangeStart,
            'rangeEnd' => $rangeEnd,
            'journal' => $journal,
            'todayRecord' => $todayRecord,
            'leaveToday' => $leaveToday,
            'punchState' => $punchState,
            'elapsedMinutes' => $todayRecord ? $todayRecord->liveWorkedMinutes() : 0,
            'weekWorkedMinutes' => $weekWorked,
            'weekExpectedMinutes' => $weekExpected,
            'weekPercent' => $weekPercent,
            'presenceRate' => $presenceRate,
            'lateCount' => $lateCount,
            'averageArrival' => $averageArrival,
            'overtimeMinutes' => $overtimeMinutes,
            'teleworkUsed' => $teleworkUsed,
            'teleworkQuota' => $teleworkQuota,
            'teleworkRemaining' => max(0, $teleworkQuota - $teleworkUsed),
            'anomalies' => $anomalies,
            'latestAnomaly' => $anomalies->first(),
            'motifs' => self::MOTIFS,
            'regularizationWindow' => (int) config('hrm.regularization_window_days'),
            'serverTimestamp' => AttendanceClock::now()->getTimestamp(),
        ]);
    }

    public function punchIn(Request $request)
    {
        $employee = $this->employee($request);
        $now = AttendanceClock::now();
        $today = $now->copy()->startOfDay();

        $leave = Conges::forEmployee($employee)
            ->where('status', 'approved')
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->exists();

        if ($leave) {
            return back()->with('error', 'Vous êtes en congé approuvé aujourd\'hui : le pointage est désactivé.');
        }

        $record = AttendanceRecord::forEmployee($employee)->whereDate('work_date', $today)->first();

        if ($record && $record->check_in !== null) {
            return back()->with('error', 'Votre arrivée est déjà enregistrée pour aujourd\'hui.');
        }

        DB::transaction(function () use ($employee, $today, $now, $record) {
            $record = $record ?: new AttendanceRecord([
                'employee_id' => $employee->id,
                'work_date' => $today->toDateString(),
            ]);

            $record->check_in = $now->format('H:i:s');
            $record->mode = 'web';
            $record->status = $record->status === 'remote'
                ? 'remote'
                : ($this->isLate($record->check_in) ? 'late' : 'present');
            $record->notes = $this->appendNote($record->notes, 'Pointage arrivée portail le '.$now->format('d/m/Y H:i'));
            $record->save();
        });

        return back()->with('success', 'Arrivée enregistrée à '.$now->format('H:i').'.');
    }

    public function punchOut(Request $request)
    {
        $employee = $this->employee($request);
        $now = AttendanceClock::now();
        $record = AttendanceRecord::forEmployee($employee)->whereDate('work_date', $now->toDateString())->first();

        if ($record === null || $record->check_in === null) {
            return back()->with('error', 'Aucune arrivée enregistrée aujourd\'hui.');
        }

        if ($record->check_out !== null) {
            return back()->with('error', 'Votre départ est déjà enregistré pour aujourd\'hui.');
        }

        DB::transaction(function () use ($record, $now) {
            if ($record->isOnBreak()) {
                $start = $this->minutesOf($record->break_start);
                $end = $now->hour * 60 + $now->minute;
                $record->break_end = $now->format('H:i:s');
                $record->break_minutes = (int) $record->break_minutes + max(0, $end - (int) $start);
            }

            $record->check_out = $now->format('H:i:s');
            $record->mode = 'web';
            $record->notes = $this->appendNote($record->notes, 'Pointage départ portail le '.$now->format('d/m/Y H:i'));
            $record->save();
        });

        return back()->with('success', 'Départ enregistré à '.$now->format('H:i').'.');
    }

    public function pauseStart(Request $request)
    {
        $employee = $this->employee($request);
        $now = AttendanceClock::now();
        $record = AttendanceRecord::forEmployee($employee)->whereDate('work_date', $now->toDateString())->first();

        if ($record === null || $record->check_in === null) {
            return back()->with('error', 'Pointez votre arrivée avant de démarrer une pause.');
        }

        if ($record->check_out !== null) {
            return back()->with('error', 'Votre journée est déjà clôturée.');
        }

        if ($record->isOnBreak()) {
            return back()->with('error', 'Une pause est déjà en cours.');
        }

        if ($record->break_end !== null) {
            return back()->with('error', 'Une pause a déjà été enregistrée aujourd\'hui.');
        }

        $record->break_start = $now->format('H:i:s');
        $record->mode = 'web';
        $record->save();

        return back()->with('success', 'Pause démarrée à '.$now->format('H:i').'.');
    }

    public function pauseEnd(Request $request)
    {
        $employee = $this->employee($request);
        $now = AttendanceClock::now();
        $record = AttendanceRecord::forEmployee($employee)->whereDate('work_date', $now->toDateString())->first();

        if ($record === null || ! $record->isOnBreak()) {
            return back()->with('error', 'Aucune pause en cours à clôturer.');
        }

        $start = $this->minutesOf($record->break_start);
        $end = $now->hour * 60 + $now->minute;

        $record->break_end = $now->format('H:i:s');
        $record->break_minutes = (int) $record->break_minutes + max(0, $end - (int) $start);
        $record->mode = 'web';
        $record->save();

        return back()->with('success', 'Pause clôturée à '.$now->format('H:i').'.');
    }

    public function telework(Request $request)
    {
        $employee = $this->employee($request);

        $validated = $request->validate([
            'date' => ['required', 'date'],
            'note' => ['nullable', 'string', 'max:255'],
        ]);

        $date = Carbon::parse($validated['date'])->startOfDay();
        $today = AttendanceClock::today();

        if ($date->lt($today) || $date->gt($today->copy()->addDays(30))) {
            return back()->with('error', 'La date de télétravail doit être comprise entre aujourd\'hui et 30 jours.');
        }

        if ($date->dayOfWeek === Carbon::SUNDAY) {
            return back()->with('error', 'Le dimanche n\'est pas un jour ouvré.');
        }

        $leave = Conges::forEmployee($employee)
            ->where('status', 'approved')
            ->whereDate('start_date', '<=', $date)
            ->whereDate('end_date', '>=', $date)
            ->exists();

        if ($leave) {
            return back()->with('error', 'Cette journée est déjà couverte par un congé approuvé.');
        }

        $monthStart = $date->copy()->startOfMonth();
        $used = AttendanceRecord::forEmployee($employee)
            ->where('status', 'remote')
            ->whereBetween('work_date', [$monthStart->toDateString(), $monthStart->copy()->endOfMonth()->toDateString()])
            ->count();

        if ($used >= (int) config('hrm.telework_monthly_quota')) {
            return back()->with('error', 'Quota mensuel de télétravail atteint ('.config('hrm.telework_monthly_quota').' jours).');
        }

        $existing = AttendanceRecord::forEmployee($employee)->whereDate('work_date', $date)->first();

        if ($existing && ($existing->check_in !== null || $existing->status === 'remote')) {
            return back()->with('error', 'Une présence ou un télétravail est déjà enregistré pour cette date.');
        }

        DB::transaction(function () use ($employee, $date, $existing, $validated) {
            $record = $existing ?: new AttendanceRecord([
                'employee_id' => $employee->id,
                'work_date' => $date->toDateString(),
            ]);

            $record->mode = 'web';
            $record->status = 'remote';
            $record->notes = $this->appendNote(
                $record->notes,
                'Télétravail déclaré depuis le portail'.(filled($validated['note'] ?? null) ? ' — '.trim($validated['note']) : '')
            );
            $record->save();
        });

        return back()->with('success', 'Télétravail déclaré pour le '.$date->format('d/m/Y').'.');
    }

    public function regularize(Request $request)
    {
        $employee = $this->employee($request);

        $validated = $request->validate([
            'date' => ['required', 'date'],
            'time' => ['required', 'date_format:H:i'],
            'direction' => ['required', Rule::in(['in', 'out'])],
            'reason' => ['required', Rule::in(array_keys(self::MOTIFS))],
            'note' => ['nullable', 'string', 'max:500'],
        ]);

        $date = Carbon::parse($validated['date'])->startOfDay();
        $today = AttendanceClock::today();
        $window = (int) config('hrm.regularization_window_days');

        if ($date->gt($today) || $date->lt($today->copy()->subDays($window))) {
            return back()->with('error', 'La régularisation est limitée aux '.$window.' derniers jours.');
        }

        $record = AttendanceRecord::forEmployee($employee)->whereDate('work_date', $date)->first();
        $time = $validated['time'].':00';

        if ($validated['direction'] === 'in' && $record && $record->check_in !== null) {
            return back()->with('error', 'Une heure d\'arrivée existe déjà pour cette date.');
        }

        if ($validated['direction'] === 'out' && ($record === null || $record->check_in === null)) {
            return back()->with('error', 'Impossible de régulariser un départ sans arrivée enregistrée.');
        }

        if ($validated['direction'] === 'out' && $record->check_out !== null) {
            return back()->with('error', 'Une heure de départ existe déjà pour cette date.');
        }

        $note = '[Régularisation collaborateur] '.self::MOTIFS[$validated['reason']]
            .(filled($validated['note'] ?? null) ? ' — '.trim($validated['note']) : '');

        DB::transaction(function () use ($employee, $date, $validated, $time, $record, $note) {
            $record = $record ?: new AttendanceRecord([
                'employee_id' => $employee->id,
                'work_date' => $date->toDateString(),
                'mode' => 'manual',
                'status' => 'present',
            ]);

            if ($validated['direction'] === 'in') {
                $record->check_in = $time;

                if ($record->status !== 'remote') {
                    $record->status = $this->isLate($time) ? 'late' : 'present';
                }
            } else {
                $record->check_out = $time;
            }

            $record->notes = $this->appendNote($record->notes, $note);
            $record->anomaly_ignored = true;
            $record->save();
        });

        return back()->with('success', 'Régularisation soumise pour le '.$date->format('d/m/Y').'.');
    }

    public function export(Request $request): StreamedResponse
    {
        $employee = $this->employee($request);
        $today = AttendanceClock::today();
        $period = $request->query('periode') === 'month' ? 'month' : 'week';
        [$start, $end] = $this->range($period, $today);

        $records = AttendanceRecord::forEmployee($employee)
            ->whereBetween('work_date', [$start->toDateString(), $end->toDateString()])
            ->orderBy('work_date')
            ->get();

        return response()->streamDownload(function () use ($records) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, [
                'Date',
                'Arrivée',
                'Départ',
                'Pause (min)',
                'Total effectué',
                'Mode',
                'Statut',
                'Notes',
            ], ';');

            foreach ($records as $record) {
                fputcsv($handle, [
                    $record->work_date?->format('d/m/Y'),
                    $record->checkInShort() ?? '—',
                    $record->checkOutShort() ?? '—',
                    $record->effectiveBreakMinutes(),
                    $record->workedLabel(),
                    $record->modeLabel(),
                    $record->statusLabel(),
                    $record->notes,
                ], ';');
            }

            fclose($handle);
        }, 'mes-pointages-'.$start->format('Ymd').'-'.$end->format('Ymd').'.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    private function range(string $period, Carbon $today): array
    {
        if ($period === 'month') {
            return [$today->copy()->startOfMonth(), $today->copy()->endOfMonth()];
        }

        $start = $today->copy()->startOfWeek(Carbon::MONDAY);

        return [$start, $start->copy()->addDays(6)];
    }

    private function punchState(?AttendanceRecord $record, ?Conges $leave): string
    {
        if ($record !== null) {
            if ($record->status === 'leave') {
                return 'leave';
            }

            if ($record->isOpenSession()) {
                return $record->isOnBreak() ? 'on_break' : 'working';
            }

            if ($record->check_in !== null) {
                return 'done';
            }
        }

        return $leave ? 'leave' : 'not_started';
    }

    private function journalRow(AttendanceRecord $record, Carbon $today): array
    {
        $live = $record->isOpenSession();

        return [
            'record' => $record,
            'date' => $record->work_date,
            'dayLabel' => ucfirst($record->work_date->locale('fr')->translatedFormat('l j F Y')),
            'week' => $record->work_date->weekOfYear,
            'isToday' => $record->work_date->isSameDay($today),
            'checkIn' => $record->checkInShort(),
            'checkOut' => $record->checkOutShort(),
            'breakLabel' => $record->isOnBreak() ? 'En pause' : $record->breakLabel(),
            'workedLabel' => $record->isOpenSession() ? $this->minutesLabel($record->liveWorkedMinutes()) : $record->workedLabel(),
            'live' => $live,
            'modeLabel' => $record->modeLabel(),
            'modeIcon' => $record->modeIcon(),
            'modeColor' => $record->modeColor(),
            'status' => $record->status,
            'statusLabel' => $record->statusLabel(),
            'statusPillClasses' => $record->statusPillClasses(),
            'deltaMinutes' => $record->check_in !== null && $record->check_out !== null
                ? $record->workedMinutes() - (int) config('hrm.expected_daily_minutes')
                : null,
            'isAnomaly' => $record->hasMissingCheckout(),
        ];
    }

    private function minutesLabel(int $minutes): string
    {
        return intdiv(max(0, $minutes), 60).'h '.str_pad((string) (max(0, $minutes) % 60), 2, '0', STR_PAD_LEFT).'m';
    }

    private function minutesOf(?string $time): ?int
    {
        if ($time === null || $time === '') {
            return null;
        }

        [$hours, $minutes] = array_pad(explode(':', $time), 2, 0);

        return (int) $hours * 60 + (int) $minutes;
    }

    private function isLate(string $time): bool
    {
        $threshold = (string) config('hrm.late_threshold');

        return $time > (strlen($threshold) === 5 ? $threshold.':00' : $threshold);
    }

    private function appendNote(?string $notes, string $note): string
    {
        return trim(($notes ? $notes.' — ' : '').$note);
    }
}
