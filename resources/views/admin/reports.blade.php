<x-admin-shell active="reports" title="Rapports & Tableaux de Bord Réglementaires">
<div class="flex flex-col w-full py-margin-mobile md:py-margin space-y-space-xl">

@if (session('success'))
<div class="p-space-md rounded-xl bg-[#ECFDF5] text-[#065F46] dark:bg-tertiary/15 dark:text-tertiary-fixed-dim font-label-lg text-label-lg flex items-center gap-space-sm shadow-sm">
    <span class="material-symbols-outlined text-body-lg">check_circle</span>
    <span>{{ session('success') }}</span>
</div>
@endif
@if (session('error'))
<div class="p-space-md rounded-xl bg-[#FFFBEB] text-[#92400E] dark:bg-[#3f3113] dark:text-[#ffd48a] font-label-lg text-label-lg flex items-center gap-space-sm shadow-sm">
    <span class="material-symbols-outlined text-body-lg">error</span>
    <span>{{ session('error') }}</span>
</div>
@endif

@include('admin.partials.validation-errors')

<!-- Top Navigation & Breadcrumb -->
<div class="flex flex-col md:flex-row md:items-end justify-between gap-space-md">
<div class="flex flex-col space-y-space-xs">
<div class="flex items-center gap-space-xs font-label-sm text-label-sm text-on-surface-variant">
<a class="hover:text-primary transition-colors" href="#">Accueil</a>
<span class="material-symbols-outlined text-body-sm text-outline">chevron_right</span>
<span class="text-on-surface font-semibold">Rapports &amp; Analytique</span>
</div>
<h1 class="font-display-lg text-display-lg text-on-surface tracking-tight">Rapports &amp; Tableaux de Bord Réglementaires</h1>
<p class="font-body-md text-body-md text-on-surface-variant">Indicateurs calculés en temps réel depuis les modules RH connectés.</p>
</div>
<!-- Actions / Controls -->
<div class="flex flex-wrap items-center gap-space-sm">
<form method="GET" action="{{ route('admin.reports') }}" class="relative flex items-center bg-surface-container-low px-space-md py-1.5 rounded-xl text-on-surface font-label-md text-label-md shadow-sm">
<span class="material-symbols-outlined text-body-md text-primary mr-space-xs">calendar_today</span>
<span class="font-semibold mr-space-xs">Période :</span>
<select class="bg-transparent border-none font-numeric-md text-numeric-md text-on-surface focus:outline-none cursor-pointer pr-space-md" name="period" onchange="this.form.submit()">
@foreach (App\Http\Controllers\ReportsController::PERIODS as $key => $label)
<option value="{{ $key }}" @selected($period === $key)>{{ $label }}</option>
@endforeach
</select>
</form>
<button class="h-10 px-space-lg rounded-xl bg-primary-container hover:bg-primary-fill text-on-primary font-label-lg text-label-lg flex items-center gap-space-xs shadow-md transition-all active:scale-[0.98]" type="button" onclick="openScheduleModal(null)">
<span class="material-symbols-outlined text-body-md">add_chart</span>
<span>Nouvelle Planification</span>
</button>
</div>
</div>
<!-- Compliance Banner -->
<div class="relative overflow-hidden bg-surface-container-lowest rounded-xl p-space-lg shadow-sm">
<div class="absolute -right-12 -top-12 w-48 h-48 bg-tertiary-fixed/30 rounded-full blur-3xl pointer-events-none"></div>
<div class="flex flex-col lg:flex-row lg:items-center justify-between gap-space-md relative z-10">
<div class="flex items-center gap-space-md">
<div class="w-12 h-12 rounded-xl bg-tertiary/10 flex items-center justify-center shrink-0">
<span class="material-symbols-outlined text-tertiary text-headline-md" style="font-variation-settings: 'FILL' 1;">gavel</span>
</div>
<div class="flex flex-col">
<div class="flex items-center gap-space-xs">
<span class="font-headline-sm text-headline-sm text-on-surface">Indicateurs RH consolidés</span>
<span class="inline-flex items-center px-space-xs py-0.5 rounded-full bg-emerald-50 text-tertiary font-label-sm text-label-sm">
<span class="w-1.5 h-1.5 rounded-full bg-tertiary mr-1"></span> {{ App\Http\Controllers\ReportsController::PERIODS[$period] }}
            </span>
</div>
<p class="font-body-sm text-body-sm text-on-surface-variant mt-0.5">
            Calculés depuis les modules Collaborateurs, Congés, Recrutement et Documents. Le module Paie n'étant pas encore connecté, ses indicateurs sont indisponibles.
          </p>
</div>
</div>
<div class="flex items-center gap-space-xl shrink-0 self-start lg:self-center bg-surface-container-low px-space-lg py-2.5 rounded-xl">
<div class="flex flex-col">
<span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Effectif Total</span>
<span class="font-numeric-md text-numeric-md text-on-surface font-semibold">{{ $totalEmployees }} collaborateur(s)</span>
</div>
<div class="h-8 w-px bg-surface-container"></div>
<div class="flex flex-col">
<span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Matricules CNSS</span>
<span class="font-label-md text-label-md text-tertiary font-semibold flex items-center gap-1">
<span class="material-symbols-outlined text-body-sm">check_circle</span> {{ $cnssRegistered }} enregistrés
          </span>
</div>
</div>
</div>
</div>
<!-- KPI Strip : Recrutement & Documents -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-space-md">
<div class="p-space-lg rounded-xl bg-surface-container-lowest shadow-sm flex items-center justify-between">
<div class="flex flex-col">
<span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wide">Candidats actifs</span>
<span class="font-display-lg text-display-lg text-on-surface font-bold mt-1">{{ $recruitment['active_candidates'] }}</span>
</div>
<div class="w-12 h-12 rounded-xl bg-secondary-container flex items-center justify-center text-primary">
<span class="material-symbols-outlined text-headline-sm">groups</span>
</div>
</div>
<div class="p-space-lg rounded-xl bg-surface-container-lowest shadow-sm flex items-center justify-between">
<div class="flex flex-col">
<span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wide">Embauches (période)</span>
<span class="font-display-lg text-display-lg text-on-surface font-bold mt-1">{{ $recruitment['hired_in_period'] }}</span>
</div>
<div class="w-12 h-12 rounded-xl bg-surface-container flex items-center justify-center text-tertiary">
<span class="material-symbols-outlined text-headline-sm">how_to_reg</span>
</div>
</div>
<div class="p-space-lg rounded-xl bg-surface-container-lowest shadow-sm flex items-center justify-between">
<div class="flex flex-col">
<span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wide">Offres ouvertes</span>
<span class="font-display-lg text-display-lg text-on-surface font-bold mt-1">{{ $recruitment['open_offers'] }}</span>
</div>
<div class="w-12 h-12 rounded-xl bg-tertiary-fixed/30 flex items-center justify-center text-tertiary">
<span class="material-symbols-outlined text-headline-sm">work</span>
</div>
</div>
<div class="p-space-lg rounded-xl bg-surface-container-lowest shadow-sm flex items-center justify-between">
<div class="flex flex-col">
<span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wide">Documents stockés</span>
<span class="font-display-lg text-display-lg text-on-surface font-bold mt-1">{{ $documents['total'] }}</span>
<span class="font-label-sm text-label-sm text-on-surface-variant">{{ number_format($documents['bytes'] / 1048576, 1, ',', ' ') }} Mo</span>
</div>
<div class="w-12 h-12 rounded-xl bg-surface-container-high flex items-center justify-center text-primary">
<span class="material-symbols-outlined text-headline-sm">folder</span>
</div>
</div>
</div>
<!-- Primary Report Modules Bento Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-space-lg">
<!-- Card 1: Paie & Fiscalité Sociale -->
<div class="flex flex-col justify-between bg-surface-container-lowest rounded-xl p-space-xl shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
<div class="flex flex-col space-y-space-md">
<div class="flex items-start justify-between">
<div class="flex items-center gap-space-sm">
<div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center text-primary group-hover:bg-primary-container group-hover:text-on-primary transition-colors">
<span class="material-symbols-outlined text-headline-sm">account_balance_wallet</span>
</div>
<div>
<span class="font-label-sm text-label-sm text-primary font-bold uppercase tracking-widest">Réglementaire • Finances</span>
<h2 class="font-headline-md text-headline-md text-on-surface">Paie &amp; Fiscalité Sociale</h2>
</div>
</div>
<span class="px-2.5 py-1 rounded-full bg-surface-container-low text-on-surface-variant font-label-sm text-label-sm">
            Mensuel &amp; Trimestriel
          </span>
</div>
<p class="font-body-md text-body-md text-on-surface-variant">
          Journal des salaires, livre de paie, retenues IRPP et cotisations CNSS.
        </p>
<!-- KPI Metrics Split -->
<div class="grid grid-cols-2 gap-space-md pt-space-xs">
<div class="p-space-md bg-surface-container-low rounded-xl">
<span class="font-label-sm text-label-sm text-on-surface-variant">Masse Salariale Cumulée</span>
<div class="font-headline-md text-headline-md text-on-surface font-bold mt-1">— <span class="font-label-sm text-label-sm font-normal text-on-surface-variant">TND</span></div>
</div>
<div class="p-space-md bg-surface-container-low rounded-xl">
<span class="font-label-sm text-label-sm text-on-surface-variant">Cotisations CNSS Globales</span>
<div class="font-headline-md text-headline-md text-on-surface font-bold mt-1">— <span class="font-label-sm text-label-sm font-normal text-on-surface-variant">TND</span></div>
</div>
</div>
</div>
<div class="flex items-center justify-between pt-space-lg mt-space-lg border-t border-surface-container-low gap-space-sm">
<div class="flex items-center gap-space-xs font-label-sm text-label-sm text-on-surface-variant">
<span class="material-symbols-outlined text-body-sm text-secondary">info</span> Données indisponibles (module Paie non connecté)
        </div>
</div>
</div>
<!-- Card 2: Bilan Social & Effectifs -->
<div class="flex flex-col justify-between bg-surface-container-lowest rounded-xl p-space-xl shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
<div class="flex flex-col space-y-space-md">
<div class="flex items-start justify-between">
<div class="flex items-center gap-space-sm">
<div class="w-10 h-10 rounded-xl bg-secondary-container flex items-center justify-center text-primary group-hover:bg-primary-container group-hover:text-on-primary transition-colors">
<span class="material-symbols-outlined text-headline-sm">groups</span>
</div>
<div>
<span class="font-label-sm text-label-sm text-secondary font-bold uppercase tracking-widest">Stratégie RH • Démographie</span>
<h2 class="font-headline-md text-headline-md text-on-surface">Bilan Social &amp; Effectifs</h2>
</div>
</div>
<span class="px-2.5 py-1 rounded-full bg-surface-container-low text-on-surface-variant font-label-sm text-label-sm">
            {{ $totalEmployees }} Collaborateur(s)
          </span>
</div>
<p class="font-body-md text-body-md text-on-surface-variant">
          Ventilation des effectifs par poste, calculée en temps réel depuis le registre du personnel.
        </p>
<!-- Department Mini Chart / Visual Headcount Miniature -->
<div class="p-space-md bg-surface-container-low rounded-xl space-y-space-sm">
<div class="flex items-center justify-between font-label-sm text-label-sm">
<span class="text-on-surface-variant">Ventilation Effectifs par Poste</span>
<span class="font-numeric-md text-on-surface font-semibold">{{ $employeesByPosition->count() }} poste(s)</span>
</div>
<!-- Inline Distribution Bars -->
<div class="w-full space-y-1.5 pt-1">
@forelse ($employeesByPosition as $position => $count)
<div class="flex items-center gap-2">
<span class="w-28 font-label-sm text-label-sm text-on-surface-variant truncate" title="{{ $position }}">{{ $position }}</span>
<div class="flex-1 bg-surface-container rounded-full h-2.5 overflow-hidden">
<div class="bg-primary-container h-full rounded-full" style="width: {{ $totalEmployees > 0 ? round($count / $totalEmployees * 100) : 0 }}%;"></div>
</div>
<span class="font-numeric-md text-label-sm text-on-surface font-semibold w-8 text-right">{{ $count }}</span>
</div>
@empty
<span class="font-label-sm text-label-sm text-on-surface-variant">Aucun collaborateur enregistré.</span>
@endforelse
</div>
</div>
</div>
<div class="flex items-center justify-between pt-space-lg mt-space-lg border-t border-surface-container-low gap-space-sm">
<div class="flex items-center gap-2 font-numeric-md text-label-sm text-on-surface-variant">
<span class="text-on-surface-variant font-semibold">Turnover : —</span>
        </div>
<div class="flex items-center gap-space-sm">
<a class="h-9 px-space-md rounded-xl bg-surface-container-low hover:bg-surface-container text-on-surface font-label-md text-label-md transition-colors" href="{{ route('admin.employees') }}">
            Voir le module
          </a>
<a class="h-9 px-space-md rounded-xl bg-primary-container hover:bg-primary-fill text-on-primary font-label-md text-label-md flex items-center gap-1 transition-colors" href="{{ route('admin.reports.export', ['dataset' => 'employees']) }}">
<span class="material-symbols-outlined text-body-md">download</span>
<span>Exporter CSV Effectifs</span>
</a>
</div>
</div>
</div>
<!-- Card 3: Assiduité & Congés -->
<div class="flex flex-col justify-between bg-surface-container-lowest rounded-xl p-space-xl shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
<div class="flex flex-col space-y-space-md">
<div class="flex items-start justify-between">
<div class="flex items-center gap-space-sm">
<div class="w-10 h-10 rounded-xl bg-tertiary-fixed/40 flex items-center justify-center text-tertiary group-hover:bg-tertiary group-hover:text-on-tertiary transition-colors">
<span class="material-symbols-outlined text-headline-sm">event_available</span>
</div>
<div>
<span class="font-label-sm text-label-sm text-tertiary font-bold uppercase tracking-widest">Temps &amp; Activité</span>
<h2 class="font-headline-md text-headline-md text-on-surface">Rapport d'Assiduité &amp; Congés</h2>
</div>
</div>
<span class="px-2.5 py-1 rounded-full bg-emerald-50 text-tertiary font-label-sm text-label-sm font-semibold">
            {{ $congesCount }} demande(s) sur la période
          </span>
</div>
<p class="font-body-md text-body-md text-on-surface-variant">
          Cartographie des absences : congés annuels, maladies contrôlés, autorisations d'absence.
        </p>
<!-- Indicators Breakdown -->
<div class="grid grid-cols-3 gap-space-sm pt-space-xs">
<div class="p-space-sm bg-surface-container-low rounded-xl text-center">
<span class="font-label-sm text-label-sm text-on-surface-variant block">Demandes (période)</span>
<span class="font-headline-sm text-headline-sm text-on-surface font-bold">{{ $congesCount }}</span>
</div>
<div class="p-space-sm bg-surface-container-low rounded-xl text-center">
<span class="font-label-sm text-label-sm text-on-surface-variant block">Jours de congés</span>
<span class="font-headline-sm text-headline-sm text-on-surface font-bold">{{ $congesDays }} j</span>
</div>
<div class="p-space-sm bg-surface-container-low rounded-xl text-center">
<span class="font-label-sm text-label-sm text-on-surface-variant block">Taux Absentéisme</span>
<span class="font-headline-sm text-headline-sm text-on-surface font-bold">—</span>
</div>
</div>
</div>
<div class="flex items-center justify-between pt-space-lg mt-space-lg border-t border-surface-container-low gap-space-sm">
<div class="flex items-center gap-space-xs font-label-sm text-label-sm text-on-surface-variant">
<span class="material-symbols-outlined text-body-sm text-secondary">update</span> Données synchronisées
        </div>
<div class="flex items-center gap-space-sm">
<a class="h-9 px-space-md rounded-xl bg-surface-container-high hover:bg-surface-container-highest text-on-surface font-label-md text-label-md flex items-center gap-1 transition-colors" href="{{ route('admin.reports.export', ['dataset' => 'conges']) }}">
<span class="material-symbols-outlined text-body-md">download</span>
<span>Exporter CSV Congés</span>
</a>
</div>
</div>
</div>
<!-- Card 4: Déclarations CNSS & Inspection -->
<div class="flex flex-col justify-between bg-surface-container-lowest rounded-xl p-space-xl shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
<div class="flex flex-col space-y-space-md">
<div class="flex items-start justify-between">
<div class="flex items-center gap-space-sm">
<div class="w-10 h-10 rounded-xl bg-primary-fixed flex items-center justify-center text-primary group-hover:bg-primary-container group-hover:text-on-primary transition-colors">
<span class="material-symbols-outlined text-headline-sm">description</span>
</div>
<div>
<span class="font-label-sm text-label-sm text-primary font-bold uppercase tracking-widest">Obligations Légales</span>
<h2 class="font-headline-md text-headline-md text-on-surface">Déclarations CNSS &amp; Inspection</h2>
</div>
</div>
<span class="px-2.5 py-1 rounded-full bg-amber-50 text-amber-800 font-label-sm text-label-sm font-semibold flex items-center gap-1">
<span class="w-1.5 h-1.5 rounded-full bg-amber-600"></span> Registre du personnel
          </span>
</div>
<p class="font-body-md text-body-md text-on-surface-variant">
          Registre obligatoire du personnel et état des matricules CNSS. La télé-déclaration des salaires dépend du module Paie (non connecté).
        </p>
<!-- Status & Certification Box -->
<div class="p-space-md bg-surface-container-low rounded-xl flex items-center justify-between">
<div class="flex items-center gap-space-sm">
<span class="material-symbols-outlined text-tertiary text-display-lg-mobile">task_alt</span>
<div class="flex flex-col">
<span class="font-label-md text-label-md text-on-surface font-semibold">Matricules CNSS enregistrés</span>
<span class="font-body-sm text-body-sm text-on-surface-variant">{{ $cnssRegistered }} sur {{ $totalEmployees }} collaborateur(s) renseigné(s)</span>
</div>
</div>
<span class="px-space-sm py-1 bg-surface-container-lowest rounded-lg font-numeric-md text-label-sm text-on-surface font-bold shadow-xs">
            Télé-déclaration : —
          </span>
</div>
</div>
<div class="flex items-center justify-between pt-space-lg mt-space-lg border-t border-surface-container-low gap-space-sm">
<div class="flex items-center gap-space-xs font-label-sm text-label-sm text-on-surface-variant">
<span class="material-symbols-outlined text-body-sm text-secondary">info</span> Fichier DSN généré par le module Paie
        </div>
<div class="flex items-center gap-space-sm">
<a class="h-9 px-space-md rounded-xl bg-primary-container hover:bg-primary-fill text-on-primary font-label-md text-label-md flex items-center gap-1.5 transition-colors" href="{{ route('admin.reports.export', ['dataset' => 'employees']) }}">
<span class="material-symbols-outlined text-body-md">download</span>
<span>Exporter Registre (CSV)</span>
</a>
</div>
</div>
</div>
</div>
<!-- Detailed Reporting / Scheduled Automated Exports Section -->
<div class="bg-surface-container-lowest rounded-xl p-space-xl shadow-sm flex flex-col space-y-space-lg">
<div class="flex flex-col md:flex-row md:items-center justify-between gap-space-sm pb-space-xs">
<div>
<div class="flex items-center gap-space-xs">
<span class="material-symbols-outlined text-primary text-body-lg">send</span>
<h3 class="font-headline-sm text-headline-sm text-on-surface">Rapports Automatisés &amp; Exports Planifiés</h3>
</div>
<p class="font-body-sm text-body-sm text-on-surface-variant mt-0.5">
          Distribution récurrente à la Direction Générale, Finance et au Cabinet Comptable.
        </p>
</div>
<div class="flex items-center gap-space-sm">
<button class="h-8 px-space-md rounded-xl bg-surface-container-high hover:bg-surface-container-highest text-on-surface font-label-md text-label-md flex items-center gap-1 transition-colors" type="button" onclick="openScheduleModal(null)">
<span class="material-symbols-outlined text-body-sm">add</span>
<span>Nouvelle Planification</span>
</button>
</div>
</div>
<!-- Data Table Container -->
<div class="overflow-x-auto rounded-xl">
<table class="w-full text-left border-collapse">
<thead>
<tr class="bg-surface-container-low text-on-surface-variant font-label-md text-label-md uppercase tracking-wider h-10">
<th class="py-2.5 px-space-md rounded-l-xl font-semibold">Intitulé du Rapport</th>
<th class="py-2.5 px-space-md font-semibold">Destinataires</th>
<th class="py-2.5 px-space-md font-semibold">Fréquence &amp; Déclencheur</th>
<th class="py-2.5 px-space-md font-semibold">Format</th>
<th class="py-2.5 px-space-md font-semibold">Dernier Envoi</th>
<th class="py-2.5 px-space-md font-semibold">Statut</th>
<th class="py-2.5 px-space-md rounded-r-xl text-right font-semibold">Actions</th>
</tr>
</thead>
<tbody class="divide-y divide-surface-container-low font-body-sm text-body-sm text-on-surface">
@forelse ($schedules as $schedule)
<tr class="hover:bg-surface-container-low/60 transition-colors h-14">
<td class="py-3 px-space-md font-medium">
<div class="flex items-center gap-space-sm">
<span class="material-symbols-outlined text-primary text-body-md">receipt_long</span>
<div>
<span class="font-label-lg text-label-lg text-on-surface font-semibold block">{{ $schedule->title }}</span>
@if ($schedule->description)
<span class="font-label-sm text-label-sm text-on-surface-variant">{{ $schedule->description }}</span>
@endif
</div>
</div>
</td>
<td class="py-3 px-space-md">
<span class="font-body-sm text-on-surface-variant">{{ $schedule->recipients }}</span>
</td>
<td class="py-3 px-space-md font-numeric-md">
<span class="text-on-surface">{{ App\Models\ReportSchedule::FREQUENCIES[$schedule->frequency] ?? $schedule->frequency }}</span>
@if ($schedule->schedule_detail)
<span class="text-on-surface-variant block text-label-sm">{{ $schedule->schedule_detail }}</span>
@endif
</td>
<td class="py-3 px-space-md">
<span class="inline-flex items-center px-2 py-0.5 rounded bg-surface-container text-on-surface font-numeric-md text-label-sm">
            {{ $schedule->format }}
          </span>
</td>
<td class="py-3 px-space-md font-numeric-md text-on-surface-variant">
            {{ $schedule->last_sent_at?->format('d M Y, H:i') ?? 'Jamais envoyé' }}
          </td>
<td class="py-3 px-space-md">
<span class="inline-flex items-center px-2.5 py-0.5 rounded-full font-label-sm text-label-sm font-semibold {{ $schedule->status === 'actif' ? 'bg-emerald-50 text-tertiary' : 'bg-surface-container text-secondary' }}">
<span class="w-1.5 h-1.5 rounded-full {{ $schedule->status === 'actif' ? 'bg-tertiary' : 'bg-[#64748B] dark:bg-outline' }} mr-1.5"></span> {{ App\Models\ReportSchedule::STATUSES[$schedule->status] ?? $schedule->status }}
</span>
</td>
<td class="py-3 px-space-md text-right">
<div class="inline-flex items-center gap-1">
<form method="POST" action="{{ route('admin.reports.schedules.send', $schedule) }}" onsubmit="return confirm('Marquer ce rapport comme envoyé maintenant ?');">
@csrf
<button aria-label="Envoyer maintenant" class="p-1.5 rounded-lg hover:bg-surface-container text-on-surface-variant hover:text-primary transition-colors" title="Exécuter maintenant" type="submit">
<span class="material-symbols-outlined text-body-md">send</span>
</button>
</form>
<button aria-label="Éditer" class="p-1.5 rounded-lg hover:bg-surface-container text-on-surface-variant hover:text-on-surface transition-colors" title="Modifier paramètres" type="button"
    data-json='@js($schedule->only(['id', 'title', 'description', 'recipients', 'frequency', 'schedule_detail', 'format', 'status']))'
    onclick="openScheduleModal(this)">
<span class="material-symbols-outlined text-body-md">edit</span>
</button>
<form method="POST" action="{{ route('admin.reports.schedules.destroy', $schedule) }}" onsubmit="return confirm('Supprimer cette planification ?');">
@csrf
@method('DELETE')
<button aria-label="Supprimer" class="p-1.5 rounded-lg hover:bg-error-container text-on-surface-variant hover:text-error transition-colors" title="Supprimer" type="submit">
<span class="material-symbols-outlined text-body-md">delete</span>
</button>
</form>
</div>
</td>
</tr>
@empty
<tr>
<td class="py-space-lg px-space-md text-on-surface-variant" colspan="7">Aucune planification. Cliquez sur « Nouvelle Planification » pour créer la première.</td>
</tr>
@endforelse
</tbody>
</table>
</div>
</div>
</div>

<!-- ==================== DRAWER: Planification (créer / modifier) ==================== -->
<div class="hidden fixed inset-0 z-50 overflow-hidden justify-end" id="schedule-modal">
<div class="absolute inset-0 bg-black/60 backdrop-blur-xs" onclick="closeModal('schedule-modal')"></div>
<div class="relative w-full max-w-2xl bg-surface-container-lowest shadow-2xl flex flex-col h-full z-10 overflow-hidden">
    <div class="p-space-xl bg-surface-container-low/60 flex items-start justify-between gap-space-md">
        <div class="flex items-center gap-space-md">
            <div class="w-12 h-12 rounded-xl bg-primary-container text-on-primary flex items-center justify-center shadow-sm shrink-0">
                <span class="material-symbols-outlined text-headline-lg">schedule_send</span>
            </div>
            <div class="flex flex-col">
                <h2 class="font-headline-md text-headline-md text-on-surface tracking-tight" id="schedule-modal-title">Nouvelle Planification</h2>
                <p class="font-body-sm text-body-sm text-on-surface-variant">Programmez l'envoi automatique d'un rapport</p>
            </div>
        </div>
        <button aria-label="Fermer" class="w-9 h-9 rounded-xl hover:bg-surface-container text-on-surface-variant hover:text-on-surface flex items-center justify-center transition-colors shrink-0" type="button" onclick="closeModal('schedule-modal')">
            <span class="material-symbols-outlined text-headline-sm">close</span>
        </button>
    </div>
    <form method="POST" action="{{ route('admin.reports.schedules.store') }}" id="schedule-form" class="flex-1 overflow-y-auto p-space-xl flex flex-col gap-space-md">
        @csrf
        <input type="hidden" name="_method" value="" id="schedule-method"/>
        <div class="flex flex-col gap-space-xs">
            <label class="font-label-md text-label-md text-on-surface font-medium" for="schedule-title">Intitulé du rapport <span class="text-error">*</span></label>
            <input class="w-full h-10 px-3 rounded-xl bg-surface-container-lowest text-on-surface font-body-md text-body-md placeholder:text-on-surface-variant focus:outline-none ring-1 ring-outline-variant focus:ring-2 focus:ring-primary transition-all" id="schedule-title" name="title" placeholder="Ex: Rapport de paie mensuel — Novembre 2025" required/>
        </div>
        <div class="flex flex-col gap-space-xs">
            <label class="font-label-md text-label-md text-on-surface font-medium" for="schedule-description">Description</label>
            <input class="w-full h-10 px-3 rounded-xl bg-surface-container-lowest text-on-surface font-body-md text-body-md placeholder:text-on-surface-variant focus:outline-none ring-1 ring-outline-variant focus:ring-2 focus:ring-primary transition-all" id="schedule-description" name="description" placeholder="Ex: Synthèse des salaires, cotisations CNSS et IRPP"/>
        </div>
        <div class="flex flex-col gap-space-xs">
            <label class="font-label-md text-label-md text-on-surface font-medium" for="schedule-recipients">Destinataires <span class="text-error">*</span></label>
            <input class="w-full h-10 px-3 rounded-xl bg-surface-container-lowest text-on-surface font-body-md text-body-md focus:outline-none ring-1 ring-outline-variant focus:ring-2 focus:ring-primary transition-all" id="schedule-recipients" name="recipients" placeholder="Direction Financière, Cabinet Comptable..." required/>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-space-md">
            <div class="flex flex-col gap-space-xs">
                <label class="font-label-md text-label-md text-on-surface font-medium" for="schedule-frequency">Fréquence <span class="text-error">*</span></label>
                <div class="relative">
                    <select class="w-full appearance-none h-10 pl-3 pr-8 rounded-xl bg-surface-container-lowest text-on-surface font-body-md text-body-md focus:outline-none ring-1 ring-outline-variant focus:ring-2 focus:ring-primary transition-all cursor-pointer" id="schedule-frequency" name="frequency">
                        @foreach (App\Models\ReportSchedule::FREQUENCIES as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    <span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-on-surface-variant pointer-events-none text-body-md">expand_more</span>
                </div>
            </div>
            <div class="flex flex-col gap-space-xs">
                <label class="font-label-md text-label-md text-on-surface font-medium" for="schedule-detail">Déclencheur / Détail</label>
                <input class="w-full h-10 px-3 rounded-xl bg-surface-container-lowest text-on-surface font-body-md text-body-md focus:outline-none ring-1 ring-outline-variant focus:ring-2 focus:ring-primary transition-all" id="schedule-detail" name="schedule_detail" placeholder="Chaque 28 du mois"/>
            </div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-space-md">
            <div class="flex flex-col gap-space-xs">
                <label class="font-label-md text-label-md text-on-surface font-medium" for="schedule-format">Format <span class="text-error">*</span></label>
                <div class="relative">
                    <select class="w-full appearance-none h-10 pl-3 pr-8 rounded-xl bg-surface-container-lowest text-on-surface font-body-md text-body-md focus:outline-none ring-1 ring-outline-variant focus:ring-2 focus:ring-primary transition-all cursor-pointer" id="schedule-format" name="format">
                        <option value="CSV">CSV</option>
                        <option value="PDF">PDF</option>
                        <option value="XLSX">XLSX</option>
                    </select>
                    <span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-on-surface-variant pointer-events-none text-body-md">expand_more</span>
                </div>
            </div>
            <div class="flex flex-col gap-space-xs">
                <label class="font-label-md text-label-md text-on-surface font-medium" for="schedule-status">Statut <span class="text-error">*</span></label>
                <div class="relative">
                    <select class="w-full appearance-none h-10 pl-3 pr-8 rounded-xl bg-surface-container-lowest text-on-surface font-body-md text-body-md focus:outline-none ring-1 ring-outline-variant focus:ring-2 focus:ring-primary transition-all cursor-pointer" id="schedule-status" name="status">
                        @foreach (App\Models\ReportSchedule::STATUSES as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    <span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-on-surface-variant pointer-events-none text-body-md">expand_more</span>
                </div>
            </div>
        </div>
    </form>
    <div class="p-space-lg bg-surface-container-lowest shadow-[0_-4px_12px_rgba(0,0,0,0.03)] flex items-center justify-between gap-space-sm">
        <button class="h-10 px-space-lg rounded-xl bg-surface-container-low hover:bg-surface-container text-on-surface font-label-lg text-label-lg transition-colors" type="button" onclick="closeModal('schedule-modal')">Annuler</button>
        <button class="h-10 px-space-xl rounded-xl bg-primary-fill hover:bg-primary-container text-on-primary font-label-lg text-label-lg shadow-md flex items-center gap-space-xs transition-all transform active:scale-95" type="submit" form="schedule-form">
            <span class="material-symbols-outlined text-body-md">check</span>
            <span>Enregistrer</span>
        </button>
    </div>
</div>
</div>

<script>
function openModal(id) {
    document.getElementById(id).classList.remove('hidden');
    document.getElementById(id).classList.add('flex');
    document.body.style.overflow = 'hidden';
}
function closeModal(id) {
    document.getElementById(id).classList.add('hidden');
    document.getElementById(id).classList.remove('flex');
    document.body.style.overflow = '';
}

function openScheduleModal(button) {
    var form = document.getElementById('schedule-form');
    form.reset();
    form.action = '{{ route('admin.reports.schedules.store') }}';
    document.getElementById('schedule-method').value = '';
    document.getElementById('schedule-modal-title').textContent = 'Nouvelle Planification';
    if (button && button.dataset.json) {
        var data = JSON.parse(button.dataset.json);
        document.getElementById('schedule-modal-title').textContent = 'Modifier ' + data.title;
        Object.keys(data).forEach(function (key) {
            var field = form.querySelector('[name="' + key + '"]');
            if (field) field.value = data[key] === null ? '' : data[key];
        });
        form.action = '{{ route('admin.reports.schedules.update', '__ID__') }}'.replace('__ID__', data.id);
        document.getElementById('schedule-method').value = 'PUT';
    }
    openModal('schedule-modal');
}
</script>
</x-admin-shell>
