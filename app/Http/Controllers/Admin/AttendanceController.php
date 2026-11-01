<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AttendanceIndexRequest;
use App\Http\Requests\Admin\AttendanceJustificationRequest;
use App\Http\Requests\Admin\AttendancePunchRequest;
use App\Http\Requests\Admin\AttendanceResolveAllRequest;
use App\Models\AttendanceRecord;
use App\Services\AttendanceService;
use App\Support\CsvExport;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AttendanceController extends Controller
{
    public function __construct(private readonly AttendanceService $attendance)
    {
    }

    public function index(AttendanceIndexRequest $request)
    {
        $date = $this->attendance->resolveDate($request->dateValue());
        $filters = $request->filters();

        return view('admin.attendance', array_merge(
            $this->attendance->overview($date, $request->employeeId(), $filters),
            ['filters' => $filters]
        ));
    }

    public function punch(AttendancePunchRequest $request)
    {
        $validated = $request->validated();
        $result = $this->attendance->punch($validated, $request->user());
        $date = $this->attendance->resolveDate($validated['date']);

        return redirect()
            ->route('admin.attendance', ['date' => $date->toDateString()])
            ->with('success', $result['changed']
                ? 'Pointage enregistré pour '.$result['employee']->name.'.'
                : 'Aucune modification : ce pointage existait déjà à cette heure.');
    }

    public function resolveAnomaly(AttendanceRecord $record)
    {
        $regularized = $this->attendance->resolve($record);
        $name = $record->employee?->name ?? 'le collaborateur';

        return back()->with('success', $regularized
            ? 'Départ régularisé à '.substr(AttendanceRecord::scheduleEndTime(), 0, 5).' pour '.$name.'.'
            : 'Anomalie marquée comme traitée pour '.$name.'.');
    }

    public function justifyAnomaly(AttendanceJustificationRequest $request, AttendanceRecord $record)
    {
        $this->attendance->justify(
            $record,
            (string) $request->validated('note'),
            $request->user()
        );

        return back()->with(
            'success',
            'Justificatif enregistré pour '.($record->employee?->name ?? 'le collaborateur').'.'
        );
    }

    public function ignoreAnomaly(AttendanceRecord $record)
    {
        $this->attendance->ignore($record);

        return back()->with(
            'success',
            'Anomalie ignorée pour '.($record->employee?->name ?? 'le collaborateur').'.'
        );
    }

    public function resolveAll(AttendanceResolveAllRequest $request)
    {
        $date = $this->attendance->resolveDate($request->validated('date'));
        $count = $this->attendance->resolveAll($date);

        return back()->with(
            'success',
            $count > 0
                ? $count.' anomalie(s) de pointage traitée(s).'
                : 'Aucune anomalie à traiter pour cette date.'
        );
    }

    public function export(AttendanceIndexRequest $request): StreamedResponse
    {
        $date = $this->attendance->resolveDate($request->dateValue());
        $employeeId = $request->employeeId();
        $rows = $this->attendance->exportRows($date, $employeeId, $request->filters());

        return CsvExport::response(
            'pointages-'.($employeeId !== null ? $date->format('Y-m') : $date->format('Y-m-d')).'.csv',
            [
                'Date',
                'Collaborateur',
                'Matricule',
                'Département',
                'Arrivée',
                'Départ',
                'Pause (min)',
                'Total effectué',
                'Mode de pointage',
                'Statut',
                'Notes RH',
            ],
            $rows
        );
    }
}
