@php
    $minutesLabel = fn (int $minutes): string => intdiv(max(0, $minutes), 60).'h '.str_pad((string) (max(0, $minutes) % 60), 2, '0', STR_PAD_LEFT).'m';

    $stateLabels = [
        'not_started' => ['label' => 'Non pointé', 'classes' => 'bg-surface-container-high text-on-surface-variant', 'dot' => 'bg-outline'],
        'working' => ['label' => 'En poste', 'classes' => 'bg-tertiary-fixed text-on-tertiary-fixed', 'dot' => 'bg-tertiary-container'],
        'on_break' => ['label' => 'En pause', 'classes' => 'bg-secondary-container text-on-secondary-fixed', 'dot' => 'bg-secondary'],
        'done' => ['label' => 'Journée terminée', 'classes' => 'bg-surface-container-high text-on-surface-variant', 'dot' => 'bg-outline'],
        'leave' => ['label' => 'En congé', 'classes' => 'bg-secondary-container text-on-secondary-fixed', 'dot' => 'bg-secondary'],
    ];
    $state = $stateLabels[$punchState] ?? $stateLabels['not_started'];

    $expectedDaily = (int) config('hrm.expected_daily_minutes');
    $dayPercent = $elapsedMinutes > 0 ? (int) min(100, round($elapsedMinutes / max(1, $expectedDaily) * 100)) : 0;
    $weekRest = max(0, $weekExpectedMinutes - $weekWorkedMinutes);

    $arrivalLabel = $averageArrival !== null
        ? sprintf('%02d:%02d', intdiv($averageArrival, 60), $averageArrival % 60)
        : '—';

    $estimatedExit = null;
    if ($todayRecord?->check_in) {
        [$h, $m] = array_pad(explode(':', (string) $todayRecord->check_in), 2, 0);
        $exit = (int) $h * 60 + (int) $m + $expectedDaily + (int) $todayRecord->break_minutes;
        $estimatedExit = sprintf('%02d:%02d', intdiv($exit, 60) % 24, $exit % 60);
    }

    $periodLabel = $period === 'month'
        ? ucfirst($today->locale('fr')->translatedFormat('F Y'))
        : 'du '.$rangeStart->locale('fr')->translatedFormat('d/m').' au '.$rangeEnd->locale('fr')->translatedFormat('d/m');

    $averageWorked = collect($journal)
        ->map(fn (array $row) => $row['record']->workedMinutes())
        ->filter(fn (int $minutes) => $minutes > 0);
    $averageWorkedLabel = $averageWorked->isNotEmpty() ? $minutesLabel((int) round($averageWorked->avg())) : '—';

    $todayRow = collect($journal)->firstWhere('isToday', true);
    $failedForm = old('form');
    $regDateDefault = $latestAnomaly?->work_date?->toDateString() ?? $today->toDateString();
@endphp

<x-collaborator-shell active="attendance" title="Mon Pointage">
    <div class="flex flex-col w-full">
        <div class="px-4 sm:px-margin py-space-xl flex flex-col gap-space-xl max-w-[1536px] mx-auto w-full">

            <!-- Top Bar -->
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-space-lg">
                <div class="flex flex-col gap-space-xs">
                    <div class="flex flex-wrap items-center gap-space-xs">
                        <span class="font-label-sm text-label-sm text-primary uppercase tracking-widest font-semibold">Présence &amp; Feuilles de Temps</span>
                        <span class="w-1.5 h-1.5 rounded-full bg-outline-variant"></span>
                        <span class="font-label-sm text-label-sm text-on-surface-variant">Régime 40h (Code du Travail TN)</span>
                    </div>
                    <h1 class="font-headline-lg text-headline-lg text-on-surface tracking-tight">Mon Pointage &amp; Suivi du Temps</h1>
                    <p class="font-body-md text-body-md text-on-surface-variant max-w-3xl">
                        Pointez vos arrivées, pauses et départs, déclarez vos journées de télétravail et régularisez vos anomalies de badgeage depuis le portail.
                    </p>
                </div>

                <!-- Live Clock -->
                <div class="flex items-center gap-space-md self-start lg:self-auto bg-surface-container-low px-space-lg py-space-md rounded-xl shadow-sm">
                    <div class="w-10 h-10 rounded-xl bg-tertiary-container/10 flex items-center justify-center text-tertiary-container shrink-0">
                        <span class="material-symbols-outlined text-[24px]">nest_clock_farsight_analog</span>
                    </div>
                    <div class="flex flex-col">
                        <div class="flex items-center gap-space-xs">
                            <span class="font-headline-sm text-headline-sm text-on-surface font-semibold tracking-tight" id="live-clock-time" data-server-timestamp="{{ $serverTimestamp }}">--:--:--</span>
                            <span class="font-label-md text-label-md text-on-surface-variant font-normal">TUN (Tunis)</span>
                        </div>
                        <span class="font-label-sm text-label-sm text-on-surface-variant">{{ ucfirst($today->locale('fr')->translatedFormat('l j F Y')) }}</span>
                    </div>
                    <div class="ml-space-sm pl-space-md flex flex-col items-end">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full font-label-sm text-[11px] font-semibold {{ $state['classes'] }}">
                            <span class="w-2 h-2 rounded-full {{ $state['dot'] }} {{ in_array($punchState, ['working', 'on_break'], true) ? 'animate-pulse' : '' }}"></span>
                            {{ $state['label'] }}
                        </span>
                        <span class="font-label-sm text-[10px] text-on-surface-variant mt-0.5">Portail Web SSO</span>
                    </div>
                </div>
            </div>

            <!-- Punch Panel -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-space-lg">
                <div class="lg:col-span-7 bg-surface-container-lowest rounded-xl p-space-xl shadow-sm flex flex-col justify-between relative overflow-hidden scroll-mt-24" id="teletravail">
                    <div class="absolute -right-8 -top-8 w-44 h-44 rounded-full bg-primary/5 pointer-events-none blur-2xl"></div>
                    <div>
                        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-space-md mb-space-md">
                            <div>
                                <span class="font-label-md text-label-md text-primary font-semibold uppercase tracking-wider">Badgeuse Virtuelle</span>
                                <h2 class="font-headline-sm text-headline-sm text-on-surface mt-0.5">Session de travail en cours</h2>
                            </div>
                            <div class="flex items-center gap-1.5 px-3 py-1 rounded-full bg-surface-container text-on-surface-variant font-label-sm text-label-sm">
                                <span class="material-symbols-outlined text-[16px] text-tertiary-container">schedule</span>
                                <span>{{ config('hrm.timezone') }}</span>
                            </div>
                        </div>

                        @if ($leaveToday)
                            <div class="p-space-sm px-space-md rounded-lg bg-secondary-container text-on-secondary-fixed font-label-md text-label-md mb-space-md flex items-center gap-2">
                                <span class="material-symbols-outlined text-[18px]">beach_access</span>
                                <span>Journée couverte par un congé approuvé : <strong>{{ $leaveToday->typeLabel() }}</strong> jusqu'au {{ $leaveToday->end_date?->locale('fr')->translatedFormat('d F Y') }}.</span>
                            </div>
                        @endif

                        <!-- Timeline -->
                        <div class="bg-surface-container-low rounded-xl p-space-md mb-space-lg">
                            <div class="flex flex-wrap items-center justify-between gap-2 text-on-surface font-label-md text-label-md mb-space-xs">
                                <div class="flex items-center gap-1.5">
                                    <span class="material-symbols-outlined text-[18px] text-tertiary-container">login</span>
                                    <span>Arrivée: <strong>{{ $todayRecord?->checkInShort() ?? '—' }}</strong></span>
                                </div>
                                <div class="flex items-center gap-1.5 text-on-surface-variant">
                                    <span class="material-symbols-outlined text-[18px]">restaurant</span>
                                    <span>Pause: <strong>{{ $todayRecord?->isOnBreak() ? 'En cours' : ($todayRecord?->breakLabel() ?? '—') }}</strong></span>
                                </div>
                                <div class="flex items-center gap-1.5 text-primary">
                                    <span class="material-symbols-outlined text-[18px]">timer</span>
                                    <span>Cumul: <strong>{{ $minutesLabel($elapsedMinutes) }}</strong></span>
                                </div>
                            </div>
                            <div class="w-full bg-surface-container-high rounded-full h-2.5 overflow-hidden">
                                <div class="bg-primary-container h-full rounded-full transition-all duration-500" style="width: {{ $dayPercent }}%"></div>
                            </div>
                            <div class="flex flex-wrap justify-between items-center gap-2 mt-1 text-[11px] font-label-sm text-on-surface-variant">
                                <span>{{ $todayRecord?->checkInShort() ?? '—' }} Prise de poste</span>
                                <span>Objectif quotidien: {{ $minutesLabel($expectedDaily) }}</span>
                                <span>Sortie estimée: {{ $estimatedExit ?? '—' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-space-md pt-space-xs">
                        @if ($punchState === 'not_started')
                            <form class="flex-1" method="POST" action="{{ route('collaborator.attendance.punch-in') }}">
                                @csrf
                                <button class="w-full h-11 px-space-lg rounded-xl bg-primary-container hover:bg-primary-fill text-on-primary font-label-lg text-label-lg font-semibold flex items-center justify-center gap-space-xs shadow-sm transition-all active:scale-[0.98] disabled:opacity-60" type="submit" @disabled($leaveToday)>
                                    <span class="material-symbols-outlined text-[20px]">login</span>
                                    <span>Pointer mon Arrivée</span>
                                </button>
                            </form>
                        @endif

                        @if ($punchState === 'working')
                            <form class="flex-1" method="POST" action="{{ route('collaborator.attendance.pause-start') }}">
                                @csrf
                                <button class="w-full h-11 px-space-lg rounded-xl bg-surface-container-high hover:bg-surface-variant text-on-surface font-label-lg text-label-lg font-medium flex items-center justify-center gap-space-xs transition-all active:scale-[0.98]" type="submit">
                                    <span class="material-symbols-outlined text-[20px]">coffee</span>
                                    <span>Démarrer une Pause</span>
                                </button>
                            </form>
                        @endif

                        @if ($punchState === 'on_break')
                            <form class="flex-1" method="POST" action="{{ route('collaborator.attendance.pause-end') }}">
                                @csrf
                                <button class="w-full h-11 px-space-lg rounded-xl bg-tertiary-fixed text-on-tertiary-fixed font-label-lg text-label-lg font-semibold flex items-center justify-center gap-space-xs shadow-sm transition-all active:scale-[0.98]" type="submit">
                                    <span class="material-symbols-outlined text-[20px]">play_arrow</span>
                                    <span>Reprendre le Travail</span>
                                </button>
                            </form>
                        @endif

                        @if ($punchState === 'working' || $punchState === 'on_break')
                            <button class="flex-1 h-11 px-space-lg rounded-xl bg-primary-container hover:bg-primary-fill text-on-primary font-label-lg text-label-lg font-semibold flex items-center justify-center gap-space-xs shadow-sm transition-all active:scale-[0.98]" data-open-punch-out type="button">
                                <span class="material-symbols-outlined text-[20px]">logout</span>
                                <span>Pointer ma Sortie</span>
                            </button>
                        @endif

                        @if ($punchState === 'done' || $punchState === 'leave' || $punchState === 'not_started')
                            <button class="flex-1 h-11 px-space-md rounded-xl bg-surface-container-high text-on-surface font-label-lg text-label-lg font-medium flex items-center justify-center gap-1 transition-all hover:bg-surface-variant" data-open-modal="telework-modal" type="button" title="Déclarer une journée en télétravail">
                                <span class="material-symbols-outlined text-[20px]">home_work</span>
                                <span>Déclarer Télétravail</span>
                            </button>
                        @endif
                    </div>

                    @if ($punchState === 'done')
                        <p class="font-label-sm text-label-sm text-on-surface-variant pt-space-sm flex items-center gap-1">
                            <span class="material-symbols-outlined text-[16px] text-tertiary-container">check_circle</span>
                            Journée clôturée : {{ $todayRecord?->workedLabel() }} travaillées. Vous pouvez encore régulariser un oubli ci-dessous.
                        </p>
                    @endif
                </div>

                <!-- Identity / Security Card -->
                <div class="lg:col-span-5 bg-surface-container-lowest rounded-xl p-space-xl shadow-sm flex flex-col justify-between gap-space-md">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-space-xs">
                            <span class="material-symbols-outlined text-primary text-[20px]">verified</span>
                            <span class="font-headline-sm text-headline-sm text-on-surface">Vérification de Sécurité</span>
                        </div>
                        <span class="px-2 py-0.5 rounded-full bg-tertiary-container/10 text-tertiary-container font-label-sm text-[11px] font-semibold">
                            Pointage Garanti INPDP
                        </span>
                    </div>

                    <div class="flex items-center gap-space-md p-space-md bg-surface-container-low rounded-xl">
                        <div class="w-12 h-12 rounded-xl bg-primary/10 text-primary flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-[26px]">fingerprint</span>
                        </div>
                        <div class="flex flex-col min-w-0">
                            <span class="font-label-lg text-label-lg text-on-surface font-semibold truncate">{{ $employee->name }}</span>
                            <span class="font-body-sm text-body-sm text-on-surface-variant truncate">{{ $employee->position ?: 'Collaborateur' }} • {{ $employee->departmentLabel() }}</span>
                            <span class="font-label-sm text-[11px] text-tertiary-container mt-0.5 flex items-center gap-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-tertiary-container"></span>
                                {{ $employee->matricule() }} — {{ $employee->contract_type ?: 'Contrat non renseigné' }}
                            </span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-space-sm">
                        <div class="p-space-sm bg-surface-container-low rounded-lg">
                            <span class="font-label-sm text-[11px] text-on-surface-variant block">Heure serveur</span>
                            <span class="font-numeric-md text-numeric-md font-semibold text-on-surface" id="server-timestamp-label">{{ \Carbon\Carbon::createFromTimestamp($serverTimestamp, config('hrm.timezone'))->format('H:i:s') }}</span>
                        </div>
                        <div class="p-space-sm bg-surface-container-low rounded-lg">
                            <span class="font-label-sm text-[11px] text-on-surface-variant block">Dernier badgeage</span>
                            <span class="font-label-md text-label-md text-on-surface font-medium flex items-center gap-1 text-tertiary-container">
                                <span class="material-symbols-outlined text-[16px]">history</span>
                                {{ $todayRecord?->check_out ? 'Départ '.$todayRecord->checkOutShort() : ($todayRecord?->checkInShort() ? 'Arrivée '.$todayRecord->checkInShort() : 'Aucun') }}
                            </span>
                        </div>
                    </div>

                    <p class="font-label-sm text-label-sm text-on-surface-variant flex items-start gap-1.5">
                        <span class="material-symbols-outlined text-[16px] text-primary shrink-0">info</span>
                        <span>Chaque pointage est horodaté côté serveur (Africa/Tunis). Les pointages manquants peuvent être régularisés sur les {{ $regularizationWindow }} derniers jours.</span>
                    </p>
                </div>
            </div>

            <!-- KPI Tiles -->
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-space-md">
                <div class="bg-surface-container-lowest rounded-xl p-space-lg shadow-sm flex flex-col justify-between">
                    <div class="flex items-center justify-between text-on-surface-variant mb-space-xs">
                        <span class="font-label-md text-label-md font-medium">Heures cette semaine</span>
                        <span class="material-symbols-outlined text-primary text-[22px]">calendar_view_week</span>
                    </div>
                    <div class="flex items-baseline gap-space-xs my-space-xs">
                        <span class="font-display-lg text-display-lg text-on-surface font-bold">{{ $minutesLabel($weekWorkedMinutes) }}</span>
                        <span class="font-label-md text-label-md text-on-surface-variant font-normal">/ {{ $minutesLabel($weekExpectedMinutes) }}</span>
                    </div>
                    <div class="flex items-center justify-between mt-space-xs pt-space-xs">
                        <span class="px-2 py-0.5 rounded-full bg-secondary-container text-on-secondary-fixed text-[11px] font-label-sm font-semibold">{{ $weekPercent }}% complété</span>
                        <span class="font-label-sm text-[11px] text-on-surface-variant">Reste {{ $minutesLabel($weekRest) }}</span>
                    </div>
                </div>

                <div class="bg-surface-container-lowest rounded-xl p-space-lg shadow-sm flex flex-col justify-between">
                    <div class="flex items-center justify-between text-on-surface-variant mb-space-xs">
                        <span class="font-label-md text-label-md font-medium">Assiduité mensuelle</span>
                        <span class="material-symbols-outlined text-tertiary-container text-[22px]">verified_user</span>
                    </div>
                    <div class="flex items-baseline gap-space-xs my-space-xs">
                        <span class="font-display-lg text-display-lg text-tertiary-container font-bold">{{ $presenceRate }}%</span>
                        <span class="font-label-md text-label-md text-tertiary-container font-medium">{{ $presenceRate >= 95 ? 'Excellent' : ($presenceRate >= 80 ? 'Correct' : 'À surveiller') }}</span>
                    </div>
                    <div class="flex items-center justify-between mt-space-xs pt-space-xs">
                        <span class="font-label-sm text-label-sm text-on-surface-variant">{{ $lateCount }} retard{{ $lateCount > 1 ? 's' : '' }} constaté{{ $lateCount > 1 ? 's' : '' }}</span>
                        <span class="font-label-sm text-[11px] text-on-surface font-semibold">Moy. arrivée {{ $arrivalLabel }}</span>
                    </div>
                </div>

                <div class="bg-surface-container-lowest rounded-xl p-space-lg shadow-sm flex flex-col justify-between">
                    <div class="flex items-center justify-between text-on-surface-variant mb-space-xs">
                        <span class="font-label-md text-label-md font-medium">Heures supplémentaires</span>
                        <span class="material-symbols-outlined text-primary text-[22px]">more_time</span>
                    </div>
                    <div class="flex items-baseline gap-space-xs my-space-xs">
                        <span class="font-display-lg text-display-lg text-on-surface font-bold">{{ $minutesLabel($overtimeMinutes) }}</span>
                        <span class="font-label-md text-label-md text-primary font-medium">{{ ucfirst($today->locale('fr')->translatedFormat('F Y')) }}</span>
                    </div>
                    <div class="flex items-center justify-between mt-space-xs pt-space-xs">
                        <span class="px-2 py-0.5 rounded-full bg-primary-fixed text-on-primary-fixed-variant text-[11px] font-label-sm font-semibold">
                            Récupération possible
                        </span>
                        <span class="font-label-sm text-[11px] text-on-surface-variant">Heures pointées</span>
                    </div>
                </div>

                <div class="bg-surface-container-lowest rounded-xl p-space-lg shadow-sm flex flex-col justify-between">
                    <div class="flex items-center justify-between text-on-surface-variant mb-space-xs">
                        <span class="font-label-md text-label-md font-medium">Anomalie{{ $anomalies->count() > 1 ? 's' : '' }} à régulariser</span>
                        <span class="material-symbols-outlined {{ $anomalies->count() > 0 ? 'text-error' : 'text-tertiary-container' }} text-[22px]">{{ $anomalies->count() > 0 ? 'warning' : 'task_alt' }}</span>
                    </div>
                    <div class="flex items-baseline gap-space-xs my-space-xs">
                        <span class="font-display-lg text-display-lg {{ $anomalies->count() > 0 ? 'text-error' : 'text-tertiary-container' }} font-bold">{{ $anomalies->count() }}</span>
                        <span class="font-body-md text-body-md text-on-surface-variant font-medium">{{ $anomalies->count() > 0 ? 'Sortie manquante' : 'Aucune anomalie' }}</span>
                    </div>
                    <div class="flex items-center justify-between mt-space-xs pt-space-xs">
                        <span class="font-label-sm text-label-sm text-on-surface-variant">
                            @if ($latestAnomaly)
                                {{ ucfirst($latestAnomaly->work_date->locale('fr')->translatedFormat('D d M')) }}
                            @else
                                Dossiers à jour
                            @endif
                        </span>
                        @if ($anomalies->count() > 0)
                            <button class="px-2.5 py-1 rounded-lg bg-error-container text-on-error-container font-label-sm text-[11px] font-semibold hover:bg-error hover:text-on-error transition-all" data-open-modal="regularize-modal" data-reg-date="{{ $latestAnomaly->work_date->toDateString() }}" data-reg-direction="out" type="button">
                                Régulariser
                            </button>
                        @else
                            <span class="font-label-sm text-[11px] text-tertiary-container font-semibold">Télétravail : {{ $teleworkRemaining }}/{{ $teleworkQuota }}</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Journal -->
            <div class="bg-surface-container-lowest rounded-xl shadow-sm overflow-hidden flex flex-col">
                <div class="p-space-lg bg-surface-container-low flex flex-col sm:flex-row sm:items-center justify-between gap-space-md">
                    <div class="flex items-center gap-space-sm">
                        <div class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center">
                            <span class="material-symbols-outlined text-[20px]">table_chart</span>
                        </div>
                        <div>
                            <h3 class="font-headline-sm text-headline-sm text-on-surface font-semibold">Journal des Pointages</h3>
                            <p class="font-body-sm text-body-sm text-on-surface-variant">{{ $periodLabel }} • {{ count($journal) }} enregistrement(s)</p>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-space-xs">
                        <div class="inline-flex rounded-lg bg-surface-container-high p-1">
                            <a class="px-3 py-1 text-label-sm font-label-sm rounded-md transition-all {{ $period === 'week' ? 'bg-surface-container-lowest text-on-surface shadow-xs font-semibold' : 'text-on-surface-variant hover:text-on-surface font-medium' }}" href="{{ route('collaborator.attendance', ['periode' => 'week']) }}">
                                Semaine
                            </a>
                            <a class="px-3 py-1 text-label-sm font-label-sm rounded-md transition-all {{ $period === 'month' ? 'bg-surface-container-lowest text-on-surface shadow-xs font-semibold' : 'text-on-surface-variant hover:text-on-surface font-medium' }}" href="{{ route('collaborator.attendance', ['periode' => 'month']) }}">
                                Mois
                            </a>
                        </div>
                        <a class="h-8 px-3 rounded-lg bg-surface-container-lowest hover:bg-surface-container text-on-surface font-label-sm text-label-sm font-semibold flex items-center gap-1 shadow-xs transition-colors" href="{{ route('collaborator.attendance.export', ['periode' => $period]) }}">
                            <span class="material-symbols-outlined text-[16px]">download</span>
                            <span>Export CSV</span>
                        </a>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-surface-container-low/60 font-label-md text-label-md text-on-surface-variant uppercase tracking-wider">
                            <tr>
                                <th class="py-space-sm px-space-lg" scope="col">Date &amp; Jour</th>
                                <th class="py-space-sm px-space-md" scope="col">Arrivée</th>
                                <th class="py-space-sm px-space-md" scope="col">Départ</th>
                                <th class="py-space-sm px-space-md" scope="col">Pause</th>
                                <th class="py-space-sm px-space-md" scope="col">Total Effectué</th>
                                <th class="py-space-sm px-space-md" scope="col">Mode</th>
                                <th class="py-space-sm px-space-md" scope="col">Statut</th>
                                <th class="py-space-sm px-space-lg text-right" scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="font-body-md text-body-md text-on-surface divide-y divide-surface-container-low">
                            @forelse ($journal as $row)
                                @php
                                    $delta = $row['deltaMinutes'];
                                @endphp
                                <tr class="hover:bg-surface-container-low/40 transition-colors {{ $row['isToday'] ? 'bg-primary/2' : '' }} {{ $row['isAnomaly'] ? 'bg-error/5' : '' }}">
                                    <td class="py-space-md px-space-lg">
                                        <div class="flex items-center gap-space-xs">
                                            @if ($row['isToday'])
                                                <span class="w-2 h-2 rounded-full bg-tertiary-container animate-pulse"></span>
                                            @endif
                                            <div>
                                                <span class="font-headline-sm text-[14px] text-on-surface font-semibold block">{{ $row['dayLabel'] }}</span>
                                                <span class="font-label-sm text-[11px] {{ $row['isAnomaly'] ? 'text-error font-medium' : ($row['isToday'] ? 'text-primary' : 'text-on-surface-variant') }}">
                                                    {{ $row['isToday'] ? "Aujourd'hui" : 'Semaine '.$row['week'] }}
                                                    @if ($row['isAnomaly']) • {{ 'Anomalie détectée' }} @endif
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-space-md px-space-md">
                                        <span class="font-numeric-md text-numeric-md {{ $row['checkIn'] ? 'font-semibold text-on-surface' : 'text-on-surface-variant' }}">{{ $row['checkIn'] ?? '—' }}</span>
                                    </td>
                                    <td class="py-space-md px-space-md">
                                        @if ($row['live'])
                                            <span class="inline-flex items-center gap-1 text-primary font-label-md text-label-md font-semibold">
                                                <span class="w-1.5 h-1.5 rounded-full bg-primary animate-ping"></span>
                                                En cours
                                            </span>
                                        @elseif ($row['checkOut'])
                                            <span class="font-numeric-md text-numeric-md font-semibold text-on-surface">{{ $row['checkOut'] }}</span>
                                        @else
                                            <span class="inline-flex items-center gap-1 text-error font-numeric-md text-numeric-md font-semibold">
                                                <span class="material-symbols-outlined text-[16px]">error</span>
                                                --:--
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-space-md px-space-md font-numeric-md text-numeric-md text-on-surface-variant">{{ $row['breakLabel'] }}</td>
                                    <td class="py-space-md px-space-md">
                                        <div class="flex items-center gap-1">
                                            <span class="font-numeric-md text-numeric-md font-semibold {{ $row['live'] ? 'text-tertiary-container' : 'text-on-surface' }}">{{ $row['workedLabel'] }}</span>
                                            @if ($delta !== null && $delta !== 0)
                                                <span class="font-label-sm text-[10px] {{ $delta > 0 ? 'text-tertiary-container' : 'text-error' }} font-semibold">
                                                    ({{ $delta > 0 ? '+' : '−' }}{{ abs($delta) }} min)
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="py-space-md px-space-md">
                                        <div class="flex items-center gap-1.5">
                                            <span class="material-symbols-outlined text-[16px] {{ $row['modeColor'] }}">{{ $row['modeIcon'] }}</span>
                                            <span class="font-body-sm text-body-sm">{{ $row['modeLabel'] }}</span>
                                        </div>
                                    </td>
                                    <td class="py-space-md px-space-md">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full {{ $row['statusPillClasses'] }} font-label-sm text-[11px] font-semibold">
                                            <span class="w-1.5 h-1.5 rounded-full bg-current opacity-70"></span>
                                            {{ $row['statusLabel'] }}
                                        </span>
                                    </td>
                                    <td class="py-space-md px-space-lg text-right">
                                        <div class="flex items-center justify-end gap-1">
                                            @if ($row['record']->notes)
                                                <span class="p-1 rounded text-on-surface-variant" title="{{ $row['record']->notes }}">
                                                    <span class="material-symbols-outlined text-[18px]">info</span>
                                                </span>
                                            @endif
                                            @if ($row['isToday'] && $row['live'])
                                                <button class="font-label-sm text-label-sm text-primary hover:text-on-primary-fixed-variant font-semibold" data-open-punch-out type="button">Pointer Sortie</button>
                                            @elseif ($row['isAnomaly'])
                                                <button class="px-3 py-1 rounded-md bg-primary-container text-on-primary font-label-sm text-label-sm font-semibold hover:bg-primary-fill transition-all shadow-xs" data-open-modal="regularize-modal" data-reg-date="{{ $row['date']->toDateString() }}" data-reg-direction="out" type="button">
                                                    Régulariser
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="px-space-md py-space-md" colspan="8">
                                        <x-collaborator.partials.empty-state
                                            icon="event_available"
                                            title="Aucun pointage sur la période"
                                            text="Pointez votre arrivée depuis la badgeuse virtuelle : vos journées de présence apparaîtront ici."
                                        />
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-space-md px-space-lg bg-surface-container-low/50 flex flex-col sm:flex-row items-center justify-between gap-space-sm text-on-surface-variant font-label-sm text-label-sm">
                    <span>{{ count($journal) }} enregistrement(s) • {{ $periodLabel }}</span>
                    <span class="flex flex-wrap items-center gap-3">
                        <span>Moyenne quotidienne : <strong class="text-on-surface">{{ $averageWorkedLabel }}</strong></span>
                        <span>Télétravail ce mois : <strong class="text-on-surface">{{ $teleworkUsed }}/{{ $teleworkQuota }}</strong></span>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Regularization Modal -->
    <div class="fixed inset-0 z-50 bg-black/60 backdrop-blur-xs flex items-center justify-center hidden p-space-md" id="regularize-modal" data-modal>
        <div class="bg-surface-container-lowest rounded-xl shadow-xl w-full max-w-lg overflow-hidden flex flex-col max-h-[90vh] overflow-y-auto">
            <div class="p-space-lg bg-surface-container-low flex items-center justify-between">
                <div class="flex items-center gap-space-xs">
                    <div class="w-8 h-8 rounded-lg bg-error-container text-on-error-container flex items-center justify-center">
                        <span class="material-symbols-outlined text-[20px]">edit_calendar</span>
                    </div>
                    <div>
                        <h4 class="font-headline-sm text-headline-sm text-on-surface font-semibold">Régulariser un Oubli de Pointage</h4>
                        <span class="font-label-sm text-label-sm text-on-surface-variant">Fenêtre de {{ $regularizationWindow }} jours — conforme aux directives RH</span>
                    </div>
                </div>
                <button class="w-8 h-8 rounded-lg text-on-surface-variant hover:bg-surface-container hover:text-on-surface flex items-center justify-center transition-colors" data-close-modal="regularize-modal" type="button">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>
            <form class="p-space-xl flex flex-col gap-space-md" id="regularize-form" method="POST" action="{{ route('collaborator.attendance.regularize') }}">
                @csrf
                <input type="hidden" name="form" value="regularize"/>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-space-md">
                    <div class="flex flex-col gap-space-xs">
                        <label class="font-label-md text-label-md text-on-surface font-medium" for="reg-date">Date concernée *</label>
                        <input class="h-10 px-space-md rounded-lg bg-surface-container-low text-on-surface font-body-md text-body-md focus:outline-none focus:ring-2 focus:ring-primary-container {{ $failedForm === 'regularize' && $errors->has('date') ? 'hrm-invalid' : '' }}" id="reg-date" name="date" type="date" required min="{{ $today->copy()->subDays($regularizationWindow)->toDateString() }}" max="{{ $today->toDateString() }}" value="{{ $failedForm === 'regularize' ? old('date', $regDateDefault) : $regDateDefault }}"/>
                        @if ($failedForm === 'regularize') @error('date')<span class="font-label-sm text-label-sm text-error">{{ $message }}</span>@enderror @endif
                    </div>
                    <div class="flex flex-col gap-space-xs">
                        <label class="font-label-md text-label-md text-on-surface font-medium" for="reg-direction">Type *</label>
                        <select class="h-10 px-space-md rounded-lg bg-surface-container-low text-on-surface font-body-md text-body-md focus:outline-none focus:ring-2 focus:ring-primary-container {{ $failedForm === 'regularize' && $errors->has('direction') ? 'hrm-invalid' : '' }}" id="reg-direction" name="direction" required>
                            <option value="in" @selected(old('direction') === 'in')>Arrivée</option>
                            <option value="out" @selected(old('direction', 'out') === 'out')>Départ</option>
                        </select>
                        @if ($failedForm === 'regularize') @error('direction')<span class="font-label-sm text-label-sm text-error">{{ $message }}</span>@enderror @endif
                    </div>
                    <div class="flex flex-col gap-space-xs">
                        <label class="font-label-md text-label-md text-on-surface font-medium" for="reg-time">Heure effective *</label>
                        <input class="h-10 px-space-md rounded-lg bg-surface-container-low text-on-surface font-numeric-md text-numeric-md focus:outline-none focus:ring-2 focus:ring-primary-container {{ $failedForm === 'regularize' && $errors->has('time') ? 'hrm-invalid' : '' }}" id="reg-time" name="time" type="time" required value="{{ old('time', '17:30') }}"/>
                        @if ($failedForm === 'regularize') @error('time')<span class="font-label-sm text-label-sm text-error">{{ $message }}</span>@enderror @endif
                    </div>
                </div>
                <div class="flex flex-col gap-space-xs">
                    <label class="font-label-md text-label-md text-on-surface font-medium" for="reg-reason">Motif de la régularisation *</label>
                    <select class="h-10 px-space-md rounded-lg bg-surface-container-low text-on-surface font-body-md text-body-md focus:outline-none focus:ring-2 focus:ring-primary-container {{ $failedForm === 'regularize' && $errors->has('reason') ? 'hrm-invalid' : '' }}" id="reg-reason" name="reason" required>
                        @foreach ($motifs as $value => $label)
                            <option value="{{ $value }}" @selected(old('reason') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @if ($failedForm === 'regularize') @error('reason')<span class="font-label-sm text-label-sm text-error">{{ $message }}</span>@enderror @endif
                </div>
                <div class="flex flex-col gap-space-xs">
                    <label class="font-label-md text-label-md text-on-surface font-medium" for="reg-note">Précision explicative RH</label>
                    <textarea class="p-space-sm rounded-lg bg-surface-container-low text-on-surface font-body-sm text-body-sm focus:outline-none focus:ring-2 focus:ring-primary-container resize-none {{ $failedForm === 'regularize' && $errors->has('note') ? 'hrm-invalid' : '' }}" id="reg-note" name="note" maxlength="500" placeholder="Ex : déplacement client jusqu'à 18h, oubli de badgeage en quittant les locaux." rows="3">{{ $failedForm === 'regularize' ? old('note') : '' }}</textarea>
                    @if ($failedForm === 'regularize') @error('note')<span class="font-label-sm text-label-sm text-error">{{ $message }}</span>@enderror @endif
                </div>
                <div class="flex items-center gap-space-sm pt-space-sm">
                    <button class="flex-1 h-10 px-space-md rounded-lg bg-surface-container-high text-on-surface font-label-lg text-label-lg font-medium hover:bg-surface-variant transition-colors" data-close-modal="regularize-modal" type="button">Annuler</button>
                    <button class="flex-1 h-10 px-space-md rounded-lg bg-primary-container text-on-primary font-label-lg text-label-lg font-semibold hover:bg-primary-fill transition-colors flex items-center justify-center gap-1 shadow-sm" type="submit">
                        <span class="material-symbols-outlined text-[18px]">send</span>
                        <span>Soumettre</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Telework Modal -->
    <div class="fixed inset-0 z-50 bg-black/60 backdrop-blur-xs flex items-center justify-center hidden p-space-md" id="telework-modal" data-modal>
        <div class="bg-surface-container-lowest rounded-xl shadow-xl w-full max-w-lg overflow-hidden flex flex-col">
            <div class="p-space-lg bg-surface-container-low flex items-center justify-between">
                <div class="flex items-center gap-space-xs">
                    <div class="w-8 h-8 rounded-lg bg-secondary-container text-on-secondary-fixed flex items-center justify-center">
                        <span class="material-symbols-outlined text-[20px]">home_work</span>
                    </div>
                    <div>
                        <h4 class="font-headline-sm text-headline-sm text-on-surface font-semibold">Déclaration de Télétravail</h4>
                        <span class="font-label-sm text-label-sm text-on-surface-variant">Quota mensuel : {{ $teleworkQuota }} jours</span>
                    </div>
                </div>
                <button class="w-8 h-8 rounded-lg text-on-surface-variant hover:bg-surface-container hover:text-on-surface flex items-center justify-center" data-close-modal="telework-modal" type="button">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>
            <form class="p-space-xl flex flex-col gap-space-md" method="POST" action="{{ route('collaborator.attendance.telework') }}">
                @csrf
                <input type="hidden" name="form" value="telework"/>
                <div class="flex flex-col gap-space-xs">
                    <label class="font-label-md text-label-md text-on-surface font-medium" for="tt-date">Journée de télétravail souhaitée *</label>
                    <input class="h-10 px-space-md rounded-lg bg-surface-container-low text-on-surface font-body-md text-body-md focus:outline-none focus:ring-2 focus:ring-primary-container {{ $failedForm === 'telework' && $errors->has('date') ? 'hrm-invalid' : '' }}" id="tt-date" name="date" type="date" required min="{{ $today->toDateString() }}" max="{{ $today->copy()->addDays(30)->toDateString() }}" value="{{ $failedForm === 'telework' ? old('date', $today->toDateString()) : $today->toDateString() }}"/>
                    @if ($failedForm === 'telework') @error('date')<span class="font-label-sm text-label-sm text-error">{{ $message }}</span>@enderror @endif
                </div>
                <div class="flex flex-col gap-space-xs">
                    <label class="font-label-md text-label-md text-on-surface font-medium" for="tt-note">Note / organisation (optionnel)</label>
                    <textarea class="p-space-sm rounded-lg bg-surface-container-low text-on-surface font-body-sm text-body-sm focus:outline-none focus:ring-2 focus:ring-primary-container resize-none {{ $failedForm === 'telework' && $errors->has('note') ? 'hrm-invalid' : '' }}" id="tt-note" name="note" maxlength="255" placeholder="Ex : rédaction technique, connexion VPN validée." rows="2">{{ $failedForm === 'telework' ? old('note') : '' }}</textarea>
                    @if ($failedForm === 'telework') @error('note')<span class="font-label-sm text-label-sm text-error">{{ $message }}</span>@enderror @endif
                </div>
                <div class="p-space-sm bg-surface-container-low rounded-lg text-on-surface-variant font-body-sm text-body-sm">
                    <p>Quota restant ce mois : <strong class="text-on-surface">{{ $teleworkRemaining }} jour(s)</strong> sur {{ $teleworkQuota }}. La déclaration est refusée si la journée est déjà couverte par un congé approuvé.</p>
                </div>
                <div class="flex items-center gap-space-sm pt-space-sm">
                    <button class="flex-1 h-10 rounded-lg bg-surface-container-high text-on-surface font-label-lg text-label-lg font-medium" data-close-modal="telework-modal" type="button">Annuler</button>
                    <button class="flex-1 h-10 rounded-lg bg-primary-container text-on-primary font-label-lg text-label-lg font-semibold hover:bg-primary-fill" type="submit">Confirmer</button>
                </div>
            </form>
        </div>
    </div>

    @include('collaborator.partials.punch-out-dialog')

    <script>
        (() => {
            // Server-seeded Tunis clock.
            const clock = document.getElementById('live-clock-time');
            const serverTs = parseInt(clock?.dataset.serverTimestamp || '0', 10);
            const offsetMs = serverTs > 0 ? serverTs * 1000 - Date.now() : 0;

            const tick = () => {
                if (!clock) return;
                const now = new Date(Date.now() + offsetMs);
                const parts = [now.getHours(), now.getMinutes(), now.getSeconds()].map((value) => String(value).padStart(2, '0'));
                clock.textContent = parts.join(':');
                const label = document.getElementById('server-timestamp-label');
                if (label) label.textContent = parts.join(':');
            };

            tick();
            setInterval(tick, 1000);

            const openModal = (id) => document.getElementById(id)?.classList.remove('hidden');
            const closeModal = (id) => document.getElementById(id)?.classList.add('hidden');

            @if ($errors->any() && in_array($failedForm, ['regularize', 'telework'], true))
                openModal(@json($failedForm === 'regularize' ? 'regularize-modal' : 'telework-modal'));
            @endif

            document.querySelectorAll('[data-open-modal]').forEach((trigger) => {
                trigger.addEventListener('click', () => {
                    const modal = trigger.dataset.openModal;
                    const dateInput = document.getElementById('reg-date');
                    if (modal === 'regularize-modal') {
                        if (trigger.dataset.regDate && dateInput) dateInput.value = trigger.dataset.regDate;
                        const direction = document.getElementById('reg-direction');
                        if (trigger.dataset.regDirection && direction) direction.value = trigger.dataset.regDirection;
                    }
                    openModal(modal);
                });
            });

            document.querySelectorAll('[data-close-modal]').forEach((trigger) => {
                trigger.addEventListener('click', () => closeModal(trigger.dataset.closeModal));
            });

            document.querySelectorAll('[data-modal]').forEach((modal) => {
                modal.addEventListener('click', (event) => {
                    if (event.target === modal) modal.classList.add('hidden');
                });
            });

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape') {
                    document.querySelectorAll('[data-modal]').forEach((modal) => modal.classList.add('hidden'));
                }
            });

            document.querySelectorAll('[data-modal] form').forEach((form) => {
                form.addEventListener('submit', () => {
                    const button = form.querySelector('button[type="submit"]');
                    if (button) {
                        button.disabled = true;
                        button.classList.add('opacity-60', 'pointer-events-none');
                    }
                });
            });
        })();
    </script>
</x-collaborator-shell>
