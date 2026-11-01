<?php

namespace App\Http\Controllers\Collaborator;

use App\Models\AttendanceRecord;
use App\Models\Conges;
use App\Models\Employee;
use App\Support\AttendanceClock;
use App\Support\LeaveCancellation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class LeaveController extends CollaboratorController
{
    public function index(Request $request)
    {
        $employee = $this->employee($request);
        $today = AttendanceClock::today();
        $year = $today->year;

        $leaves = Conges::forEmployee($employee)
            ->whereYear('start_date', $year)
            ->orderByDesc('start_date')
            ->orderByDesc('id')
            ->get();

        $takenAnnual = (int) $leaves->where('type', 'annuel')->where('status', 'approved')->sum('working_days');
        $remaining = (int) ($employee->token_leave_days ?? 0);
        $acquired = max((int) ($employee->leave_rights ?? 0), $remaining + $takenAnnual);
        $usageRate = $acquired > 0 ? (int) round($remaining / $acquired * 100) : 0;

        $sickTaken = (int) $leaves->where('type', 'maladie')->where('status', 'approved')->sum('working_days');
        $sickRemaining = max(0, (int) ($employee->token_sick_leave_days ?? 0) - $sickTaken);

        $overtimeMinutes = (int) AttendanceRecord::forEmployee($employee)
            ->whereYear('work_date', $year)
            ->get()
            ->sum(fn (AttendanceRecord $record) => max(0, $record->workedMinutes() - (int) config('hrm.expected_daily_minutes')));
        $recupTaken = (int) $leaves->where('type', 'recup')->where('status', 'approved')->sum('working_days');
        $recupAvailable = max(0, round($overtimeMinutes / 60 / 8 - $recupTaken, 1));

        $statusCounts = [
            'all' => $leaves->count(),
            'pending' => $leaves->where('status', 'pending')->count(),
            'approved' => $leaves->where('status', 'approved')->count(),
            'rejected' => $leaves->where('status', 'rejected')->count(),
            'cancelled' => $leaves->where('status', 'cancelled')->count(),
        ];

        $teamOverlap = $this->teamOverlap($employee, $today);

        return view('collaborator.conges', [
            'employee' => $employee,
            'leaves' => $leaves,
            'year' => $year,
            'remaining' => $remaining,
            'acquired' => $acquired,
            'takenAnnual' => $takenAnnual,
            'usageRate' => $usageRate,
            'sickTaken' => $sickTaken,
            'sickRemaining' => $sickRemaining,
            'recupAvailable' => $recupAvailable,
            'statusCounts' => $statusCounts,
            'teamOverlap' => $teamOverlap,
            'types' => Conges::TYPES,
            'today' => $today,
        ]);
    }

    public function store(Request $request)
    {
        $employee = $this->employee($request);

        $validated = $request->validate([
            'type' => ['required', Rule::in(array_keys(Conges::TYPES))],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'reason' => ['required', 'string', 'max:1000'],
            'attachment' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
        ]);

        $start = Carbon::parse($validated['start_date'])->startOfDay();
        $end = Carbon::parse($validated['end_date'])->startOfDay();
        $today = AttendanceClock::today();

        if ($start->lt($today)) {
            throw ValidationException::withMessages([
                'start_date' => 'La date de début ne peut pas être antérieure à aujourd\'hui.',
            ]);
        }

        $workingDays = Conges::workingDaysFor($start, $end);

        if ($workingDays < 1) {
            throw ValidationException::withMessages([
                'end_date' => 'La période ne contient aucun jour ouvré (dimanche exclu).',
            ]);
        }

        $overlap = Conges::forEmployee($employee)
            ->whereIn('status', ['pending', 'approved'])
            ->whereDate('start_date', '<=', $end->toDateString())
            ->whereDate('end_date', '>=', $start->toDateString())
            ->exists();

        if ($overlap) {
            throw ValidationException::withMessages([
                'start_date' => 'Cette période chevauche une demande déjà déposée ou approuvée.',
            ]);
        }

        $balanceError = $this->balanceError($employee, $validated['type'], $workingDays, $start->year);

        if ($balanceError !== null) {
            throw ValidationException::withMessages(['type' => $balanceError]);
        }

        $attachmentPath = null;
        $attachmentName = null;

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $attachmentPath = $file->store('leave-documents', 'local');
            $attachmentName = $file->getClientOriginalName();
        }

        $leave = Conges::create([
            'employee_id' => $employee->id,
            'employee_name' => $employee->name,
            'type' => $validated['type'],
            'status' => 'pending',
            'start_date' => $start->toDateString(),
            'end_date' => $end->toDateString(),
            'working_days' => $workingDays,
            'reason' => trim($validated['reason']),
            'attachment_path' => $attachmentPath,
            'attachment_name' => $attachmentName,
        ]);

        return redirect()
            ->route('collaborator.conges')
            ->with('success', 'Votre demande de '.$leave->typeLabel().' ('.$workingDays.' j ouvrés) a été transmise pour validation.');
    }

    public function cancel(Request $request, Conges $leave)
    {
        $employee = $this->employee($request);
        $this->assertOwned($leave, $employee);

        if ($leave->isCancelled()) {
            return back()->with('error', 'Cette demande est déjà annulée.');
        }

        if (! $leave->isCancellable()) {
            return back()->with('error', $leave->isApproved()
                ? 'Ce congé est déjà terminé : il ne peut plus être arrêté.'
                : 'Cette demande ne peut plus être annulée.');
        }

        $outcome = LeaveCancellation::stop($leave, 'le collaborateur');

        if ($outcome === null) {
            return back()->with('error', 'Cette demande ne peut plus être annulée.');
        }

        if ($outcome['result'] === LeaveCancellation::INTERRUPTED) {
            return back()->with('success', 'Votre congé a été arrêté. Le solde non consommé ('
                .$outcome['restored_days'].' j) a été réintégré.');
        }

        return back()->with('success', $leave->isApproved()
            ? 'Votre congé approuvé a été annulé. Le solde a été réintégré.'
            : 'Votre demande de congé a été annulée.');
    }

    public function attachment(Request $request, Conges $leave)
    {
        $employee = $this->employee($request);
        $this->assertOwned($leave, $employee);

        abort_unless(
            $leave->attachment_path && Storage::disk('local')->exists($leave->attachment_path),
            404
        );

        return Storage::disk('local')->download(
            $leave->attachment_path,
            $leave->attachment_name ?: basename($leave->attachment_path)
        );
    }

    private function balanceError(Employee $employee, string $type, int $workingDays, int $year): ?string
    {
        if ($type === 'annuel') {
            $remaining = (int) ($employee->token_leave_days ?? 0);

            if ($workingDays > $remaining) {
                return 'Solde de congé annuel insuffisant : '.$remaining.' jour(s) restant(s) pour '.$workingDays.' demandé(s).';
            }
        }

        if ($type === 'maladie') {
            $taken = (int) Conges::forEmployee($employee)
                ->where('type', 'maladie')
                ->where('status', 'approved')
                ->whereYear('start_date', $year)
                ->sum('working_days');
            $remaining = max(0, (int) ($employee->token_sick_leave_days ?? 0) - $taken);

            if ($workingDays > $remaining) {
                return 'Solde maladie insuffisant : '.$remaining.' jour(s) restant(s).';
            }
        }

        if ($type === 'recup') {
            $overtimeMinutes = (int) AttendanceRecord::forEmployee($employee)
                ->whereYear('work_date', $year)
                ->get()
                ->sum(fn (AttendanceRecord $record) => max(0, $record->workedMinutes() - (int) config('hrm.expected_daily_minutes')));
            $recupTaken = (int) Conges::forEmployee($employee)
                ->where('type', 'recup')
                ->where('status', 'approved')
                ->whereYear('start_date', $year)
                ->sum('working_days');
            $available = max(0, (int) floor($overtimeMinutes / 60 / 8) - $recupTaken);

            if ($workingDays > $available) {
                return 'Aucun crédit de récupération suffisant : '.$available.' jour(s) disponible(s).';
            }
        }

        return null;
    }

    private function teamOverlap(Employee $employee, Carbon $today): array
    {
        $windowEnd = $today->copy()->addDays(30);

        $leaves = Conges::with('employee')
            ->where('employee_id', '!=', $employee->id)
            ->where('status', 'approved')
            ->whereDate('start_date', '<=', $windowEnd->toDateString())
            ->whereDate('end_date', '>=', $today->toDateString())
            ->whereHas('employee', fn ($query) => $query->where('department', $employee->department))
            ->orderBy('start_date')
            ->get();

        return [
            'count' => $leaves->count(),
            'employees' => $leaves->pluck('employee')->filter()->unique('id')->take(3)->values(),
            'leaves' => $leaves->take(3),
            'windowEnd' => $windowEnd,
        ];
    }
}
