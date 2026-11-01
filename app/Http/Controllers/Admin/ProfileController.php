<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttendanceRecord;
use App\Models\Conges;
use App\Models\Document;
use App\Models\Employee;
use App\Models\JobOffer;
use App\Models\Payslip;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $anomalies = AttendanceRecord::whereDate('work_date', today())->get()
            ->reject(fn (AttendanceRecord $record) => $record->anomaly_ignored)
            ->filter(fn (AttendanceRecord $record) => $record->hasMissingCheckout() || $record->isLateArrival() || $record->hasEarlyExit())
            ->count();

        $modules = [
            [
                'key' => 'employees',
                'label' => 'Collaborateurs',
                'description' => 'Dossiers, contrats et organigramme.',
                'icon' => 'badge',
                'tone' => 'bg-primary-fixed text-primary',
                'url' => route('admin.employees'),
                'count' => Employee::where('status', 'actif')->count(),
                'countLabel' => 'actifs',
            ],
            [
                'key' => 'leaves',
                'label' => 'Congés & Absences',
                'description' => 'Demandes à valider et soldes.',
                'icon' => 'event_available',
                'tone' => 'bg-secondary-container text-on-secondary-fixed',
                'url' => route('admin.leaves'),
                'count' => Conges::where('status', 'pending')->count(),
                'countLabel' => 'en attente',
            ],
            [
                'key' => 'attendance',
                'label' => 'Pointage',
                'description' => 'Anomalies et régularisations.',
                'icon' => 'schedule',
                'tone' => 'bg-tertiary-fixed text-on-tertiary-fixed',
                'url' => route('admin.attendance'),
                'count' => $anomalies,
                'countLabel' => 'anomalies',
            ],
            [
                'key' => 'payroll',
                'label' => 'Paie',
                'description' => 'Bulletins, CNSS et virements.',
                'icon' => 'payments',
                'tone' => 'bg-surface-container-high text-on-surface',
                'url' => route('admin.payroll'),
                'count' => Payslip::where('period', now()->format('Y-m'))->where('status', 'review')->count(),
                'countLabel' => 'à réviser',
            ],
            [
                'key' => 'recruitment',
                'label' => 'Recrutement',
                'description' => 'Offres, candidats et entretiens.',
                'icon' => 'work',
                'tone' => 'bg-secondary-container text-on-secondary-fixed',
                'url' => route('admin.recruitment'),
                'count' => JobOffer::where('status', 'open')->count(),
                'countLabel' => 'offres ouvertes',
            ],
            [
                'key' => 'documents',
                'label' => 'Documents',
                'description' => 'Pièces RH et coffre-fort.',
                'icon' => 'description',
                'tone' => 'bg-primary-fixed text-primary',
                'url' => route('admin.documents'),
                'count' => Document::where('status', 'en_attente_signature')->count(),
                'countLabel' => 'à signer',
            ],
            [
                'key' => 'reports',
                'label' => 'Rapports',
                'description' => 'Exports et planifications.',
                'icon' => 'analytics',
                'tone' => 'bg-tertiary-fixed text-on-tertiary-fixed',
                'url' => route('admin.reports'),
                'count' => null,
                'countLabel' => null,
            ],
            [
                'key' => 'settings',
                'label' => 'Paramètres',
                'description' => 'Entreprise, CNSS et intégrations.',
                'icon' => 'settings',
                'tone' => 'bg-surface-container-high text-on-surface',
                'url' => route('admin.settings'),
                'count' => null,
                'countLabel' => null,
            ],
        ];

        return view('admin.profile', [
            'user' => $user,
            'modules' => $modules,
            'security' => [
                'access_ttl' => (int) config('auth.tokens.access_ttl'),
                'refresh_days' => (int) round(((int) config('auth.tokens.refresh_ttl')) / 1440),
                'remember_days' => (int) round(((int) config('auth.tokens.remember_ttl')) / 1440),
            ],
        ]);
    }
}
