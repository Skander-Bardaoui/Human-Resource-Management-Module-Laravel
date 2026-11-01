@php
    $familyLabels = [
        'celibataire' => 'Célibataire',
        'marie' => 'Chef de famille',
        'divorce' => 'Divorcé(e)',
        'veuf' => 'Veuf(ve)',
    ];
    $familyLabel = $familyLabels[$employee->family_status] ?? ($employee->family_status ?: 'Non renseigné');

    $rib = $latest?->rib ?: $employee->bank_account;
    $ribMasked = $rib ? '•••• '.substr((string) $rib, -4) : 'Non renseigné';

    $selectedPeriod = $selected ? \Carbon\Carbon::createFromFormat('Y-m', (string) $selected->period) : null;
    $periodStart = $selectedPeriod?->copy()->startOfMonth()->format('d/m/Y');
    $periodEnd = $selectedPeriod?->copy()->endOfMonth()->format('d/m/Y');
    $paidLabel = $selected?->paid_at?->locale('fr')->translatedFormat('d F Y')
        ?? ($selected?->status === 'paid' ? 'Versé' : 'En attente de versement');

    $canRequestAdvance = ! $hasOpenAdvance && $latest !== null && $maxAdvance >= $minAdvance;
    $money = fn ($value): string => $value instanceof \App\Support\Money
        ? $value->format(3)
        : number_format((float) $value, 3, ',', ' ');
    $failedForm = old('form');
@endphp

<x-collaborator-shell active="payslips" title="Mes Fiches de Paie">
    <div class="flex flex-col w-full">
        <div class="px-4 sm:px-margin py-space-xl flex flex-col gap-space-xl max-w-[1536px] w-full mx-auto">

            <!-- Top Action & Identity Bar -->
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-space-lg pb-space-sm">
                <div class="flex flex-col gap-1 max-w-2xl">
                    <div class="flex flex-wrap items-center gap-space-xs">
                        <span class="font-label-sm text-label-sm uppercase tracking-wider text-primary font-semibold bg-surface-container-high px-2 py-0.5 rounded-full">Portail RH Tunisie • Conformité Fiscale</span>
                        <span class="w-1.5 h-1.5 rounded-full bg-tertiary"></span>
                        <span class="font-label-sm text-label-sm text-on-surface-variant">Documents confidentiels</span>
                    </div>
                    <h1 class="font-headline-lg text-headline-lg text-on-surface font-bold tracking-tight">Mes Fiches de Paie &amp; Rémunération</h1>
                    <p class="font-body-md text-body-md text-on-surface-variant">
                        Historique confidentiel de vos bulletins de salaire certifiés, déclarations fiscales IRPP et demandes d'acompte.
                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-space-sm">
                    <a class="h-10 px-space-md rounded-xl bg-surface-container-lowest text-on-surface font-label-lg text-label-lg flex items-center gap-space-xs shadow-sm hover:bg-surface-container-high transition-all" href="{{ route('collaborator.documents') }}#demande-document">
                        <span class="material-symbols-outlined text-[18px] text-primary">description</span>
                        <span>Attestation de revenu</span>
                    </a>
                    <a class="h-10 px-space-md rounded-xl bg-primary-container text-on-primary font-label-lg text-label-lg flex items-center gap-space-xs shadow-sm hover:bg-primary-fill transition-all" href="#acompte-section">
                        <span class="material-symbols-outlined text-[18px]">payments</span>
                        <span>Demande d'Acompte</span>
                    </a>
                </div>
            </div>

            <!-- Highlights -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-space-md">
                <div class="p-space-lg rounded-xl bg-surface-container-lowest shadow-sm flex flex-col justify-between relative overflow-hidden group hover:shadow-md transition-all">
                    <div class="flex items-start justify-between">
                        <span class="font-label-md text-label-md text-on-surface-variant font-medium">Dernier Salaire Net</span>
                        <div class="w-8 h-8 rounded-lg bg-surface-container-high flex items-center justify-center text-primary">
                            <span class="material-symbols-outlined text-[18px]">account_balance_wallet</span>
                        </div>
                    </div>
                    <div class="my-space-md">
                        <div class="flex items-baseline gap-1.5">
                            <span class="font-display-lg text-display-lg text-on-surface font-bold tracking-tight">{{ $latest?->netLabel() ?? '—' }}</span>
                            <span class="font-label-md text-label-md text-on-surface-variant font-semibold">TND</span>
                        </div>
                        <div class="flex items-center gap-1.5 mt-1 text-tertiary">
                            <span class="material-symbols-outlined text-[14px]">event_repeat</span>
                            <span class="font-label-sm text-label-sm">{{ $latest?->periodLabel() ?? 'Aucun bulletin disponible' }}</span>
                        </div>
                    </div>
                    <div class="pt-space-xs flex items-center justify-between text-on-surface-variant font-label-sm text-label-sm">
                        <span>Banque réceptrice</span>
                        <span class="text-on-surface font-semibold truncate max-w-[150px]">{{ $latest?->bank ?: 'Non renseignée' }}</span>
                    </div>
                </div>

                <div class="p-space-lg rounded-xl bg-surface-container-lowest shadow-sm flex flex-col justify-between relative overflow-hidden group hover:shadow-md transition-all">
                    <div class="flex items-start justify-between">
                        <span class="font-label-md text-label-md text-on-surface-variant font-medium">Affiliation CNSS Employé</span>
                        <div class="w-8 h-8 rounded-lg bg-secondary-container flex items-center justify-center text-on-secondary-fixed">
                            <span class="material-symbols-outlined text-[18px]">shield</span>
                        </div>
                    </div>
                    <div class="my-space-md">
                        <div class="flex items-baseline gap-2">
                            <span class="font-headline-lg text-headline-lg text-on-surface font-bold tracking-tight">{{ $employee->cnss_number ?: '—' }}</span>
                            <span class="px-2 py-0.5 rounded-full bg-surface-container-high font-label-sm text-label-sm text-on-surface-variant font-medium">{{ number_format($cnssRate * 100, 2, ',', '') }}%</span>
                        </div>
                        <p class="font-body-sm text-body-sm text-on-surface-variant mt-1">
                            Cotisation salariale : <span class="font-numeric-md text-numeric-md font-semibold text-on-surface">{{ $latest ? $money($latest->cnss).' TND' : '—' }}</span>
                        </p>
                    </div>
                    <div class="pt-space-xs flex items-center justify-between text-on-surface-variant font-label-sm text-label-sm">
                        <span>Régime obligatoire</span>
                        <span class="text-tertiary font-semibold flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-tertiary"></span>
                            {{ $employee->hasCompleteCnssProfile() ? 'Profil complet' : 'À compléter RH' }}
                        </span>
                    </div>
                </div>

                <div class="p-space-lg rounded-xl bg-surface-container-lowest shadow-sm flex flex-col justify-between relative overflow-hidden group hover:shadow-md transition-all">
                    <div class="flex items-start justify-between">
                        <span class="font-label-md text-label-md text-on-surface-variant font-medium">Situation Fiscale</span>
                        <div class="w-8 h-8 rounded-lg bg-surface-container-high flex items-center justify-center text-on-secondary-container">
                            <span class="material-symbols-outlined text-[18px]">gavel</span>
                        </div>
                    </div>
                    <div class="my-space-md">
                        <div class="font-headline-sm text-headline-sm text-on-surface font-bold leading-tight">
                            {{ $familyLabel }}
                        </div>
                        <div class="flex flex-col gap-0.5 mt-1 font-body-sm text-body-sm text-on-surface-variant">
                            <span>Enfants à charge : <strong class="text-on-surface font-medium">{{ (int) $employee->children }}</strong></span>
                            <span>Parts fiscales : <strong class="text-on-surface font-medium">{{ number_format((float) $fiscalParts, 1, ',', '') }}</strong></span>
                        </div>
                    </div>
                    <div class="pt-space-xs flex items-center justify-between text-on-surface-variant font-label-sm text-label-sm">
                        <span>Barème fiscal</span>
                        <span class="text-primary font-semibold">IRPP tunisien</span>
                    </div>
                </div>

                <div class="p-space-lg rounded-xl bg-surface-container-lowest shadow-sm flex flex-col justify-between relative overflow-hidden group hover:shadow-md transition-all">
                    <div class="flex items-start justify-between">
                        <span class="font-label-md text-label-md text-on-surface-variant font-medium">Compte de Virement</span>
                        <div class="w-8 h-8 rounded-lg bg-surface-container-high flex items-center justify-center text-primary">
                            <span class="material-symbols-outlined text-[18px]">account_balance</span>
                        </div>
                    </div>
                    <div class="my-space-md">
                        <div class="font-headline-sm text-headline-sm text-on-surface font-semibold">{{ $latest?->bank ?: 'Banque non renseignée' }}</div>
                        <div class="font-numeric-md text-numeric-md tracking-wider font-semibold text-on-surface-variant mt-1">RIB {{ $ribMasked }}</div>
                        <div class="font-label-sm text-label-sm text-on-surface-variant">Compte utilisé pour les virements de paie</div>
                    </div>
                    <div class="pt-space-xs flex items-center justify-between text-on-surface-variant font-label-sm text-label-sm">
                        <span>Statut compte</span>
                        <span class="text-tertiary font-semibold flex items-center gap-1">
                            <span class="material-symbols-outlined text-[14px]">check_circle</span>{{ $rib ? 'Vérifié RH' : 'À compléter' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Workspace -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-space-lg items-start">

                <!-- Repository list -->
                <div class="lg:col-span-5 flex flex-col gap-space-md">
                    <div class="p-space-md rounded-xl bg-surface-container-lowest shadow-sm flex flex-wrap items-center justify-between gap-space-sm">
                        <div class="flex items-center gap-space-xs text-on-surface-variant">
                            <span class="material-symbols-outlined text-[18px]">filter_list</span>
                            <span class="font-label-sm text-label-sm">Filtrer par exercice</span>
                        </div>
                        <div class="flex flex-wrap items-center gap-1">
                            @foreach ($years as $year)
                                <a class="px-2.5 py-1 rounded-lg font-label-md text-label-md transition-all {{ (int) $year === $selectedYear ? 'text-primary font-semibold bg-surface-container-high' : 'text-on-surface-variant hover:text-on-surface hover:bg-surface-container-low' }}" href="{{ route('collaborator.payslips', ['annee' => $year]) }}">
                                    {{ $year }}
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <div class="flex items-center justify-between px-space-xs">
                        <span class="font-label-md text-label-md uppercase tracking-wider text-on-surface-variant font-semibold">Exercice {{ $selectedYear }}</span>
                        <span class="font-label-sm text-label-sm text-on-surface-variant">{{ $list->count() }} bulletin(s)</span>
                    </div>

                    @forelse ($list as $payslip)
                        @php $isSelected = $selected && $selected->id === $payslip->id; @endphp
                        <a class="p-space-md rounded-xl bg-surface-container-lowest shadow-sm hover:shadow-md flex flex-col gap-space-sm relative transition-all {{ $isSelected ? 'ring-2 ring-primary' : '' }}" href="{{ route('collaborator.payslips', ['annee' => $selectedYear, 'bulletin' => $payslip->id]) }}">
                            <div class="flex items-center justify-between gap-space-xs">
                                <div class="flex items-center gap-space-xs">
                                    <span class="material-symbols-outlined text-[20px] {{ $isSelected ? 'text-primary' : 'text-on-surface-variant' }}">description</span>
                                    <span class="font-headline-sm text-headline-sm text-on-surface {{ $isSelected ? 'font-semibold' : 'font-medium' }}">{{ $payslip->periodLabel() }}</span>
                                </div>
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full {{ $payslip->statusPillClasses() }} font-label-sm text-label-sm font-semibold">
                                    <span class="w-1.5 h-1.5 rounded-full bg-current opacity-70"></span>
                                    {{ $payslip->statusLabel() }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between text-on-surface-variant font-body-sm text-body-sm">
                                <span>
                                    @if ($payslip->paid_at)
                                        Payé le <strong class="text-on-surface font-medium">{{ $payslip->paid_at->format('d/m/Y') }}</strong>
                                    @else
                                        Période {{ $payslip->periodNumber() }}
                                    @endif
                                </span>
                                <div class="flex items-baseline gap-1">
                                    <span class="font-headline-sm text-headline-sm text-on-surface font-bold">{{ $payslip->netLabel() }}</span>
                                    <span class="font-label-sm text-label-sm text-on-surface-variant">TND</span>
                                </div>
                            </div>
                            <div class="flex items-center justify-between pt-space-xs text-on-surface-variant font-label-sm text-label-sm">
                                <span>Réf. {{ $payslip->matricule() }}-{{ $payslip->period }}</span>
                                <span class="text-primary font-semibold flex items-center gap-0.5">
                                    {{ $isSelected ? 'Affiché en aperçu' : 'Consulter' }}
                                    <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                                </span>
                            </div>
                        </a>
                    @empty
                        <x-collaborator.partials.empty-state
                            icon="receipt_long"
                            title="Aucun bulletin pour {{ $selectedYear }}"
                            text="Les bulletins générés par la paie apparaîtront ici dès qu'ils seront disponibles."
                        />
                    @endforelse

                    <div class="p-space-md rounded-xl bg-surface-container-low flex items-center justify-between text-on-surface-variant">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-[20px]">inventory_2</span>
                            <span class="font-body-sm text-body-sm">Total archivé : {{ $payslips->count() }} bulletin(s)</span>
                        </div>
                        <a class="text-primary font-label-md text-label-md font-semibold hover:underline" href="{{ route('collaborator.payslips') }}">Dernier bulletin</a>
                    </div>
                </div>

                <!-- Detail pane -->
                <div class="lg:col-span-7 flex flex-col gap-space-md">
                    @if ($selected)
                        <div class="rounded-xl bg-surface-container-lowest shadow-md overflow-hidden flex flex-col">
                            <div class="bg-surface-container px-space-lg py-space-sm flex flex-wrap items-center justify-between gap-space-xs">
                                <div class="flex items-center gap-2">
                                    <span class="material-symbols-outlined text-[18px] text-tertiary">verified</span>
                                    <span class="font-label-md text-label-md text-on-surface font-semibold">Bulletin de paie officiel</span>
                                    <span class="text-on-surface-variant font-label-sm text-label-sm">• {{ $selected->periodLabel() }}</span>
                                </div>
                                <div class="font-numeric-md text-numeric-md text-on-surface-variant">
                                    Réf. {{ $selected->matricule() }}-{{ $selected->period }}
                                </div>
                            </div>

                            <div class="p-space-xl flex flex-col gap-space-lg">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-space-lg pb-space-lg bg-surface-container-low p-space-lg rounded-xl">
                                    <div class="flex flex-col gap-1">
                                        <span class="font-label-sm text-label-sm uppercase tracking-wider text-primary font-bold">Employeur</span>
                                        <span class="font-headline-sm text-headline-sm text-on-surface font-bold">Carthage Tech Solutions SARL</span>
                                        <span class="font-body-sm text-body-sm text-on-surface-variant">Immeuble Le Grand Boulevard, Les Berges du Lac 2, Tunis</span>
                                        <div class="flex flex-col gap-0.5 mt-2 font-numeric-md text-numeric-md text-on-surface-variant">
                                            <span>Matricule Fiscal : <strong class="text-on-surface font-semibold">1428590/A/M/000</strong></span>
                                            <span>Code CNSS Employeur : <strong class="text-on-surface font-semibold">29481900-12</strong></span>
                                        </div>
                                    </div>
                                    <div class="flex flex-col gap-1 md:items-end md:text-right">
                                        <span class="font-label-sm text-label-sm uppercase tracking-wider text-tertiary font-bold">Salarié Bénéficiaire</span>
                                        <span class="font-headline-sm text-headline-sm text-on-surface font-bold">{{ $employee->name }}</span>
                                        <span class="font-body-sm text-body-sm text-on-surface-variant">Matricule : <strong class="text-on-surface">{{ $selected->matricule() }}</strong></span>
                                        <div class="flex flex-col gap-0.5 mt-2 font-numeric-md text-numeric-md text-on-surface-variant md:items-end">
                                            <span>Emploi : <strong class="text-on-surface font-semibold">{{ $employee->position ?: 'Collaborateur' }}</strong></span>
                                            <span>Département : <strong class="text-on-surface font-semibold">{{ $employee->departmentLabel() }}</strong></span>
                                            <span>CIN : <strong class="text-on-surface font-semibold">{{ $employee->cin ?: 'Non renseigné' }}</strong></span>
                                            <span>Période : <strong class="text-primary font-semibold">{{ $periodStart }} au {{ $periodEnd }}</strong></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex flex-col">
                                    <div class="flex flex-wrap items-center justify-between gap-1 pb-space-xs">
                                        <h3 class="font-headline-sm text-headline-sm text-on-surface font-semibold">Décomposition des Rubriques &amp; Cotisations</h3>
                                        <span class="font-label-sm text-label-sm text-on-surface-variant">Montants en Dinars Tunisiens (TND)</span>
                                    </div>

                                    <div class="overflow-x-auto">
                                        <table class="w-full text-left">
                                            <thead>
                                                <tr class="bg-surface-container-high text-on-surface-variant font-label-md text-label-md uppercase">
                                                    <th class="py-2.5 px-3 rounded-l-lg">Rubrique</th>
                                                    <th class="py-2.5 px-3 text-right">Base</th>
                                                    <th class="py-2.5 px-3 text-right">Taux</th>
                                                    <th class="py-2.5 px-3 text-right">Gains</th>
                                                    <th class="py-2.5 px-3 text-right rounded-r-lg">Retenues</th>
                                                </tr>
                                            </thead>
                                            <tbody class="font-body-md text-body-md divide-y divide-surface-container-high">
                                                <tr class="hover:bg-surface-container-low transition-colors">
                                                    <td class="py-2.5 px-3">
                                                        <span class="font-semibold text-on-surface">Salaire de Base Brut</span>
                                                        <div class="font-label-sm text-label-sm text-on-surface-variant">Rémunération mensuelle de base</div>
                                                    </td>
                                                    <td class="py-2.5 px-3 text-right font-numeric-md text-numeric-md text-on-surface-variant">{{ $money($selected->base_salary) }}</td>
                                                    <td class="py-2.5 px-3 text-right font-numeric-md text-numeric-md text-on-surface-variant">—</td>
                                                    <td class="py-2.5 px-3 text-right font-numeric-md text-numeric-md font-semibold text-on-surface">{{ $money($selected->base_salary) }}</td>
                                                    <td class="py-2.5 px-3 text-right font-numeric-md text-numeric-md text-on-surface-variant">—</td>
                                                </tr>
                                                <tr class="hover:bg-surface-container-low transition-colors">
                                                    <td class="py-2.5 px-3">
                                                        <span class="font-medium text-on-surface">Prime</span>
                                                        <div class="font-label-sm text-label-sm text-on-surface-variant">Prime de rendement / performance</div>
                                                    </td>
                                                    <td class="py-2.5 px-3 text-right font-numeric-md text-numeric-md text-on-surface-variant">Fixe</td>
                                                    <td class="py-2.5 px-3 text-right font-numeric-md text-numeric-md text-on-surface-variant">—</td>
                                                    <td class="py-2.5 px-3 text-right font-numeric-md text-numeric-md font-semibold text-on-surface">{{ $money($selected->prime) }}</td>
                                                    <td class="py-2.5 px-3 text-right font-numeric-md text-numeric-md text-on-surface-variant">—</td>
                                                </tr>
                                                <tr class="hover:bg-surface-container-low transition-colors">
                                                    <td class="py-2.5 px-3">
                                                        <span class="font-medium text-on-surface">Indemnité de Transport</span>
                                                        <div class="font-label-sm text-label-sm text-on-surface-variant">Convention collective sectorielle</div>
                                                    </td>
                                                    <td class="py-2.5 px-3 text-right font-numeric-md text-numeric-md text-on-surface-variant">Fixe</td>
                                                    <td class="py-2.5 px-3 text-right font-numeric-md text-numeric-md text-on-surface-variant">—</td>
                                                    <td class="py-2.5 px-3 text-right font-numeric-md text-numeric-md font-semibold text-on-surface">{{ $money($selected->transport_allowance) }}</td>
                                                    <td class="py-2.5 px-3 text-right font-numeric-md text-numeric-md text-on-surface-variant">—</td>
                                                </tr>
                                                <tr class="bg-surface-container-low">
                                                    <td class="py-2 px-3 font-label-md text-label-md font-bold text-on-surface uppercase" colspan="3">Total Salaire Brut</td>
                                                    <td class="py-2 px-3 text-right font-numeric-md text-numeric-md font-bold text-primary">{{ $money($selected->gross()) }}</td>
                                                    <td class="py-2 px-3 text-right font-numeric-md text-numeric-md text-on-surface-variant">—</td>
                                                </tr>
                                                <tr class="hover:bg-surface-container-low transition-colors">
                                                    <td class="py-2.5 px-3">
                                                        <span class="font-medium text-error">Cotisation CNSS Salarié</span>
                                                        <div class="font-label-sm text-label-sm text-on-surface-variant">Régime sécurité sociale obligatoire</div>
                                                    </td>
                                                    <td class="py-2.5 px-3 text-right font-numeric-md text-numeric-md text-on-surface-variant">{{ $money($selected->gross()) }}</td>
                                                    <td class="py-2.5 px-3 text-right font-numeric-md text-numeric-md text-on-surface-variant">{{ number_format($cnssRate * 100, 2, ',', '') }}%</td>
                                                    <td class="py-2.5 px-3 text-right font-numeric-md text-numeric-md text-on-surface-variant">—</td>
                                                    <td class="py-2.5 px-3 text-right font-numeric-md text-numeric-md font-semibold text-error">-{{ $money($selected->cnss) }}</td>
                                                </tr>
                                                <tr class="hover:bg-surface-container-low transition-colors">
                                                    <td class="py-2.5 px-3">
                                                        <span class="font-medium text-error">Retenue à la Source IRPP</span>
                                                        <div class="font-label-sm text-label-sm text-on-surface-variant">Base imposable après abattements</div>
                                                    </td>
                                                    <td class="py-2.5 px-3 text-right font-numeric-md text-numeric-md text-on-surface-variant">{{ $money($selected->taxable_base) }}</td>
                                                    <td class="py-2.5 px-3 text-right font-numeric-md text-numeric-md text-on-surface-variant">Barème TN</td>
                                                    <td class="py-2.5 px-3 text-right font-numeric-md text-numeric-md text-on-surface-variant">—</td>
                                                    <td class="py-2.5 px-3 text-right font-numeric-md text-numeric-md font-semibold text-error">-{{ $money($selected->irpp) }}</td>
                                                </tr>
                                                <tr class="hover:bg-surface-container-low transition-colors">
                                                    <td class="py-2.5 px-3">
                                                        <span class="font-medium text-error">Contribution Sociale Solidaire (CSS)</span>
                                                        <div class="font-label-sm text-label-sm text-on-surface-variant">Prélèvement légal solidarité nationale</div>
                                                    </td>
                                                    <td class="py-2.5 px-3 text-right font-numeric-md text-numeric-md text-on-surface-variant">—</td>
                                                    <td class="py-2.5 px-3 text-right font-numeric-md text-numeric-md text-on-surface-variant">1,00%</td>
                                                    <td class="py-2.5 px-3 text-right font-numeric-md text-numeric-md text-on-surface-variant">—</td>
                                                    <td class="py-2.5 px-3 text-right font-numeric-md text-numeric-md font-semibold text-error">-{{ $money($selected->css) }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="mt-space-md p-space-lg rounded-xl bg-primary-container text-on-primary flex flex-col md:flex-row items-center justify-between gap-space-md shadow-md">
                                        <div class="flex flex-col">
                                            <span class="font-label-sm text-label-sm uppercase tracking-wider text-on-primary-container font-bold">Net Viré sur votre compte {{ $selected->bank ?: '' }}</span>
                                            <span class="font-body-sm text-body-sm text-on-primary-container">{{ $paidLabel }} • Période {{ $selected->periodNumber() }}</span>
                                        </div>
                                        <div class="flex items-baseline gap-2">
                                            <span class="font-label-lg text-label-lg uppercase font-semibold">Net à Payer :</span>
                                            <span class="font-display-lg text-display-lg font-bold tracking-tight">{{ $selected->netLabel() }}</span>
                                            <span class="font-headline-sm text-headline-sm font-semibold">TND</span>
                                        </div>
                                    </div>

                                    <div class="mt-space-md flex flex-wrap items-center justify-between gap-2 font-label-sm text-label-sm text-on-surface-variant px-space-xs">
                                        <span>Code du travail tunisien : Art. 139 à 145. Bulletin généré électroniquement.</span>
                                        <span>Congés payés restants : <strong class="text-on-surface">{{ (int) $employee->token_leave_days }} j</strong></span>
                                    </div>
                                </div>
                            </div>

                            <div class="p-space-md bg-surface-container-high flex flex-wrap items-center justify-between gap-space-sm">
                                <div class="flex flex-wrap items-center gap-space-xs">
                                    <a class="h-9 px-space-md rounded-xl bg-primary-fill text-on-primary font-label-md text-label-md flex items-center gap-1.5 shadow-sm hover:opacity-90 transition-opacity" href="{{ route('collaborator.payslips.download', $selected) }}">
                                        <span class="material-symbols-outlined text-[16px]">file_download</span>
                                        <span>Télécharger le PDF</span>
                                    </a>
                                    <button class="h-9 px-space-md rounded-xl bg-surface-container-lowest text-on-surface font-label-md text-label-md flex items-center gap-1.5 shadow-sm hover:bg-surface transition-colors" onclick="window.print()" type="button">
                                        <span class="material-symbols-outlined text-[16px]">print</span>
                                        <span>Imprimer</span>
                                    </button>
                                </div>
                                <a class="h-9 px-space-md rounded-xl bg-surface-container-lowest text-on-surface-variant hover:text-on-surface font-label-md text-label-md flex items-center gap-1.5 shadow-sm hover:bg-surface transition-colors" href="{{ route('collaborator.documents') }}#demande-document">
                                    <span class="material-symbols-outlined text-[16px] text-primary">chat_bubble</span>
                                    <span>Poser une question aux RH</span>
                                </a>
                            </div>
                        </div>
                    @else
                        <div class="rounded-xl bg-surface-container-lowest shadow-md p-space-xl">
                            <x-collaborator.partials.empty-state
                                icon="receipt_long"
                                title="Aucun bulletin sélectionné"
                                text="Vos bulletins de paie générés par l'administration RH apparaîtront ici avec le détail des cotisations."
                            />
                        </div>
                    @endif
                </div>
            </div>

            <!-- Advance workflow -->
            <div class="p-space-xl rounded-xl bg-surface-container-lowest shadow-sm flex flex-col gap-space-lg scroll-mt-24" id="acompte-section">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-space-md">
                    <div class="flex flex-col gap-1">
                        <div class="flex items-center gap-space-xs">
                            <span class="material-symbols-outlined text-[20px] text-primary">request_quote</span>
                            <h2 class="font-headline-md text-headline-md text-on-surface font-bold">Demande d'Acompte sur Salaire</h2>
                        </div>
                        <p class="font-body-md text-body-md text-on-surface-variant">
                            Acompte plafonné à {{ number_format($advanceRatio, 0, ',', '') }}% du dernier salaire net, remboursé par retenue sur la paie suivante.
                        </p>
                    </div>
                    <div class="flex items-center gap-2 bg-surface-container-high px-space-md py-space-xs rounded-xl">
                        <span class="material-symbols-outlined text-[18px] text-tertiary">schedule</span>
                        <span class="font-label-md text-label-md text-on-surface">Période en cours : <strong class="text-tertiary">{{ $currentPeriodLabel }}</strong></span>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-space-md">
                    <div class="p-space-md rounded-xl bg-surface-container-low flex flex-col gap-1">
                        <span class="font-label-sm text-label-sm text-on-surface-variant uppercase font-semibold">Plafond Maximum Autorisé</span>
                        <div class="flex items-baseline gap-1.5">
                            <span class="font-headline-lg text-headline-lg font-bold text-on-surface">{{ $money($maxAdvance) }}</span>
                            <span class="font-label-md text-label-md text-on-surface-variant">TND</span>
                        </div>
                        <span class="font-body-sm text-body-sm text-on-surface-variant">{{ number_format($advanceRatio, 0, ',', '') }}% du dernier salaire net</span>
                    </div>
                    <div class="p-space-md rounded-xl bg-surface-container-low flex flex-col gap-1">
                        <span class="font-label-sm text-label-sm text-on-surface-variant uppercase font-semibold">Mode de Récupération</span>
                        <div class="font-headline-sm text-headline-sm font-semibold text-on-surface">Prélèvement Bulletin Suivant</div>
                        <span class="font-body-sm text-body-sm text-on-surface-variant">Retenue automatique sur la paie suivante</span>
                    </div>
                    <div class="p-space-md rounded-xl bg-surface-container-low flex flex-col gap-1">
                        <span class="font-label-sm text-label-sm text-on-surface-variant uppercase font-semibold">Compte Destination</span>
                        <div class="font-headline-sm text-headline-sm font-semibold text-on-surface">{{ $latest?->bank ?: 'Compte non renseigné' }}</div>
                        <span class="font-body-sm text-body-sm text-on-surface-variant">RIB {{ $ribMasked }}</span>
                    </div>
                </div>

                @if ($hasOpenAdvance)
                    <div class="p-space-sm px-space-md rounded-lg bg-secondary-container text-on-secondary-fixed font-label-md text-label-md flex items-center gap-2">
                        <span class="material-symbols-outlined text-[18px]">hourglass_top</span>
                        <span>Une demande d'acompte est déjà en cours pour {{ $currentPeriodLabel }}. Vous pourrez en déposer une nouvelle dès qu'elle sera traitée.</span>
                    </div>
                @endif

                @if ($latest === null)
                    <div class="p-space-sm px-space-md rounded-lg bg-surface-container-high text-on-surface-variant font-label-md text-label-md flex items-center gap-2">
                        <span class="material-symbols-outlined text-[18px]">info</span>
                        <span>Aucun bulletin de paie disponible : le plafond d'acompte ne peut pas être calculé pour le moment.</span>
                    </div>
                @endif

                <form class="p-space-lg rounded-xl bg-surface-container-high flex flex-col lg:flex-row items-end justify-between gap-space-lg" method="POST" action="{{ route('collaborator.payslips.advance') }}">
                    @csrf
                    <input type="hidden" name="form" value="advance"/>
                    <div class="flex-1 w-full flex flex-col sm:flex-row items-start sm:items-end gap-space-md">
                        <div class="flex flex-col gap-space-xs w-full sm:w-64">
                            <label class="font-label-md text-label-md text-on-surface font-semibold" for="montant-acompte">Montant souhaité (TND)</label>
                            <div class="relative w-full">
                                <input class="w-full h-10 px-space-md rounded-lg bg-surface-container-lowest text-on-surface font-numeric-md text-numeric-md outline-none focus:ring-2 focus:ring-primary shadow-sm disabled:opacity-60 {{ $failedForm === 'advance' && $errors->has('amount') ? 'hrm-invalid' : '' }}" id="montant-acompte" name="amount" type="number" min="{{ $minAdvance }}" max="{{ $maxAdvance > 0 ? $maxAdvance : '' }}" step="50" value="{{ old('amount', $maxAdvance > 0 ? min(500, (int) $maxAdvance) : '') }}" required @disabled(! $canRequestAdvance)/>
                                <span class="absolute right-3 top-2.5 font-label-sm text-label-sm text-on-surface-variant font-semibold">TND</span>
                            </div>
                            @if ($failedForm === 'advance') @error('amount')<span class="font-label-sm text-label-sm text-error">{{ $message }}</span>@enderror @endif
                            <span class="font-label-sm text-[11px] text-on-surface-variant">Minimum {{ $money($minAdvance) }} TND • Maximum {{ $money($maxAdvance) }} TND</span>
                        </div>
                        <div class="flex flex-col gap-space-xs flex-1 w-full">
                            <label class="font-label-md text-label-md text-on-surface font-semibold" for="motif-acompte">Motif indicatif (facultatif / confidentiel RH)</label>
                            <input class="w-full h-10 px-space-md rounded-lg bg-surface-container-lowest text-on-surface font-body-md text-body-md outline-none focus:ring-2 focus:ring-primary shadow-sm disabled:opacity-60 {{ $failedForm === 'advance' && $errors->has('reason') ? 'hrm-invalid' : '' }}" id="motif-acompte" name="reason" type="text" maxlength="255" value="{{ old('reason') }}" placeholder="Ex : dépenses familiales imprévues, rentrée scolaire..." @disabled(! $canRequestAdvance)/>
                            @if ($failedForm === 'advance') @error('reason')<span class="font-label-sm text-label-sm text-error">{{ $message }}</span>@enderror @endif
                        </div>
                    </div>
                    <button class="w-full lg:w-auto h-10 px-space-xl rounded-xl bg-primary-fill text-on-primary font-label-lg text-label-lg font-semibold flex items-center justify-center gap-space-xs shadow-md hover:bg-on-primary-fixed-variant transition-colors shrink-0 disabled:opacity-50 disabled:pointer-events-none" type="submit" @disabled(! $canRequestAdvance)>
                        <span class="material-symbols-outlined text-[18px]">send</span>
                        <span>Transmettre à la DAF</span>
                    </button>
                </form>

                <div class="flex flex-col gap-space-sm">
                    <div class="flex items-center justify-between">
                        <h3 class="font-headline-sm text-headline-sm text-on-surface font-semibold">Historique de mes demandes</h3>
                        <span class="font-label-sm text-label-sm text-on-surface-variant">{{ $advances->count() }} demande(s)</span>
                    </div>

                    @forelse ($advances as $advance)
                        <div class="p-space-md rounded-xl bg-surface-container-low flex flex-col sm:flex-row sm:items-center justify-between gap-space-sm">
                            <div class="flex items-start gap-space-sm">
                                <div class="w-9 h-9 rounded-lg bg-surface-container-lowest text-primary flex items-center justify-center shrink-0 shadow-sm">
                                    <span class="material-symbols-outlined text-[18px]">payments</span>
                                </div>
                                <div class="flex flex-col">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="font-label-lg text-label-lg font-semibold text-on-surface">{{ $money($advance->amount) }} TND</span>
                                        <span class="px-2 py-0.5 rounded bg-surface-container-high text-on-surface-variant font-label-sm text-[10px]">{{ $advance->reference() }}</span>
                                    </div>
                                    <span class="font-body-sm text-body-sm text-on-surface-variant">
                                        {{ $advance->periodLabel() }}
                                        @if ($advance->reason) • {{ $advance->reason }} @endif
                                    </span>
                                    @if ($advance->decision_note)
                                        <span class="font-label-sm text-[11px] text-on-surface-variant">Décision RH : {{ $advance->decision_note }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full {{ $advance->statusPillClasses() }} font-label-sm text-label-sm font-semibold">
                                    <span class="w-1.5 h-1.5 rounded-full bg-current opacity-70"></span>
                                    {{ $advance->statusLabel() }}
                                </span>
                                <span class="font-label-sm text-label-sm text-on-surface-variant">Déposé le {{ $advance->created_at?->format('d/m/Y') }}</span>
                            </div>
                        </div>
                    @empty
                        <x-collaborator.partials.empty-state
                            icon="request_quote"
                            title="Aucune demande d'acompte"
                            text="Votre première demande apparaîtra ici avec son statut de validation par la DAF."
                        />
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    @if ($failedForm === 'advance' && $errors->any())
        <script>
            (() => {
                const section = document.getElementById('acompte-section');
                const amount = document.getElementById('montant-acompte');
                section?.scrollIntoView({ behavior: 'smooth', block: 'start' });
                amount?.focus({ preventScroll: true });
            })();
        </script>
    @endif
</x-collaborator-shell>
