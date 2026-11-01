<x-admin-shell active="payroll" title="Gestion de la Paie & Salaires">
@php
    $filterQuery = array_filter([
        'q' => $filters['q'],
        'department' => $filters['department'],
        'status' => $filters['status'],
    ], fn ($value) => $value !== '');

    $exportQuery = array_merge($filterQuery, ['month' => $period]);
    $slip = $selectedPayslip?->payload;
    $statusCounts = [
        'ready' => $readyCount,
        'review' => $reviewCount,
        'paid' => $paidCount,
    ];
    $previousMonthShort = ucfirst($periodStart->copy()->subMonthNoOverflow()->locale('fr')->translatedFormat('M'));
    $pageNet = $payslips->getCollection()->sum(fn ($payslip) => (float) $payslip->net);
    $statusBadge = function (string $status): array {
        return match ($status) {
            'ready' => ['container' => 'bg-tertiary-fixed text-on-tertiary-fixed font-semibold', 'dot' => 'bg-tertiary'],
            'review' => ['container' => 'bg-surface-container text-on-surface-variant font-medium', 'dot' => 'bg-secondary'],
            'paid' => ['container' => 'bg-primary-container/10 text-primary font-semibold', 'dot' => 'bg-primary-container'],
            default => ['container' => 'bg-surface-container text-on-surface-variant font-medium', 'dot' => 'bg-outline'],
        };
    };
@endphp
<div class="flex flex-col w-full pb-16">
@if (session('success'))
<div class="fixed bottom-4 left-4 right-4 sm:left-auto sm:bottom-6 sm:right-6 z-[80] sm:max-w-sm p-space-md rounded-xl bg-tertiary-fixed text-on-tertiary-fixed shadow-lg flex items-start gap-space-sm" data-flash-toast role="status">
<span class="material-symbols-outlined text-body-lg shrink-0">check_circle</span>
<span class="font-label-md text-label-md">{{ session('success') }}</span>
<button class="ml-2 text-on-tertiary-fixed/70 hover:text-on-tertiary-fixed" type="button" onclick="this.parentElement.remove()" aria-label="Fermer">
<span class="material-symbols-outlined text-body-md">close</span>
</button>
</div>
@endif
@if (session('error'))
<div class="fixed bottom-4 left-4 right-4 sm:left-auto sm:bottom-6 sm:right-6 z-[80] sm:max-w-sm p-space-md rounded-xl bg-error-container text-on-error-container shadow-lg flex items-start gap-space-sm" data-flash-toast role="alert">
<span class="material-symbols-outlined text-body-lg shrink-0">error</span>
<span class="font-label-md text-label-md">{{ session('error') }}</span>
<button class="ml-2 text-on-error-container/70 hover:text-on-error-container" type="button" onclick="this.parentElement.remove()" aria-label="Fermer">
<span class="material-symbols-outlined text-body-md">close</span>
</button>
</div>
@endif
<!-- Breadcrumb & Top Metadata Bar -->
<div class="flex flex-wrap items-center justify-between gap-space-sm pt-space-lg pb-space-md">
<nav aria-label="Fil d'ariane" class="flex items-center gap-space-xs font-label-md text-label-md text-on-surface-variant">
<a class="hover:text-primary transition-colors flex items-center gap-1" href="{{ route('admin.dashboard') }}">
<span class="material-symbols-outlined text-body-sm">home</span>
<span>Accueil</span>
</a>
<span class="text-outline-variant">/</span>
<span class="text-on-surface font-semibold">Paie &amp; Rémunérations</span>
<span class="text-outline-variant">/</span>
<span class="text-primary font-medium">{{ $periodLabel }}</span>
</nav>
<div class="flex items-center gap-space-xs font-label-sm text-label-sm text-on-surface-variant bg-surface-container-low px-space-md py-1.5 rounded-full shadow-sm">
<span class="w-2 h-2 rounded-full bg-tertiary animate-pulse"></span>
<span>Barème IRPP Révisé {{ $periodStart->year }} • CNSS {{ $periodStart->year }} Conforme</span>
</div>
</div>
<!-- Main Section Header -->
<div class="flex flex-col xl:flex-row xl:items-end justify-between gap-space-lg pb-space-xl">
<div>
<div class="flex items-center gap-space-sm mb-1">
<span class="font-label-sm text-label-sm tracking-wider uppercase text-primary font-semibold">Gestion Sociale &amp; Fiscale</span>
<span class="text-outline-variant">•</span>
<span class="font-numeric-md text-numeric-md text-on-surface-variant">ID Cycle: {{ $cycleId }}</span>
</div>
<h1 class="font-display-lg text-display-lg text-on-surface tracking-tight">Gestion de la Paie &amp; Salaires</h1>
<p class="font-body-md text-body-md text-on-surface-variant mt-1 flex items-center gap-space-xs">
<span class="material-symbols-outlined text-body-md text-tertiary">verified</span>
<span>Cycle de Paie : <strong>{{ $periodLabel }}</strong> • Conforme Code du Travail Tunisien, barème IRPP {{ $periodStart->year }} &amp; Loi de Finances</span>
</p>
</div>
<!-- Action Group -->
<div class="flex flex-wrap items-center gap-space-sm">
<a class="h-10 px-space-md rounded-xl bg-surface-container-lowest hover:bg-surface-container text-on-surface font-label-lg text-label-lg flex items-center gap-space-xs shadow-sm transition-all" href="{{ route('admin.payroll.export.journal', ['month' => $period]) }}" title="Télécharger le journal de paie (CSV compatible Excel)">
<span class="material-symbols-outlined text-body-md text-tertiary">table_view</span>
<span>Journal de Paie (Excel)</span>
</a>
<a class="h-10 px-space-md rounded-xl bg-surface-container-lowest hover:bg-surface-container text-on-surface font-label-lg text-label-lg flex items-center gap-space-xs shadow-sm transition-all" href="{{ route('admin.payroll.export.cnss', ['month' => $period]) }}" title="Télécharger la télé-déclaration CNSS (.TXT)">
<span class="material-symbols-outlined text-body-md text-primary">cloud_download</span>
<span>Télé-déclaration CNSS (.TXT)</span>
</a>
<form method="POST" action="{{ route('admin.payroll.close') }}" onsubmit="return confirm('Clôturer le cycle de paie ? Tous les bulletins non payés seront marqués comme payés.');">
@csrf
<input type="hidden" name="period" value="{{ $period }}"/>
<button class="h-10 px-space-lg rounded-xl bg-primary-container hover:bg-primary-fill text-on-primary font-label-lg text-label-lg flex items-center gap-space-xs shadow-md transition-all active:scale-95 disabled:opacity-60 disabled:cursor-not-allowed disabled:hover:bg-primary-container" type="submit" @if ($periodClosed) disabled @endif>
<span class="material-symbols-outlined text-body-md">lock</span>
<span>{{ $periodClosed ? 'Mois Clôturé' : 'Clôturer le Mois' }}</span>
</button>
</form>
</div>
</div>
<!-- Operational Status Banner -->
<div class="relative overflow-hidden rounded-xl bg-gradient-to-r from-surface-container-low via-surface-container-lowest to-surface-container-low p-space-lg shadow-sm mb-space-xl">
<div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-space-md">
<div class="flex items-start md:items-center gap-space-md">
<div class="w-10 h-10 rounded-xl bg-tertiary/10 text-tertiary flex items-center justify-center shrink-0">
<span class="material-symbols-outlined text-headline-sm">account_balance</span>
</div>
<div>
<div class="flex items-center gap-space-xs">
<span class="font-headline-sm text-headline-sm text-on-surface">Cycle {{ $periodLabel }} : {{ $periodClosed ? 'Clôturé et Télétransmis' : 'Prêt pour Télétransmission' }}</span>
@if ($periodClosed)
<span class="px-2 py-0.5 rounded-full text-label-sm font-label-sm bg-tertiary-fixed text-on-tertiary-fixed font-semibold">Cycle Clôturé</span>
@elseif ($reviewCount === 0 && $coverageRate >= 100)
<span class="px-2 py-0.5 rounded-full text-label-sm font-label-sm bg-tertiary-fixed text-on-tertiary-fixed font-semibold">Validé DAF</span>
@else
<span class="px-2 py-0.5 rounded-full text-label-sm font-label-sm bg-surface-container-high text-on-surface-variant font-semibold">En Préparation</span>
@endif
</div>
<p class="font-body-sm text-body-sm text-on-surface-variant mt-0.5">
            Fichiers SEPA / Virement bancaire protocolisés prêts pour <strong>Amen Bank</strong> et <strong>BIAT</strong> (Format TXT télétransmis). Contrôle matricules CNSS : {{ $coverageRate }}% conformes.
          </p>
</div>
</div>
<div class="flex items-center gap-space-sm shrink-0">
<div class="text-right hidden sm:block">
<p class="font-label-sm text-label-sm text-on-surface-variant uppercase">Date d'échéance virement</p>
<p class="font-numeric-md text-numeric-md text-on-surface font-bold">{{ $deadline->format('j') }} {{ ucfirst($deadline->copy()->locale('fr')->translatedFormat('F Y')) }}</p>
</div>
<a class="px-space-md py-2 rounded-lg bg-surface-container hover:bg-surface-container-high text-on-surface font-label-md text-label-md transition-colors flex items-center gap-1" href="{{ route('admin.payroll.export.bank', ['month' => $period]) }}" title="Télécharger le fichier de virements bancaires (.TXT)">
<span class="material-symbols-outlined text-body-sm">send</span>
<span>Transmettre Fichiers Banques</span>
</a>
</div>
</div>
</div>
<!-- High-level Financial Metric Cards (4 Bento KPIs) -->
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-space-md mb-space-xl">
<!-- KPI 1 -->
<div class="bg-surface-container-lowest rounded-xl p-space-lg shadow-sm flex flex-col justify-between">
<div class="flex items-center justify-between text-on-surface-variant mb-space-sm">
<span class="font-label-md text-label-md uppercase tracking-wider">Masse Salariale Nette</span>
<span class="material-symbols-outlined text-body-lg text-primary">payments</span>
</div>
<div class="flex items-baseline gap-space-xs my-space-xs">
<span class="font-display-lg text-display-lg text-on-surface tracking-tight">{{ number_format($netTotal, 0, '.', ',') }}</span>
<span class="font-label-lg text-label-lg text-on-surface-variant font-medium">TND</span>
</div>
<div class="flex items-center justify-between pt-space-xs text-on-surface-variant">
<span class="font-label-sm text-label-sm text-tertiary font-semibold flex items-center gap-0.5">
<span class="material-symbols-outlined text-body-sm">check_circle</span>
          {{ $periodClosed ? $paidCount.' virements exécutés' : $readyCount.' virements prêts' }}
        </span>
<span class="font-label-sm text-label-sm">{{ $growthRate === null ? 'Historique indisponible' : (($growthRate >= 0 ? '+' : '').number_format($growthRate, 1, '.', '').'% vs '.$previousMonthShort) }}</span>
</div>
</div>
<!-- KPI 2 -->
<div class="bg-surface-container-lowest rounded-xl p-space-lg shadow-sm flex flex-col justify-between">
<div class="flex items-center justify-between text-on-surface-variant mb-space-sm">
<span class="font-label-md text-label-md uppercase tracking-wider">Cotisations CNSS (9.18%)</span>
<span class="material-symbols-outlined text-body-lg text-secondary">badge</span>
</div>
<div class="flex items-baseline gap-space-xs my-space-xs">
<span class="font-display-lg text-display-lg text-on-surface tracking-tight">{{ number_format($cnssTotal, 0, '.', ',') }}</span>
<span class="font-label-lg text-label-lg text-on-surface-variant font-medium">TND</span>
</div>
<div class="flex items-center justify-between pt-space-xs text-on-surface-variant">
<span class="font-label-sm text-label-sm">Part salariale déduite</span>
<span class="font-numeric-md text-numeric-md font-semibold text-on-surface">Régime Général</span>
</div>
</div>
<!-- KPI 3 -->
<div class="bg-surface-container-lowest rounded-xl p-space-lg shadow-sm flex flex-col justify-between">
<div class="flex items-center justify-between text-on-surface-variant mb-space-sm">
<span class="font-label-md text-label-md uppercase tracking-wider">Cotisations CNSS Patronales</span>
<span class="material-symbols-outlined text-body-lg text-tertiary">assured_workload</span>
</div>
<div class="flex items-baseline gap-space-xs my-space-xs">
<span class="font-display-lg text-display-lg text-on-surface tracking-tight">{{ number_format($employerCnssTotal, 0, '.', ',') }}</span>
<span class="font-label-lg text-label-lg text-on-surface-variant font-medium">TND</span>
</div>
<div class="flex items-center justify-between pt-space-xs text-on-surface-variant">
<span class="font-label-sm text-label-sm">16.57% + Acc. Travail (0.8%)</span>
<span class="font-numeric-md text-numeric-md font-semibold text-tertiary">Déclarable Q{{ $periodStart->quarter }}</span>
</div>
</div>
<!-- KPI 4 -->
<div class="bg-surface-container-lowest rounded-xl p-space-lg shadow-sm flex flex-col justify-between">
<div class="flex items-center justify-between text-on-surface-variant mb-space-sm">
<span class="font-label-md text-label-md uppercase tracking-wider">Retenue à la Source IRPP</span>
<span class="material-symbols-outlined text-body-lg text-primary-container">receipt_long</span>
</div>
<div class="flex items-baseline gap-space-xs my-space-xs">
<span class="font-display-lg text-display-lg text-on-surface tracking-tight">{{ number_format($irppTotal, 0, '.', ',') }}</span>
<span class="font-label-lg text-label-lg text-on-surface-variant font-medium">TND</span>
</div>
<div class="flex items-center justify-between pt-space-xs text-on-surface-variant">
<span class="font-label-sm text-label-sm">Déclaration Mensuelle N°28</span>
<span class="font-numeric-md text-numeric-md font-semibold text-primary">Échéance {{ $irppDeadline->format('d/m') }}</span>
</div>
</div>
</div>
<!-- Interactive Workspace: Table & Interactive Payslip Drawer/Panel -->
<div class="grid grid-cols-1 xl:grid-cols-12 gap-space-lg">
<!-- Left / Center: Table & Filtering (8 cols) -->
<div class="xl:col-span-8 flex flex-col gap-space-md">
<!-- Search & Filters Container -->
<form class="bg-surface-container-lowest rounded-xl p-space-md shadow-sm flex flex-wrap items-center justify-between gap-space-sm" id="payrollFilterForm" method="GET" action="{{ route('admin.payroll') }}">
<input name="month" type="hidden" value="{{ $period }}"/>
<div class="flex flex-wrap items-center gap-space-sm flex-1 min-w-[280px]">
<div class="relative flex-1 min-w-[220px]">
<span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-body-md text-on-surface-variant pointer-events-none">search</span>
<input class="w-full h-9 pl-9 pr-3 rounded-lg bg-surface-container-low font-body-sm text-body-sm text-on-surface focus:outline-none focus:bg-surface-container transition-colors" id="searchInput" name="q" placeholder="Filtrer collaborateur, CIN, matricule..." type="text" value="{{ $filters['q'] }}"/>
</div>
<!-- Department Selector -->
<select class="h-9 px-space-sm rounded-lg bg-surface-container-low font-body-sm text-body-sm text-on-surface focus:outline-none focus:bg-surface-container" name="department" onchange="this.form.requestSubmit()">
<option value="">Tous Départements</option>
@foreach (\App\Models\Employee::DEPARTMENTS as $value => $label)
<option value="{{ $value }}" @selected($filters['department'] === $value)>{{ $label }}</option>
@endforeach
</select>
<!-- Status Selector -->
<select class="h-9 px-space-sm rounded-lg bg-surface-container-low font-body-sm text-body-sm text-on-surface focus:outline-none focus:bg-surface-container" name="status" onchange="this.form.requestSubmit()">
<option value="">Tous Statuts</option>
@foreach (\App\Models\Payslip::STATUSES as $value => $label)
<option value="{{ $value }}" @selected($filters['status'] === $value)>{{ $label }}{{ $value === 'paid' ? '' : ' ('.$statusCounts[$value].')' }}</option>
@endforeach
</select>
</div>
<div class="flex items-center gap-space-xs text-on-surface-variant font-label-sm text-label-sm">
<span>{{ $activeEmployees }} Collaborateurs Actifs</span>
<span class="w-1.5 h-1.5 rounded-full bg-outline-variant"></span>
<span>{{ $coverageRate }}% Déclarés</span>
</div>
</form>
<!-- Main Payroll Register Table -->
<div class="bg-surface-container-lowest rounded-xl shadow-sm overflow-hidden">
<div class="overflow-x-auto">
<table class="w-full text-left border-collapse">
<thead>
<tr class="bg-surface-container-low h-10 text-on-surface-variant font-label-md text-label-md uppercase tracking-wider">
<th class="py-2.5 px-space-md">Collaborateur</th>
<th class="py-2.5 px-space-sm">Département</th>
<th class="py-2.5 px-space-sm text-right">Brut (TND)</th>
<th class="py-2.5 px-space-sm text-right">Primes</th>
<th class="py-2.5 px-space-sm text-right">CNSS 9.18%</th>
<th class="py-2.5 px-space-sm text-right">IRPP</th>
<th class="py-2.5 px-space-md text-right">Net à Payer</th>
<th class="py-2.5 px-space-sm text-center">Statut</th>
<th class="py-2.5 px-space-sm text-center">Action</th>
</tr>
</thead>
<tbody class="font-body-md text-body-md divide-y divide-surface-container" id="payrollTableBody">
@forelse ($payslips as $payslip)
@php
    $badge = $statusBadge($payslip->status);
    $isSelected = $selectedPayslip !== null && $selectedPayslip->id === $payslip->id;
@endphp
<tr class="hover:bg-surface-container-low transition-colors cursor-pointer {{ $isSelected ? 'bg-primary-container/5' : '' }}" data-slip="{{ json_encode($payslip->payload) }}" onclick="selectPayslip(this)">
<td class="py-3 px-space-md">
<div class="flex items-center gap-space-sm">
@if ($payslip->employee?->avatar_url)
<img class="w-8 h-8 rounded-full object-cover shrink-0" alt="{{ $payslip->employee->name }}" src="{{ $payslip->employee->avatar_url }}"/>
@else
<div class="w-8 h-8 rounded-full bg-secondary-container text-on-secondary-container flex items-center justify-center font-bold text-label-sm shrink-0">
              {{ $payslip->employee?->initials() ?? '?' }}
            </div>
@endif
<div class="min-w-0">
<p class="font-headline-sm text-headline-sm text-on-surface leading-tight truncate">{{ $payslip->employee?->name ?? 'Collaborateur' }}</p>
<p class="font-label-sm text-label-sm text-on-surface-variant font-mono">{{ $payslip->matricule() }} • CIN {{ $payslip->employee?->cin ?: '—' }}</p>
</div>
</div>
</td>
<td class="py-3 px-space-sm">
<span class="inline-block px-2 py-0.5 rounded bg-surface-container font-label-sm text-label-sm text-on-surface">{{ $payslip->employee?->departmentShortLabel() ?? '—' }}</span>
</td>
<td class="py-3 px-space-sm text-right font-numeric-md text-numeric-md text-on-surface">{{ number_format($payslip->gross(), 3, '.', ',') }}</td>
<td class="py-3 px-space-sm text-right font-numeric-md text-numeric-md text-on-surface">+{{ number_format((float) $payslip->prime, 3, '.', ',') }}</td>
<td class="py-3 px-space-sm text-right font-numeric-md text-numeric-md {{ $payslip->status === 'ready' ? 'text-error-container text-on-surface' : 'text-on-surface-variant' }}">-{{ number_format((float) $payslip->cnss, 3, '.', ',') }}</td>
<td class="py-3 px-space-sm text-right font-numeric-md text-numeric-md text-on-surface-variant">-{{ number_format((float) $payslip->irpp, 3, '.', ',') }}</td>
<td class="py-3 px-space-md text-right font-numeric-md text-numeric-md font-bold text-primary">{{ number_format((float) $payslip->net, 3, '.', ',') }}</td>
<td class="py-3 px-space-sm text-center">
<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-label-sm font-label-sm {{ $badge['container'] }}">
<span class="w-1.5 h-1.5 rounded-full {{ $badge['dot'] }}"></span>
                    {{ $payslip->statusLabel() }}
                  </span>
</td>
<td class="py-3 px-space-sm text-center">
<button class="p-1 rounded hover:bg-surface-container text-primary transition-colors" title="Aperçu du bulletin" type="button" aria-label="Aperçu du bulletin de {{ $payslip->employee?->name }}">
<span class="material-symbols-outlined text-body-md">visibility</span>
</button>
</td>
</tr>
@empty
<tr>
<td class="py-10 px-space-md text-center text-on-surface-variant" colspan="9">
<span class="material-symbols-outlined text-headline-md block mb-2">receipt_long</span>
<p class="font-body-md text-body-md">Aucun bulletin ne correspond aux filtres sélectionnés pour ce cycle.</p>
</td>
</tr>
@endforelse
</tbody>
</table>
</div>
<!-- Table Pagination & Footer Summary -->
<div class="p-space-md bg-surface-container-low flex flex-wrap items-center justify-between gap-space-sm">
<div class="flex items-center gap-space-sm font-body-sm text-body-sm text-on-surface-variant">
<span>Affichage de {{ $payslips->firstItem() ?? 0 }} à {{ $payslips->lastItem() ?? 0 }} sur {{ $payslips->total() }} collaborateurs</span>
<span class="text-outline-variant">•</span>
<span>Total Net Émis : <strong class="text-on-surface font-semibold">{{ number_format($pageNet, 3, '.', ',') }} TND</strong></span>
</div>
<div class="flex items-center gap-space-xs">
@if ($payslips->onFirstPage())
<button class="h-8 px-3 rounded-lg bg-surface-container-lowest text-on-surface font-label-md text-label-md disabled:opacity-50 shadow-sm" disabled="">Précédent</button>
@else
<a class="h-8 px-3 rounded-lg bg-surface-container-lowest hover:bg-surface-container text-on-surface font-label-md text-label-md shadow-sm" href="{{ $payslips->previousPageUrl() }}">Précédent</a>
@endif
@foreach ($payslips->getUrlRange(max(1, $payslips->currentPage() - 1), min($payslips->lastPage(), $payslips->currentPage() + 1)) as $page => $url)
@if ($page === $payslips->currentPage())
<span class="h-8 w-8 rounded-lg bg-primary-container text-on-primary font-label-md text-label-md shadow-sm flex items-center justify-center">{{ $page }}</span>
@else
<a class="h-8 w-8 rounded-lg bg-surface-container-lowest hover:bg-surface-container text-on-surface font-label-md text-label-md shadow-sm flex items-center justify-center" href="{{ $url }}">{{ $page }}</a>
@endif
@endforeach
@if ($payslips->hasMorePages())
<a class="h-8 px-3 rounded-lg bg-surface-container-lowest hover:bg-surface-container text-on-surface font-label-md text-label-md shadow-sm" href="{{ $payslips->nextPageUrl() }}">Suivant</a>
@else
<button class="h-8 px-3 rounded-lg bg-surface-container-lowest text-on-surface font-label-md text-label-md disabled:opacity-50 shadow-sm" disabled="">Suivant</button>
@endif
</div>
</div>
</div>
<!-- Quick Regulatory Note Banner -->
<div class="bg-surface-container-low rounded-xl p-space-md flex items-center justify-between gap-space-md">
<div class="flex items-center gap-space-sm">
<span class="material-symbols-outlined text-tertiary text-headline-sm">info</span>
<p class="font-body-sm text-body-sm text-on-surface">
<strong>Rappel Loi de Finances :</strong> La Contribution Sociale de Solidarité (CSS 1%) et le nouveau barème d'abattement pour charges familiales (300 TND conjoint, 100 TND/enfant) sont appliqués automatiquement sur ce cycle.
          </p>
</div>
<button class="whitespace-nowrap font-label-md text-label-md text-primary font-semibold hover:underline" type="button" onclick="document.getElementById('bracketModal').classList.remove('hidden')">
          Consulter Barème {{ $periodStart->year }}
        </button>
</div>
</div>
<!-- Right: High Precision Payslip Slide-out / Focus View (4 cols) -->
<div class="xl:col-span-4">
<div class="bg-surface-container-lowest rounded-xl shadow-lg p-space-lg sticky top-20 flex flex-col justify-between" id="slipPrintArea">
<div>
<!-- Header Bulletin -->
<div class="flex items-start justify-between pb-space-md">
<div>
<span class="font-label-sm text-label-sm uppercase tracking-wider text-primary font-bold">Aperçu Officiel</span>
<h2 class="font-headline-md text-headline-md text-on-surface font-bold tracking-tight mt-0.5">Bulletin de Paie</h2>
<p class="font-body-sm text-body-sm text-on-surface-variant">Période : {{ $periodStart->format('d/m/Y') }} au {{ $periodEnd->format('d/m/Y') }}</p>
</div>
<div class="h-9 px-2.5 rounded-lg bg-tertiary-fixed flex items-center gap-1 text-on-tertiary-fixed font-label-sm text-label-sm font-semibold">
<span class="material-symbols-outlined text-body-sm">verified_user</span>
<span>Certifié Conforme</span>
</div>
</div>
<!-- Employer & Employee Administrative Metadata -->
<div class="bg-surface-container-low rounded-xl p-space-md space-y-space-xs mb-space-md font-body-sm text-body-sm">
<div class="flex justify-between items-center text-on-surface-variant">
<span>Société :</span>
<strong class="text-on-surface">Carthage Tech Solutions SARL</strong>
</div>
<div class="flex justify-between items-center text-on-surface-variant">
<span>Affiliation CNSS Employeur :</span>
<span class="font-mono text-on-surface">29481900-12</span>
</div>
<div class="h-px bg-surface-container my-1"></div>
<div class="flex justify-between items-center text-on-surface">
<span class="font-medium">Salarié :</span>
<strong class="text-primary font-semibold" id="slipName">{{ $slip['name'] ?? '—' }}</strong>
</div>
<div class="flex justify-between items-center text-on-surface-variant">
<span>CIN &amp; Matricule :</span>
<span class="font-mono text-on-surface" id="slipCin">{{ $slip['cin'] ?? '—' }}</span>
</div>
<div class="flex justify-between items-center text-on-surface-variant">
<span>Emploi &amp; Échelon :</span>
<span class="text-on-surface font-medium" id="slipRole">{{ $slip['role'] ?? '—' }}</span>
</div>
<div class="flex justify-between items-center text-on-surface-variant">
<span>Régime de Travail :</span>
<span class="text-on-surface">40h / Semaine • Tunis</span>
</div>
</div>
<!-- Salary Deconstruction Line Items -->
<div class="space-y-space-xs mb-space-md">
<div class="flex justify-between items-center py-1 font-body-sm text-body-sm">
<span class="text-on-surface">Salaire de Base Brut</span>
<span class="font-numeric-md text-numeric-md font-medium text-on-surface" id="slipBase">{{ $slip['base'] ?? '—' }}</span>
</div>
<div class="flex justify-between items-center py-1 font-body-sm text-body-sm text-on-surface-variant">
<span>Prime de Rendement &amp; Technicité</span>
<span class="font-numeric-md text-numeric-md text-on-surface" id="slipPrime">{{ $slip['prime'] ?? '—' }}</span>
</div>
<div class="flex justify-between items-center py-1 font-body-sm text-body-sm text-on-surface-variant">
<span>Indemnité de Transport Légale</span>
<span class="font-numeric-md text-numeric-md text-on-surface" id="slipTransport">{{ $slip['transport'] ?? '—' }}</span>
</div>
<!-- Deductions Partition -->
<div class="bg-surface-container/60 rounded-lg p-2.5 space-y-1.5 my-2">
<div class="flex justify-between items-center font-body-sm text-body-sm">
<span class="text-error font-medium">Cotisation CNSS (9.18%)</span>
<span class="font-numeric-md text-numeric-md text-error" id="slipCnss">{{ $slip['cnss'] ?? '—' }}</span>
</div>
<div class="flex justify-between items-center font-body-sm text-body-sm">
<span class="text-on-surface-variant">Base Imposable Soumise à l'IRPP</span>
<span class="font-numeric-md text-numeric-md text-on-surface font-mono" id="slipTaxBase">{{ $slip['taxBase'] ?? '—' }}</span>
</div>
<div class="flex justify-between items-center font-body-sm text-body-sm">
<span class="text-error font-medium">Retenue IRPP (Tranches {{ $periodStart->year }})</span>
<span class="font-numeric-md text-numeric-md text-error" id="slipIrpp">{{ $slip['irpp'] ?? '—' }}</span>
</div>
<div class="flex justify-between items-center font-body-sm text-body-sm">
<span class="text-error font-medium">Contrib. Solidarité Sociale (CSS 1%)</span>
<span class="font-numeric-md text-numeric-md text-error" id="slipCss">{{ $slip['css'] ?? '—' }}</span>
</div>
</div>
</div>
<!-- Net to Pay Grand Banner -->
<div class="bg-primary-container text-on-primary rounded-xl p-space-md mb-space-sm shadow-md">
<span class="font-label-sm text-label-sm tracking-wider uppercase opacity-90">Net à Payer (Virement Bancaire)</span>
<div class="flex items-baseline justify-between mt-1">
<span class="font-display-lg text-display-lg font-bold tracking-tight" id="slipNet">{{ $slip['net'] ?? '—' }}</span>
<span class="font-headline-sm text-headline-sm font-semibold opacity-95">TND</span>
</div>
<p class="font-body-sm text-body-sm opacity-90 italic mt-1" id="slipInWords">
              "{{ $slip['words'] ?? 'Aucun bulletin sélectionné' }}"
            </p>
</div>
<div class="flex items-center justify-between text-on-surface-variant font-label-sm text-label-sm px-1 mb-space-md">
<span id="slipBank">Banque : {{ $slip['bank'] ?? '—' }}</span>
<span class="font-mono" id="slipRib">{{ $slip['rib'] ?? 'RIB: —' }}</span>
</div>
</div>
<!-- Action Buttons -->
<div class="flex flex-col gap-space-xs pt-space-xs no-print">
<button class="w-full h-10 rounded-xl bg-surface-container hover:bg-surface-container-high text-on-surface font-label-lg text-label-lg flex items-center justify-center gap-space-xs transition-colors" type="button" onclick="window.print()">
<span class="material-symbols-outlined text-body-md text-primary">print</span>
<span>Imprimer Bulletin</span>
</button>
<a class="w-full h-10 rounded-xl bg-primary-container hover:bg-primary-fill text-on-primary font-label-lg text-label-lg flex items-center justify-center gap-space-xs transition-all shadow-sm" id="downloadSlipBtn" href="{{ $slip['downloadUrl'] ?? '#' }}" @if (! $slip) onclick="return false" @endif>
<span class="material-symbols-outlined text-body-md">download_done</span>
<span>Télécharger PDF Signé Électroniquement</span>
</a>
</div>
</div>
</div>
</div>
<!-- Modal Barème IRPP -->
<div class="hidden fixed inset-0 z-[70] bg-black/60 backdrop-blur-sm flex items-center justify-center p-4" id="bracketModal" role="dialog" aria-modal="true" aria-labelledby="bracketModalTitle">
<div class="w-full max-w-lg bg-surface-container-lowest rounded-xl shadow-2xl p-6 space-y-5 max-h-[90vh] overflow-y-auto">
<div class="flex items-start justify-between pb-4 border-b border-surface-container">
<div>
<h2 class="font-headline-md text-headline-md text-on-surface font-bold" id="bracketModalTitle">Barème IRPP {{ $periodStart->year }}</h2>
<p class="font-body-sm text-body-sm text-on-surface-variant">Tranches annuelles appliquées après abattement de 5 000 TND par part fiscale.</p>
</div>
<button class="p-1 rounded-lg text-on-surface-variant hover:text-on-surface hover:bg-surface-container-low transition-colors" onclick="document.getElementById('bracketModal').classList.add('hidden')" type="button" aria-label="Fermer">
<span class="material-symbols-outlined text-body-lg">close</span>
</button>
</div>
<div class="space-y-2">
@foreach ([["Jusqu'à 5 000 TND", '0%'], ['5 001 à 20 000 TND', '26%'], ['20 001 à 30 000 TND', '28%'], ['30 001 à 40 000 TND', '32%'], ['40 001 à 50 000 TND', '34%'], ['50 001 à 70 000 TND', '36%'], ['Au-delà de 70 000 TND', '38%']] as $bracket)
<div class="flex items-center justify-between px-3 py-2 rounded-lg bg-surface-container-low font-body-sm text-body-sm">
<span class="text-on-surface">{{ $bracket[0] }}</span>
<span class="font-numeric-md text-numeric-md font-bold text-primary">{{ $bracket[1] }}</span>
</div>
@endforeach
</div>
<div class="p-3 rounded-lg bg-surface-container-low font-label-sm text-label-sm text-on-surface-variant space-y-1">
<p>CNSS salariale : 9.18% • CNSS patronale : 17.37% • CSS : 1% de la base imposable.</p>
<p>Indemnité de transport légale : 100 TND / mois, non soumise à cotisation.</p>
</div>
<div class="flex justify-end pt-2">
<button class="px-4 py-2 rounded-lg bg-primary-container font-label-md text-label-md text-on-primary hover:bg-primary-fill transition-colors" onclick="document.getElementById('bracketModal').classList.add('hidden')" type="button">
          Fermer
        </button>
</div>
</div>
</div>
<style>
  @media print {
    body * { visibility: hidden; }
    #slipPrintArea, #slipPrintArea * { visibility: visible; }
    #slipPrintArea {
      position: absolute;
      left: 0;
      top: 0;
      width: 100%;
      max-width: 100%;
      border-radius: 0;
      box-shadow: none;
      -webkit-print-color-adjust: exact;
      print-color-adjust: exact;
    }
    .no-print { display: none !important; }
    @page { margin: 12mm; }
  }
</style>
<script>
  function selectPayslip(row) {
    const data = JSON.parse(row.dataset.slip);
    if (!data) return;

    document.getElementById("slipName").innerText = data.name;
    document.getElementById("slipCin").innerText = data.cin;
    document.getElementById("slipRole").innerText = data.role;
    document.getElementById("slipBase").innerText = data.base;
    document.getElementById("slipPrime").innerText = data.prime;
    document.getElementById("slipTransport").innerText = data.transport;
    document.getElementById("slipCnss").innerText = data.cnss;
    document.getElementById("slipTaxBase").innerText = data.taxBase;
    document.getElementById("slipIrpp").innerText = data.irpp;
    document.getElementById("slipCss").innerText = data.css;
    document.getElementById("slipNet").innerText = data.net;
    document.getElementById("slipInWords").innerText = `"${data.words}"`;
    document.getElementById("slipBank").innerText = "Banque : " + data.bank;
    document.getElementById("slipRib").innerText = data.rib;

    const downloadBtn = document.getElementById("downloadSlipBtn");
    downloadBtn.href = data.downloadUrl;
    downloadBtn.removeAttribute("onclick");

    document.querySelectorAll("#payrollTableBody tr[data-slip]").forEach(function (item) {
      item.classList.remove("bg-primary-container/5");
    });
    row.classList.add("bg-primary-container/5");

    const url = new URL(window.location.href);
    url.searchParams.set("view", data.id);
    history.replaceState(null, "", url);
  }

  // Quick client search filter (server-side debounced)
  const payrollSearch = document.getElementById("searchInput");
  let payrollSearchTimer;
  if (payrollSearch) {
    payrollSearch.addEventListener("input", function () {
      clearTimeout(payrollSearchTimer);
      payrollSearchTimer = setTimeout(function () {
        payrollSearch.form.requestSubmit();
      }, 500);
    });

    payrollSearch.addEventListener("keydown", function (event) {
      if (event.key === "Enter") {
        event.preventDefault();
        payrollSearch.form.requestSubmit();
      }
    });
  }

  document.getElementById("bracketModal")?.addEventListener("click", function (event) {
    if (event.target === this) {
      this.classList.add("hidden");
    }
  });

  document.addEventListener("keydown", function (event) {
    if (event.key === "Escape") {
      document.getElementById("bracketModal")?.classList.add("hidden");
    }
  });

  document.querySelectorAll("[data-flash-toast]").forEach(function (toast) {
    setTimeout(function () { toast.remove(); }, 6000);
  });
</script>
</div>
</x-admin-shell>
