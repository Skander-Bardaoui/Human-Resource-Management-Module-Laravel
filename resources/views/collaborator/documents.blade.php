@php
    $categoryIcons = [
        'contrats' => 'contract',
        'identite' => 'badge',
        'paie' => 'receipt_long',
        'diplomes' => 'workspace_premium',
        'attestations' => 'verified',
    ];
    $categoryTones = [
        'contrats' => 'bg-primary-fixed text-primary',
        'identite' => 'bg-surface-container-high text-on-surface',
        'paie' => 'bg-secondary-container text-on-secondary-fixed',
        'diplomes' => 'bg-primary-fixed-dim text-primary',
        'attestations' => 'bg-secondary-fixed text-on-secondary-fixed',
    ];
    $sizeLabel = function (int $bytes): string {
        if ($bytes >= 1024 * 1024) {
            return number_format($bytes / 1024 / 1024, 1, ',', ' ').' Mo';
        }
        if ($bytes >= 1024) {
            return number_format($bytes / 1024, 0, ',', ' ').' Ko';
        }

        return $bytes.' o';
    };
    $familyLabels = [
        'celibataire' => 'Célibataire',
        'marie' => 'Marié(e)',
        'divorce' => 'Divorcé(e)',
        'veuf' => 'Veuf(ve)',
    ];
    $familyLabel = $familyLabels[$employee->family_status] ?? ($employee->family_status ?: 'Non renseigné');
    $failedForm = old('form');
@endphp

<x-collaborator-shell active="documents" title="Mes Documents & Profil">
    <div class="flex flex-col w-full">
        <div class="px-4 sm:px-margin py-space-xl flex flex-col gap-space-xl max-w-[1536px] mx-auto w-full">

            <!-- Header -->
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-space-lg">
                <div class="flex flex-col gap-space-xs">
                    <div class="flex flex-wrap items-center gap-space-sm">
                        <span class="px-2.5 py-0.5 rounded-full bg-secondary-container text-on-secondary-fixed text-label-sm font-label-sm uppercase tracking-wider font-semibold">Portail e-RH Certifié</span>
                        <span class="w-1.5 h-1.5 rounded-full bg-tertiary-container"></span>
                        <span class="text-label-sm font-label-sm text-on-surface-variant flex items-center gap-1">
                            <span class="material-symbols-outlined text-[14px] text-tertiary">verified</span> Coffre-fort privé
                        </span>
                    </div>
                    <h1 class="font-display-lg text-display-lg text-on-surface tracking-tight">Mes Documents &amp; Coffre-Fort Numérique</h1>
                    <p class="font-body-md text-body-md text-on-surface-variant max-w-3xl">
                        Accédez à vos documents contractuels et attestations certifiées, demandez une pièce RH ou générez immédiatement votre attestation de travail.
                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-space-sm shrink-0">
                    <button class="h-10 px-space-lg rounded-xl bg-surface-container text-on-surface font-label-lg text-label-lg flex items-center gap-space-xs hover:bg-surface-container-high transition-all shadow-sm" data-open-modal="updateModal" type="button">
                        <span class="material-symbols-outlined text-[18px] text-on-surface-variant">badge</span>
                        <span>Mettre à jour mes infos</span>
                    </button>
                    <a class="h-10 px-space-lg rounded-xl bg-primary-fill text-on-primary font-label-lg text-label-lg flex items-center gap-space-xs hover:bg-primary-container shadow-sm transition-all" href="#demande-document">
                        <span class="material-symbols-outlined text-[18px]">add_circle</span>
                        <span>Demander un Document RH</span>
                    </a>
                </div>
            </div>

            <!-- Identity card -->
            <div class="w-full rounded-xl bg-surface-container-lowest p-space-xl shadow-sm flex flex-col xl:flex-row gap-space-xl relative overflow-hidden">
                <div class="absolute -right-20 -bottom-20 w-80 h-80 rounded-full bg-primary/5 pointer-events-none blur-3xl"></div>

                <div class="flex items-start gap-space-lg xl:w-4/12 shrink-0">
                    <div class="relative">
                        <div class="w-20 h-20 rounded-xl shadow-sm bg-primary-fill flex items-center justify-center">
                            <span class="text-on-primary text-headline-lg font-bold">{{ $employee->initials() }}</span>
                        </div>
                        <span class="absolute -bottom-1 -right-1 w-6 h-6 rounded-full bg-tertiary-container text-on-tertiary flex items-center justify-center shadow-sm" title="{{ $employee->statusLabel() }}">
                            <span class="material-symbols-outlined text-[14px]">check</span>
                        </span>
                    </div>
                    <div class="flex flex-col min-w-0">
                        <div class="flex items-center gap-space-xs mb-0.5">
                            <h2 class="font-headline-md text-headline-md text-on-surface truncate">{{ $employee->name }}</h2>
                        </div>
                        <span class="font-label-lg text-label-lg text-primary font-semibold">{{ $employee->position ?: 'Collaborateur' }}</span>
                        <span class="font-body-sm text-body-sm text-on-surface-variant">{{ $employee->departmentLabel() }}</span>
                        <div class="flex flex-wrap items-center gap-space-xs mt-2">
                            <span class="px-2 py-0.5 rounded-full bg-surface-container-high text-on-surface font-label-sm text-label-sm">Matricule: <span class="font-numeric-md text-numeric-md font-semibold text-on-surface">{{ $employee->matricule() }}</span></span>
                            <span class="px-2 py-0.5 rounded-full bg-secondary-container text-on-secondary-fixed text-label-sm font-label-sm font-medium">{{ $employee->contract_type ?: 'Contrat' }} • {{ $employee->statusLabel() }}</span>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-2 gap-space-md xl:w-5/12 bg-surface-container-low p-space-lg rounded-xl">
                    <div class="flex flex-col">
                        <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">C.I.N Nationale</span>
                        <div class="flex items-baseline gap-1 mt-0.5">
                            <span class="font-numeric-md text-numeric-md font-bold text-on-surface">{{ $employee->cin ?: '—' }}</span>
                        </div>
                        <span class="font-label-sm text-label-sm text-tertiary-container font-medium mt-1 flex items-center gap-1">
                            <span class="material-symbols-outlined text-[13px]">verified_user</span> {{ $employee->cin ? 'Référence RH' : 'À compléter' }}
                        </span>
                    </div>
                    <div class="flex flex-col">
                        <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Immatriculation CNSS</span>
                        <span class="font-numeric-md text-numeric-md font-bold text-on-surface mt-0.5">{{ $employee->cnss_number ?: '—' }}</span>
                        <span class="font-label-sm text-label-sm text-on-surface-variant mt-1">{{ $employee->hasCompleteCnssProfile() ? 'Caisse Tunis / Régime Général' : 'Profil à compléter' }}</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Date d'Embauche</span>
                        <span class="font-numeric-md text-numeric-md font-semibold text-on-surface mt-0.5">{{ $employee->hire_date?->locale('fr')->translatedFormat('d F Y') ?? '—' }}</span>
                        <span class="font-label-sm text-label-sm text-on-surface-variant mt-1">{{ $employee->seniorityLabel() }}</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Situation &amp; Famille</span>
                        <span class="font-label-md text-label-md font-semibold text-on-surface mt-0.5">{{ $familyLabel }}</span>
                        <span class="font-label-sm text-label-sm text-on-surface-variant mt-1">{{ (int) $employee->children }} enfant(s) à charge</span>
                    </div>
                </div>

                <div class="flex flex-col justify-between gap-space-sm xl:w-3/12 bg-surface-container-high/40 p-space-lg rounded-xl">
                    <div class="flex flex-col gap-1">
                        <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider flex items-center gap-1">
                            <span class="material-symbols-outlined text-[15px] text-primary">contacts</span> Coordonnées Directes
                        </span>
                        <span class="font-body-sm text-body-sm text-on-surface truncate mt-1">{{ $employee->email ?: auth()->user()->email }}</span>
                        <span class="font-numeric-md text-numeric-md font-medium text-on-surface">{{ $employee->mobile ?: 'Téléphone non renseigné' }}</span>
                        <span class="font-label-sm text-label-sm text-on-surface-variant truncate">{{ $employee->address ?: 'Adresse non renseignée' }}</span>
                    </div>
                    <div class="flex flex-col gap-0.5 pt-2">
                        <span class="font-label-sm text-label-sm text-on-surface-variant flex items-center gap-1">
                            <span class="material-symbols-outlined text-[14px] text-error">emergency</span> Contact d'Urgence
                        </span>
                        <span class="font-body-sm text-body-sm font-semibold text-on-surface">{{ $employee->emergency_contact_name ?: 'Non renseigné' }}</span>
                        <span class="font-numeric-md text-numeric-md text-on-surface-variant">{{ $employee->emergency_contact_phone ?: '—' }}</span>
                    </div>
                </div>
            </div>

            <!-- Vault -->
            <div class="flex flex-col gap-space-lg">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-space-md">
                    <div class="flex items-center gap-1 p-1 bg-surface-container-low rounded-xl overflow-x-auto max-w-full">
                        <button class="vault-tab-btn px-space-md py-space-xs rounded-lg bg-surface-container-lowest text-primary font-label-md text-label-md shadow-sm font-semibold transition-all flex items-center gap-1.5 whitespace-nowrap" data-category="all" type="button">
                            <span>Tous les documents</span>
                            <span class="px-1.5 py-0.5 rounded-full bg-secondary-container text-on-secondary-fixed text-label-sm">{{ $documents->count() }}</span>
                        </button>
                        @foreach ($categoryCounts as $category => $count)
                            <button class="vault-tab-btn px-space-md py-space-xs rounded-lg text-on-surface-variant font-label-md text-label-md hover:text-on-surface transition-all flex items-center gap-1.5 whitespace-nowrap" data-category="{{ $category }}" type="button">
                                <span>{{ \App\Models\Document::CATEGORIES[$category] ?? $category }}</span>
                                <span class="px-1.5 py-0.5 rounded-full bg-surface-container-high text-on-surface-variant text-label-sm">{{ $count }}</span>
                            </button>
                        @endforeach
                    </div>

                    <div class="flex flex-wrap items-center gap-space-sm">
                        <div class="flex items-center gap-space-xs px-space-md py-1.5 rounded-xl bg-surface-container-lowest shadow-sm text-on-surface-variant">
                            <span class="material-symbols-outlined text-[18px]">search</span>
                            <input class="bg-transparent border-0 outline-none text-body-sm font-body-sm text-on-surface placeholder:text-on-surface-variant w-44" id="docSearchInput" placeholder="Filtrer un document..." type="text"/>
                        </div>
                        <div class="flex items-center p-1 bg-surface-container-low rounded-xl" role="group" aria-label="Disposition">
                            <button class="layout-btn p-1 rounded-lg bg-surface-container-lowest text-primary shadow-sm" data-layout="grid" title="Vue Grille" type="button">
                                <span class="material-symbols-outlined text-[18px]">grid_view</span>
                            </button>
                            <button class="layout-btn p-1 rounded-lg text-on-surface-variant hover:text-on-surface" data-layout="list" title="Vue Liste" type="button">
                                <span class="material-symbols-outlined text-[18px]">view_list</span>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-3 text-label-sm font-label-sm text-on-surface-variant">
                    <span class="inline-flex items-center gap-1"><span class="material-symbols-outlined text-[16px] text-primary">storage</span> {{ $sizeLabel((int) $totalSize) }} stockés</span>
                    @if ($expiringCount > 0)
                        <span class="inline-flex items-center gap-1 text-error font-semibold"><span class="material-symbols-outlined text-[16px]">warning</span> {{ $expiringCount }} document(s) expirant sous 60 jours</span>
                    @else
                        <span class="inline-flex items-center gap-1"><span class="material-symbols-outlined text-[16px] text-tertiary-container">task_alt</span> Aucune expiration proche</span>
                    @endif
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-space-lg" id="documentsContainer">
                    @forelse ($documents as $document)
                        <div class="doc-card group rounded-xl bg-surface-container-lowest p-space-lg shadow-sm hover:shadow-md transition-all flex flex-col justify-between" data-category="{{ $document->category }}" data-title="{{ strtolower($document->name.' '.$document->original_name.' '.$document->categoryLabel()) }}">
                            <div class="flex flex-col gap-space-md">
                                <div class="flex items-start justify-between gap-space-xs">
                                    <div class="w-12 h-12 rounded-xl {{ $categoryTones[$document->category] ?? 'bg-surface-container-high text-on-surface' }} flex items-center justify-center shrink-0">
                                        <span class="material-symbols-outlined text-[26px]">{{ $categoryIcons[$document->category] ?? 'description' }}</span>
                                    </div>
                                    <span class="px-2 py-0.5 rounded-full {{ $document->statusPillClasses() }} font-label-sm text-label-sm flex items-center gap-1 font-medium">
                                        <span class="w-1.5 h-1.5 rounded-full bg-current opacity-70"></span>
                                        {{ $document->statusLabel() }}
                                    </span>
                                </div>
                                <div class="flex flex-col">
                                    <h3 class="font-headline-sm text-headline-sm text-on-surface group-hover:text-primary transition-colors truncate">{{ $document->name }}</h3>
                                    <span class="font-numeric-md text-body-sm text-on-surface-variant font-mono truncate mt-0.5">{{ $document->original_name ?: basename($document->path) }}</span>
                                </div>
                                <div class="p-space-sm rounded-lg bg-surface-container-low flex flex-col gap-1">
                                    <div class="flex items-center justify-between text-body-sm font-body-sm">
                                        <span class="text-on-surface-variant">Catégorie :</span>
                                        <span class="font-body-sm text-on-surface font-medium truncate max-w-[160px]">{{ $document->categoryLabel() }}</span>
                                    </div>
                                    <div class="flex items-center justify-between text-body-sm font-body-sm">
                                        <span class="text-on-surface-variant">Ajouté le :</span>
                                        <span class="font-numeric-md text-on-surface font-medium">{{ $document->created_at?->format('d/m/Y') ?? '—' }}</span>
                                    </div>
                                    <div class="flex items-center justify-between text-body-sm font-body-sm">
                                        <span class="text-on-surface-variant">Taille :</span>
                                        <span class="font-numeric-md text-on-surface font-medium">{{ $document->sizeLabel() }}</span>
                                    </div>
                                    @if ($document->expires_at)
                                        <div class="flex items-center justify-between text-body-sm font-body-sm">
                                            <span class="text-on-surface-variant">Expiration :</span>
                                            <span class="font-numeric-md font-semibold {{ $document->isExpired() ? 'text-error' : ($document->isExpiringSoon() ? 'text-error' : 'text-tertiary-container') }}">
                                                {{ $document->expires_at->format('d/m/Y') }}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center gap-space-sm pt-space-md mt-space-sm">
                                <a class="flex-1 h-9 rounded-xl bg-surface-container text-on-surface font-label-md text-label-md hover:bg-surface-container-high transition-colors flex items-center justify-center gap-1" href="{{ route('collaborator.documents.download', $document) }}">
                                    <span class="material-symbols-outlined text-[16px]">download</span>
                                    <span>Télécharger</span>
                                </a>
                                <button class="w-9 h-9 rounded-xl bg-surface-container-low text-on-surface-variant hover:text-primary hover:bg-primary-fixed flex items-center justify-center transition-colors" data-preview data-preview-title="{{ $document->name }}" data-preview-meta="{{ $document->categoryLabel() }} • {{ $document->sizeLabel() }} • {{ $document->statusLabel() }}" data-preview-url="{{ route('collaborator.documents.download', $document) }}" title="Aperçu rapide" type="button">
                                    <span class="material-symbols-outlined text-[18px]">visibility</span>
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="md:col-span-2 lg:col-span-3">
                            <x-collaborator.partials.empty-state
                                icon="folder_off"
                                title="Votre coffre-fort est vide"
                                text="Les documents déposés par le service RH apparaîtront ici. Vous pouvez aussi générer immédiatement une attestation de travail."
                            />
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Express request hub -->
            <div class="w-full flex flex-col gap-space-lg pt-space-md scroll-mt-24" id="demande-document">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-space-sm">
                    <div class="flex flex-col gap-space-xs">
                        <div class="flex items-center gap-space-xs">
                            <span class="material-symbols-outlined text-primary text-[20px]">bolt</span>
                            <h2 class="font-headline-lg text-headline-lg text-on-surface tracking-tight">Génération &amp; Demande Express</h2>
                        </div>
                        <p class="font-body-sm text-body-sm text-on-surface-variant">
                            Générez votre attestation de travail immédiatement ou transmettez une demande au service RH.
                        </p>
                    </div>
                    <span class="hidden md:flex items-center gap-1 text-label-sm font-label-sm text-tertiary-container bg-surface-container-lowest px-space-md py-1 rounded-full shadow-sm">
                        <span class="w-2 h-2 rounded-full bg-tertiary-container animate-pulse"></span> Service RH disponible
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-space-lg">
                    <div class="rounded-xl bg-surface-container-lowest p-space-xl shadow-sm hover:shadow-md transition-all flex flex-col justify-between relative overflow-hidden group">
                        <div class="flex flex-col gap-space-md">
                            <div class="flex items-center justify-between">
                                <div class="w-10 h-10 rounded-xl bg-primary-fill text-on-primary flex items-center justify-center shadow-sm">
                                    <span class="material-symbols-outlined text-[22px]">quick_reference</span>
                                </div>
                                <span class="px-2 py-0.5 rounded-full bg-surface-container-high text-on-surface font-label-sm text-label-sm font-bold">Immédiat (0 min)</span>
                            </div>
                            <div class="flex flex-col gap-1">
                                <h3 class="font-headline-sm text-headline-sm text-on-surface group-hover:text-primary transition-colors">Attestation de Travail</h3>
                                <p class="font-body-sm text-body-sm text-on-surface-variant">
                                    Document PDF certifiant votre statut de {{ $employee->position ?: 'collaborateur' }} chez Carthage Tech Solutions.
                                </p>
                            </div>
                            <div class="flex items-center gap-space-xs text-label-sm font-label-sm text-on-surface-variant">
                                <span class="material-symbols-outlined text-[16px] text-tertiary-container">task_alt</span>
                                <span>Ajoutée automatiquement à votre coffre-fort</span>
                            </div>
                        </div>
                        <form class="pt-space-lg mt-space-md" method="POST" action="{{ route('collaborator.documents.attestation') }}">
                            @csrf
                            <button class="w-full h-10 px-space-md rounded-xl bg-primary-container text-on-primary font-label-lg text-label-lg hover:bg-primary-fill transition-all flex items-center justify-center gap-space-xs shadow-sm" type="submit">
                                <span class="material-symbols-outlined text-[18px]">bolt</span>
                                <span>Générer immédiatement (PDF)</span>
                            </button>
                        </form>
                    </div>

                    <div class="rounded-xl bg-surface-container-lowest p-space-xl shadow-sm hover:shadow-md transition-all flex flex-col justify-between group">
                        <div class="flex flex-col gap-space-md">
                            <div class="flex items-center justify-between">
                                <div class="w-10 h-10 rounded-xl bg-secondary-container text-on-secondary-fixed flex items-center justify-center shadow-sm">
                                    <span class="material-symbols-outlined text-[22px]">account_balance</span>
                                </div>
                                <span class="px-2 py-0.5 rounded-full bg-surface-container-high text-on-surface font-label-sm text-label-sm font-bold">Visa &amp; Banques</span>
                            </div>
                            <div class="flex flex-col gap-1">
                                <h3 class="font-headline-sm text-headline-sm text-on-surface group-hover:text-primary transition-colors">Salaire &amp; Domiciliation</h3>
                                <p class="font-body-sm text-body-sm text-on-surface-variant">
                                    Attestation de salaire et confirmation de titularisation pour dossier bancaire ou visa consulaire.
                                </p>
                            </div>
                            <div class="flex items-center gap-space-xs text-label-sm font-label-sm text-on-surface-variant">
                                <span class="material-symbols-outlined text-[16px] text-primary">schedule</span>
                                <span>Traitement RH sous 48h ouvrées</span>
                            </div>
                        </div>
                        <div class="pt-space-lg mt-space-md">
                            <button class="w-full h-10 px-space-md rounded-xl bg-surface-container text-on-surface font-label-lg text-label-lg hover:bg-surface-container-high transition-all flex items-center justify-center gap-space-xs" data-open-modal="requestModal" data-request-type="attestation_salaire" type="button">
                                <span class="material-symbols-outlined text-[18px]">assignment_turned_in</span>
                                <span>Demander avec motif</span>
                            </button>
                        </div>
                    </div>

                    <div class="rounded-xl bg-surface-container-lowest p-space-xl shadow-sm hover:shadow-md transition-all flex flex-col justify-between group">
                        <div class="flex flex-col gap-space-md">
                            <div class="flex items-center justify-between">
                                <div class="w-10 h-10 rounded-xl bg-tertiary-fixed text-on-tertiary-fixed-variant flex items-center justify-center shadow-sm">
                                    <span class="material-symbols-outlined text-[22px]">family_restroom</span>
                                </div>
                                <span class="px-2 py-0.5 rounded-full bg-surface-container-high text-on-surface font-label-sm text-label-sm font-bold">Fiscalité &amp; IRPP</span>
                            </div>
                            <div class="flex flex-col gap-1">
                                <h3 class="font-headline-sm text-headline-sm text-on-surface group-hover:text-primary transition-colors">Déclaration de Situation</h3>
                                <p class="font-body-sm text-body-sm text-on-surface-variant">
                                    Déclarez un changement d'adresse, de RIB ou une naissance pour ajuster vos abattements IRPP.
                                </p>
                            </div>
                            <div class="flex items-center gap-space-xs text-label-sm font-label-sm text-on-surface-variant">
                                <span class="material-symbols-outlined text-[16px] text-tertiary-container">tune</span>
                                <span>Précisez la situation à déclarer</span>
                            </div>
                        </div>
                        <div class="pt-space-lg mt-space-md">
                            <button class="w-full h-10 px-space-md rounded-xl bg-surface-container text-on-surface font-label-lg text-label-lg hover:bg-surface-container-high transition-all flex items-center justify-center gap-space-xs" data-open-modal="requestModal" data-request-type="declaration_situation" type="button">
                                <span class="material-symbols-outlined text-[18px]">edit_document</span>
                                <span>Déclarer un changement</span>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col gap-space-sm">
                    <div class="flex items-center justify-between">
                        <h3 class="font-headline-sm text-headline-sm text-on-surface font-semibold">Suivi de mes demandes</h3>
                        <span class="font-label-sm text-label-sm text-on-surface-variant">{{ $requests->count() }} demande(s)</span>
                    </div>
                    @forelse ($requests as $requestItem)
                        <div class="p-space-md rounded-xl bg-surface-container-lowest shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-space-sm">
                            <div class="flex items-start gap-space-sm">
                                <div class="w-9 h-9 rounded-lg bg-surface-container-low text-primary flex items-center justify-center shrink-0">
                                    <span class="material-symbols-outlined text-[18px]">description</span>
                                </div>
                                <div class="flex flex-col">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="font-label-lg text-label-lg font-semibold text-on-surface">{{ $requestItem->typeLabel() }}</span>
                                        <span class="px-2 py-0.5 rounded bg-surface-container-high text-on-surface-variant font-label-sm text-[10px]">{{ $requestItem->reference() }}</span>
                                    </div>
                                    <span class="font-body-sm text-body-sm text-on-surface-variant">{{ $requestItem->purpose ?: 'Aucun motif précisé' }}</span>
                                    <span class="font-label-sm text-[11px] text-on-surface-variant">Déposée le {{ $requestItem->created_at?->locale('fr')->translatedFormat('d F Y H:i') }}</span>
                                    @if ($requestItem->notes)
                                        <span class="font-label-sm text-[11px] text-on-surface-variant">RH : {{ $requestItem->notes }}</span>
                                    @endif
                                </div>
                            </div>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full {{ $requestItem->statusPillClasses() }} font-label-sm text-label-sm font-semibold shrink-0">
                                <span class="w-1.5 h-1.5 rounded-full bg-current opacity-70"></span>
                                {{ $requestItem->statusLabel() }}
                            </span>
                        </div>
                    @empty
                        <x-collaborator.partials.empty-state
                            icon="assignment"
                            title="Aucune demande de document"
                            text="Vos demandes d'attestations et déclarations apparaîtront ici avec leur statut de traitement."
                        />
                    @endforelse
                </div>
            </div>

            <!-- INPDP notice -->
            <div class="w-full rounded-xl bg-surface-container-low p-space-lg flex flex-col md:flex-row items-center justify-between gap-space-lg shadow-sm">
                <div class="flex items-start gap-space-md">
                    <div class="w-10 h-10 rounded-xl bg-tertiary-container text-on-tertiary flex items-center justify-center shrink-0 shadow-sm">
                        <span class="material-symbols-outlined text-[22px]">shield_lock</span>
                    </div>
                    <div class="flex flex-col">
                        <div class="flex flex-wrap items-center gap-space-xs">
                            <span class="font-label-lg text-label-lg text-on-surface font-bold">Sécurité &amp; Protection des Données INPDP (Tunisie)</span>
                            <span class="px-2 py-0.5 rounded bg-tertiary-container/15 text-tertiary-container text-label-sm font-label-sm font-semibold">Loi n° 2004-63</span>
                        </div>
                        <p class="font-body-sm text-body-sm text-on-surface-variant max-w-4xl mt-0.5">
                            Vos documents et données personnelles restent accessibles uniquement à vous et au service RH autorisé. Chaque téléchargement est vérifié côté serveur.
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-space-sm shrink-0">
                    <div class="flex flex-col text-right">
                        <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Stockage Sécurisé</span>
                        <span class="font-numeric-md text-numeric-md font-bold text-on-surface">Serveur RH local</span>
                    </div>
                    <span class="w-3 h-3 rounded-full bg-tertiary-container" title="Service opérationnel"></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Preview modal -->
    <div class="fixed inset-0 bg-black/60 backdrop-blur-[2px] z-50 hidden items-center justify-center p-space-lg" id="previewModal" data-modal>
        <div class="w-full max-w-lg rounded-xl bg-surface-container-lowest p-space-xl shadow-xl flex flex-col gap-space-lg">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-space-xs">
                    <span class="material-symbols-outlined text-primary text-[22px]">description</span>
                    <h3 class="font-headline-sm text-headline-sm text-on-surface" id="previewTitle">Aperçu du Document</h3>
                </div>
                <button class="w-8 h-8 rounded-lg flex items-center justify-center text-on-surface-variant hover:bg-surface-container hover:text-on-surface" data-close-modal="previewModal" type="button">
                    <span class="material-symbols-outlined text-[18px]">close</span>
                </button>
            </div>
            <div class="p-space-lg rounded-xl bg-surface-container-low flex flex-col gap-space-sm">
                <div class="flex justify-between items-center text-body-sm font-body-sm">
                    <span class="text-on-surface-variant">Détails :</span>
                    <span class="font-label-md text-on-surface font-semibold text-right" id="previewMeta">—</span>
                </div>
                <div class="flex justify-between items-center text-body-sm font-body-sm">
                    <span class="text-on-surface-variant">Accès :</span>
                    <span class="text-tertiary-container font-label-md font-medium flex items-center gap-1">
                        <span class="material-symbols-outlined text-[14px]">check_circle</span> Vérifié côté serveur
                    </span>
                </div>
                <div class="h-32 rounded-lg bg-surface-container-lowest p-space-md flex flex-col justify-center items-center text-center">
                    <span class="material-symbols-outlined text-outline-variant text-[40px] mb-1">picture_as_pdf</span>
                    <span class="font-label-sm text-label-sm text-on-surface-variant">Téléchargez le document pour l'ouvrir dans votre lecteur PDF</span>
                </div>
            </div>
            <div class="flex items-center justify-end gap-space-sm pt-space-xs">
                <button class="h-9 px-space-lg rounded-xl bg-surface-container text-on-surface font-label-md text-label-md hover:bg-surface-container-high transition-colors" data-close-modal="previewModal" type="button">Fermer</button>
                <a class="h-9 px-space-lg rounded-xl bg-primary-fill text-on-primary font-label-lg text-label-lg hover:bg-primary-container transition-all flex items-center gap-1 shadow-sm" href="#" id="previewDownload">
                    <span class="material-symbols-outlined text-[16px]">download</span>
                    <span>Télécharger le PDF</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Document request modal -->
    <div class="fixed inset-0 bg-black/60 backdrop-blur-[2px] z-50 hidden items-center justify-center p-space-lg" id="requestModal" data-modal>
        <div class="w-full max-w-lg rounded-xl bg-surface-container-lowest p-space-xl shadow-xl flex flex-col gap-space-lg">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-space-xs">
                    <span class="material-symbols-outlined text-primary text-[22px]">add_circle</span>
                    <h3 class="font-headline-sm text-headline-sm text-on-surface">Demande de Document RH</h3>
                </div>
                <button class="w-8 h-8 rounded-lg flex items-center justify-center text-on-surface-variant hover:bg-surface-container hover:text-on-surface" data-close-modal="requestModal" type="button">
                    <span class="material-symbols-outlined text-[18px]">close</span>
                </button>
            </div>
            <form class="flex flex-col gap-space-md" method="POST" action="{{ route('collaborator.documents.request') }}" id="requestForm">
                @csrf
                <input type="hidden" name="form" value="document_request"/>
                <div class="flex flex-col gap-space-xs">
                    <label class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider" for="request-type">Type de document *</label>
                    <select class="h-10 px-space-md rounded-lg bg-surface-container-lowest border-0 ring-1 ring-outline-variant text-on-surface font-body-md text-body-md focus:ring-2 focus:ring-primary outline-none {{ $failedForm === 'document_request' && $errors->has('type') ? 'hrm-invalid' : '' }}" id="request-type" name="type" required>
                        @foreach ($requestTypes as $value => $label)
                            <option value="{{ $value }}" @selected(old('type') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @if ($failedForm === 'document_request') @error('type')<span class="font-label-sm text-label-sm text-error">{{ $message }}</span>@enderror @endif
                </div>
                <div class="flex flex-col gap-space-xs">
                    <label class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider" for="request-purpose">Motif / précisions</label>
                    <textarea class="p-space-sm rounded-lg bg-surface-container-lowest border-0 ring-1 ring-outline-variant text-on-surface font-body-md text-body-md focus:ring-2 focus:ring-primary outline-none resize-none {{ $failedForm === 'document_request' && $errors->has('purpose') ? 'hrm-invalid' : '' }}" id="request-purpose" name="purpose" rows="3" maxlength="500" placeholder="Ex : dossier visa, crédit bancaire, changement d'adresse...">{{ old('purpose') }}</textarea>
                    @if ($failedForm === 'document_request') @error('purpose')<span class="font-label-sm text-label-sm text-error">{{ $message }}</span>@enderror @endif
                    <span class="font-label-sm text-[11px] text-on-surface-variant" id="request-purpose-hint">Obligatoire pour une déclaration de situation.</span>
                </div>
                <div class="flex items-center justify-end gap-space-sm pt-space-sm">
                    <button class="h-9 px-space-lg rounded-xl bg-surface-container text-on-surface font-label-md text-label-md hover:bg-surface-container-high transition-colors" data-close-modal="requestModal" type="button">Annuler</button>
                    <button class="h-9 px-space-lg rounded-xl bg-primary-fill text-on-primary font-label-lg text-label-lg hover:bg-primary-container transition-all shadow-sm" type="submit">Envoyer la demande</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Profile modal -->
    <div class="fixed inset-0 bg-black/60 backdrop-blur-[2px] z-50 hidden items-center justify-center p-space-lg" id="updateModal" data-modal>
        <div class="w-full max-w-lg rounded-xl bg-surface-container-lowest p-space-xl shadow-xl flex flex-col gap-space-lg max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-space-xs">
                    <span class="material-symbols-outlined text-primary text-[22px]">manage_accounts</span>
                    <h3 class="font-headline-sm text-headline-sm text-on-surface">Mise à jour du Dossier Collaborateur</h3>
                </div>
                <button class="w-8 h-8 rounded-lg flex items-center justify-center text-on-surface-variant hover:bg-surface-container hover:text-on-surface" data-close-modal="updateModal" type="button">
                    <span class="material-symbols-outlined text-[18px]">close</span>
                </button>
            </div>
            <form class="flex flex-col gap-space-md" method="POST" action="{{ route('collaborator.documents.profile') }}">
                @csrf
                @method('PATCH')
                <input type="hidden" name="form" value="profile_update"/>
                <div class="flex flex-col gap-space-xs">
                    <label class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider" for="profile-mobile">Téléphone personnel</label>
                    <input class="h-10 px-space-md rounded-lg bg-surface-container-lowest border-0 ring-1 ring-outline-variant text-on-surface font-numeric-md text-numeric-md focus:ring-2 focus:ring-primary outline-none {{ $failedForm === 'profile_update' && $errors->has('mobile') ? 'hrm-invalid' : '' }}" id="profile-mobile" name="mobile" type="text" maxlength="30" value="{{ old('mobile', $employee->mobile) }}" placeholder="+216 ..."/>
                    @if ($failedForm === 'profile_update') @error('mobile')<span class="font-label-sm text-label-sm text-error">{{ $message }}</span>@enderror @endif
                </div>
                <div class="flex flex-col gap-space-xs">
                    <label class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider" for="profile-emergency-name">Contact d'urgence (nom)</label>
                    <input class="h-10 px-space-md rounded-lg bg-surface-container-lowest border-0 ring-1 ring-outline-variant text-on-surface font-body-md text-body-md focus:ring-2 focus:ring-primary outline-none {{ $failedForm === 'profile_update' && $errors->has('emergency_contact_name') ? 'hrm-invalid' : '' }}" id="profile-emergency-name" name="emergency_contact_name" type="text" maxlength="120" value="{{ old('emergency_contact_name', $employee->emergency_contact_name) }}"/>
                    @if ($failedForm === 'profile_update') @error('emergency_contact_name')<span class="font-label-sm text-label-sm text-error">{{ $message }}</span>@enderror @endif
                </div>
                <div class="flex flex-col gap-space-xs">
                    <label class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider" for="profile-emergency-phone">Téléphone d'urgence</label>
                    <input class="h-10 px-space-md rounded-lg bg-surface-container-lowest border-0 ring-1 ring-outline-variant text-on-surface font-numeric-md text-numeric-md focus:ring-2 focus:ring-primary outline-none {{ $failedForm === 'profile_update' && $errors->has('emergency_contact_phone') ? 'hrm-invalid' : '' }}" id="profile-emergency-phone" name="emergency_contact_phone" type="text" maxlength="30" value="{{ old('emergency_contact_phone', $employee->emergency_contact_phone) }}"/>
                    @if ($failedForm === 'profile_update') @error('emergency_contact_phone')<span class="font-label-sm text-label-sm text-error">{{ $message }}</span>@enderror @endif
                </div>
                <div class="flex flex-col gap-space-xs">
                    <label class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider" for="profile-address">Adresse résidentielle</label>
                    <input class="h-10 px-space-md rounded-lg bg-surface-container-lowest border-0 ring-1 ring-outline-variant text-on-surface font-body-md text-body-md focus:ring-2 focus:ring-primary outline-none {{ $failedForm === 'profile_update' && $errors->has('address') ? 'hrm-invalid' : '' }}" id="profile-address" name="address" type="text" maxlength="255" value="{{ old('address', $employee->address) }}"/>
                    @if ($failedForm === 'profile_update') @error('address')<span class="font-label-sm text-label-sm text-error">{{ $message }}</span>@enderror @endif
                </div>
                <p class="font-label-sm text-[11px] text-on-surface-variant">
                    Les autres informations (CIN, CNSS, poste, contrat) sont gérées exclusivement par le service RH.
                </p>
                <div class="flex items-center justify-end gap-space-sm pt-space-md">
                    <button class="h-9 px-space-lg rounded-xl bg-surface-container text-on-surface font-label-md text-label-md hover:bg-surface-container-high transition-colors" data-close-modal="updateModal" type="button">Annuler</button>
                    <button class="h-9 px-space-lg rounded-xl bg-primary-fill text-on-primary font-label-lg text-label-lg hover:bg-primary-container transition-all shadow-sm" type="submit">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        (() => {
            const container = document.getElementById('documentsContainer');
            const searchInput = document.getElementById('docSearchInput');
            const tabs = document.querySelectorAll('.vault-tab-btn');
            const cards = () => document.querySelectorAll('.doc-card');

            let activeCategory = 'all';

            const applyFilters = () => {
                const query = (searchInput?.value || '').toLowerCase().trim();
                cards().forEach((card) => {
                    const matchesCategory = activeCategory === 'all' || card.dataset.category === activeCategory;
                    const matchesQuery = !query || (card.dataset.title || '').includes(query);
                    card.classList.toggle('hidden', !(matchesCategory && matchesQuery));
                });
            };

            tabs.forEach((tab) => {
                tab.addEventListener('click', () => {
                    tabs.forEach((other) => {
                        other.classList.remove('bg-surface-container-lowest', 'text-primary', 'shadow-sm', 'font-semibold');
                        other.classList.add('text-on-surface-variant');
                    });
                    tab.classList.add('bg-surface-container-lowest', 'text-primary', 'shadow-sm', 'font-semibold');
                    tab.classList.remove('text-on-surface-variant');
                    activeCategory = tab.dataset.category;
                    applyFilters();
                });
            });

            searchInput?.addEventListener('input', applyFilters);

            const layoutButtons = document.querySelectorAll('.layout-btn');
            layoutButtons.forEach((button) => {
                button.addEventListener('click', () => {
                    layoutButtons.forEach((other) => {
                        other.classList.remove('bg-surface-container-lowest', 'text-primary', 'shadow-sm');
                        other.classList.add('text-on-surface-variant');
                    });
                    button.classList.add('bg-surface-container-lowest', 'text-primary', 'shadow-sm');
                    button.classList.remove('text-on-surface-variant');

                    if (!container) return;
                    if (button.dataset.layout === 'list') {
                        container.className = 'flex flex-col gap-space-md';
                    } else {
                        container.className = 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-space-lg';
                    }
                    applyFilters();
                });
            });

            const openModal = (id) => {
                const modal = document.getElementById(id);
                if (!modal) return;
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            };
            const closeModal = (id) => {
                const modal = document.getElementById(id);
                if (!modal) return;
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            };

            @if ($errors->any() && in_array($failedForm, ['document_request', 'profile_update'], true))
                openModal(@json($failedForm === 'document_request' ? 'requestModal' : 'updateModal'));
            @endif

            document.querySelectorAll('[data-open-modal]').forEach((trigger) => {
                trigger.addEventListener('click', () => {
                    if (trigger.dataset.requestType) {
                        const select = document.getElementById('request-type');
                        if (select) select.value = trigger.dataset.requestType;
                        togglePurposeRequirement();
                    }
                    openModal(trigger.dataset.openModal);
                });
            });

            document.querySelectorAll('[data-close-modal]').forEach((trigger) => {
                trigger.addEventListener('click', () => closeModal(trigger.dataset.closeModal));
            });

            document.querySelectorAll('[data-modal]').forEach((modal) => {
                modal.addEventListener('click', (event) => {
                    if (event.target === modal) {
                        modal.classList.add('hidden');
                        modal.classList.remove('flex');
                    }
                });
            });

            document.querySelectorAll('[data-preview]').forEach((trigger) => {
                trigger.addEventListener('click', () => {
                    document.getElementById('previewTitle').textContent = trigger.dataset.previewTitle || 'Document';
                    document.getElementById('previewMeta').textContent = trigger.dataset.previewMeta || '—';
                    document.getElementById('previewDownload').href = trigger.dataset.previewUrl || '#';
                    openModal('previewModal');
                });
            });

            const requestType = document.getElementById('request-type');
            const requestPurpose = document.getElementById('request-purpose');
            const purposeHint = document.getElementById('request-purpose-hint');
            const togglePurposeRequirement = () => {
                if (!requestType || !requestPurpose) return;
                const isDeclaration = requestType.value === 'declaration_situation';
                requestPurpose.required = isDeclaration;
                if (purposeHint) {
                    purposeHint.textContent = isDeclaration
                        ? 'Obligatoire : précisez la situation à déclarer.'
                        : 'Facultatif, mais utile au traitement RH.';
                }
            };
            requestType?.addEventListener('change', togglePurposeRequirement);
            togglePurposeRequirement();

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape') {
                    document.querySelectorAll('[data-modal]').forEach((modal) => {
                        modal.classList.add('hidden');
                        modal.classList.remove('flex');
                    });
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
