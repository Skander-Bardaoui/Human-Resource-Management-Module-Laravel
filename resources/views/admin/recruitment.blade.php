<x-admin-shell :active="$view === 'offers' ? 'offers' : 'recruitment'" title="Gestion du Recrutement & Candidatures">
@php
    $contractLabels = [
        'CDI' => 'CDI (Durée Indéterminée)',
        'CDD' => 'CDD (Durée Déterminée)',
        'SIVP' => 'Stage SIVP (ANETI)',
        'Karama' => 'Contrat Karama',
        'Consulting' => 'Consultance / Externe',
    ];
    $sourceLabels = [
        'LinkedIn' => 'LinkedIn Recruiter',
        'Recommandation interne' => 'Recommandation interne',
        'Site Carrières' => 'Site Carrières Carthage Tech',
        'Keejob / Tanitjobs' => 'Keejob / Tanitjobs',
    ];
    $mockDepartments = collect(['Ingénierie & R&D', 'Produit & Design', 'Commercial & Marketing', 'Finance & Opérations RH']);
    $extraDepartments = $departments->diff($mockDepartments)->values();
    $stageDot = [
        'received' => 'bg-secondary',
        'rh_evaluation' => 'bg-primary-container',
        'test_technical' => 'bg-surface-tint',
        'offer' => 'bg-tertiary-fixed-dim',
        'hired' => 'bg-tertiary',
    ];
@endphp
<div class="flex flex-col w-full pb-16" data-quick-create="{{ is_string(request('new')) ? request('new') : '' }}">
<!-- Top Ambient Glow (Scoped & Soft) -->
<div class="relative w-full">
<div class="absolute -top-6 right-12 w-96 h-96 bg-primary/5 rounded-full blur-3xl pointer-events-none -z-10"></div>
<div class="absolute top-20 left-1/3 w-80 h-80 bg-tertiary-fixed/10 rounded-full blur-3xl pointer-events-none -z-10"></div>
</div>

@if (session('success'))
<div class="mb-space-md p-space-md rounded-xl bg-[#ECFDF5] text-[#065F46] dark:bg-tertiary/15 dark:text-tertiary-fixed-dim font-label-lg text-label-lg flex items-center gap-space-sm shadow-sm">
    <span class="material-symbols-outlined text-body-lg">check_circle</span>
    <span>{{ session('success') }}</span>
</div>
@endif
@if (session('error'))
<div class="mb-space-md p-space-md rounded-xl bg-[#FFFBEB] text-[#92400E] dark:bg-[#3f3113] dark:text-[#ffd48a] font-label-lg text-label-lg flex items-center gap-space-sm shadow-sm">
    <span class="material-symbols-outlined text-body-lg">error</span>
    <span>{{ session('error') }}</span>
</div>
@endif

@include('admin.partials.validation-errors')

<!-- Breadcrumbs & Meta Topline -->
<div class="flex items-center justify-between pt-space-lg mb-space-xs">
<div class="flex items-center gap-space-xs text-on-surface-variant font-label-md text-label-md">
<span class="hover:text-primary cursor-pointer transition-colors">Accueil</span>
<span class="material-symbols-outlined text-body-sm text-outline-variant">chevron_right</span>
<span class="text-on-surface font-semibold">Recrutement &amp; Talents</span>
<span class="ml-space-sm px-2 py-0.5 rounded-full bg-surface-container-high text-primary font-label-sm text-label-sm">Session Q1 2025</span>
</div>
</div>
<!-- Primary Page Header & Executive Controls -->
<div class="flex flex-col xl:flex-row xl:items-end justify-between gap-space-lg pb-space-lg">
<div class="flex flex-col gap-1 min-w-0">
<div class="flex items-center gap-space-sm flex-wrap">
<h1 class="font-headline-lg text-headline-lg text-on-surface tracking-tight font-bold">
          Gestion du Recrutement &amp; Candidatures
        </h1>
</div>
</div>
<!-- Actions & View Switcher -->
<div class="flex items-center gap-space-md shrink-0 flex-wrap">
@if ($view !== 'offers')
<!-- Segmented View Toggle -->
<div class="inline-flex p-1 bg-surface-container rounded-xl shadow-inner">
<a class="flex items-center gap-1.5 px-space-md py-1.5 rounded-lg font-label-lg text-label-lg transition-all {{ $view === 'kanban' ? 'bg-surface-container-lowest text-primary font-semibold shadow-sm' : 'text-on-surface-variant hover:text-on-surface' }}" href="{{ route('admin.recruitment', array_merge(request()->query(), ['view' => 'kanban'])) }}">
<span class="material-symbols-outlined text-body-md" style="font-variation-settings: 'FILL' 1;">view_kanban</span>
<span>Tableau Kanban</span>
</a>
</div>
@endif
<!-- Action Button -->
<button class="h-10 px-space-lg rounded-xl bg-primary-container hover:bg-primary-fill text-on-primary font-label-lg text-label-lg flex items-center gap-space-xs shadow-md shadow-primary/20 hover:shadow-lg transition-all active:scale-[0.98]" type="button" onclick="{{ $view === 'offers' ? 'openOfferModal(null)' : 'openCandidateModal(null)' }}">
<span class="material-symbols-outlined text-body-lg">{{ $view === 'offers' ? 'work' : 'person_add' }}</span>
<span>{{ $view === 'offers' ? '+ Nouvelle Offre' : '+ Nouveau Recrutement' }}</span>
</button>
</div>
</div>
<!-- Executive Quick Stats Strip -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-space-md mb-space-lg">
<!-- Stat 1 -->
<div class="p-space-lg rounded-xl bg-surface-container-lowest shadow-sm flex items-center justify-between group hover:shadow-md transition-all">
<div class="flex flex-col">
<span class="font-label-sm text-label-sm text-on-surface-variant font-medium tracking-wide uppercase">Candidatures actives</span>
<div class="flex items-baseline gap-space-xs mt-1">
<span class="font-display-lg text-display-lg text-on-surface font-bold">{{ $stats['active_candidates'] }}</span>
@if ($stats['growth_pct'] !== null)
<span class="font-label-sm text-label-sm text-tertiary font-semibold flex items-center">
<span class="material-symbols-outlined text-body-sm">{{ $stats['growth_pct'] >= 0 ? 'arrow_upward' : 'arrow_downward' }}</span> +{{ $stats['growth_pct'] }}%
          </span>
@endif
</div>
</div>
<div class="w-12 h-12 rounded-xl bg-secondary-container flex items-center justify-center text-primary">
<span class="material-symbols-outlined text-headline-sm">groups</span>
</div>
</div>
<!-- Stat 2 -->
<div class="p-space-lg rounded-xl bg-surface-container-lowest shadow-sm flex items-center justify-between group hover:shadow-md transition-all">
<div class="flex flex-col">
<span class="font-label-sm text-label-sm text-on-surface-variant font-medium tracking-wide uppercase">Entretiens cette semaine</span>
<div class="flex items-baseline gap-space-xs mt-1">
<span class="font-display-lg text-display-lg text-on-surface font-bold">{{ str_pad($stats['interviews_this_week'], 2, '0', STR_PAD_LEFT) }}</span>
<span class="font-label-sm text-label-sm text-on-surface-variant font-medium">{{ $stats['interviews_today'] }} aujourd'hui</span>
</div>
</div>
<div class="w-12 h-12 rounded-xl bg-surface-container-high flex items-center justify-center text-primary-container">
<span class="material-symbols-outlined text-headline-sm">calendar_month</span>
</div>
</div>
<!-- Stat 3 -->
<div class="p-space-lg rounded-xl bg-surface-container-lowest shadow-sm flex items-center justify-between group hover:shadow-md transition-all">
<div class="flex flex-col">
<span class="font-label-sm text-label-sm text-on-surface-variant font-medium tracking-wide uppercase">Offres en attente</span>
<div class="flex items-baseline gap-space-xs mt-1">
<span class="font-display-lg text-display-lg text-on-surface font-bold">{{ str_pad($stats['pending_offers'], 2, '0', STR_PAD_LEFT) }}</span>
<span class="font-label-sm text-label-sm text-tertiary-container font-semibold">Validation DG</span>
</div>
</div>
<div class="w-12 h-12 rounded-xl bg-tertiary-fixed/30 flex items-center justify-center text-tertiary">
<span class="material-symbols-outlined text-headline-sm">fact_check</span>
</div>
</div>
<!-- Stat 4 -->
<div class="p-space-lg rounded-xl bg-surface-container-lowest shadow-sm flex items-center justify-between group hover:shadow-md transition-all">
<div class="flex flex-col">
<span class="font-label-sm text-label-sm text-on-surface-variant font-medium tracking-wide uppercase">Embauches ce mois</span>
<div class="flex items-baseline gap-space-xs mt-1">
<span class="font-display-lg text-display-lg text-tertiary font-bold">{{ str_pad($stats['hired_this_month'], 2, '0', STR_PAD_LEFT) }}</span>
@if ($stats['hired_this_month'] > 0)
<span class="font-label-sm text-label-sm text-tertiary font-semibold flex items-center">
<span class="material-symbols-outlined text-body-sm">check_circle</span> 100% objectif
          </span>
@endif
</div>
</div>
<div class="w-12 h-12 rounded-xl bg-surface-container flex items-center justify-center text-tertiary">
<span class="material-symbols-outlined text-headline-sm">how_to_reg</span>
</div>
</div>
</div>
<!-- Filtration & Search Toolbar -->
<form method="GET" action="{{ route('admin.recruitment') }}" class="bg-surface-container-lowest rounded-xl p-space-md shadow-sm mb-space-lg flex flex-col xl:flex-row items-center justify-between gap-space-md">
<input type="hidden" name="view" value="{{ $view }}"/>
<!-- Search Bar with Shortcut -->
<div class="relative w-full xl:w-96">
<span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-body-lg pointer-events-none">search</span>
<input id="recruitment-search" class="w-full h-10 pl-10 pr-12 rounded-lg bg-surface-container-low font-body-md text-body-md text-on-surface placeholder:text-on-surface-variant focus:outline-none focus:bg-surface-container transition-colors" name="search" placeholder="Filtrer par candidat, poste, compétence (React, Go...)" type="text" value="{{ request('search') }}"/>
<span class="absolute right-3 top-1/2 -translate-y-1/2 font-label-sm text-label-sm text-on-surface-variant bg-surface-container-lowest px-1.5 py-0.5 rounded shadow-sm">/</span>
</div>
<!-- Dropdown Filters Group -->
<div class="flex items-center gap-space-sm w-full xl:w-auto flex-wrap sm:flex-nowrap">
<!-- Department Filter -->
<div class="relative flex-1 sm:flex-none">
<select class="h-10 pl-3 pr-8 rounded-lg bg-surface-container-low font-label-lg text-label-lg text-on-surface appearance-none focus:outline-none focus:bg-surface-container cursor-pointer w-full sm:w-auto" name="department" onchange="this.form.submit()">
<option value="">Département: Tous</option>
@foreach ($mockDepartments as $department)
<option value="{{ $department }}" @selected(request('department') === $department)>{{ $department }}</option>
@endforeach
@foreach ($extraDepartments as $department)
<option value="{{ $department }}" @selected(request('department') === $department)>{{ $department }}</option>
@endforeach
</select>
<span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-on-surface-variant text-body-md pointer-events-none">expand_more</span>
</div>
<!-- Contract Type Filter (Tunisian Legal Framework) -->
<div class="relative flex-1 sm:flex-none">
<select class="h-10 pl-3 pr-8 rounded-lg bg-surface-container-low font-label-lg text-label-lg text-on-surface appearance-none focus:outline-none focus:bg-surface-container cursor-pointer w-full sm:w-auto" name="contract_type" onchange="this.form.submit()">
<option value="">Type de Contrat: Tous</option>
@foreach (App\Models\JobOffer::CONTRACT_TYPES as $type)
<option value="{{ $type }}" @selected(request('contract_type') === $type)>{{ $contractLabels[$type] ?? $type }}</option>
@endforeach
</select>
<span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-on-surface-variant text-body-md pointer-events-none">expand_more</span>
</div>
<!-- Provenance / Source -->
<div class="relative hidden md:block">
<select class="h-10 pl-3 pr-8 rounded-lg bg-surface-container-low font-label-lg text-label-lg text-on-surface appearance-none focus:outline-none focus:bg-surface-container cursor-pointer" name="source" onchange="this.form.submit()">
<option value="">Canal: Tous</option>
@foreach (App\Models\Candidate::SOURCES as $source)
<option value="{{ $source }}" @selected(request('source') === $source)>{{ $sourceLabels[$source] ?? $source }}</option>
@endforeach
</select>
<span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-on-surface-variant text-body-md pointer-events-none">expand_more</span>
</div>
<a class="h-10 w-10 flex items-center justify-center rounded-lg bg-surface-container-low hover:bg-surface-container text-on-surface-variant hover:text-on-surface transition-colors" title="Réinitialiser les filtres" href="{{ route('admin.recruitment', ['view' => $view]) }}">
<span class="material-symbols-outlined text-body-lg">filter_alt_off</span>
</a>
</div>
</form>

@if ($view === 'kanban')
<!-- Interactive Recruitment Kanban Board -->
<!-- 5 Columns Container with Horizontal Scroll fallback on mid-size screens -->
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-space-md items-start">
@foreach ($candidatesByStage as $stageKey => $stageCandidates)
<!-- COLUMN -->
<div class="flex flex-col bg-surface-container-low rounded-xl p-space-sm shadow-sm min-w-0" data-stage="{{ $stageKey }}">
<!-- Column Header -->
<div class="p-space-sm flex items-center justify-between">
<div class="flex items-center gap-space-xs min-w-0">
<span class="w-2.5 h-2.5 rounded-full {{ $stageDot[$stageKey] }}"></span>
<h2 class="font-label-lg text-label-lg text-on-surface font-bold truncate">{{ App\Models\Candidate::STAGES[$stageKey] }}</h2>
<span class="px-2 py-0.5 rounded-full font-label-sm text-label-sm font-semibold {{ $stageKey === 'hired' ? 'bg-tertiary-fixed/40 text-tertiary font-bold' : 'bg-surface-container text-on-surface-variant' }}">{{ $stageCandidates->count() }}</span>
</div>
<button class="w-7 h-7 rounded-lg hover:bg-surface-container flex items-center justify-center text-on-surface-variant hover:text-on-surface" type="button" title="Nouveau candidat à cette étape" onclick="openCandidateModal(null, '{{ $stageKey }}')">
<span class="material-symbols-outlined text-body-md">more_horiz</span>
</button>
</div>
<!-- Cards Container -->
<div class="flex flex-col gap-space-sm mt-space-xs min-h-24 rounded-lg transition-colors" data-dropzone data-stage="{{ $stageKey }}">
@forelse ($stageCandidates as $candidate)
    @include('admin.partials.candidate-card', ['candidate' => $candidate])
@empty
<div class="border border-dashed border-outline-variant rounded-xl p-space-md text-center font-label-sm text-label-sm text-on-surface-variant">
    Aucun candidat — glissez une carte ici ou cliquez sur ⋯
</div>
@endforelse
</div>
</div>
@endforeach
</div>
<!-- Tunisian Regulatory & Pipeline Health Footer Bar -->
<div class="mt-space-xl p-space-md bg-surface-container-lowest rounded-xl shadow-sm flex flex-col md:flex-row items-center justify-between gap-space-md">
<div class="flex items-center gap-space-md min-w-0">
<div class="w-10 h-10 rounded-lg bg-surface-container-high flex items-center justify-center text-primary shrink-0">
<span class="material-symbols-outlined text-headline-sm">policy</span>
</div>
<div class="flex flex-col min-w-0">
<span class="font-label-lg text-label-lg text-on-surface font-semibold">Conformité aux Procédures d'Embauche en Tunisie</span>
<span class="font-body-sm text-body-sm text-on-surface-variant truncate">
          Tous les modèles de contrats proposés intègrent les clauses de période d'essai selon le Code du Travail Tunisien et les barèmes de retenue à la source IRPP/CSS.
        </span>
</div>
</div>
<div class="flex items-center gap-space-sm shrink-0">
<a class="px-space-md py-2 rounded-lg bg-surface-container-low hover:bg-surface-container text-on-surface font-label-md text-label-md transition-colors flex items-center gap-1.5" href="{{ route('admin.reports.export', ['dataset' => 'candidates']) }}">
<span class="material-symbols-outlined text-body-md">download</span>
<span>Exporter Pipeline (.XLSX)</span>
</a>
<button class="px-space-md py-2 rounded-lg bg-surface-container text-on-surface hover:bg-surface-container-high font-label-md text-label-md font-semibold transition-colors flex items-center gap-1.5" type="button" onclick="openCandidateModal(null)">
<span class="material-symbols-outlined text-body-md">settings</span>
<span>Paramètres d'Évaluation</span>
</button>
</div>
</div>
@else
<!-- Job Offers List View -->
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-space-md items-start">
@forelse ($offers as $offer)
<div class="p-space-lg rounded-xl bg-surface-container-lowest shadow-sm hover:shadow-md transition-all flex flex-col gap-space-sm">
    <div class="flex items-start justify-between gap-space-sm">
        <div class="flex items-center gap-space-sm min-w-0">
            <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center text-primary shrink-0">
                <span class="material-symbols-outlined text-body-lg">work</span>
            </div>
            <div class="min-w-0">
                <h2 class="font-headline-sm text-headline-sm text-on-surface font-bold truncate">{{ $offer->title }}</h2>
                <span class="font-label-sm text-label-sm text-on-surface-variant">{{ $offer->department }}{{ $offer->location ? ' • ' . $offer->location : '' }}</span>
            </div>
        </div>
        <span class="shrink-0 px-2.5 py-1 rounded-full font-label-sm text-label-sm font-semibold {{ $offer->status === 'open' ? 'bg-[#ECFDF5] text-[#065F46] dark:bg-tertiary/15 dark:text-tertiary-fixed-dim' : ($offer->status === 'pending_validation' ? 'bg-[#FFFBEB] text-[#92400E] dark:bg-[#3f3113] dark:text-[#ffd48a]' : 'bg-[#F1F5F9] text-[#475569] dark:bg-surface-container-high dark:text-on-surface-variant') }}">
            {{ App\Models\JobOffer::STATUSES[$offer->status] ?? $offer->status }}
        </span>
    </div>
    <div class="flex flex-wrap gap-space-sm font-label-sm text-label-sm">
        <span class="px-2.5 py-1 rounded-full bg-secondary-container text-on-secondary-fixed font-semibold">{{ $contractLabels[$offer->contract_type] ?? $offer->contract_type }}</span>
        @if ($offer->salary_min !== null || $offer->salary_max !== null)
        <span class="px-2.5 py-1 rounded-full bg-surface-container text-on-surface-variant font-numeric-md">
            {{ $offer->salary_min !== null ? number_format($offer->salary_min, 0, ',', ' ') . ' TND' : '—' }} — {{ $offer->salary_max !== null ? number_format($offer->salary_max, 0, ',', ' ') . ' TND' : '—' }}
        </span>
        @endif
        <span class="px-2.5 py-1 rounded-full bg-surface-container text-on-surface-variant">
            <span class="material-symbols-outlined text-body-sm align-middle">groups</span> {{ $offer->candidates_count }} candidat(s)
        </span>
    </div>
    @if ($offer->description)
    <p class="font-body-sm text-body-sm text-on-surface-variant line-clamp-2">{{ $offer->description }}</p>
    @endif
    <div class="flex items-center justify-end gap-1 pt-space-xs border-t border-surface-container">
        <button
            class="p-1.5 hover:text-primary transition-colors text-on-surface-variant"
            type="button"
            title="Modifier l'offre"
            data-json="{{ json_encode($offer->only(['id', 'title', 'department', 'contract_type', 'status', 'location', 'salary_min', 'salary_max', 'description'])) }}"
            onclick="openOfferModal(this)"
        >
            <span class="material-symbols-outlined text-body-md">edit</span>
        </button>
        <form method="POST" action="{{ route('admin.recruitment.offers.destroy', $offer) }}" onsubmit="return confirm('Supprimer cette offre ?');">
            @csrf
            @method('DELETE')
            <button class="p-1.5 hover:text-error transition-colors text-on-surface-variant" type="submit" title="Supprimer">
                <span class="material-symbols-outlined text-body-md">delete</span>
            </button>
        </form>
    </div>
</div>
@empty
<div class="col-span-full border border-dashed border-outline-variant rounded-xl p-space-xl text-center font-body-lg text-body-lg text-on-surface-variant">
    Aucune offre pour le moment. Cliquez sur « + Nouvelle Offre » pour créer la première.
</div>
@endforelse
</div>
@endif
</div>

<!-- ==================== DRAWER: Candidat (créer / modifier) ==================== -->
<div class="hidden fixed inset-0 z-50 overflow-hidden justify-end" id="candidate-modal">
<div class="absolute inset-0 bg-black/60 backdrop-blur-xs" onclick="closeModal('candidate-modal')"></div>
<div class="relative w-full max-w-2xl bg-surface-container-lowest shadow-2xl flex flex-col h-full z-10 overflow-hidden">
    <div class="p-space-xl bg-surface-container-low/60 flex items-start justify-between gap-space-md">
        <div class="flex items-center gap-space-md">
            <div class="w-12 h-12 rounded-xl bg-primary-container text-on-primary flex items-center justify-center shadow-sm shrink-0">
                <span class="material-symbols-outlined text-headline-lg">person_search</span>
            </div>
            <div class="flex flex-col">
                <h2 class="font-headline-md text-headline-md text-on-surface tracking-tight" id="candidate-modal-title">Nouveau Candidat</h2>
                <p class="font-body-sm text-body-sm text-on-surface-variant">Ajoutez ou mettez à jour un profil candidat</p>
            </div>
        </div>
        <button aria-label="Fermer" class="w-9 h-9 rounded-xl hover:bg-surface-container text-on-surface-variant hover:text-on-surface flex items-center justify-center transition-colors shrink-0" type="button" onclick="closeModal('candidate-modal')">
            <span class="material-symbols-outlined text-headline-sm">close</span>
        </button>
    </div>
    <form method="POST" action="{{ route('admin.recruitment.candidates.store') }}" id="candidate-form" class="flex-1 overflow-y-auto p-space-xl grid grid-cols-1 sm:grid-cols-2 gap-space-md">
        @csrf
        <input type="hidden" name="_method" value="" id="candidate-method"/>
        <div class="flex flex-col gap-space-xs">
            <label class="font-label-md text-label-md text-on-surface font-medium" for="candidate-name">Nom complet <span class="text-error">*</span></label>
            <input class="w-full h-10 px-3 rounded-xl bg-surface-container-lowest text-on-surface font-body-md text-body-md placeholder:text-on-surface-variant focus:outline-none ring-1 ring-outline-variant focus:ring-2 focus:ring-primary transition-all" id="candidate-name" name="name" placeholder="Ex: Yasmine Trabelsi" required/>
        </div>
        <div class="flex flex-col gap-space-xs">
            <label class="font-label-md text-label-md text-on-surface font-medium" for="candidate-position">Poste visé <span class="text-error">*</span></label>
            <input class="w-full h-10 px-3 rounded-xl bg-surface-container-lowest text-on-surface font-body-md text-body-md placeholder:text-on-surface-variant focus:outline-none ring-1 ring-outline-variant focus:ring-2 focus:ring-primary transition-all" id="candidate-position" name="position" placeholder="Ex: Développeuse Full-Stack" required/>
        </div>
        <div class="flex flex-col gap-space-xs">
            <label class="font-label-md text-label-md text-on-surface font-medium" for="candidate-email">Email</label>
            <input class="w-full h-10 px-3 rounded-xl bg-surface-container-lowest text-on-surface font-body-md text-body-md placeholder:text-on-surface-variant focus:outline-none ring-1 ring-outline-variant focus:ring-2 focus:ring-primary transition-all" id="candidate-email" name="email" placeholder="Ex: y.trabelsi@gmail.com" type="email"/>
        </div>
        <div class="flex flex-col gap-space-xs">
            <label class="font-label-md text-label-md text-on-surface font-medium" for="candidate-phone">Téléphone</label>
            <input class="w-full h-10 px-3 rounded-xl bg-surface-container-lowest text-on-surface font-body-md text-body-md placeholder:text-on-surface-variant focus:outline-none ring-1 ring-outline-variant focus:ring-2 focus:ring-primary transition-all" id="candidate-phone" name="phone" placeholder="Ex: 98 123 456"/>
        </div>
        <div class="flex flex-col gap-space-xs">
            <label class="font-label-md text-label-md text-on-surface font-medium" for="candidate-offer">Offre liée</label>
            <div class="relative">
                <select class="w-full appearance-none h-10 pl-3 pr-8 rounded-xl bg-surface-container-lowest text-on-surface font-body-md text-body-md focus:outline-none ring-1 ring-outline-variant focus:ring-2 focus:ring-primary transition-all cursor-pointer" id="candidate-offer" name="job_offer_id">
                    <option value="">— Aucune —</option>
                    @foreach ($offers as $offer)
                    <option value="{{ $offer->id }}">{{ $offer->title }} ({{ $offer->department }})</option>
                    @endforeach
                </select>
                <span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-on-surface-variant pointer-events-none text-body-md">expand_more</span>
            </div>
        </div>
        <div class="flex flex-col gap-space-xs">
            <label class="font-label-md text-label-md text-on-surface font-medium" for="candidate-stage">Étape kanban <span class="text-error">*</span></label>
            <div class="relative">
                <select class="w-full appearance-none h-10 pl-3 pr-8 rounded-xl bg-surface-container-lowest text-on-surface font-body-md text-body-md focus:outline-none ring-1 ring-outline-variant focus:ring-2 focus:ring-primary transition-all cursor-pointer" id="candidate-stage" name="stage">
                    @foreach (App\Models\Candidate::STAGES as $key => $label)
                    <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
                <span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-on-surface-variant pointer-events-none text-body-md">expand_more</span>
            </div>
        </div>
        <div class="flex flex-col gap-space-xs">
            <label class="font-label-md text-label-md text-on-surface font-medium" for="candidate-match">Match (%)</label>
            <input class="w-full h-10 px-3 rounded-xl bg-surface-container-lowest text-on-surface font-body-md text-body-md placeholder:text-on-surface-variant focus:outline-none ring-1 ring-outline-variant focus:ring-2 focus:ring-primary transition-all" id="candidate-match" name="match_percentage" placeholder="Ex: 85" type="number" min="0" max="100"/>
        </div>
        <div class="flex flex-col gap-space-xs">
            <label class="font-label-md text-label-md text-on-surface font-medium" for="candidate-experience">Années d'expérience</label>
            <input class="w-full h-10 px-3 rounded-xl bg-surface-container-lowest text-on-surface font-body-md text-body-md placeholder:text-on-surface-variant focus:outline-none ring-1 ring-outline-variant focus:ring-2 focus:ring-primary transition-all" id="candidate-experience" name="experience_years" placeholder="Ex: 4" type="number" min="0" max="60"/>
        </div>
        <div class="sm:col-span-2 flex flex-col gap-space-xs">
            <label class="font-label-md text-label-md text-on-surface font-medium" for="candidate-skills">Compétences (séparées par des virgules)</label>
            <input class="w-full h-10 px-3 rounded-xl bg-surface-container-lowest text-on-surface font-body-md text-body-md focus:outline-none ring-1 ring-outline-variant focus:ring-2 focus:ring-primary transition-all" id="candidate-skills" name="skills" placeholder="React, TypeScript, Node.js"/>
        </div>
        <div class="flex flex-col gap-space-xs">
            <label class="font-label-md text-label-md text-on-surface font-medium" for="candidate-source">Canal / Source</label>
            <div class="relative">
                <select class="w-full appearance-none h-10 pl-3 pr-8 rounded-xl bg-surface-container-lowest text-on-surface font-body-md text-body-md focus:outline-none ring-1 ring-outline-variant focus:ring-2 focus:ring-primary transition-all cursor-pointer" id="candidate-source" name="source">
                    <option value="">—</option>
                    @foreach (App\Models\Candidate::SOURCES as $source)
                    <option value="{{ $source }}">{{ $sourceLabels[$source] ?? $source }}</option>
                    @endforeach
                </select>
                <span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-on-surface-variant pointer-events-none text-body-md">expand_more</span>
            </div>
        </div>
        <div class="flex flex-col gap-space-xs">
            <label class="font-label-md text-label-md text-on-surface font-medium" for="candidate-salary">Prétention salariale (TND)</label>
            <input class="w-full h-10 px-3 rounded-xl bg-surface-container-lowest text-on-surface font-body-md text-body-md placeholder:text-on-surface-variant focus:outline-none ring-1 ring-outline-variant focus:ring-2 focus:ring-primary transition-all" id="candidate-salary" name="salary_claim" placeholder="Ex: 2800" type="number" min="0" step="0.01"/>
        </div>
        <div class="flex flex-col gap-space-xs">
            <label class="font-label-md text-label-md text-on-surface font-medium" for="candidate-availability">Disponibilité</label>
            <input class="w-full h-10 px-3 rounded-xl bg-surface-container-lowest text-on-surface font-body-md text-body-md focus:outline-none ring-1 ring-outline-variant focus:ring-2 focus:ring-primary transition-all" id="candidate-availability" name="availability" placeholder="Disponible immédiatement"/>
        </div>
        <div class="flex flex-col gap-space-xs">
            <label class="font-label-md text-label-md text-on-surface font-medium" for="candidate-contract">Contrat visé</label>
            <div class="relative">
                <select class="w-full appearance-none h-10 pl-3 pr-8 rounded-xl bg-surface-container-lowest text-on-surface font-body-md text-body-md focus:outline-none ring-1 ring-outline-variant focus:ring-2 focus:ring-primary transition-all cursor-pointer" id="candidate-contract" name="contract_type_target">
                    <option value="">—</option>
                    @foreach (App\Models\JobOffer::CONTRACT_TYPES as $type)
                    <option value="{{ $type }}">{{ $contractLabels[$type] ?? $type }}</option>
                    @endforeach
                </select>
                <span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-on-surface-variant pointer-events-none text-body-md">expand_more</span>
            </div>
        </div>
        <div class="sm:col-span-2 flex flex-col gap-space-xs">
            <label class="font-label-md text-label-md text-on-surface font-medium" for="candidate-avatar">URL Avatar (optionnel)</label>
            <input class="w-full h-10 px-3 rounded-xl bg-surface-container-lowest text-on-surface font-body-md text-body-md focus:outline-none ring-1 ring-outline-variant focus:ring-2 focus:ring-primary transition-all" id="candidate-avatar" name="avatar_url" placeholder="https://..."/>
        </div>
        <div class="sm:col-span-2 flex flex-col gap-space-xs">
            <label class="font-label-md text-label-md text-on-surface font-medium" for="candidate-notes">Notes</label>
            <textarea class="w-full min-h-[96px] px-3 py-2 rounded-xl bg-surface-container-lowest text-on-surface font-body-md text-body-md placeholder:text-on-surface-variant focus:outline-none ring-1 ring-outline-variant focus:ring-2 focus:ring-primary transition-all resize-y" id="candidate-notes" name="notes" placeholder="Ex: Excellente maîtrise de React et Node.js, disponible sous 1 mois..." rows="3"></textarea>
        </div>
    </form>
    <div class="p-space-lg bg-surface-container-lowest shadow-[0_-4px_12px_rgba(0,0,0,0.03)] flex items-center justify-between gap-space-sm">
        <button class="h-10 px-space-lg rounded-xl bg-surface-container-low hover:bg-surface-container text-on-surface font-label-lg text-label-lg transition-colors" type="button" onclick="closeModal('candidate-modal')">Annuler</button>
        <button class="h-10 px-space-xl rounded-xl bg-primary-fill hover:bg-primary-container text-on-primary font-label-lg text-label-lg shadow-md flex items-center gap-space-xs transition-all transform active:scale-95" type="submit" form="candidate-form">
            <span class="material-symbols-outlined text-body-md">check</span>
            <span>Enregistrer</span>
        </button>
    </div>
</div>
</div>

<!-- ==================== DRAWER: Offre (créer / modifier) ==================== -->
<div class="hidden fixed inset-0 z-50 overflow-hidden justify-end" id="offer-modal">
<div class="absolute inset-0 bg-black/60 backdrop-blur-xs" onclick="closeModal('offer-modal')"></div>
<div class="relative w-full max-w-2xl bg-surface-container-lowest shadow-2xl flex flex-col h-full z-10 overflow-hidden">
    <div class="p-space-xl bg-surface-container-low/60 flex items-start justify-between gap-space-md">
        <div class="flex items-center gap-space-md">
            <div class="w-12 h-12 rounded-xl bg-primary-container text-on-primary flex items-center justify-center shadow-sm shrink-0">
                <span class="material-symbols-outlined text-headline-lg">work</span>
            </div>
            <div class="flex flex-col">
                <h2 class="font-headline-md text-headline-md text-on-surface tracking-tight" id="offer-modal-title">Nouvelle Offre</h2>
                <p class="font-body-sm text-body-sm text-on-surface-variant">Publiez ou mettez à jour une offre d'emploi</p>
            </div>
        </div>
        <button aria-label="Fermer" class="w-9 h-9 rounded-xl hover:bg-surface-container text-on-surface-variant hover:text-on-surface flex items-center justify-center transition-colors shrink-0" type="button" onclick="closeModal('offer-modal')">
            <span class="material-symbols-outlined text-headline-sm">close</span>
        </button>
    </div>
    <form method="POST" action="{{ route('admin.recruitment.offers.store') }}" id="offer-form" class="flex-1 overflow-y-auto p-space-xl grid grid-cols-1 sm:grid-cols-2 gap-space-md">
        @csrf
        <input type="hidden" name="_method" value="" id="offer-method"/>
        <div class="sm:col-span-2 flex flex-col gap-space-xs">
            <label class="font-label-md text-label-md text-on-surface font-medium" for="offer-title">Intitulé du poste <span class="text-error">*</span></label>
            <input class="w-full h-10 px-3 rounded-xl bg-surface-container-lowest text-on-surface font-body-md text-body-md placeholder:text-on-surface-variant focus:outline-none ring-1 ring-outline-variant focus:ring-2 focus:ring-primary transition-all" id="offer-title" name="title" placeholder="Ex: Ingénieur DevOps Senior" required/>
        </div>
        <div class="flex flex-col gap-space-xs">
            <label class="font-label-md text-label-md text-on-surface font-medium" for="offer-department">Département <span class="text-error">*</span></label>
            <div class="relative">
                <select class="w-full appearance-none h-10 pl-3 pr-8 rounded-xl bg-surface-container-lowest text-on-surface font-body-md text-body-md focus:outline-none ring-1 ring-outline-variant focus:ring-2 focus:ring-primary transition-all cursor-pointer" id="offer-department" name="department">
                    @foreach ($mockDepartments as $department)
                    <option value="{{ $department }}">{{ $department }}</option>
                    @endforeach
                    @foreach ($extraDepartments as $department)
                    <option value="{{ $department }}">{{ $department }}</option>
                    @endforeach
                </select>
                <span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-on-surface-variant pointer-events-none text-body-md">expand_more</span>
            </div>
        </div>
        <div class="flex flex-col gap-space-xs">
            <label class="font-label-md text-label-md text-on-surface font-medium" for="offer-location">Localisation</label>
            <input class="w-full h-10 px-3 rounded-xl bg-surface-container-lowest text-on-surface font-body-md text-body-md placeholder:text-on-surface-variant focus:outline-none ring-1 ring-outline-variant focus:ring-2 focus:ring-primary transition-all" id="offer-location" name="location" placeholder="Ex: Tunis — Hybride"/>
        </div>
        <div class="flex flex-col gap-space-xs">
            <label class="font-label-md text-label-md text-on-surface font-medium" for="offer-contract">Type de contrat <span class="text-error">*</span></label>
            <div class="relative">
                <select class="w-full appearance-none h-10 pl-3 pr-8 rounded-xl bg-surface-container-lowest text-on-surface font-body-md text-body-md focus:outline-none ring-1 ring-outline-variant focus:ring-2 focus:ring-primary transition-all cursor-pointer" id="offer-contract" name="contract_type">
                    @foreach (App\Models\JobOffer::CONTRACT_TYPES as $type)
                    <option value="{{ $type }}">{{ $contractLabels[$type] ?? $type }}</option>
                    @endforeach
                </select>
                <span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-on-surface-variant pointer-events-none text-body-md">expand_more</span>
            </div>
        </div>
        <div class="flex flex-col gap-space-xs">
            <label class="font-label-md text-label-md text-on-surface font-medium" for="offer-status">Statut <span class="text-error">*</span></label>
            <div class="relative">
                <select class="w-full appearance-none h-10 pl-3 pr-8 rounded-xl bg-surface-container-lowest text-on-surface font-body-md text-body-md focus:outline-none ring-1 ring-outline-variant focus:ring-2 focus:ring-primary transition-all cursor-pointer" id="offer-status" name="status">
                    @foreach (App\Models\JobOffer::STATUSES as $key => $label)
                    <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
                <span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-on-surface-variant pointer-events-none text-body-md">expand_more</span>
            </div>
        </div>
        <div class="flex flex-col gap-space-xs">
            <label class="font-label-md text-label-md text-on-surface font-medium" for="offer-salary-min">Salaire min (TND)</label>
            <input class="w-full h-10 px-3 rounded-xl bg-surface-container-lowest text-on-surface font-body-md text-body-md placeholder:text-on-surface-variant focus:outline-none ring-1 ring-outline-variant focus:ring-2 focus:ring-primary transition-all" id="offer-salary-min" name="salary_min" placeholder="Ex: 2500" type="number" min="0" step="0.01"/>
        </div>
        <div class="flex flex-col gap-space-xs">
            <label class="font-label-md text-label-md text-on-surface font-medium" for="offer-salary-max">Salaire max (TND)</label>
            <input class="w-full h-10 px-3 rounded-xl bg-surface-container-lowest text-on-surface font-body-md text-body-md placeholder:text-on-surface-variant focus:outline-none ring-1 ring-outline-variant focus:ring-2 focus:ring-primary transition-all" id="offer-salary-max" name="salary_max" placeholder="Ex: 3800" type="number" min="0" step="0.01"/>
        </div>
        <div class="sm:col-span-2 flex flex-col gap-space-xs">
            <label class="font-label-md text-label-md text-on-surface font-medium" for="offer-description">Description</label>
            <textarea class="w-full min-h-[96px] px-3 py-2 rounded-xl bg-surface-container-lowest text-on-surface font-body-md text-body-md placeholder:text-on-surface-variant focus:outline-none ring-1 ring-outline-variant focus:ring-2 focus:ring-primary transition-all resize-y" id="offer-description" name="description" placeholder="Ex: Missions, stack technique, avantages et process de recrutement..." rows="3"></textarea>
        </div>
    </form>
    <div class="p-space-lg bg-surface-container-lowest shadow-[0_-4px_12px_rgba(0,0,0,0.03)] flex items-center justify-between gap-space-sm">
        <button class="h-10 px-space-lg rounded-xl bg-surface-container-low hover:bg-surface-container text-on-surface font-label-lg text-label-lg transition-colors" type="button" onclick="closeModal('offer-modal')">Annuler</button>
        <button class="h-10 px-space-xl rounded-xl bg-primary-fill hover:bg-primary-container text-on-primary font-label-lg text-label-lg shadow-md flex items-center gap-space-xs transition-all transform active:scale-95" type="submit" form="offer-form">
            <span class="material-symbols-outlined text-body-md">check</span>
            <span>Enregistrer</span>
        </button>
    </div>
</div>
</div>

<!-- ==================== DRAWER: Entretien ==================== -->
<div class="hidden fixed inset-0 z-50 overflow-hidden justify-end" id="interview-modal">
<div class="absolute inset-0 bg-black/60 backdrop-blur-xs" onclick="closeModal('interview-modal')"></div>
<div class="relative w-full max-w-2xl bg-surface-container-lowest shadow-2xl flex flex-col h-full z-10 overflow-hidden">
    <div class="p-space-xl bg-surface-container-low/60 flex items-start justify-between gap-space-md">
        <div class="flex items-center gap-space-md">
            <div class="w-12 h-12 rounded-xl bg-primary-container text-on-primary flex items-center justify-center shadow-sm shrink-0">
                <span class="material-symbols-outlined text-headline-lg">event</span>
            </div>
            <div class="flex flex-col">
                <h2 class="font-headline-md text-headline-md text-on-surface tracking-tight">Planifier un entretien</h2>
                <p class="font-body-sm text-body-sm text-on-surface-variant">Définissez le créneau et l'intervieweur</p>
            </div>
        </div>
        <button aria-label="Fermer" class="w-9 h-9 rounded-xl hover:bg-surface-container text-on-surface-variant hover:text-on-surface flex items-center justify-center transition-colors shrink-0" type="button" onclick="closeModal('interview-modal')">
            <span class="material-symbols-outlined text-headline-sm">close</span>
        </button>
    </div>
    <form method="POST" action="{{ route('admin.recruitment.candidates.interviews.store', '__ID__') }}" id="interview-form" class="flex-1 overflow-y-auto p-space-xl flex flex-col gap-space-md">
        @csrf
        <div class="p-space-md rounded-xl bg-surface-container-low flex items-start gap-space-sm">
            <span class="material-symbols-outlined text-primary text-body-lg shrink-0 mt-0.5">person</span>
            <p class="font-body-sm text-body-sm text-on-surface-variant">Candidat : <span class="text-on-surface font-semibold" id="interview-candidate-name">—</span></p>
        </div>
        <div class="flex flex-col gap-space-xs">
            <label class="font-label-md text-label-md text-on-surface font-medium" for="interview-date">Date et heure <span class="text-error">*</span></label>
            <input class="w-full h-10 px-3 rounded-xl bg-surface-container-lowest text-on-surface font-body-md text-body-md focus:outline-none ring-1 ring-outline-variant focus:ring-2 focus:ring-primary transition-all" id="interview-date" name="scheduled_at" type="datetime-local" required/>
        </div>
        <div class="flex flex-col gap-space-xs">
            <label class="font-label-md text-label-md text-on-surface font-medium" for="interview-interviewer">Intervieweur <span class="text-error">*</span></label>
            <input class="w-full h-10 px-3 rounded-xl bg-surface-container-lowest text-on-surface font-body-md text-body-md placeholder:text-on-surface-variant focus:outline-none ring-1 ring-outline-variant focus:ring-2 focus:ring-primary transition-all" id="interview-interviewer" name="interviewer" placeholder="Ex: Amel Ben Salah" required/>
        </div>
        <div class="flex flex-col gap-space-xs">
            <label class="font-label-md text-label-md text-on-surface font-medium" for="interview-type">Type</label>
            <div class="relative">
                <select class="w-full appearance-none h-10 pl-3 pr-8 rounded-xl bg-surface-container-lowest text-on-surface font-body-md text-body-md focus:outline-none ring-1 ring-outline-variant focus:ring-2 focus:ring-primary transition-all cursor-pointer" id="interview-type" name="type">
                    @foreach (App\Models\Interview::TYPES as $key => $label)
                    <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
                <span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-on-surface-variant pointer-events-none text-body-md">expand_more</span>
            </div>
        </div>
        <div class="flex flex-col gap-space-xs">
            <label class="font-label-md text-label-md text-on-surface font-medium" for="interview-notes">Notes</label>
            <textarea class="w-full min-h-[96px] px-3 py-2 rounded-xl bg-surface-container-lowest text-on-surface font-body-md text-body-md placeholder:text-on-surface-variant focus:outline-none ring-1 ring-outline-variant focus:ring-2 focus:ring-primary transition-all resize-y" id="interview-notes" name="notes" placeholder="Ex: Entretien technique — étude de cas d'architecture..." rows="2"></textarea>
        </div>
    </form>
    <div class="p-space-lg bg-surface-container-lowest shadow-[0_-4px_12px_rgba(0,0,0,0.03)] flex items-center justify-between gap-space-sm">
        <button class="h-10 px-space-lg rounded-xl bg-surface-container-low hover:bg-surface-container text-on-surface font-label-lg text-label-lg transition-colors" type="button" onclick="closeModal('interview-modal')">Annuler</button>
        <button class="h-10 px-space-xl rounded-xl bg-primary-fill hover:bg-primary-container text-on-primary font-label-lg text-label-lg shadow-md flex items-center gap-space-xs transition-all transform active:scale-95" type="submit" form="interview-form">
            <span class="material-symbols-outlined text-body-md">event_available</span>
            <span>Planifier</span>
        </button>
    </div>
</div>
</div>

<script>
function openModal(id) {
    document.getElementById(id).classList.remove('hidden');
    document.getElementById(id).classList.add('flex');
    document.body.style.overflow = 'hidden';
}
function closeModal(id) {
    document.getElementById(id).classList.add('hidden');
    document.getElementById(id).classList.remove('flex');
    document.body.style.overflow = '';
}

function fillForm(form, data) {
    Object.keys(data).forEach(function (key) {
        var field = form.querySelector('[name="' + key + '"]');
        if (field && data[key] !== null) {
            field.value = data[key];
        }
    });
}

function openCandidateModal(button, presetStage) {
    var form = document.getElementById('candidate-form');
    form.reset();
    form.action = "{{ route('admin.recruitment.candidates.store') }}";
    document.getElementById('candidate-method').value = '';
    document.getElementById('candidate-modal-title').textContent = 'Nouveau Candidat';
    if (presetStage) {
        form.querySelector('[name="stage"]').value = presetStage;
    }
    if (button && button.dataset.json) {
        var data = JSON.parse(button.dataset.json);
        document.getElementById('candidate-modal-title').textContent = 'Modifier ' + data.name;
        fillForm(form, data);
        form.action = "{{ route('admin.recruitment.candidates.update', '__ID__') }}".replace('__ID__', data.id);
        document.getElementById('candidate-method').value = 'PUT';
    }
    openModal('candidate-modal');
}

function openOfferModal(button) {
    var form = document.getElementById('offer-form');
    form.reset();
    form.action = "{{ route('admin.recruitment.offers.store') }}";
    document.getElementById('offer-method').value = '';
    document.getElementById('offer-modal-title').textContent = 'Nouvelle Offre';
    if (button && button.dataset.json) {
        var data = JSON.parse(button.dataset.json);
        document.getElementById('offer-modal-title').textContent = 'Modifier ' + data.title;
        fillForm(form, data);
        form.action = "{{ route('admin.recruitment.offers.update', '__ID__') }}".replace('__ID__', data.id);
        document.getElementById('offer-method').value = 'PUT';
    }
    openModal('offer-modal');
}

function openInterviewModal(button) {
    var form = document.getElementById('interview-form');
    form.reset();
    form.action = "{{ route('admin.recruitment.candidates.interviews.store', '__ID__') }}".replace('__ID__', button.dataset.candidateId);
    document.getElementById('interview-candidate-name').textContent = button.dataset.candidateName;
    openModal('interview-modal');
}

// Drag & drop natif HTML5
var draggedId = null;
document.querySelectorAll('[draggable="true"][data-candidate-id]').forEach(function (card) {
    card.addEventListener('dragstart', function (e) {
        draggedId = card.getAttribute('data-candidate-id');
        e.dataTransfer.setData('text/plain', draggedId);
        e.dataTransfer.effectAllowed = 'move';
        card.classList.add('opacity-50');
    });
    card.addEventListener('dragend', function () {
        card.classList.remove('opacity-50');
        draggedId = null;
    });
});

document.querySelectorAll('[data-dropzone]').forEach(function (zone) {
    zone.addEventListener('dragover', function (e) {
        e.preventDefault();
        e.dataTransfer.dropEffect = 'move';
        zone.classList.add('bg-primary/5');
    });
    zone.addEventListener('dragleave', function () {
        zone.classList.remove('bg-primary/5');
    });
    zone.addEventListener('drop', function (e) {
        e.preventDefault();
        zone.classList.remove('bg-primary/5');
        var id = e.dataTransfer.getData('text/plain') || draggedId;
        var stage = zone.getAttribute('data-stage');
        if (!id || !stage) return;
        fetch("{{ route('admin.recruitment.candidates.stage', '__ID__') }}".replace('__ID__', id), {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ stage: stage })
        }).then(function (res) {
            if (res.ok) {
                window.location.reload();
            } else {
                alert('Impossible de déplacer la carte.');
            }
        });
    });
});

// Raccourci "/" pour focus la recherche
document.addEventListener('keydown', function (e) {
    if (e.key === '/' && document.activeElement.tagName !== 'INPUT' && document.activeElement.tagName !== 'TEXTAREA' && document.activeElement.tagName !== 'SELECT') {
        e.preventDefault();
        var search = document.getElementById('recruitment-search');
        if (search) search.focus();
    }
});

// Ouverture rapide depuis la navbar
(function () {
    var quickCreate = document.querySelector('[data-quick-create]');
    var action = quickCreate ? quickCreate.dataset.quickCreate : null;
    if (action === 'offer') {
        openOfferModal(null);
    } else if (action === 'candidate') {
        openCandidateModal(null);
    }
})();
</script>
</x-admin-shell>
