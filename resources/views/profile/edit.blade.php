@php
    $user = auth()->user();
    $nameParts = preg_split('/\s+/', trim($user->name));
    $firstName = $nameParts[0] ?? $user->name;
    $initials = strtoupper(substr($nameParts[0] ?? '', 0, 1) . substr($nameParts[1] ?? '', 0, 1));
    $matricule = 'MAT-' . str_pad($user->id, 4, '0', STR_PAD_LEFT);
    $hasAccountErrors = $errors->any() || $errors->updatePassword->any() || $errors->userDeletion->any();
@endphp

<x-collaborator-shell active="profile" title="Mon Profil & Carrière">
    <div class="flex flex-col w-full">
        @if (in_array(session('status'), ['profile-updated', 'password-updated', 'verification-link-sent']))
            <div class="fixed top-20 right-4 z-[70] px-space-lg py-space-md rounded-xl bg-surface-container-lowest shadow-lg border border-surface-container-high flex items-center gap-space-sm transition-opacity duration-300" id="profile-toast" role="status">
                <span class="material-symbols-outlined text-[20px] text-tertiary">check_circle</span>
                <span class="font-label-lg text-label-lg text-on-surface">
                    @if (session('status') === 'profile-updated')
                        Vos informations ont été enregistrées.
                    @elseif (session('status') === 'password-updated')
                        Votre mot de passe a été mis à jour.
                    @else
                        Un nouveau lien de vérification a été envoyé.
                    @endif
                </span>
            </div>
        @endif

        <!-- Top Viewport Context Header -->
        <div class="px-margin pt-space-lg pb-space-md flex flex-col gap-space-sm bg-surface-container-low/60">
            <div class="flex items-center gap-space-xs text-on-surface-variant font-label-md text-label-md flex-wrap">
                <a class="hover:text-primary transition-colors flex items-center gap-1" href="{{ route('dashboard') }}">
                    <span class="material-symbols-outlined text-[15px]">home</span>
                    <span>Accueil</span>
                </a>
                <span class="text-outline-variant">/</span>
                <span class="hover:text-on-surface transition-colors">Espace Collaborateur</span>
                <span class="text-outline-variant">/</span>
                <span class="text-primary font-semibold">Mon Profil &amp; Carrière</span>
            </div>
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-space-md">
                <div>
                    <h1 class="font-headline-lg text-headline-lg text-on-surface tracking-tight flex items-center gap-space-xs flex-wrap">
                        <span>Mon Profil Professionnel &amp; Évolution de Carrière</span>
                        <span class="px-2 py-0.5 rounded-full bg-secondary-container text-on-secondary-fixed text-label-sm font-label-sm">RH-PROFIL-L5</span>
                    </h1>
                    <p class="font-body-md text-body-md text-on-surface-variant mt-0.5">
                        Consultez votre trajectoire professionnelle, vos compétences validées, votre historique salarial et préparez vos futurs entretiens d'évaluation.
                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-space-sm w-full md:w-auto shrink-0">
                    <button class="h-[38px] px-space-lg rounded-xl bg-surface-container-lowest text-on-surface font-label-lg text-label-lg shadow-sm hover:bg-surface-container-high transition-all flex items-center gap-space-xs" id="btn-update-info" type="button">
                        <span class="material-symbols-outlined text-[18px] text-secondary">edit_note</span>
                        <span>Mettre à jour mes informations</span>
                    </button>
                    <button class="h-[38px] px-space-lg rounded-xl bg-primary-fill text-on-primary font-label-lg text-label-lg shadow-sm hover:bg-primary-container transition-all flex items-center gap-space-xs" id="btn-request-review" type="button">
                        <span class="material-symbols-outlined text-[18px]">calendar_add_on</span>
                        <span>Demander un Point Carrière</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Primary Profile Details & KPI Strip -->
        <div class="px-margin pt-space-lg flex flex-col gap-space-lg">
            <!-- Profile Card (Elevated Canvas) -->
            <div class="bg-surface-container-lowest rounded-xl p-space-xl shadow-[0_1px_3px_0_rgba(15,23,42,0.05)] relative overflow-hidden">
                <div class="absolute -right-16 -top-16 w-64 h-64 bg-primary-fixed/30 rounded-full blur-3xl pointer-events-none"></div>
                <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-space-xl relative z-10">
                    <!-- Identity Section -->
                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-space-lg min-w-0">
                        <div class="relative shrink-0">
                            <div class="w-24 h-24 rounded-2xl bg-gradient-to-tr from-primary to-primary-container p-0.5 shadow-md">
                                <div class="w-full h-full rounded-[14px] bg-primary-container flex items-center justify-center overflow-hidden">
                                    <span class="text-on-primary font-headline-lg text-headline-lg font-bold">{{ $initials ?: 'U' }}</span>
                                </div>
                            </div>
                            <span class="absolute -bottom-1 -right-1 w-6 h-6 rounded-full bg-tertiary-container flex items-center justify-center text-on-tertiary shadow-sm" title="Statut Collaborateur Actif">
                                <span class="material-symbols-outlined text-[14px]">check</span>
                            </span>
                        </div>
                        <div class="flex flex-col gap-1 min-w-0">
                            <div class="flex flex-wrap items-center gap-space-xs">
                                <h2 class="font-headline-lg text-headline-lg text-on-surface font-bold">{{ $user->name }}</h2>
                                <span class="px-2.5 py-0.5 rounded-full bg-tertiary-container/10 text-tertiary font-label-sm text-label-sm flex items-center gap-1 font-semibold">
                                    <span class="w-1.5 h-1.5 rounded-full bg-tertiary"></span>
                                    CDI Cadre
                                </span>
                                <span class="px-2 py-0.5 rounded-full bg-surface-container-high text-on-surface-variant font-label-sm text-label-sm">
                                    Matricule: <span class="font-mono font-medium text-on-surface">{{ $matricule }}</span>
                                </span>
                            </div>
                            <div class="flex items-center gap-space-xs text-on-surface font-label-lg text-label-lg flex-wrap">
                                <span class="text-primary font-semibold">Lead Fullstack Engineer &amp; Architecture Lead</span>
                                <span class="text-outline-variant">•</span>
                                <span class="text-on-surface-variant">Pôle Ingénierie Logicielle</span>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-x-space-lg gap-y-1 mt-2 text-body-sm font-body-sm text-on-surface-variant">
                                <div class="flex items-center gap-1.5">
                                    <span class="material-symbols-outlined text-[16px] text-secondary">badge</span>
                                    <span>CIN : <span class="font-mono text-on-surface font-medium">09845211</span></span>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <span class="material-symbols-outlined text-[16px] text-secondary">security</span>
                                    <span>CNSS : <span class="font-mono text-on-surface font-medium">09482110-12</span></span>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <span class="material-symbols-outlined text-[16px] text-secondary">calendar_today</span>
                                    <span>Embauche : <span class="text-on-surface font-medium">15 Janvier 2020</span></span>
                                </div>
                                <div class="flex items-center gap-1.5 min-w-0">
                                    <span class="material-symbols-outlined text-[16px] text-secondary shrink-0">mail</span>
                                    <span class="truncate">{{ $user->email }}</span>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <span class="material-symbols-outlined text-[16px] text-secondary">call</span>
                                    <span>+216 71 820 400 (Poste 418)</span>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <span class="material-symbols-outlined text-[16px] text-secondary">pin_drop</span>
                                    <span>Immeuble Iris, Les Berges du Lac 2, Tunis</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Manager & Emergency Contact Mini-Panel -->
                    <div class="w-full lg:w-72 bg-surface-container-low rounded-xl p-space-md flex flex-col gap-space-sm shrink-0">
                        <div class="flex items-center justify-between">
                            <span class="text-label-sm font-label-sm uppercase tracking-wider text-on-surface-variant font-semibold">Manager Référent</span>
                            <span class="px-1.5 py-0.5 rounded bg-surface-container-high text-on-surface font-label-sm text-[10px]">N+1 Direct</span>
                        </div>
                        <div class="flex items-center gap-space-sm">
                            <div class="w-9 h-9 rounded-full bg-primary-container text-on-primary flex items-center justify-center font-bold text-label-md shadow-sm">
                                AB
                            </div>
                            <div class="flex flex-col">
                                <span class="font-label-lg text-label-lg font-semibold text-on-surface leading-tight">Amel Ben Salah</span>
                                <span class="font-body-sm text-body-sm text-on-surface-variant leading-tight">Directrice RH / VP People</span>
                            </div>
                        </div>
                        <div class="pt-space-xs mt-1 bg-surface-container/40 rounded px-2 py-1 flex items-center justify-between font-label-sm text-label-sm">
                            <span class="text-on-surface-variant">Contact Urgence RH :</span>
                            <span class="text-on-surface font-medium">+216 29 440 112 (Épouse)</span>
                        </div>
                    </div>
                </div>
                <!-- Quick KPI Strip -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-space-md mt-space-xl pt-space-lg bg-surface-container-low/40 -mx-space-xl -mb-space-xl px-space-xl pb-space-lg">
                    <!-- KPI 1 -->
                    <div class="flex items-center gap-space-sm p-space-sm rounded-xl bg-surface-container-lowest shadow-[0_1px_2px_rgba(15,23,42,0.04)]">
                        <div class="w-10 h-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-[20px]">hourglass_top</span>
                        </div>
                        <div class="flex flex-col min-w-0">
                            <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Ancienneté</span>
                            <span class="font-headline-sm text-headline-sm text-on-surface font-bold truncate">5 ans 2 mois</span>
                            <span class="font-label-sm text-label-sm text-secondary truncate">Échelon III • Pos. 2 (TIC TN)</span>
                        </div>
                    </div>
                    <!-- KPI 2 -->
                    <div class="flex items-center gap-space-sm p-space-sm rounded-xl bg-surface-container-lowest shadow-[0_1px_2px_rgba(15,23,42,0.04)]">
                        <div class="w-10 h-10 rounded-xl bg-tertiary-container/15 text-tertiary flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-[20px]">military_tech</span>
                        </div>
                        <div class="flex flex-col min-w-0">
                            <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Niveau Séniorité</span>
                            <span class="font-headline-sm text-headline-sm text-on-surface font-bold truncate">Lead / Staff (L5)</span>
                            <span class="font-label-sm text-label-sm text-tertiary truncate">Top 5% Pôle Architecture</span>
                        </div>
                    </div>
                    <!-- KPI 3 -->
                    <div class="flex items-center gap-space-sm p-space-sm rounded-xl bg-surface-container-lowest shadow-[0_1px_2px_rgba(15,23,42,0.04)]">
                        <div class="w-10 h-10 rounded-xl bg-secondary-container/30 text-on-secondary-fixed flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-[20px]">grade</span>
                        </div>
                        <div class="flex flex-col min-w-0">
                            <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Dernier Entretien (EIA)</span>
                            <div class="flex items-baseline gap-1">
                                <span class="font-headline-sm text-headline-sm text-on-surface font-bold">4.8</span>
                                <span class="font-label-sm text-label-sm text-on-surface-variant">/5</span>
                                <span class="ml-1 px-1.5 py-0.2 rounded bg-tertiary-container/10 text-tertiary font-label-sm text-[10px] font-bold">Excellent</span>
                            </div>
                            <span class="font-label-sm text-label-sm text-on-surface-variant">Février 2025 (Validé RH)</span>
                        </div>
                    </div>
                    <!-- KPI 4 -->
                    <div class="flex items-center gap-space-sm p-space-sm rounded-xl bg-surface-container-lowest shadow-[0_1px_2px_rgba(15,23,42,0.04)]">
                        <div class="w-10 h-10 rounded-xl bg-primary-fixed/60 text-on-primary-fixed-variant flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-[20px]">event_repeat</span>
                        </div>
                        <div class="flex flex-col min-w-0">
                            <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Prochaine Échéance</span>
                            <span class="font-headline-sm text-headline-sm text-on-surface font-bold truncate">Bilan Bisannuel</span>
                            <span class="font-label-sm text-label-sm text-primary font-medium truncate">Prévu en Avril 2025</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation Tabs for Profile & Career Modules -->
        <div class="mx-margin mt-space-lg bg-surface-container-low p-1.5 rounded-xl flex items-center overflow-x-auto gap-1">
            <button class="px-space-md py-space-xs rounded-xl bg-surface-container-lowest text-primary font-label-lg text-label-lg font-semibold shadow-sm flex items-center gap-space-xs shrink-0 transition-all">
                <span class="material-symbols-outlined text-[18px]">timeline</span>
                <span>Parcours &amp; Trajectoire</span>
            </button>
            <button class="px-space-md py-space-xs rounded-xl text-on-surface-variant hover:text-on-surface font-label-lg text-label-lg flex items-center gap-space-xs shrink-0 transition-all">
                <span class="material-symbols-outlined text-[18px]">verified</span>
                <span>Compétences &amp; Certifications</span>
                <span class="px-1.5 py-0.2 rounded-full bg-surface-container-highest text-on-surface-variant font-label-sm text-[10px]">12</span>
            </button>
            <button class="px-space-md py-space-xs rounded-xl text-on-surface-variant hover:text-on-surface font-label-lg text-label-lg flex items-center gap-space-xs shrink-0 transition-all">
                <span class="material-symbols-outlined text-[18px]">payments</span>
                <span>Historique Rémunération &amp; Promotions</span>
            </button>
            <button class="px-space-md py-space-xs rounded-xl text-on-surface-variant hover:text-on-surface font-label-lg text-label-lg flex items-center gap-space-xs shrink-0 transition-all">
                <span class="material-symbols-outlined text-[18px]">assignment_turned_in</span>
                <span>Entretiens Annuels (EIA)</span>
                <span class="w-2 h-2 rounded-full bg-tertiary"></span>
            </button>
            <button class="px-space-md py-space-xs rounded-xl text-on-surface-variant hover:text-on-surface font-label-lg text-label-lg flex items-center gap-space-xs shrink-0 transition-all">
                <span class="material-symbols-outlined text-[18px]">school</span>
                <span>Plan de Formation &amp; Développement</span>
            </button>
        </div>

        <!-- Main Content Dual-Column Layout (2/3 & 1/3) -->
        <div class="px-margin grid grid-cols-1 lg:grid-cols-12 gap-space-lg mb-margin mt-space-lg">
            <!-- MAIN COLUMN (8 cols) -->
            <div class="lg:col-span-8 flex flex-col gap-space-lg">
                <!-- Interactive Career Timeline -->
                <div class="bg-surface-container-lowest rounded-xl p-space-xl shadow-[0_1px_3px_0_rgba(15,23,42,0.05)]">
                    <div class="flex items-center justify-between pb-space-md mb-space-lg">
                        <div class="flex items-center gap-space-sm">
                            <div class="w-8 h-8 rounded-lg bg-primary-fixed flex items-center justify-center text-primary">
                                <span class="material-symbols-outlined text-[20px]">route</span>
                            </div>
                            <div>
                                <h3 class="font-headline-sm text-headline-sm text-on-surface font-bold">Trajectoire &amp; Historique de Carrière chez Carthage Tech</h3>
                                <p class="font-body-sm text-body-sm text-on-surface-variant">Progression continue depuis votre arrivée le 15 Janvier 2020</p>
                            </div>
                        </div>
                        <span class="px-2.5 py-1 rounded-full bg-surface-container-high text-on-surface-variant font-label-sm text-label-sm">
                            3 Postes Occupés
                        </span>
                    </div>
                    <!-- Timeline Steps Container -->
                    <div class="relative pl-6 space-y-space-xl before:absolute before:left-[11px] before:top-3 before:bottom-3 before:w-0.5 before:bg-surface-container-high">
                        <!-- Node 1: Present (Lead Fullstack Engineer) -->
                        <div class="relative group">
                            <div class="absolute -left-6 top-1 w-5 h-5 rounded-full bg-primary-fill flex items-center justify-center ring-4 ring-surface-container-lowest shadow-sm">
                                <span class="w-2 h-2 rounded-full bg-on-primary"></span>
                            </div>
                            <div class="bg-surface-container-low p-space-lg rounded-xl flex flex-col gap-space-sm hover:shadow-md transition-shadow">
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1">
                                    <div class="flex items-center gap-space-xs flex-wrap">
                                        <h4 class="font-headline-sm text-headline-sm text-on-surface font-bold">Lead Fullstack Engineer &amp; Tech Lead</h4>
                                        <span class="px-2 py-0.5 rounded-full bg-primary/10 text-primary font-label-sm text-[11px] font-semibold">Poste Actuel</span>
                                    </div>
                                    <span class="font-label-md text-label-md text-on-surface-variant">Mars 2023 – Présent (2 ans)</span>
                                </div>
                                <p class="font-body-md text-body-md text-on-surface">
                                    Promotion interne validée suite à la refonte de l'architecture distribuée Cloud-native v2. Pilotage technique direct d'une squad de 6 ingénieurs, garant de la scalabilité et du respect des normes de sécurité INPDP.
                                </p>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-space-sm pt-space-xs">
                                    <div class="flex items-center gap-2 text-body-sm font-body-sm text-on-surface-variant bg-surface-container-lowest p-2 rounded-lg">
                                        <span class="material-symbols-outlined text-[16px] text-tertiary">trending_up</span>
                                        <span>Position : <strong class="text-on-surface">Grille Cadre L5 - Pos. 2</strong></span>
                                    </div>
                                    <div class="flex items-center gap-2 text-body-sm font-body-sm text-on-surface-variant bg-surface-container-lowest p-2 rounded-lg">
                                        <span class="material-symbols-outlined text-[16px] text-primary">groups</span>
                                        <span>Encadrement : <strong class="text-on-surface">6 Développeurs + 2 PFEs</strong></span>
                                    </div>
                                </div>
                                <div class="flex flex-wrap items-center justify-between pt-space-xs gap-space-xs font-label-sm text-label-sm">
                                    <div class="flex items-center gap-1.5 text-tertiary">
                                        <span class="material-symbols-outlined text-[16px]">verified</span>
                                        <span>Validation CODIR &amp; RH (Avenant au contrat n°CT-2023-04)</span>
                                    </div>
                                    <a class="text-primary hover:underline flex items-center gap-1" href="#">
                                        <span>Consulter l'avenant</span>
                                        <span class="material-symbols-outlined text-[14px]">arrow_outward</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <!-- Node 2: Senior Software Engineer -->
                        <div class="relative group">
                            <div class="absolute -left-6 top-1 w-5 h-5 rounded-full bg-secondary flex items-center justify-center ring-4 ring-surface-container-lowest">
                                <span class="w-1.5 h-1.5 rounded-full bg-surface-container-lowest"></span>
                            </div>
                            <div class="bg-surface-container-lowest p-space-lg rounded-xl shadow-sm flex flex-col gap-space-sm">
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1">
                                    <h4 class="font-headline-sm text-headline-sm text-on-surface font-semibold">Senior Software Engineer</h4>
                                    <span class="font-label-md text-label-md text-on-surface-variant">Janvier 2021 – Février 2023 (2 ans 1 mois)</span>
                                </div>
                                <p class="font-body-md text-body-md text-on-surface-variant">
                                    Responsable du pôle Frontend et de la passerelle API Gateway (NestJS / GraphQL). Refonte ergonomique du portail SaaS B2B, amélioration des temps de chargement de 42% et mise en place des tests d'intégration automatisés.
                                </p>
                                <div class="flex flex-wrap items-center gap-2 pt-1">
                                    <span class="px-2 py-0.5 rounded bg-surface-container text-on-surface-variant font-label-sm text-[11px]">Next.js</span>
                                    <span class="px-2 py-0.5 rounded bg-surface-container text-on-surface-variant font-label-sm text-[11px]">TypeScript</span>
                                    <span class="px-2 py-0.5 rounded bg-surface-container text-on-surface-variant font-label-sm text-[11px]">GraphQL Gateway</span>
                                    <span class="px-2 py-0.5 rounded bg-surface-container text-on-surface-variant font-label-sm text-[11px]">Docker Swarm</span>
                                </div>
                                <div class="flex items-center gap-1.5 text-on-surface-variant font-label-sm text-label-sm pt-space-xs">
                                    <span class="material-symbols-outlined text-[16px] text-tertiary">check_circle</span>
                                    <span>Validation : Amel Ben Salah &amp; Tarek Jaziri (CTO)</span>
                                </div>
                            </div>
                        </div>
                        <!-- Node 3: Initial Fullstack Engineer -->
                        <div class="relative group">
                            <div class="absolute -left-6 top-1 w-5 h-5 rounded-full bg-secondary-container flex items-center justify-center ring-4 ring-surface-container-lowest">
                                <span class="w-1.5 h-1.5 rounded-full bg-secondary"></span>
                            </div>
                            <div class="bg-surface-container-lowest p-space-lg rounded-xl shadow-sm flex flex-col gap-space-sm">
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1">
                                    <h4 class="font-headline-sm text-headline-sm text-on-surface font-semibold">Fullstack Engineer (Recrutement CDI)</h4>
                                    <span class="font-label-md text-label-md text-on-surface-variant">15 Janvier 2020 – Décembre 2020 (1 an)</span>
                                </div>
                                <p class="font-body-md text-body-md text-on-surface-variant">
                                    Intégration initiale au sein de Carthage Tech Solutions sous contrat CDI Cadre. Validation avec félicitations de la période d'essai de 6 mois selon le Code du Travail tunisien. Contribution aux modules de facturation et conformité fiscale locale.
                                </p>
                                <div class="flex items-center gap-1.5 text-on-surface-variant font-label-sm text-label-sm pt-space-xs">
                                    <span class="material-symbols-outlined text-[16px] text-secondary">contract</span>
                                    <span>Contrat de travail initial réf. CTS-CDI-2020-019 archivé</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- 2025 Strategic OKRs / Performance Objectives -->
                <div class="bg-surface-container-lowest rounded-xl p-space-xl shadow-[0_1px_3px_0_rgba(15,23,42,0.05)]">
                    <div class="flex items-center justify-between pb-space-md mb-space-lg">
                        <div class="flex items-center gap-space-sm">
                            <div class="w-8 h-8 rounded-lg bg-tertiary-container/15 flex items-center justify-center text-tertiary">
                                <span class="material-symbols-outlined text-[20px]">track_changes</span>
                            </div>
                            <div>
                                <h3 class="font-headline-sm text-headline-sm text-on-surface font-bold">Objectifs Stratégiques &amp; OKRs 2025</h3>
                                <p class="font-body-sm text-body-sm text-on-surface-variant">Feuille de route convenue lors de l'EIA de Février 2025</p>
                            </div>
                        </div>
                        <span class="px-2.5 py-1 rounded-full bg-tertiary-container/10 text-tertiary font-label-sm text-label-sm font-semibold">
                            2/3 Atteints ou Validés
                        </span>
                    </div>
                    <div class="flex flex-col gap-space-md">
                        <!-- Objective 1 -->
                        <div class="p-space-md rounded-xl bg-surface-container-low flex flex-col gap-space-xs">
                            <div class="flex items-start justify-between gap-space-sm">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="px-2 py-0.5 rounded bg-primary-container text-on-primary font-label-sm text-[10px] font-bold">TECH</span>
                                        <h5 class="font-label-lg text-label-lg font-semibold text-on-surface">Modernisation de l'infrastructure Cloud &amp; CI/CD</h5>
                                    </div>
                                    <p class="font-body-sm text-body-sm text-on-surface-variant mt-1">
                                        Migration vers clusters EKS multi-régions et pipeline de déploiement continu à zéro temps d'arrêt (Zero-Downtime Deployment).
                                    </p>
                                </div>
                                <span class="font-numeric-md text-numeric-md font-bold text-primary shrink-0">75%</span>
                            </div>
                            <div class="w-full bg-surface-container-highest rounded-full h-2 mt-1">
                                <div class="bg-primary h-2 rounded-full transition-all duration-500" style="width: 75%"></div>
                            </div>
                            <div class="flex items-center justify-between text-label-sm font-label-sm text-on-surface-variant pt-1 flex-wrap gap-1">
                                <span class="text-tertiary font-medium flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-tertiary"></span>
                                    En bonne voie – Déploiement cible fin Mars 2025
                                </span>
                                <span>Pondération : 40%</span>
                            </div>
                        </div>
                        <!-- Objective 2 -->
                        <div class="p-space-md rounded-xl bg-surface-container-low flex flex-col gap-space-xs">
                            <div class="flex items-start justify-between gap-space-sm">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="px-2 py-0.5 rounded bg-tertiary-container text-on-tertiary font-label-sm text-[10px] font-bold">PEOPLE</span>
                                        <h5 class="font-label-lg text-label-lg font-semibold text-on-surface">Mentorat &amp; Encadrement de 2 ingénieurs juniors / stagiaires PFE</h5>
                                    </div>
                                    <p class="font-body-sm text-body-sm text-on-surface-variant mt-1">
                                        Accompagnement de 2 recrues issues de l'INSAT et de l'ENIT sur la stack TypeScript et bonnes pratiques de code review.
                                    </p>
                                </div>
                                <span class="font-numeric-md text-numeric-md font-bold text-tertiary shrink-0">100%</span>
                            </div>
                            <div class="w-full bg-surface-container-highest rounded-full h-2 mt-1">
                                <div class="bg-tertiary h-2 rounded-full transition-all duration-500" style="width: 100%"></div>
                            </div>
                            <div class="flex items-center justify-between text-label-sm font-label-sm text-on-surface-variant pt-1 flex-wrap gap-1">
                                <span class="text-tertiary font-medium flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[14px]">check_circle</span>
                                    Objectif pleinement atteint – Félicitations du département
                                </span>
                                <span>Pondération : 30%</span>
                            </div>
                        </div>
                        <!-- Objective 3 -->
                        <div class="p-space-md rounded-xl bg-surface-container-low flex flex-col gap-space-xs">
                            <div class="flex items-start justify-between gap-space-sm">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="px-2 py-0.5 rounded bg-secondary-container text-on-secondary-fixed font-label-sm text-[10px] font-bold">CERTIF</span>
                                        <h5 class="font-label-lg text-label-lg font-semibold text-on-surface">Certification AWS Solutions Architect Professional</h5>
                                    </div>
                                    <p class="font-body-sm text-body-sm text-on-surface-variant mt-1">
                                        Passage officiel de l'examen SAP-C02 avec prise en charge par le plan de formation Carthage Tech.
                                    </p>
                                </div>
                                <span class="font-numeric-md text-numeric-md font-bold text-tertiary shrink-0">100%</span>
                            </div>
                            <div class="w-full bg-surface-container-highest rounded-full h-2 mt-1">
                                <div class="bg-tertiary h-2 rounded-full transition-all duration-500" style="width: 100%"></div>
                            </div>
                            <div class="flex items-center justify-between text-label-sm font-label-sm text-on-surface-variant pt-1 flex-wrap gap-1">
                                <span class="text-tertiary font-medium flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[14px]">stars</span>
                                    Validé avec succès • Prime technique de 1 200 TND octroyée
                                </span>
                                <span>Pondération : 30%</span>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Latest Evaluation & Review Card -->
                <div class="bg-surface-container-lowest rounded-xl p-space-xl shadow-[0_1px_3px_0_rgba(15,23,42,0.05)]">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-space-md mb-space-lg gap-2">
                        <div class="flex items-center gap-space-sm">
                            <div class="w-8 h-8 rounded-lg bg-secondary-container/50 flex items-center justify-center text-on-secondary-fixed">
                                <span class="material-symbols-outlined text-[20px]">rate_review</span>
                            </div>
                            <div>
                                <h3 class="font-headline-sm text-headline-sm text-on-surface font-bold">Synthèse de l'Entretien Annuel d'Évaluation 2024 / 2025</h3>
                                <p class="font-body-sm text-body-sm text-on-surface-variant">Tenu le 12 Février 2025 • Évaluateur : Amel Ben Salah</p>
                            </div>
                        </div>
                        <button class="h-8 px-space-md rounded-xl bg-surface-container-high hover:bg-surface-container-highest text-on-surface font-label-sm text-label-sm flex items-center gap-1.5 transition-colors self-start sm:self-auto" type="button">
                            <span class="material-symbols-outlined text-[16px] text-error">picture_as_pdf</span>
                            <span>Télécharger le Compte-Rendu Signé (PDF)</span>
                        </button>
                    </div>
                    <div class="bg-surface-container-low p-space-lg rounded-xl flex flex-col gap-space-md">
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-space-sm text-center">
                            <div class="bg-surface-container-lowest p-space-sm rounded-lg shadow-sm">
                                <span class="font-label-sm text-label-sm text-on-surface-variant">Performance Métier</span>
                                <p class="font-headline-sm text-headline-sm text-on-surface font-bold mt-1">4.9 / 5</p>
                            </div>
                            <div class="bg-surface-container-lowest p-space-sm rounded-lg shadow-sm">
                                <span class="font-label-sm text-label-sm text-on-surface-variant">Leadership Squad</span>
                                <p class="font-headline-sm text-headline-sm text-on-surface font-bold mt-1">4.8 / 5</p>
                            </div>
                            <div class="bg-surface-container-lowest p-space-sm rounded-lg shadow-sm">
                                <span class="font-label-sm text-label-sm text-on-surface-variant">Conformité Délais</span>
                                <p class="font-headline-sm text-headline-sm text-on-surface font-bold mt-1">4.7 / 5</p>
                            </div>
                            <div class="bg-surface-container-lowest p-space-sm rounded-lg shadow-sm">
                                <span class="font-label-sm text-label-sm text-on-surface-variant">Innovation &amp; R&amp;D</span>
                                <p class="font-headline-sm text-headline-sm text-on-surface font-bold mt-1">5.0 / 5</p>
                            </div>
                        </div>
                        <div class="flex flex-col gap-2 pt-space-xs">
                            <h5 class="font-label-md text-label-md font-semibold text-on-surface uppercase tracking-wider">Commentaire d'Évaluation de la Direction :</h5>
                            <blockquote class="italic text-body-md font-body-md text-on-surface-variant bg-surface-container-lowest p-space-md rounded-lg leading-relaxed shadow-sm">
                                « {{ $firstName }} a démontré un niveau d'engagement exemplaire sur l'exercice 2024. Son autonomie dans la refonte de la couche microservices et sa pédagogie avec les juniors ont consolidé la position du pôle ingénierie. Une augmentation salariale rétroactive au 1er Janvier 2025 a été accordée et validée par la direction générale. Prochaine étape : préparation au rôle de Staff Software Architect. »
                            </blockquote>
                        </div>
                        <div class="flex flex-wrap items-center justify-between text-body-sm font-body-sm text-on-surface-variant pt-1 gap-1">
                            <div class="flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-[16px] text-tertiary">draw</span>
                                <span>Signatures électroniques INPDP : <strong class="text-on-surface">Validées le 14/02/2025</strong></span>
                            </div>
                            <span class="text-secondary">Statut : Archivé au dossier RH</span>
                        </div>
                    </div>
                </div>
            </div>
            <!-- SIDEBAR COLUMN (4 cols) -->
            <div class="lg:col-span-4 flex flex-col gap-space-lg">
                <!-- Technical Stack & Skills Matrix -->
                <div class="bg-surface-container-lowest rounded-xl p-space-xl shadow-[0_1px_3px_0_rgba(15,23,42,0.05)]">
                    <div class="flex items-center justify-between pb-space-md mb-space-md">
                        <div class="flex items-center gap-space-xs">
                            <span class="material-symbols-outlined text-[20px] text-primary">data_object</span>
                            <h3 class="font-headline-sm text-headline-sm text-on-surface font-bold">Matrice de Compétences</h3>
                        </div>
                        <button class="text-primary hover:text-primary-container font-label-sm text-label-sm font-semibold flex items-center gap-0.5" type="button">
                            <span class="material-symbols-outlined text-[16px]">add</span>
                            <span>Suggérer</span>
                        </button>
                    </div>
                    <div class="flex flex-col gap-space-md">
                        <!-- Skill 1 -->
                        <div class="flex flex-col gap-1">
                            <div class="flex justify-between font-label-md text-label-md">
                                <span class="font-semibold text-on-surface">TypeScript / Node.js</span>
                                <span class="text-primary font-bold">Expert (95%)</span>
                            </div>
                            <div class="w-full bg-surface-container-high rounded-full h-1.5">
                                <div class="bg-primary h-1.5 rounded-full" style="width: 95%"></div>
                            </div>
                        </div>
                        <!-- Skill 2 -->
                        <div class="flex flex-col gap-1">
                            <div class="flex justify-between font-label-md text-label-md">
                                <span class="font-semibold text-on-surface">React &amp; Next.js</span>
                                <span class="text-primary font-bold">Expert (92%)</span>
                            </div>
                            <div class="w-full bg-surface-container-high rounded-full h-1.5">
                                <div class="bg-primary h-1.5 rounded-full" style="width: 92%"></div>
                            </div>
                        </div>
                        <!-- Skill 3 -->
                        <div class="flex flex-col gap-1">
                            <div class="flex justify-between font-label-md text-label-md">
                                <span class="font-semibold text-on-surface">Architecture Microservices</span>
                                <span class="text-tertiary font-bold">Avancé (88%)</span>
                            </div>
                            <div class="w-full bg-surface-container-high rounded-full h-1.5">
                                <div class="bg-tertiary h-1.5 rounded-full" style="width: 88%"></div>
                            </div>
                        </div>
                        <!-- Skill 4 -->
                        <div class="flex flex-col gap-1">
                            <div class="flex justify-between font-label-md text-label-md">
                                <span class="font-semibold text-on-surface">Docker &amp; Kubernetes (EKS)</span>
                                <span class="text-tertiary font-bold">Avancé (85%)</span>
                            </div>
                            <div class="w-full bg-surface-container-high rounded-full h-1.5">
                                <div class="bg-tertiary h-1.5 rounded-full" style="width: 85%"></div>
                            </div>
                        </div>
                        <!-- Skill 5 -->
                        <div class="flex flex-col gap-1">
                            <div class="flex justify-between font-label-md text-label-md">
                                <span class="font-semibold text-on-surface">PostgreSQL &amp; Optimisation Query</span>
                                <span class="text-tertiary font-bold">Avancé (84%)</span>
                            </div>
                            <div class="w-full bg-surface-container-high rounded-full h-1.5">
                                <div class="bg-tertiary h-1.5 rounded-full" style="width: 84%"></div>
                            </div>
                        </div>
                    </div>
                    <!-- Soft Skills Section -->
                    <div class="mt-space-lg pt-space-md bg-surface-container-low -mx-space-xl -mb-space-xl p-space-lg rounded-b-xl">
                        <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider font-semibold block mb-2">Soft Skills &amp; Management :</span>
                        <div class="flex flex-wrap gap-1.5">
                            <span class="px-2.5 py-1 rounded-full bg-surface-container-lowest text-on-surface font-label-sm text-label-sm shadow-sm">
                                Leadership Technique
                            </span>
                            <span class="px-2.5 py-1 rounded-full bg-surface-container-lowest text-on-surface font-label-sm text-label-sm shadow-sm">
                                Animation d'Équipe Agile
                            </span>
                            <span class="px-2.5 py-1 rounded-full bg-surface-container-lowest text-on-surface font-label-sm text-label-sm shadow-sm">
                                Communication Inter-Pôles
                            </span>
                            <span class="px-2.5 py-1 rounded-full bg-surface-container-lowest text-on-surface font-label-sm text-label-sm shadow-sm">
                                Mentorat Junior
                            </span>
                        </div>
                    </div>
                </div>
                <!-- Validated Certifications -->
                <div class="bg-surface-container-lowest rounded-xl p-space-xl shadow-[0_1px_3px_0_rgba(15,23,42,0.05)]">
                    <div class="flex items-center justify-between pb-space-sm mb-space-md">
                        <div class="flex items-center gap-space-xs">
                            <span class="material-symbols-outlined text-[20px] text-tertiary">card_membership</span>
                            <h3 class="font-headline-sm text-headline-sm text-on-surface font-bold">Certifications Validées</h3>
                        </div>
                        <span class="font-label-sm text-label-sm text-on-surface-variant font-medium">3 Actives</span>
                    </div>
                    <div class="flex flex-col gap-space-sm">
                        <!-- Cert 1 -->
                        <div class="p-space-sm rounded-xl bg-surface-container-low flex items-start gap-space-sm">
                            <div class="w-8 h-8 rounded-lg bg-tertiary-container/20 text-tertiary flex items-center justify-center shrink-0">
                                <span class="material-symbols-outlined text-[18px]">verified_user</span>
                            </div>
                            <div class="flex flex-col min-w-0">
                                <span class="font-label-lg text-label-lg font-semibold text-on-surface truncate">AWS Certified Solutions Architect</span>
                                <span class="font-body-sm text-body-sm text-on-surface-variant">Amazon Web Services • Nov 2024</span>
                                <span class="font-label-sm text-label-sm text-tertiary mt-0.5">Vérifiée RH • ID: AWS-892401</span>
                            </div>
                        </div>
                        <!-- Cert 2 -->
                        <div class="p-space-sm rounded-xl bg-surface-container-low flex items-start gap-space-sm">
                            <div class="w-8 h-8 rounded-lg bg-primary-container/20 text-primary flex items-center justify-center shrink-0">
                                <span class="material-symbols-outlined text-[18px]">psychology</span>
                            </div>
                            <div class="flex flex-col min-w-0">
                                <span class="font-label-lg text-label-lg font-semibold text-on-surface truncate">Management Agile &amp; Scrum Master</span>
                                <span class="font-body-sm text-body-sm text-on-surface-variant">Tunisie Compétences • Juin 2023</span>
                                <span class="font-label-sm text-label-sm text-secondary mt-0.5">Délivré à Tunis</span>
                            </div>
                        </div>
                        <!-- Cert 3 -->
                        <div class="p-space-sm rounded-xl bg-surface-container-low flex items-start gap-space-sm">
                            <div class="w-8 h-8 rounded-lg bg-surface-container-highest text-on-surface flex items-center justify-center shrink-0">
                                <span class="material-symbols-outlined text-[18px]">gavel</span>
                            </div>
                            <div class="flex flex-col min-w-0">
                                <span class="font-label-lg text-label-lg font-semibold text-on-surface truncate">Droit du Travail &amp; Sécurité</span>
                                <span class="font-body-sm text-body-sm text-on-surface-variant">Formation Interne RH • Octobre 2022</span>
                                <span class="font-label-sm text-label-sm text-on-surface-variant mt-0.5">Référent Sécurité Équipe</span>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Convention Collective & Classification Tunisienne -->
                <div class="bg-surface-container-lowest rounded-xl p-space-xl shadow-[0_1px_3px_0_rgba(15,23,42,0.05)]">
                    <div class="flex items-center gap-space-xs pb-space-sm mb-space-md">
                        <span class="material-symbols-outlined text-[20px] text-secondary">balance</span>
                        <h3 class="font-headline-sm text-headline-sm text-on-surface font-bold">Cadre Conventionnel Tunisien</h3>
                    </div>
                    <div class="p-space-md rounded-xl bg-surface-container-low flex flex-col gap-space-sm">
                        <div class="flex items-center justify-between">
                            <span class="font-label-md text-label-md text-on-surface font-semibold">Convention Collective Sectorielle :</span>
                            <span class="px-2 py-0.5 rounded bg-primary-fixed text-primary font-label-sm text-[11px] font-bold">Syntec TN</span>
                        </div>
                        <p class="font-body-sm text-body-sm text-on-surface-variant leading-relaxed">
                            Bureaux d'Études, Sociétés d'Ingénierie &amp; Conseil Informatique en Tunisie.
                        </p>
                        <div class="bg-surface-container-lowest p-space-sm rounded-lg flex flex-col gap-1 text-body-sm font-body-sm mt-1 shadow-sm">
                            <div class="flex justify-between">
                                <span class="text-on-surface-variant">Classification légale :</span>
                                <span class="font-semibold text-on-surface">Catégorie 7 • Échelon 3</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-on-surface-variant">Base légale horaire :</span>
                                <span class="font-semibold text-on-surface">Régime 40h / semaine</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-on-surface-variant">Avancement ancienneté :</span>
                                <span class="font-semibold text-tertiary">+1.5% tous les 2 ans</span>
                            </div>
                        </div>
                        <div class="flex items-start gap-2 pt-space-xs text-body-sm font-body-sm text-on-surface-variant">
                            <span class="material-symbols-outlined text-[16px] text-primary shrink-0 mt-0.5">event</span>
                            <p>Prochain échelon automatique prévu en <strong class="text-on-surface">Janvier 2026</strong> (Conformément au Code du Travail Tunisien Art. 23).</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Account Settings Modal -->
    <div class="{{ $hasAccountErrors ? '' : 'hidden' }} fixed inset-0 z-[60]" id="account-modal" role="dialog" aria-modal="true" aria-labelledby="account-modal-title">
        <div class="absolute inset-0 bg-inverse-surface/50 backdrop-blur-sm" data-modal-close></div>
        <div class="relative flex min-h-full items-center justify-center p-4">
            <div class="w-full max-w-2xl max-h-[90vh] overflow-y-auto rounded-xl bg-surface-container-lowest shadow-2xl">
                <div class="sticky top-0 z-10 flex items-start justify-between gap-space-md border-b border-surface-container-high bg-surface-container-lowest px-space-xl py-space-lg">
                    <div>
                        <h2 class="font-headline-md text-headline-md text-on-surface font-bold" id="account-modal-title">Paramètres du compte</h2>
                        <p class="font-body-sm text-body-sm text-on-surface-variant mt-0.5">Mettez à jour vos coordonnées, votre mot de passe ou gérez la suppression de votre compte.</p>
                    </div>
                    <button aria-label="Fermer" class="w-9 h-9 rounded-xl flex items-center justify-center text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface transition-colors shrink-0" data-modal-close type="button">
                        <span class="material-symbols-outlined text-[20px]">close</span>
                    </button>
                </div>

                <div class="p-space-xl flex flex-col gap-space-xl">
                    <!-- Profile Information -->
                    <section>
                        <div class="flex items-center gap-space-sm mb-space-md">
                            <span class="material-symbols-outlined text-[20px] text-primary">person</span>
                            <div>
                                <h3 class="font-label-lg text-label-lg text-on-surface font-semibold">Informations personnelles</h3>
                                <p class="font-body-sm text-body-sm text-on-surface-variant">Nom affiché et adresse e-mail de connexion.</p>
                            </div>
                        </div>
                        <form class="flex flex-col gap-space-md" method="POST" action="{{ route('profile.update') }}">
                            @csrf
                            @method('patch')
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-space-md">
                                <div class="flex flex-col gap-1">
                                    <label class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider" for="name">Nom complet</label>
                                    <input autocomplete="name" class="w-full h-10 px-space-md rounded-xl bg-surface-container-low border border-outline-variant/60 focus:border-primary focus:ring-2 focus:ring-primary/20 font-body-md text-body-md text-on-surface outline-none transition-all" id="name" name="name" required type="text" value="{{ old('name', $user->name) }}"/>
                                    @error('name')
                                        <span class="font-label-sm text-label-sm text-error">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="flex flex-col gap-1">
                                    <label class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider" for="email">Adresse e-mail</label>
                                    <input autocomplete="username" class="w-full h-10 px-space-md rounded-xl bg-surface-container-low border border-outline-variant/60 focus:border-primary focus:ring-2 focus:ring-primary/20 font-body-md text-body-md text-on-surface outline-none transition-all" id="email" name="email" required type="email" value="{{ old('email', $user->email) }}"/>
                                    @error('email')
                                        <span class="font-label-sm text-label-sm text-error">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                                <div class="p-space-sm rounded-xl bg-surface-container-low font-body-sm text-body-sm text-on-surface-variant">
                                    Votre adresse e-mail n'est pas vérifiée.
                                    <button class="underline text-primary hover:text-primary-container" form="send-verification" type="submit">
                                        Cliquez ici pour renvoyer l'e-mail de vérification.
                                    </button>
                                </div>
                            @endif

                            <div class="flex justify-end">
                                <button class="h-10 px-space-lg rounded-xl bg-primary-fill text-on-primary font-label-lg text-label-lg shadow-sm hover:bg-primary-container transition-all flex items-center gap-space-xs" type="submit">
                                    <span class="material-symbols-outlined text-[18px]">save</span>
                                    <span>Enregistrer les informations</span>
                                </button>
                            </div>
                        </form>
                        <form id="send-verification" method="post" action="{{ route('verification.send') }}">
                            @csrf
                        </form>
                    </section>

                    <div class="border-t border-surface-container-high"></div>

                    <!-- Password -->
                    <section>
                        <div class="flex items-center gap-space-sm mb-space-md">
                            <span class="material-symbols-outlined text-[20px] text-primary">lock</span>
                            <div>
                                <h3 class="font-label-lg text-label-lg text-on-surface font-semibold">Sécurité du compte</h3>
                                <p class="font-body-sm text-body-sm text-on-surface-variant">Utilisez un mot de passe long et aléatoire pour rester protégé.</p>
                            </div>
                        </div>
                        <form class="flex flex-col gap-space-md" method="POST" action="{{ route('password.update') }}">
                            @csrf
                            @method('put')
                            <div class="flex flex-col gap-1">
                                <label class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider" for="update_password_current_password">Mot de passe actuel</label>
                                <input autocomplete="current-password" class="w-full h-10 px-space-md rounded-xl bg-surface-container-low border border-outline-variant/60 focus:border-primary focus:ring-2 focus:ring-primary/20 font-body-md text-body-md text-on-surface outline-none transition-all" id="update_password_current_password" name="current_password" type="password"/>
                                @if ($errors->updatePassword->get('current_password'))
                                    <span class="font-label-sm text-label-sm text-error">{{ $errors->updatePassword->first('current_password') }}</span>
                                @endif
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-space-md">
                                <div class="flex flex-col gap-1">
                                    <label class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider" for="update_password_password">Nouveau mot de passe</label>
                                    <input autocomplete="new-password" class="w-full h-10 px-space-md rounded-xl bg-surface-container-low border border-outline-variant/60 focus:border-primary focus:ring-2 focus:ring-primary/20 font-body-md text-body-md text-on-surface outline-none transition-all" id="update_password_password" name="password" type="password"/>
                                    @if ($errors->updatePassword->get('password'))
                                        <span class="font-label-sm text-label-sm text-error">{{ $errors->updatePassword->first('password') }}</span>
                                    @endif
                                </div>
                                <div class="flex flex-col gap-1">
                                    <label class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider" for="update_password_password_confirmation">Confirmer le mot de passe</label>
                                    <input autocomplete="new-password" class="w-full h-10 px-space-md rounded-xl bg-surface-container-low border border-outline-variant/60 focus:border-primary focus:ring-2 focus:ring-primary/20 font-body-md text-body-md text-on-surface outline-none transition-all" id="update_password_password_confirmation" name="password_confirmation" type="password"/>
                                    @if ($errors->updatePassword->get('password_confirmation'))
                                        <span class="font-label-sm text-label-sm text-error">{{ $errors->updatePassword->first('password_confirmation') }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="flex justify-end">
                                <button class="h-10 px-space-lg rounded-xl bg-primary-fill text-on-primary font-label-lg text-label-lg shadow-sm hover:bg-primary-container transition-all flex items-center gap-space-xs" type="submit">
                                    <span class="material-symbols-outlined text-[18px]">key</span>
                                    <span>Mettre à jour le mot de passe</span>
                                </button>
                            </div>
                        </form>
                    </section>

                    <div class="border-t border-surface-container-high"></div>

                    <!-- Delete Account -->
                    <section>
                        <div class="flex items-center gap-space-sm mb-space-md">
                            <span class="material-symbols-outlined text-[20px] text-error">warning</span>
                            <div>
                                <h3 class="font-label-lg text-label-lg text-on-surface font-semibold">Supprimer le compte</h3>
                                <p class="font-body-sm text-body-sm text-on-surface-variant">Cette action est définitive : toutes les données associées seront supprimées.</p>
                            </div>
                        </div>
                        <form class="flex flex-col gap-space-md p-space-md rounded-xl bg-error-container/30" method="POST" action="{{ route('profile.destroy') }}" onsubmit="return confirm('Supprimer définitivement votre compte ? Cette action est irréversible.');">
                            @csrf
                            @method('delete')
                            <div class="flex flex-col gap-1">
                                <label class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider" for="delete_password">Mot de passe de confirmation</label>
                                <input autocomplete="current-password" class="w-full h-10 px-space-md rounded-xl bg-surface-container-lowest border border-outline-variant/60 focus:border-error focus:ring-2 focus:ring-error/20 font-body-md text-body-md text-on-surface outline-none transition-all" id="delete_password" name="password" placeholder="Saisissez votre mot de passe" type="password"/>
                                @if ($errors->userDeletion->get('password'))
                                    <span class="font-label-sm text-label-sm text-error">{{ $errors->userDeletion->first('password') }}</span>
                                @endif
                            </div>
                            <div class="flex justify-end">
                                <button class="h-10 px-space-lg rounded-xl bg-error text-on-error font-label-lg text-label-lg shadow-sm hover:opacity-90 transition-all flex items-center gap-space-xs" type="submit">
                                    <span class="material-symbols-outlined text-[18px]">delete_forever</span>
                                    <span>Supprimer mon compte</span>
                                </button>
                            </div>
                        </form>
                    </section>
                </div>
            </div>
        </div>
    </div>

    <script>
        (function () {
            const modal = document.getElementById('account-modal');
            const openBtn = document.getElementById('btn-update-info');
            const requestBtn = document.getElementById('btn-request-review');
            const closeEls = document.querySelectorAll('[data-modal-close]');

            if (!modal) return;

            const setScrollLock = (locked) => document.body.classList.toggle('overflow-hidden', locked);

            const openModal = () => {
                modal.classList.remove('hidden');
                setScrollLock(true);
                const nameInput = modal.querySelector('input[name="name"]');
                if (nameInput) nameInput.focus();
            };

            const closeModal = () => {
                modal.classList.add('hidden');
                setScrollLock(false);
            };

            if (openBtn) openBtn.addEventListener('click', openModal);
            closeEls.forEach((el) => el.addEventListener('click', closeModal));

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape' && !modal.classList.contains('hidden')) closeModal();
            });

            if (!modal.classList.contains('hidden')) setScrollLock(true);

            if (requestBtn) {
                requestBtn.addEventListener('click', () => {
                    alert("Une notification de demande de Point Carrière a été envoyée à Amel Ben Salah (Directrice RH).");
                });
            }

            const toast = document.getElementById('profile-toast');
            if (toast) {
                setTimeout(() => {
                    toast.style.opacity = '0';
                    setTimeout(() => toast.remove(), 300);
                }, 3500);
            }
        })();
    </script>
</x-collaborator-shell>
