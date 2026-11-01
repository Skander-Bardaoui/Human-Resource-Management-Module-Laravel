<?php

namespace App\Http\Controllers\Collaborator;

use App\Models\AdvanceRequest;
use App\Models\Payslip;
use App\Support\AttendanceClock;
use App\Support\PayrollCalculator;
use App\Support\PayslipGenerator;
use App\Support\PayslipPdf;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PayslipController extends CollaboratorController
{
    public function index(Request $request)
    {
        $employee = $this->employee($request);
        $now = AttendanceClock::now();

        PayslipGenerator::ensureFor($employee, $now->format('Y-m'));
        PayslipGenerator::ensureFor($employee, $now->copy()->subMonthNoOverflow()->format('Y-m'));

        $allPayslips = Payslip::forEmployee($employee)->orderByDesc('period')->get();

        $years = $allPayslips->map(fn (Payslip $payslip) => (int) substr((string) $payslip->period, 0, 4))
            ->push($now->year)
            ->unique()
            ->sortDesc()
            ->values();

        $selectedYear = (int) $request->query('annee', $now->year);

        if (! $years->contains($selectedYear)) {
            $selectedYear = (int) $years->first();
        }

        $list = $allPayslips->filter(fn (Payslip $payslip) => (int) substr((string) $payslip->period, 0, 4) === $selectedYear)->values();

        $selected = null;

        if ($request->filled('bulletin')) {
            $selected = Payslip::with('employee')->findOrFail($request->integer('bulletin'));
            $this->assertOwned($selected, $employee);

            $selectedYear = (int) substr((string) $selected->period, 0, 4);
            $list = $allPayslips->filter(fn (Payslip $payslip) => (int) substr((string) $payslip->period, 0, 4) === $selectedYear)->values();
        } else {
            $selected = $list->first();
        }

        $latest = $allPayslips->first();
        $maxAdvance = $latest ? round((float) $latest->net * (float) config('hrm.advance_max_ratio'), 3) : 0.0;
        $minAdvance = (float) config('hrm.advance_min_amount');
        $currentPeriod = $now->format('Y-m');

        $hasOpenAdvance = AdvanceRequest::forEmployee($employee)
            ->where('period', $currentPeriod)
            ->whereIn('status', AdvanceRequest::OPEN_STATUSES)
            ->exists();

        $advances = AdvanceRequest::forEmployee($employee)->orderByDesc('created_at')->take(10)->get();

        return view('collaborator.payslips', [
            'employee' => $employee,
            'payslips' => $allPayslips,
            'years' => $years,
            'selectedYear' => $selectedYear,
            'list' => $list,
            'selected' => $selected,
            'latest' => $latest,
            'maxAdvance' => $maxAdvance,
            'minAdvance' => $minAdvance,
            'advanceRatio' => (int) round((float) config('hrm.advance_max_ratio') * 100),
            'hasOpenAdvance' => $hasOpenAdvance,
            'currentPeriodLabel' => ucfirst($now->locale('fr')->translatedFormat('F Y')),
            'advances' => $advances,
            'fiscalParts' => PayrollCalculator::familyHalfParts($employee) / 2,
            'cnssRate' => ((int) config('payroll.cnss.employee_rate_bp', 918)) / 10000,
        ]);
    }

    public function download(Request $request, Payslip $payslip)
    {
        $employee = $this->employee($request);
        $this->assertOwned($payslip, $employee);

        $payslip->load('employee');
        $pdf = PayslipPdf::render($payslip, $payslip->periodLabel());

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="bulletin-'.$payslip->matricule().'-'.$payslip->period.'.pdf"',
        ]);
    }

    public function advanceStore(Request $request)
    {
        $employee = $this->employee($request);

        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:'.(float) config('hrm.advance_min_amount')],
            'reason' => ['nullable', 'string', 'max:255'],
        ]);

        $latest = Payslip::forEmployee($employee)->orderByDesc('period')->first();

        if ($latest === null) {
            return back()->withInput()->with('error', 'Aucun bulletin de paie disponible pour calculer le plafond d\'acompte.');
        }

        $max = round((float) $latest->net * (float) config('hrm.advance_max_ratio'), 3);

        if (round((float) $validated['amount'], 3) > $max) {
            throw ValidationException::withMessages([
                'amount' => 'Le montant dépasse le plafond autorisé de '.number_format($max, 3, ',', ' ').' TND ('
                    .(int) round((float) config('hrm.advance_max_ratio') * 100).'% du net).',
            ]);
        }

        $period = AttendanceClock::now()->format('Y-m');

        $open = AdvanceRequest::forEmployee($employee)
            ->where('period', $period)
            ->whereIn('status', AdvanceRequest::OPEN_STATUSES)
            ->exists();

        if ($open) {
            return back()->withInput()->with('error', 'Une demande d\'acompte est déjà en cours pour '.ucfirst(AttendanceClock::now()->locale('fr')->translatedFormat('F Y')).'.');
        }

        AdvanceRequest::create([
            'employee_id' => $employee->id,
            'period' => $period,
            'amount' => round((float) $validated['amount'], 3),
            'reason' => filled($validated['reason'] ?? null) ? trim($validated['reason']) : null,
            'status' => 'pending',
        ]);

        return redirect()
            ->route('collaborator.payslips', ['annee' => AttendanceClock::now()->year])
            ->with('success', 'Votre demande d\'acompte de '.number_format((float) $validated['amount'], 3, ',', ' ').' TND a été transmise à la DAF.');
    }
}
