<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Payslip;
use App\Support\FrenchNumber;
use App\Support\PayslipGenerator;
use App\Support\PayslipPdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PayrollController extends Controller
{
    public function index(Request $request)
    {
        $period = $this->resolvePeriod($request->query('month'));
        $this->ensurePeriodPayslips($period);

        $filters = $this->filters($request);
        $payslips = $this->filteredQuery($period, $filters)
            ->orderBy(Employee::select('name')->whereColumn('employees.id', 'payslips.employee_id'))
            ->paginate(8)
            ->appends(array_filter([
                'month' => $period,
                'q' => $filters['q'],
                'department' => $filters['department'],
                'status' => $filters['status'],
            ]));

        $payslips->getCollection()->transform(fn (Payslip $payslip) => $this->decoratePayslip($payslip));

        $allPeriod = Payslip::with('employee')->where('period', $period)->get();

        $selectedPayslip = $request->filled('view')
            ? Payslip::with('employee')->where('period', $period)->find($request->integer('view'))
            : $payslips->first();

        $selectedPayslip = $selectedPayslip ? $this->decoratePayslip($selectedPayslip) : null;

        $periodDate = Carbon::createFromFormat('Y-m', $period)->startOfMonth();
        $previousPeriod = $periodDate->copy()->subMonth()->format('Y-m');
        $previousNet = (float) Payslip::where('period', $previousPeriod)->sum('net');
        $netTotal = (float) $allPeriod->sum('net');
        $activeEmployees = Employee::where('status', '!=', 'inactif')->count();
        $coveredEmployees = $allPeriod->pluck('employee_id')->unique()->count();
        $readyCount = $allPeriod->where('status', 'ready')->count();
        $reviewCount = $allPeriod->where('status', 'review')->count();
        $paidCount = $allPeriod->where('status', 'paid')->count();
        $periodClosed = $allPeriod->isNotEmpty() && $paidCount === $allPeriod->count();

        $closed = $allPeriod->isNotEmpty() && $allPeriod->every(fn (Payslip $payslip) => $payslip->status === 'paid');

        return view('admin.payroll', [
            'payslips' => $payslips,
            'selectedPayslip' => $selectedPayslip,
            'period' => $period,
            'periodLabel' => ucfirst($periodDate->copy()->locale('fr')->translatedFormat('F Y')),
            'periodLower' => $periodDate->copy()->locale('fr')->translatedFormat('F Y'),
            'periodStart' => $periodDate->copy()->startOfMonth(),
            'periodEnd' => $periodDate->copy()->endOfMonth(),
            'cycleId' => 'TN-PAY-'.$periodDate->format('Y-m'),
            'deadline' => $periodDate->copy()->day(28),
            'irppDeadline' => $periodDate->copy()->addMonthNoOverflow()->day(28),
            'filters' => $filters,
            'netTotal' => $netTotal,
            'cnssTotal' => (float) $allPeriod->sum('cnss'),
            'employerCnssTotal' => (float) $allPeriod->sum(fn (Payslip $payslip) => $payslip->employerCnss()),
            'irppTotal' => (float) $allPeriod->sum('irpp'),
            'growthRate' => $previousNet > 0 ? round(($netTotal - $previousNet) / $previousNet * 100, 1) : null,
            'activeEmployees' => $activeEmployees,
            'coveredEmployees' => $coveredEmployees,
            'coverageRate' => $activeEmployees > 0 ? round($coveredEmployees / $activeEmployees * 100) : 100,
            'readyCount' => $readyCount,
            'reviewCount' => $reviewCount,
            'paidCount' => $paidCount,
            'periodClosed' => $closed,
            'periodClosedCount' => $paidCount,
            'payslipCount' => $allPeriod->count(),
        ]);
    }

    public function close(Request $request)
    {
        $period = $this->resolvePeriod($request->input('period'));

        $updated = Payslip::where('period', $period)
            ->where('status', '!=', 'paid')
            ->update(['status' => 'paid', 'paid_at' => now()]);

        if ($updated === 0) {
            return back()->with('error', 'Le cycle de paie est déjà clôturé.');
        }

        return back()->with('success', 'Le cycle de paie a été clôturé : '.$updated.' bulletin(s) marqué(s) payé(s).');
    }

    public function exportJournal(Request $request): StreamedResponse
    {
        $period = $this->resolvePeriod($request->query('month'));
        $this->ensurePeriodPayslips($period);
        $payslips = $this->periodPayslips($period);

        return response()->streamDownload(function () use ($payslips, $period) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, [
                'Journal de paie',
                'Période',
                'Matricule',
                'Collaborateur',
                'CIN',
                'Département',
                'Salaire de base',
                'Primes',
                'Indemnité transport',
                'Brut',
                'CNSS salariale 9.18%',
                'Base imposable',
                'IRPP',
                'CSS 1%',
                'Net à payer',
                'Banque',
                'RIB',
                'Statut',
            ], ';');

            foreach ($payslips as $payslip) {
                fputcsv($handle, [
                    'Bulletin de paie',
                    $period,
                    $payslip->matricule(),
                    $payslip->employee?->name,
                    $payslip->employee?->cin,
                    $payslip->employee?->departmentLabel(),
                    number_format((float) $payslip->base_salary, 3, '.', ''),
                    number_format((float) $payslip->prime, 3, '.', ''),
                    number_format((float) $payslip->transport_allowance, 3, '.', ''),
                    number_format($payslip->gross(), 3, '.', ''),
                    number_format((float) $payslip->cnss, 3, '.', ''),
                    number_format((float) $payslip->taxable_base, 3, '.', ''),
                    number_format((float) $payslip->irpp, 3, '.', ''),
                    number_format((float) $payslip->css, 3, '.', ''),
                    number_format((float) $payslip->net, 3, '.', ''),
                    $payslip->bank,
                    $payslip->rib,
                    $payslip->statusLabel(),
                ], ';');
            }

            fclose($handle);
        }, 'journal-paie-'.$period.'.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function exportCnss(Request $request): StreamedResponse
    {
        $period = $this->resolvePeriod($request->query('month'));
        $this->ensurePeriodPayslips($period);
        $payslips = $this->periodPayslips($period);

        return response()->streamDownload(function () use ($payslips, $period) {
            $totalBase = 0.0;
            $totalEmployee = 0.0;
            $totalEmployer = 0.0;

            echo "CNSS-TN;TELEDECLARATION;PERIODE=".$period."\n";
            echo "CNSS_EMPLOYEUR=29481900-12;RAISON_SOCIALE=CARTHAGE TECH SOLUTIONS SARL\n";
            echo "MATRICULE;NOM;CIN;N_CNSS;BASE;PART_SALARIALE_9_18;PART_PATRONALE_17_37;STATUT\n";

            foreach ($payslips as $payslip) {
                $base = $payslip->gross();
                $employer = $payslip->employerCnss();
                $totalBase += $base;
                $totalEmployee += (float) $payslip->cnss;
                $totalEmployer += $employer;

                echo implode(';', [
                    $payslip->matricule(),
                    $payslip->employee?->name,
                    $payslip->employee?->cin,
                    $payslip->employee?->cnss_number,
                    number_format($base, 3, '.', ''),
                    number_format((float) $payslip->cnss, 3, '.', ''),
                    number_format($employer, 3, '.', ''),
                    strtoupper($payslip->status),
                ])."\n";
            }

            echo implode(';', [
                'TOTAL',
                '',
                '',
                '',
                number_format($totalBase, 3, '.', ''),
                number_format($totalEmployee, 3, '.', ''),
                number_format($totalEmployer, 3, '.', ''),
                '',
            ])."\n";
        }, 'tele-declaration-cnss-'.$period.'.txt', [
            'Content-Type' => 'text/plain; charset=UTF-8',
        ]);
    }

    public function exportBank(Request $request): StreamedResponse
    {
        $period = $this->resolvePeriod($request->query('month'));
        $this->ensurePeriodPayslips($period);
        $payslips = $this->periodPayslips($period);
        $deadline = Carbon::createFromFormat('Y-m', $period)->day(28);

        return response()->streamDownload(function () use ($payslips, $period, $deadline) {
            echo "VIREMENTS SALAIRES - CARTHAGE TECH SOLUTIONS SARL\n";
            echo "PERIODE=".$period.';DATE_EXECUTION='.$deadline->format('Y-m-d').";EMETTEUR=Amen Bank / BIAT\n";
            echo "BENEFICIAIRE;BANQUE;RIB;MONTANT_TND;REFERENCE\n";

            $total = 0.0;

            foreach ($payslips as $payslip) {
                $total += (float) $payslip->net;
                echo implode(';', [
                    $payslip->employee?->name,
                    $payslip->bank ?: 'BANQUE',
                    $payslip->rib ?: str_repeat('0', 20),
                    number_format((float) $payslip->net, 3, '.', ''),
                    'PAIE-'.$period.'-'.$payslip->matricule(),
                ])."\n";
            }

            echo 'TOTAL;'.$payslips->count().' VIREMENTS;;'.number_format($total, 3, '.', '').";\n";
        }, 'virements-banques-'.$period.'.txt', [
            'Content-Type' => 'text/plain; charset=UTF-8',
        ]);
    }

    public function downloadPayslip(Payslip $payslip)
    {
        $payslip->load('employee');
        $label = ucfirst(
            Carbon::createFromFormat('Y-m', $payslip->period)->startOfMonth()->locale('fr')->translatedFormat('F Y')
        );
        $pdf = PayslipPdf::render($payslip, $label);

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="bulletin-'.$payslip->matricule().'-'.$payslip->period.'.pdf"',
        ]);
    }

    private function filteredQuery(string $period, array $filters)
    {
        return Payslip::query()
            ->with('employee')
            ->where('period', $period)
            ->when($filters['q'] !== '', function ($query) use ($filters) {
                $term = '%'.$filters['q'].'%';

                $query->whereHas('employee', function ($query) use ($term) {
                    $query->where('name', 'like', $term)
                        ->orWhere('cin', 'like', $term)
                        ->orWhere('cnss_number', 'like', $term)
                        ->orWhere('email', 'like', $term);
                });
            })
            ->when(
                array_key_exists($filters['department'], Employee::DEPARTMENTS),
                fn ($query) => $query->whereHas(
                    'employee',
                    fn ($query) => $query->where('department', $filters['department'])
                )
            )
            ->when(
                array_key_exists($filters['status'], Payslip::STATUSES),
                fn ($query) => $query->where('status', $filters['status'])
            );
    }

    private function ensurePeriodPayslips(string $period): void
    {
        PayslipGenerator::ensureForPeriod($period);
    }

    private function periodPayslips(string $period)
    {
        return Payslip::with('employee')
            ->where('period', $period)
            ->get()
            ->sortBy(fn (Payslip $payslip) => $payslip->employee?->name)
            ->values();
    }

    private function decoratePayslip(Payslip $payslip): Payslip
    {
        $payslip->setAttribute('payload', $this->slipPayload($payslip));

        return $payslip;
    }

    private function slipPayload(Payslip $payslip): array
    {
        $employee = $payslip->employee;
        $base = (float) $payslip->base_salary;
        $category = $base >= 4000 ? 7 : ($base >= 3200 ? 6 : ($base >= 2600 ? 5 : 4));

        return [
            'id' => $payslip->id,
            'name' => $employee?->name ?? 'Collaborateur',
            'cin' => ($employee?->cin ?: '—').' • '.$payslip->matricule(),
            'role' => ($employee?->position ?: '—').' • Cat. '.$category,
            'base' => $this->money($base).' TND',
            'prime' => '+'.$this->money((float) $payslip->prime).' TND',
            'transport' => '+'.$this->money((float) $payslip->transport_allowance).' TND',
            'cnss' => '-'.$this->money((float) $payslip->cnss).' TND',
            'taxBase' => $this->money((float) $payslip->taxable_base).' TND',
            'irpp' => '-'.$this->money((float) $payslip->irpp).' TND',
            'css' => '-'.$this->money((float) $payslip->css).' TND',
            'net' => $this->money((float) $payslip->net),
            'words' => FrenchNumber::amountInWords((float) $payslip->net),
            'bank' => $payslip->bank ?: '—',
            'rib' => $payslip->rib ? 'RIB: '.$payslip->rib : 'RIB: —',
            'downloadUrl' => route('admin.payroll.payslip', $payslip),
        ];
    }

    private function money(float $value): string
    {
        return number_format($value, 3, '.', ',');
    }

    private function filters(Request $request): array
    {
        return [
            'q' => trim((string) $request->query('q', '')),
            'department' => (string) $request->query('department', ''),
            'status' => (string) $request->query('status', ''),
        ];
    }

    private function resolvePeriod(?string $value): string
    {
        $value = (string) $value;

        return preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $value) ? $value : now()->format('Y-m');
    }
}
