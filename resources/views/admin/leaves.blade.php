@php
    $frenchMonths = [1 => 'Jan', 2 => 'Fév', 3 => 'Mars', 4 => 'Avr', 5 => 'Mai', 6 => 'Juin', 7 => 'Juil', 8 => 'Août', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Déc'];
    $frenchMonthsFull = [1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril', 5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août', 9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre'];
    $monthLabel = $frenchMonthsFull[(int) $selectedMonth->format('n')].' '.$selectedMonth->format('Y');
    $todayLabel = now()->day.' '.$frenchMonths[(int) now()->format('n')];
    $filterQuery = array_filter($filters, fn ($value) => $value !== '');
    $linkWith = fn (array $extra = []) => route('admin.leaves', array_filter(
        array_merge($filterQuery, $extra),
        fn ($value) => $value !== null && $value !== ''
    ));
    $exportUrl = route('admin.leaves.export', $filterQuery);
    $isCalendar = $display === 'calendar';
    $previousMonth = $selectedMonth->copy()->subMonthNoOverflow();
    $nextMonth = $selectedMonth->copy()->addMonthNoOverflow();
    $statusBadges = [
        'pending' => ['label' => 'En Attente', 'classes' => 'bg-secondary-container/50 text-on-secondary-fixed-variant', 'dot' => 'bg-secondary'],
        'approved' => ['label' => 'Approuvé', 'classes' => 'bg-tertiary-fixed text-on-tertiary-fixed', 'dot' => 'bg-tertiary'],
        'rejected' => ['label' => 'Rejeté', 'classes' => 'bg-error-container text-on-error-container', 'dot' => 'bg-error'],
        'cancelled' => ['label' => 'Annulé', 'classes' => 'bg-surface-container text-on-surface-variant', 'dot' => 'bg-outline'],
    ];
    $typeChipClasses = [
        'annuel' => 'bg-surface-container-high text-primary',
        'maladie' => 'bg-surface-container text-error',
        'famille' => 'bg-surface-container-high text-primary',
        'sans-solde' => 'bg-surface-container text-on-surface',
        'recup' => 'bg-surface-container text-on-surface',
    ];
    $avatarPalette = [
        'bg-secondary-container text-on-secondary-container',
        'bg-surface-container-highest text-primary',
        'bg-primary-fixed text-primary',
        'bg-tertiary-fixed text-tertiary',
    ];
    $typeOptions = [
        'annuel' => 'Congé Payé Annuel',
        'maladie' => 'Congé Maladie',
        'sans-solde' => 'Congé Sans Solde',
        'famille' => 'Événement Familial (Mariage, Décès, Naissance)',
        'recup' => 'Récupération Heures Sup.',
    ];
    $pageItems = collect();
    if ($leaves->lastPage() <= 7) {
        for ($page = 1; $page <= $leaves->lastPage(); $page++) {
            $pageItems->push($page);
        }
    } else {
        $pageItems->push(1);
        $windowStart = max(2, $leaves->currentPage() - 1);
        $windowEnd = min($leaves->lastPage() - 1, $leaves->currentPage() + 1);
        if ($windowStart > 2) {
            $pageItems->push('...');
        }
        for ($page = $windowStart; $page <= $windowEnd; $page++) {
            $pageItems->push($page);
        }
        if ($windowEnd < $leaves->lastPage() - 1) {
            $pageItems->push('...');
        }
        $pageItems->push($leaves->lastPage());
    }
    $defaultEmployee = $employees->first();
    $selectedEmployeeId = old('employee_id', $defaultEmployee?->id);
    $selectedEmployee = $employees->firstWhere('id', (int) $selectedEmployeeId) ?? $defaultEmployee;
    $defaultBalance = $selectedEmployee?->token_leave_days;
    $initialType = old('type', 'annuel');
    $defaultStart = old('start_date', now()->toDateString());
    $defaultEnd = old('end_date', now()->addDays(4)->toDateString());
    try {
        $initialDays = \App\Models\Conges::workingDaysFor($defaultStart, $defaultEnd);
    } catch (\Throwable $exception) {
        $initialDays = 0;
    }
    $initialBalance = $initialType === 'annuel' && $defaultBalance !== null
        ? max(0, (int) $defaultBalance - $initialDays)
        : $defaultBalance;
    $monthStart = $selectedMonth->copy()->startOfMonth();
    $monthEnd = $selectedMonth->copy()->endOfMonth();
    $gridStart = $monthStart->copy()->startOfWeek(\Carbon\Carbon::MONDAY);
    $gridEnd = $monthEnd->copy()->endOfWeek(\Carbon\Carbon::SUNDAY);
    $calendarDays = [];
    $leavesByDay = [];
    foreach ($calendarLeaves as $calendarLeave) {
        $cursor = $calendarLeave->start_date->copy();
        if ($cursor->lt($gridStart)) {
            $cursor = $gridStart->copy();
        }
        $limit = $calendarLeave->end_date->copy();
        if ($limit->gt($gridEnd)) {
            $limit = $gridEnd->copy();
        }
        while ($cursor->lte($limit)) {
            $leavesByDay[$cursor->toDateString()][] = $calendarLeave;
            $cursor->addDay();
        }
    }
    for ($day = $gridStart->copy(); $day->lte($gridEnd); $day->addDay()) {
        $calendarDays[] = $day->copy();
    }
@endphp
<x-admin-shell active="leaves" title="Gestion des Congés & Absences">
<div class="flex flex-col w-full pb-margin">
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
<!-- Dynamic Atmospheric Glow -->
<div class="relative w-full overflow-hidden">
<div class="absolute -top-10 left-1/4 w-96 h-48 bg-primary/5 rounded-full blur-3xl pointer-events-none"></div>
<div class="absolute top-12 right-12 w-80 h-36 bg-tertiary/5 rounded-full blur-2xl pointer-events-none"></div>
<!-- Page Header & Action Bar -->
<div class="flex flex-col md:flex-row md:items-end justify-between gap-space-md pt-space-lg mb-space-xl">
<div class="flex flex-col gap-space-xs">
<div class="flex items-center gap-space-xs font-label-sm text-label-sm text-on-surface-variant">
<span>Accueil</span>
<span class="material-symbols-outlined text-body-sm">chevron_right</span>
<span class="text-primary font-semibold">Congés</span>
</div>
<div class="flex flex-wrap items-center gap-space-sm md:items-baseline md:gap-space-md">
<h1 class="font-headline-lg text-headline-lg text-on-surface tracking-tight">Gestion des Congés &amp; Absences</h1>
<span class="font-label-sm text-label-sm px-2.5 py-0.5 rounded-full bg-surface-container-high text-primary font-semibold uppercase tracking-wider">Loi TN N° 66-27</span>
</div>
</div>
<div class="flex flex-wrap items-center gap-space-sm md:gap-space-md">
<!-- View Switcher -->
<div class="bg-surface-container-low p-1 rounded-xl flex items-center shadow-sm">
<a class="flex items-center gap-space-xs px-space-md py-1.5 rounded-lg font-label-md text-label-md transition-all {{ $isCalendar ? 'text-on-surface-variant hover:text-on-surface' : 'bg-surface-container-lowest text-primary shadow-sm font-semibold' }}" href="{{ $linkWith(['display' => null, 'view' => null]) }}">
<span class="material-symbols-outlined text-body-md" @if (! $isCalendar) style="font-variation-settings: 'FILL' 1;" @endif>table_chart</span>
<span>Vue Tableau</span>
</a>
<a class="flex items-center gap-space-xs px-space-md py-1.5 rounded-lg font-label-md text-label-md transition-all {{ $isCalendar ? 'bg-surface-container-lowest text-primary shadow-sm font-semibold' : 'text-on-surface-variant hover:text-on-surface' }}" href="{{ $linkWith(['display' => 'calendar', 'view' => null]) }}">
<span class="material-symbols-outlined text-body-md" @if ($isCalendar) style="font-variation-settings: 'FILL' 1;" @endif>calendar_month</span>
<span>Vue Calendrier d'Équipe</span>
</a>
</div>
<!-- Primary CTA Trigger Modal -->
<button class="h-10 px-space-lg rounded-xl bg-primary-container hover:bg-primary-fill text-on-primary font-label-lg text-label-lg flex items-center gap-space-xs shadow-md transition-all active:scale-95" id="openModalBtn" type="button">
<span class="material-symbols-outlined text-body-md">add_circle</span>
<span>Nouvelle Demande de Congé</span>
</button>
</div>
</div>
<!-- Top KPI Balance & Summary Cards (Bento Metric Layout) -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-gutter mb-space-xl">
<!-- Card 1: Action Required -->
<div class="relative overflow-hidden bg-surface-container-lowest rounded-xl p-space-xl shadow-sm flex flex-col justify-between group hover:shadow-md transition-shadow">
<div class="absolute right-0 top-0 w-24 h-24 bg-gradient-to-bl from-error-container/30 to-transparent rounded-bl-full pointer-events-none"></div>
<div class="flex items-center justify-between mb-space-sm">
<div class="flex items-center gap-space-xs">
<span class="w-2.5 h-2.5 rounded-full bg-error animate-ping"></span>
<span class="font-label-md text-label-md uppercase tracking-wider text-on-surface-variant font-semibold">Action Requise</span>
</div>
<span class="material-symbols-outlined text-error bg-error-container/50 p-2 rounded-xl text-headline-sm">pending_actions</span>
</div>
<div class="my-space-xs">
<div class="flex items-baseline gap-space-xs">
<span class="font-display-lg text-display-lg text-on-surface font-bold">{{ $pendingCount }}</span>
<span class="font-headline-sm text-headline-sm text-on-surface-variant font-medium">demandes</span>
</div>
<p class="font-body-sm text-body-sm text-on-surface-variant mt-1">Congés en attente de validation RH</p>
</div>
<div class="mt-space-md pt-space-xs flex items-center justify-between">
<div class="flex -space-x-2">
@forelse ($pendingAvatars as $pendingEmployee)
@php $pendingAvatarClass = $avatarPalette[$pendingEmployee->id % 4]; @endphp
@if ($pendingEmployee->avatar_url)
<img alt="Portrait de {{ $pendingEmployee->name }}" class="w-6 h-6 rounded-full ring-2 ring-surface-container-lowest object-cover" loading="lazy" src="{{ $pendingEmployee->avatar_url }}"/>
@else
<span class="w-6 h-6 rounded-full ring-2 ring-surface-container-lowest {{ $pendingAvatarClass }} flex items-center justify-center text-[9px] font-bold">{{ $pendingEmployee->initials() }}</span>
@endif
@empty
<span class="w-6 h-6 rounded-full ring-2 ring-surface-container-lowest bg-surface-container flex items-center justify-center text-on-surface-variant">
<span class="material-symbols-outlined text-[12px]">person</span>
</span>
@endforelse
</div>
<span class="font-label-sm text-label-sm text-error font-semibold flex items-center gap-1">
            À traiter avant 18:00
            <span class="material-symbols-outlined text-body-sm">arrow_forward</span>
</span>
</div>
</div>
<!-- Card 2: Collaborateurs en congé -->
<div class="relative overflow-hidden bg-surface-container-lowest rounded-xl p-space-xl shadow-sm flex flex-col justify-between group hover:shadow-md transition-shadow">
<div class="absolute right-0 top-0 w-24 h-24 bg-gradient-to-bl from-secondary-container/40 to-transparent rounded-bl-full pointer-events-none"></div>
<div class="flex items-center justify-between mb-space-sm">
<span class="font-label-md text-label-md uppercase tracking-wider text-on-surface-variant font-semibold">Effectif Absent</span>
<span class="material-symbols-outlined text-secondary bg-surface-container p-2 rounded-xl text-headline-sm">badge</span>
</div>
<div class="my-space-xs">
<div class="flex items-baseline gap-space-xs">
<span class="font-display-lg text-display-lg text-on-surface font-bold">{{ $absentToday }}</span>
<span class="font-headline-sm text-headline-sm text-on-surface-variant font-medium">collaborateurs</span>
</div>
<p class="font-body-sm text-body-sm text-on-surface-variant mt-1">En congé régulier ce jour ({{ $todayLabel }})</p>
</div>
<div class="mt-space-md pt-space-xs flex items-center justify-between">
<div class="flex items-center gap-1.5">
<span class="w-2 h-2 rounded-full bg-tertiary"></span>
<span class="font-label-sm text-label-sm text-on-surface-variant">Taux de présence: <strong class="text-on-surface font-semibold">{{ $presenceRate !== null ? number_format($presenceRate, 1, '.', '') : '—' }}%</strong></span>
</div>
<a class="font-label-sm text-label-sm text-primary font-medium hover:underline cursor-pointer" href="{{ $linkWith(['display' => 'calendar', 'view' => null]) }}">Voir planning</a>
</div>
</div>
<!-- Card 3: Solde Moyen -->
<div class="relative overflow-hidden bg-surface-container-lowest rounded-xl p-space-xl shadow-sm flex flex-col justify-between group hover:shadow-md transition-shadow">
<div class="absolute right-0 top-0 w-24 h-24 bg-gradient-to-bl from-tertiary-fixed/30 to-transparent rounded-bl-full pointer-events-none"></div>
<div class="flex items-center justify-between mb-space-sm">
<span class="font-label-md text-label-md uppercase tracking-wider text-on-surface-variant font-semibold">Réserve Globale</span>
<span class="material-symbols-outlined text-tertiary bg-surface-container-low p-2 rounded-xl text-headline-sm">account_balance_wallet</span>
</div>
<div class="my-space-xs">
<div class="flex items-baseline gap-space-xs">
<span class="font-display-lg text-display-lg text-on-surface font-bold">{{ number_format($averageBalance, 1, '.', '') }}</span>
<span class="font-headline-sm text-headline-sm text-tertiary font-medium">jours / sal.</span>
</div>
<p class="font-body-sm text-body-sm text-on-surface-variant mt-1">Solde moyen disponible par collaborateur</p>
</div>
<div class="mt-space-md pt-space-xs flex items-center justify-between">
<div class="w-full bg-surface-container-high h-2 rounded-full overflow-hidden mr-space-md">
<div class="bg-tertiary h-full rounded-full" style="width: {{ $acquiredRate }}%;"></div>
</div>
<span class="font-label-sm text-label-sm text-on-surface font-semibold whitespace-nowrap">{{ $acquiredRate }}% acquis</span>
</div>
</div>
</div>
<!-- Filter & Search Toolbar Strip -->
<form class="bg-surface-container-lowest rounded-xl p-space-md shadow-sm mb-space-lg flex flex-col lg:flex-row items-center justify-between gap-space-md" id="leave-filters" method="GET" action="{{ route('admin.leaves') }}">
@if ($isCalendar)
<input type="hidden" name="display" value="calendar"/>
@endif
<input type="hidden" name="month" value="{{ $filters['month'] }}"/>
<!-- Search Input -->
<div class="relative w-full lg:w-56 2xl:w-72 shrink-0">
<span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-body-md pointer-events-none">search</span>
<input class="w-full h-9 pl-9 pr-3 rounded-lg bg-surface-container-low font-body-sm text-body-sm text-on-surface placeholder:text-on-surface-variant focus:outline-none focus:bg-surface-container" placeholder="Filtrer collaborateur, CIN..." type="text" name="q" value="{{ $filters['q'] }}" aria-label="Rechercher une demande de congé"/>
</div>
<!-- Select Groups -->
<div class="flex flex-wrap lg:flex-nowrap items-center gap-space-sm w-full lg:w-auto">
<!-- Type Filter -->
<div class="flex items-center bg-surface-container-low rounded-lg px-2 h-9">
<span class="material-symbols-outlined text-on-surface-variant text-body-md mr-1.5">category</span>
<select class="bg-transparent font-label-md text-label-md text-on-surface focus:outline-none cursor-pointer pr-2" name="type" aria-label="Filtrer par type de congé" onchange="this.form.requestSubmit()">
<option value="">Tous les Types</option>
<option value="annuel" @selected($filters['type'] === 'annuel')>Annuel Payé (Code N° 66)</option>
<option value="maladie" @selected($filters['type'] === 'maladie')>Congé Maladie</option>
<option value="famille" @selected($filters['type'] === 'famille')>Maternité / Paternité</option>
<option value="sans-solde" @selected($filters['type'] === 'sans-solde')>Sans Solde</option>
<option value="recup" @selected($filters['type'] === 'recup')>Récupération</option>
</select>
</div>
<!-- Status Filter -->
<div class="flex items-center bg-surface-container-low rounded-lg px-2 h-9">
<span class="material-symbols-outlined text-on-surface-variant text-body-md mr-1.5">filter_list</span>
<select class="bg-transparent font-label-md text-label-md text-on-surface focus:outline-none cursor-pointer pr-2" name="status" aria-label="Filtrer par statut" onchange="this.form.requestSubmit()">
<option value="">Tous les Statuts</option>
<option value="pending" @selected($filters['status'] === 'pending')>En attente ({{ $pendingCount }})</option>
<option value="approved" @selected($filters['status'] === 'approved')>Approuvé</option>
<option value="rejected" @selected($filters['status'] === 'rejected')>Rejeté</option>
</select>
</div>
<!-- Department Filter -->
<div class="flex items-center bg-surface-container-low rounded-lg px-2 h-9">
<span class="material-symbols-outlined text-on-surface-variant text-body-md mr-1.5">domain</span>
<select class="bg-transparent font-label-md text-label-md text-on-surface focus:outline-none cursor-pointer pr-2" name="department" aria-label="Filtrer par département" onchange="this.form.requestSubmit()">
<option value="">Tous les Départements</option>
@foreach (\App\Models\Employee::DEPARTMENTS as $value => $label)
<option value="{{ $value }}" @selected($filters['department'] === $value)>{{ $label }}</option>
@endforeach
</select>
</div>
<!-- Month Picker Widget -->
<div class="flex items-center gap-space-xs bg-surface-container-high px-space-md h-9 rounded-lg text-primary">
<a class="hover:text-primary-container p-0.5" href="{{ $linkWith(['month' => $previousMonth->format('Y-m'), 'view' => null]) }}" title="Mois précédent">
<span class="material-symbols-outlined text-body-md">chevron_left</span>
</a>
<span class="font-label-md text-label-md font-semibold px-1 whitespace-nowrap">{{ $monthLabel }}</span>
<a class="hover:text-primary-container p-0.5" href="{{ $linkWith(['month' => $nextMonth->format('Y-m'), 'view' => null]) }}" title="Mois suivant">
<span class="material-symbols-outlined text-body-md">chevron_right</span>
</a>
</div>
<!-- Export CSV Button -->
<a class="h-9 px-space-md rounded-lg bg-surface-container-low hover:bg-surface-container font-label-md text-label-md text-on-surface flex items-center gap-1 transition-colors" href="{{ $exportUrl }}" title="Exporter rapport de congés">
<span class="material-symbols-outlined text-body-md">download</span>
<span class="hidden sm:inline">Export</span>
</a>
</div>
</form>
@if ($isCalendar)
<!-- Team Calendar View -->
<div class="bg-surface-container-lowest rounded-xl shadow-sm overflow-hidden">
<div class="px-space-lg py-space-md bg-surface-container-low flex flex-col sm:flex-row sm:items-center justify-between gap-space-sm">
<div class="flex items-center gap-space-sm">
<div class="w-10 h-10 rounded-xl bg-primary-container text-on-primary flex items-center justify-center shadow-sm">
<span class="material-symbols-outlined text-headline-sm">calendar_month</span>
</div>
<div>
<h2 class="font-headline-sm text-headline-sm text-on-surface font-semibold">Planning d'Équipe — {{ $monthLabel }}</h2>
<p class="font-label-sm text-label-sm text-on-surface-variant">Congés approuvés, en attente et refusés sur le mois sélectionné</p>
</div>
</div>
<div class="flex flex-wrap items-center gap-space-md font-label-sm text-label-sm text-on-surface-variant">
<span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-tertiary"></span>Approuvé</span>
<span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-secondary"></span>En Attente</span>
<span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-error"></span>Rejeté</span>
</div>
</div>
<div class="overflow-x-auto">
<div class="min-w-[760px]">
<div class="grid grid-cols-7 bg-surface-container-low/60 border-b border-surface-container">
@foreach (['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'] as $weekday)
<div class="py-2 px-2 text-center font-label-md text-label-md uppercase tracking-wider text-on-surface-variant">{{ $weekday }}</div>
@endforeach
</div>
<div class="grid grid-cols-7">
@foreach ($calendarDays as $day)
@php
    $dayLeaves = collect($leavesByDay[$day->toDateString()] ?? []);
    $isSunday = $day->dayOfWeek === \Carbon\Carbon::SUNDAY;
    $isCurrentMonth = $day->month === $selectedMonth->month && $day->year === $selectedMonth->year;
@endphp
<div class="min-h-[112px] border-b border-r border-surface-container p-1.5 flex flex-col gap-1 {{ $isSunday ? 'bg-surface-container-low/40' : '' }} {{ $isCurrentMonth ? '' : 'opacity-40' }}">
<div class="flex items-center justify-between gap-1">
<span class="w-6 h-6 rounded-full flex items-center justify-center font-label-sm text-label-sm {{ $day->isToday() ? 'bg-primary-container text-on-primary font-bold' : 'text-on-surface-variant' }}">{{ $day->day }}</span>
@if ($isSunday)
<span class="font-label-sm text-[9px] uppercase tracking-wider text-on-surface-variant/70">Repos</span>
@endif
</div>
@foreach ($dayLeaves->take(2) as $dayLeave)
@php $dayStatusMeta = $statusBadges[$dayLeave->status] ?? $statusBadges['pending']; @endphp
<a class="truncate rounded px-1.5 py-0.5 font-label-sm text-[10px] font-semibold {{ $dayStatusMeta['classes'] }} hover:opacity-80 transition-opacity" href="{{ $linkWith(['display' => 'calendar', 'view' => $dayLeave->id]) }}" title="{{ $dayLeave->employeeName() }} — {{ $dayLeave->typeLabel() }} ({{ $dayLeave->statusLabel() }})">
<span class="inline-block w-1.5 h-1.5 rounded-full {{ $dayStatusMeta['dot'] }} mr-1"></span>{{ $dayLeave->employeeInitials() }} · {{ $dayLeave->typeLabel() }}
</a>
@endforeach
@if ($dayLeaves->count() > 2)
<span class="font-label-sm text-[10px] text-on-surface-variant pl-1">+{{ $dayLeaves->count() - 2 }} autre{{ $dayLeaves->count() - 2 > 1 ? 's' : '' }}</span>
@endif
</div>
@endforeach
</div>
</div>
</div>
<div class="bg-surface-container-low px-space-lg py-space-sm flex flex-col sm:flex-row items-center justify-between gap-space-sm">
<span class="font-label-sm text-label-sm text-on-surface-variant">
<span class="font-semibold text-on-surface">{{ $calendarLeaves->count() }}</span> demande{{ $calendarLeaves->count() > 1 ? 's' : '' }} sur la période
</span>
<a class="font-label-sm text-label-sm text-primary font-semibold hover:underline flex items-center gap-1" href="{{ $linkWith(['display' => null, 'view' => null]) }}">
<span class="material-symbols-outlined text-body-sm">table_chart</span>
Revenir à la vue tableau
</a>
</div>
</div>
@else
<!-- Data Table Container -->
<div class="bg-surface-container-lowest rounded-xl shadow-sm overflow-hidden">
<div class="overflow-x-auto">
<table class="w-full table-fixed min-w-[1040px] text-left border-collapse">
<colgroup>
<col class="w-[18%]"/>
<col class="w-[13%]"/>
<col class="w-[16%]"/>
<col class="w-[8%]"/>
<col class="w-[9%]"/>
<col class="w-[9%]"/>
<col class="w-[10%]"/>
<col class="w-[17%]"/>
</colgroup>
<thead>
<tr class="bg-surface-container-low h-10 text-on-surface-variant font-label-md text-label-md uppercase tracking-wider">
<th class="py-2.5 px-space-lg font-semibold">Collaborateur</th>
<th class="py-2.5 px-space-md font-semibold">Type de Congé</th>
<th class="py-2.5 px-space-md font-semibold">Période (Début - Fin)</th>
<th class="py-2.5 px-space-md font-semibold text-center">Durée</th>
<th class="py-2.5 px-space-md font-semibold">Motif / Justificatif</th>
<th class="py-2.5 px-space-md font-semibold text-right">Solde Restant</th>
<th class="py-2.5 px-space-md font-semibold text-center">Statut</th>
<th class="py-2.5 px-space-md font-semibold text-right">Actions</th>
</tr>
</thead>
<tbody class="divide-y-0">
@forelse ($leaves as $leave)
@php
    $rowStatusMeta = $statusBadges[$leave->status] ?? $statusBadges['pending'];
    $rowAvatarClass = $avatarPalette[($leave->employee?->id ?? 0) % 4];
@endphp
<tr class="h-16 hover:bg-surface-container-low transition-colors bg-surface-container-lowest">
<td class="py-3 px-space-lg overflow-hidden">
<div class="flex items-center gap-space-md min-w-0">
<div class="relative shrink-0">
@if ($leave->employeeAvatar())
<img alt="Portrait de {{ $leave->employeeName() }}" class="w-9 h-9 rounded-full object-cover" loading="lazy" src="{{ $leave->employeeAvatar() }}"/>
@else
<div class="w-9 h-9 rounded-full {{ $rowAvatarClass }} flex items-center justify-center font-label-md text-label-md font-bold">{{ $leave->employeeInitials() }}</div>
@endif
<span class="absolute bottom-0 right-0 w-2.5 h-2.5 rounded-full {{ $rowStatusMeta['dot'] }} ring-2 ring-surface-container-lowest"></span>
</div>
<div class="flex flex-col min-w-0">
<span class="font-label-lg text-label-lg text-on-surface font-semibold truncate">{{ $leave->employeeName() }}</span>
<span class="font-label-sm text-label-sm text-on-surface-variant truncate">{{ $leave->employeePosition() }} • {{ $leave->employeeDepartmentShort() }}</span>
</div>
</div>
</td>
<td class="py-3 px-space-md overflow-hidden">
<div class="flex items-center gap-1.5 min-w-0">
<span class="material-symbols-outlined {{ $leave->typeColor() }} text-body-md shrink-0">{{ $leave->typeIcon() }}</span>
<span class="font-body-md text-on-surface font-medium truncate text-[13px] leading-5" title="{{ $leave->typeLabel() }}">{{ $leave->typeLabel() }}</span>
</div>
</td>
<td class="py-3 px-space-md overflow-hidden">
<div class="flex flex-col font-numeric-md text-numeric-md text-on-surface min-w-0">
<span class="font-semibold whitespace-nowrap text-[13px] leading-5" title="{{ $leave->start_date?->format('d/m/Y') }} → {{ $leave->end_date?->format('d/m/Y') }}">
{{ $leave->start_date?->format('d/m/Y') }}
@if ($leave->start_date && $leave->end_date && ! $leave->start_date->equalTo($leave->end_date))
<span class="text-on-surface-variant font-normal">au</span> {{ $leave->end_date->format('d/m/Y') }}
@endif
</span>
<span class="font-label-sm text-label-sm text-on-surface-variant truncate">{{ $leave->periodNote() }}</span>
</div>
</td>
<td class="py-3 px-space-md text-center overflow-hidden">
<span class="inline-flex items-center justify-center px-1.5 py-0.5 rounded-md {{ $typeChipClasses[$leave->type] ?? 'bg-surface-container text-on-surface' }} font-numeric-md text-numeric-md font-bold whitespace-nowrap">
                  {{ $leave->working_days }} jour{{ $leave->working_days > 1 ? 's' : '' }}
                </span>
</td>
<td class="py-3 px-space-md overflow-hidden">
@if ($leave->attachment_path)
<a class="flex items-center gap-1.5 px-2 py-1 rounded bg-surface-container-low max-w-full w-fit hover:bg-surface-container cursor-pointer" href="{{ route('admin.leaves.attachment', $leave) }}" title="Télécharger {{ $leave->attachment_name }}">
<span class="material-symbols-outlined text-error text-body-sm shrink-0">picture_as_pdf</span>
<span class="font-label-sm text-label-sm text-on-surface font-medium underline truncate min-w-0">{{ $leave->attachment_name }}</span>
</a>
@else
<div class="flex items-center gap-1.5 text-on-surface-variant min-w-0">
<span class="material-symbols-outlined text-body-sm shrink-0">{{ $leave->reasonIcon() }}</span>
<span class="font-body-sm text-body-sm text-on-surface truncate min-w-0" title="{{ $leave->reason }}">{{ $leave->reason }}</span>
</div>
@endif
</td>
<td class="py-3 px-space-md text-right overflow-hidden">
<span class="font-numeric-md text-numeric-md font-semibold text-on-surface whitespace-nowrap">{{ $leave->balanceAfterDays() !== null ? $leave->balanceAfterDays().' jours' : '—' }}</span>
<span class="block font-label-sm text-label-sm text-on-surface-variant truncate" title="{{ $leave->balanceCaption() }}">{{ $leave->balanceCaption() }}</span>
</td>
<td class="py-3 px-space-md text-center">
<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full {{ $rowStatusMeta['classes'] }} font-label-sm text-label-sm font-semibold whitespace-nowrap">
<span class="w-2 h-2 rounded-full {{ $rowStatusMeta['dot'] }}"></span>
                  {{ $rowStatusMeta['label'] }}
                </span>
</td>
<td class="py-3 px-space-md text-right overflow-hidden">
@if ($leave->isPending())
<div class="flex items-center justify-end gap-1">
<form action="{{ route('admin.leaves.approve', $leave) }}" method="POST" class="shrink-0">
@csrf
@method('PATCH')
<button class="h-8 px-2 rounded-lg bg-tertiary-container hover:bg-tertiary text-on-tertiary font-label-sm text-label-sm font-semibold flex items-center gap-1 transition-colors shadow-sm whitespace-nowrap" type="submit" title="Approuver la demande">
<span class="material-symbols-outlined text-body-sm">check</span>
<span>Approuver</span>
</button>
</form>
<form action="{{ route('admin.leaves.reject', $leave) }}" method="POST" class="shrink-0" onsubmit="return confirm('Refuser la demande de congé de {{ addslashes($leave->employeeName()) }} ?')">
@csrf
@method('PATCH')
<button class="h-8 px-2 rounded-lg bg-error-container hover:bg-error hover:text-on-error text-error font-label-sm text-label-sm font-semibold flex items-center gap-1 transition-colors whitespace-nowrap" type="submit" title="Refuser la demande">
<span class="material-symbols-outlined text-body-sm">close</span>
<span>Refuser</span>
</button>
</form>
</div>
@else
<div class="flex items-center justify-end gap-1">
@if ($leave->isApproved() && $leave->isCancellable())
<form action="{{ route('admin.leaves.stop', $leave) }}" class="shrink-0" data-days="{{ $leave->remainingWorkingDays() }}" data-employee="{{ $leave->employeeName() }}" data-mode="{{ $leave->isInProgress() ? 'progress' : 'future' }}" data-period="{{ $leave->start_date?->format('d/m/Y') }} → {{ $leave->end_date?->format('d/m/Y') }}" data-stop-leave data-type="{{ $leave->typeLabel() }}" method="POST">
@csrf
@method('PATCH')
<button class="h-8 px-2 rounded-lg bg-error-container hover:bg-error hover:text-on-error text-error font-label-sm text-label-sm font-semibold flex items-center gap-1 transition-colors whitespace-nowrap" type="submit" title="Arrêter le congé approuvé">
<span class="material-symbols-outlined text-body-sm">stop_circle</span>
<span>Arrêter</span>
</button>
</form>
@endif
<a class="h-8 px-3 rounded-lg bg-surface-container-low hover:bg-surface-container text-on-surface font-label-sm text-label-sm font-medium transition-colors inline-flex items-center" href="{{ $linkWith(['view' => $leave->id, 'display' => $isCalendar ? 'calendar' : null]) }}">
                  Détails
                </a>
</div>
@endif
</td>
</tr>
@empty
<tr>
<td class="py-space-xl px-space-lg text-center" colspan="8">
<div class="flex flex-col items-center gap-space-sm text-on-surface-variant">
<span class="material-symbols-outlined text-display-lg">event_busy</span>
<span class="font-headline-sm text-headline-sm text-on-surface font-semibold">Aucune demande de congé sur cette période</span>
<span class="font-body-sm text-body-sm">Ajustez le mois ou les filtres pour afficher d'autres demandes.</span>
<a class="mt-space-xs font-label-md text-label-md text-primary font-semibold hover:underline" href="{{ route('admin.leaves', ['month' => $filters['month']]) }}">Réinitialiser les filtres</a>
</div>
</td>
</tr>
@endforelse
</tbody>
</table>
</div>
<!-- Pagination footer inside card -->
<div class="bg-surface-container-low px-space-lg py-space-sm flex flex-col sm:flex-row items-center justify-between gap-space-sm">
<span class="font-label-sm text-label-sm text-on-surface-variant">
@if ($leaves->total() > 0)
          Affichage de <span class="font-semibold text-on-surface">{{ $leaves->firstItem() }} à {{ $leaves->lastItem() }}</span> sur <span class="font-semibold text-on-surface">{{ $leaves->total() }}</span> demandes de congés
@else
          Aucune demande de congé
@endif
        </span>
<div class="flex items-center gap-space-xs">
@if ($leaves->onFirstPage())
<span class="w-8 h-8 rounded-lg bg-surface-container-lowest text-on-surface-variant flex items-center justify-center shadow-sm opacity-40 pointer-events-none" aria-disabled="true">
<span class="material-symbols-outlined text-body-md">chevron_left</span>
</span>
@else
<a class="w-8 h-8 rounded-lg bg-surface-container-lowest text-on-surface-variant hover:text-on-surface flex items-center justify-center shadow-sm" href="{{ $leaves->previousPageUrl() }}" aria-label="Page précédente">
<span class="material-symbols-outlined text-body-md">chevron_left</span>
</a>
@endif
@foreach ($pageItems as $pageItem)
@if ($pageItem === '...')
<span class="px-1 text-on-surface-variant font-label-sm text-label-sm">...</span>
@elseif ((int) $pageItem === $leaves->currentPage())
<span class="w-8 h-8 rounded-lg bg-primary-container text-on-primary font-label-sm text-label-sm font-bold shadow-sm flex items-center justify-center" aria-current="page">{{ $pageItem }}</span>
@else
<a class="w-8 h-8 rounded-lg bg-surface-container-lowest text-on-surface hover:bg-surface-container font-label-sm text-label-sm font-semibold shadow-sm flex items-center justify-center" href="{{ $leaves->url($pageItem) }}">{{ $pageItem }}</a>
@endif
@endforeach
@if ($leaves->hasMorePages())
<a class="w-8 h-8 rounded-lg bg-surface-container-lowest text-on-surface-variant hover:text-on-surface flex items-center justify-center shadow-sm" href="{{ $leaves->nextPageUrl() }}" aria-label="Page suivante">
<span class="material-symbols-outlined text-body-md">chevron_right</span>
</a>
@else
<span class="w-8 h-8 rounded-lg bg-surface-container-lowest text-on-surface-variant flex items-center justify-center shadow-sm opacity-40 pointer-events-none" aria-disabled="true">
<span class="material-symbols-outlined text-body-md">chevron_right</span>
</span>
@endif
</div>
</div>
</div>
@endif
</div>
</div>

@if ($viewLeave)
@php
    $viewStatusMeta = $statusBadges[$viewLeave->status] ?? $statusBadges['pending'];
    $viewAvatarClass = $avatarPalette[($viewLeave->employee?->id ?? 0) % 4];
    $viewCloseUrl = $linkWith(['view' => null]);
@endphp
<!-- Leave Details Drawer -->
<div class="fixed inset-0 z-50 overflow-hidden flex justify-end transition-opacity duration-300" id="view-leave-modal">
<div class="fixed inset-0 bg-black/60 backdrop-blur-xs transition-opacity" id="view-leave-backdrop"></div>
<div class="relative w-full max-w-2xl bg-surface-container-lowest shadow-2xl flex flex-col h-full z-10 overflow-hidden">
<!-- Drawer Header -->
<div class="p-space-xl bg-surface-container-low/60 flex items-start justify-between gap-space-md">
<div class="flex items-center gap-space-md min-w-0">
<div class="relative shrink-0">
@if ($viewLeave->employeeAvatar())
<img alt="Portrait de {{ $viewLeave->employeeName() }}" class="w-14 h-14 rounded-full object-cover" src="{{ $viewLeave->employeeAvatar() }}"/>
@else
<div class="w-14 h-14 rounded-full {{ $viewAvatarClass }} font-headline-md text-headline-md flex items-center justify-center font-bold">{{ $viewLeave->employeeInitials() }}</div>
@endif
<span class="absolute bottom-0 right-0 w-3 h-3 rounded-full {{ $viewStatusMeta['dot'] }} ring-2 ring-surface-container-lowest"></span>
</div>
<div class="flex flex-col min-w-0">
<h2 class="font-headline-md text-headline-md text-on-surface tracking-tight truncate">{{ $viewLeave->employeeName() }}</h2>
<p class="font-body-sm text-body-sm text-on-surface-variant truncate">{{ $viewLeave->employeePosition() }}</p>
<div class="flex flex-wrap items-center gap-1.5 mt-1">
<span class="px-2.5 py-1 rounded-full bg-surface-container text-primary font-label-sm text-label-sm font-semibold">{{ $viewLeave->employeeDepartment() }}</span>
<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full {{ $viewStatusMeta['classes'] }} font-label-sm text-label-sm font-semibold">
<span class="w-1.5 h-1.5 rounded-full {{ $viewStatusMeta['dot'] }}"></span>
{{ $viewStatusMeta['label'] }}
</span>
</div>
</div>
</div>
<button aria-label="Fermer" class="w-9 h-9 rounded-xl hover:bg-surface-container text-on-surface-variant hover:text-on-surface flex items-center justify-center transition-colors shrink-0" id="close-view-leave-btn" type="button">
<span class="material-symbols-outlined text-headline-sm">close</span>
</button>
</div>
<!-- Drawer Content -->
<div class="flex-1 overflow-y-auto p-space-xl space-y-space-lg">
<!-- Request Details -->
<div class="bg-surface-container-low/50 rounded-xl p-space-md">
<h3 class="font-label-md text-label-md text-on-surface-variant uppercase tracking-wider mb-space-sm">Détails de la demande</h3>
<div class="grid grid-cols-1 sm:grid-cols-2 gap-space-md">
<div class="flex items-start gap-space-sm">
<span class="material-symbols-outlined {{ $viewLeave->typeColor() }} text-body-lg">{{ $viewLeave->typeIcon() }}</span>
<div class="flex flex-col">
<span class="font-label-sm text-label-sm text-on-surface-variant">Type de congé</span>
<span class="font-body-md text-body-md text-on-surface font-medium">{{ $viewLeave->typeLabel() }}</span>
</div>
</div>
<div class="flex items-start gap-space-sm">
<span class="material-symbols-outlined text-primary text-body-lg">timer</span>
<div class="flex flex-col">
<span class="font-label-sm text-label-sm text-on-surface-variant">Durée décomptée</span>
<span class="font-body-md text-body-md text-on-surface font-medium">{{ $viewLeave->working_days }} jour{{ $viewLeave->working_days > 1 ? 's' : '' }} ouvré{{ $viewLeave->working_days > 1 ? 's' : '' }}</span>
</div>
</div>
<div class="flex items-start gap-space-sm">
<span class="material-symbols-outlined text-primary text-body-lg">event</span>
<div class="flex flex-col">
<span class="font-label-sm text-label-sm text-on-surface-variant">Période</span>
<span class="font-body-md text-body-md text-on-surface font-medium">
{{ $viewLeave->start_date?->format('d/m/Y') }}
@if ($viewLeave->start_date && $viewLeave->end_date && ! $viewLeave->start_date->equalTo($viewLeave->end_date))
— {{ $viewLeave->end_date->format('d/m/Y') }}
@endif
</span>
<span class="font-label-sm text-label-sm text-on-surface-variant">{{ $viewLeave->periodNote() }}</span>
</div>
</div>
<div class="flex items-start gap-space-sm">
<span class="material-symbols-outlined text-primary text-body-lg">account_balance_wallet</span>
<div class="flex flex-col">
<span class="font-label-sm text-label-sm text-on-surface-variant">Solde actuel</span>
<span class="font-body-md text-body-md text-on-surface font-medium">{{ $viewLeave->balanceDays() !== null ? $viewLeave->balanceDays().' jours' : 'Non renseigné' }}</span>
<span class="font-label-sm text-label-sm text-on-surface-variant">{{ $viewLeave->balanceCaption() }}</span>
</div>
</div>
<div class="flex items-start gap-space-sm sm:col-span-2">
<span class="material-symbols-outlined text-primary text-body-lg">{{ $viewLeave->reasonIcon() }}</span>
<div class="flex flex-col min-w-0">
<span class="font-label-sm text-label-sm text-on-surface-variant">Motif / Commentaire</span>
<span class="font-body-md text-body-md text-on-surface">{{ $viewLeave->reason }}</span>
</div>
</div>
</div>
@if ($viewLeave->attachment_path)
<div class="mt-space-md rounded-xl bg-surface-container-lowest p-space-sm flex items-center justify-between gap-space-sm shadow-xs">
<div class="flex items-center gap-space-sm min-w-0">
<span class="material-symbols-outlined text-error text-headline-sm">picture_as_pdf</span>
<div class="flex flex-col min-w-0">
<span class="font-label-md text-label-md text-on-surface font-semibold truncate">{{ $viewLeave->attachment_name }}</span>
<span class="font-label-sm text-label-sm text-on-surface-variant">Pièce justificative téléversée</span>
</div>
</div>
<a class="h-9 px-space-md rounded-xl bg-surface-container-low hover:bg-surface-container text-primary font-label-md text-label-md shadow-xs flex items-center gap-1 transition-colors shrink-0" href="{{ route('admin.leaves.attachment', $viewLeave) }}">
<span class="material-symbols-outlined text-body-md">download</span>
<span>Télécharger</span>
</a>
</div>
@endif
</div>
<!-- Collaborator -->
<div class="bg-surface-container-low/50 rounded-xl p-space-md">
<h3 class="font-label-md text-label-md text-on-surface-variant uppercase tracking-wider mb-space-sm">Collaborateur</h3>
<div class="grid grid-cols-1 sm:grid-cols-2 gap-space-md">
<div class="flex items-start gap-space-sm">
<span class="material-symbols-outlined text-primary text-body-lg">mail</span>
<div class="flex flex-col min-w-0">
<span class="font-label-sm text-label-sm text-on-surface-variant">Email</span>
<span class="font-body-md text-body-md text-on-surface truncate">{{ $viewLeave->employeeEmail() ?? 'Non renseigné' }}</span>
</div>
</div>
<div class="flex items-start gap-space-sm">
<span class="material-symbols-outlined text-primary text-body-lg">call</span>
<div class="flex flex-col">
<span class="font-label-sm text-label-sm text-on-surface-variant">Mobile</span>
<span class="font-numeric-md text-numeric-md font-mono text-on-surface">{{ $viewLeave->employeeMobile() ?? 'Non renseigné' }}</span>
</div>
</div>
<div class="flex items-start gap-space-sm">
<span class="material-symbols-outlined text-primary text-body-lg">badge</span>
<div class="flex flex-col">
<span class="font-label-sm text-label-sm text-on-surface-variant">CIN tunisienne</span>
<span class="font-numeric-md text-numeric-md font-mono text-on-surface">{{ $viewLeave->employeeCin() ?? 'Non renseignée' }}</span>
</div>
</div>
<div class="flex items-start gap-space-sm">
<span class="material-symbols-outlined text-primary text-body-lg">domain</span>
<div class="flex flex-col">
<span class="font-label-sm text-label-sm text-on-surface-variant">Département</span>
<span class="font-body-md text-body-md text-on-surface">{{ $viewLeave->employeeDepartment() }}</span>
</div>
</div>
<div class="flex items-start gap-space-sm sm:col-span-2">
<span class="material-symbols-outlined text-primary text-body-lg">work</span>
<div class="flex flex-col">
<span class="font-label-sm text-label-sm text-on-surface-variant">Poste occupé</span>
<span class="font-body-md text-body-md text-on-surface">{{ $viewLeave->employeePosition() }}</span>
</div>
</div>
</div>
</div>
<!-- Validation -->
<div class="bg-surface-container-low/50 rounded-xl p-space-md">
<h3 class="font-label-md text-label-md text-on-surface-variant uppercase tracking-wider mb-space-sm">Validation RH</h3>
<div class="grid grid-cols-1 sm:grid-cols-2 gap-space-md">
<div class="flex items-start gap-space-sm">
<span class="material-symbols-outlined text-primary text-body-lg">verified_user</span>
<div class="flex flex-col">
<span class="font-label-sm text-label-sm text-on-surface-variant">Statut de la demande</span>
<span class="inline-flex items-center gap-1.5 px-2.5 py-1 mt-0.5 rounded-full {{ $viewStatusMeta['classes'] }} font-label-sm text-label-sm font-semibold w-fit">
<span class="w-1.5 h-1.5 rounded-full {{ $viewStatusMeta['dot'] }}"></span>
{{ $viewStatusMeta['label'] }}
</span>
</div>
</div>
<div class="flex items-start gap-space-sm">
<span class="material-symbols-outlined text-primary text-body-lg">schedule</span>
<div class="flex flex-col">
<span class="font-label-sm text-label-sm text-on-surface-variant">Traitée le</span>
<span class="font-body-md text-body-md text-on-surface">{{ $viewLeave->reviewed_at?->format('d/m/Y à H:i') ?? 'En attente de traitement' }}</span>
</div>
</div>
<div class="flex items-start gap-space-sm">
<span class="material-symbols-outlined text-primary text-body-lg">person_check</span>
<div class="flex flex-col">
<span class="font-label-sm text-label-sm text-on-surface-variant">Traitée par</span>
<span class="font-body-md text-body-md text-on-surface">{{ $viewLeave->reviewer?->name ?? '—' }}</span>
</div>
</div>
@if ($viewLeave->decision_note)
<div class="flex items-start gap-space-sm sm:col-span-2">
<span class="material-symbols-outlined text-primary text-body-lg">sticky_note_2</span>
<div class="flex flex-col">
<span class="font-label-sm text-label-sm text-on-surface-variant">Note de décision</span>
<span class="font-body-md text-body-md text-on-surface">{{ $viewLeave->decision_note }}</span>
</div>
</div>
@endif
</div>
</div>
</div>
<!-- Drawer Footer -->
<div class="p-space-lg bg-surface-container-lowest shadow-[0_-4px_12px_rgba(0,0,0,0.03)] flex flex-wrap items-center justify-between gap-space-sm">
<button class="h-10 px-space-lg rounded-xl bg-surface-container-low hover:bg-surface-container text-on-surface font-label-lg text-label-lg transition-colors" id="cancel-view-leave-btn" type="button">
          Fermer
        </button>
@if ($viewLeave->isPending())
<div class="flex items-center gap-1.5">
<form action="{{ route('admin.leaves.reject', $viewLeave) }}" method="POST" onsubmit="return confirm('Refuser la demande de congé de {{ addslashes($viewLeave->employeeName()) }} ?')">
@csrf
@method('PATCH')
<button class="h-10 px-space-md rounded-xl bg-error-container hover:bg-error hover:text-on-error text-error font-label-md text-label-md font-semibold flex items-center gap-1 transition-colors" type="submit">
<span class="material-symbols-outlined text-body-md">close</span>
<span>Refuser</span>
</button>
</form>
<form action="{{ route('admin.leaves.approve', $viewLeave) }}" method="POST">
@csrf
@method('PATCH')
<button class="h-10 px-space-lg rounded-xl bg-tertiary-container hover:bg-tertiary text-on-tertiary font-label-md text-label-md font-semibold flex items-center gap-1 transition-colors shadow-sm" type="submit">
<span class="material-symbols-outlined text-body-md">check</span>
<span>Approuver</span>
</button>
</form>
</div>
@elseif ($viewLeave->isApproved() && $viewLeave->isCancellable())
<div class="flex items-center gap-1.5">
<form action="{{ route('admin.leaves.stop', $viewLeave) }}" data-days="{{ $viewLeave->remainingWorkingDays() }}" data-employee="{{ $viewLeave->employeeName() }}" data-mode="{{ $viewLeave->isInProgress() ? 'progress' : 'future' }}" data-period="{{ $viewLeave->start_date?->format('d/m/Y') }} → {{ $viewLeave->end_date?->format('d/m/Y') }}" data-stop-leave data-type="{{ $viewLeave->typeLabel() }}" method="POST">
@csrf
@method('PATCH')
<button class="h-10 px-space-md rounded-xl bg-error-container hover:bg-error hover:text-on-error text-error font-label-md text-label-md font-semibold flex items-center gap-1 transition-colors" type="submit">
<span class="material-symbols-outlined text-body-md">stop_circle</span>
<span>Arrêter le congé</span>
</button>
</form>
</div>
@else
<span class="font-label-sm text-label-sm text-on-surface-variant flex items-center gap-1.5">
<span class="material-symbols-outlined text-tertiary text-body-md">task_alt</span>
Demande traitée
</span>
@endif
</div>
</div>
</div>
@endif

<!-- Slide-Over Drawer: Nouvelle Demande de Congé -->
<div class="fixed inset-0 z-50 overflow-hidden flex justify-end {{ $errors->any() ? '' : 'hidden' }}" data-quick-create="{{ request()->boolean('new') ? '1' : '0' }}" id="leaveModal">
<!-- Overlay backdrop -->
<div class="fixed inset-0 bg-black/60 backdrop-blur-xs transition-opacity" id="modalBackdrop"></div>
<!-- Slide-over Drawer Panel -->
<div class="relative w-full max-w-2xl bg-surface-container-lowest shadow-2xl flex flex-col h-full z-10 overflow-hidden">
<!-- Drawer Header -->
<div class="p-space-xl bg-surface-container-low/60 flex items-start justify-between gap-space-md">
<div class="flex items-center gap-space-md">
<div class="w-12 h-12 rounded-xl bg-primary-container text-on-primary flex items-center justify-center shadow-sm shrink-0">
<span class="material-symbols-outlined text-headline-lg">event_note</span>
</div>
<div class="flex flex-col">
<h2 class="font-headline-md text-headline-md text-on-surface tracking-tight">Soumettre une demande de congé</h2>
<p class="font-body-sm text-body-sm text-on-surface-variant">Conformité légale selon le Code du Travail Tunisien</p>
</div>
</div>
<button aria-label="Fermer" class="w-9 h-9 rounded-xl hover:bg-surface-container text-on-surface-variant hover:text-on-surface flex items-center justify-center transition-colors shrink-0" id="closeModalBtn" type="button">
<span class="material-symbols-outlined text-headline-sm">close</span>
</button>
</div>
<!-- Drawer Form Content -->
<form action="{{ route('admin.leaves.store') }}" class="flex-1 overflow-y-auto p-space-xl flex flex-col gap-space-md" enctype="multipart/form-data" id="leaveForm" method="POST">
@csrf
@if ($errors->any())
<div class="p-space-md rounded-xl bg-error-container text-on-error-container flex items-start gap-space-sm">
<span class="material-symbols-outlined text-body-lg shrink-0 mt-0.5">error</span>
<div class="flex flex-col gap-1">
<span class="font-label-md text-label-md font-semibold">Veuillez corriger les informations suivantes</span>
<ul class="list-disc list-inside font-body-sm text-body-sm space-y-0.5">
@foreach ($errors->all() as $error)
<li>{{ $error }}</li>
@endforeach
</ul>
</div>
</div>
@endif
<!-- Field 1: Collaborateur Dropdown -->
<div class="flex flex-col gap-space-xs">
<label class="font-label-md text-label-md text-on-surface font-medium" for="leaveEmployeeSelect">Collaborateur Bénéficiaire</label>
<div class="relative">
<select class="w-full appearance-none h-10 pl-3 pr-8 rounded-xl bg-surface-container-lowest text-on-surface font-body-md text-body-md focus:outline-none ring-1 ring-outline-variant focus:ring-2 focus:ring-primary transition-all cursor-pointer" id="leaveEmployeeSelect" name="employee_id" required>
@forelse ($employees as $employee)
<option value="{{ $employee->id }}" data-balance="{{ $employee->token_leave_days ?? '' }}" @selected((int) $selectedEmployeeId === $employee->id)>{{ $employee->name }} — {{ $employee->position }} ({{ $employee->departmentShortLabel() }})</option>
@empty
<option value="">Aucun collaborateur disponible</option>
@endforelse
</select>
<span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-on-surface-variant pointer-events-none text-body-md">expand_more</span>
</div>
</div>
<!-- Field 2: Type de Congé -->
<div class="flex flex-col gap-space-xs">
<label class="font-label-md text-label-md text-on-surface font-medium" for="leaveTypeSelect">Type de Congé</label>
<div class="relative">
<select class="w-full appearance-none h-10 pl-3 pr-8 rounded-xl bg-surface-container-lowest text-on-surface font-body-md text-body-md focus:outline-none ring-1 ring-outline-variant focus:ring-2 focus:ring-primary transition-all cursor-pointer" id="leaveTypeSelect" name="type" required>
@foreach ($typeOptions as $value => $label)
<option value="{{ $value }}" @selected($initialType === $value)>{{ $label }}@if ($value === 'annuel') (Solde actuel disponible : {{ $defaultBalance ?? 0 }} jours)@elseif ($value === 'maladie') (Justificatif médical requis)@endif</option>
@endforeach
</select>
<span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-on-surface-variant pointer-events-none text-body-md">expand_more</span>
</div>
</div>
<!-- Date Range Grid with calculated duration -->
<div class="grid grid-cols-1 sm:grid-cols-2 gap-space-md">
<div class="flex flex-col gap-space-xs">
<label class="font-label-md text-label-md text-on-surface font-medium" for="startDate">Date Début</label>
<input class="w-full h-10 px-3 rounded-xl bg-surface-container-lowest text-on-surface font-body-md text-body-md focus:outline-none ring-1 ring-outline-variant focus:ring-2 focus:ring-primary transition-all" id="startDate" name="start_date" type="date" value="{{ $defaultStart }}" required/>
</div>
<div class="flex flex-col gap-space-xs">
<label class="font-label-md text-label-md text-on-surface font-medium" for="endDate">Date Fin</label>
<input class="w-full h-10 px-3 rounded-xl bg-surface-container-lowest text-on-surface font-body-md text-body-md focus:outline-none ring-1 ring-outline-variant focus:ring-2 focus:ring-primary transition-all" id="endDate" name="end_date" type="date" value="{{ $defaultEnd }}" required/>
</div>
</div>
<!-- Live Calculation & Balance Projection Card -->
<div class="p-space-md rounded-xl bg-surface-container-low flex items-center justify-between">
<div class="flex items-center gap-space-sm">
<div class="w-8 h-8 rounded-lg bg-surface-container-high flex items-center justify-center text-primary">
<span class="material-symbols-outlined text-body-lg">calculate</span>
</div>
<div>
<p class="font-label-md text-label-md text-on-surface font-semibold">Durée calculée : <span class="text-primary font-bold" id="durationDisplay">{{ $initialDays }} jour{{ $initialDays > 1 ? 's' : '' }} ouvré{{ $initialDays > 1 ? 's' : '' }}</span></p>
<p class="font-label-sm text-label-sm text-on-surface-variant">Exclut le dimanche légal de repos hebdomadaire</p>
</div>
</div>
<div class="text-right">
<span class="font-label-sm text-label-sm uppercase tracking-wider text-on-surface-variant font-semibold">Solde après approbation</span>
<p class="font-numeric-md text-numeric-md font-bold text-tertiary" id="balanceAfterDisplay">{{ $initialBalance !== null ? $initialBalance.' jours restants' : 'Solde non renseigné' }}</p>
</div>
</div>
<!-- Field 4: Motif / Commentaire -->
<div class="flex flex-col gap-space-xs">
<label class="font-label-md text-label-md text-on-surface font-medium" for="leaveReason">Motif / Commentaire de la demande</label>
<textarea class="w-full min-h-[96px] px-3 py-2 rounded-xl bg-surface-container-lowest text-on-surface font-body-md text-body-md placeholder:text-on-surface-variant focus:outline-none ring-1 ring-outline-variant focus:ring-2 focus:ring-primary transition-all resize-y" id="leaveReason" name="reason" placeholder="Précisez la raison ou les dispositions de passation..." required rows="2" maxlength="1000">{{ old('reason') }}</textarea>
</div>
<!-- Field 5: Pièce jointe Upload Zone -->
<div class="flex flex-col gap-space-xs">
<div class="flex items-center justify-between gap-space-sm">
<label class="font-label-md text-label-md text-on-surface font-medium" for="leaveAttachment">Pièce justificative (Optionnelle ou Obligatoire pour maladie)</label>
<span class="font-label-sm text-label-sm text-on-surface-variant shrink-0">PDF, PNG, JPG jusqu'à 10MB</span>
</div>
<div class="relative bg-surface-container-low rounded-xl ring-1 ring-outline-variant p-space-lg flex flex-col items-center justify-center text-center hover:bg-surface-container transition-colors cursor-pointer group">
<input accept=".pdf,.png,.jpg,.jpeg,application/pdf,image/png,image/jpeg" class="absolute inset-0 opacity-0 cursor-pointer w-full h-full" id="leaveAttachment" name="attachment" type="file"/>
<div class="w-10 h-10 rounded-full bg-surface-container-highest group-hover:scale-110 flex items-center justify-center text-primary mb-2 transition-transform">
<span class="material-symbols-outlined text-headline-sm">upload_file</span>
</div>
<p class="font-label-md text-label-md text-on-surface font-medium">Glissez un document ou <span class="text-primary font-bold underline">parcourez votre appareil</span></p>
<p class="font-label-sm text-label-sm text-on-surface-variant mt-0.5">Certificat médical, acte de mariage, convocation officielle</p>
<p class="hidden font-label-sm text-label-sm text-primary font-semibold mt-1" id="attachmentName"></p>
</div>
</div>
</form>
<!-- Drawer Sticky Footer Actions -->
<div class="p-space-lg bg-surface-container-lowest shadow-[0_-4px_12px_rgba(0,0,0,0.03)] flex items-center justify-between gap-space-sm">
<button class="h-10 px-space-lg rounded-xl bg-surface-container-low hover:bg-surface-container text-on-surface font-label-lg text-label-lg transition-colors" id="cancelModalBtn" type="button">
          Annuler
        </button>
<button class="h-10 px-space-xl rounded-xl bg-primary-fill hover:bg-primary-container text-on-primary font-label-lg text-label-lg shadow-md flex items-center gap-space-xs transition-all transform active:scale-95" form="leaveForm" id="submitLeaveBtn" type="submit">
<span class="material-symbols-outlined text-body-md">send</span>
<span>Soumettre pour validation</span>
</button>
</div>
</div>
</div>

@include('partials.stop-leave-dialog')

<script>
  (function() {
    const leaveModal = document.getElementById('leaveModal');
    const openModalBtn = document.getElementById('openModalBtn');
    const closeModalBtn = document.getElementById('closeModalBtn');
    const cancelModalBtn = document.getElementById('cancelModalBtn');
    const modalBackdrop = document.getElementById('modalBackdrop');
    const leaveForm = document.getElementById('leaveForm');
    const submitLeaveBtn = document.getElementById('submitLeaveBtn');
    const employeeSelect = document.getElementById('leaveEmployeeSelect');
    const typeSelect = document.getElementById('leaveTypeSelect');
    const startInput = document.getElementById('startDate');
    const endInput = document.getElementById('endDate');
    const durationDisplay = document.getElementById('durationDisplay');
    const balanceAfterDisplay = document.getElementById('balanceAfterDisplay');
    const attachmentInput = document.getElementById('leaveAttachment');
    const attachmentName = document.getElementById('attachmentName');
    const filtersForm = document.getElementById('leave-filters');
    const searchInput = filtersForm ? filtersForm.querySelector('input[name="q"]') : null;

    function openModal() {
      if (!leaveModal) return;
      leaveModal.classList.remove('hidden');
      document.body.style.overflow = 'hidden';
      updateCalculation();
    }

    function closeModal() {
      if (!leaveModal) return;
      leaveModal.classList.add('hidden');
      document.body.style.overflow = '';
    }

    if (openModalBtn) openModalBtn.addEventListener('click', openModal);
    if (closeModalBtn) closeModalBtn.addEventListener('click', closeModal);
    if (cancelModalBtn) cancelModalBtn.addEventListener('click', closeModal);
    if (modalBackdrop) modalBackdrop.addEventListener('click', closeModal);

    function workingDays(startValue, endValue) {
      if (!startValue || !endValue) return null;
      const start = new Date(startValue + 'T00:00:00');
      const end = new Date(endValue + 'T00:00:00');

      if (Number.isNaN(start.getTime()) || Number.isNaN(end.getTime()) || end < start) {
        return null;
      }

      let days = 0;
      for (const day = new Date(start); day <= end; day.setDate(day.getDate() + 1)) {
        if (day.getDay() !== 0) days++;
      }

      return days;
    }

    function selectedBalance() {
      if (!employeeSelect || !employeeSelect.selectedOptions.length) return null;
      const raw = employeeSelect.selectedOptions[0].dataset.balance;
      return raw === undefined || raw === '' ? null : parseInt(raw, 10);
    }

    function formatDays(days) {
      return days + ' jour' + (days > 1 ? 's' : '') + ' ouvré' + (days > 1 ? 's' : '');
    }

    function updateCalculation() {
      const balance = selectedBalance();
      const annualOption = typeSelect ? typeSelect.querySelector('option[value="annuel"]') : null;

      if (annualOption) {
        annualOption.textContent = 'Congé Payé Annuel' + (balance !== null ? ' (Solde actuel disponible : ' + balance + ' jours)' : '');
      }

      if (!startInput || !endInput || !durationDisplay || !balanceAfterDisplay) return;

      const days = workingDays(startInput.value, endInput.value);

      if (days === null) {
        durationDisplay.textContent = 'Dates invalides';
        balanceAfterDisplay.textContent = '—';
        return;
      }

      durationDisplay.textContent = formatDays(days);

      if (!typeSelect || typeSelect.value !== 'annuel') {
        balanceAfterDisplay.textContent = 'Sans impact';
        return;
      }

      balanceAfterDisplay.textContent = balance === null
        ? 'Solde non renseigné'
        : Math.max(0, balance - days) + ' jours restants';
    }

    [employeeSelect, typeSelect, startInput, endInput].forEach(function(element) {
      if (element) element.addEventListener('change', updateCalculation);
    });

    if (startInput) startInput.addEventListener('input', updateCalculation);
    if (endInput) endInput.addEventListener('input', updateCalculation);

    if (attachmentInput && attachmentName) {
      attachmentInput.addEventListener('change', function() {
        const file = attachmentInput.files && attachmentInput.files[0];
        attachmentName.textContent = file ? file.name : '';
        attachmentName.classList.toggle('hidden', !file);
      });
    }

    if (leaveForm && submitLeaveBtn) {
      leaveForm.addEventListener('submit', function() {
        submitLeaveBtn.disabled = true;
        submitLeaveBtn.classList.add('opacity-70', 'pointer-events-none');
        submitLeaveBtn.innerHTML = '<span class="material-symbols-outlined animate-spin text-body-md">progress_activity</span><span>Traitement...</span>';
      });
    }

    if (filtersForm && searchInput) {
      let searchTimer;
      searchInput.addEventListener('input', function() {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(function() { filtersForm.requestSubmit(); }, 500);
      });
    }

    const viewModal = document.getElementById('view-leave-modal');

    function closeViewDrawer() {
      if (!viewModal) return;
      viewModal.classList.add('hidden');
      document.body.style.overflow = '';
      const url = new URL(window.location.href);
      url.searchParams.delete('view');
      window.history.replaceState({}, '', url);
    }

    if (viewModal) {
      const closeViewBtn = document.getElementById('close-view-leave-btn');
      const cancelViewBtn = document.getElementById('cancel-view-leave-btn');
      const viewBackdrop = document.getElementById('view-leave-backdrop');
      if (closeViewBtn) closeViewBtn.addEventListener('click', closeViewDrawer);
      if (cancelViewBtn) cancelViewBtn.addEventListener('click', closeViewDrawer);
      if (viewBackdrop) viewBackdrop.addEventListener('click', closeViewDrawer);
    }

    document.addEventListener('keydown', function(event) {
      if (event.key !== 'Escape') return;
      if (leaveModal && !leaveModal.classList.contains('hidden')) closeModal();
      if (viewModal && !viewModal.classList.contains('hidden')) closeViewDrawer();
    });

    if (leaveModal && !leaveModal.classList.contains('hidden')) {
      document.body.style.overflow = 'hidden';
    }

    if (viewModal) {
      document.body.style.overflow = 'hidden';
    }

    updateCalculation();

    if (leaveModal && leaveModal.dataset.quickCreate === '1') {
      openModal();
    }

    document.querySelectorAll('[data-flash-toast]').forEach(function(toast) {
      setTimeout(function() {
        toast.classList.add('opacity-0', 'translate-y-2');
        toast.style.transition = 'opacity 300ms ease, transform 300ms ease';
        setTimeout(function() { toast.remove(); }, 320);
      }, 4500);
    });
  })();
</script>
</x-admin-shell>
