@php
    $stateLabels = [
        'not_started' => ['label' => 'Non pointé', 'classes' => 'bg-surface-container-high text-on-surface-variant', 'dot' => 'bg-outline'],
        'working' => ['label' => 'En poste', 'classes' => 'bg-tertiary-fixed text-on-tertiary-fixed', 'dot' => 'bg-tertiary-container'],
        'on_break' => ['label' => 'En pause', 'classes' => 'bg-secondary-container text-on-secondary-fixed', 'dot' => 'bg-secondary'],
        'done' => ['label' => 'Journée terminée', 'classes' => 'bg-surface-container-high text-on-surface-variant', 'dot' => 'bg-outline'],
        'leave' => ['label' => 'En congé', 'classes' => 'bg-secondary-container text-on-secondary-fixed', 'dot' => 'bg-secondary'],
    ];
    $state = $stateLabels[$punchState] ?? $stateLabels['not_started'];

    $punchLabel = match ($punchState) {
        'working', 'on_break' => 'Session active',
        'done' => 'Session clôturée',
        'leave' => 'Journée de congé',
        default => 'Aucune session',
    };
@endphp

<x-collaborator-shell active="dashboard" title="Mon Tableau de bord">

    <div class="flex flex-col w-full">
        <div class="px-4 sm:px-margin py-space-xl flex flex-col gap-space-xl max-w-[1536px] w-full mx-auto">

            <!-- Top Identity & Punch Status Bar -->
            <div class="relative overflow-hidden rounded-xl bg-surface-container-lowest p-space-xl shadow-sm flex flex-col lg:flex-row lg:items-center justify-between gap-space-lg">
                <div class="absolute -right-16 -top-16 w-64 h-64 rounded-full bg-primary-fixed/30 blur-3xl pointer-events-none"></div>
                <div class="flex flex-col gap-space-xs z-10">
                    <div class="flex flex-wrap items-center gap-space-sm text-on-surface-variant font-label-md text-label-md">
                        <span class="flex items-center gap-1 font-semibold text-primary">
                            <span class="material-symbols-outlined text-[16px]">calendar_today</span>
                            {{ ucfirst($now->locale('fr')->translatedFormat('l j F Y')) }}
                        </span>
                        <span class="w-1 h-1 rounded-full bg-outline-variant"></span>
                        <span>{{ $employee->departmentLabel() ?: 'Département non renseigné' }}</span>
                        <span class="w-1 h-1 rounded-full bg-outline-variant"></span>
                        <span>Site Lac 2, Tunis</span>
                    </div>
                    <div class="flex flex-wrap items-center gap-space-sm">
                        <h1 class="font-display-lg text-display-lg text-on-surface tracking-tight">Bonjour {{ $firstName }}</h1>
                        <span class="inline-flex items-center gap-1.5 px-space-sm py-0.5 rounded-full font-label-sm text-label-sm font-semibold {{ $state['classes'] }}">
                            <span class="w-2 h-2 rounded-full {{ $state['dot'] }} {{ in_array($punchState, ['working', 'on_break'], true) ? 'animate-pulse' : '' }}"></span>
                            {{ $state['label'] }}
                        </span>
                    </div>
                    <p class="font-body-md text-body-md text-on-surface-variant">
                        @if ($todayRecord?->check_in)
                            Badge validé à <span class="font-numeric-md text-numeric-md font-semibold text-on-surface">{{ $todayRecord->checkInShort() }}</span>
                            • {{ $todayRecord->modeLabel() }}
                            @if ($todayRecord->check_out)
                                • Sortie à <span class="font-numeric-md text-numeric-md font-semibold text-on-surface">{{ $todayRecord->checkOutShort() }}</span>
                            @endif
                        @elseif ($leaveToday)
                            Journée couverte par un congé approuvé : <span class="font-semibold text-on-surface">{{ $leaveToday->typeLabel() }}</span>
                        @else
                            Aucun pointage enregistré aujourd'hui • Cliquez sur « Pointer Arrivée » pour démarrer votre journée.
                        @endif
                    </p>
                </div>

                <!-- Quick Punch Control Box -->
                <div class="z-10 flex flex-wrap sm:flex-nowrap items-center gap-space-sm shrink-0 bg-surface-container-low p-space-sm rounded-xl">
                    <div class="flex items-center gap-space-sm px-space-md py-space-xs">
                        <div class="w-10 h-10 rounded-xl bg-primary-container text-on-primary flex items-center justify-center shadow-sm">
                            <span class="material-symbols-outlined text-[22px]">timer</span>
                        </div>
                        <div class="flex flex-col">
                            <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">{{ $punchLabel }}</span>
                            <span class="font-numeric-md text-numeric-md font-semibold text-on-surface" id="sessionTimer" data-seconds="{{ $elapsedSeconds }}" data-running="{{ $punchState === 'working' ? '1' : '0' }}">--h --m --s</span>
                        </div>
                    </div>
                    <div class="h-8 w-px bg-surface-container-high hidden sm:block"></div>
                    <div class="flex items-center gap-space-xs w-full sm:w-auto">
                        @if ($punchState === 'working' || $punchState === 'on_break')
                            @if ($punchState === 'on_break')
                                <form method="POST" action="{{ route('collaborator.attendance.pause-end') }}">
                                    @csrf
                                    <button class="h-10 px-space-md rounded-xl bg-tertiary-fixed text-on-tertiary-fixed font-label-lg text-label-lg flex items-center justify-center gap-space-xs shadow-sm transition-all active:scale-95" type="submit">
                                        <span class="material-symbols-outlined text-[18px]">play_arrow</span>
                                        <span>Reprendre</span>
                                    </button>
                                </form>
                            @endif
                            <form method="POST" action="{{ route('collaborator.attendance.punch-out') }}" onsubmit="return confirm('Confirmer l\'enregistrement de votre départ pour la journée ?');">
                                @csrf
                                <button class="h-10 px-space-md rounded-xl bg-surface-container-lowest text-on-surface hover:bg-surface-variant font-label-lg text-label-lg flex items-center justify-center gap-space-xs shadow-sm transition-all active:scale-95" type="submit">
                                    <span class="material-symbols-outlined text-[18px] text-error">logout</span>
                                    <span>Pointer Départ</span>
                                </button>
                            </form>
                        @elseif ($punchState === 'not_started')
                            <form method="POST" action="{{ route('collaborator.attendance.punch-in') }}">
                                @csrf
                                <button class="h-10 px-space-md rounded-xl bg-primary-container text-on-primary hover:bg-primary-fill font-label-lg text-label-lg flex items-center justify-center gap-space-xs shadow-sm transition-all active:scale-95" type="submit">
                                    <span class="material-symbols-outlined text-[18px]">login</span>
                                    <span>Pointer Arrivée</span>
                                </button>
                            </form>
                        @else
                            <a class="h-10 px-space-md rounded-xl bg-surface-container-lowest text-on-surface hover:bg-surface-variant font-label-lg text-label-lg flex items-center justify-center gap-space-xs shadow-sm transition-all" href="{{ route('collaborator.attendance') }}">
                                <span class="material-symbols-outlined text-[18px]">{{ $punchState === 'leave' ? 'beach_access' : 'history' }}</span>
                                <span>Mon pointage</span>
                            </a>
                        @endif
                        <a class="h-10 px-space-md rounded-xl bg-surface-container-high text-on-secondary-container hover:bg-surface-variant font-label-lg text-label-lg flex items-center justify-center gap-space-xs transition-all" href="{{ route('collaborator.attendance') }}#teletravail">
                            <span class="material-symbols-outlined text-[18px]">home_work</span>
                            <span>Déclarer TT</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- 4 High-Density KPI Metric Tiles -->
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-space-lg">
                <!-- KPI 1: Solde Congés -->
                <a class="p-space-lg rounded-xl bg-surface-container-lowest shadow-sm flex flex-col justify-between gap-space-md hover:shadow-md transition-shadow" href="{{ route('collaborator.conges') }}">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-space-xs">
                            <div class="w-8 h-8 rounded-lg bg-surface-container-high text-primary flex items-center justify-center">
                                <span class="material-symbols-outlined text-[18px]">beach_access</span>
                            </div>
                            <span class="font-label-md text-label-md text-on-surface-variant font-medium">Solde Congés Payés</span>
                        </div>
                        <span class="px-2 py-0.5 rounded-full bg-surface-container-high text-on-surface-variant font-label-sm text-[10px]">Loi TN</span>
                    </div>
                    <div class="flex items-baseline gap-space-xs">
                        <span class="font-display-lg text-display-lg text-on-surface tracking-tight">{{ $leaveRemaining }}.0</span>
                        <span class="font-label-lg text-label-lg text-on-surface-variant">Jours restants</span>
                    </div>
                    <div class="flex flex-col gap-1.5 pt-space-xs">
                        <div class="w-full bg-surface-container h-1.5 rounded-full overflow-hidden flex">
                            <div class="bg-primary-container h-full rounded-full" style="width: {{ $leaveRatio }}%"></div>
                        </div>
                        <div class="flex items-center justify-between text-on-surface-variant font-label-sm text-[11px]">
                            <span>Acquis: {{ $leaveAcquired }}.0j (Pris: {{ $leaveTaken }}.0j)</span>
                            <span class="text-primary font-medium">
                                @if ($nextLeave)
                                    Prochain: {{ $nextLeave->start_date?->format('d/m') }}
                                @else
                                    Aucun prévu
                                @endif
                            </span>
                        </div>
                    </div>
                </a>

                <!-- KPI 2: Salaire Net -->
                <a class="p-space-lg rounded-xl bg-surface-container-lowest shadow-sm flex flex-col justify-between gap-space-md hover:shadow-md transition-shadow" href="{{ route('collaborator.payslips') }}{{ $latestPayslip ? '?bulletin='.$latestPayslip->id : '' }}">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-space-xs">
                            <div class="w-8 h-8 rounded-lg bg-secondary-container text-on-secondary-fixed flex items-center justify-center">
                                <span class="material-symbols-outlined text-[18px]">account_balance_wallet</span>
                            </div>
                            <span class="font-label-md text-label-md text-on-surface-variant font-medium">Dernier Salaire Net</span>
                        </div>
                        @if ($latestPayslip)
                            <span class="px-2 py-0.5 rounded-full {{ $latestPayslip->statusPillClasses() }} font-label-sm text-[10px] font-semibold">{{ $latestPayslip->statusLabel() }}</span>
                        @endif
                    </div>
                    <div class="flex items-baseline gap-space-xs">
                        <span class="font-display-lg text-display-lg text-on-surface font-numeric-md tracking-tight">{{ $latestPayslip?->netLabel() ?? '—' }}</span>
                        <span class="font-label-lg text-label-lg text-on-surface-variant">TND</span>
                    </div>
                    <div class="flex items-center justify-between pt-space-xs font-label-sm text-[11px] text-on-surface-variant">
                        <span class="flex items-center gap-1 min-w-0">
                            <span class="material-symbols-outlined text-[14px] text-secondary">account_balance</span>
                            <span class="truncate">{{ $latestPayslip?->bank ?: 'Banque non renseignée' }}{{ $latestPayslip?->rib ? ' (RIB ...'.substr($latestPayslip->rib, -4).')' : '' }}</span>
                        </span>
                        <span class="text-primary font-semibold flex items-center gap-0.5 shrink-0">
                            <span>{{ $latestPayslip?->periodLabel() ?? 'Aucun bulletin' }}</span>
                            <span class="material-symbols-outlined text-[13px]">arrow_forward</span>
                        </span>
                    </div>
                </a>

                <!-- KPI 3: Assiduité -->
                <a class="p-space-lg rounded-xl bg-surface-container-lowest shadow-sm flex flex-col justify-between gap-space-md hover:shadow-md transition-shadow" href="{{ route('collaborator.attendance') }}">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-space-xs">
                            <div class="w-8 h-8 rounded-lg bg-surface-container-high text-tertiary-container flex items-center justify-center">
                                <span class="material-symbols-outlined text-[18px]">rule</span>
                            </div>
                            <span class="font-label-md text-label-md text-on-surface-variant font-medium">Assiduité (30 jours)</span>
                        </div>
                        <span class="px-2 py-0.5 rounded-full bg-tertiary-fixed text-on-tertiary-fixed-variant font-label-sm text-[10px] font-semibold">{{ $presenceRate >= 95 ? 'Excellent' : ($presenceRate >= 80 ? 'Correct' : 'À surveiller') }}</span>
                    </div>
                    <div class="flex items-baseline gap-space-xs">
                        <span class="font-display-lg text-display-lg text-on-surface tracking-tight">{{ $presenceRate }}%</span>
                        <span class="font-label-lg text-label-lg text-on-surface-variant">Présence validée</span>
                    </div>
                    <div class="flex items-center justify-between pt-space-xs font-label-sm text-[11px] text-on-surface-variant">
                        <span class="flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full {{ $lateCount === 0 ? 'bg-tertiary-container' : 'bg-error' }}"></span>
                            {{ $lateCount }} retard{{ $lateCount > 1 ? 's' : '' }} ce mois
                        </span>
                        <span class="font-numeric-md text-on-surface font-semibold bg-surface-container-high px-1.5 py-0.5 rounded">
                            {{ $overtimeHours > 0 ? number_format($overtimeHours, 1, ',', ' ').'h sup.' : '0h sup.' }}
                        </span>
                    </div>
                </a>

                <!-- KPI 4: Demandes en cours -->
                <a class="p-space-lg rounded-xl bg-surface-container-lowest shadow-sm flex flex-col justify-between gap-space-md hover:shadow-md transition-shadow" href="{{ route('collaborator.conges') }}">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-space-xs">
                            <div class="w-8 h-8 rounded-lg bg-surface-container-high text-primary flex items-center justify-center">
                                <span class="material-symbols-outlined text-[18px]">pending_actions</span>
                            </div>
                            <span class="font-label-md text-label-md text-on-surface-variant font-medium">Demandes en attente</span>
                        </div>
                        <span class="px-2 py-0.5 rounded-full bg-secondary-container text-on-secondary-container font-label-sm text-[10px] font-semibold">{{ $pendingCount }} active{{ $pendingCount > 1 ? 's' : '' }}</span>
                    </div>
                    <div class="flex items-baseline gap-space-xs">
                        <span class="font-display-lg text-display-lg text-on-surface tracking-tight">{{ str_pad((string) $pendingCount, 2, '0', STR_PAD_LEFT) }}</span>
                        <span class="font-label-lg text-label-lg text-on-surface-variant">En revue RH</span>
                    </div>
                    <div class="flex items-center justify-between pt-space-xs font-label-sm text-[11px] text-on-surface-variant">
                        <span class="truncate max-w-[180px]">{{ $activity[0]['title'] ?? 'Aucune demande déposée' }}</span>
                        <span class="text-secondary font-medium">{{ $activity[0]['status'] ?? 'À jour' }}</span>
                    </div>
                </a>
            </div>

            <!-- Main Content Layout -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-space-xl">
                <!-- Left Column (Wide) -->
                <div class="lg:col-span-8 flex flex-col gap-space-xl">
                    <!-- Section: Mes Demandes Récentes -->
                    <div class="p-space-xl rounded-xl bg-surface-container-lowest shadow-sm flex flex-col gap-space-lg">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-space-xs">
                            <div class="flex items-center gap-space-sm">
                                <div class="w-8 h-8 rounded-lg bg-surface-container-high text-primary flex items-center justify-center">
                                    <span class="material-symbols-outlined text-[18px]">assignment_turned_in</span>
                                </div>
                                <div class="flex flex-col">
                                    <h2 class="font-headline-sm text-headline-sm text-on-surface">Mes Demandes Récentes &amp; Suivi RH</h2>
                                    <span class="font-label-sm text-label-sm text-on-surface-variant">Statut en direct de vos requêtes administratives</span>
                                </div>
                            </div>
                            <a class="self-start sm:self-auto text-primary hover:text-on-primary-fixed-variant font-label-md text-label-md font-semibold flex items-center gap-1 transition-colors" href="{{ route('collaborator.conges') }}">
                                <span>Voir tout l'historique</span>
                                <span class="material-symbols-outlined text-[16px]">chevron_right</span>
                            </a>
                        </div>

                        <div class="flex flex-col gap-space-sm">
                            @forelse ($activity as $item)
                                <a class="p-space-md rounded-xl bg-surface-container-low hover:bg-surface-container transition-colors flex flex-col sm:flex-row sm:items-center justify-between gap-space-md" href="{{ $item['url'] }}">
                                    <div class="flex items-start gap-space-md">
                                        <div class="w-10 h-10 rounded-xl bg-surface-container-lowest text-primary flex items-center justify-center shrink-0 shadow-sm mt-0.5">
                                            <span class="material-symbols-outlined text-[20px]">{{ $item['icon'] }}</span>
                                        </div>
                                        <div class="flex flex-col">
                                            <div class="flex flex-wrap items-center gap-space-xs">
                                                <span class="font-label-lg text-label-lg font-semibold text-on-surface">{{ $item['title'] }}</span>
                                            </div>
                                            <span class="font-body-sm text-body-sm text-on-surface-variant mt-0.5">{{ $item['subtitle'] }}</span>
                                            <div class="flex items-center gap-space-xs mt-1 text-on-surface-variant font-label-sm text-[11px]">
                                                <span class="material-symbols-outlined text-[13px]">schedule</span>
                                                <span>{{ $item['meta'] }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex sm:flex-col items-center sm:items-end justify-between sm:justify-center gap-space-xs shrink-0">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full font-label-sm text-label-sm font-medium {{ $item['statusClasses'] }}">
                                            {{ $item['status'] }}
                                        </span>
                                        <span class="font-label-sm text-label-sm text-on-surface-variant flex items-center gap-0.5">
                                            Consulter
                                            <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                                        </span>
                                    </div>
                                </a>
                            @empty
                                <x-collaborator.partials.empty-state
                                    icon="assignment"
                                    title="Aucune demande déposée"
                                    text="Vos demandes de congé, documents et acomptes apparaîtront ici avec leur statut."
                                />
                            @endforelse
                        </div>
                    </div>

                    <!-- Section: Planning de mon équipe -->
                    <div class="p-space-xl rounded-xl bg-surface-container-lowest shadow-sm flex flex-col gap-space-lg">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-space-sm">
                            <div class="flex items-center gap-space-sm">
                                <div class="w-8 h-8 rounded-lg bg-surface-container-high text-primary flex items-center justify-center">
                                    <span class="material-symbols-outlined text-[18px]">group</span>
                                </div>
                                <div>
                                    <h2 class="font-headline-sm text-headline-sm text-on-surface">Planning de mon équipe ({{ $employee->departmentLabel() }})</h2>
                                    <span class="font-label-sm text-label-sm text-on-surface-variant">Visibilité des disponibilités et modes de travail cette semaine</span>
                                </div>
                            </div>
                            <div class="flex flex-wrap items-center gap-space-xs text-on-surface-variant font-label-sm text-[11px]">
                                <span class="inline-flex items-center gap-1">
                                    <span class="w-2 h-2 rounded-full bg-primary-container"></span> Bureau Lac 2
                                </span>
                                <span class="inline-flex items-center gap-1 ml-2">
                                    <span class="w-2 h-2 rounded-full bg-secondary-container"></span> Télétravail (TT)
                                </span>
                                <span class="inline-flex items-center gap-1 ml-2">
                                    <span class="w-2 h-2 rounded-full bg-outline-variant"></span> Congé / Absence
                                </span>
                            </div>
                        </div>

                        <div class="overflow-x-auto -mx-space-xl px-space-xl">
                            <div class="min-w-[620px] flex flex-col gap-space-xs">
                                <div class="grid grid-cols-6 gap-space-xs text-center py-space-xs font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">
                                    <div class="text-left pl-space-sm">Collaborateur</div>
                                    @foreach ($team['days'] as $day)
                                        <div class="p-1 rounded {{ $day['isToday'] ? 'bg-surface-container-high text-on-surface font-semibold' : '' }}">{{ $day['label'] }}</div>
                                    @endforeach
                                </div>

                                @forelse ($team['rows'] as $row)
                                    <div class="grid grid-cols-6 items-center gap-space-xs p-space-sm rounded-xl {{ $row['isSelf'] ? 'bg-surface-container-low' : 'hover:bg-surface-container-low transition-colors' }}">
                                        <div class="flex items-center gap-space-xs min-w-0">
                                            <div class="w-7 h-7 rounded-full {{ $row['isSelf'] ? 'bg-primary-fill text-on-primary' : 'bg-surface-container-high text-on-surface' }} font-label-sm text-[11px] flex items-center justify-center font-bold shrink-0">{{ $row['employee']->initials() }}</div>
                                            <div class="flex flex-col truncate">
                                                <span class="font-label-md text-label-md {{ $row['isSelf'] ? 'font-semibold' : 'font-medium' }} text-on-surface truncate">{{ $row['employee']->name }}{{ $row['isSelf'] ? ' (Moi)' : '' }}</span>
                                                <span class="font-label-sm text-[10px] text-on-surface-variant truncate">{{ $row['employee']->position ?: 'Collaborateur' }}</span>
                                            </div>
                                        </div>
                                        @foreach ($row['cells'] as $cell)
                                            <div class="py-1.5 px-2 rounded-lg {{ $cell['classes'] }} text-center font-label-sm text-[11px] truncate">{{ $cell['label'] }}</div>
                                        @endforeach
                                    </div>
                                @empty
                                    <x-collaborator.partials.empty-state icon="group" title="Aucun collaborateur" text="Le planning s'affichera dès que votre équipe sera renseignée." />
                                @endforelse
                            </div>
                        </div>

                        <div class="p-space-sm rounded-xl bg-surface-container-low flex flex-col sm:flex-row sm:items-center justify-between gap-space-xs text-on-surface-variant font-label-sm text-[12px]">
                            <span class="flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-[16px] text-primary">info</span>
                                Le planning est calculé à partir des congés approuvés et des pointages enregistrés.
                            </span>
                            <a class="text-primary hover:underline font-semibold shrink-0" href="{{ route('collaborator.attendance') }}#teletravail">Déclarer un jour de TT</a>
                        </div>
                    </div>
                </div>

                <!-- Right Column (Sidebar Widgets) -->
                <div class="lg:col-span-4 flex flex-col gap-space-xl">
                    <!-- Widget: Bulletin de Paie -->
                    <div class="p-space-xl rounded-xl bg-surface-container-lowest shadow-sm flex flex-col gap-space-lg relative overflow-hidden">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-space-xs">
                                <div class="w-8 h-8 rounded-lg bg-primary-fill text-on-primary flex items-center justify-center">
                                    <span class="material-symbols-outlined text-[18px]">receipt</span>
                                </div>
                                <h2 class="font-headline-sm text-headline-sm text-on-surface">Bulletin de Paie</h2>
                            </div>
                            @if ($latestPayslip)
                                <span class="px-2 py-0.5 rounded-full bg-secondary-container text-on-secondary-fixed font-label-sm text-[11px] font-semibold">{{ $latestPayslip->periodLabel() }}</span>
                            @endif
                        </div>

                        @if ($latestPayslip)
                            <div class="p-space-md rounded-xl bg-surface-container-low flex flex-col gap-space-sm">
                                <div class="flex items-center justify-between pb-space-xs border-b border-surface-container-high">
                                    <span class="font-body-sm text-body-sm text-on-surface-variant">Salaire de Base Brut</span>
                                    <span class="font-numeric-md text-numeric-md font-semibold text-on-surface">{{ number_format((float) $latestPayslip->base_salary, 3, ',', ' ') }} TND</span>
                                </div>
                                <div class="flex items-center justify-between text-on-surface-variant font-body-sm text-body-sm">
                                    <span class="flex items-center gap-1">
                                        <span>Retenue CNSS</span>
                                        <span class="material-symbols-outlined text-[14px]" title="Cotisation obligatoire sécurité sociale">help</span>
                                    </span>
                                    <span class="font-numeric-md text-numeric-md text-error">- {{ number_format((float) $latestPayslip->cnss, 3, ',', ' ') }} TND</span>
                                </div>
                                <div class="flex items-center justify-between text-on-surface-variant font-body-sm text-body-sm">
                                    <span>Retenue IRPP &amp; CSS</span>
                                    <span class="font-numeric-md text-numeric-md text-error">- {{ number_format((float) $latestPayslip->irpp + (float) $latestPayslip->css, 3, ',', ' ') }} TND</span>
                                </div>
                                <div class="flex items-center justify-between pt-space-xs border-t border-surface-container-high">
                                    <span class="font-label-lg text-label-lg font-bold text-on-surface">Net à payer en compte</span>
                                    <span class="font-numeric-md text-headline-sm font-bold text-tertiary-container">{{ $latestPayslip->netLabel() }} TND</span>
                                </div>
                            </div>
                            <div class="flex flex-col gap-space-xs">
                                <a class="w-full h-10 rounded-xl bg-primary-container text-on-primary font-label-lg text-label-lg flex items-center justify-center gap-space-xs hover:bg-primary-fill shadow-sm transition-all" href="{{ route('collaborator.payslips.download', $latestPayslip) }}">
                                    <span class="material-symbols-outlined text-[18px]">download</span>
                                    <span>Télécharger Bulletin PDF Certifié</span>
                                </a>
                                <div class="flex items-center justify-center gap-1 text-on-surface-variant font-label-sm text-[11px] pt-1">
                                    <span class="material-symbols-outlined text-[14px] text-tertiary-container">lock</span>
                                    <span>Horodatage légal INPDP et code QR d'intégrité</span>
                                </div>
                            </div>
                        @else
                            <x-collaborator.partials.empty-state icon="receipt_long" title="Aucun bulletin disponible" text="Votre premier bulletin apparaîtra ici dès qu'il sera généré par la paie." />
                        @endif
                    </div>

                    <!-- Widget: Actions Rapides -->
                    <div class="p-space-xl rounded-xl bg-surface-container-lowest shadow-sm flex flex-col gap-space-md">
                        <div class="flex items-center gap-space-xs">
                            <div class="w-8 h-8 rounded-lg bg-surface-container-high text-primary flex items-center justify-center">
                                <span class="material-symbols-outlined text-[18px]">bolt</span>
                            </div>
                            <h2 class="font-headline-sm text-headline-sm text-on-surface">Actions Rapides Collaborateur</h2>
                        </div>
                        <div class="flex flex-col gap-space-xs">
                            <a class="p-space-sm rounded-xl bg-surface-container-low hover:bg-surface-container flex items-center justify-between text-on-surface transition-all group" href="{{ route('collaborator.payslips') }}#acompte-section">
                                <div class="flex items-center gap-space-sm">
                                    <span class="w-8 h-8 rounded-lg bg-surface-container-lowest text-primary flex items-center justify-center shadow-sm group-hover:scale-105 transition-transform">
                                        <span class="material-symbols-outlined text-[18px]">request_quote</span>
                                    </span>
                                    <div class="flex flex-col">
                                        <span class="font-label-md text-label-md font-semibold">Demander un acompte sur salaire</span>
                                        <span class="font-label-sm text-[11px] text-on-surface-variant">Plafond : {{ (int) round((float) config('hrm.advance_max_ratio') * 100) }}% du net</span>
                                    </div>
                                </div>
                                <span class="material-symbols-outlined text-[18px] text-on-surface-variant group-hover:translate-x-0.5 transition-transform">chevron_right</span>
                            </a>
                            <a class="p-space-sm rounded-xl bg-surface-container-low hover:bg-surface-container flex items-center justify-between text-on-surface transition-all group" href="{{ route('collaborator.conges') }}#nouvelle-demande">
                                <div class="flex items-center gap-space-sm">
                                    <span class="w-8 h-8 rounded-lg bg-surface-container-lowest text-tertiary-container flex items-center justify-center shadow-sm group-hover:scale-105 transition-transform">
                                        <span class="material-symbols-outlined text-[18px]">medical_services</span>
                                    </span>
                                    <div class="flex flex-col">
                                        <span class="font-label-md text-label-md font-semibold">Déposer un arrêt maladie / certificat</span>
                                        <span class="font-label-sm text-[11px] text-on-surface-variant">Délai réglementaire : 48h ouvrées</span>
                                    </div>
                                </div>
                                <span class="material-symbols-outlined text-[18px] text-on-surface-variant group-hover:translate-x-0.5 transition-transform">chevron_right</span>
                            </a>
                            <a class="p-space-sm rounded-xl bg-surface-container-low hover:bg-surface-container flex items-center justify-between text-on-surface transition-all group" href="{{ route('collaborator.documents') }}#demande-document">
                                <div class="flex items-center gap-space-sm">
                                    <span class="w-8 h-8 rounded-lg bg-surface-container-lowest text-primary flex items-center justify-center shadow-sm group-hover:scale-105 transition-transform">
                                        <span class="material-symbols-outlined text-[18px]">description</span>
                                    </span>
                                    <div class="flex flex-col">
                                        <span class="font-label-md text-label-md font-semibold">Attestation de travail / CNSS</span>
                                        <span class="font-label-sm text-[11px] text-on-surface-variant">Génération instantanée certifiée</span>
                                    </div>
                                </div>
                                <span class="material-symbols-outlined text-[18px] text-on-surface-variant group-hover:translate-x-0.5 transition-transform">chevron_right</span>
                            </a>
                        </div>
                    </div>

                    <!-- Widget: Rappels RH -->
                    <div class="p-space-xl rounded-xl bg-surface-container-lowest shadow-sm flex flex-col gap-space-md">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-space-xs">
                                <div class="w-8 h-8 rounded-lg bg-surface-container-high text-primary flex items-center justify-center">
                                    <span class="material-symbols-outlined text-[18px]">campaign</span>
                                </div>
                                <h2 class="font-headline-sm text-headline-sm text-on-surface">Rappels RH &amp; Échéances</h2>
                            </div>
                            <span class="w-2 h-2 rounded-full bg-tertiary-container" title="Données à jour"></span>
                        </div>
                        <div class="flex flex-col gap-space-sm">
                            @foreach ($reminders as $reminder)
                                <a class="p-space-md rounded-xl bg-surface-container-low hover:bg-surface-container transition-colors flex flex-col gap-1" href="{{ $reminder['url'] }}">
                                    <div class="flex items-center justify-between">
                                        <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full {{ $reminder['tone'] }} font-label-sm text-[10px] font-semibold uppercase">
                                            <span class="material-symbols-outlined text-[13px]">{{ $reminder['icon'] }}</span>
                                            {{ $reminder['label'] }}
                                        </span>
                                    </div>
                                    <span class="font-label-lg text-label-lg font-bold text-on-surface">{{ $reminder['title'] }}</span>
                                    <p class="font-body-sm text-body-sm text-on-surface-variant">{{ $reminder['body'] }}</p>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<script>
    (() => {
        const timerEl = document.getElementById('sessionTimer');
        if (!timerEl) return;

        let seconds = parseInt(timerEl.dataset.seconds || '0', 10);
        const running = timerEl.dataset.running === '1';

        const render = () => {
            const h = Math.floor(seconds / 3600);
            const m = Math.floor((seconds % 3600) / 60);
            const s = seconds % 60;
            timerEl.textContent = `${String(h).padStart(2, '0')}h ${String(m).padStart(2, '0')}m ${String(s).padStart(2, '0')}s`;
        };

        render();

        if (running) {
            setInterval(() => {
                seconds++;
                render();
            }, 1000);
        }
    })();
</script>
</x-collaborator-shell>
