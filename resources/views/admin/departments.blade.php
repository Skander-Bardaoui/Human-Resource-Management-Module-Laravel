<x-admin-shell active="departments" title="Gestion des Départements">
@php
    $managerShort = function (?string $name): string {
        if (! $name) {
            return 'Lead à assigner';
        }
        $parts = preg_split('/\s+/', trim($name));
        return strtoupper(substr($parts[0], 0, 1)).'. '.$parts[count($parts) - 1];
    };
    $treeWidth = max(320, $departmentCount * 170);
@endphp
<div class="flex flex-col w-full pb-16 space-y-8">
@if (session('success'))
<div class="fixed bottom-4 left-4 right-4 sm:left-auto sm:bottom-6 sm:right-6 z-[80] sm:max-w-sm p-space-md rounded-xl bg-tertiary-fixed text-on-tertiary-fixed shadow-lg flex items-start gap-space-sm" data-flash-toast role="status">
<span class="material-symbols-outlined text-body-lg shrink-0">check_circle</span>
<span class="font-label-md text-label-md">{{ session('success') }}</span>
<button class="ml-2 text-on-tertiary-fixed/70 hover:text-on-tertiary-fixed" type="button" onclick="this.parentElement.remove()" aria-label="Fermer">
<span class="material-symbols-outlined text-body-md">close</span>
</button>
</div>
@endif
@if (session('error'))
<div class="fixed bottom-4 left-4 right-4 sm:left-auto sm:bottom-6 sm:right-6 z-[80] sm:max-w-sm p-space-md rounded-xl bg-error-container text-on-error-container shadow-lg flex items-start gap-space-sm" data-flash-toast role="alert">
<span class="material-symbols-outlined text-body-lg shrink-0">error</span>
<span class="font-label-md text-label-md">{{ session('error') }}</span>
<button class="ml-2 text-on-error-container/70 hover:text-on-error-container" type="button" onclick="this.parentElement.remove()" aria-label="Fermer">
<span class="material-symbols-outlined text-body-md">close</span>
</button>
</div>
@endif
<!-- Breadcrumb & Top Bar -->
<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pt-6">
<div class="flex flex-col gap-1">
<div class="flex items-center gap-2 font-label-sm text-label-sm text-on-surface-variant">
<a class="hover:text-primary transition-colors" href="{{ route('admin.dashboard') }}">Accueil</a>
<span class="material-symbols-outlined text-body-sm">chevron_right</span>
<span class="text-on-surface font-semibold">Départements &amp; Structure</span>
</div>
<h1 class="font-headline-lg text-headline-lg text-on-surface tracking-tight">Gestion des Départements</h1>
<p class="font-body-sm text-body-sm text-on-surface-variant">
        {{ $activeCount }} collaborateurs répartis sur {{ $departmentCount }} départements opérationnels • Carthage Tech Solutions SARL, Tunis Lac II
      </p>
</div>
<!-- Actions & View Switcher -->
<div class="flex items-center gap-3">
<div class="bg-surface-container-low p-1 rounded-xl flex items-center shadow-sm">
<button class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-surface-container-lowest text-primary font-label-md text-label-md shadow-sm transition-all" id="viewGridBtn" onclick="switchView('grid')">
<span class="material-symbols-outlined text-body-sm">grid_view</span>
<span>Vue Grille Cartes</span>
</button>
<button class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-on-surface-variant hover:text-on-surface font-label-md text-label-md transition-all" id="viewTreeBtn" onclick="switchView('tree')">
<span class="material-symbols-outlined text-body-sm">account_tree</span>
<span>Vue Organigramme</span>
</button>
</div>
<button class="h-9 px-4 rounded-xl bg-primary-container hover:bg-primary-fill font-label-lg text-label-lg text-on-primary flex items-center gap-1.5 shadow-sm transition-all hover:shadow-md" onclick="openDeptModal()" type="button">
<span class="material-symbols-outlined text-body-md">domain_add</span>
<span>+ Nouveau Département</span>
</button>
</div>
</div>
<!-- KPI Summary Banner -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
<!-- Stat 1 -->
<div class="bg-surface-container-lowest p-5 rounded-xl shadow-sm flex flex-col justify-between relative overflow-hidden group hover:shadow-md transition-shadow">
<div class="absolute -right-3 -top-3 w-16 h-16 bg-primary-container/10 rounded-full blur-xl pointer-events-none"></div>
<div class="flex items-center justify-between">
<span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Départements</span>
<div class="w-8 h-8 rounded-lg bg-surface-container-low flex items-center justify-center text-primary">
<span class="material-symbols-outlined text-headline-sm">apartment</span>
</div>
</div>
<div class="mt-3 flex items-baseline gap-2">
<span class="font-display-lg text-display-lg text-on-surface font-bold tracking-tight">{{ str_pad((string) $departmentCount, 2, '0', STR_PAD_LEFT) }}</span>
<span class="font-label-sm text-label-sm text-on-surface-variant">Unités actives</span>
</div>
<div class="mt-2 flex items-center gap-1 font-label-sm text-label-sm {{ $leadCoverage >= 100 ? 'text-tertiary' : 'text-secondary' }}">
<span class="material-symbols-outlined text-body-sm">{{ $leadCoverage >= 100 ? 'check_circle' : 'pending' }}</span>
<span>{{ $leadCoverage >= 100 ? "Toutes pourvues d'un Lead" : $managerCount.' / '.$departmentCount.' leads assignés' }}</span>
</div>
</div>
<!-- Stat 2 -->
<div class="bg-surface-container-lowest p-5 rounded-xl shadow-sm flex flex-col justify-between relative overflow-hidden group hover:shadow-md transition-shadow">
<div class="absolute -right-3 -top-3 w-16 h-16 bg-secondary-container/20 rounded-full blur-xl pointer-events-none"></div>
<div class="flex items-center justify-between">
<span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Effectif Global</span>
<div class="w-8 h-8 rounded-lg bg-surface-container-low flex items-center justify-center text-secondary">
<span class="material-symbols-outlined text-headline-sm">groups</span>
</div>
</div>
<div class="mt-3 flex items-baseline gap-2">
<span class="font-display-lg text-display-lg text-on-surface font-bold tracking-tight">{{ $activeCount }}</span>
<span class="font-label-sm text-label-sm text-on-surface-variant">Salariés déclarés</span>
</div>
<div class="mt-2 flex items-center gap-1 font-label-sm text-label-sm text-on-surface-variant">
<span class="font-numeric-md text-numeric-md font-semibold text-primary">{{ $cnssAffiliationRate }}%</span>
<span>Affiliés CNSS Tunisie</span>
</div>
</div>
<!-- Stat 3 -->
<div class="bg-surface-container-lowest p-5 rounded-xl shadow-sm flex flex-col justify-between relative overflow-hidden group hover:shadow-md transition-shadow">
<div class="flex items-center justify-between">
<span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Managers de Pôle</span>
<div class="w-8 h-8 rounded-lg bg-surface-container-low flex items-center justify-center text-primary-container">
<span class="material-symbols-outlined text-headline-sm">manage_accounts</span>
</div>
</div>
<div class="mt-3 flex items-baseline gap-2">
<span class="font-display-lg text-display-lg text-on-surface font-bold tracking-tight">{{ str_pad((string) $managerCount, 2, '0', STR_PAD_LEFT) }}</span>
<span class="font-label-sm text-label-sm text-on-surface-variant">Cadres Dirigeants</span>
</div>
<div class="mt-2 flex items-center gap-1 font-label-sm text-label-sm text-tertiary">
<span class="material-symbols-outlined text-body-sm">military_tech</span>
<span>Ratio encadrement : 1 / {{ $leadershipRatio ?? '—' }}</span>
</div>
</div>
<!-- Stat 4 -->
<div class="bg-surface-container-lowest p-5 rounded-xl shadow-sm flex flex-col justify-between relative overflow-hidden group hover:shadow-md transition-shadow">
<div class="absolute -right-3 -top-3 w-16 h-16 bg-tertiary-fixed/30 rounded-full blur-xl pointer-events-none"></div>
<div class="flex items-center justify-between">
<span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Masse Salariale Brute</span>
<div class="w-8 h-8 rounded-lg bg-surface-container-low flex items-center justify-center text-tertiary">
<span class="material-symbols-outlined text-headline-sm">payments</span>
</div>
</div>
<div class="mt-3 flex items-baseline gap-1">
<span class="font-display-lg text-display-lg text-on-surface font-bold tracking-tight font-numeric-md">{{ number_format($totalGross, 0, ',', ' ') }}</span>
<span class="font-label-sm text-label-sm text-on-surface-variant">TND / mois</span>
</div>
<div class="mt-2 flex items-center gap-1 font-label-sm text-label-sm text-on-surface-variant">
<span class="w-2 h-2 rounded-full bg-tertiary"></span>
<span>Budget conforme à {{ $budgetComplianceRate }}%</span>
</div>
</div>
</div>
<!-- VIEW 1: GRID MODE (Active by default) -->
<div class="flex flex-col space-y-6" id="gridContainer">
<!-- Filters & Search mini strip -->
<div class="flex flex-col sm:flex-row items-center justify-between gap-3 bg-surface-container-lowest p-3 rounded-xl shadow-sm">
<div class="flex items-center gap-2 w-full sm:w-auto">
<div class="relative w-full sm:w-72">
<span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-body-md pointer-events-none">filter_list</span>
<input class="w-full h-9 pl-9 pr-4 rounded-lg bg-surface-container-low font-body-sm text-body-sm text-on-surface placeholder:text-on-surface-variant focus:outline-none focus:bg-surface-container" id="deptSearchInput" onkeyup="filterDepartments()" placeholder="Filtrer par nom, lead, technologie..." type="text"/>
</div>
</div>
<div class="flex items-center gap-2 text-on-surface-variant font-label-sm text-label-sm">
<span>Trié par :</span>
<button class="bg-surface-container-low px-2.5 py-1 rounded-md text-on-surface font-semibold flex items-center gap-1 hover:bg-surface-container transition-colors" id="sortDeptBtn" type="button">
<span id="sortDeptLabel">Effectif décroissant</span>
<span class="material-symbols-outlined text-body-sm" id="sortDeptIcon">arrow_downward</span>
</button>
</div>
</div>
<!-- Department Cards (3 columns grid) -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="departmentsCardsList">
@foreach ($departments as $department)
@php
    $teamCount = (int) $department->team_count;
    $occupancy = max(0, min(100, (int) $department->occupancy));
    $metricValue = $department->metric_value ?: $occupancy.'%';
    $filterText = $department->name.' '.($department->manager?->name ?? '').' '.implode(' ', $department->specialties ?? []).' '.implode(' ', $department->tags ?? []);
@endphp
<div class="dept-card bg-surface-container-lowest rounded-xl p-6 shadow-sm hover:shadow-md transition-all flex flex-col justify-between relative group" data-count="{{ $teamCount }}" data-name="{{ strtolower($filterText) }}">
<div class="space-y-4">
<!-- Top Tag & Options -->
<div class="flex items-start justify-between">
<div class="flex items-center gap-2">
<span class="w-2.5 h-2.5 rounded-full {{ $department->dot_color }}"></span>
<span class="font-label-sm text-label-sm uppercase font-semibold {{ $department->text_color }}">{{ $department->tag ?? 'Département' }}</span>
</div>
<div class="flex items-center gap-1">
<span class="px-2 py-0.5 rounded-full bg-surface-container font-numeric-md text-label-sm text-on-surface font-semibold">{{ $teamCount }} Collaborateurs</span>
<button class="text-on-surface-variant hover:text-on-surface p-1 rounded-lg hover:bg-surface-container transition-colors js-edit-dept" data-department="{{ json_encode(['id' => $department->id, 'name' => $department->name, 'manager_id' => $department->manager_id, 'code' => $department->code, 'budget' => (float) $department->monthly_budget, 'description' => $department->description]) }}" title="Modifier le département" type="button">
<span class="material-symbols-outlined text-body-md">more_vert</span>
</button>
</div>
</div>
<!-- Title & Description -->
<div>
<h3 class="font-headline-md text-headline-md text-on-surface font-bold tracking-tight">{{ $department->name }}</h3>
<p class="font-body-sm text-body-sm text-on-surface-variant mt-1">{{ $department->description ?: 'Périmètre opérationnel en cours de définition.' }}</p>
</div>
<!-- Manager Row -->
<div class="bg-surface-container-low p-3 rounded-xl flex items-center justify-between">
<div class="flex items-center gap-3 min-w-0">
@if ($department->manager?->avatar_url)
<img class="w-10 h-10 rounded-full object-cover shrink-0 shadow-sm" data-alt="Portrait professionnel de {{ $department->manager->name }}" src="{{ $department->manager->avatar_url }}"/>
@else
<div class="w-10 h-10 rounded-full bg-surface-container-high text-on-surface flex items-center justify-center font-label-md text-label-md font-bold shrink-0 shadow-sm">
{{ $department->manager?->initials() ?? '?' }}
</div>
@endif
<div class="flex flex-col min-w-0">
<span class="font-label-md text-label-md text-on-surface font-bold truncate">{{ $department->manager?->name ?? 'Lead non assigné' }}</span>
<span class="font-label-sm text-label-sm text-on-surface-variant truncate">{{ $department->manager ? $department->manager->position.' • '.$department->manager->email : 'Assignez un responsable de pôle' }}</span>
</div>
</div>
@if ($department->manager)
<a class="material-symbols-outlined text-on-surface-variant text-body-md shrink-0 ml-2 hover:text-primary transition-colors" href="mailto:{{ $department->manager->email }}" title="Contacter le responsable">mail</a>
@else
<span class="material-symbols-outlined text-on-surface-variant text-body-md shrink-0 ml-2">person_add</span>
@endif
</div>
<!-- Sub-poles & Tags -->
<div class="space-y-2">
<div class="flex items-center justify-between font-label-sm text-label-sm">
<span class="text-on-surface-variant">{{ $department->specialties_label }} :</span>
<span class="font-semibold text-on-surface">{{ implode(', ', $department->specialties ?? []) ?: 'À définir' }}</span>
</div>
<div class="flex flex-wrap gap-1.5 pt-1">
@forelse ($department->tags ?? [] as $tag)
<span class="px-2 py-0.5 rounded-md bg-surface-container font-label-sm text-label-sm text-on-surface-variant">{{ $tag }}</span>
@empty
<span class="px-2 py-0.5 rounded-md bg-surface-container font-label-sm text-label-sm text-on-surface-variant">Aucun tag</span>
@endforelse
</div>
</div>
<!-- Workload & Budget metrics -->
<div class="space-y-2 pt-2">
<div class="flex items-center justify-between font-label-sm text-label-sm">
<span class="text-on-surface-variant">{{ $department->occupancy_label }}</span>
<span class="font-numeric-md font-bold {{ $department->metric_color }}">{{ $metricValue }}</span>
</div>
<div class="w-full h-1.5 bg-surface-container rounded-full overflow-hidden">
<div class="h-full {{ $department->bar_color }} rounded-full" style="width: {{ $occupancy }}%"></div>
</div>
<div class="flex items-center justify-between pt-1">
<span class="font-label-sm text-label-sm text-on-surface-variant">Budget Salarial Mensuel</span>
<span class="font-numeric-md text-body-sm font-bold text-on-surface">{{ $department->monthly_budget > 0 ? number_format((float) $department->monthly_budget, 0, ',', ' ').' TND' : 'Non défini' }}</span>
</div>
</div>
</div>
<!-- Footer / Quick Links -->
<div class="pt-5 mt-4 flex items-center justify-between">
<div class="flex -space-x-2 overflow-hidden items-center">
@foreach ($department->employees->take(3) as $member)
@if ($member->avatar_url)
<img class="inline-block h-6 w-6 rounded-full ring-2 ring-surface-container-lowest object-cover" data-alt="Portrait de {{ $member->name }}" src="{{ $member->avatar_url }}"/>
@else
<span class="inline-flex items-center justify-center h-6 w-6 rounded-full bg-surface-container-high text-on-surface font-label-sm text-label-sm font-bold ring-2 ring-surface-container-lowest">{{ $member->initials() }}</span>
@endif
@endforeach
@if ($teamCount > 3)
<span class="inline-flex items-center justify-center h-6 w-6 rounded-full bg-surface-container-high text-on-surface font-label-sm text-label-sm font-bold ring-2 ring-surface-container-lowest">+{{ $teamCount - 3 }}</span>
@elseif ($teamCount === 0)
<span class="font-label-sm text-label-sm text-on-surface-variant italic">Aucun membre affecté</span>
@endif
</div>
<div class="flex items-center gap-1">
<a class="px-2.5 py-1 rounded-lg text-primary hover:bg-surface-container-low font-label-sm text-label-sm font-semibold transition-colors" href="{{ route('admin.employees', ['department' => $department->slug]) }}">Voir l'équipe</a>
<button class="p-1 rounded-lg text-on-surface-variant hover:text-on-surface hover:bg-surface-container-low transition-colors js-edit-dept" data-department="{{ json_encode(['id' => $department->id, 'name' => $department->name, 'manager_id' => $department->manager_id, 'code' => $department->code, 'budget' => (float) $department->monthly_budget, 'description' => $department->description]) }}" title="Paramètres" type="button">
<span class="material-symbols-outlined text-body-sm">settings</span>
</button>
</div>
</div>
</div>
@endforeach
<!-- Add Department Quick Placeholder Tile -->
<div class="border-2 border-dashed border-outline-variant/60 rounded-xl p-6 flex flex-col items-center justify-center text-center space-y-3 hover:border-primary transition-colors cursor-pointer group bg-surface/40 hover:bg-surface-container-low" id="addDeptTile" onclick="openDeptModal()">
<div class="w-12 h-12 rounded-full bg-surface-container flex items-center justify-center text-on-surface-variant group-hover:bg-primary-container group-hover:text-on-primary transition-all">
<span class="material-symbols-outlined text-headline-md">add</span>
</div>
<div>
<h4 class="font-headline-sm text-headline-sm text-on-surface font-semibold">Créer une structure</h4>
<p class="font-body-sm text-body-sm text-on-surface-variant max-w-xs mt-1">Définissez un nouveau département, assignez un lead et associez une ligne budgétaire.</p>
</div>
<span class="font-label-md text-label-md text-primary font-bold group-hover:underline">Ajouter un département →</span>
</div>
</div>
</div>
<!-- VIEW 2: HIERARCHICAL ORG TREE (Hidden by default, toggled via JS) -->
<div class="hidden flex-col space-y-6" id="treeContainer">
<div class="bg-surface-container-lowest p-6 rounded-xl shadow-sm overflow-x-auto">
<div class="min-w-[900px] flex flex-col items-center py-6">
<!-- Level 0: Direction Générale -->
<div class="flex flex-col items-center">
<div class="bg-surface-container-low p-4 rounded-xl shadow-sm border border-outline-variant/30 w-72 flex items-center gap-3">
<div class="w-12 h-12 rounded-full bg-primary-container text-on-primary flex items-center justify-center shadow-sm shrink-0 font-bold">DG</div>
<div class="flex flex-col min-w-0">
<span class="font-label-sm text-label-sm text-primary font-bold uppercase">Direction Générale</span>
<span class="font-label-lg text-label-lg text-on-surface font-bold truncate">{{ optional(auth()->user())->name ?? 'Direction Générale' }}</span>
<span class="font-body-sm text-body-sm text-on-surface-variant truncate">Président Directeur Général</span>
</div>
</div>
<!-- Connecting stem -->
<div class="w-0.5 h-8 bg-outline-variant"></div>
</div>
<!-- Cross Branch Line -->
<div class="h-0.5 bg-outline-variant" style="width: {{ max(280, $treeWidth - 30) }}px"></div>
<!-- Level 1: Department Directors -->
<div class="flex justify-between pt-0" style="width: {{ $treeWidth }}px">
@foreach ($departments as $department)
<div class="flex flex-col items-center">
<div class="w-0.5 h-6 bg-outline-variant"></div>
<div class="bg-surface-container-lowest p-3 rounded-xl shadow-sm border border-outline-variant/30 w-40 flex flex-col items-center text-center">
<span class="w-2 h-2 rounded-full {{ $department->dot_color }} mb-1"></span>
<span class="font-label-sm text-label-sm font-bold text-on-surface">{{ \Illuminate\Support\Str::limit(str_replace(' & ', ' ', $department->name), 26) }}</span>
<span class="font-body-sm text-body-sm text-on-surface-variant">{{ $managerShort($department->manager?->name) }}</span>
<span class="mt-1 px-2 py-0.5 rounded-full bg-surface-container-low font-numeric-md text-label-sm font-semibold">{{ $department->team_count }} pers.</span>
</div>
<!-- Sub specialists mini stem -->
<div class="w-0.5 h-4 bg-outline-variant"></div>
<div class="bg-surface-container-low p-2 rounded-lg text-center w-36">
<p class="font-label-sm text-label-sm text-on-surface-variant">{{ implode(' • ', $department->sub_teams ?? []) ?: 'Équipe en constitution' }}</p>
</div>
</div>
@endforeach
</div>
</div>
</div>
</div>
<!-- Tunisian Regulatory & Payroll Cross-Department Breakdown -->
<div class="bg-surface-container-lowest p-6 rounded-xl shadow-sm space-y-4">
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
<div>
<h3 class="font-headline-sm text-headline-sm text-on-surface font-semibold">Répartition Budgétaire &amp; Conformité Sociale (Tunisie)</h3>
<p class="font-body-sm text-body-sm text-on-surface-variant">Consolidation des charges patronales CNSS (16.57%) et accidents de travail (0.5%) par département pour l'exercice {{ now()->year }}.</p>
</div>
<div class="flex items-center gap-2">
<a class="px-3 py-1.5 rounded-lg bg-surface-container-low hover:bg-surface-container font-label-md text-label-md text-on-surface flex items-center gap-1.5 transition-colors" href="{{ route('admin.departments.export') }}">
<span class="material-symbols-outlined text-body-sm">download</span>
<span>Exporter CSV / PDF</span>
</a>
</div>
</div>
<!-- Interactive mini table -->
<div class="overflow-x-auto">
<table class="w-full text-left font-body-sm text-body-sm">
<thead>
<tr class="bg-surface-container-low text-on-surface-variant font-label-md text-label-md uppercase">
<th class="py-3 px-4 rounded-l-lg">Département</th>
<th class="py-3 px-4">Responsable</th>
<th class="py-3 px-4 text-center">Effectif</th>
<th class="py-3 px-4 text-right">Salaire Brut Total</th>
<th class="py-3 px-4 text-right">Cotisations CNSS (Patronal)</th>
<th class="py-3 px-4 text-center rounded-r-lg">Statut Conformité</th>
</tr>
</thead>
<tbody class="text-on-surface divide-y divide-surface-container">
@forelse ($departments as $department)
<tr class="hover:bg-surface-container-low/50 transition-colors">
<td class="py-3.5 px-4 font-semibold flex items-center gap-2">
<span class="w-2 h-2 rounded-full {{ $department->dot_color }}"></span>
<span>{{ $department->name }}</span>
</td>
<td class="py-3.5 px-4">{{ $department->manager?->name ?? 'Non assigné' }}</td>
<td class="py-3.5 px-4 text-center font-numeric-md">{{ $department->team_count }}</td>
<td class="py-3.5 px-4 text-right font-numeric-md font-semibold">{{ number_format((float) $department->gross_total, 3, '.', ',') }} TND</td>
<td class="py-3.5 px-4 text-right font-numeric-md text-on-surface-variant">{{ number_format((float) $department->cnss_total, 3, '.', ',') }} TND</td>
<td class="py-3.5 px-4 text-center">
@if ($department->monthly_budget <= 0)
<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-label-sm bg-surface-container text-on-surface-variant font-semibold">
<span class="w-1.5 h-1.5 rounded-full bg-outline"></span>
                Budget à définir
              </span>
@elseif ($department->within_budget)
<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-label-sm bg-tertiary/10 text-tertiary font-semibold">
<span class="w-1.5 h-1.5 rounded-full bg-tertiary"></span>
                Conforme
              </span>
@else
<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-label-sm bg-error-container text-on-error-container font-semibold">
<span class="w-1.5 h-1.5 rounded-full bg-error"></span>
                Dépassement
              </span>
@endif
</td>
</tr>
@empty
<tr>
<td class="py-6 px-4 text-center text-on-surface-variant" colspan="6">Aucun département enregistré.</td>
</tr>
@endforelse
</tbody>
</table>
</div>
</div>
<!-- SLIDE-OVER DRAWER: Nouveau Département / Modification -->
<div class="hidden fixed inset-0 z-50 overflow-hidden flex justify-end" id="newDeptModal">
<div class="fixed inset-0 bg-black/60 backdrop-blur-xs transition-opacity" onclick="document.getElementById('newDeptModal').classList.add('hidden')"></div>
<div class="relative w-full max-w-2xl bg-surface-container-lowest shadow-2xl flex flex-col h-full z-10 overflow-hidden">
<!-- Drawer Header -->
<div class="p-space-xl bg-surface-container-low/60 flex items-start justify-between gap-space-md">
<div class="flex items-center gap-space-md">
<div class="w-12 h-12 rounded-xl bg-primary-container text-on-primary flex items-center justify-center shadow-sm shrink-0">
<span class="material-symbols-outlined text-headline-lg">account_tree</span>
</div>
<div class="flex flex-col">
<h2 class="font-headline-md text-headline-md text-on-surface tracking-tight" id="deptModalTitle">Nouveau Département</h2>
<p class="font-body-sm text-body-sm text-on-surface-variant" id="deptModalSubtitle">Structuration organisationnelle • Carthage Tech</p>
</div>
</div>
<button aria-label="Fermer" class="w-9 h-9 rounded-xl hover:bg-surface-container text-on-surface-variant hover:text-on-surface flex items-center justify-center transition-colors shrink-0" onclick="document.getElementById('newDeptModal').classList.add('hidden')" type="button">
<span class="material-symbols-outlined text-headline-sm">close</span>
</button>
</div>
<!-- Drawer Form Content -->
<form class="flex-1 overflow-y-auto p-space-xl flex flex-col gap-space-md" id="deptForm" method="POST" action="{{ route('admin.departments.store') }}">
@csrf
<input id="deptMethod" name="_method" type="hidden" value="POST"/>
@if ($errors->any())
<div class="p-space-md rounded-xl bg-error-container text-on-error-container flex items-start gap-space-sm">
<span class="material-symbols-outlined text-body-lg shrink-0 mt-0.5">error</span>
<div class="flex flex-col gap-1">
<span class="font-label-md text-label-md font-semibold">Veuillez corriger les informations suivantes</span>
<ul class="list-disc list-inside font-body-sm text-body-sm space-y-0.5">
@foreach ($errors->all() as $error)
<li>{{ $error }}</li>
@endforeach
</ul>
</div>
</div>
@endif
<div class="flex flex-col gap-space-xs">
<label class="font-label-md text-label-md text-on-surface font-medium" for="deptName">Intitulé du Département</label>
<input class="w-full h-10 px-3 rounded-xl bg-surface-container-lowest text-on-surface font-body-md text-body-md focus:outline-none ring-1 ring-outline-variant focus:ring-2 focus:ring-primary transition-all" id="deptName" name="name" placeholder="Ex: Data Intelligence &amp; IA" type="text" value="{{ old('name') }}" required/>
</div>
<div class="flex flex-col gap-space-xs">
<label class="font-label-md text-label-md text-on-surface font-medium" for="deptManager">Responsable de Pôle (Lead)</label>
<div class="relative">
<select class="w-full appearance-none h-10 pl-3 pr-8 rounded-xl bg-surface-container-lowest text-on-surface font-body-md text-body-md focus:outline-none ring-1 ring-outline-variant focus:ring-2 focus:ring-primary transition-all cursor-pointer" id="deptManager" name="manager_id">
<option value="">Sélectionner un collaborateur...</option>
@foreach ($employees as $employee)
<option value="{{ $employee->id }}" @selected((string) old('manager_id') === (string) $employee->id)>{{ $employee->name }} ({{ $employee->position }})</option>
@endforeach
</select>
<span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-on-surface-variant pointer-events-none text-body-md">expand_more</span>
</div>
</div>
<div class="flex flex-col gap-space-xs">
<label class="font-label-md text-label-md text-on-surface font-medium" for="deptCode">Code Analytique / Centre de Coûts</label>
<input class="w-full h-10 px-3 rounded-xl bg-surface-container-lowest text-on-surface font-body-md text-body-md focus:outline-none ring-1 ring-outline-variant focus:ring-2 focus:ring-primary transition-all" id="deptCode" name="code" placeholder="Ex: CC-TECH-2025" type="text" value="{{ old('code') }}"/>
</div>
<div class="flex flex-col gap-space-xs">
<label class="font-label-md text-label-md text-on-surface font-medium" for="deptBudget">Budget Mensuel Estimé (TND)</label>
<input class="w-full h-10 px-3 rounded-xl bg-surface-container-lowest text-on-surface font-body-md text-body-md focus:outline-none ring-1 ring-outline-variant focus:ring-2 focus:ring-primary transition-all" id="deptBudget" min="0" name="monthly_budget" placeholder="25000" step="0.001" type="number" value="{{ old('monthly_budget') }}"/>
</div>
<div class="flex flex-col gap-space-xs">
<label class="font-label-md text-label-md text-on-surface font-medium" for="deptDescription">Missions &amp; Objectifs</label>
<textarea class="w-full min-h-[96px] px-3 py-2 rounded-xl bg-surface-container-lowest text-on-surface font-body-md text-body-md placeholder:text-on-surface-variant focus:outline-none ring-1 ring-outline-variant focus:ring-2 focus:ring-primary transition-all resize-y" id="deptDescription" name="description" placeholder="Décrivez succinctement le périmètre opérationnel..." rows="3">{{ old('description') }}</textarea>
</div>
</form>
<!-- Drawer Sticky Footer Actions -->
<div class="p-space-lg bg-surface-container-lowest shadow-[0_-4px_12px_rgba(0,0,0,0.03)] flex items-center justify-between gap-space-sm">
<button class="h-10 px-space-lg rounded-xl bg-surface-container-low hover:bg-surface-container text-on-surface font-label-lg text-label-lg transition-colors" onclick="document.getElementById('newDeptModal').classList.add('hidden')" type="button">
          Annuler
        </button>
<button class="h-10 px-space-xl rounded-xl bg-primary-fill hover:bg-primary-container text-on-primary font-label-lg text-label-lg shadow-md flex items-center gap-space-xs transition-all transform active:scale-95" id="deptSubmitBtn" type="submit" form="deptForm">
<span class="material-symbols-outlined text-body-md">check</span>
<span id="deptSubmitLabel">Enregistrer le Département</span>
</button>
</div>
</div>
</div>
<!-- Micro Interaction Script for Switch View & Search Filter -->
<script>
    function switchView(mode) {
      const gridBtn = document.getElementById('viewGridBtn');
      const treeBtn = document.getElementById('viewTreeBtn');
      const gridContainer = document.getElementById('gridContainer');
      const treeContainer = document.getElementById('treeContainer');

      if (mode === 'grid') {
        gridContainer.classList.remove('hidden');
        treeContainer.classList.add('hidden');
        gridBtn.className = 'flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-surface-container-lowest text-primary font-label-md text-label-md shadow-sm transition-all';
        treeBtn.className = 'flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-on-surface-variant hover:text-on-surface font-label-md text-label-md transition-all';
      } else {
        gridContainer.classList.add('hidden');
        treeContainer.classList.remove('hidden');
        treeContainer.classList.add('flex');
        treeBtn.className = 'flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-surface-container-lowest text-primary font-label-md text-label-md shadow-sm transition-all';
        gridBtn.className = 'flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-on-surface-variant hover:text-on-surface font-label-md text-label-md transition-all';
      }
    }

    function filterDepartments() {
      const query = document.getElementById('deptSearchInput').value.toLowerCase();
      const cards = document.querySelectorAll('.dept-card');
      cards.forEach(card => {
        const dataName = (card.getAttribute('data-name') || '').toLowerCase();
        if (dataName.includes(query)) {
          card.style.display = '';
        } else {
          card.style.display = 'none';
        }
      });
    }

    function openDeptModal(department) {
      const modal = document.getElementById('newDeptModal');
      const form = document.getElementById('deptForm');
      if (!modal || !form) return;

      const isEdit = !!department;
      const storeAction = "{{ route('admin.departments.store') }}";
      const updateBase = "{{ url('/admin/departments') }}";

      document.getElementById('deptModalTitle').textContent = isEdit ? 'Modifier le Département' : 'Nouveau Département';
      document.getElementById('deptModalSubtitle').textContent = isEdit ? 'Mise à jour structurelle • Carthage Tech' : 'Structuration organisationnelle • Carthage Tech';
      document.getElementById('deptSubmitLabel').textContent = isEdit ? 'Enregistrer les modifications' : 'Enregistrer le Département';

      form.action = isEdit ? updateBase + '/' + department.id : storeAction;
      document.getElementById('deptMethod').value = isEdit ? 'PUT' : 'POST';
      document.getElementById('deptName').value = isEdit ? (department.name || '') : '';
      document.getElementById('deptManager').value = isEdit && department.manager_id ? String(department.manager_id) : '';
      document.getElementById('deptCode').value = isEdit ? (department.code || '') : '';
      document.getElementById('deptBudget').value = isEdit && department.budget ? department.budget : '';
      document.getElementById('deptDescription').value = isEdit ? (department.description || '') : '';

      modal.classList.remove('hidden');
    }

    document.querySelectorAll('.js-edit-dept').forEach(function(button) {
      button.addEventListener('click', function() {
        try {
          openDeptModal(JSON.parse(button.dataset.department));
        } catch (error) {
          openDeptModal();
        }
      });
    });

    let departmentsSortDescending = true;
    const sortDeptBtn = document.getElementById('sortDeptBtn');
    if (sortDeptBtn) {
      sortDeptBtn.addEventListener('click', function() {
        departmentsSortDescending = !departmentsSortDescending;
        const list = document.getElementById('departmentsCardsList');
        const tile = document.getElementById('addDeptTile');
        const cards = Array.from(list.querySelectorAll('.dept-card'));
        cards.sort(function(a, b) {
          const diff = parseInt(a.dataset.count || '0', 10) - parseInt(b.dataset.count || '0', 10);
          return departmentsSortDescending ? -diff : diff;
        });
        cards.forEach(function(card) { list.insertBefore(card, tile); });
        document.getElementById('sortDeptLabel').textContent = departmentsSortDescending ? 'Effectif décroissant' : 'Effectif croissant';
        document.getElementById('sortDeptIcon').textContent = departmentsSortDescending ? 'arrow_downward' : 'arrow_upward';
      });
    }

    @if ($errors->any())
    openDeptModal();
    @endif

    document.querySelectorAll('[data-flash-toast]').forEach(function(toast) {
      setTimeout(function() { toast.remove(); }, 6000);
    });
  </script>
</div>
</x-admin-shell>
