<x-admin-shell active="employees" title="Gestion des Collaborateurs">
@php
    $departmentBadges = [
        'it' => 'bg-surface-container text-primary',
        'produit' => 'bg-primary-fixed/40 text-on-primary-fixed-variant',
        'commerce' => 'bg-secondary-container/60 text-on-secondary-fixed',
        'finance' => 'bg-surface-container-high text-on-surface',
        'rh' => 'bg-surface-container-high text-primary',
    ];
    $statusBadges = [
        'actif' => ['badge' => 'bg-tertiary-fixed/40 text-tertiary', 'dot' => 'bg-tertiary'],
        'essai' => ['badge' => 'bg-secondary-container/60 text-on-secondary-container', 'dot' => 'bg-secondary'],
        'conge' => ['badge' => 'bg-primary-fixed/40 text-on-primary-fixed-variant', 'dot' => 'bg-primary'],
        'inactif' => ['badge' => 'bg-surface-container-high text-on-surface-variant', 'dot' => 'bg-outline'],
    ];
    $avatarPalette = [
        'bg-secondary-container text-on-secondary-container',
        'bg-surface-container-highest text-primary',
        'bg-primary-fixed text-primary',
        'bg-tertiary-fixed text-tertiary',
    ];
    $frenchMonths = [1 => 'Jan', 2 => 'Fév', 3 => 'Mars', 4 => 'Avr', 5 => 'Mai', 6 => 'Juin', 7 => 'Juil', 8 => 'Août', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Déc'];
    $familyStatusLabels = ['celibataire' => 'Célibataire', 'marie' => 'Marié(e)', 'divorce' => 'Divorcé(e)', 'veuf' => 'Veuf / Veuve'];
    $filterQuery = array_filter($filters, fn ($value) => $value !== '');
    $linkWith = fn (array $extra) => route('admin.employees', array_merge($filterQuery, $extra));
    $exportUrl = route('admin.employees.export', $filterQuery);
    $isEditing = $editEmployee !== null;
    $editNameParts = $isEditing ? preg_split('/\s+/', trim($editEmployee->name)) : [];
    $defaultFirstname = $editNameParts[0] ?? '';
    $defaultLastname = count($editNameParts) > 1 ? implode(' ', array_slice($editNameParts, 1)) : '';
    $defaultMobile = preg_replace('/^\+216\s*/', '', (string) old('mobile', $editEmployee?->mobile ?? ''));
    $onboardValue = fn (string $key, $fallback = '') => old($key, $onboardValues[$key] ?? $fallback);
    if ($onboardValues) {
        $defaultFirstname = $onboardValue('firstname');
        $defaultLastname = $onboardValue('lastname');
        $defaultMobile = preg_replace('/^\+216\s*/', '', (string) $onboardValue('mobile'));
    }
    $defaultWorkMobile = preg_replace('/^\+216\s*/', '', (string) old('work_mobile', $editEmployee?->work_mobile ?? ''));
    $defaultCnss = old('cnss_number', $editEmployee && $editEmployee->cnss_number !== 'Non renseigné' ? $editEmployee->cnss_number : '');
    $defaultBank = old('bank_account', $editEmployee && $editEmployee->bank_account !== 'Non renseigné' ? $editEmployee->bank_account : '');
    $existingCinFile = $isEditing ? $editEmployee->cin_file_path : null;
    $stepFieldMap = [
        1 => ['firstname', 'lastname', 'cin', 'date_of_birth', 'email', 'mobile', 'family_status', 'children', 'address', 'cin_file', 'remove_cin_file'],
        2 => ['position', 'department', 'contract_type', 'hire_date', 'status', 'work_mobile'],
        3 => ['cnss_number', 'bank_account'],
        4 => ['leave_rights', 'token_leave_days', 'token_sick_leave_days', 'employee_note'],
    ];
    $firstErrorStep = 1;
    foreach ($stepFieldMap as $stepNumber => $stepFields) {
        if (count(array_intersect($stepFields, $errors->keys())) > 0) {
            $firstErrorStep = $stepNumber;
            break;
        }
    }
    $openDrawer = $isEditing || $errors->any() || $onboardValues !== null || request()->boolean('new');
    $openView = $viewEmployee !== null && ! $openDrawer;
    $initialStep = $errors->any() ? $firstErrorStep : 1;
    $stepActiveClass = 'bg-primary-container text-on-primary shadow-xs';
    $stepIdleClass = 'text-on-surface-variant hover:bg-surface-container transition-colors';
    $stepNumberActiveClass = 'bg-surface-container-lowest text-primary';
    $stepNumberIdleClass = 'bg-surface-container text-on-surface';
@endphp
<div class="flex flex-col w-full pb-margin">
@if (session('success'))
<div class="fixed top-20 right-6 z-[80] max-w-sm p-space-md rounded-xl bg-tertiary-fixed text-on-tertiary-fixed shadow-lg flex items-start gap-space-sm" id="flash-toast" role="status">
<span class="material-symbols-outlined text-body-lg shrink-0">check_circle</span>
<span class="font-label-md text-label-md">{{ session('success') }}</span>
<button class="ml-2 text-on-tertiary-fixed/70 hover:text-on-tertiary-fixed" type="button" onclick="this.parentElement.remove()" aria-label="Fermer">
<span class="material-symbols-outlined text-body-md">close</span>
</button>
</div>
@endif
@if (session('error'))
<div class="fixed top-20 right-6 z-[80] max-w-sm p-space-md rounded-xl bg-error-container text-on-error-container shadow-lg flex items-start gap-space-sm" id="flash-error-toast" role="alert">
<span class="material-symbols-outlined text-body-lg shrink-0">error</span>
<span class="font-label-md text-label-md">{{ session('error') }}</span>
<button class="ml-2 text-on-error-container/70 hover:text-on-error-container" type="button" onclick="this.parentElement.remove()" aria-label="Fermer">
<span class="material-symbols-outlined text-body-md">close</span>
</button>
</div>
@endif
<!-- Subtle ambient glow background decoration -->
<div class="relative w-full overflow-hidden">
<div class="absolute -top-10 right-24 w-96 h-96 bg-primary-fixed-dim/20 rounded-full blur-3xl pointer-events-none -z-10"></div>
<div class="absolute top-48 left-1/3 w-80 h-80 bg-tertiary-fixed/15 rounded-full blur-3xl pointer-events-none -z-10"></div>
<!-- Page Breadcrumb & Header Bar -->
<div class="pt-space-md mb-space-xl flex flex-col md:flex-row md:items-end justify-between gap-space-lg">
<div class="flex flex-col min-w-0">
<!-- Monospaced/Uppercase overline metadata -->
<div class="flex items-center gap-space-xs mb-space-xs">
<span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Accueil</span>
<span class="material-symbols-outlined text-outline text-label-sm">chevron_right</span>
<span class="font-label-sm text-label-sm text-primary font-semibold uppercase tracking-wider">Collaborateurs</span>
<span class="ml-space-sm px-2 py-0.5 rounded-full bg-surface-container font-numeric-md text-label-sm text-on-surface-variant font-bold">{{ $activeProfiles }} Profils Actifs</span>
</div>
<div class="flex items-baseline gap-space-md">
<h1 class="font-headline-lg text-headline-lg text-on-surface tracking-tight">Gestion des Collaborateurs</h1>
<span class="hidden sm:inline font-label-md text-label-md text-on-surface-variant">Conforme Code du Travail &amp; CNSS Tunisie</span>
</div>
</div>
<!-- Action Buttons -->
<div class="flex items-center gap-space-sm shrink-0">
<a class="h-10 px-space-lg rounded-xl bg-surface-container-lowest hover:bg-surface-container-low text-on-surface font-label-lg text-label-lg shadow-sm flex items-center gap-space-xs transition-all" href="{{ $exportUrl }}">
<span class="material-symbols-outlined text-body-lg text-on-surface-variant">file_download</span>
<span>Exporter CSV</span>
</a>
</div>
</div>
<!-- Quick Stats Cards Strip -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-space-md mb-space-xl">
<div class="bg-surface-container-lowest rounded-xl p-space-lg shadow-sm flex items-center justify-between">
<div class="flex flex-col">
<span class="font-label-md text-label-md text-on-surface-variant">Effectif Total</span>
<span class="font-display-lg text-display-lg text-on-surface font-bold tracking-tight mt-1">{{ $totalEmployees }}</span>
<span class="font-label-sm text-label-sm text-tertiary flex items-center gap-0.5 mt-1">
<span class="material-symbols-outlined text-label-md">trending_up</span> +{{ $newThisQuarter }} ce trimestre
          </span>
</div>
<div class="w-12 h-12 rounded-xl bg-surface-container-high flex items-center justify-center text-primary">
<span class="material-symbols-outlined text-headline-md">group</span>
</div>
</div>
<div class="bg-surface-container-lowest rounded-xl p-space-lg shadow-sm flex items-center justify-between">
<div class="flex flex-col">
<span class="font-label-md text-label-md text-on-surface-variant">Période d'Essai</span>
<span class="font-display-lg text-display-lg text-on-surface font-bold tracking-tight mt-1">{{ str_pad((string) $probationCount, 2, '0', STR_PAD_LEFT) }}</span>
<span class="font-label-sm text-label-sm text-on-surface-variant mt-1">Évaluation sous 30 j</span>
</div>
<div class="w-12 h-12 rounded-xl bg-secondary-container/40 flex items-center justify-center text-secondary">
<span class="material-symbols-outlined text-headline-md">hourglass_top</span>
</div>
</div>
<div class="bg-surface-container-lowest rounded-xl p-space-lg shadow-sm flex items-center justify-between">
<div class="flex flex-col">
<span class="font-label-md text-label-md text-on-surface-variant">En Congé Légal</span>
<span class="font-display-lg text-display-lg text-on-surface font-bold tracking-tight mt-1">{{ str_pad((string) $onLeaveCount, 2, '0', STR_PAD_LEFT) }}</span>
<span class="font-label-sm text-label-sm text-primary flex items-center gap-0.5 mt-1">
<span class="material-symbols-outlined text-label-md">event_available</span> Retours prévus semaine
          </span>
</div>
<div class="w-12 h-12 rounded-xl bg-primary-fixed/30 flex items-center justify-center text-primary">
<span class="material-symbols-outlined text-headline-md">beach_access</span>
</div>
</div>
<div class="bg-surface-container-lowest rounded-xl p-space-lg shadow-sm flex items-center justify-between">
<div class="flex flex-col">
<span class="font-label-md text-label-md text-on-surface-variant">Conformité CNSS</span>
<span class="font-display-lg text-display-lg text-tertiary font-bold tracking-tight mt-1">{{ $cnssRate }}%</span>
<span class="font-label-sm text-label-sm text-tertiary mt-1">{{ $cnssComplete }} / {{ $totalEmployees }} dossiers complets</span>
</div>
<div class="w-12 h-12 rounded-xl bg-tertiary-fixed/30 flex items-center justify-center text-tertiary">
<span class="material-symbols-outlined text-headline-md">verified_user</span>
</div>
</div>
</div>
<!-- Filter & Search Toolbar Card -->
<form class="bg-surface-container-lowest rounded-xl p-space-md shadow-sm mb-space-lg flex flex-col lg:flex-row gap-space-md items-stretch lg:items-center justify-between" id="employee-filters" method="GET" action="{{ route('admin.employees') }}">
<!-- Search Input -->
<div class="relative flex-1 min-w-[280px]">
<span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-on-surface-variant text-body-lg pointer-events-none">search</span>
<input class="w-full h-10 pl-11 pr-4 rounded-xl bg-surface-container-low font-body-md text-body-md text-on-surface placeholder:text-on-surface-variant focus:outline-none focus:bg-surface-container transition-all" id="employee-search" name="q" placeholder="Rechercher par nom, email, CIN, poste..." type="text" value="{{ $filters['q'] }}"/>
</div>
<!-- Dropdown Filters Group -->
<div class="flex flex-wrap items-center gap-space-sm">
<!-- Department Filter -->
<div class="relative">
<select class="appearance-none h-10 pl-3 pr-8 rounded-xl bg-surface-container-low font-body-sm text-body-sm text-on-surface font-medium focus:outline-none focus:bg-surface-container cursor-pointer transition-all" name="department" onchange="this.form.requestSubmit()">
<option value="">Tous les départements</option>
@foreach (\App\Models\Employee::DEPARTMENTS as $value => $label)
<option value="{{ $value }}" @selected($filters['department'] === $value)>{{ $label }}</option>
@endforeach
</select>
<span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-on-surface-variant pointer-events-none text-body-md">expand_more</span>
</div>
<!-- Status Filter -->
<div class="relative">
<select class="appearance-none h-10 pl-3 pr-8 rounded-xl bg-surface-container-low font-body-sm text-body-sm text-on-surface font-medium focus:outline-none focus:bg-surface-container cursor-pointer transition-all" name="status" onchange="this.form.requestSubmit()">
<option value="">Tous statuts</option>
<option value="actif" @selected($filters['status'] === 'actif')>Actif (CDI / SIVP)</option>
<option value="essai" @selected($filters['status'] === 'essai')>Période d'essai</option>
<option value="conge" @selected($filters['status'] === 'conge')>En congé</option>
<option value="inactif" @selected($filters['status'] === 'inactif')>Inactif</option>
</select>
<span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-on-surface-variant pointer-events-none text-body-md">expand_more</span>
</div>
<!-- Seniority Filter -->
<div class="relative">
<select class="appearance-none h-10 pl-3 pr-8 rounded-xl bg-surface-container-low font-body-sm text-body-sm text-on-surface font-medium focus:outline-none focus:bg-surface-container cursor-pointer transition-all" name="seniority" onchange="this.form.requestSubmit()">
<option value="">Ancienneté</option>
<option value="senior" @selected($filters['seniority'] === 'senior')>&gt; 2 ans</option>
<option value="mid" @selected($filters['seniority'] === 'mid')>1 à 2 ans</option>
<option value="junior" @selected($filters['seniority'] === 'junior')>Junior (&lt; 1 an)</option>
</select>
<span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-on-surface-variant pointer-events-none text-body-md">expand_more</span>
</div>
@if (count($filterQuery) > 0)
<a class="h-10 px-space-sm rounded-xl text-on-surface-variant hover:text-on-surface hover:bg-surface-container font-label-sm text-label-sm flex items-center gap-1 transition-colors" href="{{ route('admin.employees') }}" title="Réinitialiser les filtres">
<span class="material-symbols-outlined text-body-md">filter_alt_off</span>
<span class="hidden sm:inline">Réinitialiser</span>
</a>
@endif
<div class="h-6 w-[1px] bg-surface-container-high mx-space-xs hidden sm:block"></div>
<!-- View Segment Toggles -->
<div class="flex items-center p-1 bg-surface-container-low rounded-xl">
<button aria-label="Table View" class="p-1.5 rounded-lg bg-surface-container-lowest text-primary shadow-xs" id="table-view-btn" type="button">
<span class="material-symbols-outlined text-body-lg">view_list</span>
</button>
<button aria-label="Grid Cards View" class="p-1.5 rounded-lg text-on-surface-variant hover:text-on-surface hover:bg-surface-container transition-colors" id="grid-view-btn" type="button">
<span class="material-symbols-outlined text-body-lg">grid_view</span>
</button>
</div>
</div>
</div>
</form>
<!-- Main Employees Table Surface -->
<div class="bg-surface-container-lowest rounded-xl shadow-sm overflow-hidden flex flex-col">
<!-- Bulk Selection Bar -->
<div class="hidden px-space-lg py-space-sm bg-primary-fixed/20 border-b border-surface-container flex flex-wrap items-center justify-between gap-space-sm" id="bulk-actions">
<div class="flex items-center gap-space-sm">
<span class="material-symbols-outlined text-primary text-body-lg">check_circle</span>
<span class="font-label-md text-label-md text-on-surface"><span class="font-numeric-md font-bold" id="bulk-count">0</span> collaborateur(s) sélectionné(s)</span>
</div>
<button class="h-8 px-space-md rounded-lg bg-error-container hover:bg-error text-on-error-container hover:text-on-error font-label-md text-label-md flex items-center gap-1 transition-colors" id="bulk-delete-btn" type="button">
<span class="material-symbols-outlined text-body-md">delete_sweep</span>
<span>Supprimer la sélection</span>
</button>
</div>
<form class="hidden" id="bulk-delete-form" method="POST" action="{{ route('admin.employees.bulkDestroy') }}">
@csrf
@method('DELETE')
</form>
<div class="overflow-x-auto w-full" id="employees-table-wrap">
<table class="w-full text-left border-collapse">
<thead>
<tr class="bg-surface-container-low h-11 text-on-surface-variant font-label-md text-label-md tracking-wider uppercase">
<th class="w-12 px-space-lg" scope="col">
<input aria-label="Sélectionner tous les collaborateurs de la page" class="w-4 h-4 rounded bg-surface-container-lowest text-primary focus:ring-0 cursor-pointer" id="select-all" type="checkbox"/>
</th>
<th class="px-space-md font-semibold" scope="col">Collaborateur</th>
<th class="px-space-md font-semibold" scope="col">Département</th>
<th class="px-space-md font-semibold" scope="col">Contact &amp; Mobile</th>
<th class="px-space-md font-semibold" scope="col">CIN Tunisienne</th>
<th class="px-space-md font-semibold" scope="col">Embauche &amp; Ancienneté</th>
<th class="px-space-md font-semibold" scope="col">Statut</th>
<th class="px-space-lg text-right font-semibold" scope="col">Actions</th>
</tr>
</thead>
<tbody class="divide-y divide-surface-container-low font-body-md text-body-md text-on-surface">
@forelse ($employees as $employee)
@php
    $statusMeta = $statusBadges[$employee->status] ?? $statusBadges['inactif'];
    $avatarClass = $avatarPalette[$employee->id % 4];
    $portalAccount = $portalAccounts[mb_strtolower(trim((string) $employee->email))] ?? null;
@endphp
<tr class="h-16 hover:bg-surface-container-low/70 transition-colors group" data-employee-row>
<td class="px-space-lg">
<input aria-label="Sélectionner {{ $employee->name }}" class="employee-select w-4 h-4 rounded bg-surface-container-lowest text-primary focus:ring-0 cursor-pointer" type="checkbox" value="{{ $employee->id }}"/>
</td>
<td class="px-space-md py-space-sm">
<div class="flex items-center gap-space-md min-w-0">
<div class="relative shrink-0">
<div class="w-10 h-10 rounded-full {{ $avatarClass }} font-headline-sm text-headline-sm flex items-center justify-center font-bold relative overflow-hidden">
@if ($employee->avatar_url)
<img alt="Portrait de {{ $employee->name }}" class="absolute inset-0 w-full h-full object-cover" loading="lazy" onerror="this.remove()" src="{{ $employee->avatar_url }}"/>
@endif
<span>{{ $employee->initials() }}</span>
</div>
<span class="absolute bottom-0 right-0 w-2.5 h-2.5 rounded-full {{ $statusMeta['dot'] }} ring-2 ring-surface-container-lowest"></span>
</div>
<div class="flex flex-col min-w-0">
<a class="font-label-lg text-label-lg font-semibold text-on-surface group-hover:text-primary transition-colors truncate" href="{{ $linkWith(['view' => $employee->id]) }}">{{ $employee->name }}</a>
<span class="font-body-sm text-body-sm text-on-surface-variant truncate">{{ $employee->position }}</span>
</div>
</div>
</td>
<td class="px-space-md py-space-sm whitespace-nowrap">
<span class="px-2.5 py-1 rounded-full {{ $departmentBadges[$employee->department] ?? 'bg-surface-container text-primary' }} font-label-sm text-label-sm font-semibold">
                  {{ $employee->departmentLabel() }}
                </span>
</td>
<td class="px-space-md py-space-sm">
<div class="flex flex-col text-left">
<span class="font-body-sm text-body-sm text-on-surface">{{ $employee->email }}</span>
<span class="font-numeric-md text-label-sm text-on-surface-variant font-mono tracking-tight">{{ $employee->mobile }}</span>
</div>
</td>
<td class="px-space-md py-space-sm whitespace-nowrap">
<div class="flex items-center gap-1.5 font-numeric-md text-numeric-md font-mono text-on-surface">
<span class="material-symbols-outlined text-outline text-label-md">badge</span>
<span>{{ $employee->cin }}</span>
</div>
</td>
<td class="px-space-md py-space-sm whitespace-nowrap">
<div class="flex flex-col">
<span class="font-body-sm text-body-sm text-on-surface">
@if ($employee->hire_date)
{{ $employee->hire_date->format('d') }} {{ $frenchMonths[(int) $employee->hire_date->format('n')] }} {{ $employee->hire_date->format('Y') }}
@else
Date non renseignée
@endif
</span>
<span class="font-label-sm text-label-sm text-on-surface-variant">{{ $employee->seniorityLabel() }}</span>
</div>
</td>
<td class="px-space-md py-space-sm whitespace-nowrap">
<div class="flex flex-col items-start gap-1">
<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full {{ $statusMeta['badge'] }} font-label-sm text-label-sm font-semibold">
<span class="w-1.5 h-1.5 rounded-full {{ $statusMeta['dot'] }}"></span>
                  {{ $employee->statusLabel() }}
                </span>
@if ($portalAccount)
<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full {{ $portalAccount->isActive() ? 'bg-tertiary-fixed/40 text-tertiary' : 'bg-error-container/50 text-error' }} font-label-sm text-label-sm font-semibold">
<span class="material-symbols-outlined text-[14px]">{{ $portalAccount->isActive() ? 'verified_user' : 'lock' }}</span>
                  Portail {{ $portalAccount->isActive() ? 'actif' : 'désactivé' }}
                </span>
@endif
</div>
</td>
<td class="px-space-lg py-space-sm whitespace-nowrap text-right">
<div class="flex items-center justify-end gap-1 text-on-surface-variant">
<a class="p-1.5 rounded-lg hover:bg-surface-container hover:text-primary transition-colors" href="{{ $linkWith(['view' => $employee->id]) }}" title="Voir profil">
<span class="material-symbols-outlined text-body-lg">visibility</span>
</a>
<a class="p-1.5 rounded-lg hover:bg-surface-container hover:text-on-surface transition-colors" href="{{ $linkWith(['edit' => $employee->id]) }}" title="Modifier">
<span class="material-symbols-outlined text-body-lg">edit</span>
</a>
@if ($portalAccount)
<form class="inline-flex" data-access-toggle data-action="{{ $portalAccount->isActive() ? 'disable' : 'enable' }}" data-account-email="{{ $portalAccount->email }}" data-employee="{{ $employee->name }}" method="POST" action="{{ route('admin.employees.access', $employee) }}">
@csrf
@method('PATCH')
<button class="p-1.5 rounded-lg hover:bg-surface-container transition-colors {{ $portalAccount->isActive() ? 'hover:text-error' : 'hover:text-tertiary' }}" title="{{ $portalAccount->isActive() ? 'Désactiver l\'accès portail' : 'Réactiver l\'accès portail' }}" type="submit">
<span class="material-symbols-outlined text-body-lg">{{ $portalAccount->isActive() ? 'lock' : 'how_to_reg' }}</span>
</button>
</form>
@endif
<button class="row-menu-btn p-1.5 rounded-lg hover:bg-surface-container hover:text-on-surface transition-colors" data-cin-url="{{ $employee->cin_file_path ? route('admin.employees.cin', $employee) : '' }}" data-delete-form="delete-employee-{{ $employee->id }}" data-edit-url="{{ $linkWith(['edit' => $employee->id]) }}" data-name="{{ $employee->name }}" data-view-url="{{ $linkWith(['view' => $employee->id]) }}" title="Plus d'actions" type="button">
<span class="material-symbols-outlined text-body-lg">more_vert</span>
</button>
</div>
<form class="hidden" id="delete-employee-{{ $employee->id }}" method="POST" action="{{ route('admin.employees.destroy', $employee) }}">
@csrf
@method('DELETE')
</form>
</td>
</tr>
@empty
<tr>
<td class="px-space-lg py-16" colspan="8">
<div class="flex flex-col items-center justify-center text-center gap-space-sm">
<div class="w-14 h-14 rounded-2xl bg-surface-container flex items-center justify-center text-on-surface-variant">
<span class="material-symbols-outlined text-headline-md">person_search</span>
</div>
<p class="font-headline-sm text-headline-sm text-on-surface">Aucun collaborateur trouvé</p>
<p class="font-body-sm text-body-sm text-on-surface-variant">Ajustez vos filtres ou ajoutez un nouveau profil au registre.</p>
<a class="mt-2 h-9 px-space-lg rounded-xl bg-primary-fill hover:bg-primary-container text-on-primary font-label-md text-label-md flex items-center gap-1 transition-colors" href="{{ route('admin.employees') }}">
<span class="material-symbols-outlined text-body-md">restart_alt</span>
<span>Réinitialiser les filtres</span>
</a>
</div>
</td>
</tr>
@endforelse
</tbody>
</table>
</div>
<!-- Grid Cards View -->
<div class="hidden p-space-md" id="employees-grid">
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-space-md">
@forelse ($employees as $employee)
@php
    $statusMeta = $statusBadges[$employee->status] ?? $statusBadges['inactif'];
    $avatarClass = $avatarPalette[$employee->id % 4];
    $portalAccount = $portalAccounts[mb_strtolower(trim((string) $employee->email))] ?? null;
@endphp
<div class="rounded-xl border border-surface-container-high bg-surface-container-low/30 p-space-md flex flex-col gap-space-md hover:shadow-md transition-shadow">
<div class="flex items-start justify-between gap-space-sm">
<div class="relative shrink-0">
<div class="w-12 h-12 rounded-full {{ $avatarClass }} font-headline-md text-headline-md flex items-center justify-center font-bold relative overflow-hidden">
@if ($employee->avatar_url)
<img alt="Portrait de {{ $employee->name }}" class="absolute inset-0 w-full h-full object-cover" loading="lazy" onerror="this.remove()" src="{{ $employee->avatar_url }}"/>
@endif
<span>{{ $employee->initials() }}</span>
</div>
<span class="absolute bottom-0 right-0 w-2.5 h-2.5 rounded-full {{ $statusMeta['dot'] }} ring-2 ring-surface-container-lowest"></span>
</div>
<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full {{ $statusMeta['badge'] }} font-label-sm text-label-sm font-semibold">
<span class="w-1.5 h-1.5 rounded-full {{ $statusMeta['dot'] }}"></span>
{{ $employee->statusLabel() }}
</span>
</div>
<div class="flex flex-col min-w-0">
<span class="font-label-lg text-label-lg font-semibold text-on-surface truncate">{{ $employee->name }}</span>
<span class="font-body-sm text-body-sm text-on-surface-variant truncate">{{ $employee->position }}</span>
</div>
<div class="flex flex-wrap items-center gap-1.5">
<span class="px-2.5 py-1 rounded-full {{ $departmentBadges[$employee->department] ?? 'bg-surface-container text-primary' }} font-label-sm text-label-sm font-semibold">{{ $employee->departmentLabel() }}</span>
<span class="px-2.5 py-1 rounded-full bg-surface-container font-label-sm text-label-sm text-on-surface-variant font-semibold">{{ $employee->contract_type }}</span>
</div>
<div class="flex flex-col gap-1 font-body-sm text-body-sm text-on-surface-variant">
<span class="flex items-center gap-1.5 truncate"><span class="material-symbols-outlined text-body-md">mail</span>{{ $employee->email }}</span>
<span class="flex items-center gap-1.5"><span class="material-symbols-outlined text-body-md">call</span>{{ $employee->mobile }}</span>
<span class="flex items-center gap-1.5"><span class="material-symbols-outlined text-body-md">badge</span>{{ $employee->cin }}</span>
</div>
<div class="flex items-center justify-between border-t border-surface-container-high pt-space-sm mt-auto">
<span class="font-label-sm text-label-sm text-on-surface-variant">{{ $employee->seniorityLabel() }}</span>
<div class="flex items-center gap-1 text-on-surface-variant">
<a class="p-1.5 rounded-lg hover:bg-surface-container hover:text-primary transition-colors" href="{{ $linkWith(['view' => $employee->id]) }}" title="Voir profil">
<span class="material-symbols-outlined text-body-lg">visibility</span>
</a>
<a class="p-1.5 rounded-lg hover:bg-surface-container hover:text-on-surface transition-colors" href="{{ $linkWith(['edit' => $employee->id]) }}" title="Modifier">
<span class="material-symbols-outlined text-body-lg">edit</span>
</a>
@if ($portalAccount)
<form class="inline-flex" data-access-toggle data-action="{{ $portalAccount->isActive() ? 'disable' : 'enable' }}" data-account-email="{{ $portalAccount->email }}" data-employee="{{ $employee->name }}" method="POST" action="{{ route('admin.employees.access', $employee) }}">
@csrf
@method('PATCH')
<button class="p-1.5 rounded-lg hover:bg-surface-container transition-colors {{ $portalAccount->isActive() ? 'hover:text-error' : 'hover:text-tertiary' }}" title="{{ $portalAccount->isActive() ? 'Désactiver l\'accès portail' : 'Réactiver l\'accès portail' }}" type="submit">
<span class="material-symbols-outlined text-body-lg">{{ $portalAccount->isActive() ? 'lock' : 'how_to_reg' }}</span>
</button>
</form>
@endif
</div>
</div>
</div>
@empty
<div class="col-span-full py-16 flex flex-col items-center justify-center text-center gap-space-sm">
<div class="w-14 h-14 rounded-2xl bg-surface-container flex items-center justify-center text-on-surface-variant">
<span class="material-symbols-outlined text-headline-md">person_search</span>
</div>
<p class="font-headline-sm text-headline-sm text-on-surface">Aucun collaborateur trouvé</p>
<p class="font-body-sm text-body-sm text-on-surface-variant">Ajustez vos filtres ou ajoutez un nouveau profil au registre.</p>
</div>
@endforelse
</div>
</div>
<!-- Pagination Footer -->
<div class="px-space-lg py-space-md bg-surface-container-low/40 flex flex-col sm:flex-row items-center justify-between gap-space-md">
<div class="flex items-center gap-space-xs font-label-md text-label-md text-on-surface-variant">
<span>Affichage</span>
<span class="font-numeric-md text-on-surface font-semibold">{{ $employees->firstItem() ?? 0 }}</span>
<span>à</span>
<span class="font-numeric-md text-on-surface font-semibold">{{ $employees->lastItem() ?? 0 }}</span>
<span>sur</span>
<span class="font-numeric-md text-on-surface font-semibold">{{ $employees->total() }}</span>
<span>collaborateurs</span>
</div>
<div class="flex items-center gap-space-xs">
@if ($employees->onFirstPage())
<button class="h-9 px-space-md rounded-xl bg-surface-container-lowest text-on-surface-variant font-label-md text-label-md shadow-xs disabled:opacity-40 flex items-center gap-1 transition-colors" disabled="" type="button">
<span class="material-symbols-outlined text-body-md">chevron_left</span>
<span>Précédent</span>
</button>
@else
<a class="h-9 px-space-md rounded-xl bg-surface-container-lowest hover:bg-surface-container text-on-surface-variant font-label-md text-label-md shadow-xs flex items-center gap-1 transition-colors" href="{{ $employees->previousPageUrl() }}">
<span class="material-symbols-outlined text-body-md">chevron_left</span>
<span>Précédent</span>
</a>
@endif
@php
    $currentPage = $employees->currentPage();
    $lastPage = max(1, $employees->lastPage());
    $startPage = max(1, min($currentPage - 1, $lastPage - 3));
    $endPage = min($lastPage, $startPage + 3);
@endphp
<div class="flex items-center gap-1 mx-1">
@if ($startPage > 1)
<a class="w-8 h-8 rounded-xl hover:bg-surface-container text-on-surface font-label-md text-label-md flex items-center justify-center" href="{{ $employees->url(1) }}">1</a>
@if ($startPage > 2)
<span class="text-on-surface-variant px-1 font-label-md text-label-md">...</span>
@endif
@endif
@for ($page = $startPage; $page <= $endPage; $page++)
@if ($page === $currentPage)
<span class="w-8 h-8 rounded-xl bg-primary-fill text-on-primary font-label-md text-label-md font-semibold flex items-center justify-center">{{ $page }}</span>
@else
<a class="w-8 h-8 rounded-xl hover:bg-surface-container text-on-surface font-label-md text-label-md flex items-center justify-center" href="{{ $employees->url($page) }}">{{ $page }}</a>
@endif
@endfor
@if ($endPage < $lastPage)
@if ($endPage < $lastPage - 1)
<span class="text-on-surface-variant px-1 font-label-md text-label-md">...</span>
@endif
<a class="w-8 h-8 rounded-xl hover:bg-surface-container text-on-surface font-label-md text-label-md flex items-center justify-center" href="{{ $employees->url($lastPage) }}">{{ $lastPage }}</a>
@endif
</div>
@if ($employees->hasMorePages())
<a class="h-9 px-space-md rounded-xl bg-surface-container-lowest hover:bg-surface-container text-on-surface font-label-md text-label-md shadow-xs flex items-center gap-1 transition-colors" href="{{ $employees->nextPageUrl() }}">
<span>Suivant</span>
<span class="material-symbols-outlined text-body-md">chevron_right</span>
</a>
@else
<button class="h-9 px-space-md rounded-xl bg-surface-container-lowest text-on-surface font-label-md text-label-md shadow-xs flex items-center gap-1 transition-colors disabled:opacity-40" disabled="" type="button">
<span>Suivant</span>
<span class="material-symbols-outlined text-body-md">chevron_right</span>
</button>
@endif
</div>
</div>
</div>
</div>
<!-- Shared Row Action Menu -->
<div class="hidden fixed z-[70] w-56 bg-surface-container-lowest rounded-xl shadow-[0_12px_32px_rgba(15,23,42,0.18)] py-space-xs" id="row-action-menu">
<div class="px-space-md py-space-xs border-b border-surface-container">
<p class="font-label-sm text-label-sm text-on-surface-variant">Actions rapides</p>
</div>
<a class="flex items-center gap-space-sm px-space-md py-2 font-body-sm text-body-sm text-on-surface-variant hover:bg-surface-container hover:text-on-surface transition-colors" href="#" id="row-menu-view">
<span class="material-symbols-outlined text-body-md">visibility</span>
<span>Voir le profil</span>
</a>
<a class="flex items-center gap-space-sm px-space-md py-2 font-body-sm text-body-sm text-on-surface-variant hover:bg-surface-container hover:text-on-surface transition-colors" href="#" id="row-menu-edit">
<span class="material-symbols-outlined text-body-md">edit</span>
<span>Modifier le profil</span>
</a>
<a class="hidden flex items-center gap-space-sm px-space-md py-2 font-body-sm text-body-sm text-on-surface-variant hover:bg-surface-container hover:text-on-surface transition-colors" href="#" id="row-menu-cin">
<span class="material-symbols-outlined text-body-md">description</span>
<span>Télécharger la CIN</span>
</a>
<div class="my-space-xs border-t border-surface-container"></div>
<button class="w-full flex items-center gap-space-sm px-space-md py-2 font-body-sm text-body-sm text-error hover:bg-error-container hover:text-on-error-container transition-colors text-left" id="row-menu-delete" type="button">
<span class="material-symbols-outlined text-body-md">delete</span>
<span>Supprimer</span>
</button>
</div>
<!-- Slide-Over Multi-Step Modal (Add / Edit Employee) -->
<div class="fixed inset-0 z-50 overflow-hidden flex justify-end transition-opacity duration-300 {{ $openDrawer ? '' : 'hidden' }}" data-close-url="{{ $linkWith([]) }}" data-initial-step="{{ $initialStep }}" data-is-editing="{{ $isEditing ? '1' : '0' }}" id="add-employee-modal">
<!-- Overlay backdrop -->
<div class="fixed inset-0 bg-black/60 backdrop-blur-xs transition-opacity" id="modal-backdrop"></div>
<!-- Slide-over Drawer Panel -->
<div class="relative w-full max-w-2xl bg-surface-container-lowest shadow-2xl flex flex-col h-full z-10 overflow-hidden">
<!-- Drawer Header -->
<div class="p-space-xl bg-surface-container-low/60 flex items-start justify-between">
<div class="flex items-center gap-space-md">
<div class="w-12 h-12 rounded-xl bg-primary-container text-on-primary flex items-center justify-center shadow-sm">
<span class="material-symbols-outlined text-headline-lg">{{ $isEditing ? 'edit' : 'fitbit_push_ups' }}</span>
</div>
<div class="flex flex-col">
<h2 class="font-headline-md text-headline-md text-on-surface tracking-tight">{{ $isEditing ? 'Modifier le Collaborateur' : 'Nouveau Collaborateur' }}</h2>
<p class="font-body-sm text-body-sm text-on-surface-variant">{{ $isEditing ? 'Mise à jour du dossier de '.$editEmployee->name : "Ajout d'un employé au registre réglementaire tunisien" }}</p>
</div>
</div>
<button aria-label="Fermer" class="w-9 h-9 rounded-xl hover:bg-surface-container text-on-surface-variant hover:text-on-surface flex items-center justify-center transition-colors" id="close-modal-btn" type="button">
<span class="material-symbols-outlined text-headline-sm">close</span>
</button>
</div>
<!-- Multi-step Stepper Ribbon -->
<div class="px-space-xl py-space-sm bg-surface-container-lowest overflow-x-auto">
<div class="flex items-center min-w-max gap-space-sm">
<button class="js-step-trigger flex items-center gap-2 px-3 py-2 rounded-xl {{ $initialStep === 1 ? $stepActiveClass : $stepIdleClass }}" data-step-target="1" type="button">
<span class="js-step-number w-5 h-5 rounded-full {{ $initialStep === 1 ? $stepNumberActiveClass : $stepNumberIdleClass }} font-label-sm text-label-sm flex items-center justify-center font-bold">1</span>
<span class="js-step-label font-label-md text-label-md {{ $initialStep === 1 ? 'font-semibold' : 'font-medium' }}">Infos Personnelles</span>
</button>
<span class="material-symbols-outlined text-outline-variant text-label-md">arrow_forward_ios</span>
<button class="js-step-trigger flex items-center gap-2 px-3 py-2 rounded-xl {{ $initialStep === 2 ? $stepActiveClass : $stepIdleClass }}" data-step-target="2" type="button">
<span class="js-step-number w-5 h-5 rounded-full {{ $initialStep === 2 ? $stepNumberActiveClass : $stepNumberIdleClass }} font-label-sm text-label-sm flex items-center justify-center font-bold">2</span>
<span class="js-step-label font-label-md text-label-md {{ $initialStep === 2 ? 'font-semibold' : 'font-medium' }}">Contrat &amp; Poste</span>
</button>
<span class="material-symbols-outlined text-outline-variant text-label-md">arrow_forward_ios</span>
<button class="js-step-trigger flex items-center gap-2 px-3 py-2 rounded-xl {{ $initialStep === 3 ? $stepActiveClass : $stepIdleClass }}" data-step-target="3" type="button">
<span class="js-step-number w-5 h-5 rounded-full {{ $initialStep === 3 ? $stepNumberActiveClass : $stepNumberIdleClass }} font-label-sm text-label-sm flex items-center justify-center font-bold">3</span>
<span class="js-step-label font-label-md text-label-md {{ $initialStep === 3 ? 'font-semibold' : 'font-medium' }}">Paie &amp; CNSS</span>
</button>
<span class="material-symbols-outlined text-outline-variant text-label-md">arrow_forward_ios</span>
<button class="js-step-trigger flex items-center gap-2 px-3 py-2 rounded-xl {{ $initialStep === 4 ? $stepActiveClass : $stepIdleClass }}" data-step-target="4" type="button">
<span class="js-step-number w-5 h-5 rounded-full {{ $initialStep === 4 ? $stepNumberActiveClass : $stepNumberIdleClass }} font-label-sm text-label-sm flex items-center justify-center font-bold">4</span>
<span class="js-step-label font-label-md text-label-md {{ $initialStep === 4 ? 'font-semibold' : 'font-medium' }}">Congés</span>
</button>
</div>
</div>
<!-- Drawer Form Content -->
<div class="flex-1 overflow-y-auto p-space-xl space-y-space-lg">
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
<form class="space-y-space-md" enctype="multipart/form-data" id="new-employee-form" method="POST" action="{{ $isEditing ? route('admin.employees.update', $editEmployee) : route('admin.employees.store') }}" novalidate>
@csrf
@if ($isEditing)
@method('PUT')
@endif
@if ($onboardValues)
<input type="hidden" name="candidate_id" value="{{ old('candidate_id', $onboardValues['candidate_id']) }}"/>
<div class="p-space-md rounded-xl bg-tertiary-fixed/15 ring-1 ring-tertiary/20 flex items-start gap-space-sm">
<span class="material-symbols-outlined text-tertiary text-body-lg shrink-0 mt-0.5">how_to_reg</span>
<div class="flex flex-col text-on-surface">
<span class="font-label-md text-label-md font-semibold">Onboarding depuis le recrutement</span>
<span class="font-body-sm text-body-sm text-on-surface-variant">Candidat : <strong>{{ $onboardValues['candidate_name'] }}</strong>. Les champs sont pré-remplis — complétez la CIN et la date de naissance, puis enregistrez la fiche collaborateur.</span>
</div>
</div>
@endif
<!-- Step 1: Personal Information -->
<div class="space-y-space-md js-step-panel {{ $initialStep === 1 ? '' : 'hidden' }}" data-step-panel="1">
<!-- Info Banner: Legal Compliance -->
<div class="p-space-md rounded-xl bg-surface-container-low flex items-start gap-space-sm">
<span class="material-symbols-outlined text-primary text-body-lg shrink-0 mt-0.5">policy</span>
<div class="flex flex-col text-on-surface">
<span class="font-label-md text-label-md font-semibold">Vérification d'identité tunisienne</span>
<span class="font-body-sm text-body-sm text-on-surface-variant">Le numéro de CIN doit comporter exactement 8 chiffres conformes au registre national d'état civil.</span>
</div>
</div>
<!-- Row: Prénom & Nom -->
<div class="grid grid-cols-1 sm:grid-cols-2 gap-space-md">
<div class="flex flex-col gap-space-xs">
<label class="font-label-md text-label-md text-on-surface font-medium" for="emp-firstname">Prénom <span class="text-error">*</span></label>
<input class="h-10 px-3 rounded-xl bg-surface-container-lowest text-on-surface font-body-md text-body-md focus:outline-none ring-1 ring-outline-variant focus:ring-2 focus:ring-primary transition-all" id="emp-firstname" name="firstname" placeholder="Ex: Amira" required="" type="text" value="{{ old('firstname', $defaultFirstname) }}"/>
@error('firstname')<span class="font-label-sm text-label-sm text-error">{{ $message }}</span>@enderror
</div>
<div class="flex flex-col gap-space-xs">
<label class="font-label-md text-label-md text-on-surface font-medium" for="emp-lastname">Nom de famille <span class="text-error">*</span></label>
<input class="h-10 px-3 rounded-xl bg-surface-container-lowest text-on-surface font-body-md text-body-md focus:outline-none ring-1 ring-outline-variant focus:ring-2 focus:ring-primary transition-all" id="emp-lastname" name="lastname" placeholder="Ex: Bouazizi" required="" type="text" value="{{ old('lastname', $defaultLastname) }}"/>
@error('lastname')<span class="font-label-sm text-label-sm text-error">{{ $message }}</span>@enderror
</div>
</div>
<!-- Row: CIN & Date de Naissance -->
<div class="grid grid-cols-1 sm:grid-cols-2 gap-space-md">
<div class="flex flex-col gap-space-xs">
<label class="font-label-md text-label-md text-on-surface font-medium" for="emp-cin">Numéro de CIN (8 chiffres) <span class="text-error">*</span></label>
<div class="relative">
<span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-body-md">badge</span>
<input class="w-full h-10 pl-10 pr-3 rounded-xl bg-surface-container-lowest text-on-surface font-numeric-md text-numeric-md font-mono focus:outline-none ring-1 ring-outline-variant focus:ring-2 focus:ring-primary transition-all" id="emp-cin" maxlength="8" name="cin" pattern="[0-9]{8}" placeholder="09845211" required="" type="text" value="{{ old('cin', $editEmployee?->cin) }}"/>
</div>
@error('cin')<span class="font-label-sm text-label-sm text-error">{{ $message }}</span>@enderror
</div>
<div class="flex flex-col gap-space-xs">
<label class="font-label-md text-label-md text-on-surface font-medium" for="emp-dob">Date de naissance <span class="text-error">*</span></label>
<input class="h-10 px-3 rounded-xl bg-surface-container-lowest text-on-surface font-body-md text-body-md focus:outline-none ring-1 ring-outline-variant focus:ring-2 focus:ring-primary transition-all" id="emp-dob" name="date_of_birth" required="" type="date" value="{{ old('date_of_birth', $editEmployee?->date_of_birth?->format('Y-m-d')) }}"/>
@error('date_of_birth')<span class="font-label-sm text-label-sm text-error">{{ $message }}</span>@enderror
</div>
</div>
<!-- Row: Email & Téléphone -->
<div class="grid grid-cols-1 sm:grid-cols-2 gap-space-md">
<div class="flex flex-col gap-space-xs">
<label class="font-label-md text-label-md text-on-surface font-medium" for="emp-email">Email Personnel / Pro <span class="text-error">*</span></label>
<div class="relative">
<span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-body-md">mail</span>
<input class="w-full h-10 pl-10 pr-3 rounded-xl bg-surface-container-lowest text-on-surface font-body-md text-body-md focus:outline-none ring-1 ring-outline-variant focus:ring-2 focus:ring-primary transition-all" id="emp-email" name="email" placeholder="a.bouazizi@carthagetech.tn" required="" type="email" value="{{ $onboardValue('email', $editEmployee?->email) }}"/>
</div>
@error('email')<span class="font-label-sm text-label-sm text-error">{{ $message }}</span>@enderror
</div>
<div class="flex flex-col gap-space-xs">
<label class="font-label-md text-label-md text-on-surface font-medium" for="emp-phone">Numéro Mobile (Tunisie) <span class="text-error">*</span></label>
<div class="relative">
<span class="absolute left-3 top-1/2 -translate-y-1/2 font-numeric-md text-label-md font-mono text-on-surface-variant font-bold">+216</span>
<input class="w-full h-10 pl-14 pr-3 rounded-xl bg-surface-container-lowest text-on-surface font-numeric-md text-numeric-md font-mono focus:outline-none ring-1 ring-outline-variant focus:ring-2 focus:ring-primary transition-all" id="emp-phone" name="mobile" placeholder="98 123 456" required="" type="tel" value="{{ $defaultMobile }}"/>
</div>
@error('mobile')<span class="font-label-sm text-label-sm text-error">{{ $message }}</span>@enderror
</div>
</div>
<!-- Row: Situation Familiale & Enfants -->
<div class="grid grid-cols-1 sm:grid-cols-2 gap-space-md">
<div class="flex flex-col gap-space-xs">
<label class="font-label-md text-label-md text-on-surface font-medium" for="emp-family">Situation Familiale (Chef de famille)</label>
<div class="relative">
<select class="w-full appearance-none h-10 pl-3 pr-8 rounded-xl bg-surface-container-lowest text-on-surface font-body-md text-body-md focus:outline-none ring-1 ring-outline-variant focus:ring-2 focus:ring-primary transition-all" id="emp-family" name="family_status">
<option value="celibataire" @selected(old('family_status', $editEmployee?->family_status) === 'celibataire')>Célibataire</option>
<option value="marie" @selected(old('family_status', $editEmployee?->family_status) === 'marie')>Marié(e)</option>
<option value="divorce" @selected(old('family_status', $editEmployee?->family_status) === 'divorce')>Divorcé(e)</option>
<option value="veuf" @selected(old('family_status', $editEmployee?->family_status) === 'veuf')>Veuf / Veuve</option>
</select>
<span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-on-surface-variant pointer-events-none text-body-md">expand_more</span>
</div>
@error('family_status')<span class="font-label-sm text-label-sm text-error">{{ $message }}</span>@enderror
</div>
<div class="flex flex-col gap-space-xs">
<label class="font-label-md text-label-md text-on-surface font-medium" for="emp-children">Nombre d'enfants à charge (Déduction IRPP)</label>
<input class="h-10 px-3 rounded-xl bg-surface-container-lowest text-on-surface font-numeric-md text-numeric-md placeholder:text-on-surface-variant focus:outline-none ring-1 ring-outline-variant focus:ring-2 focus:ring-primary transition-all" id="emp-children" max="10" min="0" name="children" placeholder="Ex: 2" type="number" value="{{ old('children', $editEmployee?->children ?? 0) }}"/>
@error('children')<span class="font-label-sm text-label-sm text-error">{{ $message }}</span>@enderror
</div>
</div>
<!-- Address -->
<div class="flex flex-col gap-space-xs">
<label class="font-label-md text-label-md text-on-surface font-medium" for="emp-address">Adresse Résidentielle Principale</label>
<div class="relative">
<span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-body-md">home_pin</span>
<input class="w-full h-10 pl-10 pr-3 rounded-xl bg-surface-container-lowest text-on-surface font-body-md text-body-md focus:outline-none ring-1 ring-outline-variant focus:ring-2 focus:ring-primary transition-all" id="emp-address" name="address" placeholder="Rue, Code Postal, Gouvernorat, Tunisie" type="text" value="{{ old('address', $editEmployee?->address) }}"/>
</div>
@error('address')<span class="font-label-sm text-label-sm text-error">{{ $message }}</span>@enderror
</div>
<!-- File Upload: CIN Copy -->
<div class="flex flex-col gap-space-xs pt-space-xs">
<label class="font-label-md text-label-md text-on-surface font-medium" for="emp-cin-file">Copie Recto/Verso CIN (PDF ou Scan)</label>
<div class="rounded-xl p-space-md bg-surface-container-low flex items-center justify-between">
<div class="flex items-center gap-space-sm min-w-0">
<span class="material-symbols-outlined text-primary text-headline-sm">description</span>
<div class="flex flex-col min-w-0">
<span class="font-label-md text-label-md text-on-surface font-semibold truncate" id="cin-file-name">{{ $existingCinFile ? basename($existingCinFile) : 'Aucun document téléversé' }}</span>
<span class="font-label-sm text-label-sm text-on-surface-variant" id="cin-file-meta">{{ $existingCinFile ? 'Document existant • Validé' : 'PDF, JPG ou PNG • 5 Mo max' }}</span>
</div>
</div>
<div class="flex items-center gap-space-xs">
@if ($existingCinFile)
<a class="text-primary hover:text-primary-container transition-colors p-1" href="{{ route('admin.employees.cin', $editEmployee) }}" id="cin-existing-link" title="Télécharger le document existant">
<span class="material-symbols-outlined text-body-md">download</span>
</a>
@endif
<button class="text-on-surface-variant hover:text-primary transition-colors p-1" id="cin-upload-btn" title="Téléverser un document" type="button">
<span class="material-symbols-outlined text-body-md">upload_file</span>
</button>
<button class="text-on-surface-variant hover:text-error transition-colors p-1 {{ $existingCinFile ? '' : 'hidden' }}" id="cin-file-clear" title="Retirer le fichier" type="button">
<span class="material-symbols-outlined text-body-md">delete</span>
</button>
</div>
</div>
<input accept=".pdf,.jpg,.jpeg,.png" class="hidden" id="emp-cin-file" name="cin_file" type="file"/>
<input id="remove-cin-file" name="remove_cin_file" type="hidden" value="0"/>
@error('cin_file')<span class="font-label-sm text-label-sm text-error">{{ $message }}</span>@enderror
</div>
</div>
<!-- Step 2: Contract & Position -->
<div class="space-y-space-md js-step-panel {{ $initialStep === 2 ? '' : 'hidden' }}" data-step-panel="2">
<div class="p-space-md rounded-xl bg-surface-container-low flex items-start gap-space-sm">
<span class="material-symbols-outlined text-primary text-body-lg shrink-0 mt-0.5">work</span>
<div class="flex flex-col text-on-surface">
<span class="font-label-md text-label-md font-semibold">Cadre contractuel</span>
<span class="font-body-sm text-body-sm text-on-surface-variant">Le contrat et la date d'embauche déterminent l'ancienneté et les droits légaux du collaborateur.</span>
</div>
</div>
<div class="flex flex-col gap-space-xs">
<label class="font-label-md text-label-md text-on-surface font-medium" for="emp-position">Intitulé du poste <span class="text-error">*</span></label>
<input class="h-10 px-3 rounded-xl bg-surface-container-lowest text-on-surface font-body-md text-body-md focus:outline-none ring-1 ring-outline-variant focus:ring-2 focus:ring-primary transition-all" id="emp-position" name="position" placeholder="Ex: Ingénieur Logiciel" required="" type="text" value="{{ $onboardValue('position', $editEmployee?->position) }}"/>
@error('position')<span class="font-label-sm text-label-sm text-error">{{ $message }}</span>@enderror
</div>
<div class="grid grid-cols-1 sm:grid-cols-2 gap-space-md">
<div class="flex flex-col gap-space-xs">
<label class="font-label-md text-label-md text-on-surface font-medium" for="emp-department">Département <span class="text-error">*</span></label>
<div class="relative">
<select class="w-full appearance-none h-10 pl-3 pr-8 rounded-xl bg-surface-container-lowest text-on-surface font-body-md text-body-md focus:outline-none ring-1 ring-outline-variant focus:ring-2 focus:ring-primary transition-all" id="emp-department" name="department" required="">
@foreach (\App\Models\Employee::DEPARTMENTS as $value => $label)
<option value="{{ $value }}" @selected($onboardValue('department', $editEmployee?->department ?? 'it') === $value)>{{ $label }}</option>
@endforeach
</select>
<span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-on-surface-variant pointer-events-none text-body-md">expand_more</span>
</div>
@error('department')<span class="font-label-sm text-label-sm text-error">{{ $message }}</span>@enderror
</div>
<div class="flex flex-col gap-space-xs">
<label class="font-label-md text-label-md text-on-surface font-medium" for="emp-contract">Type de contrat <span class="text-error">*</span></label>
<div class="relative">
<select class="w-full appearance-none h-10 pl-3 pr-8 rounded-xl bg-surface-container-lowest text-on-surface font-body-md text-body-md focus:outline-none ring-1 ring-outline-variant focus:ring-2 focus:ring-primary transition-all" id="emp-contract" name="contract_type" required="">
@foreach (\App\Models\Employee::CONTRACT_TYPES as $value => $label)
<option value="{{ $value }}" @selected($onboardValue('contract_type', $editEmployee?->contract_type ?? 'CDI') === $value)>{{ $label }}</option>
@endforeach
</select>
<span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-on-surface-variant pointer-events-none text-body-md">expand_more</span>
</div>
@error('contract_type')<span class="font-label-sm text-label-sm text-error">{{ $message }}</span>@enderror
</div>
</div>
<div class="grid grid-cols-1 sm:grid-cols-2 gap-space-md">
<div class="flex flex-col gap-space-xs">
<label class="font-label-md text-label-md text-on-surface font-medium" for="emp-hire-date">Date d'embauche <span class="text-error">*</span></label>
<input class="h-10 px-3 rounded-xl bg-surface-container-lowest text-on-surface font-body-md text-body-md focus:outline-none ring-1 ring-outline-variant focus:ring-2 focus:ring-primary transition-all" id="emp-hire-date" name="hire_date" required="" type="date" value="{{ $onboardValue('hire_date', $editEmployee?->hire_date?->format('Y-m-d')) }}"/>
@error('hire_date')<span class="font-label-sm text-label-sm text-error">{{ $message }}</span>@enderror
</div>
<div class="flex flex-col gap-space-xs">
<label class="font-label-md text-label-md text-on-surface font-medium" for="emp-status">Statut <span class="text-error">*</span></label>
<div class="relative">
<select class="w-full appearance-none h-10 pl-3 pr-8 rounded-xl bg-surface-container-lowest text-on-surface font-body-md text-body-md focus:outline-none ring-1 ring-outline-variant focus:ring-2 focus:ring-primary transition-all" id="emp-status" name="status" required="">
@foreach (\App\Models\Employee::STATUSES as $value => $label)
<option value="{{ $value }}" @selected($onboardValue('status', $editEmployee?->status ?? 'actif') === $value)>{{ $label }}</option>
@endforeach
</select>
<span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-on-surface-variant pointer-events-none text-body-md">expand_more</span>
</div>
@error('status')<span class="font-label-sm text-label-sm text-error">{{ $message }}</span>@enderror
</div>
</div>
<div class="flex flex-col gap-space-xs">
<label class="font-label-md text-label-md text-on-surface font-medium" for="emp-work-phone">Numéro Mobile Professionnel</label>
<div class="relative">
<span class="absolute left-3 top-1/2 -translate-y-1/2 font-numeric-md text-label-md font-mono text-on-surface-variant font-bold">+216</span>
<input class="w-full h-10 pl-14 pr-3 rounded-xl bg-surface-container-lowest text-on-surface font-numeric-md text-numeric-md font-mono focus:outline-none ring-1 ring-outline-variant focus:ring-2 focus:ring-primary transition-all" id="emp-work-phone" name="work_mobile" placeholder="71 820 400" type="tel" value="{{ $defaultWorkMobile }}"/>
</div>
@error('work_mobile')<span class="font-label-sm text-label-sm text-error">{{ $message }}</span>@enderror
</div>
</div>
<!-- Step 3: Payroll & CNSS -->
<div class="space-y-space-md js-step-panel {{ $initialStep === 3 ? '' : 'hidden' }}" data-step-panel="3">
<div class="p-space-md rounded-xl bg-surface-container-low flex items-start gap-space-sm">
<span class="material-symbols-outlined text-primary text-body-lg shrink-0 mt-0.5">account_balance</span>
<div class="flex flex-col text-on-surface">
<span class="font-label-md text-label-md font-semibold">Déclarations sociales &amp; paie</span>
<span class="font-body-sm text-body-sm text-on-surface-variant">Renseignez le numéro CNSS et le compte bancaire utilisés pour les virements de salaire.</span>
</div>
</div>
<div class="grid grid-cols-1 sm:grid-cols-2 gap-space-md">
<div class="flex flex-col gap-space-xs">
<label class="font-label-md text-label-md text-on-surface font-medium" for="emp-cnss">Numéro d'affiliation CNSS</label>
<input class="h-10 px-3 rounded-xl bg-surface-container-lowest text-on-surface font-numeric-md text-numeric-md font-mono focus:outline-none ring-1 ring-outline-variant focus:ring-2 focus:ring-primary transition-all" id="emp-cnss" name="cnss_number" placeholder="Ex: 09482110-12" type="text" value="{{ $defaultCnss }}"/>
@error('cnss_number')<span class="font-label-sm text-label-sm text-error">{{ $message }}</span>@enderror
</div>
<div class="flex flex-col gap-space-xs">
<label class="font-label-md text-label-md text-on-surface font-medium" for="emp-bank">Compte Bancaire (RIB)</label>
<div class="relative">
<span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-body-md">credit_card</span>
<input class="w-full h-10 pl-10 pr-3 rounded-xl bg-surface-container-lowest text-on-surface font-numeric-md text-numeric-md font-mono focus:outline-none ring-1 ring-outline-variant focus:ring-2 focus:ring-primary transition-all" id="emp-bank" name="bank_account" placeholder="Ex: BIAT 08 123 4567890123456 78" type="text" value="{{ $defaultBank }}"/>
</div>
@error('bank_account')<span class="font-label-sm text-label-sm text-error">{{ $message }}</span>@enderror
</div>
</div>
<div class="p-space-md rounded-xl bg-secondary-container/30 flex items-start gap-space-sm">
<span class="material-symbols-outlined text-secondary text-body-lg shrink-0 mt-0.5">info</span>
<div class="flex flex-col text-on-surface">
<span class="font-label-md text-label-md font-semibold">Conformité CNSS Tunisie</span>
<span class="font-body-sm text-body-sm text-on-surface-variant">Ces champs facultatifs peuvent être complétés ultérieurement depuis la fiche du collaborateur.</span>
</div>
</div>
</div>
<!-- Step 4: Leave Rights -->
<div class="space-y-space-md js-step-panel {{ $initialStep === 4 ? '' : 'hidden' }}" data-step-panel="4">
<div class="p-space-md rounded-xl bg-surface-container-low flex items-start gap-space-sm">
<span class="material-symbols-outlined text-primary text-body-lg shrink-0 mt-0.5">beach_access</span>
<div class="flex flex-col text-on-surface">
<span class="font-label-md text-label-md font-semibold">Droits aux congés légaux</span>
<span class="font-body-sm text-body-sm text-on-surface-variant">Le Code du Travail tunisien accorde un minimum de 30 jours de congé annuel payé par an.</span>
</div>
</div>
<div class="grid grid-cols-1 sm:grid-cols-3 gap-space-md">
<div class="flex flex-col gap-space-xs">
<label class="font-label-md text-label-md text-on-surface font-medium" for="emp-leave-rights">Droits annuels</label>
<input class="h-10 px-3 rounded-xl bg-surface-container-lowest text-on-surface font-numeric-md text-numeric-md placeholder:text-on-surface-variant focus:outline-none ring-1 ring-outline-variant focus:ring-2 focus:ring-primary transition-all" id="emp-leave-rights" max="365" min="0" name="leave_rights" placeholder="Ex: 30" type="number" value="{{ old('leave_rights', $editEmployee?->leave_rights ?? 30) }}"/>
@error('leave_rights')<span class="font-label-sm text-label-sm text-error">{{ $message }}</span>@enderror
</div>
<div class="flex flex-col gap-space-xs">
<label class="font-label-md text-label-md text-on-surface font-medium" for="emp-leave-token">Solde congés</label>
<input class="h-10 px-3 rounded-xl bg-surface-container-lowest text-on-surface font-numeric-md text-numeric-md placeholder:text-on-surface-variant focus:outline-none ring-1 ring-outline-variant focus:ring-2 focus:ring-primary transition-all" id="emp-leave-token" max="365" min="0" name="token_leave_days" placeholder="Ex: 30" type="number" value="{{ old('token_leave_days', $editEmployee?->token_leave_days ?? 30) }}"/>
@error('token_leave_days')<span class="font-label-sm text-label-sm text-error">{{ $message }}</span>@enderror
</div>
<div class="flex flex-col gap-space-xs">
<label class="font-label-md text-label-md text-on-surface font-medium" for="emp-sick-token">Solde maladie</label>
<input class="h-10 px-3 rounded-xl bg-surface-container-lowest text-on-surface font-numeric-md text-numeric-md placeholder:text-on-surface-variant focus:outline-none ring-1 ring-outline-variant focus:ring-2 focus:ring-primary transition-all" id="emp-sick-token" max="365" min="0" name="token_sick_leave_days" placeholder="Ex: 15" type="number" value="{{ old('token_sick_leave_days', $editEmployee?->token_sick_leave_days ?? 15) }}"/>
@error('token_sick_leave_days')<span class="font-label-sm text-label-sm text-error">{{ $message }}</span>@enderror
</div>
</div>
<div class="flex flex-col gap-space-xs">
<label class="font-label-md text-label-md text-on-surface font-medium" for="emp-note">Note interne RH</label>
<textarea class="min-h-[96px] px-3 py-2 rounded-xl bg-surface-container-lowest text-on-surface font-body-md text-body-md focus:outline-none ring-1 ring-outline-variant focus:ring-2 focus:ring-primary transition-all resize-y" id="emp-note" name="employee_note" placeholder="Informations complémentaires sur le collaborateur...">{{ old('employee_note', $editEmployee?->employee_note) }}</textarea>
@error('employee_note')<span class="font-label-sm text-label-sm text-error">{{ $message }}</span>@enderror
</div>
</div>
</form>
</div>
<!-- Drawer Sticky Footer Actions -->
<div class="p-space-lg bg-surface-container-lowest shadow-[0_-4px_12px_rgba(0,0,0,0.03)] flex items-center justify-between">
<button class="h-10 px-space-lg rounded-xl bg-surface-container-low hover:bg-surface-container text-on-surface font-label-lg text-label-lg transition-colors" id="modal-back-btn" type="button">
          {{ $initialStep === 1 ? 'Annuler' : 'Précédent' }}
        </button>
<button class="h-10 px-space-xl rounded-xl bg-primary-fill hover:bg-primary-container text-on-primary font-label-lg text-label-lg shadow-md flex items-center gap-space-xs transition-all transform active:scale-95" id="modal-next-btn" type="button">
<span id="modal-next-label">{{ $initialStep === 4 ? 'Enregistrer le collaborateur' : ($initialStep === 3 ? 'Suivant: Congés' : ($initialStep === 2 ? 'Suivant: Paie & CNSS' : 'Suivant: Contrat & Poste')) }}</span>
<span class="material-symbols-outlined text-body-md" id="modal-next-icon">{{ $initialStep === 4 ? 'check' : 'arrow_forward' }}</span>
</button>
</div>
</div>
</div>
@if ($viewEmployee)
@php
    $viewStatusMeta = $statusBadges[$viewEmployee->status] ?? $statusBadges['inactif'];
    $viewAvatarClass = $avatarPalette[$viewEmployee->id % 4];
    $viewAccount = $portalAccounts[mb_strtolower(trim((string) $viewEmployee->email))] ?? null;
@endphp
<!-- Slide-Over Employee Profile Drawer -->
<div class="fixed inset-0 z-50 overflow-hidden flex justify-end transition-opacity duration-300 {{ $openView ? '' : 'hidden' }}" id="view-employee-modal">
<div class="fixed inset-0 bg-black/60 backdrop-blur-xs transition-opacity" id="view-modal-backdrop"></div>
<div class="relative w-full max-w-2xl bg-surface-container-lowest shadow-2xl flex flex-col h-full z-10 overflow-hidden">
<!-- Drawer Header -->
<div class="p-space-xl bg-surface-container-low/60 flex items-start justify-between gap-space-md">
<div class="flex items-center gap-space-md min-w-0">
<div class="relative shrink-0">
<div class="w-14 h-14 rounded-full {{ $viewAvatarClass }} font-headline-md text-headline-md flex items-center justify-center font-bold relative overflow-hidden">
@if ($viewEmployee->avatar_url)
<img alt="Portrait de {{ $viewEmployee->name }}" class="absolute inset-0 w-full h-full object-cover" onerror="this.remove()" src="{{ $viewEmployee->avatar_url }}"/>
@endif
<span>{{ $viewEmployee->initials() }}</span>
</div>
<span class="absolute bottom-0 right-0 w-3 h-3 rounded-full {{ $viewStatusMeta['dot'] }} ring-2 ring-surface-container-lowest"></span>
</div>
<div class="flex flex-col min-w-0">
<h2 class="font-headline-md text-headline-md text-on-surface tracking-tight truncate">{{ $viewEmployee->name }}</h2>
<p class="font-body-sm text-body-sm text-on-surface-variant truncate">{{ $viewEmployee->position }}</p>
<div class="flex flex-wrap items-center gap-1.5 mt-1">
<span class="px-2.5 py-1 rounded-full {{ $departmentBadges[$viewEmployee->department] ?? 'bg-surface-container text-primary' }} font-label-sm text-label-sm font-semibold">{{ $viewEmployee->departmentLabel() }}</span>
<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full {{ $viewStatusMeta['badge'] }} font-label-sm text-label-sm font-semibold">
<span class="w-1.5 h-1.5 rounded-full {{ $viewStatusMeta['dot'] }}"></span>
{{ $viewEmployee->statusLabel() }}
</span>
</div>
</div>
</div>
<button aria-label="Fermer" class="w-9 h-9 rounded-xl hover:bg-surface-container text-on-surface-variant hover:text-on-surface flex items-center justify-center transition-colors shrink-0" id="close-view-modal-btn" type="button">
<span class="material-symbols-outlined text-headline-sm">close</span>
</button>
</div>
<!-- Profile Content -->
<div class="flex-1 overflow-y-auto p-space-xl space-y-space-lg">
<!-- Contact -->
<div class="bg-surface-container-low/50 rounded-xl p-space-md">
<h3 class="font-label-md text-label-md text-on-surface-variant uppercase tracking-wider mb-space-sm">Coordonnées</h3>
<div class="grid grid-cols-1 sm:grid-cols-2 gap-space-md">
<div class="flex items-start gap-space-sm">
<span class="material-symbols-outlined text-primary text-body-lg">mail</span>
<div class="flex flex-col min-w-0">
<span class="font-label-sm text-label-sm text-on-surface-variant">Email</span>
<span class="font-body-md text-body-md text-on-surface truncate">{{ $viewEmployee->email }}</span>
</div>
</div>
<div class="flex items-start gap-space-sm">
<span class="material-symbols-outlined text-primary text-body-lg">call</span>
<div class="flex flex-col min-w-0">
<span class="font-label-sm text-label-sm text-on-surface-variant">Mobile personnel</span>
<span class="font-numeric-md text-numeric-md font-mono text-on-surface">{{ $viewEmployee->mobile }}</span>
</div>
</div>
<div class="flex items-start gap-space-sm">
<span class="material-symbols-outlined text-primary text-body-lg">smartphone</span>
<div class="flex flex-col min-w-0">
<span class="font-label-sm text-label-sm text-on-surface-variant">Mobile professionnel</span>
<span class="font-numeric-md text-numeric-md font-mono text-on-surface">{{ $viewEmployee->work_mobile ?: 'Non renseigné' }}</span>
</div>
</div>
<div class="flex items-start gap-space-sm">
<span class="material-symbols-outlined text-primary text-body-lg">home_pin</span>
<div class="flex flex-col min-w-0">
<span class="font-label-sm text-label-sm text-on-surface-variant">Adresse</span>
<span class="font-body-md text-body-md text-on-surface">{{ $viewEmployee->address ?: 'Non renseignée' }}</span>
</div>
</div>
</div>
</div>
<!-- Legal & Contract -->
<div class="bg-surface-container-low/50 rounded-xl p-space-md">
<h3 class="font-label-md text-label-md text-on-surface-variant uppercase tracking-wider mb-space-sm">Dossier légal &amp; contrat</h3>
<div class="grid grid-cols-1 sm:grid-cols-2 gap-space-md">
<div class="flex items-start gap-space-sm">
<span class="material-symbols-outlined text-primary text-body-lg">badge</span>
<div class="flex flex-col">
<span class="font-label-sm text-label-sm text-on-surface-variant">CIN tunisienne</span>
<span class="font-numeric-md text-numeric-md font-mono text-on-surface">{{ $viewEmployee->cin }}</span>
</div>
</div>
<div class="flex items-start gap-space-sm">
<span class="material-symbols-outlined text-primary text-body-lg">verified_user</span>
<div class="flex flex-col">
<span class="font-label-sm text-label-sm text-on-surface-variant">Affiliation CNSS</span>
<span class="font-numeric-md text-numeric-md font-mono text-on-surface">{{ $viewEmployee->cnss_number ?: 'Non renseigné' }}</span>
</div>
</div>
<div class="flex items-start gap-space-sm">
<span class="material-symbols-outlined text-primary text-body-lg">work</span>
<div class="flex flex-col">
<span class="font-label-sm text-label-sm text-on-surface-variant">Contrat</span>
<span class="font-body-md text-body-md text-on-surface">{{ $viewEmployee->contract_type }}</span>
</div>
</div>
<div class="flex items-start gap-space-sm">
<span class="material-symbols-outlined text-primary text-body-lg">event</span>
<div class="flex flex-col">
<span class="font-label-sm text-label-sm text-on-surface-variant">Date d'embauche</span>
<span class="font-body-md text-body-md text-on-surface">
@if ($viewEmployee->hire_date)
{{ $viewEmployee->hire_date->format('d') }} {{ $frenchMonths[(int) $viewEmployee->hire_date->format('n')] }} {{ $viewEmployee->hire_date->format('Y') }}
@else
Non renseignée
@endif
</span>
<span class="font-label-sm text-label-sm text-on-surface-variant">{{ $viewEmployee->seniorityLabel() }}</span>
</div>
</div>
<div class="flex items-start gap-space-sm">
<span class="material-symbols-outlined text-primary text-body-lg">cake</span>
<div class="flex flex-col">
<span class="font-label-sm text-label-sm text-on-surface-variant">Date de naissance</span>
<span class="font-body-md text-body-md text-on-surface">{{ $viewEmployee->date_of_birth?->format('d/m/Y') ?? 'Non renseignée' }}</span>
</div>
</div>
<div class="flex items-start gap-space-sm">
<span class="material-symbols-outlined text-primary text-body-lg">family_restroom</span>
<div class="flex flex-col">
<span class="font-label-sm text-label-sm text-on-surface-variant">Situation familiale</span>
<span class="font-body-md text-body-md text-on-surface">{{ $familyStatusLabels[$viewEmployee->family_status] ?? 'Non renseignée' }} • {{ $viewEmployee->children }} enfant(s)</span>
</div>
</div>
<div class="flex items-start gap-space-sm sm:col-span-2">
<span class="material-symbols-outlined text-primary text-body-lg">account_balance</span>
<div class="flex flex-col min-w-0">
<span class="font-label-sm text-label-sm text-on-surface-variant">Compte bancaire (RIB)</span>
<span class="font-numeric-md text-numeric-md font-mono text-on-surface break-all">{{ $viewEmployee->bank_account ?: 'Non renseigné' }}</span>
</div>
</div>
</div>
</div>
<!-- Leave Balances -->
<div class="bg-surface-container-low/50 rounded-xl p-space-md">
<h3 class="font-label-md text-label-md text-on-surface-variant uppercase tracking-wider mb-space-sm">Droits aux congés</h3>
<div class="grid grid-cols-3 gap-space-sm">
<div class="rounded-xl bg-surface-container-lowest p-space-sm text-center shadow-xs">
<span class="font-label-sm text-label-sm text-on-surface-variant block">Droits annuels</span>
<span class="font-headline-sm text-headline-sm text-on-surface font-bold">{{ $viewEmployee->leave_rights ?? 0 }} j</span>
</div>
<div class="rounded-xl bg-surface-container-lowest p-space-sm text-center shadow-xs">
<span class="font-label-sm text-label-sm text-on-surface-variant block">Solde congés</span>
<span class="font-headline-sm text-headline-sm text-primary font-bold">{{ $viewEmployee->token_leave_days ?? 0 }} j</span>
</div>
<div class="rounded-xl bg-surface-container-lowest p-space-sm text-center shadow-xs">
<span class="font-label-sm text-label-sm text-on-surface-variant block">Solde maladie</span>
<span class="font-headline-sm text-headline-sm text-tertiary font-bold">{{ $viewEmployee->token_sick_leave_days ?? 0 }} j</span>
</div>
</div>
</div>
@if ($viewAccount)
<div class="bg-surface-container-low/50 rounded-xl p-space-md flex flex-wrap items-center justify-between gap-space-md">
<div class="flex items-center gap-space-sm min-w-0">
<span class="w-10 h-10 rounded-full {{ $viewAccount->isActive() ? 'bg-tertiary-fixed/40 text-tertiary' : 'bg-error-container/50 text-error' }} flex items-center justify-center shrink-0">
<span class="material-symbols-outlined text-body-lg">{{ $viewAccount->isActive() ? 'verified_user' : 'lock' }}</span>
</span>
<div class="flex flex-col min-w-0">
<span class="font-label-md text-label-md text-on-surface-variant uppercase tracking-wider">Accès au portail</span>
<span class="font-body-md text-body-md text-on-surface truncate">{{ $viewAccount->email }}</span>
</div>
</div>
<div class="flex items-center gap-space-sm">
<span class="px-2.5 py-1 rounded-full {{ $viewAccount->isActive() ? 'bg-tertiary-fixed/40 text-tertiary' : 'bg-error-container/50 text-error' }} font-label-sm text-label-sm font-semibold">{{ $viewAccount->isActive() ? 'Actif' : 'Désactivé' }}</span>
<form data-access-toggle data-action="{{ $viewAccount->isActive() ? 'disable' : 'enable' }}" data-account-email="{{ $viewAccount->email }}" data-employee="{{ $viewEmployee->name }}" method="POST" action="{{ route('admin.employees.access', $viewEmployee) }}">
@csrf
@method('PATCH')
<button class="h-9 px-space-md rounded-xl font-label-md text-label-md font-semibold flex items-center gap-1.5 transition-colors {{ $viewAccount->isActive() ? 'bg-error-container text-on-error-container hover:bg-error hover:text-on-error' : 'bg-primary-fill text-on-primary hover:bg-primary-container' }}" type="submit">
<span class="material-symbols-outlined text-body-md">{{ $viewAccount->isActive() ? 'lock' : 'how_to_reg' }}</span>
<span>{{ $viewAccount->isActive() ? 'Désactiver' : 'Réactiver' }} l'accès</span>
</button>
</form>
</div>
</div>
@endif
@if ($viewEmployee->cin_file_path)
<div class="rounded-xl p-space-md bg-surface-container-low flex items-center justify-between">
<div class="flex items-center gap-space-sm min-w-0">
<span class="material-symbols-outlined text-primary text-headline-sm">description</span>
<div class="flex flex-col min-w-0">
<span class="font-label-md text-label-md text-on-surface font-semibold truncate">{{ basename($viewEmployee->cin_file_path) }}</span>
<span class="font-label-sm text-label-sm text-on-surface-variant">Copie CIN téléversée</span>
</div>
</div>
<a class="h-9 px-space-md rounded-xl bg-surface-container-lowest hover:bg-surface-container text-primary font-label-md text-label-md shadow-xs flex items-center gap-1 transition-colors" href="{{ route('admin.employees.cin', $viewEmployee) }}">
<span class="material-symbols-outlined text-body-md">download</span>
<span>Télécharger</span>
</a>
</div>
@endif
@if ($viewEmployee->employee_note)
<div class="p-space-md rounded-xl bg-secondary-container/30 flex items-start gap-space-sm">
<span class="material-symbols-outlined text-secondary text-body-lg shrink-0 mt-0.5">sticky_note_2</span>
<div class="flex flex-col text-on-surface">
<span class="font-label-md text-label-md font-semibold">Note interne RH</span>
<span class="font-body-sm text-body-sm text-on-surface-variant">{{ $viewEmployee->employee_note }}</span>
</div>
</div>
@endif
</div>
<!-- Footer -->
<div class="p-space-lg bg-surface-container-lowest shadow-[0_-4px_12px_rgba(0,0,0,0.03)] flex items-center justify-between">
<button class="h-10 px-space-lg rounded-xl bg-surface-container-low hover:bg-surface-container text-on-surface font-label-lg text-label-lg transition-colors" id="cancel-view-modal-btn" type="button">
          Fermer
        </button>
<a class="h-10 px-space-xl rounded-xl bg-primary-fill hover:bg-primary-container text-on-primary font-label-lg text-label-lg shadow-md flex items-center gap-space-xs transition-all transform active:scale-95" href="{{ $linkWith(['edit' => $viewEmployee->id]) }}">
<span class="material-symbols-outlined text-body-md">edit</span>
<span>Modifier le profil</span>
</a>
</div>
</div>
</div>
@endif
@include('partials.access-toggle-dialog')
<!-- Inline UI Interactions Script -->
<script>
    (function() {
      const drawer = document.getElementById('add-employee-modal');
      const closeBtn = document.getElementById('close-modal-btn');
      const backBtn = document.getElementById('modal-back-btn');
      const nextBtn = document.getElementById('modal-next-btn');
      const nextLabel = document.getElementById('modal-next-label');
      const nextIcon = document.getElementById('modal-next-icon');
      const backdrop = document.getElementById('modal-backdrop');
      const form = document.getElementById('new-employee-form');
      const panels = Array.from(document.querySelectorAll('.js-step-panel'));
      const stepTriggers = Array.from(document.querySelectorAll('.js-step-trigger'));
      const viewModal = document.getElementById('view-employee-modal');
      const viewCloseBtn = document.getElementById('close-view-modal-btn');
      const viewCancelBtn = document.getElementById('cancel-view-modal-btn');
      const viewBackdrop = document.getElementById('view-modal-backdrop');
      const rowMenu = document.getElementById('row-action-menu');
      const nextLabels = {
        1: 'Suivant: Contrat & Poste',
        2: 'Suivant: Paie & CNSS',
        3: 'Suivant: Congés',
        4: 'Enregistrer le collaborateur'
      };
      let currentStep = 1;

      function lockScroll(locked) {
        document.body.style.overflow = locked ? 'hidden' : '';
      }

      function setStep(step) {
        currentStep = Math.min(4, Math.max(1, step));
        panels.forEach(function(panel) {
          panel.classList.toggle('hidden', Number(panel.dataset.stepPanel) !== currentStep);
        });
        stepTriggers.forEach(function(trigger) {
          const isActive = Number(trigger.dataset.stepTarget) === currentStep;
          trigger.classList.toggle('bg-primary-container', isActive);
          trigger.classList.toggle('text-on-primary', isActive);
          trigger.classList.toggle('shadow-xs', isActive);
          trigger.classList.toggle('text-on-surface-variant', !isActive);
          trigger.classList.toggle('hover:bg-surface-container', !isActive);
          trigger.classList.toggle('transition-colors', !isActive);
          const number = trigger.querySelector('.js-step-number');
          if (number) {
            number.classList.toggle('bg-surface-container-lowest', isActive);
            number.classList.toggle('text-primary', isActive);
            number.classList.toggle('bg-surface-container', !isActive);
            number.classList.toggle('text-on-surface', !isActive);
          }
          const label = trigger.querySelector('.js-step-label');
          if (label) {
            label.classList.toggle('font-semibold', isActive);
            label.classList.toggle('font-medium', !isActive);
          }
        });
        if (backBtn) backBtn.textContent = currentStep === 1 ? 'Annuler' : 'Précédent';
        if (nextLabel) nextLabel.textContent = nextLabels[currentStep];
        if (nextIcon) nextIcon.textContent = currentStep === 4 ? 'check' : 'arrow_forward';
      }

      function panelIsValid(stepNumber, report) {
        const panel = panels.find(function(panel) { return Number(panel.dataset.stepPanel) === stepNumber; });
        if (!panel) return true;
        const fields = Array.from(panel.querySelectorAll('input, select, textarea')).filter(function(field) {
          return field.willValidate && !field.disabled && field.type !== 'hidden' && field.type !== 'file';
        });
        for (const field of fields) {
          if (!field.checkValidity()) {
            if (report) {
              field.reportValidity();
              field.focus();
            }
            return false;
          }
        }
        return true;
      }

      function closeDrawer() {
        if (!drawer) return;
        if (drawer.dataset.isEditing === '1') {
          window.location.href = drawer.dataset.closeUrl;
          return;
        }
        drawer.classList.add('hidden');
        drawer.classList.remove('flex');
        lockScroll(false);
        const url = new URL(window.location.href);
        if (url.searchParams.has('new')) {
          url.searchParams.delete('new');
          window.history.replaceState({}, '', url);
        }
      }

      function closeViewModal() {
        if (!viewModal) return;
        viewModal.classList.add('hidden');
        viewModal.classList.remove('flex');
        const url = new URL(window.location.href);
        if (url.searchParams.has('view')) {
          url.searchParams.delete('view');
          window.history.replaceState({}, '', url);
        }
        if (drawer && !drawer.classList.contains('hidden')) {
          lockScroll(true);
        } else {
          lockScroll(false);
        }
      }

      if (drawer) {
        currentStep = Number(drawer.dataset.initialStep || '1') || 1;
        if (!drawer.classList.contains('hidden')) {
          lockScroll(true);
          setStep(currentStep);
        }
      }

      if (closeBtn) closeBtn.addEventListener('click', closeDrawer);
      if (backdrop) backdrop.addEventListener('click', closeDrawer);

      if (backBtn) {
        backBtn.addEventListener('click', function() {
          if (currentStep === 1) {
            closeDrawer();
          } else {
            setStep(currentStep - 1);
          }
        });
      }

      if (nextBtn && form) {
        nextBtn.addEventListener('click', function() {
          if (currentStep < 4) {
            if (panelIsValid(currentStep, true)) setStep(currentStep + 1);
            return;
          }
          for (let step = 1; step <= 4; step++) {
            if (!panelIsValid(step, false)) {
              setStep(step);
              panelIsValid(step, true);
              return;
            }
          }
          nextBtn.disabled = true;
          form.requestSubmit();
        });
      }

      stepTriggers.forEach(function(trigger) {
        trigger.addEventListener('click', function() {
          setStep(Number(trigger.dataset.stepTarget));
        });
      });

      if (viewCloseBtn) viewCloseBtn.addEventListener('click', closeViewModal);
      if (viewCancelBtn) viewCancelBtn.addEventListener('click', closeViewModal);
      if (viewBackdrop) viewBackdrop.addEventListener('click', closeViewModal);
      if (viewModal && !viewModal.classList.contains('hidden')) {
        lockScroll(true);
      }

      // CIN file upload
      const cinInput = document.getElementById('emp-cin-file');
      const cinUploadBtn = document.getElementById('cin-upload-btn');
      const cinClear = document.getElementById('cin-file-clear');
      const cinName = document.getElementById('cin-file-name');
      const cinMeta = document.getElementById('cin-file-meta');
      const cinExisting = document.getElementById('cin-existing-link');
      const removeCin = document.getElementById('remove-cin-file');

      if (cinUploadBtn && cinInput) cinUploadBtn.addEventListener('click', function() { cinInput.click(); });

      if (cinInput) {
        cinInput.addEventListener('change', function() {
          const file = cinInput.files && cinInput.files[0];
          if (!file) return;
          cinName.textContent = file.name;
          cinMeta.textContent = (file.size / 1024 / 1024).toFixed(1).replace('.', ',') + ' Mo • Prêt à téléverser';
          cinClear.classList.remove('hidden');
          if (removeCin) removeCin.value = '0';
        });
      }

      if (cinClear) {
        cinClear.addEventListener('click', function() {
          if (cinInput) cinInput.value = '';
          cinName.textContent = 'Aucun document téléversé';
          cinMeta.textContent = 'Le document existant sera retiré à l\'enregistrement';
          cinClear.classList.add('hidden');
          if (cinExisting) cinExisting.classList.add('hidden');
          if (removeCin) removeCin.value = '1';
        });
      }

      // Row action menu
      if (rowMenu) {
        const viewLink = document.getElementById('row-menu-view');
        const editLink = document.getElementById('row-menu-edit');
        const cinLink = document.getElementById('row-menu-cin');
        const deleteBtn = document.getElementById('row-menu-delete');
        let deleteFormId = null;

        document.addEventListener('click', function(event) {
          const trigger = event.target.closest('.row-menu-btn');
          if (trigger) {
            event.preventDefault();
            deleteFormId = trigger.dataset.deleteForm || null;
            viewLink.href = trigger.dataset.viewUrl;
            editLink.href = trigger.dataset.editUrl;
            if (trigger.dataset.cinUrl) {
              cinLink.href = trigger.dataset.cinUrl;
              cinLink.classList.remove('hidden');
            } else {
              cinLink.classList.add('hidden');
            }
            rowMenu.classList.remove('hidden');
            const rect = trigger.getBoundingClientRect();
            const menuWidth = rowMenu.offsetWidth;
            const menuHeight = rowMenu.offsetHeight;
            let left = rect.right - menuWidth;
            left = Math.max(8, Math.min(left, window.innerWidth - menuWidth - 8));
            let top = rect.bottom + 8;
            if (top + menuHeight > window.innerHeight - 8) {
              top = rect.top - menuHeight - 8;
            }
            rowMenu.style.left = left + 'px';
            rowMenu.style.top = Math.max(8, top) + 'px';
            return;
          }
          if (!rowMenu.contains(event.target)) rowMenu.classList.add('hidden');
        });

        [viewLink, editLink, cinLink].forEach(function(link) {
          link.addEventListener('click', function() { rowMenu.classList.add('hidden'); });
        });

        deleteBtn.addEventListener('click', function() {
          if (!deleteFormId) return;
          const deleteForm = document.getElementById(deleteFormId);
          if (deleteForm && window.confirm('Supprimer définitivement ce collaborateur du registre ?')) {
            deleteForm.submit();
          }
          rowMenu.classList.add('hidden');
        });
      }

      // Bulk selection
      const selectAll = document.getElementById('select-all');
      const bulkActions = document.getElementById('bulk-actions');
      const bulkCount = document.getElementById('bulk-count');
      const bulkDeleteBtn = document.getElementById('bulk-delete-btn');
      const bulkForm = document.getElementById('bulk-delete-form');
      const rowChecks = function() { return Array.from(document.querySelectorAll('.employee-select')); };

      function updateBulkBar() {
        const checks = rowChecks();
        const selected = checks.filter(function(check) { return check.checked; });
        if (bulkCount) bulkCount.textContent = selected.length;
        if (bulkActions) bulkActions.classList.toggle('hidden', selected.length === 0);
        if (selectAll) {
          selectAll.checked = checks.length > 0 && selected.length === checks.length;
          selectAll.indeterminate = selected.length > 0 && selected.length < checks.length;
        }
        document.querySelectorAll('tr[data-employee-row]').forEach(function(row) {
          const check = row.querySelector('.employee-select');
          row.classList.toggle('bg-primary-fixed/10', !!check && check.checked);
        });
      }

      if (selectAll) {
        selectAll.addEventListener('change', function() {
          rowChecks().forEach(function(check) { check.checked = selectAll.checked; });
          updateBulkBar();
        });
      }

      document.addEventListener('change', function(event) {
        if (event.target.classList.contains('employee-select')) updateBulkBar();
      });

      if (bulkDeleteBtn && bulkForm) {
        bulkDeleteBtn.addEventListener('click', function() {
          const selected = rowChecks().filter(function(check) { return check.checked; });
          if (!selected.length) return;
          if (!window.confirm('Supprimer ' + selected.length + ' collaborateur(s) sélectionné(s) ?')) return;
          bulkForm.querySelectorAll('input[name="ids[]"]').forEach(function(input) { input.remove(); });
          selected.forEach(function(check) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'ids[]';
            input.value = check.value;
            bulkForm.appendChild(input);
          });
          bulkForm.submit();
        });
      }

      // Live search
      const filtersForm = document.getElementById('employee-filters');
      const searchInput = document.getElementById('employee-search');
      if (filtersForm && searchInput) {
        let searchTimer;
        searchInput.addEventListener('input', function() {
          clearTimeout(searchTimer);
          searchTimer = setTimeout(function() { filtersForm.requestSubmit(); }, 500);
        });
      }

      // Table / grid toggle
      const tableViewBtn = document.getElementById('table-view-btn');
      const gridViewBtn = document.getElementById('grid-view-btn');
      const tableView = document.getElementById('employees-table-wrap');
      const gridView = document.getElementById('employees-grid');

      function setView(mode) {
        const grid = mode === 'grid';
        if (tableView) tableView.classList.toggle('hidden', grid);
        if (gridView) gridView.classList.toggle('hidden', !grid);
        if (tableViewBtn) {
          tableViewBtn.classList.toggle('bg-surface-container-lowest', !grid);
          tableViewBtn.classList.toggle('text-primary', !grid);
          tableViewBtn.classList.toggle('shadow-xs', !grid);
          tableViewBtn.classList.toggle('text-on-surface-variant', grid);
          tableViewBtn.classList.toggle('hover:text-on-surface', grid);
          tableViewBtn.classList.toggle('hover:bg-surface-container', grid);
          tableViewBtn.classList.toggle('transition-colors', grid);
        }
        if (gridViewBtn) {
          gridViewBtn.classList.toggle('bg-surface-container-lowest', grid);
          gridViewBtn.classList.toggle('text-primary', grid);
          gridViewBtn.classList.toggle('shadow-xs', grid);
          gridViewBtn.classList.toggle('text-on-surface-variant', !grid);
          gridViewBtn.classList.toggle('hover:text-on-surface', !grid);
          gridViewBtn.classList.toggle('hover:bg-surface-container', !grid);
          gridViewBtn.classList.toggle('transition-colors', !grid);
        }
      }

      if (tableViewBtn) tableViewBtn.addEventListener('click', function() { setView('table'); });
      if (gridViewBtn) gridViewBtn.addEventListener('click', function() { setView('grid'); });

      // Escape key handling
      document.addEventListener('keydown', function(event) {
        if (event.key !== 'Escape') return;
        if (rowMenu && !rowMenu.classList.contains('hidden')) {
          rowMenu.classList.add('hidden');
          return;
        }
        if (viewModal && !viewModal.classList.contains('hidden')) {
          closeViewModal();
          return;
        }
        if (drawer && !drawer.classList.contains('hidden')) closeDrawer();
      });

      // Flash toast auto-hide
      const toast = document.getElementById('flash-toast');
      if (toast) {
        setTimeout(function() {
          toast.classList.add('opacity-0', 'translate-y-2');
          toast.style.transition = 'opacity 300ms ease, transform 300ms ease';
          setTimeout(function() { toast.remove(); }, 320);
        }, 4500);
      }
    })();
  </script>
</div>
</x-admin-shell>
