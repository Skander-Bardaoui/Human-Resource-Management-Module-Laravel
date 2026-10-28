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
    $todayQuery = array_merge(array_diff_key($filterQuery, ['employee' => true]), ['date' => $today->toDateString()]);
    $resetQuery = ['date' => $selectedDate->toDateString()];
    $isHistory = $historyEmployee !== null;
    $isToday = $selectedDate->toDateString() === $today->toDateString();
    $hasFilters = $filters['q'] !== '' || $filters['department'] !== '' || $filters['status'] !== '' || $employeeId !== null;
    $presentLabel = 'Effectif présent '.($isToday ? "aujourd'hui" : 'le '.$selectedDate->format('d/m'));
    $presenceHint = $activeEmployees > 0
        ? $presenceRate.' % de l\'effectif actif'
        : 'Aucun collaborateur actif';
    $lateHint = 'Tolérance +'.$rules['lateTolerance'].' min'.($lateCount > 0 ? ' • moyenne +'.$averageLate.' min' : '');
    $absenceHint = $absenceCount > 0 ? 'Relance manuelle requise' : 'Aucune absence injustifiée';
    $thresholdHours = $rules['overtimeThreshold'] / 60;
    $overtimeThresholdLabel = number_format($thresholdHours, $thresholdHours == (int) $thresholdHours ? 0 : 1, ',', ' ');
    $overtimeHint = 'Seuil '.$overtimeThresholdLabel.' h/jour'.($overtimeAgents > 0 ? ' • '.$overtimeAgents.' agent(s)' : '');
    $overtimeValue = number_format((float) $overtimeHours, 1, ',', ' ').' h';
    $emptyTitle = $isHistory ? 'Aucun pointage sur ce mois' : 'Aucun pointage pour cette date';
    $emptyDescription = $isHistory
        ? 'Aucun enregistrement pour '.$historyEmployee->name.' en '.$overtimeLabel.'.'
        : 'Aucun enregistrement ne correspond à la date ou aux filtres sélectionnés.';
@endphp

<x-ui.toast />

<div class="flex w-full flex-col gap-6 pb-16">
    <header class="flex flex-col gap-4 pt-5 md:flex-row md:items-center md:justify-between">
        <div class="flex flex-col gap-1">
            <nav class="flex items-center gap-2 font-label-sm text-label-sm text-on-surface-variant" aria-label="Fil d'Ariane">
                <span class="transition-colors hover:text-primary">Accueil</span>
                <span class="material-symbols-outlined text-[14px]" aria-hidden="true">chevron_right</span>
                <span class="font-semibold text-on-surface">Présence &amp; Pointage</span>
            </nav>
            <div class="mt-1 flex flex-wrap items-center gap-3">
                <h1 class="font-headline-lg text-headline-lg tracking-tight text-on-surface">Suivi du Pointage &amp; Présence</h1>
                <span class="inline-flex items-center gap-1.5 rounded-full bg-surface-container px-2.5 py-0.5 font-label-sm text-label-sm text-on-surface-variant">
                    <span class="h-1.5 w-1.5 rounded-full bg-primary" aria-hidden="true"></span>
                    Registre RH
                </span>
            </div>
            <p class="font-body-sm text-body-sm text-on-surface-variant">
                Pointages centralisés, saisies manuelles et anomalies • Horaire de référence : {{ $rules['scheduleStart'] }} – {{ $rules['scheduleEnd'] }}
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <a class="flex h-9 items-center gap-2 rounded-xl bg-surface-container-low px-3.5 font-label-lg text-label-lg text-on-surface shadow-sm transition-all hover:bg-surface-container" href="{{ route('admin.attendance.export', $exportQuery) }}">
                <span class="material-symbols-outlined text-body-lg text-secondary" aria-hidden="true">file_download</span>
                <span>Exporter</span>
            </a>
            <button
                class="flex h-9 items-center gap-2 rounded-xl bg-primary-container px-4 font-label-lg text-label-lg text-on-primary shadow-sm transition-all hover:bg-primary active:scale-[0.98] disabled:cursor-not-allowed disabled:opacity-50"
                type="button"
                data-open-punch
                aria-haspopup="dialog"
                @disabled($employees->isEmpty())
            >
                <span class="material-symbols-outlined text-body-md" aria-hidden="true">add_circle</span>
                <span>Pointage manuel</span>
            </button>
        </div>
    </header>

    @if ($errors->default->any())
        <div class="flex items-start gap-3 rounded-xl border border-error/30 bg-error-container/50 p-4 text-on-error-container" role="alert">
            <span class="material-symbols-outlined text-body-lg shrink-0" aria-hidden="true">error</span>
            <div>
                <p class="font-label-md text-label-md font-semibold">Action impossible</p>
                <p class="font-body-sm text-body-sm">{{ $errors->default->first() }}</p>
            </div>
        </div>
    @endif

    <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4" aria-label="Indicateurs du jour">
        <x-ui.kpi-card
            :label="$presentLabel"
            :value="$presentCount.' / '.$activeEmployees"
            icon="badge"
            tone="tertiary"
            :hint="$presenceHint"
        />
        <x-ui.kpi-card
            label="Retards constatés"
            :value="(string) $lateCount"
            icon="schedule"
            tone="secondary"
            :hint="$lateHint"
        />
        <x-ui.kpi-card
            label="Absences injustifiées"
            :value="(string) $absenceCount"
            icon="warning"
            tone="error"
            :hint="$absenceHint"
        />
        <x-ui.kpi-card
            :label="'Heures supp. — '.$overtimeLabel"
            :value="$overtimeValue"
            icon="more_time"
            tone="primary"
            :hint="$overtimeHint"
        />
    </section>

    <section class="rounded-xl bg-surface-container-lowest p-4 shadow-sm" aria-label="Navigation par date et filtres">
        <div class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">
            <div class="flex flex-wrap items-center gap-2">
                <div class="inline-flex items-center rounded-xl bg-surface-container-low p-1">
                    <a
                        class="flex h-8 w-8 items-center justify-center rounded-lg text-on-surface-variant transition-colors hover:bg-surface-container hover:text-on-surface focus:outline-none focus:ring-2 focus:ring-primary"
                        href="{{ route('admin.attendance', array_merge($filterQuery, ['date' => $previousDate->toDateString()])) }}"
                        aria-label="Jour précédent"
                    >
                        <span class="material-symbols-outlined text-body-md" aria-hidden="true">chevron_left</span>
                    </a>
                    <div class="flex items-center gap-2 px-3 py-1">
                        <span class="material-symbols-outlined text-body-md text-primary" aria-hidden="true">calendar_today</span>
                        <span class="font-label-lg text-label-lg font-semibold text-on-surface">{{ $dateLabel }}</span>
                    </div>
                    <a
                        class="flex h-8 w-8 items-center justify-center rounded-lg text-on-surface-variant transition-colors hover:bg-surface-container hover:text-on-surface focus:outline-none focus:ring-2 focus:ring-primary"
                        href="{{ route('admin.attendance', array_merge($filterQuery, ['date' => $nextDate->toDateString()])) }}"
                        aria-label="Jour suivant"
                    >
                        <span class="material-symbols-outlined text-body-md" aria-hidden="true">chevron_right</span>
                    </a>
                </div>
                <a class="flex h-8 items-center rounded-lg bg-surface-container px-3 font-label-sm text-label-sm text-on-surface transition-colors hover:bg-surface-container-high focus:outline-none focus:ring-2 focus:ring-primary" href="{{ route('admin.attendance', $todayQuery) }}">
                    Aujourd'hui
                </a>
                <span class="ml-2 hidden font-label-sm text-label-sm text-on-surface-variant xl:inline">
                    Référence : {{ $rules['scheduleStart'] }} – {{ $rules['scheduleEnd'] }}
                </span>
            </div>

            <form class="flex flex-wrap items-center gap-3" id="attendanceFilterForm" method="GET" action="{{ route('admin.attendance') }}">
                <input type="hidden" name="date" value="{{ $selectedDate->toDateString() }}" />
                @if ($employeeId)
                    <input type="hidden" name="employee" value="{{ $employeeId }}" />
                @endif

                <div class="relative min-w-[220px]">
                    <label class="sr-only" for="searchInput">Rechercher un collaborateur</label>
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[18px] text-on-surface-variant" aria-hidden="true">search</span>
                    <input
                        class="h-9 w-full rounded-xl bg-surface-container-low pl-9 pr-3 font-body-sm text-body-sm text-on-surface placeholder:text-on-surface-variant focus:bg-surface-container focus:outline-none focus:ring-2 focus:ring-primary"
                        id="searchInput"
                        name="q"
                        value="{{ $filters['q'] }}"
                        placeholder="Nom, CIN, CNSS, poste..."
                        type="search"
                    />
                </div>

                <div class="relative">
                    <label class="sr-only" for="departmentFilter">Département</label>
                    <select class="h-9 cursor-pointer appearance-none rounded-xl bg-surface-container-low pl-3 pr-8 font-label-md text-label-md text-on-surface focus:bg-surface-container focus:outline-none focus:ring-2 focus:ring-primary" id="departmentFilter" name="department" onchange="this.form.requestSubmit()">
                        <option value="">Tous les départements</option>
                        @foreach (\App\Models\Employee::DEPARTMENTS as $key => $label)
                            <option value="{{ $key }}" @selected($filters['department'] === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <span class="material-symbols-outlined pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 text-[18px] text-on-surface-variant" aria-hidden="true">arrow_drop_down</span>
                </div>

                <div class="relative">
                    <label class="sr-only" for="statusFilter">Statut de la journée</label>
                    <select class="h-9 cursor-pointer appearance-none rounded-xl bg-surface-container-low pl-3 pr-8 font-label-md text-label-md text-on-surface focus:bg-surface-container focus:outline-none focus:ring-2 focus:ring-primary" id="statusFilter" name="status" onchange="this.form.requestSubmit()">
                        <option value="">Tous les statuts</option>
                        @foreach (\App\Models\AttendanceRecord::STATUSES as $key => $label)
                            <option value="{{ $key }}" @selected($filters['status'] === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <span class="material-symbols-outlined pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 text-[18px] text-on-surface-variant" aria-hidden="true">arrow_drop_down</span>
                </div>

                <button class="sr-only" type="submit">Filtrer</button>

                @if ($hasFilters)
                    <a class="font-label-sm text-label-sm text-primary hover:underline focus:outline-none focus:ring-2 focus:ring-primary" href="{{ route('admin.attendance', $resetQuery) }}">
                        Réinitialiser
                    </a>
                @endif
            </form>
        </div>
    </section>

    <section class="overflow-hidden rounded-xl bg-surface-container-lowest shadow-sm" aria-labelledby="attendance-table-title">
        <div class="flex flex-col gap-3 border-b border-surface-container bg-surface-container-low/50 p-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex flex-wrap items-center gap-3">
                <h2 class="font-headline-sm text-headline-sm text-on-surface" id="attendance-table-title">
                    {{ $isHistory ? 'Historique des pointages' : 'Journal des pointages' }}
                </h2>
                <span class="rounded-full bg-surface-container px-2 py-0.5 font-label-sm text-label-sm text-on-surface-variant">
                    {{ $records->total() }} pointage{{ $records->total() > 1 ? 's' : '' }}
                </span>
                @if ($isHistory)
                    <span class="rounded-full bg-primary-fixed px-2 py-0.5 font-label-sm text-label-sm text-on-primary-fixed">
                        {{ $historyEmployee->name }}
                    </span>
                    <a class="rounded-full bg-surface-container-lowest px-2 py-0.5 font-label-sm text-label-sm text-primary transition-colors hover:bg-surface-container focus:outline-none focus:ring-2 focus:ring-primary" href="{{ route('admin.attendance', $dayQuery) }}">
                        Revenir au jour
                    </a>
                @endif
            </div>
            <p class="font-label-sm text-label-sm text-on-surface-variant">
                @if ($isHistory)
                    Mois de {{ $overtimeLabel }}
                @else
                    {{ $activeEmployees }} collaborateur{{ $activeEmployees > 1 ? 's' : '' }} actif{{ $activeEmployees > 1 ? 's' : '' }}
                @endif
            </p>
        </div>

        @if ($records->isEmpty())
            <div class="p-4">
                <x-ui.empty-state icon="event_busy" :title="$emptyTitle" :description="$emptyDescription">
                    @if ($hasFilters || $isHistory)
                        <a class="inline-flex h-9 items-center rounded-xl bg-surface-container px-4 font-label-md text-label-md text-on-surface transition-colors hover:bg-surface-container-high focus:outline-none focus:ring-2 focus:ring-primary" href="{{ route('admin.attendance', $resetQuery) }}">
                            Réinitialiser les filtres
                        </a>
                    @endif
                </x-ui.empty-state>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full border-collapse text-left">
                    <thead>
                        <tr class="h-11 bg-surface-container-low font-label-md text-label-md uppercase tracking-wider text-on-surface-variant">
                            <th class="px-4 py-2 font-medium">Collaborateur</th>
                            <th class="px-3 py-2 font-medium">Département</th>
                            <th class="px-3 py-2 font-medium">Arrivée</th>
                            <th class="px-3 py-2 font-medium">Départ</th>
                            <th class="px-3 py-2 font-medium">Pause</th>
                            <th class="px-3 py-2 font-medium">Total effectué</th>
                            <th class="px-3 py-2 font-medium">Mode</th>
                            <th class="px-3 py-2 font-medium">Statut</th>
                            <th class="px-4 py-2 text-right font-medium">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-container font-body-md text-body-md text-on-surface">
                        @foreach ($records as $record)
                            @php
                                $employee = $record->employee;
                                $isLate = $record->status === 'late';
                                $canEdit = $employee !== null
                                    && $employee->status !== 'inactif'
                                    && $record->status !== 'leave';
                                $editDirection = $record->check_out ? 'out' : 'in';
                                $editTime = $record->check_out
                                    ? $record->checkOutShort()
                                    : ($record->check_in ? $record->checkInShort() : $rules['scheduleStart']);
                            @endphp
                            <tr class="h-14 transition-colors hover:bg-surface-container-low/60 {{ $record->rowClasses() }}">
                                <td class="px-4 py-2.5">
                                    <div class="flex items-center gap-3">
                                        @if ($employee?->avatar_url)
                                            <img class="h-9 w-9 shrink-0 rounded-full object-cover" src="{{ $employee->avatar_url }}" alt="Portrait de {{ $employee->name }}" />
                                        @else
                                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-surface-container font-label-md text-label-md font-bold text-on-surface-variant" aria-hidden="true">
                                                {{ $employee?->initials() ?? '?' }}
                                            </div>
                                        @endif
                                        <div class="flex min-w-0 flex-col">
                                            <span class="truncate font-label-lg text-label-lg font-semibold text-on-surface">{{ $employee?->name ?? 'Collaborateur supprimé' }}</span>
                                            <span class="truncate font-label-sm text-label-sm text-on-surface-variant">
                                                MAT-{{ str_pad((string) $record->employee_id, 4, '0', STR_PAD_LEFT) }} • {{ $employee?->position ?? '—' }}
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-3 py-2.5">
                                    <span class="rounded bg-surface-container px-2 py-0.5 font-label-md text-label-md text-on-surface">{{ $employee?->departmentShortLabel() ?? '—' }}</span>
                                </td>
                                <td class="px-3 py-2.5">
                                    @if ($record->check_in)
                                        <div class="flex items-center gap-1.5">
                                            <span class="font-numeric-md text-numeric-md font-semibold {{ $isLate ? 'text-error' : 'text-on-surface' }}">{{ $record->checkInShort() }}</span>
                                            @if ($isLate)
                                                <span class="rounded bg-error-container px-1.5 py-0.5 font-label-sm text-[11px] font-medium text-error">+{{ $record->lateMinutes() }} min</span>
                                            @elseif ($record->status === 'remote')
                                                <span class="rounded bg-primary-fixed px-1.5 py-0.5 font-label-sm text-[11px] font-medium text-primary">À distance</span>
                                            @else
                                                <span class="rounded bg-tertiary-fixed/40 px-1.5 py-0.5 font-label-sm text-[11px] font-medium text-tertiary">À l'heure</span>
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
                                        <span class="font-numeric-md text-numeric-md italic text-on-surface-variant">En poste</span>
                                    @endif
                                </td>
                                <td class="px-3 py-2.5">
                                    <span class="font-numeric-md text-numeric-md text-on-surface-variant">{{ $record->breakLabel() }}</span>
                                </td>
                                <td class="px-3 py-2.5">
                                    @if ($record->check_in && $record->check_out)
                                        <span class="font-numeric-md text-numeric-md font-semibold {{ $record->status === 'absent' ? 'text-error' : 'text-on-surface' }}">{{ $record->workedLabel() }}</span>
                                    @elseif ($record->check_in)
                                        <span class="font-label-sm text-label-sm italic text-on-surface-variant">En cours</span>
                                    @else
                                        <span class="font-label-sm text-label-sm text-on-surface-variant">—</span>
                                    @endif
                                </td>
                                <td class="px-3 py-2.5">
                                    @if ($record->check_in && $record->mode !== 'none')
                                        <div class="flex items-center gap-1.5 font-label-sm text-label-sm text-on-surface-variant">
                                            <span class="material-symbols-outlined text-[16px] {{ $record->modeColor() }}" aria-hidden="true">{{ $record->modeIcon() }}</span>
                                            <span>{{ $record->modeLabel() }}</span>
                                        </div>
                                    @else
                                        <span class="font-label-sm text-label-sm text-on-surface-variant">—</span>
                                    @endif
                                </td>
                                <td class="px-3 py-2.5">
                                    <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 font-label-sm text-label-sm font-semibold {{ $record->statusPillClasses() }}">
                                        <span class="h-1.5 w-1.5 rounded-full {{ $record->statusDotClasses() }}" aria-hidden="true"></span>
                                        {{ $record->statusLabel() }}
                                    </span>
                                </td>
                                <td class="px-4 py-2.5 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        @if ($record->status === 'absent' && $employee?->email)
                                            <a class="flex h-7 items-center gap-1 rounded-lg bg-surface-container-lowest px-2.5 font-label-sm text-label-sm text-error transition-colors hover:bg-surface-container focus:outline-none focus:ring-2 focus:ring-primary" href="mailto:{{ $employee->email }}?subject={{ rawurlencode('Rappel de pointage — '.$record->work_date->format('d/m/Y')) }}">
                                                <span class="material-symbols-outlined text-[14px]" aria-hidden="true">send</span>
                                                Relancer
                                            </a>
                                        @endif
                                        @if ($canEdit)
                                            <button
                                                class="js-edit-punch rounded-lg p-1.5 text-on-surface-variant transition-colors hover:bg-surface-container hover:text-primary focus:outline-none focus:ring-2 focus:ring-primary"
                                                type="button"
                                                title="Modifier le pointage"
                                                aria-label="Modifier le pointage de {{ $employee?->name ?? 'ce collaborateur' }}"
                                                data-employee="{{ $record->employee_id }}"
                                                data-date="{{ $record->work_date->toDateString() }}"
                                                data-time="{{ $editTime }}"
                                                data-direction="{{ $editDirection }}"
                                            >
                                                <span class="material-symbols-outlined text-body-md" aria-hidden="true">edit</span>
                                            </button>
                                        @endif
                                        @if ($employee)
                                            <a
                                                class="rounded-lg p-1.5 text-on-surface-variant transition-colors hover:bg-surface-container hover:text-on-surface focus:outline-none focus:ring-2 focus:ring-primary"
                                                href="{{ route('admin.attendance', array_merge($filterQuery, ['date' => $selectedDate->toDateString(), 'employee' => $record->employee_id])) }}"
                                                title="Consulter l'historique"
                                                aria-label="Consulter l'historique de {{ $employee->name }}"
                                            >
                                                <span class="material-symbols-outlined text-body-md" aria-hidden="true">history</span>
                                            </a>
                                        @else
                                            <span class="font-label-sm text-label-sm text-on-surface-variant">—</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <div class="flex flex-col items-center justify-between gap-3 border-t border-surface-container bg-surface-container-low/40 p-4 sm:flex-row">
            <span class="font-label-sm text-label-sm text-on-surface-variant">
                Affichage de {{ $records->firstItem() ?? 0 }} à {{ $records->lastItem() ?? 0 }} sur {{ $records->total() }} pointage{{ $records->total() > 1 ? 's' : '' }}
            </span>
            <x-ui.pagination :paginator="$records" />
        </div>
    </section>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <section class="rounded-xl bg-surface-container-lowest p-5 shadow-sm lg:col-span-2" aria-labelledby="week-title">
            <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <h2 class="font-headline-sm text-headline-sm text-on-surface" id="week-title">Assiduité de la semaine</h2>
                    <p class="font-body-sm text-body-sm text-on-surface-variant">{{ $weekLabel }} • Jours ouvrés selon le calendrier des congés (dimanche exclu).</p>
                </div>
                <div class="flex items-center gap-3">
                    <span class="flex items-center gap-1.5 font-label-sm text-label-sm text-on-surface-variant">
                        <span class="h-3 w-3 rounded bg-primary" aria-hidden="true"></span> Présents
                    </span>
                    <span class="flex items-center gap-1.5 font-label-sm text-label-sm text-on-surface-variant">
                        <span class="h-3 w-3 rounded bg-secondary-fixed-dim" aria-hidden="true"></span> Retards
                    </span>
                </div>
            </div>

            <div class="flex h-44 w-full items-end gap-2 px-1 pb-2 pt-4 sm:gap-4" role="img" aria-label="Taux de présence quotidien — {{ $weekLabel }}">
                @foreach ($weekDays as $day)
                    <div class="flex h-full flex-1 flex-col items-center justify-end gap-2 {{ $day['isFuture'] ? 'opacity-40' : '' }}">
                        <span class="font-label-sm text-label-sm {{ $day['isToday'] ? 'font-bold text-primary' : 'text-on-surface-variant' }}">
                            {{ $day['rate'] !== null ? $day['rate'].' %' : '—' }}
                        </span>
                        <div
                            class="flex h-full w-full max-w-[48px] flex-col justify-end rounded-t-lg p-1 {{ $day['isToday'] ? 'bg-primary-fixed/20 ring-1 ring-primary/20' : 'bg-surface-container-low' }}"
                            title="{{ $day['fullLabel'] }} — {{ $day['rate'] !== null ? $day['rate'].' % de présence' : 'jour à venir' }}"
                        >
                            @if ($day['rate'] !== null)
                                <div class="w-full rounded-t-sm bg-secondary-fixed" style="height: {{ $day['lateHeight'] }}%"></div>
                                <div class="w-full rounded-b-sm {{ $day['isToday'] ? 'bg-primary-container' : 'bg-primary' }}" style="height: {{ $day['primaryHeight'] }}%"></div>
                            @else
                                <div class="h-[70%] w-full rounded-t-sm bg-surface-container-high"></div>
                            @endif
                        </div>
                        <span class="font-label-sm text-label-sm {{ $day['isToday'] ? 'font-bold text-primary' : 'text-on-surface-variant' }}">{{ $day['label'] }}</span>
                    </div>
                @endforeach
            </div>

            <div class="mt-4 flex flex-col gap-2 rounded-lg bg-surface-container-low/40 p-2.5 sm:flex-row sm:items-center sm:justify-between">
                <span class="flex items-center gap-2 font-label-sm text-label-sm text-on-surface">
                    <span class="material-symbols-outlined text-body-md text-primary" aria-hidden="true">info</span>
                    Taux calculés sur {{ $activeEmployees }} collaborateur{{ $activeEmployees > 1 ? 's' : '' }} actif{{ $activeEmployees > 1 ? 's' : '' }}.
                </span>
                <span class="font-label-sm text-label-sm text-on-surface-variant">Tolérance retard : +{{ $rules['lateTolerance'] }} min</span>
            </div>
        </section>

        <section class="flex flex-col rounded-xl bg-surface-container-lowest p-5 shadow-sm" aria-labelledby="anomalies-title">
            <div class="mb-3 flex items-start justify-between gap-3">
                <div>
                    <h2 class="flex items-center gap-2 font-headline-sm text-headline-sm text-on-surface" id="anomalies-title">
                        Anomalies
                        <span class="rounded-full px-2 py-0.5 font-label-sm text-label-sm font-bold {{ $anomalyCount > 0 ? 'bg-error-container text-on-error-container' : 'bg-surface-container text-on-surface-variant' }}">{{ $anomalyCount }}</span>
                    </h2>
                    <p class="font-body-sm text-body-sm text-on-surface-variant">Pointages nécessitant une régularisation RH.</p>
                </div>
                @if ($anomalyCount > 0)
                    <form method="POST" action="{{ route('admin.attendance.anomalies.resolveAll') }}" onsubmit="return confirm('Marquer les {{ $anomalyCount }} anomalie(s) de cette date comme traitées ?');">
                        @csrf
                        <input type="hidden" name="date" value="{{ $selectedDate->toDateString() }}" />
                        <button class="font-label-sm text-label-sm text-primary hover:underline focus:outline-none focus:ring-2 focus:ring-primary" type="submit">Tout traiter</button>
                    </form>
                @endif
            </div>

            <div class="space-y-3 {{ $anomalyCount > 4 ? 'max-h-[26rem] overflow-y-auto pr-1' : '' }}">
                @forelse ($anomalies as $anomaly)
                    <article class="flex items-start gap-3 rounded-xl bg-surface-container-low p-3">
                        <span class="mt-0.5 flex h-7 w-7 shrink-0 items-center justify-center rounded-full {{ $anomaly['tone'] }}" aria-hidden="true">
                            <span class="material-symbols-outlined text-[16px]">{{ $anomaly['icon'] }}</span>
                        </span>
                        <div class="min-w-0 flex-1">
                            <p class="font-label-md text-label-md font-semibold text-on-surface">{{ $anomaly['title'] }}</p>
                            <p class="font-body-sm text-body-sm text-on-surface-variant">{{ $anomaly['subtitle'] }}</p>
                            <p class="mt-0.5 font-label-sm text-label-sm text-on-surface-variant">{{ $anomaly['detail'] }}</p>
                            <div class="mt-2 flex flex-wrap items-center gap-2">
                                @if ($anomaly['action'] === 'resolve')
                                    <form method="POST" action="{{ route('admin.attendance.anomalies.resolve', $anomaly['record']) }}">
                                        @csrf
                                        <button class="h-6 rounded bg-surface-container px-2 font-label-sm text-label-sm text-on-surface transition-colors hover:bg-surface-container-high focus:outline-none focus:ring-2 focus:ring-primary" type="submit">
                                            {{ $anomaly['actionLabel'] }}
                                        </button>
                                    </form>
                                @else
                                    <button
                                        class="js-justify-anomaly h-6 rounded bg-surface-container px-2 font-label-sm text-label-sm text-on-surface transition-colors hover:bg-surface-container-high focus:outline-none focus:ring-2 focus:ring-primary"
                                        type="button"
                                        data-record-id="{{ $anomaly['record']->id }}"
                                        data-justify-action="{{ route('admin.attendance.anomalies.justify', $anomaly['record']) }}"
                                        data-justify-subtitle="{{ $anomaly['subtitle'] }}"
                                    >
                                        {{ $anomaly['actionLabel'] }}
                                    </button>
                                @endif
                                <form method="POST" action="{{ route('admin.attendance.anomalies.ignore', $anomaly['record']) }}">
                                    @csrf
                                    <button class="font-label-sm text-label-sm text-on-surface-variant hover:text-on-surface focus:outline-none focus:ring-2 focus:ring-primary" type="submit">Ignorer</button>
                                </form>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="flex items-center gap-3 rounded-xl bg-surface-container-low p-4 text-on-surface-variant">
                        <span class="material-symbols-outlined text-tertiary" aria-hidden="true">task_alt</span>
                        <div>
                            <p class="font-label-md text-label-md font-semibold text-on-surface">
                                {{ $ignoredAnomalyCount > 0 ? 'Toutes les anomalies sont traitées.' : 'Aucune anomalie détectée.' }}
                            </p>
                            <p class="font-body-sm text-body-sm">
                                {{ $ignoredAnomalyCount > 0 ? $ignoredAnomalyCount.' anomalie(s) marquée(s) comme traitée(s).' : 'Les pointages de cette date sont cohérents.' }}
                            </p>
                        </div>
                    </div>
                @endforelse
            </div>

            <div class="mt-auto flex flex-col gap-2 border-t border-surface-container pt-3 text-on-surface-variant sm:flex-row sm:items-center sm:justify-between">
                <span class="font-label-sm text-label-sm">
                    @if ($lastUpdatedAt)
                        Dernière mise à jour : {{ $lastUpdatedAt->format('d/m/Y à H:i') }}
                    @else
                        Aucun pointage enregistré pour cette date
                    @endif
                </span>
                <a class="flex items-center gap-1 font-label-sm text-label-sm text-primary hover:text-primary-container focus:outline-none focus:ring-2 focus:ring-primary" href="{{ url()->full() }}">
                    <span class="material-symbols-outlined text-[14px]" aria-hidden="true">refresh</span>
                    Actualiser
                </a>
            </div>
        </section>
    </div>
</div>

<x-ui.modal id="punchDialog" title="Pointage manuel" description="Saisie exceptionnelle tracée au nom de l'administrateur connecté.">
    <form class="space-y-4" method="POST" action="{{ route('admin.attendance.punch') }}">
        @csrf

        @if ($errors->punch->any())
            <div class="rounded-lg bg-error-container p-3 text-on-error-container" role="alert">
                <p class="font-label-sm text-label-sm">{{ $errors->punch->first() }}</p>
            </div>
        @endif

        <div>
            <label class="mb-1 block font-label-md text-label-md font-semibold text-on-surface" for="punchEmployee">Collaborateur</label>
            <select class="h-10 w-full rounded-xl bg-surface-container-low px-3 font-body-md text-body-md text-on-surface focus:bg-surface-container focus:outline-none focus:ring-2 focus:ring-primary" id="punchEmployee" name="employee_id" required>
                <option value="" @selected(! old('employee_id'))>Sélectionner un collaborateur</option>
                @foreach ($employees as $employee)
                    <option value="{{ $employee->id }}" @selected((string) old('employee_id', $employeeId) === (string) $employee->id)>
                        {{ $employee->name }} — MAT-{{ str_pad((string) $employee->id, 4, '0', STR_PAD_LEFT) }} ({{ $employee->departmentShortLabel() }})
                    </option>
                @endforeach
            </select>
            @error('employee_id', 'punch')
                <p class="mt-1 font-label-sm text-label-sm text-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
            <div>
                <label class="mb-1 block font-label-md text-label-md font-semibold text-on-surface" for="punchDate">Date</label>
                <input class="h-10 w-full rounded-xl bg-surface-container-low px-3 font-body-md text-body-md text-on-surface focus:bg-surface-container focus:outline-none focus:ring-2 focus:ring-primary" id="punchDate" name="date" type="date" value="{{ old('date', $selectedDate->toDateString()) }}" max="{{ $today->toDateString() }}" required />
                @error('date', 'punch')
                    <p class="mt-1 font-label-sm text-label-sm text-error">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="mb-1 block font-label-md text-label-md font-semibold text-on-surface" for="punchTime">Heure</label>
                <input class="h-10 w-full rounded-xl bg-surface-container-low px-3 font-body-md text-body-md text-on-surface focus:bg-surface-container focus:outline-none focus:ring-2 focus:ring-primary" id="punchTime" name="time" type="time" value="{{ old('time', $rules['scheduleStart']) }}" required />
                @error('time', 'punch')
                    <p class="mt-1 font-label-sm text-label-sm text-error">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
            <div>
                <label class="mb-1 block font-label-md text-label-md font-semibold text-on-surface" for="punchDirection">Sens du pointage</label>
                <select class="h-10 w-full rounded-xl bg-surface-container-low px-3 font-body-md text-body-md text-on-surface focus:bg-surface-container focus:outline-none focus:ring-2 focus:ring-primary" id="punchDirection" name="direction" required>
                    @foreach (\App\Models\AttendanceRecord::DIRECTIONS as $value => $label)
                        <option value="{{ $value }}" @selected(old('direction', 'in') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('direction', 'punch')
                    <p class="mt-1 font-label-sm text-label-sm text-error">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="mb-1 block font-label-md text-label-md font-semibold text-on-surface" for="punchMotif">Motif</label>
                <select class="h-10 w-full rounded-xl bg-surface-container-low px-3 font-body-md text-body-md text-on-surface focus:bg-surface-container focus:outline-none focus:ring-2 focus:ring-primary" id="punchMotif" name="motif" required>
                    <option value="" disabled @selected(! old('motif'))>Sélectionner un motif</option>
                    @foreach (\App\Models\AttendanceRecord::MOTIFS as $value => $label)
                        <option value="{{ $value }}" @selected(old('motif') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('motif', 'punch')
                    <p class="mt-1 font-label-sm text-label-sm text-error">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div>
            <label class="mb-1 block font-label-md text-label-md font-semibold text-on-surface" for="punchComment">
                Commentaire <span class="font-normal text-on-surface-variant">(optionnel)</span>
            </label>
            <textarea class="w-full rounded-xl bg-surface-container-low p-2.5 font-body-md text-body-md text-on-surface placeholder:text-on-surface-variant focus:bg-surface-container focus:outline-none focus:ring-2 focus:ring-primary" id="punchComment" name="comment" rows="2" maxlength="500" placeholder="Ex. ordre de mission, panne du terminal...">{{ old('comment') }}</textarea>
            @error('comment', 'punch')
                <p class="mt-1 font-label-sm text-label-sm text-error">{{ $message }}</p>
            @enderror
        </div>

        <p class="flex items-start gap-2.5 rounded-lg bg-surface-container-low p-3 font-body-sm text-body-sm text-on-surface-variant">
            <span class="material-symbols-outlined mt-0.5 text-body-md text-primary" aria-hidden="true">verified</span>
            <span>Ce pointage sera ajouté aux notes du collaborateur avec votre nom ({{ auth()->user()?->name ?? 'Administrateur RH' }}).</span>
        </p>

        <div class="flex items-center justify-end gap-3 border-t border-surface-container pt-4">
            <button class="h-9 rounded-xl bg-surface-container px-4 font-label-lg text-label-lg text-on-surface transition-colors hover:bg-surface-container-high focus:outline-none focus:ring-2 focus:ring-primary" type="button" onclick="document.getElementById('punchDialog').close()">
                Annuler
            </button>
            <button class="h-9 rounded-xl bg-primary-container px-5 font-label-lg text-label-lg text-on-primary shadow-sm transition-colors hover:bg-primary focus:outline-none focus:ring-2 focus:ring-primary" type="submit">
                Enregistrer
            </button>
        </div>
    </form>
</x-ui.modal>

<x-ui.modal id="justifyDialog" title="Justifier l'anomalie" description="Le justificatif est ajouté aux notes du pointage.">
    <form class="space-y-4" id="justifyForm" method="POST" action="{{ (int) old('record_id') ? route('admin.attendance.anomalies.justify', (int) old('record_id')) : '' }}">
        @csrf
        <input type="hidden" id="justifyRecord" name="record_id" value="{{ old('record_id') }}" />

        @if ($errors->justify->any())
            <div class="rounded-lg bg-error-container p-3 text-on-error-container" role="alert">
                <p class="font-label-sm text-label-sm">{{ $errors->justify->first() }}</p>
            </div>
        @endif

        <p class="rounded-lg bg-surface-container-low p-3 font-body-sm text-body-sm text-on-surface-variant" id="justifyEmployee">
            {{ (int) old('record_id') ? 'Anomalie sélectionnée' : '' }}
        </p>

        <div>
            <label class="mb-1 block font-label-md text-label-md font-semibold text-on-surface" for="justifyNote">Justificatif</label>
            <textarea class="w-full rounded-xl bg-surface-container-low p-2.5 font-body-md text-body-md text-on-surface placeholder:text-on-surface-variant focus:bg-surface-container focus:outline-none focus:ring-2 focus:ring-primary" id="justifyNote" name="note" rows="4" maxlength="500" placeholder="Ex. bon de sortie signé, mission validée..." required>{{ old('note') }}</textarea>
            @error('note', 'justify')
                <p class="mt-1 font-label-sm text-label-sm text-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center justify-end gap-3 border-t border-surface-container pt-4">
            <button class="h-9 rounded-xl bg-surface-container px-4 font-label-lg text-label-lg text-on-surface transition-colors hover:bg-surface-container-high focus:outline-none focus:ring-2 focus:ring-primary" type="button" onclick="document.getElementById('justifyDialog').close()">
                Annuler
            </button>
            <button class="h-9 rounded-xl bg-primary-container px-5 font-label-lg text-label-lg text-on-primary shadow-sm transition-colors hover:bg-primary focus:outline-none focus:ring-2 focus:ring-primary" type="submit">
                Enregistrer le justificatif
            </button>
        </div>
    </form>
</x-ui.modal>

<script>
    (function () {
        function openDialog(id, focusId) {
            var dialog = document.getElementById(id);

            if (!dialog) {
                return;
            }

            if (!dialog.open) {
                if (typeof dialog.showModal === 'function') {
                    dialog.showModal();
                } else {
                    dialog.setAttribute('open', '');
                }
            }

            if (focusId) {
                window.setTimeout(function () {
                    var field = document.getElementById(focusId);

                    if (field) {
                        field.focus();
                    }
                }, 60);
            }
        }

        var openPunch = document.querySelector('[data-open-punch]');

        if (openPunch) {
            openPunch.addEventListener('click', function () {
                openDialog('punchDialog', 'punchEmployee');
            });
        }

        document.querySelectorAll('.js-edit-punch').forEach(function (button) {
            button.addEventListener('click', function () {
                var employee = document.getElementById('punchEmployee');
                var date = document.getElementById('punchDate');
                var time = document.getElementById('punchTime');
                var direction = document.getElementById('punchDirection');

                if (employee && button.dataset.employee) {
                    employee.value = button.dataset.employee;
                }

                if (date && button.dataset.date) {
                    date.value = button.dataset.date;
                }

                if (time && button.dataset.time) {
                    time.value = button.dataset.time;
                }

                if (direction) {
                    direction.value = button.dataset.direction || 'in';
                }

                openDialog('punchDialog', 'punchMotif');
            });
        });

        document.querySelectorAll('.js-justify-anomaly').forEach(function (button) {
            button.addEventListener('click', function () {
                var form = document.getElementById('justifyForm');
                var record = document.getElementById('justifyRecord');
                var note = document.getElementById('justifyNote');
                var employee = document.getElementById('justifyEmployee');

                if (form && button.dataset.justifyAction) {
                    form.action = button.dataset.justifyAction;
                }

                if (record) {
                    record.value = button.dataset.recordId || '';
                }

                if (note) {
                    note.value = '';
                }

                if (employee) {
                    employee.textContent = button.dataset.justifySubtitle || '';
                }

                openDialog('justifyDialog', 'justifyNote');
            });
        });

        @if ($errors->punch->any())
            openDialog('punchDialog', 'punchEmployee');
        @endif

        @if ($errors->justify->any())
            openDialog('justifyDialog', 'justifyNote');
        @endif

        var filterForm = document.getElementById('attendanceFilterForm');
        var searchInput = document.getElementById('searchInput');
        var searchTimer;

        if (filterForm && searchInput) {
            searchInput.addEventListener('input', function () {
                clearTimeout(searchTimer);
                searchTimer = window.setTimeout(function () {
                    filterForm.requestSubmit();
                }, 500);
            });

            searchInput.addEventListener('keydown', function (event) {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    clearTimeout(searchTimer);
                    filterForm.requestSubmit();
                }
            });
        }
    })();
</script>
</x-admin-shell>
