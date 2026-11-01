<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PayrollIndexRequest;
use App\Http\Requests\Admin\PayrollPeriodRequest;
use App\Models\Payslip;
use App\Services\PayrollService;
use App\Support\CsvExport;
use App\Support\PayrollCalculator;
use App\Support\PayslipPdf;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PayrollController extends Controller
{
    public function __construct(private readonly PayrollService $payroll)
    {
    }

    public function index(PayrollIndexRequest $request)
    {
        return view('admin.payroll', $this->payroll->overview(
            $request->period(),
            $request->filters(),
            $request->viewId()
        ));
    }

    public function generate(PayrollPeriodRequest $request): RedirectResponse
    {
        $period = $request->period();
        $result = $this->payroll->generate($period);

        if ($result['created'] === 0) {
            return redirect()
                ->route('admin.payroll', ['month' => $period])
                ->with('success', 'Tous les bulletins de '.$this->payroll->periodLabel($period).' sont déjà générés.');
        }

        return redirect()
            ->route('admin.payroll', ['month' => $period])
            ->with('success', $result['created'].' bulletin(s) généré(s) pour '.$this->payroll->periodLabel($period).'.');
    }

    public function close(PayrollPeriodRequest $request): RedirectResponse
    {
        $period = $request->period();
        $result = $this->payroll->close($period);
        $label = $this->payroll->periodLabel($period);

        return match ($result['reason']) {
            'empty' => redirect()->route('admin.payroll', ['month' => $period])
                ->with('error', 'Aucun bulletin pour '.$label.'. Générez les bulletins avant de clôturer.'),
            'missing' => redirect()->route('admin.payroll', ['month' => $period])
                ->with('error', 'Clôture impossible : '.$result['missing'].' collaborateur(s) actif(s) n\'ont pas de bulletin pour '.$label.'.'),
            'already' => redirect()->route('admin.payroll', ['month' => $period])
                ->with('error', 'Le cycle de paie de '.$label.' est déjà clôturé.'),
            default => redirect()->route('admin.payroll', ['month' => $period])
                ->with('success', 'Cycle de '.$label.' clôturé : '.$result['updated'].' bulletin(s) marqué(s) payé(s).'),
        };
    }

    public function exportJournal(PayrollIndexRequest $request): StreamedResponse|RedirectResponse
    {
        $period = $request->period();
        $rows = $this->payroll->journalRows($period, $request->filters());

        if ($rows->isEmpty()) {
            return redirect()
                ->route('admin.payroll', ['month' => $period])
                ->with('error', 'Aucun bulletin à exporter pour les filtres sélectionnés.');
        }

        return CsvExport::response(
            'journal-paie-'.$period.'.csv',
            $this->journalHeadings(),
            $rows
        );
    }

    public function exportCnss(PayrollIndexRequest $request): StreamedResponse|RedirectResponse
    {
        $period = $request->period();
        $rows = $this->payroll->cnssRows($period, $request->filters());

        if ($rows->count() <= 1) {
            return redirect()
                ->route('admin.payroll', ['month' => $period])
                ->with('error', 'Aucun bulletin à exporter pour les filtres sélectionnés.');
        }

        return CsvExport::response(
            'cnss-'.$period.'.csv',
            [
                'Matricule',
                'Collaborateur',
                'CIN',
                'N° CNSS',
                'Brut',
                'CNSS salariale ('.PayrollCalculator::rateLabel((int) config('payroll.cnss.employee_rate_bp', 918)).')',
                'CNSS patronale ('.PayrollCalculator::rateLabel((int) config('payroll.cnss.employer_rate_bp', 1737)).')',
                'Statut',
            ],
            $rows
        );
    }

    public function exportBank(PayrollIndexRequest $request): StreamedResponse|RedirectResponse
    {
        $period = $request->period();
        $rows = $this->payroll->bankRows($period, $request->filters());

        if ($rows->isEmpty()) {
            return redirect()
                ->route('admin.payroll', ['month' => $period])
                ->with('error', 'Aucun virement au statut « Virement Prêt » avec un RIB renseigné pour cette période.');
        }

        return CsvExport::response(
            'virements-'.$period.'.csv',
            [
                'Bénéficiaire',
                'Matricule',
                'Banque',
                'RIB',
                'Montant net (TND)',
                'Référence',
            ],
            $rows
        );
    }

    public function downloadPayslip(Payslip $payslip)
    {
        $payslip->load('employee');
        $pdf = PayslipPdf::render($payslip, $this->payroll->periodLabel($payslip->period));

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="bulletin-'.$payslip->matricule().'-'.$payslip->period.'.pdf"',
        ]);
    }

    /**
     * @return array<int, string>
     */
    private function journalHeadings(): array
    {
        return [
            'Collaborateur',
            'Matricule',
            'CIN',
            'N° CNSS',
            'Département',
            'Salaire de base',
            'Prime',
            'Indemnité transport',
            'Brut',
            'CNSS salariale ('.PayrollCalculator::rateLabel((int) config('payroll.cnss.employee_rate_bp', 918)).')',
            'Base imposable',
            'IRPP',
            'CSS ('.PayrollCalculator::rateLabel((int) config('payroll.css_rate_bp', 100)).')',
            'Net à payer',
            'Banque',
            'RIB',
            'Statut',
        ];
    }
}
