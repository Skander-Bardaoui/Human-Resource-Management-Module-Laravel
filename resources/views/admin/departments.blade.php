<x-admin-shell active="departments" title="Gestion des Départements">
@php
    $hasFilters = $filters['q'] !== '' || $filters['sort'] !== 'headcount' || $filters['direction'] !== 'desc';

    $departmentHint = match (true) {
        $totals['departmentCount'] === 0 => 'Aucun département enregistré',
        $totals['managerCount'] === $totals['departmentCount'] => 'Tous les départements ont un responsable',
        default => $totals['managerCount'].' / '.$totals['departmentCount'].' départements avec un responsable',
    };

    $headcountHint = $totals['unassigned'] === 0
        ? 'Tous les collaborateurs actifs sont affectés'
        : $totals['unassigned'].($totals['unassigned'] === 1 ? ' collaborateur non affecté' : ' collaborateurs non affectés');

    $payrollHint = $totals['payslipCount'] === 0
        ? 'Aucune fiche de paie pour '.$periodLabel
        : ($totals['payslipCount'] === 1 ? '1 fiche de paie' : $totals['payslipCount'].' fiches de paie').' · '.$periodLabel;

    $budgetHint = $totals['definedBudgets'] === 0
        ? 'Aucun budget défini'
        : $totals['conforming'].' / '.$totals['definedBudgets'].' budgets respectés · '.$totals['conformityRate'].' %';
@endphp
<div class="flex w-full flex-col space-y-8 pb-16 pt-6">
    <x-ui.toast />

    {{-- En-tête --}}
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div class="flex flex-col gap-1">
            <div class="flex items-center gap-2 font-label-sm text-label-sm text-on-surface-variant">
                <a class="transition-colors hover:text-primary" href="{{ route('admin.dashboard') }}">Accueil</a>
                <span class="material-symbols-outlined text-body-sm" aria-hidden="true">chevron_right</span>
                <span class="font-semibold text-on-surface">Départements &amp; Structure</span>
            </div>
            <h1 class="font-headline-lg text-headline-lg tracking-tight text-on-surface">Gestion des Départements</h1>
            <p class="font-body-sm text-body-sm text-on-surface-variant">
                {{ $totals['activeEmployees'] }} {{ $totals['activeEmployees'] === 1 ? 'collaborateur actif' : 'collaborateurs actifs' }}
                · {{ $totals['departmentCount'] }} {{ $totals['departmentCount'] === 1 ? 'département' : 'départements' }}
                · Paie de {{ $periodLabel }}
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <a
                class="inline-flex h-10 items-center gap-2 rounded-xl bg-surface-container-low px-4 font-label-md text-label-md text-on-surface transition-colors hover:bg-surface-container focus:outline-none focus:ring-2 focus:ring-primary"
                href="{{ route('admin.departments.export', array_filter($filters)) }}"
            >
                <span class="material-symbols-outlined text-body-md" aria-hidden="true">download</span>
                <span>Exporter CSV</span>
            </a>
            <button
                class="inline-flex h-10 items-center gap-2 rounded-xl bg-primary-container px-4 font-label-md text-label-md text-on-primary transition-colors hover:bg-primary focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2"
                type="button"
                data-open-department-create
            >
                <span class="material-symbols-outlined text-body-md" aria-hidden="true">domain_add</span>
                <span>Nouveau département</span>
            </button>
        </div>
    </div>

    {{-- Indicateurs --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-ui.kpi-card
            label="Départements"
            :value="str_pad((string) $totals['departmentCount'], 2, '0', STR_PAD_LEFT)"
            :hint="$departmentHint"
            icon="apartment"
            tone="primary"
        />

        <x-ui.kpi-card
            label="Effectif actif"
            :value="$totals['activeEmployees']"
            :hint="$headcountHint"
            icon="groups"
            tone="secondary"
        />

        <x-ui.kpi-card
            label="Masse salariale brute"
            :value="$totals['totalGross']->format(0).' TND'"
            :hint="$payrollHint"
            icon="payments"
            tone="tertiary"
        />

        <x-ui.kpi-card
            label="Budget mensuel"
            :value="$totals['totalBudget']->format(0).' TND'"
            :hint="$budgetHint"
            icon="account_balance_wallet"
            tone="neutral"
        />
    </div>

    {{-- Recherche et tri --}}
    <form class="flex flex-col gap-3 rounded-xl bg-surface-container-lowest p-4 shadow-sm lg:flex-row lg:items-end" method="GET" action="{{ route('admin.departments') }}">
        <div class="flex-1">
            <label class="sr-only" for="department-search">Rechercher un département</label>
            <div class="relative">
                <span class="material-symbols-outlined pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-body-md text-on-surface-variant" aria-hidden="true">search</span>
                <input
                    class="h-10 w-full rounded-lg bg-surface-container-low pl-10 pr-3 font-body-sm text-body-sm text-on-surface placeholder:text-on-surface-variant focus:bg-surface-container focus:outline-none focus:ring-2 focus:ring-primary"
                    id="department-search"
                    name="q"
                    type="search"
                    maxlength="100"
                    value="{{ $filters['q'] }}"
                    placeholder="Rechercher par nom, code ou responsable…"
                />
            </div>
        </div>

        <div class="grid grid-cols-2 gap-3 sm:flex sm:items-center">
            <div>
                <label class="sr-only" for="department-sort">Trier par</label>
                <select
                    class="h-10 w-full rounded-lg bg-surface-container-low px-3 font-body-sm text-body-sm text-on-surface focus:outline-none focus:ring-2 focus:ring-primary"
                    id="department-sort"
                    name="sort"
                    onchange="this.form.submit()"
                >
                    <option value="headcount" @selected($filters['sort'] === 'headcount')>Effectif</option>
                    <option value="name" @selected($filters['sort'] === 'name')>Nom</option>
                    <option value="budget" @selected($filters['sort'] === 'budget')>Budget</option>
                </select>
            </div>
            <div>
                <label class="sr-only" for="department-direction">Ordre de tri</label>
                <select
                    class="h-10 w-full rounded-lg bg-surface-container-low px-3 font-body-sm text-body-sm text-on-surface focus:outline-none focus:ring-2 focus:ring-primary"
                    id="department-direction"
                    name="direction"
                    onchange="this.form.submit()"
                >
                    <option value="desc" @selected($filters['direction'] === 'desc')>Décroissant</option>
                    <option value="asc" @selected($filters['direction'] === 'asc')>Croissant</option>
                </select>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <button
                class="inline-flex h-10 items-center gap-2 rounded-lg bg-primary-container px-4 font-label-md text-label-md text-on-primary transition-colors hover:bg-primary focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2"
                type="submit"
            >
                <span class="material-symbols-outlined text-body-md" aria-hidden="true">search</span>
                <span>Rechercher</span>
            </button>
            @if ($hasFilters)
                <a
                    class="inline-flex h-10 items-center rounded-lg px-3 font-label-md text-label-md text-on-surface-variant transition-colors hover:bg-surface-container hover:text-on-surface focus:outline-none focus:ring-2 focus:ring-primary"
                    href="{{ route('admin.departments') }}"
                >
                    Réinitialiser
                </a>
            @endif
        </div>
    </form>

    {{-- Cartes des départements --}}
    <section class="space-y-4" aria-labelledby="departments-heading">
        <div class="flex items-end justify-between gap-3">
            <div>
                <h2 class="font-headline-sm text-headline-sm font-semibold text-on-surface" id="departments-heading">Départements</h2>
                <p class="font-body-sm text-body-sm text-on-surface-variant">
                    {{ $departments->total() }} {{ $departments->total() === 1 ? 'département' : 'départements' }}
                    @if ($hasFilters)
                        · filtres actifs
                    @endif
                </p>
            </div>
            <p class="hidden font-label-sm text-label-sm text-on-surface-variant sm:block">Masse salariale brute · sans charges patronales</p>
        </div>

        <div class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-3">
            @forelse ($departments as $summary)
                @php
                    $department = $summary->department;
                    $variance = $summary->variance();
                    $editPayload = [
                        'id' => $department->id,
                        'name' => $department->name,
                        'manager_id' => $department->manager_id !== null ? (string) $department->manager_id : '',
                        'code' => $department->code ?? '',
                        'budget' => $summary->budget->isZero() ? '' : $summary->budget->toDecimalString(),
                        'description' => $department->description ?? '',
                    ];
                    $deletePayload = [
                        'id' => $department->id,
                        'name' => $department->name,
                        'slug' => $department->slug,
                        'employees' => (int) $department->employees_count,
                        'locked' => $summary->canViewTeam(),
                    ];
                @endphp

                <article class="flex flex-col rounded-xl bg-surface-container-lowest p-5 shadow-sm transition-shadow hover:shadow-md">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="truncate font-label-sm text-label-sm uppercase tracking-wider text-on-surface-variant">{{ $department->code ?: 'Département' }}</p>
                            <h3 class="mt-1 truncate font-headline-sm text-headline-sm font-semibold text-on-surface" title="{{ $department->name }}">{{ $department->name }}</h3>
                        </div>
                        <x-ui.status-badge :tone="$summary->budgetStatus->tone()" :label="$summary->budgetStatus->label()" />
                    </div>

                    <p class="mt-2 min-h-[36px] font-body-sm text-body-sm text-on-surface-variant">{{ $department->description ?: 'Aucune description renseignée.' }}</p>

                    <div class="mt-4 flex items-center gap-3 rounded-xl bg-surface-container-low p-3">
                        @if ($department->manager?->avatar_url)
                            <img class="h-10 w-10 shrink-0 rounded-full object-cover" src="{{ $department->manager->avatar_url }}" alt="Portrait de {{ $department->manager->name }}"/>
                        @else
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-surface-container-high font-label-md text-label-md font-bold text-on-surface" aria-hidden="true">
                                {{ $department->manager?->initials() ?? '—' }}
                            </span>
                        @endif
                        <div class="min-w-0 flex-1">
                            <p class="truncate font-label-md text-label-md font-semibold text-on-surface">{{ $department->manager?->name ?? 'Responsable non assigné' }}</p>
                            <p class="truncate font-label-sm text-label-sm text-on-surface-variant">{{ $department->manager ? ($department->manager->position ?: $department->manager->email ?: 'Responsable de département') : 'Assignez un responsable pour ce département.' }}</p>
                        </div>
                        @if ($department->manager?->email)
                            <a
                                class="shrink-0 rounded-lg p-1.5 text-on-surface-variant transition-colors hover:bg-surface-container hover:text-primary focus:outline-none focus:ring-2 focus:ring-primary"
                                href="mailto:{{ $department->manager->email }}"
                                aria-label="Contacter {{ $department->manager->name }}"
                            >
                                <span class="material-symbols-outlined text-body-md" aria-hidden="true">mail</span>
                            </a>
                        @endif
                    </div>

                    <div class="mt-4 grid grid-cols-2 gap-3">
                        <div class="rounded-lg bg-surface-container-low p-3">
                            <p class="font-label-sm text-label-sm uppercase tracking-wider text-on-surface-variant">Effectif actif</p>
                            <p class="mt-1 font-numeric-md text-numeric-md font-semibold text-on-surface">{{ $summary->headcount }}</p>
                        </div>
                        <div class="rounded-lg bg-surface-container-low p-3">
                            <p class="font-label-sm text-label-sm uppercase tracking-wider text-on-surface-variant">Masse salariale</p>
                            <p class="mt-1 font-semibold text-on-surface">
                                <x-ui.money :value="$summary->gross" :decimals="3" />
                            </p>
                        </div>
                    </div>

                    <div class="mt-4 space-y-2">
                        <div class="flex items-center justify-between gap-2 font-label-sm text-label-sm">
                            <span class="text-on-surface-variant">Budget mensuel</span>
                            @if ($summary->budgetStatus->value === 'undefined')
                                <span class="font-semibold text-on-surface-variant">Non défini</span>
                            @else
                                <x-ui.money :value="$summary->budget" :decimals="3" class="font-semibold text-on-surface" />
                            @endif
                        </div>

                        @if ($summary->budgetStatus->value === 'undefined')
                            <p class="font-label-sm text-label-sm text-on-surface-variant">Définissez un budget pour suivre la consommation salariale.</p>
                        @else
                            <div class="h-1.5 overflow-hidden rounded-full bg-surface-container" aria-hidden="true">
                                <div
                                    class="h-full rounded-full {{ $summary->budgetStatus->value === 'over' ? 'bg-error' : 'bg-tertiary' }}"
                                    style="width: {{ min(100, max(0, $summary->usagePercent ?? 0)) }}%"
                                ></div>
                            </div>
                            <div class="flex items-center justify-between gap-2 font-label-sm text-label-sm">
                                <span class="text-on-surface-variant">Consommé : {{ $summary->usagePercent ?? 0 }} %</span>
                                @if ($variance !== null && $variance->millimes() < 0)
                                    <span class="font-semibold text-error">
                                        Dépassement de <x-ui.money :value="$variance->abs()" :decimals="3" />
                                    </span>
                                @elseif ($variance !== null)
                                    <span class="font-semibold text-tertiary">
                                        Marge de <x-ui.money :value="$variance" :decimals="3" />
                                    </span>
                                @endif
                            </div>
                        @endif
                    </div>

                    <div class="mt-5 flex flex-col gap-3 border-t border-surface-container pt-4 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex items-center">
                            @if ($summary->headcount === 0)
                                <span class="font-label-sm text-label-sm italic text-on-surface-variant">Aucun collaborateur actif</span>
                            @else
                                <div class="flex -space-x-2">
                                    @foreach ($summary->avatars as $member)
                                        @if ($member->avatar_url)
                                            <img class="inline-block h-7 w-7 rounded-full object-cover ring-2 ring-surface-container-lowest" src="{{ $member->avatar_url }}" alt="Portrait de {{ $member->name }}"/>
                                        @else
                                            <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-surface-container-high font-label-sm text-label-sm font-bold text-on-surface ring-2 ring-surface-container-lowest" aria-hidden="true">{{ $member->initials() }}</span>
                                        @endif
                                    @endforeach
                                    @if ($summary->headcount > 3)
                                        <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-surface-container-high font-label-sm text-label-sm font-bold text-on-surface ring-2 ring-surface-container-lowest">+{{ $summary->headcount - 3 }}</span>
                                    @endif
                                </div>
                            @endif
                        </div>

                        <div class="flex items-center gap-1">
                            @if ($summary->canViewTeam())
                                <a
                                    class="rounded-lg px-2.5 py-1.5 font-label-sm text-label-sm font-semibold text-primary transition-colors hover:bg-surface-container-low focus:outline-none focus:ring-2 focus:ring-primary"
                                    href="{{ route('admin.employees', ['department' => $department->slug]) }}"
                                >
                                    Voir l'équipe
                                </a>
                            @endif
                            <button
                                class="inline-flex items-center gap-1 rounded-lg px-2 py-1.5 font-label-sm text-label-sm font-semibold text-on-surface-variant transition-colors hover:bg-surface-container-low hover:text-on-surface focus:outline-none focus:ring-2 focus:ring-primary"
                                type="button"
                                data-edit-department="{{ json_encode($editPayload) }}"
                                aria-label="Modifier {{ $department->name }}"
                            >
                                <span class="material-symbols-outlined text-body-sm" aria-hidden="true">edit</span>
                                <span>Modifier</span>
                            </button>
                            <button
                                class="rounded-lg p-1.5 text-on-surface-variant transition-colors hover:bg-error-container hover:text-on-error-container focus:outline-none focus:ring-2 focus:ring-error"
                                type="button"
                                data-delete-department="{{ json_encode($deletePayload) }}"
                                aria-label="Supprimer {{ $department->name }}"
                                title="Supprimer le département"
                            >
                                <span class="material-symbols-outlined text-body-sm" aria-hidden="true">delete</span>
                            </button>
                        </div>
                    </div>
                </article>
            @empty
                <div class="md:col-span-2 xl:col-span-3">
                    @if ($filters['q'] !== '')
                        <x-ui.empty-state
                            icon="search_off"
                            title="Aucun résultat"
                            :description="'Aucun département ne correspond à « '.$filters['q'].' ».'"
                        >
                            <a
                                class="inline-flex h-9 items-center rounded-lg bg-surface-container-low px-4 font-label-md text-label-md text-on-surface transition-colors hover:bg-surface-container focus:outline-none focus:ring-2 focus:ring-primary"
                                href="{{ route('admin.departments') }}"
                            >
                                Effacer la recherche
                            </a>
                        </x-ui.empty-state>
                    @else
                        <x-ui.empty-state
                            icon="apartment"
                            title="Aucun département"
                            description="Créez une structure pour suivre son effectif, sa masse salariale et son budget mensuel."
                        >
                            <button
                                class="inline-flex h-9 items-center gap-2 rounded-lg bg-primary-container px-4 font-label-md text-label-md text-on-primary transition-colors hover:bg-primary focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2"
                                type="button"
                                data-open-department-create
                            >
                                <span class="material-symbols-outlined text-body-md" aria-hidden="true">domain_add</span>
                                <span>Créer un département</span>
                            </button>
                        </x-ui.empty-state>
                    @endif
                </div>
            @endforelse
        </div>

        <x-ui.pagination :paginator="$departments" />
    </section>

    {{-- Synthèse budgétaire --}}
    <section class="space-y-3" aria-labelledby="budget-heading">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="font-headline-sm text-headline-sm font-semibold text-on-surface" id="budget-heading">Synthèse budgétaire</h2>
                <p class="font-body-sm text-body-sm text-on-surface-variant">Masse salariale brute comparée au budget mensuel pour la paie de {{ $periodLabel }}.</p>
            </div>
            <a
                class="inline-flex items-center gap-1.5 font-label-sm text-label-sm font-semibold text-primary transition-colors hover:text-primary-container focus:outline-none focus:ring-2 focus:ring-primary"
                href="{{ route('admin.departments.export', array_filter($filters)) }}"
            >
                <span class="material-symbols-outlined text-body-sm" aria-hidden="true">download</span>
                <span>Télécharger la synthèse complète</span>
            </a>
        </div>

        <div class="overflow-hidden rounded-xl bg-surface-container-lowest shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[820px] text-left font-body-sm text-body-sm">
                    <caption class="sr-only">Synthèse budgétaire par département pour la paie de {{ $periodLabel }}</caption>
                    <thead>
                        <tr class="bg-surface-container-low font-label-sm text-label-sm uppercase tracking-wider text-on-surface-variant">
                            <th class="px-4 py-3 font-semibold" scope="col">Département</th>
                            <th class="px-4 py-3 font-semibold" scope="col">Responsable</th>
                            <th class="px-4 py-3 text-center font-semibold" scope="col">Effectif</th>
                            <th class="px-4 py-3 text-right font-semibold" scope="col">Masse salariale</th>
                            <th class="px-4 py-3 text-right font-semibold" scope="col">Budget</th>
                            <th class="px-4 py-3 text-right font-semibold" scope="col">Écart</th>
                            <th class="px-4 py-3 text-center font-semibold" scope="col">Statut</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-container">
                        @forelse ($departments as $summary)
                            @php($rowVariance = $summary->variance())
                            <tr class="transition-colors hover:bg-surface-container-low/50">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <span class="font-semibold text-on-surface">{{ $summary->department->name }}</span>
                                        @if ($summary->department->code)
                                            <span class="rounded bg-surface-container px-1.5 py-0.5 font-label-sm text-label-sm text-on-surface-variant">{{ $summary->department->code }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-on-surface-variant">{{ $summary->department->manager?->name ?? 'Non assigné' }}</td>
                                <td class="px-4 py-3 text-center font-numeric-md text-numeric-md text-on-surface">{{ $summary->headcount }}</td>
                                <td class="px-4 py-3 text-right">
                                    <x-ui.money :value="$summary->gross" :decimals="3" class="font-semibold text-on-surface" />
                                </td>
                                <td class="px-4 py-3 text-right">
                                    @if ($summary->budgetStatus->value === 'undefined')
                                        <span class="text-on-surface-variant">—</span>
                                    @else
                                        <x-ui.money :value="$summary->budget" :decimals="3" class="text-on-surface" />
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right">
                                    @if ($rowVariance === null)
                                        <span class="text-on-surface-variant">—</span>
                                    @elseif ($rowVariance->millimes() < 0)
                                        <x-ui.money :value="$rowVariance" :decimals="3" :signed="true" class="font-semibold text-error" />
                                    @else
                                        <x-ui.money :value="$rowVariance" :decimals="3" :signed="true" class="font-semibold text-tertiary" />
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <x-ui.status-badge :tone="$summary->budgetStatus->tone()" :label="$summary->budgetStatus->label()" />
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="px-4 py-8 text-center font-body-sm text-body-sm text-on-surface-variant" colspan="7">
                                    Aucun département à afficher.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <p class="font-label-sm text-label-sm text-on-surface-variant">Montants bruts (salaire de base et primes), hors charges patronales. La synthèse affiche la page courante ; l'export CSV couvre l'ensemble des départements filtrés.</p>
    </section>

    <x-admin.department-form-dialog :employees="$employees" />

    {{-- Confirmation de suppression --}}
    <x-ui.modal
        id="department-delete-dialog"
        title="Supprimer le département"
        description="Cette action est définitive et ne peut pas être annulée."
    >
        <p class="font-body-md text-body-md text-on-surface-variant">
            Vous êtes sur le point de supprimer <strong class="font-semibold text-on-surface" id="department-delete-name"></strong>.
        </p>

        <div class="mt-4 hidden rounded-lg bg-error-container p-3 text-on-error-container" id="department-delete-blocked">
            <p class="font-body-sm text-body-sm" id="department-delete-reason"></p>
            <a
                class="mt-2 inline-flex items-center gap-1 font-label-sm text-label-sm font-semibold underline underline-offset-2 focus:outline-none focus:ring-2 focus:ring-on-error-container"
                id="department-delete-team-link"
                href="{{ route('admin.employees') }}"
            >
                Voir les collaborateurs concernés
            </a>
        </div>

        <form class="mt-5 flex flex-col-reverse gap-3 border-t border-surface-container pt-5 sm:flex-row sm:justify-end" id="department-delete-form" method="POST" action="{{ route('admin.departments') }}">
            @csrf
            @method('DELETE')
            <button
                class="h-10 rounded-lg bg-surface-container-low px-4 font-label-md text-label-md text-on-surface transition-colors hover:bg-surface-container focus:outline-none focus:ring-2 focus:ring-primary"
                type="button"
                onclick="document.getElementById('department-delete-dialog').close()"
            >
                Annuler
            </button>
            <button
                class="h-10 rounded-lg bg-error px-4 font-label-md text-label-md text-on-error transition-colors hover:bg-error/90 focus:outline-none focus:ring-2 focus:ring-error focus:ring-offset-2"
                id="department-delete-submit"
                type="submit"
            >
                Supprimer définitivement
            </button>
        </form>
    </x-ui.modal>
</div>

<script>
    (function () {
        const formDialog = document.getElementById('department-form-dialog');
        const form = document.getElementById('department-form');

        if (!formDialog || !form) {
            return;
        }

        const formTitle = document.getElementById('department-form-dialog-title');
        const submitLabel = document.getElementById('department-submit-label');
        const modeInput = document.getElementById('department-form-mode');
        const idInput = document.getElementById('department-dialog-id');
        const methodInput = document.getElementById('department-dialog-method');
        const createNotice = document.getElementById('department-create-notice');
        const nameInput = document.getElementById('department-name');
        const managerSelect = document.getElementById('department-manager');
        const codeInput = document.getElementById('department-code');
        const budgetInput = document.getElementById('department-budget');
        const descriptionInput = document.getElementById('department-description');
        const storeUrl = @json(route('admin.departments.store'));
        const updateBase = @json(url('/admin/departments'));

        function clearServerErrors() {
            form.querySelectorAll('[aria-invalid="true"]').forEach(function (field) {
                field.removeAttribute('aria-invalid');
            });
            form.querySelectorAll('[id$="-error"]').forEach(function (message) {
                message.remove();
            });
            form.querySelectorAll('.border-error').forEach(function (field) {
                field.classList.remove('border-error');
                field.classList.add('border-outline-variant');
            });
        }

        function openFormDialog() {
            formDialog.showModal();
            window.setTimeout(function () {
                const invalid = form.querySelector('[aria-invalid="true"]');
                (invalid || nameInput).focus();
            }, 30);
        }

        function selectManager(managerId) {
            managerSelect.querySelectorAll('option[data-temporary]').forEach(function (option) {
                option.remove();
            });

            managerSelect.value = managerId || '';

            if (managerId && managerSelect.value !== String(managerId)) {
                const option = document.createElement('option');
                option.value = managerId;
                option.textContent = 'Responsable actuel';
                option.dataset.temporary = 'true';
                managerSelect.appendChild(option);
                managerSelect.value = String(managerId);
            }
        }

        function prepareCreate() {
            clearServerErrors();
            formTitle.textContent = 'Nouveau département';
            submitLabel.textContent = 'Créer le département';
            form.action = storeUrl;
            modeInput.value = 'create';
            idInput.value = '';
            methodInput.value = 'POST';
            nameInput.value = '';
            selectManager('');
            codeInput.value = '';
            budgetInput.value = '';
            descriptionInput.value = '';
            createNotice.classList.remove('hidden');
            openFormDialog();
        }

        function prepareEdit(data) {
            clearServerErrors();
            formTitle.textContent = 'Modifier le département';
            submitLabel.textContent = 'Enregistrer les modifications';
            form.action = updateBase + '/' + data.id;
            modeInput.value = 'edit';
            idInput.value = data.id;
            methodInput.value = 'PUT';
            nameInput.value = data.name || '';
            codeInput.value = data.code || '';
            budgetInput.value = data.budget || '';
            descriptionInput.value = data.description || '';
            selectManager(data.manager_id);
            createNotice.classList.add('hidden');
            openFormDialog();
        }

        document.querySelectorAll('[data-open-department-create]').forEach(function (button) {
            button.addEventListener('click', prepareCreate);
        });

        document.querySelectorAll('[data-edit-department]').forEach(function (button) {
            button.addEventListener('click', function () {
                try {
                    prepareEdit(JSON.parse(button.dataset.editDepartment));
                } catch (error) {
                    prepareCreate();
                }
            });
        });

        const deleteDialog = document.getElementById('department-delete-dialog');
        const deleteForm = document.getElementById('department-delete-form');
        const deleteName = document.getElementById('department-delete-name');
        const deleteSubmit = document.getElementById('department-delete-submit');
        const deleteBlocked = document.getElementById('department-delete-blocked');
        const deleteReason = document.getElementById('department-delete-reason');
        const deleteTeamLink = document.getElementById('department-delete-team-link');
        const employeesUrl = @json(route('admin.employees'));
        const deleteBase = @json(url('/admin/departments'));

        document.querySelectorAll('[data-delete-department]').forEach(function (button) {
            button.addEventListener('click', function () {
                const data = JSON.parse(button.dataset.deleteDepartment);
                const attached = Number(data.employees || 0);
                const blocked = Boolean(data.locked) || attached > 0;

                deleteName.textContent = '« ' + data.name + ' »';
                deleteForm.action = deleteBase + '/' + data.id;
                deleteSubmit.classList.toggle('hidden', blocked);
                deleteBlocked.classList.toggle('hidden', !blocked);

                if (data.locked) {
                    deleteReason.textContent = 'Ce département est une structure de référence du module Collaborateurs et ne peut pas être supprimé.'
                        + (attached > 0 ? ' ' + attached + (attached === 1 ? ' collaborateur y est rattaché.' : ' collaborateurs y sont rattachés.') : '');
                } else if (attached > 0) {
                    deleteReason.textContent = attached + (attached === 1
                        ? ' collaborateur est encore rattaché à ce département. Réaffectez-le avant de supprimer.'
                        : ' collaborateurs sont encore rattachés à ce département. Réaffectez-les avant de supprimer.');
                }

                deleteTeamLink.href = employeesUrl + '?department=' + encodeURIComponent(data.slug);
                deleteTeamLink.classList.toggle('hidden', attached === 0);
                deleteDialog.showModal();
            });
        });

        [formDialog, deleteDialog].forEach(function (dialog) {
            if (!dialog) {
                return;
            }

            dialog.addEventListener('click', function (event) {
                if (event.target === dialog) {
                    dialog.close();
                }
            });
        });

        @if ($errors->any() && in_array(old('_form'), ['create', 'edit'], true))
            openFormDialog();
        @endif
    })();
</script>
</x-admin-shell>
