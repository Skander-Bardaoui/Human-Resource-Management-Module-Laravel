<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Conges;
use App\Models\Employee;
use App\Support\LeaveCancellation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LeaveController extends Controller
{
    public function index(Request $request)
    {
        $filters = $this->filters($request);
        $display = $request->query('display') === 'calendar' ? 'calendar' : 'table';
        $selectedMonth = Carbon::createFromFormat('Y-m', $filters['month'])->startOfMonth();

        $leaves = $this->filteredQuery($filters)
            ->with('employee')
            ->orderByDesc('start_date')
            ->orderByDesc('id')
            ->paginate(5)
            ->appends(array_filter($filters, fn ($value) => $value !== ''));

        $viewLeave = $request->filled('view')
            ? Conges::with(['employee', 'reviewer'])->find($request->integer('view'))
            : null;

        $calendarLeaves = $display === 'calendar'
            ? $this->filteredQuery($filters)->with('employee')->orderBy('start_date')->get()
            : collect();

        $pendingLeaves = Conges::with('employee')
            ->where('status', 'pending')
            ->orderBy('start_date')
            ->get();

        $pendingCount = $pendingLeaves->count();
        $pendingAvatars = $pendingLeaves->pluck('employee')->filter()->unique('id')->take(3);

        $absentToday = Conges::where('status', 'approved')
            ->whereDate('start_date', '<=', today())
            ->whereDate('end_date', '>=', today())
            ->get(['employee_id', 'employee_name'])
            ->map(fn ($leave) => $leave->employee_id ? 'id:'.$leave->employee_id : 'name:'.$leave->employee_name)
            ->unique()
            ->count();

        $activeCount = Employee::where('status', '!=', 'inactif')->count();
        $presenceRate = $activeCount > 0
            ? round(max(0, $activeCount - $absentToday) / $activeCount * 100, 1)
            : null;

        $averageBalance = (float) (Employee::where('status', '!=', 'inactif')
            ->whereNotNull('token_leave_days')
            ->avg('token_leave_days') ?? 0);
        $averageRights = (float) (Employee::where('status', '!=', 'inactif')
            ->where('leave_rights', '>', 0)
            ->avg('leave_rights') ?? 30);
        $acquiredRate = $averageRights > 0
            ? (int) round(min(100, max(0, $averageBalance / $averageRights * 100)))
            : 0;

        $employees = Employee::where('status', '!=', 'inactif')->orderBy('name')->get();

        return view('admin.leaves', compact(
            'leaves',
            'filters',
            'display',
            'selectedMonth',
            'viewLeave',
            'calendarLeaves',
            'pendingCount',
            'pendingAvatars',
            'absentToday',
            'presenceRate',
            'averageBalance',
            'acquiredRate',
            'employees',
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => ['required', 'integer', 'exists:employees,id'],
            'type' => ['required', Rule::in(array_keys(Conges::TYPES))],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'reason' => ['required', 'string', 'max:1000'],
            'attachment' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
        ]);

        $employee = Employee::findOrFail($validated['employee_id']);
        $start = Carbon::parse($validated['start_date']);
        $end = Carbon::parse($validated['end_date']);
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
            'working_days' => Conges::workingDaysFor($start, $end),
            'reason' => trim($validated['reason']),
            'attachment_path' => $attachmentPath,
            'attachment_name' => $attachmentName,
        ]);

        return redirect()
            ->route('admin.leaves', ['month' => $start->format('Y-m')])
            ->with('success', 'La demande de congé de '.$leave->employeeName().' a été soumise pour validation.');
    }

    public function approve(Request $request, Conges $leave)
    {
        if (! $leave->isPending()) {
            return back()->with('error', 'Cette demande a déjà été traitée.');
        }

        DB::transaction(function () use ($leave, $request) {
            $leave->update([
                'status' => 'approved',
                'reviewed_at' => now(),
                'reviewed_by' => $request->user()->id,
                'decision_note' => null,
            ]);

            if ($leave->type === 'annuel' && $leave->employee) {
                $employee = $leave->employee;

                $employee->update([
                    'token_leave_days' => max(
                        0,
                        (int) $employee->token_leave_days - (int) $leave->working_days
                    ),
                ]);
            }
        });

        return back()->with('success', 'La demande de '.$leave->employeeName().' a été approuvée.');
    }

    public function stop(Request $request, Conges $leave)
    {
        if (! $leave->isApproved()) {
            return back()->with('error', 'Seul un congé déjà approuvé peut être arrêté.');
        }

        if (! $leave->isCancellable()) {
            return back()->with('error', 'Ce congé est déjà terminé : il ne peut plus être arrêté.');
        }

        $outcome = LeaveCancellation::stop($leave, 'l\'administration', $request->user()->id);

        if ($outcome === null) {
            return back()->with('error', 'Ce congé ne peut plus être arrêté.');
        }

        $message = $outcome['result'] === LeaveCancellation::INTERRUPTED
            ? 'Le congé de '.$leave->employeeName().' a été arrêté. '.$outcome['restored_days'].' jour(s) réintégré(s).'
            : 'Le congé de '.$leave->employeeName().' a été annulé. '.$outcome['restored_days'].' jour(s) réintégré(s).';

        return back()->with('success', $message);
    }

    public function reject(Request $request, Conges $leave)
    {
        if (! $leave->isPending()) {
            return back()->with('error', 'Cette demande a déjà été traitée.');
        }

        $validated = $request->validate([
            'decision_note' => ['nullable', 'string', 'max:500'],
        ]);

        $leave->update([
            'status' => 'rejected',
            'reviewed_at' => now(),
            'reviewed_by' => $request->user()->id,
            'decision_note' => $validated['decision_note'] ?? null,
        ]);

        return back()->with('success', 'La demande de '.$leave->employeeName().' a été refusée.');
    }

    public function export(Request $request): StreamedResponse
    {
        $leaves = $this->filteredQuery($this->filters($request))
            ->with('employee')
            ->orderByDesc('start_date')
            ->get();

        return response()->streamDownload(function () use ($leaves) {
            $handle = fopen('php://output', 'w');

            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, [
                'Collaborateur',
                'CIN',
                'Département',
                'Type de congé',
                'Début',
                'Fin',
                'Durée (jours ouvrés)',
                'Statut',
                'Motif',
                'Justificatif',
                'Solde actuel',
            ], ';');

            foreach ($leaves as $leave) {
                fputcsv($handle, [
                    $leave->employeeName(),
                    $leave->employeeCin(),
                    $leave->employeeDepartment(),
                    $leave->typeLabel(),
                    $leave->start_date?->format('d/m/Y'),
                    $leave->end_date?->format('d/m/Y'),
                    $leave->working_days,
                    $leave->statusLabel(),
                    $leave->reason,
                    $leave->attachment_name,
                    $leave->balanceDays(),
                ], ';');
            }

            fclose($handle);
        }, 'conges-'.now()->format('Y-m-d').'.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function downloadAttachment(Conges $leave)
    {
        abort_unless(
            $leave->attachment_path && Storage::disk('local')->exists($leave->attachment_path),
            404
        );

        return Storage::disk('local')->download(
            $leave->attachment_path,
            $leave->attachment_name ?: basename($leave->attachment_path)
        );
    }

    private function filters(Request $request): array
    {
        $month = (string) $request->query('month', now()->format('Y-m'));

        if (! preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $month)) {
            $month = now()->format('Y-m');
        }

        return [
            'q' => trim((string) $request->query('q', '')),
            'type' => (string) $request->query('type', ''),
            'status' => (string) $request->query('status', ''),
            'department' => (string) $request->query('department', ''),
            'month' => $month,
        ];
    }

    private function filteredQuery(array $filters)
    {
        $start = Carbon::createFromFormat('Y-m', $filters['month'])->startOfMonth();
        $end = $start->copy()->endOfMonth();

        return Conges::query()
            ->when($filters['q'] !== '', function ($query) use ($filters) {
                $term = '%'.$filters['q'].'%';

                $query->where(function ($query) use ($term) {
                    $query->where('employee_name', 'like', $term)
                        ->orWhere('reason', 'like', $term)
                        ->orWhereHas('employee', function ($query) use ($term) {
                            $query->where('name', 'like', $term)
                                ->orWhere('cin', 'like', $term)
                                ->orWhere('email', 'like', $term);
                        });
                });
            })
            ->when(
                array_key_exists($filters['type'], Conges::TYPES),
                fn ($query) => $query->where('type', $filters['type'])
            )
            ->when(
                array_key_exists($filters['status'], Conges::STATUSES),
                fn ($query) => $query->where('status', $filters['status'])
            )
            ->when(
                array_key_exists($filters['department'], Employee::DEPARTMENTS),
                fn ($query) => $query->whereHas(
                    'employee',
                    fn ($query) => $query->where('department', $filters['department'])
                )
            )
            ->whereDate('start_date', '<=', $end->toDateString())
            ->whereDate('end_date', '>=', $start->toDateString());
    }
}
