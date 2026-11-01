<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Data\DepartmentSummary;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DepartmentIndexRequest;
use App\Http\Requests\Admin\DepartmentRequest;
use App\Models\Department;
use App\Models\Employee;
use App\Services\DepartmentService;
use App\Support\CsvExport;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DepartmentController extends Controller
{
    public function __construct(private readonly DepartmentService $departments)
    {
    }

    public function index(DepartmentIndexRequest $request)
    {
        $filters = $request->filters();
        $period = $this->currentPeriod();

        return view('admin.departments', [
            'departments' => $this->departments->paginate($filters, $period),
            'filters' => $filters,
            'totals' => $this->departments->totals($period),
            'employees' => Employee::query()
                ->orderBy('name')
                ->get(['id', 'name', 'position', 'status']),
            'period' => $period,
            'periodLabel' => $this->periodLabel($period),
        ]);
    }

    public function store(DepartmentRequest $request)
    {
        $department = $this->departments->create($request->validated());

        return redirect()
            ->route('admin.departments')
            ->with('success', 'Le département « '.$department->name.' » a été créé.');
    }

    public function update(DepartmentRequest $request, Department $department)
    {
        $this->departments->update($department, $request->validated());

        return redirect()
            ->route('admin.departments')
            ->with('success', 'Le département « '.$department->name.' » a été mis à jour.');
    }

    public function destroy(Department $department)
    {
        if (array_key_exists($department->slug, Employee::DEPARTMENTS)) {
            return redirect()
                ->route('admin.departments')
                ->with('error', 'Le département « '.$department->name.' » est une structure utilisée par le module Collaborateurs et ne peut pas être supprimé.');
        }

        $attached = $department->employees()->count();

        if ($attached > 0) {
            $message = $attached === 1
                ? 'Suppression impossible : 1 collaborateur est encore rattaché au département « '.$department->name.' ». Réaffectez-le avant de supprimer.'
                : 'Suppression impossible : '.$attached.' collaborateurs sont encore rattachés au département « '.$department->name.' ». Réaffectez-les avant de supprimer.';

            return redirect()
                ->route('admin.departments')
                ->with('error', $message);
        }

        $department->delete();

        return redirect()
            ->route('admin.departments')
            ->with('success', 'Le département « '.$department->name.' » a été supprimé.');
    }

    public function export(DepartmentIndexRequest $request): StreamedResponse
    {
        $period = $this->currentPeriod();

        $rows = $this->departments
            ->summaries($request->filters(), $period)
            ->map(function (DepartmentSummary $summary) {
                $department = $summary->department;

                return [
                    CsvExport::text($department->name),
                    CsvExport::text($department->code),
                    CsvExport::text($department->manager?->name),
                    $summary->headcount,
                    $summary->gross->toCsv(),
                    $summary->budget->toCsv(),
                    $summary->variance()?->toCsv() ?? '',
                    CsvExport::text($summary->budgetStatus->label()),
                ];
            });

        return CsvExport::response(
            'departements-'.now('Africa/Tunis')->format('Y-m-d').'.csv',
            [
                'Département',
                'Code analytique',
                'Responsable',
                'Effectif actif',
                'Masse salariale brute (TND)',
                'Budget mensuel (TND)',
                'Écart budgétaire (TND)',
                'Statut',
            ],
            $rows
        );
    }

    private function currentPeriod(): string
    {
        return now('Africa/Tunis')->format('Y-m');
    }

    private function periodLabel(string $period): string
    {
        return Carbon::createFromFormat('Y-m', $period)
            ->locale('fr')
            ->translatedFormat('F Y');
    }
}
