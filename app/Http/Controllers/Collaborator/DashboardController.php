<?php

namespace App\Http\Controllers\Collaborator;

use App\Models\AdvanceRequest;
use App\Models\AttendanceRecord;
use App\Models\Conges;
use App\Models\Document;
use App\Models\DocumentRequest;
use App\Models\Employee;
use App\Models\Payslip;
use App\Support\AttendanceClock;
use App\Support\PayslipGenerator;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends CollaboratorController
{
    public function index(Request $request)
    {
        $employee = $this->employee($request);
        $today = AttendanceClock::today()->startOfDay();
        $now = AttendanceClock::now();

        $todayRecord = AttendanceRecord::forEmployee($employee)->whereDate('work_date', $today)->first();
        $leaveToday = Conges::forEmployee($employee)
            ->where('status', 'approved')
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->first();

        $punchState = $this->punchState($todayRecord, $leaveToday);
        $elapsedSeconds = $this->elapsedSeconds($todayRecord, $punchState);

        $currentPeriod = $now->format('Y-m');
        $previousPeriod = $now->copy()->subMonthNoOverflow()->format('Y-m');
        PayslipGenerator::ensureFor($employee, $currentPeriod);
        PayslipGenerator::ensureFor($employee, $previousPeriod);

        $latestPayslip = Payslip::forEmployee($employee)->orderByDesc('period')->first();

        $leaveTaken = (int) Conges::forEmployee($employee)
            ->where('type', 'annuel')
            ->where('status', 'approved')
            ->whereYear('start_date', $now->year)
            ->sum('working_days');
        $leaveRemaining = (int) ($employee->token_leave_days ?? 0);
        $leaveAcquired = max((int) ($employee->leave_rights ?? 0), $leaveRemaining + $leaveTaken);
        $nextLeave = Conges::forEmployee($employee)
            ->where('status', 'approved')
            ->whereDate('start_date', '>=', $today)
            ->orderBy('start_date')
            ->first();

        $windowStart = $today->copy()->subDays(29);
        $windowRecords = AttendanceRecord::forEmployee($employee)
            ->whereBetween('work_date', [$windowStart->toDateString(), $today->toDateString()])
            ->get();
        $presentDays = $windowRecords->whereIn('status', ['present', 'late', 'remote'])->count();
        $expectedDays = max(1, Conges::workingDaysFor($windowStart, $today));
        $presenceRate = (int) min(100, round($presentDays / $expectedDays * 100));

        $monthRecords = AttendanceRecord::forEmployee($employee)
            ->whereBetween('work_date', [$now->copy()->startOfMonth()->toDateString(), $today->toDateString()])
            ->get();
        $lateCount = $monthRecords->where('status', 'late')->count();
        $overtimeMinutes = (int) $monthRecords->sum(fn (AttendanceRecord $record) => max(0, $record->workedMinutes() - (int) config('hrm.expected_daily_minutes')));
        $overtimeHours = round($overtimeMinutes / 60, 1);

        $pendingLeaves = Conges::forEmployee($employee)->where('status', 'pending')->count();
        $pendingDocuments = DocumentRequest::forEmployee($employee)->whereIn('status', ['pending', 'processing'])->count();
        $pendingAdvances = AdvanceRequest::forEmployee($employee)->where('status', 'pending')->count();
        $pendingCount = $pendingLeaves + $pendingDocuments + $pendingAdvances;

        $activity = $this->activity($employee);

        $team = $this->teamWeek($employee, $today);

        $reminders = $this->reminders($employee, $nextLeave, $latestPayslip);

        return view('dashboard', [
            'employee' => $employee,
            'firstName' => preg_split('/\s+/', trim($employee->name))[0] ?? $employee->name,
            'initials' => $employee->initials(),
            'today' => $today,
            'now' => $now,
            'todayRecord' => $todayRecord,
            'leaveToday' => $leaveToday,
            'punchState' => $punchState,
            'elapsedSeconds' => $elapsedSeconds,
            'serverTimestamp' => $now->getTimestamp(),
            'leaveRemaining' => $leaveRemaining,
            'leaveAcquired' => $leaveAcquired,
            'leaveTaken' => $leaveTaken,
            'leaveRatio' => $leaveAcquired > 0 ? min(100, (int) round($leaveRemaining / $leaveAcquired * 100)) : 0,
            'nextLeave' => $nextLeave,
            'latestPayslip' => $latestPayslip,
            'presenceRate' => $presenceRate,
            'lateCount' => $lateCount,
            'overtimeHours' => $overtimeHours,
            'pendingCount' => $pendingCount,
            'activity' => $activity,
            'team' => $team,
            'reminders' => $reminders,
        ]);
    }

    private function punchState(?AttendanceRecord $record, ?Conges $leave): string
    {
        if ($record === null) {
            return $leave ? 'leave' : 'not_started';
        }

        if ($record->status === 'leave') {
            return 'leave';
        }

        if ($record->isOpenSession()) {
            return $record->isOnBreak() ? 'on_break' : 'working';
        }

        if ($record->check_in !== null) {
            return 'done';
        }

        return $leave ? 'leave' : 'not_started';
    }

    private function elapsedSeconds(?AttendanceRecord $record, string $state): int
    {
        if ($record === null || ! in_array($state, ['working', 'on_break', 'done'], true)) {
            return 0;
        }

        return max(0, $record->liveWorkedMinutes()) * 60;
    }

    private function activity(Employee $employee): array
    {
        $items = [];

        foreach (Conges::forEmployee($employee)->orderByDesc('created_at')->take(3)->get() as $leave) {
            $items[] = [
                'icon' => $leave->typeIcon(),
                'title' => 'Demande de '.$leave->typeLabel(),
                'subtitle' => $leave->reference().' • '.$leave->start_date?->format('d/m/Y').' → '.$leave->end_date?->format('d/m/Y'),
                'status' => $leave->statusLabel(),
                'statusClasses' => $this->leaveStatusClasses($leave->status),
                'meta' => 'Soumise le '.$leave->created_at?->locale('fr')->translatedFormat('j M Y H:i'),
                'url' => route('collaborator.conges'),
                'sort' => $leave->created_at?->getTimestamp() ?? 0,
            ];
        }

        foreach (DocumentRequest::forEmployee($employee)->orderByDesc('created_at')->take(2)->get() as $documentRequest) {
            $items[] = [
                'icon' => 'description',
                'title' => $documentRequest->typeLabel(),
                'subtitle' => $documentRequest->reference().' • '.($documentRequest->purpose ?: 'Sans motif précisé'),
                'status' => $documentRequest->statusLabel(),
                'statusClasses' => $documentRequest->statusPillClasses(),
                'meta' => 'Demandée le '.$documentRequest->created_at?->locale('fr')->translatedFormat('j M Y H:i'),
                'url' => route('collaborator.documents'),
                'sort' => $documentRequest->created_at?->getTimestamp() ?? 0,
            ];
        }

        foreach (AdvanceRequest::forEmployee($employee)->orderByDesc('created_at')->take(2)->get() as $advance) {
            $items[] = [
                'icon' => 'savings',
                'title' => 'Acompte sur salaire',
                'subtitle' => $advance->reference().' • '.number_format((float) $advance->amount, 3, ',', ' ').' TND',
                'status' => $advance->statusLabel(),
                'statusClasses' => $advance->statusPillClasses(),
                'meta' => 'Demandé le '.$advance->created_at?->locale('fr')->translatedFormat('j M Y H:i'),
                'url' => route('collaborator.payslips'),
                'sort' => $advance->created_at?->getTimestamp() ?? 0,
            ];
        }

        usort($items, fn (array $a, array $b) => $b['sort'] <=> $a['sort']);

        return array_slice($items, 0, 4);
    }

    private function leaveStatusClasses(string $status): string
    {
        return match ($status) {
            'approved' => 'bg-tertiary-fixed text-on-tertiary-fixed',
            'rejected' => 'bg-error-container text-on-error-container',
            'cancelled' => 'bg-surface-container text-on-surface-variant',
            default => 'bg-secondary-container text-on-secondary-fixed',
        };
    }

    private function teamWeek(Employee $employee, Carbon $today): array
    {
        $start = $today->copy()->startOfWeek(Carbon::MONDAY);
        $days = collect(range(0, 4))->map(fn (int $index) => $start->copy()->addDays($index));

        $colleagues = Employee::where('department', $employee->department)
            ->where('id', '!=', $employee->id)
            ->where('status', '!=', 'inactif')
            ->orderBy('name')
            ->take(3)
            ->get();

        $people = collect([$employee])->merge($colleagues);
        $ids = $people->pluck('id')->all();

        $records = AttendanceRecord::whereIn('employee_id', $ids)
            ->whereBetween('work_date', [$days->first()->toDateString(), $days->last()->toDateString()])
            ->get()
            ->keyBy(fn (AttendanceRecord $record) => $record->employee_id.'|'.$record->work_date?->toDateString());

        $leaves = Conges::whereIn('employee_id', $ids)
            ->whereIn('status', ['approved'])
            ->whereDate('start_date', '<=', $days->last()->toDateString())
            ->whereDate('end_date', '>=', $days->first()->toDateString())
            ->get();

        return [
            'days' => $days->map(fn (Carbon $day) => [
                'date' => $day,
                'label' => ucfirst($day->locale('fr')->translatedFormat('D j')),
                'isToday' => $day->isSameDay($today),
            ])->all(),
            'rows' => $people->map(function (Employee $person) use ($days, $records, $leaves, $employee) {
                return [
                    'employee' => $person,
                    'isSelf' => $person->id === $employee->id,
                    'cells' => $days->map(function (Carbon $day) use ($person, $records, $leaves) {
                        $key = $person->id.'|'.$day->toDateString();
                        $record = $records->get($key);
                        $leave = $leaves->first(fn (Conges $leave) => $leave->employee_id === $person->id
                            && $leave->start_date->lte($day)
                            && $leave->end_date->gte($day));

                        if ($leave) {
                            return $this->cell('Congé', 'bg-surface-container-high text-on-surface-variant');
                        }

                        if ($record && $record->status === 'remote') {
                            return $this->cell('Télétravail', 'bg-secondary-container text-on-secondary-fixed');
                        }

                        if ($record && $record->check_in !== null) {
                            return $this->cell('Site Lac 2', 'bg-primary-container text-on-primary');
                        }

                        if ($record && $record->status === 'leave') {
                            return $this->cell('Congé', 'bg-surface-container-high text-on-surface-variant');
                        }

                        if ($day->isAfter(AttendanceClock::today())) {
                            return $this->cell('—', 'bg-surface-container-lowest text-on-surface-variant');
                        }

                        return $this->cell('Absence', 'bg-error-container/60 text-on-error-container');
                    })->all(),
                ];
            })->all(),
        ];
    }

    private function cell(string $label, string $classes): array
    {
        return ['label' => $label, 'classes' => $classes];
    }

    private function reminders(Employee $employee, ?Conges $nextLeave, ?Payslip $latestPayslip): array
    {
        $reminders = [];

        if ($nextLeave) {
            $reminders[] = [
                'icon' => 'event_available',
                'tone' => 'bg-primary-fixed text-on-primary-fixed',
                'label' => 'Prochain congé',
                'title' => $nextLeave->typeLabel().' approuvé',
                'body' => 'Du '.$nextLeave->start_date?->format('d/m/Y').' au '.$nextLeave->end_date?->format('d/m/Y').' ('.$nextLeave->working_days.' j ouvrés).',
                'url' => route('collaborator.conges'),
            ];
        }

        if ($latestPayslip) {
            $reminders[] = [
                'icon' => 'receipt_long',
                'tone' => 'bg-secondary-container text-on-secondary-fixed',
                'label' => 'Paie',
                'title' => 'Bulletin '.$latestPayslip->periodLabel(),
                'body' => 'Net à payer : '.$latestPayslip->netLabel().' TND. Téléchargeable depuis vos fiches de paie.',
                'url' => route('collaborator.payslips', ['bulletin' => $latestPayslip->id]),
            ];
        }

        $expiring = Document::forEmployee($employee)->whereNotNull('expires_at')
            ->whereDate('expires_at', '>=', AttendanceClock::today())
            ->whereDate('expires_at', '<=', AttendanceClock::today()->addDays(60))
            ->orderBy('expires_at')
            ->first();

        if ($expiring) {
            $reminders[] = [
                'icon' => 'warning',
                'tone' => 'bg-error-container text-on-error-container',
                'label' => 'Document',
                'title' => $expiring->name,
                'body' => 'Expire le '.$expiring->expires_at?->format('d/m/Y').' — pensez à fournir un renouvellement.',
                'url' => route('collaborator.documents'),
            ];
        }

        if ($reminders === []) {
            $reminders[] = [
                'icon' => 'check_circle',
                'tone' => 'bg-tertiary-fixed text-on-tertiary-fixed',
                'label' => 'RH',
                'title' => 'Aucun rappel en attente',
                'body' => 'Votre dossier est à jour. Consultez vos documents pour vérifier les pièces disponibles.',
                'url' => route('collaborator.documents'),
            ];
        }

        return $reminders;
    }
}
