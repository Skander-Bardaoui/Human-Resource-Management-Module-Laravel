<x-admin-shell active="attendance" title="Suivi du Pointage & Présence">
@php
    $filterQuery = array_filter([
        'employee' => $employeeId,
        'q' => $filters['q'],
        'department' => $filters['department'],
        'status' => $filters['status'],
    ], fn ($value) => $value !== null && $value !== '');
    $exportQuery = array_merge($filterQuery, ['date' => $selectedDate->toDateString()]);
    $dayQuery = array_merge(array_diff_key($filterQuery, ['employee' => true]), ['date' => $selectedDate->toDateString()]);
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
<!-- Top Navigation & Breadcrumbs -->
<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 py-5">
<div class="flex flex-col gap-1">
<nav class="flex items-center gap-2 font-label-sm text-label-sm text-on-surface-variant">
<span class="hover:text-primary cursor-pointer transition-colors">Accueil</span>
<span class="material-symbols-outlined text-[14px]">chevron_right</span>
<span class="text-on-surface font-semibold">Présence &amp; Pointage</span>
</nav>
<div class="flex items-baseline gap-3 mt-1">
<h1 class="font-headline-lg text-headline-lg text-on-surface tracking-tight">Suivi du Pointage &amp; Présence</h1>
<span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-tertiary-fixed text-on-tertiary-fixed font-label-sm text-label-sm">
<span class="w-1.5 h-1.5 rounded-full bg-tertiary animate-pulse"></span>
          Direct Biométrique
        </span>
</div>
<p class="font-body-sm text-body-sm text-on-surface-variant">
        Badges &amp; empreintes synchronisés en temps réel • Conforme au Code du Travail Tunisien (Loi n° 66-27 - 48h/40h hebdo)
      </p>
</div>
<!-- Main Header Action CTAs -->
<div class="flex items-center gap-2.5">
<a class="h-9 px-3.5 rounded-xl bg-surface-container-low hover:bg-surface-container text-on-surface font-label-lg text-label-lg flex items-center gap-2 shadow-sm transition-all" href="{{ route('admin.attendance.export', $exportQuery) }}">
<span class="material-symbols-outlined text-body-lg text-secondary">file_download</span>
<span>Exporter Rapport</span>
</a>
<button class="h-9 px-4 rounded-xl bg-primary-container hover:bg-primary-fill text-on-primary font-label-lg text-label-lg flex items-center gap-2 shadow-sm transition-all active:scale-[0.98]" id="openModalBtn" type="button">
<span class="material-symbols-outlined text-body-md">add_circle</span>
<span>+ Pointage Manuel</span>
</button>
</div>
</div>
<!-- KPI Metric Overview Bento Grid -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
<!-- KPI 1: Présents -->
<div class="bg-surface-container-lowest p-5 rounded-xl shadow-sm flex flex-col justify-between relative overflow-hidden group hover:shadow-md transition-shadow">
<div class="flex items-center justify-between">
<span class="font-label-md text-label-md text-on-surface-variant">Effectif Présent {{ $selectedDate->isToday() ? "Aujourd'hui" : $selectedDate->format('d/m') }}</span>
<div class="w-8 h-8 rounded-lg bg-surface-container-low flex items-center justify-center text-tertiary">
<span class="material-symbols-outlined text-body-lg" style="font-variation-settings: 'FILL' 1;">badge</span>
</div>
</div>
<div class="my-3 flex items-baseline gap-2">
<span class="font-display-lg text-display-lg text-on-surface tracking-tight">{{ $presentCount }}</span>
<span class="font-headline-sm text-headline-sm text-on-surface-variant">/ {{ $activeEmployees }}</span>
</div>
<div class="flex items-center justify-between font-label-sm text-label-sm">
<span class="inline-flex items-center gap-1 text-tertiary font-semibold">
<span class="material-symbols-outlined text-[15px]">trending_up</span>
          {{ $presenceRate }}% Taux présence
        </span>
<span class="text-on-surface-variant">{{ $syncedModes }} modes synchro</span>
</div>
<div class="absolute bottom-0 left-0 right-0 h-1 bg-tertiary-fixed"></div>
</div>
<!-- KPI 2: Retards -->
<div class="bg-surface-container-lowest p-5 rounded-xl shadow-sm flex flex-col justify-between relative overflow-hidden group hover:shadow-md transition-shadow">
<div class="flex items-center justify-between">
<span class="font-label-md text-label-md text-on-surface-variant">Retards Constatés</span>
<div class="w-8 h-8 rounded-lg bg-secondary-container flex items-center justify-center text-secondary">
<span class="material-symbols-outlined text-body-lg">schedule</span>
</div>
</div>
<div class="my-3 flex items-baseline gap-2">
<span class="font-display-lg text-display-lg text-on-surface tracking-tight">{{ $lateCount }}</span>
<span class="font-body-sm text-body-sm text-on-surface-variant">collaborateurs</span>
</div>
<div class="flex items-center justify-between font-label-sm text-label-sm">
<span class="text-on-secondary-container font-medium">Tolérance &gt; 15 min</span>
<span class="text-on-surface-variant">Moy. +{{ $averageLate }} min</span>
</div>
<div class="absolute bottom-0 left-0 right-0 h-1 bg-secondary-fixed"></div>
</div>
<!-- KPI 3: Absences Non Justifiées -->
<div class="bg-surface-container-lowest p-5 rounded-xl shadow-sm flex flex-col justify-between relative overflow-hidden group hover:shadow-md transition-shadow">
<div class="flex items-center justify-between">
<span class="font-label-md text-label-md text-on-surface-variant">Absences Injustifiées</span>
<div class="w-8 h-8 rounded-lg bg-error-container flex items-center justify-center text-error">
<span class="material-symbols-outlined text-body-lg">warning</span>
</div>
</div>
<div class="my-3 flex items-baseline gap-2">
<span class="font-display-lg text-display-lg text-error tracking-tight">{{ $absenceCount }}</span>
<span class="font-body-sm text-body-sm text-on-surface-variant">sans préavis</span>
</div>
<div class="flex items-center justify-between font-label-sm text-label-sm">
<span class="inline-flex items-center gap-1 text-error font-medium">
<span class="material-symbols-outlined text-[14px]">notification_important</span>
          {{ $absenceCount }} Rappels envoyés
        </span>
<span class="text-on-surface-variant">Délai 48h</span>
</div>
<div class="absolute bottom-0 left-0 right-0 h-1 bg-error"></div>
</div>
<!-- KPI 4: Heures Supplémentaires -->
<div class="bg-surface-container-lowest p-5 rounded-xl shadow-sm flex flex-col justify-between relative overflow-hidden group hover:shadow-md transition-shadow">
<div class="flex items-center justify-between">
<span class="font-label-md text-label-md text-on-surface-variant">Heures Supp. (Mois en cours)</span>
<div class="w-8 h-8 rounded-lg bg-primary-fixed flex items-center justify-center text-primary">
<span class="material-symbols-outlined text-body-lg">more_time</span>
</div>
</div>
<div class="my-3 flex items-baseline gap-2">
<span class="font-display-lg text-display-lg text-primary tracking-tight">{{ number_format($overtimeHours, 1, ',', ' ') }}</span>
<span class="font-headline-sm text-headline-sm text-on-surface-variant">heures</span>
</div>
<div class="flex items-center justify-between font-label-sm text-label-sm">
<span class="text-primary font-semibold">Taux légal: 125% / 150%</span>
<span class="text-on-surface-variant">{{ $overtimeAgents }} agents</span>
</div>
<div class="absolute bottom-0 left-0 right-0 h-1 bg-primary"></div>
</div>
</div>
<!-- Operational Control Bar: Datepicker, Presets, Filters & Search -->
<div class="bg-surface-container-lowest rounded-xl p-4 shadow-sm mb-6 flex flex-col lg:flex-row lg:items-center justify-between gap-4">
<!-- Date Navigation Segment -->
<div class="flex items-center flex-wrap gap-2">
<div class="inline-flex items-center bg-surface-container-low p-1 rounded-xl">
<a aria-label="Jour précédent" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-surface-container text-on-surface-variant hover:text-on-surface transition-colors" href="{{ route('admin.attendance', array_merge($filterQuery, ['date' => $previousDate->toDateString()])) }}">
<span class="material-symbols-outlined text-body-md">chevron_left</span>
</a>
<div class="flex items-center gap-2 px-3 py-1">
<span class="material-symbols-outlined text-primary text-body-md">calendar_today</span>
<span class="font-label-lg text-label-lg text-on-surface font-semibold">{{ $dateLabel }}</span>
</div>
<a aria-label="Jour suivant" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-surface-container text-on-surface-variant hover:text-on-surface transition-colors" href="{{ route('admin.attendance', array_merge($filterQuery, ['date' => $nextDate->toDateString()])) }}">
<span class="material-symbols-outlined text-body-md">chevron_right</span>
</a>
</div>
<a class="h-8 px-3 rounded-lg bg-surface-container text-on-surface font-label-sm text-label-sm hover:bg-surface-container-high transition-colors flex items-center" href="{{ route('admin.attendance', array_merge($filterQuery, ['date' => today()->toDateString()])) }}">
        Aujourd'hui
      </a>
<span class="text-on-surface-variant font-label-sm text-label-sm ml-2 hidden xl:inline">Cycle normal : 08:30 - 17:30</span>
</div>
<!-- Filters & Search Query -->
<form class="flex items-center flex-wrap gap-3" id="attendanceFilterForm" method="GET" action="{{ route('admin.attendance') }}">
<input type="hidden" name="date" value="{{ $selectedDate->toDateString() }}"/>
@if ($employeeId)
<input type="hidden" name="employee" value="{{ $employeeId }}"/>
@endif
<!-- Search Input -->
<div class="relative min-w-[220px]">
<span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-[18px]">search</span>
<input class="w-full h-9 pl-9 pr-3 rounded-xl bg-surface-container-low font-body-sm text-body-sm text-on-surface placeholder:text-on-surface-variant focus:outline-none focus:bg-surface-container" id="searchInput" name="q" value="{{ $filters['q'] }}" placeholder="Filtrer collaborateur, matricule..." type="text"/>
</div>
<!-- Department Filter -->
<div class="relative">
<select class="h-9 pl-3 pr-8 rounded-xl bg-surface-container-low font-label-md text-label-md text-on-surface appearance-none focus:outline-none focus:bg-surface-container cursor-pointer" name="department" onchange="this.form.requestSubmit()">
<option value="">Tous Départements</option>
@foreach (\App\Models\Employee::DEPARTMENTS as $key => $label)
<option value="{{ $key }}" @selected($filters['department'] === $key)>{{ $label }}</option>
@endforeach
</select>
<span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-on-surface-variant text-[18px] pointer-events-none">arrow_drop_down</span>
</div>
<!-- Attendance Status Filter -->
<div class="relative">
<select class="h-9 pl-3 pr-8 rounded-xl bg-surface-container-low font-label-md text-label-md text-on-surface appearance-none focus:outline-none focus:bg-surface-container cursor-pointer" id="statusFilter" name="status" onchange="this.form.requestSubmit()">
<option value="">Tous les Statuts</option>
<option value="ontime" @selected($filters['status'] === 'ontime')>À l'heure</option>
<option value="late" @selected($filters['status'] === 'late')>En Retard</option>
<option value="remote" @selected($filters['status'] === 'remote')>Télétravail</option>
<option value="absent" @selected($filters['status'] === 'absent')>Absent Non Justifié</option>
<option value="leave" @selected($filters['status'] === 'leave')>En Congé Légal</option>
</select>
<span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-on-surface-variant text-[18px] pointer-events-none">arrow_drop_down</span>
</div>
<button class="sr-only" type="submit">Filtrer</button>
</form>
</div>
<!-- Main Roster Attendance Data Table -->
<div class="bg-surface-container-lowest rounded-xl shadow-sm overflow-hidden mb-8">
<div class="p-4 bg-surface-container-low/50 flex items-center justify-between">
<div class="flex items-center gap-3">
<span class="font-headline-sm text-headline-sm text-on-surface">{{ $historyEmployee ? 'Historique des Pointages' : 'Journal des Pointages Journaliers' }}</span>
<span class="px-2 py-0.5 rounded-full bg-surface-container text-on-surface-variant font-label-sm text-label-sm">
          @if ($historyEmployee)
            {{ $records->total() }} pointages • {{ $historyEmployee->name }}
          @else
            {{ $activeEmployees }} collaborateurs actifs
          @endif
        </span>
@if ($historyEmployee)
<a class="px-2 py-0.5 rounded-full bg-primary-fixed text-on-primary-fixed font-label-sm text-label-sm hover:bg-primary-container transition-colors" href="{{ route('admin.attendance', $dayQuery) }}">Revenir au jour</a>
@endif
</div>
<div class="flex items-center gap-2">
<span class="font-label-sm text-label-sm text-on-surface-variant flex items-center gap-1.5">
<span class="w-2 h-2 rounded-full {{ $syncedModes > 0 ? 'bg-tertiary' : 'bg-error' }}"></span> {{ $syncedModes > 0 ? 'Terminal Lac 2 En Ligne' : 'Terminal hors ligne' }}
        </span>
</div>
</div>
<div class="overflow-x-auto">
<table class="w-full text-left border-collapse" id="attendanceTable">
<thead>
<tr class="bg-surface-container-low font-label-md text-label-md text-on-surface-variant uppercase tracking-wider h-11">
<th class="px-4 py-2 font-medium">Collaborateur</th>
<th class="px-3 py-2 font-medium">Département</th>
<th class="px-3 py-2 font-medium">Arrivée</th>
<th class="px-3 py-2 font-medium">Départ</th>
<th class="px-3 py-2 font-medium">Pause Midi</th>
<th class="px-3 py-2 font-medium">Total Effectué</th>
<th class="px-3 py-2 font-medium">Mode de Pointage</th>
<th class="px-3 py-2 font-medium">Statut Journée</th>
<th class="px-4 py-2 font-medium text-right">Actions</th>
</tr>
</thead>
<tbody class="divide-y divide-surface-container font-body-md text-body-md text-on-surface">
@forelse ($records as $record)
@php
    $employee = $record->employee;
    $isLate = $record->status === 'late';
@endphp
<tr class="hover:bg-surface-container-low/60 transition-colors h-14 {{ $record->rowClasses() }}">
<td class="px-4 py-2.5">
<div class="flex items-center gap-3">
@if ($employee?->avatar_url)
<img class="w-9 h-9 rounded-full object-cover shrink-0" data-alt="Portrait professionnel de {{ $employee->name }}" src="{{ $employee->avatar_url }}"/>
@else
<div class="w-9 h-9 rounded-full bg-surface-container flex items-center justify-center font-label-md text-label-md text-on-surface-variant font-bold">
                  {{ $employee?->initials() ?? '?' }}
                </div>
@endif
<div class="flex flex-col min-w-0">
<span class="font-label-lg text-label-lg text-on-surface font-semibold truncate">{{ $employee?->name ?? 'Collaborateur supprimé' }}</span>
<span class="font-label-sm text-label-sm text-on-surface-variant truncate">MAT-{{ str_pad((string) $record->employee_id, 4, '0', STR_PAD_LEFT) }} • {{ $employee?->position ?? '—' }}</span>
</div>
</div>
</td>
<td class="px-3 py-2.5">
<span class="font-label-md text-label-md text-on-surface bg-surface-container px-2 py-0.5 rounded">{{ $employee?->departmentShortLabel() ?? '—' }}</span>
</td>
<td class="px-3 py-2.5">
@if ($record->check_in)
<div class="flex items-center gap-1.5">
<span class="font-numeric-md text-numeric-md font-semibold {{ $isLate ? 'text-error' : 'text-on-surface' }}">{{ $record->checkInShort() }}</span>
@if ($isLate)
<span class="inline-flex items-center text-[11px] text-error bg-error-container px-1.5 py-0.2 rounded font-medium">+{{ $record->lateMinutes() }} min</span>
@elseif ($record->status === 'remote')
<span class="inline-flex items-center text-[11px] text-primary bg-primary-fixed px-1.5 py-0.2 rounded font-medium">À distance</span>
@else
<span class="inline-flex items-center text-[11px] text-tertiary bg-tertiary-fixed/40 px-1.5 py-0.2 rounded font-medium">À l'heure</span>
@endif
</div>
@else
<span class="font-label-sm text-label-sm font-medium {{ $record->status === 'absent' ? 'text-error' : 'text-on-surface-variant' }}">
              {{ $record->status === 'absent' ? 'Non pointé' : ($record->status === 'leave' ? 'En congé' : '—') }}
            </span>
@endif
</td>
<td class="px-3 py-2.5">
@if ($record->check_out)
<span class="font-numeric-md text-numeric-md text-on-surface">{{ $record->checkOutShort() }}</span>
@elseif (in_array($record->status, ['absent', 'leave'], true))
<span class="font-label-sm text-label-sm text-on-surface-variant">—</span>
@else
<span class="font-numeric-md text-numeric-md text-on-surface-variant italic">En poste...</span>
@endif
</td>
<td class="px-3 py-2.5">
<span class="font-numeric-md text-numeric-md text-on-surface-variant">{{ $record->breakLabel() }}</span>
</td>
<td class="px-3 py-2.5">
<span class="font-numeric-md text-numeric-md font-semibold {{ $record->status === 'absent' ? 'text-error' : 'text-on-surface' }}">{{ $record->workedLabel() }}</span>
</td>
<td class="px-3 py-2.5">
@if ($record->check_in && $record->mode !== 'none')
<div class="flex items-center gap-1.5 font-label-sm text-label-sm text-on-surface-variant">
<span class="material-symbols-outlined text-[16px] {{ $record->modeColor() }}">{{ $record->modeIcon() }}</span>
<span>{{ $record->modeLabel() }}</span>
</div>
@else
<span class="font-label-sm text-label-sm text-on-surface-variant italic">Aucun enregistrement</span>
@endif
</td>
<td class="px-3 py-2.5">
<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full font-label-sm text-label-sm font-semibold {{ $record->statusPillClasses() }}">
<span class="w-1.5 h-1.5 rounded-full {{ $record->statusDotClasses() }}"></span> {{ $record->statusLabel() }}
              </span>
</td>
<td class="px-4 py-2.5 text-right">
<div class="flex items-center justify-end gap-1">
@if ($record->status === 'absent' && $employee)
<a class="h-7 px-2.5 rounded-lg bg-surface-container-lowest hover:bg-surface-container text-error font-label-sm text-label-sm transition-colors flex items-center gap-1" href="mailto:{{ $employee->email }}?subject={{ rawurlencode('Rappel de pointage — '.$record->work_date->format('d/m/Y')) }}">
<span class="material-symbols-outlined text-[14px]">send</span> Relancer
                </a>
@else
<button class="p-1.5 hover:bg-surface-container rounded-lg text-on-surface-variant hover:text-primary transition-colors js-edit-punch" data-date="{{ $record->work_date->toDateString() }}" data-direction="in" data-employee="{{ $record->employee_id }}" data-time="{{ $record->check_in ? substr($record->check_in, 0, 5) : '08:30' }}" title="Modifier le pointage" type="button">
<span class="material-symbols-outlined text-body-md">edit</span>
</button>
<a class="p-1.5 hover:bg-surface-container rounded-lg text-on-surface-variant hover:text-on-surface transition-colors" href="{{ route('admin.attendance', array_merge($filterQuery, ['date' => $selectedDate->toDateString(), 'employee' => $record->employee_id])) }}" title="Consulter l'historique">
<span class="material-symbols-outlined text-body-md">{{ $record->status === 'remote' ? 'info' : 'history' }}</span>
</a>
@endif
</div>
</td>
</tr>
@empty
<tr>
<td class="px-4 py-10 text-center" colspan="9">
<div class="flex flex-col items-center gap-2 text-on-surface-variant">
<span class="material-symbols-outlined text-headline-lg">event_busy</span>
<span class="font-body-md text-body-md">Aucun pointage ne correspond à cette date ou à ces filtres.</span>
<a class="font-label-md text-label-md text-primary hover:underline" href="{{ route('admin.attendance', $dayQuery) }}">Réinitialiser les filtres</a>
</div>
</td>
</tr>
@endforelse
</tbody>
</table>
</div>
<!-- Table Pagination Footer -->
<div class="p-4 bg-surface-container-low/40 flex flex-col sm:flex-row items-center justify-between gap-3">
<span class="font-label-sm text-label-sm text-on-surface-variant">
        Affichage de {{ $records->firstItem() ?? 0 }} à {{ $records->lastItem() ?? 0 }} sur {{ $records->total() }} pointages
      </span>
@if ($records->hasPages())
<div class="flex items-center gap-1.5">
@if ($records->onFirstPage())
<button class="px-3 py-1.5 rounded-lg bg-surface-container-lowest text-on-surface-variant opacity-50 cursor-not-allowed font-label-sm text-label-sm shadow-sm" disabled="" type="button">
          Précédent
        </button>
@else
<a class="px-3 py-1.5 rounded-lg bg-surface-container-lowest hover:bg-surface-container text-on-surface font-label-sm text-label-sm shadow-sm transition-colors" href="{{ $records->previousPageUrl() }}">
          Précédent
        </a>
@endif
@foreach ($records->getUrlRange(max(1, $records->currentPage() - 1), min($records->lastPage(), $records->currentPage() + 1)) as $page => $url)
<a class="px-3 py-1.5 rounded-lg font-label-sm text-label-sm shadow-sm transition-colors {{ $page === $records->currentPage() ? 'bg-primary-container text-on-primary' : 'bg-surface-container-lowest hover:bg-surface-container text-on-surface' }}" href="{{ $url }}">
          {{ $page }}
        </a>
@endforeach
@if ($records->hasMorePages())
<a class="px-3 py-1.5 rounded-lg bg-surface-container-lowest hover:bg-surface-container text-on-surface font-label-sm text-label-sm shadow-sm transition-colors" href="{{ $records->nextPageUrl() }}">
          Suivant
        </a>
@else
<button class="px-3 py-1.5 rounded-lg bg-surface-container-lowest text-on-surface-variant opacity-50 cursor-not-allowed font-label-sm text-label-sm shadow-sm" disabled="" type="button">
          Suivant
        </button>
@endif
</div>
@endif
</div>
</div>
<!-- Bottom Analytics & Compliance Section: Weekly Breakdown + Anomaly Stream -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
<!-- Chart: Taux de présence hebdomadaire -->
<div class="lg:col-span-2 bg-surface-container-lowest p-5 rounded-xl shadow-sm flex flex-col justify-between">
<div class="flex items-center justify-between mb-4">
<div>
<h2 class="font-headline-sm text-headline-sm text-on-surface">Rythme &amp; Assiduité Hebdomadaire</h2>
<p class="font-body-sm text-body-sm text-on-surface-variant">Moyenne globale des 5 derniers jours ouvrés (Lun - Ven)</p>
</div>
<div class="flex items-center gap-3">
<span class="flex items-center gap-1.5 font-label-sm text-label-sm text-on-surface-variant">
<span class="w-3 h-3 rounded bg-primary"></span> Présents
          </span>
<span class="flex items-center gap-1.5 font-label-sm text-label-sm text-on-surface-variant">
<span class="w-3 h-3 rounded bg-secondary-fixed-dim"></span> Retards
          </span>
</div>
</div>
<!-- Compact SVG Bar Visualization -->
<div class="h-44 w-full flex items-end gap-6 pt-4 pb-2 px-2">
@foreach ($weekDays as $day)
<div class="flex-1 flex flex-col items-center gap-2 h-full justify-end {{ $day['isFuture'] ? 'opacity-40' : '' }}">
<span class="font-label-sm text-label-sm {{ $day['isToday'] ? 'text-primary font-bold' : 'text-on-surface-variant' }}">{{ $day['rate'] !== null ? $day['rate'].'%' : '--' }}</span>
<div class="w-full max-w-[48px] rounded-t-lg h-full flex flex-col justify-end p-1 {{ $day['isToday'] ? 'bg-primary-fixed/20 ring-1 ring-primary/20' : 'bg-surface-container-low' }}">
@if ($day['rate'] !== null)
<div class="w-full bg-secondary-fixed rounded-t-sm" style="height: {{ $day['lateHeight'] }}%"></div>
<div class="w-full {{ $day['isToday'] ? 'bg-primary-container' : 'bg-primary' }} rounded-b-sm" style="height: {{ $day['primaryHeight'] }}%"></div>
@else
<div class="w-full bg-surface-container-high rounded-t-sm h-[70%]"></div>
@endif
</div>
<span class="font-label-sm text-label-sm {{ $day['isToday'] ? 'text-primary font-bold' : 'text-on-surface-variant' }}">{{ $day['label'] }}</span>
</div>
@endforeach
</div>
<div class="mt-4 pt-3 flex items-center justify-between bg-surface-container-low/40 rounded-lg p-2.5">
<div class="flex items-center gap-2">
<span class="material-symbols-outlined text-primary text-body-md">gavel</span>
<span class="font-label-sm text-label-sm text-on-surface font-medium">Règle de présence 48h</span>
</div>
<span class="font-label-sm text-label-sm text-on-surface-variant">Majoration auto des H.S. au-delà du 48ème volume hebd.</span>
</div>
</div>
<!-- Right Box: Anomalies & Instant Alerts Stream -->
<div class="bg-surface-container-lowest p-5 rounded-xl shadow-sm flex flex-col justify-between">
<div>
<div class="flex items-center justify-between mb-3">
<h2 class="font-headline-sm text-headline-sm text-on-surface flex items-center gap-2">
<span>Anomalies de Pointage</span>
<span class="px-2 py-0.5 rounded-full bg-error-container text-on-error-container font-label-sm text-label-sm font-bold">{{ count($anomalies) }}</span>
</h2>
@if (count($anomalies) > 0)
<form method="POST" action="{{ route('admin.attendance.anomalies.resolveAll') }}">
@csrf
<input type="hidden" name="date" value="{{ $selectedDate->toDateString() }}"/>
<button class="font-label-sm text-label-sm text-primary hover:underline" type="submit">Tout traiter</button>
</form>
@endif
</div>
<p class="font-body-sm text-body-sm text-on-surface-variant mb-4">Événements exigeant une régularisation RH rapide</p>
<!-- Anomaly List -->
<div class="space-y-3">
@forelse ($anomalies as $anomaly)
<div class="p-3 bg-surface-container-low rounded-xl flex items-start gap-3">
<div class="w-7 h-7 rounded-full {{ $anomaly['tone'] }} flex items-center justify-center shrink-0 mt-0.5">
<span class="material-symbols-outlined text-[16px]">{{ $anomaly['icon'] }}</span>
</div>
<div class="flex flex-col flex-1 min-w-0">
<span class="font-label-md text-label-md text-on-surface font-semibold truncate">{{ $anomaly['title'] }}</span>
<span class="font-body-sm text-body-sm text-on-surface-variant">{{ $anomaly['subtitle'] }}</span>
<div class="flex items-center gap-2 mt-2">
<form method="POST" action="{{ route('admin.attendance.anomalies.'.$anomaly['action'], $anomaly['record']) }}">
@csrf
<button class="h-6 px-2 rounded bg-surface-container hover:bg-surface-container-high text-on-surface font-label-sm text-label-sm" type="submit">{{ $anomaly['actionLabel'] }}</button>
</form>
@if ($anomaly['action'] !== 'ignore')
<form method="POST" action="{{ route('admin.attendance.anomalies.ignore', $anomaly['record']) }}">
@csrf
<button class="font-label-sm text-label-sm text-on-surface-variant hover:text-on-surface" type="submit">Ignorer</button>
</form>
@endif
</div>
</div>
</div>
@empty
<div class="p-4 bg-surface-container-low rounded-xl flex items-center gap-3 text-on-surface-variant">
<span class="material-symbols-outlined text-tertiary text-body-lg">task_alt</span>
<span class="font-label-md text-label-md">Aucune anomalie à traiter pour cette date.</span>
</div>
@endforelse
</div>
</div>
<div class="mt-4 pt-3 flex items-center justify-between text-on-surface-variant font-label-sm text-label-sm">
<span>Synchronisation passerelle : <b class="text-on-surface font-medium">{{ now()->format('H:i:s') }}</b></span>
<a class="flex items-center gap-1 text-primary hover:text-primary-container" href="{{ url()->full() }}">
<span class="material-symbols-outlined text-[14px]">refresh</span>
          Actualiser
        </a>
</div>
</div>
</div>
<!-- Manual Punch Slide-Over Drawer -->
<div class="fixed inset-0 z-50 overflow-hidden flex justify-end transition-opacity duration-300 hidden" id="manualPunchModal">
<div class="fixed inset-0 bg-black/60 backdrop-blur-xs transition-opacity" id="punchModalBackdrop"></div>
<div class="relative w-full max-w-2xl bg-surface-container-lowest shadow-2xl flex flex-col h-full z-10 overflow-hidden">
<!-- Drawer Header -->
<div class="p-space-xl bg-surface-container-low/60 flex items-start justify-between gap-space-md">
<div class="flex items-center gap-space-md">
<div class="w-12 h-12 rounded-xl bg-primary-container text-on-primary flex items-center justify-center shadow-sm shrink-0">
<span class="material-symbols-outlined text-headline-lg">schedule</span>
</div>
<div class="flex flex-col">
<h2 class="font-headline-md text-headline-md text-on-surface tracking-tight">Ajouter un Pointage Manuel</h2>
<p class="font-body-sm text-body-sm text-on-surface-variant">Régularisation exceptionnelle de présence avec visa RH</p>
</div>
</div>
<button aria-label="Fermer" class="w-9 h-9 rounded-xl hover:bg-surface-container text-on-surface-variant hover:text-on-surface flex items-center justify-center transition-colors shrink-0" id="closeModalBtn" type="button">
<span class="material-symbols-outlined text-headline-sm">close</span>
</button>
</div>
<!-- Drawer Form Content -->
<form class="flex-1 overflow-y-auto p-space-xl flex flex-col gap-space-md" id="punchForm" method="POST" action="{{ route('admin.attendance.punch') }}">
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
<div class="flex flex-col gap-space-xs">
<label class="font-label-md text-label-md text-on-surface font-medium" for="punchEmployee">Collaborateur concerné</label>
<div class="relative">
<select class="w-full appearance-none h-10 pl-3 pr-8 rounded-xl bg-surface-container-lowest text-on-surface font-body-md text-body-md focus:outline-none ring-1 ring-outline-variant focus:ring-2 focus:ring-primary transition-all cursor-pointer" id="punchEmployee" name="employee_id" required>
@foreach ($employees as $employee)
<option value="{{ $employee->id }}" @selected($employeeId === $employee->id)>{{ $employee->name }} (MAT-{{ str_pad((string) $employee->id, 4, '0', STR_PAD_LEFT) }})</option>
@endforeach
</select>
<span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-on-surface-variant pointer-events-none text-body-md">expand_more</span>
</div>
</div>
<div class="grid grid-cols-1 sm:grid-cols-2 gap-space-md">
<div class="flex flex-col gap-space-xs">
<label class="font-label-md text-label-md text-on-surface font-medium" for="punchDate">Date</label>
<input class="w-full h-10 px-3 rounded-xl bg-surface-container-lowest text-on-surface font-body-md text-body-md focus:outline-none ring-1 ring-outline-variant focus:ring-2 focus:ring-primary transition-all" id="punchDate" name="date" type="date" value="{{ old('date', $selectedDate->toDateString()) }}" required/>
</div>
<div class="flex flex-col gap-space-xs">
<label class="font-label-md text-label-md text-on-surface font-medium" for="punchTime">Heure de pointage</label>
<input class="w-full h-10 px-3 rounded-xl bg-surface-container-lowest text-on-surface font-body-md text-body-md focus:outline-none ring-1 ring-outline-variant focus:ring-2 focus:ring-primary transition-all" id="punchTime" name="time" type="time" value="{{ old('time', '08:30') }}" required/>
</div>
</div>
<div class="grid grid-cols-1 sm:grid-cols-2 gap-space-md">
<div class="flex flex-col gap-space-xs">
<label class="font-label-md text-label-md text-on-surface font-medium" for="punchDirection">Sens de flux</label>
<div class="relative">
<select class="w-full appearance-none h-10 pl-3 pr-8 rounded-xl bg-surface-container-lowest text-on-surface font-body-md text-body-md focus:outline-none ring-1 ring-outline-variant focus:ring-2 focus:ring-primary transition-all cursor-pointer" id="punchDirection" name="direction">
<option value="in" @selected(old('direction') === 'in')>Entrée / Arrivée</option>
<option value="out" @selected(old('direction') === 'out')>Sortie / Départ</option>
<option value="pause_in" @selected(old('direction') === 'pause_in')>Sortie Pause Déjeuner</option>
<option value="pause_out" @selected(old('direction') === 'pause_out')>Retour Pause Déjeuner</option>
</select>
<span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-on-surface-variant pointer-events-none text-body-md">expand_more</span>
</div>
</div>
<div class="flex flex-col gap-space-xs">
<label class="font-label-md text-label-md text-on-surface font-medium" for="punchMotif">Motif de dérogation</label>
<div class="relative">
<select class="w-full appearance-none h-10 pl-3 pr-8 rounded-xl bg-surface-container-lowest text-on-surface font-body-md text-body-md focus:outline-none ring-1 ring-outline-variant focus:ring-2 focus:ring-primary transition-all cursor-pointer" id="punchMotif" name="motif">
@foreach ($motifs as $value => $label)
<option value="{{ $value }}" @selected(old('motif') === $value)>{{ $label }}</option>
@endforeach
</select>
<span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-on-surface-variant pointer-events-none text-body-md">expand_more</span>
</div>
</div>
</div>
<div class="flex flex-col gap-space-xs">
<label class="font-label-md text-label-md text-on-surface font-medium" for="punchComment">Commentaire de validation</label>
<textarea class="w-full min-h-[96px] px-3 py-2 rounded-xl bg-surface-container-lowest text-on-surface font-body-md text-body-md placeholder:text-on-surface-variant focus:outline-none ring-1 ring-outline-variant focus:ring-2 focus:ring-primary transition-all resize-y" id="punchComment" name="comment" placeholder="Ex: Ordre de mission validé par la Direction Générale..." rows="2">{{ old('comment') }}</textarea>
</div>
<div class="p-space-md rounded-xl bg-surface-container-low flex items-start gap-space-sm">
<span class="material-symbols-outlined text-primary text-body-lg shrink-0 mt-0.5">verified</span>
<p class="font-body-sm text-body-sm text-on-surface-variant">
            Ce pointage sera tracé au registre d'audit avec la signature électronique de l'utilisateur connecté ({{ optional(auth()->user())->name ?? 'Administrateur RH' }}).
          </p>
</div>
</form>
<!-- Drawer Sticky Footer Actions -->
<div class="p-space-lg bg-surface-container-lowest shadow-[0_-4px_12px_rgba(0,0,0,0.03)] flex items-center justify-between gap-space-sm">
<button class="h-10 px-space-lg rounded-xl bg-surface-container-low hover:bg-surface-container text-on-surface font-label-lg text-label-lg transition-colors" id="cancelModalBtn" type="button">
          Annuler
        </button>
<button class="h-10 px-space-xl rounded-xl bg-primary-fill hover:bg-primary-container text-on-primary font-label-lg text-label-lg shadow-md flex items-center gap-space-xs transition-all transform active:scale-95" type="submit" form="punchForm">
<span class="material-symbols-outlined text-body-md">check</span>
<span>Enregistrer le pointage</span>
</button>
</div>
</div>
</div>
<script>
    // Client-side interactions for Modal, filters and prefill
    (function() {
      const modal = document.getElementById('manualPunchModal');
      const backdrop = document.getElementById('punchModalBackdrop');
      const openBtn = document.getElementById('openModalBtn');
      const closeBtn = document.getElementById('closeModalBtn');
      const cancelBtn = document.getElementById('cancelModalBtn');

      function showModal() {
        if (!modal) return;
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
      }

      function hideModal() {
        if (!modal) return;
        modal.classList.add('hidden');
        document.body.style.overflow = '';
      }

      if (openBtn) openBtn.addEventListener('click', showModal);
      if (closeBtn) closeBtn.addEventListener('click', hideModal);
      if (cancelBtn) cancelBtn.addEventListener('click', hideModal);
      if (backdrop) backdrop.addEventListener('click', hideModal);

      document.querySelectorAll('.js-edit-punch').forEach(function(button) {
        button.addEventListener('click', function() {
          const employeeSelect = document.getElementById('punchEmployee');
          const dateInput = document.getElementById('punchDate');
          const timeInput = document.getElementById('punchTime');
          const directionSelect = document.getElementById('punchDirection');

          if (employeeSelect && button.dataset.employee) employeeSelect.value = button.dataset.employee;
          if (dateInput && button.dataset.date) dateInput.value = button.dataset.date;
          if (timeInput && button.dataset.time) timeInput.value = button.dataset.time;
          if (directionSelect) directionSelect.value = button.dataset.direction || 'in';

          showModal();
        });
      });

      @if ($errors->any())
      showModal();
      @endif

      const filterForm = document.getElementById('attendanceFilterForm');
      const searchInput = document.getElementById('searchInput');
      let searchTimer;

      if (filterForm && searchInput) {
        searchInput.addEventListener('input', function() {
          clearTimeout(searchTimer);
          searchTimer = setTimeout(function() { filterForm.requestSubmit(); }, 500);
        });
        searchInput.addEventListener('keydown', function(event) {
          if (event.key === 'Enter') {
            event.preventDefault();
            clearTimeout(searchTimer);
            filterForm.requestSubmit();
          }
        });
      }

      document.querySelectorAll('[data-flash-toast]').forEach(function(toast) {
        setTimeout(function() { toast.remove(); }, 6000);
      });
    })();
  </script>
</div>
</x-admin-shell>
