@php
    $todayDate = $today->toDateString();
    $availableRate = $acquired > 0 ? (int) round($remaining / $acquired * 100) : 0;
    $consumedRate = max(0, 100 - $availableRate);
    $familyTaken = $leaves->where('type', 'famille')->where('status', 'approved')->count();

    $typeHints = [
        'annuel' => $remaining.' j restants',
        'maladie' => $sickRemaining.' j restants',
        'famille' => 'Mariage / Naissance / Décès',
        'sans-solde' => 'Autorisation préalable',
        'recup' => number_format($recupAvailable, 1, ',', ' ').' j disponibles',
    ];

    $statusStyles = [
        'pending' => ['classes' => 'bg-secondary-container text-on-secondary-fixed', 'dot' => 'bg-secondary'],
        'approved' => ['classes' => 'bg-tertiary-fixed text-on-tertiary-fixed', 'dot' => 'bg-tertiary-container'],
        'rejected' => ['classes' => 'bg-error-container text-on-error-container', 'dot' => 'bg-error'],
        'cancelled' => ['classes' => 'bg-surface-container text-on-surface-variant', 'dot' => 'bg-outline'],
    ];

    $tabs = [
        'all' => 'Toutes',
        'pending' => 'En attente',
        'approved' => 'Approuvées',
        'rejected' => 'Refusées',
        'cancelled' => 'Annulées',
    ];
@endphp

<x-collaborator-shell active="conges" title="Mes Congés & Absences">
    <div class="flex flex-col w-full">
        <div class="px-4 sm:px-margin py-space-lg flex flex-col gap-space-xl max-w-[1536px] mx-auto w-full">

            <!-- Hero / Header Section -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-space-lg bg-surface-container-lowest p-space-xl rounded-xl shadow-sm relative overflow-hidden">
                <div class="absolute -right-12 -top-12 w-64 h-64 bg-primary-container/5 rounded-full blur-3xl pointer-events-none"></div>
                <div class="flex flex-col gap-space-xs z-10">
                    <div class="flex flex-wrap items-center gap-space-xs">
                        <span class="px-2 py-0.5 rounded-full bg-secondary-container text-on-secondary-fixed text-label-sm font-label-sm uppercase tracking-wider font-semibold">
                            Réglementation TN — Art. 112 à 133
                        </span>
                        <span class="text-on-surface-variant font-label-sm text-label-sm">• Exercice {{ $year }}</span>
                    </div>
                    <h1 class="font-headline-lg text-headline-lg text-on-surface tracking-tight">
                        Mes Congés &amp; Demandes d'Absence
                    </h1>
                    <p class="font-body-md text-body-md text-on-surface-variant max-w-2xl">
                        Consultez vos soldes réglementaires (Loi n° 66-27 portant Code du Travail), planifiez vos départs en concertation avec l'équipe et suivez l'avancement de vos visas d'approbation.
                    </p>
                </div>
                <div class="flex items-center gap-space-sm z-10 shrink-0">
                    <button class="h-10 px-space-lg rounded-xl bg-primary-container text-on-primary font-label-lg text-label-lg flex items-center gap-space-xs hover:bg-primary-fill shadow-sm transition-all transform active:scale-95" id="btn-open-request-drawer" type="button">
                        <span class="material-symbols-outlined text-[20px]">add_circle</span>
                        <span>Déposer une Demande de Congé</span>
                    </button>
                </div>
            </div>

            <!-- Balance Overview Strip -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-space-lg">
                <!-- Card 1: Congé Annuel Payé -->
                <div class="p-space-xl rounded-xl bg-surface-container-lowest shadow-sm flex flex-col justify-between relative overflow-hidden group hover:shadow-md transition-all">
                    <div class="flex items-start justify-between">
                        <div class="flex flex-col">
                            <span class="text-on-surface-variant font-label-sm text-label-sm uppercase tracking-wider">Solde Principal</span>
                            <span class="text-on-surface font-headline-sm text-headline-sm mt-0.5">Congé Annuel Payé</span>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-primary-fixed flex items-center justify-center text-primary">
                            <span class="material-symbols-outlined text-[22px]">beach_access</span>
                        </div>
                    </div>
                    <div class="my-space-md">
                        <div class="flex items-baseline gap-space-xs">
                            <span class="font-display-lg text-display-lg text-primary tracking-tight" id="annual-balance-display">{{ $remaining }}.0</span>
                            <span class="font-headline-sm text-headline-sm text-on-surface-variant">Jours restants</span>
                        </div>
                        <div class="flex items-center justify-between text-on-surface-variant font-label-md text-label-md mt-1">
                            <span>{{ $acquired }}.0 j acquis</span>
                            <span class="text-error font-medium">{{ $takenAnnual }}.0 j pris</span>
                        </div>
                    </div>
                    <div class="w-full">
                        <div class="w-full h-2 rounded-full bg-surface-container-high overflow-hidden">
                            <div class="h-full bg-primary-container rounded-full transition-all duration-700" style="width: {{ $availableRate }}%;"></div>
                        </div>
                        <div class="flex justify-between items-center mt-1.5 font-label-sm text-label-sm text-on-surface-variant">
                            <span>Taux de consommation : {{ $consumedRate }}%</span>
                            <span class="text-tertiary-container font-semibold">{{ $availableRate }}% disponible</span>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Congé Maladie -->
                <div class="p-space-xl rounded-xl bg-surface-container-lowest shadow-sm flex flex-col justify-between group hover:shadow-md transition-all">
                    <div class="flex items-start justify-between">
                        <div class="flex flex-col">
                            <span class="text-on-surface-variant font-label-sm text-label-sm uppercase tracking-wider">Santé &amp; Prévoyance</span>
                            <span class="text-on-surface font-headline-sm text-headline-sm mt-0.5">Maladie Ordinaire</span>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-secondary-container flex items-center justify-center text-secondary">
                            <span class="material-symbols-outlined text-[22px]">healing</span>
                        </div>
                    </div>
                    <div class="my-space-md">
                        <div class="flex items-baseline gap-space-xs">
                            <span class="font-display-lg text-display-lg text-on-surface tracking-tight">{{ $sickTaken }}.0</span>
                            <span class="font-headline-sm text-headline-sm text-on-surface-variant">Jour(s) utilisé(s)</span>
                        </div>
                        <div class="text-on-surface-variant font-label-md text-label-md mt-1">
                            {{ $sickRemaining }}.0 jour(s) encore disponible(s) sur le droit maladie
                        </div>
                    </div>
                    <div class="p-space-xs px-space-sm rounded-lg bg-surface-container-low flex items-center gap-space-xs text-on-surface-variant font-label-sm text-label-sm">
                        <span class="material-symbols-outlined text-[16px] text-tertiary-container">verified</span>
                        <span>Dépôt certificat obligatoire sous 48h</span>
                    </div>
                </div>

                <!-- Card 3: Événements Familiaux -->
                <div class="p-space-xl rounded-xl bg-surface-container-lowest shadow-sm flex flex-col justify-between group hover:shadow-md transition-all">
                    <div class="flex items-start justify-between">
                        <div class="flex flex-col">
                            <span class="text-on-surface-variant font-label-sm text-label-sm uppercase tracking-wider">Droits Conventionnels</span>
                            <span class="text-on-surface font-headline-sm text-headline-sm mt-0.5">Événements Familiaux</span>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-secondary-fixed flex items-center justify-center text-on-secondary-fixed">
                            <span class="material-symbols-outlined text-[22px]">diversity_1</span>
                        </div>
                    </div>
                    <div class="my-space-md">
                        <div class="flex items-baseline gap-space-xs">
                            <span class="font-display-lg text-display-lg text-on-surface tracking-tight">{{ $familyTaken }}</span>
                            <span class="font-headline-sm text-headline-sm text-on-surface-variant">Congé(s) pris</span>
                        </div>
                        <div class="text-on-surface-variant font-label-md text-label-md mt-1 flex flex-wrap gap-1">
                            <span class="px-1.5 py-0.5 rounded bg-surface-container text-on-surface font-label-sm text-label-sm">Mariage (3j)</span>
                            <span class="px-1.5 py-0.5 rounded bg-surface-container text-on-surface font-label-sm text-label-sm">Naiss. (2j)</span>
                            <span class="px-1.5 py-0.5 rounded bg-surface-container text-on-surface font-label-sm text-label-sm">Décès (2-3j)</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-1 text-on-surface-variant font-label-sm text-label-sm">
                        <span class="material-symbols-outlined text-[16px]">info</span>
                        <span>Sans impact sur le solde annuel</span>
                    </div>
                </div>

                <!-- Card 4: Récupération -->
                <div class="p-space-xl rounded-xl bg-surface-container-lowest shadow-sm flex flex-col justify-between group hover:shadow-md transition-all">
                    <div class="flex items-start justify-between">
                        <div class="flex flex-col">
                            <span class="text-on-surface-variant font-label-sm text-label-sm uppercase tracking-wider">Compensation</span>
                            <span class="text-on-surface font-headline-sm text-headline-sm mt-0.5">Heures Sup. / Astreinte</span>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-tertiary-fixed flex items-center justify-center text-tertiary">
                            <span class="material-symbols-outlined text-[22px]">more_time</span>
                        </div>
                    </div>
                    <div class="my-space-md">
                        <div class="flex items-baseline gap-space-xs">
                            <span class="font-display-lg text-display-lg text-tertiary-container tracking-tight">{{ number_format($recupAvailable, 1, ',', ' ') }}</span>
                            <span class="font-headline-sm text-headline-sm text-on-surface-variant">Jour dispo</span>
                        </div>
                        <div class="text-on-surface-variant font-label-md text-label-md mt-1">
                            Calculé sur vos heures supplémentaires pointées en {{ $year }}
                        </div>
                    </div>
                    <div class="flex items-center justify-between font-label-sm text-label-sm">
                        <span class="text-on-surface-variant">Crédit récupération</span>
                        <a class="text-primary font-semibold hover:underline" href="{{ route('collaborator.attendance') }}">Voir le pointage</a>
                    </div>
                </div>
            </div>

            <!-- Team Overlap Banner -->
            <div class="rounded-xl bg-surface-container-lowest p-space-md px-space-lg shadow-sm flex flex-col md:flex-row items-start md:items-center justify-between gap-space-md">
                <div class="flex items-center gap-space-md">
                    <div class="w-10 h-10 rounded-xl bg-tertiary-fixed/30 flex items-center justify-center text-tertiary-container shrink-0">
                        <span class="material-symbols-outlined text-[22px]">groups</span>
                    </div>
                    <div class="flex flex-col">
                        <div class="flex flex-wrap items-center gap-space-xs">
                            <span class="font-headline-sm text-headline-sm text-on-surface">Continuité opérationnelle — {{ $employee->departmentLabel() }}</span>
                            @if ($teamOverlap['count'] > 0)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-secondary-container text-on-secondary-fixed font-label-sm text-label-sm font-semibold">
                                    <span class="w-1.5 h-1.5 rounded-full bg-secondary"></span>
                                    {{ $teamOverlap['count'] }} absence(s) planifiée(s)
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-tertiary-fixed text-on-tertiary-fixed font-label-sm text-label-sm font-semibold">
                                    <span class="w-1.5 h-1.5 rounded-full bg-tertiary-container"></span>
                                    Aucun conflit
                                </span>
                            @endif
                        </div>
                        <p class="font-body-sm text-body-sm text-on-surface-variant">
                            @if ($teamOverlap['count'] > 0)
                                Absences approuvées dans votre département jusqu'au {{ $teamOverlap['windowEnd']->locale('fr')->translatedFormat('d F Y') }} :
                                {{ $teamOverlap['leaves']->map(fn ($leave) => ($leave->employee?->name ?? 'Collaborateur').' ('.($leave->start_date?->format('d/m')).' → '.($leave->end_date?->format('d/m')).')')->join(', ') }}.
                            @else
                                Aucun congé approuvé dans votre département sur les 30 prochains jours. Votre planning peut être déposé sereinement.
                            @endif
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-space-sm w-full md:w-auto justify-end">
                    @if ($teamOverlap['employees']->isNotEmpty())
                        <div class="flex -space-x-2 overflow-hidden">
                            @foreach ($teamOverlap['employees'] as $colleague)
                                <div class="inline-block h-8 w-8 rounded-full ring-2 ring-surface bg-surface-container-high text-on-surface flex items-center justify-center font-label-sm text-[11px] font-bold" title="{{ $colleague->name }}">
                                    {{ $colleague->initials() }}
                                </div>
                            @endforeach
                            @if ($teamOverlap['count'] > $teamOverlap['employees']->count())
                                <div class="inline-block h-8 w-8 rounded-full ring-2 ring-surface bg-primary-container text-on-primary flex items-center justify-center font-label-sm text-[11px] font-bold">
                                    +{{ $teamOverlap['count'] - $teamOverlap['employees']->count() }}
                                </div>
                            @endif
                        </div>
                    @endif
                    <a class="px-space-md py-1.5 rounded-lg bg-surface-container-high text-on-surface font-label-md text-label-md hover:bg-surface-variant transition-colors" href="{{ route('collaborator.attendance') }}">
                        Planning équipe
                    </a>
                </div>
            </div>

            <!-- Main Workspace Split -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-space-xl items-start">

                <!-- Left Column: Request Form -->
                <div class="lg:col-span-5 flex flex-col gap-space-lg" id="request-card-container">
                    <div class="bg-surface-container-lowest p-space-xl rounded-xl shadow-sm flex flex-col gap-space-lg scroll-mt-24" id="nouvelle-demande">
                        <div class="flex items-center justify-between">
                            <div class="flex flex-col">
                                <h2 class="font-headline-sm text-headline-sm text-on-surface flex items-center gap-space-xs">
                                    <span class="material-symbols-outlined text-primary text-[20px]">edit_calendar</span>
                                    Nouvelle Demande d'Absence
                                </h2>
                                <span class="font-body-sm text-body-sm text-on-surface-variant">Décompte automatique des jours ouvrés (dimanche exclu)</span>
                            </div>
                            <span class="px-2 py-0.5 rounded-full bg-surface-container text-on-surface font-label-sm text-label-sm font-medium">Visa N+1</span>
                        </div>

                        <form class="flex flex-col gap-space-md" id="leave-request-form" method="POST" action="{{ route('collaborator.conges.store') }}" enctype="multipart/form-data" data-remaining="{{ $remaining }}" data-recup="{{ $recupAvailable }}" data-sick="{{ $sickRemaining }}">
                            @csrf

                            <!-- Type -->
                            <div class="flex flex-col gap-space-xs">
                                <label class="font-label-lg text-label-lg text-on-surface" for="leave-type-select">Type de congé ou motif réglementaire</label>
                                <select class="h-10 px-space-md rounded-lg bg-surface-container-low text-on-surface font-body-md text-body-md focus:outline-none focus:ring-2 focus:ring-primary-container transition-all @error('type') ring-2 ring-error @enderror" id="leave-type-select" name="type" required>
                                    @foreach ($types as $value => $label)
                                        <option value="{{ $value }}" @selected(old('type', 'annuel') === $value)>
                                            {{ $label }}{{ isset($typeHints[$value]) ? ' — '.$typeHints[$value] : '' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('type')<span class="font-label-sm text-label-sm text-error">{{ $message }}</span>@enderror
                            </div>

                            <!-- Dates -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-space-md">
                                <div class="flex flex-col gap-space-xs">
                                    <label class="font-label-lg text-label-lg text-on-surface" for="date-start">Date de début</label>
                                    <input class="h-10 px-space-md rounded-lg bg-surface-container-low text-on-surface font-body-md text-body-md focus:outline-none focus:ring-2 focus:ring-primary-container @error('start_date') ring-2 ring-error @enderror" id="date-start" name="start_date" type="date" value="{{ old('start_date', $todayDate) }}" min="{{ $todayDate }}" required/>
                                    @error('start_date')<span class="font-label-sm text-label-sm text-error">{{ $message }}</span>@enderror
                                </div>
                                <div class="flex flex-col gap-space-xs">
                                    <label class="font-label-lg text-label-lg text-on-surface" for="date-end">Date de fin</label>
                                    <input class="h-10 px-space-md rounded-lg bg-surface-container-low text-on-surface font-body-md text-body-md focus:outline-none focus:ring-2 focus:ring-primary-container @error('end_date') ring-2 ring-error @enderror" id="date-end" name="end_date" type="date" value="{{ old('end_date', $todayDate) }}" min="{{ $todayDate }}" required/>
                                    @error('end_date')<span class="font-label-sm text-label-sm text-error">{{ $message }}</span>@enderror
                                </div>
                            </div>

                            <!-- Live calculation -->
                            <div class="p-space-md rounded-xl bg-surface-container-high/60 flex flex-col gap-space-xs">
                                <div class="flex items-center justify-between">
                                    <span class="font-label-md text-label-md text-on-surface-variant">Jours ouvrés décomptés :</span>
                                    <span class="font-headline-sm text-headline-sm text-primary font-bold" id="calculated-days-count">1 Jour</span>
                                </div>
                                <div class="flex items-center justify-between pt-1 text-on-surface">
                                    <span class="font-label-md text-label-md text-on-surface-variant">Nouveau solde prévisionnel :</span>
                                    <span class="font-headline-sm text-headline-sm text-tertiary-container font-semibold" id="projected-balance">{{ $remaining }}.0 Jours</span>
                                </div>
                                <div class="flex items-center gap-1 font-label-sm text-label-sm text-on-surface-variant pt-1">
                                    <span class="material-symbols-outlined text-[14px] text-tertiary">check_circle</span>
                                    <span>Dimanche non décompté (régime 40h/semaine) — les jours fériés légaux ne sont pas gérés automatiquement.</span>
                                </div>
                            </div>

                            <!-- Motif -->
                            <div class="flex flex-col gap-space-xs">
                                <div class="flex items-center justify-between">
                                    <label class="font-label-lg text-label-lg text-on-surface" for="leave-comment">Motif / Commentaire de passation</label>
                                    <span class="font-label-sm text-label-sm text-on-surface-variant">{{ $employee->name }}</span>
                                </div>
                                <textarea class="p-space-md rounded-lg bg-surface-container-low text-on-surface font-body-md text-body-md focus:outline-none focus:ring-2 focus:ring-primary-container resize-none @error('reason') ring-2 ring-error @enderror" id="leave-comment" name="reason" rows="3" maxlength="1000" required placeholder="Ex : Congés de printemps. Relais des revues de code assuré par Mehdi Zghal.">{{ old('reason') }}</textarea>
                                @error('reason')<span class="font-label-sm text-label-sm text-error">{{ $message }}</span>@enderror
                            </div>

                            <!-- Attachment -->
                            <div class="flex flex-col gap-space-xs" id="justificatif-container">
                                <div class="flex items-center justify-between">
                                    <label class="font-label-lg text-label-lg text-on-surface" for="leave-attachment">Pièce justificative</label>
                                    <span class="font-label-sm text-label-sm text-on-surface-variant" id="justificatif-optional-badge">Optionnel pour congé payé</span>
                                </div>
                                <div class="p-space-md rounded-xl bg-surface-container-low flex items-center justify-between gap-space-md">
                                    <div class="flex items-center gap-space-sm overflow-hidden">
                                        <div class="w-8 h-8 rounded-lg bg-surface-container-highest flex items-center justify-center text-on-surface-variant shrink-0">
                                            <span class="material-symbols-outlined text-[18px]">attach_file</span>
                                        </div>
                                        <div class="flex flex-col truncate">
                                            <span class="font-label-md text-label-md text-on-surface truncate" id="attachment-name">Ajouter un document (PDF, PNG, JPG)</span>
                                            <span class="font-label-sm text-label-sm text-on-surface-variant">Max. 10 Mo — requis pour maladie &amp; événement familial</span>
                                        </div>
                                    </div>
                                    <label class="h-8 px-space-md rounded-lg bg-surface-container-high text-on-surface font-label-md text-label-md flex items-center gap-1 cursor-pointer hover:bg-surface-variant transition-colors shrink-0">
                                        <span>Parcourir</span>
                                        <input class="hidden" id="leave-attachment" name="attachment" type="file" accept=".pdf,.jpg,.jpeg,.png"/>
                                    </label>
                                </div>
                                @error('attachment')<span class="font-label-sm text-label-sm text-error">{{ $message }}</span>@enderror
                            </div>

                            <!-- Submit -->
                            <div class="pt-space-xs flex flex-col gap-space-xs">
                                <button class="w-full h-10 rounded-xl bg-primary-container text-on-primary font-label-lg text-label-lg flex items-center justify-center gap-space-xs hover:bg-primary-fill shadow-sm transition-all transform active:scale-95 disabled:opacity-60 disabled:pointer-events-none" id="submit-leave-btn" type="submit">
                                    <span class="material-symbols-outlined text-[18px]">send</span>
                                    <span data-submit-label>Transmettre pour Visa Manager (N+1)</span>
                                </button>
                                <p class="font-label-sm text-label-sm text-on-surface-variant text-center">
                                    Votre demande sera visible par le service RH et votre manager dès sa transmission.
                                </p>
                            </div>
                        </form>
                    </div>

                    <!-- Practical guide -->
                    <div class="bg-surface-container-lowest p-space-lg rounded-xl shadow-sm flex flex-col gap-space-sm">
                        <div class="flex items-center gap-space-xs text-on-surface font-headline-sm text-headline-sm">
                            <span class="material-symbols-outlined text-primary text-[20px]">policy</span>
                            <span>Rappels Conventionnels &amp; RH</span>
                        </div>
                        <ul class="flex flex-col gap-2 font-body-sm text-body-sm text-on-surface-variant">
                            <li class="flex items-start gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-primary mt-1.5 shrink-0"></span>
                                <span><strong>Délai de prévenance :</strong> déposer au moins 7 jours calendaires à l'avance pour tout congé supérieur à 3 jours.</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-primary mt-1.5 shrink-0"></span>
                                <span><strong>Certificat maladie :</strong> transmission du feuillet CNSS sous 48 heures au service RH.</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-primary mt-1.5 shrink-0"></span>
                                <span><strong>Fractionnement :</strong> le congé principal doit comporter au moins 12 jours ouvrables consécutifs.</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-primary mt-1.5 shrink-0"></span>
                                <span><strong>Annulation :</strong> une demande en attente peut être annulée, un congé approuvé peut être arrêté avant sa fin ; les jours non consommés sont réintégrés à votre solde.</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Right Column: History -->
                <div class="lg:col-span-7 flex flex-col gap-space-lg">
                    <div class="bg-surface-container-lowest rounded-xl shadow-sm overflow-hidden flex flex-col">
                        <!-- Tabs -->
                        <div class="p-space-lg flex flex-col sm:flex-row sm:items-center justify-between gap-space-md bg-surface-container-low/50">
                            <div class="flex items-center gap-1 bg-surface-container-high p-1 rounded-xl overflow-x-auto">
                                @foreach ($tabs as $key => $label)
                                    <button class="history-tab px-space-md py-1 rounded-lg font-label-md text-label-md transition-all whitespace-nowrap {{ $key === 'all' ? 'bg-surface text-on-surface shadow-xs' : 'text-on-surface-variant hover:text-on-surface' }}" data-filter="{{ $key }}" type="button">
                                        {{ $label }} ({{ $statusCounts[$key] }})
                                    </button>
                                @endforeach
                            </div>
                            <div class="flex items-center gap-space-xs text-on-surface-variant font-label-sm text-label-sm">
                                <span class="material-symbols-outlined text-[16px]">tune</span>
                                <span>Exercice : <strong class="text-on-surface">{{ $year }}</strong></span>
                            </div>
                        </div>

                        <!-- Table -->
                        <div class="overflow-x-auto">
                            <table class="w-full text-left font-body-md text-body-md text-on-surface">
                                <thead>
                                    <tr class="bg-surface-container-low text-on-surface-variant font-label-md text-label-md uppercase tracking-wider h-10">
                                        <th class="px-space-md font-semibold">Type &amp; Période</th>
                                        <th class="px-space-md font-semibold text-center">Durée</th>
                                        <th class="px-space-md font-semibold">Validation</th>
                                        <th class="px-space-md font-semibold">Statut</th>
                                        <th class="px-space-md font-semibold text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-surface-container-low">
                                    @forelse ($leaves as $leave)
                                        @php
                                            $style = $statusStyles[$leave->status] ?? $statusStyles['pending'];
                                            $reviewer = $leave->reviewer?->name ?? ($leave->isPending() ? 'Visa N+1 en attente' : 'Service RH');
                                            $detail = $leave->decision_note ?: $leave->reason;
                                        @endphp
                                        <tr class="hover:bg-surface-container-low/40 transition-colors" data-leave-row data-status="{{ $leave->status }}" title="{{ $detail }}">
                                            <td class="px-space-md py-space-md">
                                                <div class="flex flex-col">
                                                    <div class="flex items-center gap-1.5 flex-wrap">
                                                        <span class="material-symbols-outlined text-[18px] {{ $leave->typeColor() }}">{{ $leave->typeIcon() }}</span>
                                                        <span class="font-headline-sm text-headline-sm text-on-surface">{{ $leave->typeLabel() }}</span>
                                                        <span class="px-1.5 py-0.5 rounded bg-surface-container-high text-on-surface-variant font-label-sm text-[10px]">{{ $leave->reference() }}</span>
                                                    </div>
                                                    <span class="font-body-sm text-body-sm text-on-surface-variant mt-0.5">
                                                        @if ($leave->start_date->equalTo($leave->end_date))
                                                            {{ $leave->start_date->locale('fr')->translatedFormat('d F Y') }}
                                                        @else
                                                            {{ $leave->start_date->locale('fr')->translatedFormat('d M Y') }} → {{ $leave->end_date->locale('fr')->translatedFormat('d M Y') }}
                                                        @endif
                                                    </span>
                                                    <span class="font-label-sm text-label-sm text-outline mt-0.5">
                                                        Déposé le {{ $leave->created_at?->locale('fr')->translatedFormat('d M Y') ?? '—' }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="px-space-md py-space-md text-center">
                                                <span class="font-numeric-md text-numeric-md font-semibold text-on-surface">{{ $leave->working_days }} j</span>
                                                <span class="block font-label-sm text-label-sm text-on-surface-variant">Ouvrés</span>
                                            </td>
                                            <td class="px-space-md py-space-md">
                                                <div class="flex flex-col max-w-[220px]">
                                                    <span class="font-label-md text-label-md text-on-surface">{{ $reviewer }}</span>
                                                    <span class="font-label-sm text-label-sm text-on-surface-variant truncate">{{ $detail ?: 'Aucun commentaire' }}</span>
                                                    @if ($leave->reviewed_at)
                                                        <span class="font-label-sm text-label-sm text-outline mt-0.5">{{ $leave->reviewed_at->locale('fr')->translatedFormat('d M Y H:i') }}</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="px-space-md py-space-md">
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full {{ $style['classes'] }} font-label-sm text-label-sm font-semibold">
                                                    <span class="w-2 h-2 rounded-full {{ $style['dot'] }}"></span>
                                                    {{ $leave->statusLabel() }}
                                                </span>
                                            </td>
                                            <td class="px-space-md py-space-md text-right">
                                                <div class="flex items-center justify-end gap-1">
                                                    @if ($leave->attachment_path)
                                                        <a class="p-1.5 rounded-lg text-primary hover:bg-surface-container-high transition-colors" href="{{ route('collaborator.conges.attachment', $leave) }}" title="Télécharger le justificatif">
                                                            <span class="material-symbols-outlined text-[18px]">attach_file</span>
                                                        </a>
                                                    @endif
                                                    @if ($leave->isCancellable())
                                                        @php
                                                            $isStop = $leave->isApproved();
                                                            $stopMode = $isStop ? ($leave->isInProgress() ? 'progress' : 'future') : 'cancel';
                                                        @endphp
                                                        <form method="POST" action="{{ route('collaborator.conges.cancel', $leave) }}" data-days="{{ $leave->remainingWorkingDays() }}" data-mode="{{ $stopMode }}" data-period="{{ $leave->start_date?->format('d/m/Y') }} → {{ $leave->end_date?->format('d/m/Y') }}" data-stop-leave data-type="{{ $leave->typeLabel() }}">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button class="p-1.5 rounded-lg text-error hover:bg-error-container/20 transition-colors" title="{{ $isStop ? 'Arrêter mon congé' : 'Annuler ma demande' }}" type="submit">
                                                                <span class="material-symbols-outlined text-[18px]">{{ $isStop ? 'stop_circle' : 'cancel' }}</span>
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td class="px-space-md py-space-md" colspan="5">
                                                <x-collaborator.partials.empty-state
                                                    icon="event_busy"
                                                    title="Aucune demande de congé"
                                                    text="Déposez votre première demande depuis le formulaire à gauche : elle apparaîtra ici avec son statut de validation."
                                                />
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="p-space-md px-space-lg bg-surface-container-low flex flex-col sm:flex-row items-center justify-between gap-space-sm text-on-surface-variant font-label-sm text-label-sm">
                            <span>{{ $statusCounts['all'] }} demande(s) sur l'exercice {{ $year }}</span>
                            <span class="flex items-center gap-2">
                                <span class="inline-flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-secondary"></span>{{ $statusCounts['pending'] }} en attente</span>
                                <span class="inline-flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-tertiary-container"></span>{{ $statusCounts['approved'] }} approuvée(s)</span>
                            </span>
                        </div>
                    </div>

                    <!-- Legal info -->
                    <div class="p-space-lg rounded-xl bg-surface-container-low flex items-start gap-space-md">
                        <span class="material-symbols-outlined text-primary text-[24px] shrink-0 mt-0.5">account_balance</span>
                        <div class="flex flex-col gap-1">
                            <h3 class="font-headline-sm text-headline-sm text-on-surface">Cadre Légal des Droits à Congés en Tunisie</h3>
                            <p class="font-body-sm text-body-sm text-on-surface-variant leading-relaxed">
                                En application du <strong>Code du Travail tunisien</strong>, chaque salarié acquiert un minimum d'un jour ouvrable de congé payé par mois de service effectif, majoré selon l'ancienneté conventionnelle. Le décompte collaborateur exclut uniquement le dimanche ; les jours fériés officiels restent à valider avec le service RH lors de la pose.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('partials.stop-leave-dialog')

    <script>
        (() => {
            const form = document.getElementById('leave-request-form');
            const typeSelect = document.getElementById('leave-type-select');
            const dateStart = document.getElementById('date-start');
            const dateEnd = document.getElementById('date-end');
            const calculatedDays = document.getElementById('calculated-days-count');
            const projectedBalance = document.getElementById('projected-balance');
            const justificatifBadge = document.getElementById('justificatif-optional-badge');
            const attachmentInput = document.getElementById('leave-attachment');
            const attachmentName = document.getElementById('attachment-name');
            const submitBtn = document.getElementById('submit-leave-btn');
            const tabs = document.querySelectorAll('.history-tab');
            const rows = document.querySelectorAll('[data-leave-row]');
            const openRequestBtn = document.getElementById('btn-open-request-drawer');
            const requestCard = document.getElementById('request-card-container');

            if (!form) return;

            const remaining = parseFloat(form.dataset.remaining || '0');
            const recup = parseFloat(form.dataset.recup || '0');
            const sick = parseFloat(form.dataset.sick || '0');

            const badgeByType = {
                annuel: { text: 'Optionnel pour congé payé', classes: 'font-label-sm text-label-sm text-on-surface-variant' },
                maladie: { text: 'Certificat médical requis sous 48h', classes: 'font-label-sm text-label-sm text-error font-semibold' },
                famille: { text: "Justificatif d'état civil requis", classes: 'font-label-sm text-label-sm text-primary font-semibold' },
                'sans-solde': { text: 'Autorisation préalable N+1 + RH', classes: 'font-label-sm text-label-sm text-on-surface-variant' },
                recup: { text: 'Visa responsable astreinte', classes: 'font-label-sm text-label-sm text-on-surface-variant' },
            };

            // Whole-day count, Sunday is the only non-working day.
            const workingDays = (startValue, endValue) => {
                if (!startValue || !endValue) return 0;
                const start = new Date(startValue + 'T00:00:00');
                const end = new Date(endValue + 'T00:00:00');
                if (Number.isNaN(start.getTime()) || Number.isNaN(end.getTime()) || end < start) return 0;

                let count = 0;
                const cursor = new Date(start.getTime());
                while (cursor <= end) {
                    if (cursor.getDay() !== 0) count++;
                    cursor.setDate(cursor.getDate() + 1);
                }
                return count;
            };

            const updateSimulation = () => {
                const type = typeSelect.value;
                const days = workingDays(dateStart.value, dateEnd.value);

                calculatedDays.textContent = days + (days > 1 ? ' Jours' : ' Jour');

                if (type === 'annuel') {
                    const projected = remaining - days;
                    projectedBalance.textContent = Math.max(0, projected).toFixed(1) + ' Jours';
                    projectedBalance.classList.toggle('text-error', projected < 0);
                    projectedBalance.classList.toggle('text-tertiary-container', projected >= 0);
                } else if (type === 'recup') {
                    const projected = recup - days;
                    projectedBalance.textContent = Math.max(0, projected).toFixed(1) + ' Jours de récupération';
                    projectedBalance.classList.toggle('text-error', projected < 0);
                    projectedBalance.classList.toggle('text-tertiary-container', projected >= 0);
                } else if (type === 'maladie') {
                    projectedBalance.textContent = 'Solde maladie : ' + Math.max(0, sick - days).toFixed(1) + ' Jours (annuel préservé)';
                    projectedBalance.classList.remove('text-error');
                    projectedBalance.classList.add('text-tertiary-container');
                } else {
                    projectedBalance.textContent = remaining.toFixed(1) + ' Jours (solde annuel préservé)';
                    projectedBalance.classList.remove('text-error');
                    projectedBalance.classList.add('text-tertiary-container');
                }

                const badge = badgeByType[type] || badgeByType.annuel;
                justificatifBadge.textContent = badge.text;
                justificatifBadge.className = badge.classes;
            };

            dateStart.addEventListener('change', () => {
                if (dateEnd.value && dateEnd.value < dateStart.value) dateEnd.value = dateStart.value;
                dateEnd.min = dateStart.value;
                updateSimulation();
            });
            dateEnd.addEventListener('change', updateSimulation);
            typeSelect.addEventListener('change', updateSimulation);

            attachmentInput?.addEventListener('change', () => {
                attachmentName.textContent = attachmentInput.files?.[0]?.name || 'Ajouter un document (PDF, PNG, JPG)';
            });

            form.addEventListener('submit', () => {
                submitBtn.disabled = true;
                submitBtn.classList.add('opacity-70');
                const label = submitBtn.querySelector('[data-submit-label]');
                if (label) label.textContent = 'Transmission en cours...';
            });

            tabs.forEach((tab) => {
                tab.addEventListener('click', () => {
                    tabs.forEach((other) => {
                        other.classList.remove('bg-surface', 'text-on-surface', 'shadow-xs');
                        other.classList.add('text-on-surface-variant');
                    });
                    tab.classList.add('bg-surface', 'text-on-surface', 'shadow-xs');
                    tab.classList.remove('text-on-surface-variant');

                    const filter = tab.dataset.filter;
                    rows.forEach((row) => {
                        row.style.display = filter === 'all' || row.dataset.status === filter ? '' : 'none';
                    });
                });
            });

            openRequestBtn?.addEventListener('click', () => {
                requestCard?.scrollIntoView({ behavior: 'smooth', block: 'start' });
                requestCard?.classList.add('ring-2', 'ring-primary-container');
                setTimeout(() => requestCard?.classList.remove('ring-2', 'ring-primary-container'), 1500);
            });

            updateSimulation();
        })();
    </script>
</x-collaborator-shell>
