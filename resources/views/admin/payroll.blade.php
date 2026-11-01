<x-admin-shell active="payroll" title="Gestion de la Paie & Salaires">
@php
    $filterQuery = array_filter([
        'q' => $filters['q'],
        'department' => $filters['department'],
        'status' => $filters['status'],
    ], fn ($value) => $value !== '');
    $exportQuery = array_merge($filterQuery, ['month' => $period]);
    $hasFilters = $filterQuery !== [];
    $slip = $selectedPayload;
    $pageNet = \App\Support\Money::sum($payslips->getCollection()->map(fn ($payslip) => $payslip->netAmount()));
    $growthLabel = $growthRate === null
        ? 'Historique indisponible'
        : (($growthRate >= 0 ? '+' : '').$growthRate.' % vs '.$previousMonthShort);
    $cycleTone = $periodClosed ? 'primary' : ($payslipCount === 0 ? 'neutral' : ($missingCount > 0 ? 'error' : ($reviewCount > 0 ? 'warning' : 'success')));
    $cycleTitle = $periodClosed
        ? 'Cycle clôturé'
        : ($payslipCount === 0
            ? 'Aucun bulletin généré'
            : ($missingCount > 0
                ? 'Cycle incomplet : '.$missingCount.' bulletin(s) manquant(s)'
                : ($reviewCount > 0 ? $reviewCount.' bulletin(s) en révision' : 'Prêt à clôturer')));
    $cycleHint = $periodClosed
        ? $paidCount.' bulletin(s) marqué(s) payé(s) pour '.$periodLabel.'.'
        : ($payslipCount === 0
            ? 'Générez les bulletins des '.$activeEmployees.' collaborateur(s) actif(s) pour démarrer le cycle.'
            : $readyCount.' prêt(s) · '.$reviewCount.' en révision · '.$paidCount.' payé(s) · '.$missingCount.' manquant(s).');
@endphp

<x-ui.toast />

@if ($errors->any())
    <div class="mt-space-md flex items-start gap-space-sm rounded-xl bg-error-container p-space-md text-on-error-container shadow-sm" role="alert">
        <span class="material-symbols-outlined text-body-lg shrink-0" aria-hidden="true">error</span>
        <p class="font-label-md text-label-md">{{ $errors->first() }}</p>
    </div>
@endif

<div class="flex w-full flex-col pb-16">
    <div class="flex flex-wrap items-center justify-between gap-space-sm pb-space-md pt-space-lg">
        <nav aria-label="Fil d'ariane" class="flex items-center gap-space-xs font-label-md text-label-md text-on-surface-variant">
            <a class="flex items-center gap-1 transition-colors hover:text-primary" href="{{ route('admin.dashboard') }}">
                <span class="material-symbols-outlined text-body-sm">home</span>
                <span>Accueil</span>
            </a>
            <span class="text-outline-variant">/</span>
            <span class="font-semibold text-on-surface">Paie &amp; Rémunérations</span>
            <span class="text-outline-variant">/</span>
            <span class="font-medium text-primary">{{ $periodLabel }}</span>
        </nav>
        <div class="flex items-center gap-space-xs rounded-full bg-surface-container-low px-space-md py-1.5 font-label-sm text-label-sm text-on-surface-variant shadow-sm">
            <span class="material-symbols-outlined text-body-sm">tune</span>
            <span>Paramétrage de paie {{ $version }}</span>
        </div>
    </div>

    <div class="flex flex-col justify-between gap-space-lg pb-space-xl xl:flex-row xl:items-end">
        <div>
            <div class="mb-1 flex flex-wrap items-center gap-space-sm">
                <span class="font-label-sm text-label-sm font-semibold uppercase tracking-wider text-primary">Gestion Sociale &amp; Fiscale</span>
                <span class="text-outline-variant">•</span>
                <span class="font-numeric-md text-numeric-md text-on-surface-variant">Cycle {{ $cycleId }}</span>
            </div>
            <h1 class="font-display-lg text-display-lg tracking-tight text-on-surface">Gestion de la Paie &amp; Salaires</h1>
            <p class="mt-1 flex flex-wrap items-center gap-space-xs font-body-md text-body-md text-on-surface-variant">
                <span class="material-symbols-outlined text-body-md text-tertiary">calendar_month</span>
                <span><strong>{{ $periodLabel }}</strong> · {{ $payslipCount }} bulletin(s) · {{ $coveredEmployees }}/{{ $activeEmployees }} collaborateur(s) couvert(s)</span>
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-space-sm">
            @if ($payslipCount > 0)
                <a class="flex h-10 items-center gap-space-xs rounded-xl bg-surface-container-lowest px-space-md font-label-lg text-label-lg text-on-surface shadow-sm transition-all hover:bg-surface-container focus:outline-none focus:ring-2 focus:ring-primary" href="{{ route('admin.payroll.export.journal', $exportQuery) }}" title="Exporter le journal de paie au format CSV">
                    <span class="material-symbols-outlined text-body-md text-tertiary">table_view</span>
                    <span>Journal CSV</span>
                </a>
                <a class="flex h-10 items-center gap-space-xs rounded-xl bg-surface-container-lowest px-space-md font-label-lg text-label-lg text-on-surface shadow-sm transition-all hover:bg-surface-container focus:outline-none focus:ring-2 focus:ring-primary" href="{{ route('admin.payroll.export.cnss', $exportQuery) }}" title="Exporter les cotisations CNSS au format CSV">
                    <span class="material-symbols-outlined text-body-md text-primary">database</span>
                    <span>CNSS CSV</span>
                </a>
                <a class="flex h-10 items-center gap-space-xs rounded-xl bg-surface-container-lowest px-space-md font-label-lg text-label-lg text-on-surface shadow-sm transition-all hover:bg-surface-container focus:outline-none focus:ring-2 focus:ring-primary" href="{{ route('admin.payroll.export.bank', $exportQuery) }}" title="Exporter les virements au statut Prêt avec RIB">
                    <span class="material-symbols-outlined text-body-md text-secondary">send</span>
                    <span>Virements CSV</span>
                </a>
            @endif
            <button class="flex h-10 items-center gap-space-xs rounded-xl bg-surface-container px-space-md font-label-lg text-label-lg text-on-surface shadow-sm transition-all hover:bg-surface-container-high focus:outline-none focus:ring-2 focus:ring-primary disabled:cursor-not-allowed disabled:opacity-50" type="button" onclick="openDialog('generatePayrollDialog')" @disabled($activeEmployees === 0)>
                <span class="material-symbols-outlined text-body-md">auto_awesome</span>
                <span>Générer les bulletins</span>
            </button>
            <button class="flex h-10 items-center gap-space-xs rounded-xl bg-primary-container px-space-lg font-label-lg text-label-lg text-on-primary shadow-md transition-all hover:bg-primary focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 disabled:hover:bg-primary-container" type="button" onclick="openDialog('closePayrollDialog')" @disabled($periodClosed || $payslipCount === 0 || $missingCount > 0)>
                <span class="material-symbols-outlined text-body-md">lock</span>
                <span>{{ $periodClosed ? 'Mois clôturé' : 'Clôturer le mois' }}</span>
            </button>
        </div>
    </div>

    <section class="mb-space-xl rounded-xl bg-surface-container-low p-space-lg shadow-sm" aria-label="État du cycle de paie">
        <div class="flex flex-col justify-between gap-space-md md:flex-row md:items-center">
            <div class="flex items-start gap-space-md">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-surface-container-lowest text-primary">
                    <span class="material-symbols-outlined text-headline-sm">account_balance</span>
                </div>
                <div>
                    <div class="flex flex-wrap items-center gap-space-xs">
                        <h2 class="font-headline-sm text-headline-sm text-on-surface">Cycle {{ $periodLabel }} · {{ $cycleTitle }}</h2>
                        <x-ui.status-badge :tone="$cycleTone" :label="$periodClosed ? 'Clôturé' : ($payslipCount === 0 ? 'À générer' : 'En cours')" />
                    </div>
                    <p class="mt-0.5 font-body-sm text-body-sm text-on-surface-variant">{{ $cycleHint }}</p>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-space-md">
                <div class="text-left sm:text-right">
                    <p class="font-label-sm text-label-sm uppercase text-on-surface-variant">Échéance de paiement cible</p>
                    <p class="font-numeric-md text-numeric-md font-bold text-on-surface">{{ $deadline->format('j') }} {{ ucfirst($deadline->copy()->locale('fr')->translatedFormat('F Y')) }}</p>
                </div>
                <div class="text-left sm:text-right">
                    <p class="font-label-sm text-label-sm uppercase text-on-surface-variant">Déclaration IRPP cible</p>
                    <p class="font-numeric-md text-numeric-md font-semibold text-on-surface">{{ $irppDeadline->format('d/m/Y') }}</p>
                </div>
            </div>
        </div>
        <div class="mt-space-md flex flex-wrap items-center gap-space-xs border-t border-outline-variant/30 pt-space-sm">
            <x-ui.status-badge tone="success" :label="$readyCount.' prêt(s)'" />
            <x-ui.status-badge tone="warning" :label="$reviewCount.' en révision'" />
            <x-ui.status-badge tone="primary" :label="$paidCount.' payé(s)'" />
            <x-ui.status-badge :tone="$missingCount > 0 ? 'error' : 'neutral'" :label="$missingCount.' manquant(s)'" />
            <span class="ml-auto font-label-sm text-label-sm text-on-surface-variant">
                Couverture : {{ $coverageRate === null ? '—' : $coverageRate.' %' }} · {{ $coveredEmployees }}/{{ $activeEmployees }} collaborateurs actifs
            </span>
        </div>
    </section>

    <div class="mb-space-xl grid grid-cols-1 gap-space-md md:grid-cols-2 xl:grid-cols-4">
        <x-ui.kpi-card
            label="Masse salariale brute"
            :value="$grossTotal->format(3).' TND'"
            icon="payments"
            tone="primary"
            :hint="$payslipCount.' bulletin(s) · '.$coveredEmployees.'/'.$activeEmployees.' collaborateurs couverts'"
        />
        <x-ui.kpi-card
            label="Net à payer"
            :value="$netTotal->format(3).' TND'"
            icon="account_balance_wallet"
            tone="tertiary"
            :hint="'Prêt : '.$readyCount.' · Payé : '.$paidCount.' · '.$growthLabel"
        />
        <x-ui.kpi-card
            label="CNSS salariale + patronale"
            :value="$cnssTotal->plus($employerCnssTotal)->format(3).' TND'"
            icon="badge"
            tone="secondary"
            :hint="'Salariale '.$cnssTotal->format(3).' TND ('.$rates['employeeCnss'].') · Patronale '.$employerCnssTotal->format(3).' TND ('.$rates['employerCnss'].')'"
        />
        <x-ui.kpi-card
            label="IRPP retenu"
            :value="$irppTotal->format(3).' TND'"
            icon="receipt_long"
            tone="neutral"
            :hint="'CSS '.$cssTotal->format(3).' TND ('.$rates['css'].') · Paramétrage '.$version"
        />
    </div>

    <div class="grid grid-cols-1 gap-space-lg xl:grid-cols-12">
        <div class="flex flex-col gap-space-md xl:col-span-8">
            <form class="flex flex-wrap items-center justify-between gap-space-sm rounded-xl bg-surface-container-lowest p-space-md shadow-sm" id="payrollFilterForm" method="GET" action="{{ route('admin.payroll') }}">
                <input name="month" type="hidden" value="{{ $period }}"/>
                <div class="flex min-w-[280px] flex-1 flex-wrap items-center gap-space-sm">
                    <div class="relative min-w-[220px] flex-1">
                        <label class="sr-only" for="payrollSearch">Rechercher un collaborateur</label>
                        <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-body-md text-on-surface-variant material-symbols-outlined">search</span>
                        <input class="h-9 w-full rounded-lg bg-surface-container-low pl-9 pr-3 font-body-sm text-body-sm text-on-surface transition-colors focus:bg-surface-container focus:outline-none focus:ring-2 focus:ring-primary" id="payrollSearch" name="q" placeholder="Nom, CIN, N° CNSS, e-mail..." type="text" value="{{ $filters['q'] }}"/>
                    </div>
                    <label class="sr-only" for="payrollDepartment">Filtrer par département</label>
                    <select class="h-9 rounded-lg bg-surface-container-low px-space-sm font-body-sm text-body-sm text-on-surface focus:bg-surface-container focus:outline-none focus:ring-2 focus:ring-primary" id="payrollDepartment" name="department" onchange="this.form.requestSubmit()">
                        <option value="">Tous les départements</option>
                        @foreach (\App\Models\Employee::DEPARTMENTS as $value => $label)
                            <option value="{{ $value }}" @selected($filters['department'] === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <label class="sr-only" for="payrollStatus">Filtrer par statut</label>
                    <select class="h-9 rounded-lg bg-surface-container-low px-space-sm font-body-sm text-body-sm text-on-surface focus:bg-surface-container focus:outline-none focus:ring-2 focus:ring-primary" id="payrollStatus" name="status" onchange="this.form.requestSubmit()">
                        <option value="">Tous les statuts</option>
                        @foreach (\App\Models\Payslip::STATUSES as $value => $label)
                            <option value="{{ $value }}" @selected($filters['status'] === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @if ($hasFilters)
                        <a class="font-label-sm text-label-sm font-semibold text-primary hover:underline" href="{{ route('admin.payroll', ['month' => $period]) }}">Réinitialiser</a>
                    @endif
                </div>
                <div class="flex items-center gap-space-xs font-label-sm text-label-sm text-on-surface-variant">
                    <span>{{ $activeEmployees }} actif(s)</span>
                    <span class="h-1.5 w-1.5 rounded-full bg-outline-variant"></span>
                    <span>{{ $coveredEmployees }} couvert(s)</span>
                </div>
            </form>

            <div class="overflow-hidden rounded-xl bg-surface-container-lowest shadow-sm">
                @if ($payslips->isEmpty())
                    <div class="p-space-md">
                        @if ($payslipCount === 0)
                            <x-ui.empty-state icon="receipt_long" title="Aucun bulletin pour {{ $periodLabel }}" description="Générez les bulletins des collaborateurs actifs pour démarrer le cycle. Les montants seront calculés selon le paramétrage de paie {{ $version }}.">
                                <button class="inline-flex items-center gap-space-xs rounded-xl bg-primary-container px-space-lg py-2.5 font-label-lg text-label-lg text-on-primary shadow-sm transition-colors hover:bg-primary focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2" type="button" onclick="openDialog('generatePayrollDialog')">
                                    <span class="material-symbols-outlined text-body-md">auto_awesome</span>
                                    <span>Générer les bulletins</span>
                                </button>
                            </x-ui.empty-state>
                        @else
                            <x-ui.empty-state icon="filter_alt_off" title="Aucun résultat" description="Aucun bulletin ne correspond aux filtres sélectionnés pour {{ $periodLabel }}.">
                                <a class="inline-flex items-center gap-space-xs rounded-xl bg-surface-container px-space-lg py-2.5 font-label-lg text-label-lg text-on-surface transition-colors hover:bg-surface-container-high focus:outline-none focus:ring-2 focus:ring-primary" href="{{ route('admin.payroll', ['month' => $period]) }}">
                                    <span class="material-symbols-outlined text-body-md">filter_alt_off</span>
                                    <span>Effacer les filtres</span>
                                </a>
                            </x-ui.empty-state>
                        @endif
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse text-left">
                            <thead>
                                <tr class="h-10 bg-surface-container-low font-label-md text-label-md uppercase tracking-wider text-on-surface-variant">
                                    <th class="px-space-md py-2.5">Collaborateur</th>
                                    <th class="px-space-sm py-2.5">Département</th>
                                    <th class="px-space-sm py-2.5 text-right">Brut</th>
                                    <th class="px-space-sm py-2.5 text-right">Prime</th>
                                    <th class="px-space-sm py-2.5 text-right">CNSS</th>
                                    <th class="px-space-sm py-2.5 text-right">IRPP</th>
                                    <th class="px-space-md py-2.5 text-right">Net</th>
                                    <th class="px-space-sm py-2.5 text-center">Statut</th>
                                    <th class="px-space-sm py-2.5 text-center">Aperçu</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-surface-container font-body-md text-body-md">
                                @foreach ($payslips as $payslip)
                                    @php
                                        $isSelected = $selectedPayslip !== null && $selectedPayslip->id === $payslip->id;
                                        $viewUrl = route('admin.payroll', array_merge($exportQuery, ['view' => $payslip->id]));
                                    @endphp
                                    <tr class="transition-colors hover:bg-surface-container-low {{ $isSelected ? 'bg-primary-container/5' : '' }}">
                                        <td class="px-space-md py-3">
                                            <div class="flex items-center gap-space-sm">
                                                @if ($payslip->employee?->avatar_url)
                                                    <img class="h-8 w-8 shrink-0 rounded-full object-cover" alt="" src="{{ $payslip->employee->avatar_url }}"/>
                                                @else
                                                    <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-secondary-container font-bold text-label-sm text-on-secondary-container">
                                                        {{ $payslip->employee?->initials() ?? '?' }}
                                                    </div>
                                                @endif
                                                <div class="min-w-0">
                                                    <p class="truncate font-headline-sm text-headline-sm leading-tight text-on-surface">{{ $payslip->employee?->name ?? 'Collaborateur' }}</p>
                                                    <p class="font-mono font-label-sm text-label-sm text-on-surface-variant">{{ $payslip->matricule() }} · CIN {{ $payslip->employee?->cin ?: '—' }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-space-sm py-3">
                                            <span class="inline-block rounded bg-surface-container px-2 py-0.5 font-label-sm text-label-sm text-on-surface">{{ $payslip->employee?->departmentShortLabel() ?? '—' }}</span>
                                        </td>
                                        <td class="px-space-sm py-3 text-right font-numeric-md text-numeric-md text-on-surface">{{ $payslip->gross()->format(3) }}</td>
                                        <td class="px-space-sm py-3 text-right font-numeric-md text-numeric-md text-on-surface-variant">+{{ $payslip->primeAmount()->format(3) }}</td>
                                        <td class="px-space-sm py-3 text-right font-numeric-md text-numeric-md text-on-surface-variant">−{{ $payslip->cnssAmount()->format(3) }}</td>
                                        <td class="px-space-sm py-3 text-right font-numeric-md text-numeric-md text-on-surface-variant">−{{ $payslip->irppAmount()->format(3) }}</td>
                                        <td class="px-space-md py-3 text-right font-numeric-md text-numeric-md font-bold text-primary">{{ $payslip->netAmount()->format(3) }}</td>
                                        <td class="px-space-sm py-3 text-center">
                                            <x-ui.status-badge :tone="$payslip->statusTone()" :label="$payslip->statusLabel()" />
                                        </td>
                                        <td class="px-space-sm py-3 text-center">
                                            <a class="inline-flex items-center justify-center rounded p-1 text-primary transition-colors hover:bg-surface-container focus:outline-none focus:ring-2 focus:ring-primary" href="{{ $viewUrl }}" aria-label="Aperçu du bulletin de {{ $payslip->employee?->name }}" title="Aperçu du bulletin">
                                                <span class="material-symbols-outlined text-body-md">visibility</span>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="flex flex-wrap items-center justify-between gap-space-sm bg-surface-container-low p-space-md">
                        <div class="flex flex-wrap items-center gap-space-sm font-body-sm text-body-sm text-on-surface-variant">
                            <span>Affichage de {{ $payslips->firstItem() ?? 0 }} à {{ $payslips->lastItem() ?? 0 }} sur {{ $payslips->total() }} collaborateur(s)</span>
                            <span class="text-outline-variant">•</span>
                            <span>Total net de la page : <strong class="font-semibold text-on-surface">{{ $pageNet->format(3) }} TND</strong></span>
                        </div>
                        <x-ui.pagination :paginator="$payslips" />
                    </div>
                @endif
            </div>

            <div class="flex flex-col justify-between gap-space-md rounded-xl bg-surface-container-low p-space-md sm:flex-row sm:items-center">
                <div class="flex items-start gap-space-sm">
                    <span class="material-symbols-outlined text-headline-sm text-tertiary">info</span>
                    <p class="font-body-sm text-body-sm text-on-surface">
                        <strong>Paramètres appliqués :</strong> CNSS salariale {{ $rates['employeeCnss'] }} · CNSS patronale {{ $rates['employerCnss'] }} · CSS {{ $rates['css'] }} · Transport {{ $rates['transport']->format(3) }} TND. Ces valeurs sont configurables et à valider par la direction financière.
                    </p>
                </div>
                <button class="whitespace-nowrap font-label-md text-label-md font-semibold text-primary hover:underline focus:outline-none focus:ring-2 focus:ring-primary" type="button" onclick="openDialog('bracketDialog')">
                    Consulter le barème {{ $version }}
                </button>
            </div>
        </div>

        <div class="xl:col-span-4">
            <div class="sticky top-20 flex flex-col justify-between rounded-xl bg-surface-container-lowest p-space-lg shadow-lg" id="slipPrintArea">
                @if ($slip)
                    <div>
                        <div class="flex items-start justify-between gap-space-sm pb-space-md">
                            <div>
                                <span class="font-label-sm text-label-sm font-bold uppercase tracking-wider text-primary">Aperçu du bulletin</span>
                                <h2 class="mt-0.5 font-headline-md text-headline-md font-bold tracking-tight text-on-surface">Bulletin de Paie</h2>
                                <p class="font-body-sm text-body-sm text-on-surface-variant">Période : {{ $periodStart->format('d/m/Y') }} au {{ $periodEnd->format('d/m/Y') }}</p>
                            </div>
                            <x-ui.status-badge :tone="$selectedPayslip->statusTone()" :label="$selectedPayslip->statusLabel()" />
                        </div>

                        <div class="mb-space-md space-y-space-xs rounded-xl bg-surface-container-low p-space-md font-body-sm text-body-sm">
                            <div class="flex items-center justify-between gap-space-sm text-on-surface-variant">
                                <span>Société :</span>
                                <strong class="text-right text-on-surface">{{ $company['name'] }}</strong>
                            </div>
                            <div class="flex items-center justify-between gap-space-sm text-on-surface-variant">
                                <span>Affiliation CNSS :</span>
                                <span class="font-mono text-on-surface">{{ $company['cnss'] }}</span>
                            </div>
                            <div class="my-1 h-px bg-surface-container"></div>
                            <div class="flex items-center justify-between gap-space-sm text-on-surface">
                                <span class="font-medium">Salarié :</span>
                                <strong class="text-right font-semibold text-primary" id="slipName">{{ $slip['name'] }}</strong>
                            </div>
                            <div class="flex items-center justify-between gap-space-sm text-on-surface-variant">
                                <span>CIN :</span>
                                <span class="font-mono text-on-surface" id="slipCin">{{ $slip['cin'] }}</span>
                            </div>
                            <div class="flex items-center justify-between gap-space-sm text-on-surface-variant">
                                <span>Matricule :</span>
                                <span class="font-mono text-on-surface">{{ $slip['matricule'] }}</span>
                            </div>
                            <div class="flex items-center justify-between gap-space-sm text-on-surface-variant">
                                <span>Emploi :</span>
                                <span class="text-right font-medium text-on-surface" id="slipRole">{{ $slip['role'] }}</span>
                            </div>
                            <div class="flex items-center justify-between gap-space-sm text-on-surface-variant">
                                <span>Département :</span>
                                <span class="text-on-surface" id="slipDepartment">{{ $slip['department'] }}</span>
                            </div>
                        </div>

                        <div class="mb-space-md space-y-space-xs">
                            <div class="flex items-center justify-between py-1 font-body-sm text-body-sm">
                                <span class="text-on-surface">Salaire de base</span>
                                <span class="font-numeric-md text-numeric-md font-medium text-on-surface" id="slipBase">{{ $slip['base'] }}</span>
                            </div>
                            <div class="flex items-center justify-between py-1 font-body-sm text-body-sm text-on-surface-variant">
                                <span>Prime</span>
                                <span class="font-numeric-md text-numeric-md text-on-surface" id="slipPrime">{{ $slip['prime'] }}</span>
                            </div>
                            <div class="flex items-center justify-between py-1 font-body-sm text-body-sm text-on-surface-variant">
                                <span>Indemnité de transport</span>
                                <span class="font-numeric-md text-numeric-md text-on-surface" id="slipTransport">{{ $slip['transport'] }}</span>
                            </div>
                            <div class="my-2 space-y-1.5 rounded-lg bg-surface-container/60 p-2.5">
                                <div class="flex items-center justify-between font-body-sm text-body-sm">
                                    <span class="font-medium text-error">CNSS salariale ({{ $rates['employeeCnss'] }})</span>
                                    <span class="font-numeric-md text-numeric-md text-error" id="slipCnss">{{ $slip['cnss'] }}</span>
                                </div>
                                <div class="flex items-center justify-between font-body-sm text-body-sm">
                                    <span class="text-on-surface-variant">Base imposable IRPP</span>
                                    <span class="font-mono font-numeric-md text-numeric-md font-medium text-on-surface" id="slipTaxBase">{{ $slip['taxBase'] }}</span>
                                </div>
                                <div class="flex items-center justify-between font-body-sm text-body-sm">
                                    <span class="font-medium text-error">IRPP ({{ $version }})</span>
                                    <span class="font-numeric-md text-numeric-md text-error" id="slipIrpp">{{ $slip['irpp'] }}</span>
                                </div>
                                <div class="flex items-center justify-between font-body-sm text-body-sm">
                                    <span class="font-medium text-error">CSS ({{ $rates['css'] }})</span>
                                    <span class="font-numeric-md text-numeric-md text-error" id="slipCss">{{ $slip['css'] }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="mb-space-sm rounded-xl bg-primary-container p-space-md text-on-primary shadow-md">
                            <span class="font-label-sm text-label-sm uppercase tracking-wider opacity-90">Net à payer (virement bancaire)</span>
                            <div class="mt-1 flex items-baseline justify-between">
                                <span class="font-display-lg text-display-lg font-bold tracking-tight" id="slipNet">{{ $slip['net'] }}</span>
                                <span class="font-headline-sm text-headline-sm font-semibold opacity-95">TND</span>
                            </div>
                            <p class="mt-1 font-body-sm text-body-sm italic opacity-90" id="slipInWords">« {{ $slip['words'] }} »</p>
                        </div>
                        <div class="mb-space-md flex items-center justify-between px-1 font-label-sm text-label-sm text-on-surface-variant">
                            <span id="slipBank">Banque : {{ $slip['bank'] }}</span>
                            <span class="font-mono" id="slipRib">{{ $slip['rib'] }}</span>
                        </div>
                    </div>

                    <div class="flex flex-col gap-space-xs pt-space-xs no-print">
                        <button class="flex h-10 w-full items-center justify-center gap-space-xs rounded-xl bg-surface-container font-label-lg text-label-lg text-on-surface transition-colors hover:bg-surface-container-high focus:outline-none focus:ring-2 focus:ring-primary" type="button" onclick="window.print()">
                            <span class="material-symbols-outlined text-body-md text-primary">print</span>
                            <span>Imprimer le bulletin</span>
                        </button>
                        <a class="flex h-10 w-full items-center justify-center gap-space-xs rounded-xl bg-primary-container font-label-lg text-label-lg text-on-primary shadow-sm transition-all hover:bg-primary focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2" href="{{ $slip['downloadUrl'] }}">
                            <span class="material-symbols-outlined text-body-md">download</span>
                            <span>Télécharger le bulletin PDF</span>
                        </a>
                    </div>
                @else
                    <x-ui.empty-state icon="receipt_long" title="Aucun bulletin sélectionné" description="Sélectionnez un bulletin dans le registre pour afficher son aperçu." />
                @endif
            </div>
        </div>
    </div>
</div>

<x-ui.modal id="generatePayrollDialog" title="Générer les bulletins" description="Crée uniquement les bulletins manquants pour {{ $periodLabel }}.">
    <div class="space-y-space-sm font-body-sm text-body-sm text-on-surface-variant">
        <p><strong class="text-on-surface">{{ $missingCount }}</strong> collaborateur(s) actif(s) sans bulletin · <strong class="text-on-surface">{{ $activeEmployees }}</strong> actif(s) au total.</p>
        <p>Le salaire de base reprend le dernier bulletin connu ; à défaut, la grille du paramétrage {{ $version }} est utilisée. Les bulletins existants ne sont jamais modifiés.</p>
    </div>
    <form class="mt-space-lg flex flex-wrap justify-end gap-space-sm" method="POST" action="{{ route('admin.payroll.generate') }}">
        @csrf
        <input name="period" type="hidden" value="{{ $period }}"/>
        <button class="rounded-xl bg-surface-container px-space-md py-2 font-label-md text-label-md text-on-surface transition-colors hover:bg-surface-container-high focus:outline-none focus:ring-2 focus:ring-primary" type="button" onclick="document.getElementById('generatePayrollDialog').close()">Annuler</button>
        <button class="rounded-xl bg-primary-container px-space-lg py-2 font-label-md text-label-md text-on-primary shadow-sm transition-colors hover:bg-primary focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50" type="submit" @disabled($missingCount === 0 || $activeEmployees === 0)>
            {{ $missingCount === 0 ? 'Tous les bulletins existent' : 'Générer '.$missingCount.' bulletin(s)' }}
        </button>
    </form>
</x-ui.modal>

<x-ui.modal id="closePayrollDialog" title="Clôturer la période {{ $periodLabel }}" description="Les bulletins non payés seront marqués comme payés.">
    <div class="space-y-space-sm font-body-sm text-body-sm text-on-surface-variant">
        <p><strong class="text-on-surface">{{ $payslipCount - $paidCount }}</strong> bulletin(s) seront marqués payés avec la date du jour.</p>
        <p>Cette action est définitive depuis l'interface. Vérifiez le journal de paie et les virements avant de clôturer.</p>
    </div>
    <form class="mt-space-lg flex flex-wrap justify-end gap-space-sm" method="POST" action="{{ route('admin.payroll.close') }}">
        @csrf
        <input name="period" type="hidden" value="{{ $period }}"/>
        <button class="rounded-xl bg-surface-container px-space-md py-2 font-label-md text-label-md text-on-surface transition-colors hover:bg-surface-container-high focus:outline-none focus:ring-2 focus:ring-primary" type="button" onclick="document.getElementById('closePayrollDialog').close()">Annuler</button>
        <button class="rounded-xl bg-primary-container px-space-lg py-2 font-label-md text-label-md text-on-primary shadow-sm transition-colors hover:bg-primary focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2" type="submit">
            Clôturer et marquer payé
        </button>
    </form>
</x-ui.modal>

<x-ui.modal id="bracketDialog" title="Barème IRPP · paramétrage {{ $version }}" description="Tranches annuelles appliquées après abattement de {{ $rates['annualDeduction']->format(0) }} TND par part fiscale.">
    <div class="space-y-2">
        @foreach ($brackets as $bracket)
            <div class="flex items-center justify-between rounded-lg bg-surface-container-low px-3 py-2 font-body-sm text-body-sm">
                <span class="text-on-surface">{{ $bracket['label'] }}</span>
                <span class="font-numeric-md text-numeric-md font-bold text-primary">{{ $bracket['rate'] }}</span>
            </div>
        @endforeach
    </div>
    <div class="mt-space-md space-y-1 rounded-lg bg-surface-container-low p-3 font-label-sm text-label-sm text-on-surface-variant">
        <p>CNSS salariale : {{ $rates['employeeCnss'] }} · CNSS patronale : {{ $rates['employerCnss'] }} · CSS : {{ $rates['css'] }} de la base imposable.</p>
        <p>Indemnité de transport : {{ $rates['transport']->format(3) }} TND, exclue du brut cotisable et de la base imposable selon le paramétrage actuel.</p>
        <p>Parts fiscales : conjoint et enfants selon le paramétrage. Valeurs à valider par la direction financière.</p>
    </div>
    <div class="mt-space-lg flex justify-end">
        <button class="rounded-lg bg-primary-container px-4 py-2 font-label-md text-label-md text-on-primary transition-colors hover:bg-primary focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2" type="button" onclick="document.getElementById('bracketDialog').close()">
            Fermer
        </button>
    </div>
</x-ui.modal>

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
    function openDialog(id) {
        const dialog = document.getElementById(id);

        if (dialog && !dialog.open) {
            dialog.showModal();
        }
    }

    document.querySelectorAll("dialog").forEach(function (dialog) {
        dialog.addEventListener("click", function (event) {
            if (event.target === dialog) {
                dialog.close();
            }
        });
    });

    const payrollSearch = document.getElementById("payrollSearch");
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
</script>
</x-admin-shell>
