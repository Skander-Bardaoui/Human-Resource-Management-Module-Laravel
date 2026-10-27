<x-admin-shell active="documents" title="Gestion Documentaire & Fichiers Collaborateurs">
<div class="flex flex-col w-full pb-margin">

@if (session('success'))
<div class="mb-space-md p-space-md rounded-xl bg-[#ECFDF5] text-[#065F46] font-label-lg text-label-lg flex items-center gap-space-sm shadow-sm">
    <span class="material-symbols-outlined text-body-lg">check_circle</span>
    <span>{{ session('success') }}</span>
</div>
@endif
@if (session('error'))
<div class="mb-space-md p-space-md rounded-xl bg-[#FFFBEB] text-[#92400E] font-label-lg text-label-lg flex items-center gap-space-sm shadow-sm">
    <span class="material-symbols-outlined text-body-lg">error</span>
    <span>{{ session('error') }}</span>
</div>
@endif

<!-- Breadcrumb & Top Bar -->
<div class="flex flex-col md:flex-row md:items-center justify-between gap-space-md mb-space-lg">
<div class="flex flex-col gap-space-xs">
<div class="flex items-center gap-space-xs font-label-sm text-label-sm text-on-surface-variant">
<span class="hover:text-primary transition-colors cursor-pointer">Accueil</span>
<span class="material-symbols-outlined text-[14px]">chevron_right</span>
<span class="text-on-surface font-semibold">Documents RH</span>
</div>
<h1 class="font-headline-lg text-headline-lg text-on-surface tracking-tight">Gestion Documentaire &amp; Fichiers Collaborateurs</h1>
</div>
<!-- Global CTA Buttons -->
<div class="flex items-center gap-space-sm self-start md:self-auto shrink-0">
<button class="h-10 px-space-md rounded-xl bg-surface-container-lowest text-secondary hover:text-on-surface hover:bg-surface-container shadow-sm transition-all flex items-center gap-space-xs font-label-lg text-label-lg active:scale-95" form="zip-form" type="submit" title="Télécharger les documents cochés (ZIP)">
<span class="material-symbols-outlined text-body-lg">folder_zip</span>
<span>Télécharger la sélection (ZIP)</span>
</button>
<button class="h-10 px-space-lg rounded-xl bg-primary-container text-on-primary hover:bg-primary shadow-sm hover:shadow-md transition-all flex items-center gap-space-xs font-label-lg text-label-lg active:scale-95" type="button" onclick="openModal('upload-modal')">
<span class="material-symbols-outlined text-body-lg">upload_file</span>
<span>+ Téléverser un Document</span>
</button>
</div>
</div>

<form id="zip-form" method="POST" action="{{ route('admin.documents.zip') }}" class="hidden">@csrf</form>

<!-- Key Metrics / Storage Bento Summary -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-space-md mb-space-xl">
<!-- Total Documents -->
<div class="bg-surface-container-lowest rounded-xl p-space-lg shadow-sm hover:shadow-md transition-shadow flex flex-col justify-between">
<div class="flex items-center justify-between">
<span class="font-label-sm text-label-sm uppercase tracking-wider text-on-surface-variant">Total Documents</span>
<div class="w-8 h-8 rounded-lg bg-surface-container flex items-center justify-center text-primary">
<span class="material-symbols-outlined text-body-lg">inventory_2</span>
</div>
</div>
<div class="mt-space-md">
<span class="font-display-lg text-display-lg text-on-surface tracking-tight">{{ $stats['total'] }}</span>
<div class="flex items-center gap-1.5 mt-space-xs">
<span class="w-1.5 h-1.5 rounded-full bg-tertiary"></span>
<span class="font-body-sm text-body-sm text-on-surface-variant truncate">Contrats, Avenants, Cartes CIN</span>
</div>
</div>
</div>
<!-- En Attente de Signature -->
<div class="bg-surface-container-lowest rounded-xl p-space-lg shadow-sm hover:shadow-md transition-shadow flex flex-col justify-between">
<div class="flex items-center justify-between">
<span class="font-label-sm text-label-sm uppercase tracking-wider text-on-surface-variant">Signature Requise</span>
<div class="w-8 h-8 rounded-lg bg-secondary-container flex items-center justify-center text-secondary">
<span class="material-symbols-outlined text-body-lg">draw</span>
</div>
</div>
<div class="mt-space-md">
<div class="flex items-baseline gap-space-xs">
<span class="font-display-lg text-display-lg text-on-surface tracking-tight">{{ $stats['signature_required'] }}</span>
@if ($stats['signature_required'] > 0)
<span class="font-label-md text-label-md text-error font-semibold">Action requise</span>
@endif
</div>
<p class="font-body-sm text-body-sm text-on-surface-variant mt-space-xs">Collaborateurs à relancer par SMS/Mail</p>
</div>
</div>
<!-- Pièces à Renouveler -->
<div class="bg-surface-container-lowest rounded-xl p-space-lg shadow-sm hover:shadow-md transition-shadow flex flex-col justify-between">
<div class="flex items-center justify-between">
<span class="font-label-sm text-label-sm uppercase tracking-wider text-on-surface-variant">Pièces à Renouveler</span>
<div class="w-8 h-8 rounded-lg bg-surface-container-high flex items-center justify-center text-primary-fixed-dim">
<span class="material-symbols-outlined text-body-lg">update</span>
</div>
</div>
<div class="mt-space-md">
<div class="flex items-baseline gap-space-xs">
<span class="font-display-lg text-display-lg text-on-surface tracking-tight">{{ $stats['to_renew'] }}</span>
<span class="font-label-sm text-label-sm text-secondary bg-surface-container px-1.5 py-0.5 rounded">Échéance &lt; 30j</span>
</div>
<p class="font-body-sm text-body-sm text-on-surface-variant mt-space-xs truncate">Certificats médicaux &amp; Séjours</p>
</div>
</div>
<!-- Cloud Storage Usage Gauge -->
<div class="bg-surface-container-lowest rounded-xl p-space-lg shadow-sm hover:shadow-md transition-shadow flex flex-col justify-between">
<div class="flex items-center justify-between">
<span class="font-label-sm text-label-sm uppercase tracking-wider text-on-surface-variant">Espace Cloud Stockage</span>
<div class="w-8 h-8 rounded-lg bg-surface-container flex items-center justify-center text-primary">
<span class="material-symbols-outlined text-body-lg">cloud_sync</span>
</div>
</div>
<div class="mt-space-md">
<div class="flex items-baseline justify-between mb-1">
<span class="font-numeric-md text-numeric-md font-bold text-on-surface">{{ number_format($stats['storage_bytes'] / (1024 ** 3), 1, ',', ' ') }} Go</span>
<span class="font-label-sm text-label-sm text-on-surface-variant">100 Go alloués</span>
</div>
<div class="w-full bg-surface-container rounded-full h-2 overflow-hidden">
<div class="bg-primary-container h-full rounded-full transition-all duration-500" style="width: {{ $stats['storage_percent'] }}%"></div>
</div>
<span class="font-label-sm text-label-sm text-tertiary font-medium block mt-1">Serveur local Tunisie Telecom DataCenter</span>
</div>
</div>
</div>
<!-- Drag and Drop Smart Upload Ingestion Area -->
<div class="relative group cursor-pointer bg-surface-container-lowest rounded-xl p-space-lg shadow-sm hover:shadow-md transition-all mb-space-xl overflow-hidden" id="drop-zone">
<div class="flex flex-col lg:flex-row items-center justify-between gap-space-md">
<div class="flex items-center gap-space-lg">
<div class="w-14 h-14 rounded-xl bg-surface-container-low group-hover:bg-primary-container/10 text-primary flex items-center justify-center transition-colors shrink-0">
<span class="material-symbols-outlined text-[32px]">cloud_upload</span>
</div>
<div class="flex flex-col">
<div class="flex items-center gap-space-sm flex-wrap">
<span class="font-headline-sm text-headline-sm text-on-surface font-semibold">Glissez-déposez vos fichiers ici ou Parcourir</span>
<span class="px-2 py-0.5 rounded-full bg-tertiary/10 text-tertiary font-label-sm text-label-sm font-semibold flex items-center gap-1">
<span class="material-symbols-outlined text-[13px]">smart_toy</span> Reconnaissance OCR CIN active
            </span>
</div>
<p class="font-body-sm text-body-sm text-on-surface-variant mt-0.5">
            Formats acceptés : PDF, PNG, JPG, JPEG, DOCX jusqu'à 25 Mo • Rattachement automatique au dossier agent par scan CIN/Matricule.
          </p>
</div>
</div>
<div class="flex items-center gap-space-sm shrink-0">
<button class="h-9 px-space-md rounded-lg bg-surface-container-high hover:bg-surface-container text-on-surface font-label-md text-label-md transition-colors flex items-center gap-1.5" type="button" onclick="document.getElementById('upload-files-input').click()">
<span class="material-symbols-outlined text-body-md">folder_open</span>
<span>Explorer les dossiers</span>
</button>
</div>
</div>
</div>
<!-- Navigation Filters & Search Ribbon -->
<div class="bg-surface-container-lowest rounded-xl p-space-md shadow-sm mb-space-lg flex flex-col gap-space-md">
<!-- Search and quick controls -->
<div class="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-space-md">
<form method="GET" action="{{ route('admin.documents') }}" class="relative flex-1 max-w-lg">
<span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-body-lg pointer-events-none">search</span>
<input class="w-full h-10 pl-10 pr-4 rounded-lg bg-surface-container-low font-body-md text-body-md text-on-surface placeholder:text-on-surface-variant focus:outline-none focus:bg-surface-container transition-all" id="search-input" name="search" placeholder="Rechercher par libellé, agent, CIN (ex: 11029482), tag..." type="text" value="{{ request('search') }}"/>
</form>
<!-- View Switcher & Actions -->
<div class="flex items-center justify-between md:justify-end gap-space-sm">
<div class="flex items-center bg-surface-container-low p-0.5 rounded-lg">
<button class="px-3 py-1.5 rounded-md font-label-md text-label-md flex items-center gap-1.5 bg-surface-container-lowest text-primary shadow-xs transition-all" id="view-grid-btn" onclick="switchView('grid')" type="button">
<span class="material-symbols-outlined text-[18px]">grid_view</span>
<span class="hidden sm:inline">Grille</span>
</button>
<button class="px-3 py-1.5 rounded-md font-label-md text-label-md flex items-center gap-1.5 text-on-surface-variant hover:text-on-surface transition-all" id="view-list-btn" onclick="switchView('list')" type="button">
<span class="material-symbols-outlined text-[18px]">view_list</span>
<span class="hidden sm:inline">Liste détaillée</span>
</button>
</div>
<button class="h-9 px-space-md rounded-lg bg-surface-container-low text-on-surface-variant hover:text-on-surface hover:bg-surface-container flex items-center gap-1 font-label-md text-label-md transition-colors" type="button">
<span class="material-symbols-outlined text-[18px]">filter_list</span>
<span>Trier: Récent</span>
</button>
</div>
</div>
<!-- Category Segments Bar -->
<div class="flex items-center gap-space-xs overflow-x-auto pb-1 scrollbar-none" id="category-bar">
<a class="category-pill shrink-0 px-space-md py-1.5 rounded-full font-label-md text-label-md transition-all {{ !request('category') ? 'bg-primary-container text-on-primary' : 'bg-surface-container-low text-on-surface-variant hover:bg-surface-container hover:text-on-surface' }}" href="{{ route('admin.documents', array_merge(request()->except('category'), [])) }}">
        Tous ({{ $stats['total'] }})
      </a>
@foreach (App\Models\Document::CATEGORIES as $key => $label)
<a class="category-pill shrink-0 px-space-md py-1.5 rounded-full font-label-md text-label-md transition-all {{ request('category') === $key ? 'bg-primary-container text-on-primary' : 'bg-surface-container-low text-on-surface-variant hover:bg-surface-container hover:text-on-surface' }}" href="{{ route('admin.documents', array_merge(request()->query(), ['category' => $key])) }}">
        {{ $label }} ({{ $categoryCounts[$key] }})
      </a>
@endforeach
</div>
</div>

@php
$categoryIcons = [
    'contrats' => ['description', 'bg-error-container/40 text-error'],
    'identite' => ['badge', 'bg-secondary-container text-primary-container'],
    'paie' => ['receipt_long', 'bg-surface-container-high text-primary'],
    'diplomes' => ['school', 'bg-tertiary-container/20 text-tertiary'],
    'attestations' => ['assignment', 'bg-surface-container text-secondary'],
];
$statusPills = [
    'actif' => ['bg-[#ECFDF5] text-[#065F46]', 'bg-[#10B981]'],
    'conforme' => ['bg-[#ECFDF5] text-[#065F46]', 'bg-[#10B981]'],
    'en_attente_signature' => ['bg-[#FFFBEB] text-[#92400E]', 'bg-[#F59E0B]'],
    'cloture' => ['bg-[#F1F5F9] text-[#475569]', 'bg-[#64748B]'],
];
@endphp

<!-- Documents Grid View Container -->
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-space-lg" id="documents-grid">
@forelse ($documents as $doc)
@php
    [$icon, $iconClasses] = $categoryIcons[$doc->category] ?? ['description', 'bg-surface-container text-on-surface-variant'];
    [$pillClasses, $dotClasses] = $statusPills[$doc->status] ?? ['bg-[#F1F5F9] text-[#475569]', 'bg-[#64748B]'];
    $sizeLabel = $doc->size >= 1048576
        ? number_format($doc->size / 1048576, 1, ',', ' ') . ' Mo'
        : max(1, (int) round($doc->size / 1024)) . ' Ko';
@endphp
<div class="doc-card group bg-surface-container-lowest rounded-xl p-space-lg shadow-sm hover:shadow-md transition-all flex flex-col justify-between">
<div>
<div class="flex items-start justify-between gap-space-sm mb-space-md">
<div class="flex items-center gap-space-sm min-w-0">
<div class="w-10 h-10 rounded-lg {{ $iconClasses }} flex items-center justify-center shrink-0">
<span class="material-symbols-outlined text-[22px]">{{ $icon }}</span>
</div>
<div class="min-w-0 flex flex-col">
<h2 class="font-headline-sm text-headline-sm text-on-surface truncate group-hover:text-primary transition-colors cursor-pointer" title="{{ $doc->name }}" onclick="window.open('{{ route('admin.documents.download', $doc) }}', '_blank')">{{ $doc->name }}</h2>
<span class="font-label-sm text-label-sm text-on-surface-variant font-numeric-md">{{ $sizeLabel }} • Ajouté le {{ $doc->created_at->format('d M Y') }}</span>
</div>
</div>
<!-- Status Pill -->
<span class="shrink-0 h-6 px-2.5 rounded-full font-label-sm text-label-sm font-semibold inline-flex items-center gap-1.5 {{ $pillClasses }}">
<span class="w-1.5 h-1.5 rounded-full {{ $dotClasses }}"></span>
<span>{{ App\Models\Document::STATUSES[$doc->status] ?? $doc->status }}</span>
</span>
</div>
<div class="bg-surface-container-low rounded-lg p-space-sm mb-space-md flex flex-col gap-1">
<div class="flex items-center justify-between text-body-sm font-body-sm">
<span class="text-on-surface-variant">Salarié :</span>
<span class="text-on-surface font-semibold">{{ $doc->employee?->name ?? 'Non rattaché' }}</span>
</div>
<div class="flex items-center justify-between text-body-sm font-body-sm">
<span class="text-on-surface-variant">Catégorie :</span>
<span class="text-on-surface">{{ App\Models\Document::CATEGORIES[$doc->category] ?? $doc->category }}</span>
</div>
@if ($doc->expires_at)
<div class="flex items-center justify-between text-body-sm font-body-sm">
<span class="text-on-surface-variant">Expiration :</span>
<span class="text-on-surface font-numeric-md">{{ $doc->expires_at->format('d/m/Y') }}</span>
</div>
@endif
</div>
</div>
<div class="flex items-center justify-between pt-space-xs">
<span class="font-label-sm text-label-sm text-on-surface-variant flex items-center gap-1 truncate">
<input class="rounded accent-primary cursor-pointer shrink-0" form="zip-form" name="ids[]" type="checkbox" value="{{ $doc->id }}" title="Sélectionner pour le ZIP"/>
@if ($doc->expires_at)
<span class="material-symbols-outlined text-[14px]">calendar_today</span> Valide jusqu'au {{ $doc->expires_at->format('d/m/Y') }}
        @else
<span class="material-symbols-outlined text-[14px]">lock</span> Chiffré AES-256
        @endif
</span>
<div class="flex items-center gap-1 shrink-0">
<a aria-label="Aperçu" class="w-8 h-8 rounded-lg bg-surface-container-low hover:bg-surface-container text-on-surface-variant hover:text-primary transition-colors flex items-center justify-center" href="{{ route('admin.documents.download', $doc) }}" target="_blank" rel="noopener" title="Aperçu">
<span class="material-symbols-outlined text-body-md">visibility</span>
</a>
<a aria-label="Télécharger" class="w-8 h-8 rounded-lg bg-surface-container-low hover:bg-surface-container text-on-surface-variant hover:text-on-surface transition-colors flex items-center justify-center" download href="{{ route('admin.documents.download', $doc) }}" title="Télécharger">
<span class="material-symbols-outlined text-body-md">download</span>
</a>
<button
    aria-label="Modifier"
    class="w-8 h-8 rounded-lg bg-surface-container-low hover:bg-surface-container text-on-surface-variant hover:text-on-surface transition-colors flex items-center justify-center"
    type="button"
    title="Modifier les métadonnées"
    data-json="{{ json_encode(['id' => $doc->id, 'name' => $doc->name, 'employee_id' => $doc->employee_id, 'category' => $doc->category, 'status' => $doc->status, 'expires_at' => $doc->expires_at?->format('Y-m-d'), 'notes' => $doc->notes]) }}"
    onclick="openEditModal(this)"
>
<span class="material-symbols-outlined text-body-md">edit</span>
</button>
<form method="POST" action="{{ route('admin.documents.destroy', $doc) }}" onsubmit="return confirm('Supprimer ce document ? Le fichier sera effacé du disque.');">
@csrf
@method('DELETE')
<button aria-label="Supprimer" class="w-8 h-8 rounded-lg bg-surface-container-low hover:bg-error-container text-on-surface-variant hover:text-error transition-colors flex items-center justify-center" type="submit">
<span class="material-symbols-outlined text-body-md">delete</span>
</button>
</form>
</div>
</div>
</div>
@empty
<div class="col-span-full border border-dashed border-outline-variant rounded-xl p-space-xl text-center font-body-lg text-body-lg text-on-surface-variant">
    Aucun document. Téléversez le premier via la zone de dépôt ci-dessus.
</div>
@endforelse
</div>
<!-- Documents Detailed Table View (Initially Hidden) -->
<div class="hidden bg-surface-container-lowest rounded-xl shadow-sm overflow-hidden" id="documents-list">
<div class="overflow-x-auto">
<table class="w-full text-left border-collapse">
<thead>
<tr class="bg-surface-container-low h-10 border-b border-surface-container">
<th class="px-space-md font-label-md text-label-md text-on-surface-variant uppercase tracking-wider w-10">
<input class="rounded accent-primary cursor-pointer" type="checkbox" onclick="document.querySelectorAll('input[name=\'ids[]\']').forEach(c => c.checked = this.checked);"/>
</th>
<th class="px-space-md font-label-md text-label-md text-on-surface-variant uppercase tracking-wider">Document / Fichier</th>
<th class="px-space-md font-label-md text-label-md text-on-surface-variant uppercase tracking-wider">Collaborateur associé</th>
<th class="px-space-md font-label-md text-label-md text-on-surface-variant uppercase tracking-wider">Catégorie</th>
<th class="px-space-md font-label-md text-label-md text-on-surface-variant uppercase tracking-wider">Taille &amp; Date</th>
<th class="px-space-md font-label-md text-label-md text-on-surface-variant uppercase tracking-wider">Statut légal</th>
<th class="px-space-md font-label-md text-label-md text-on-surface-variant uppercase tracking-wider text-right">Actions</th>
</tr>
</thead>
<tbody class="divide-y divide-surface-container-low">
@forelse ($documents as $doc)
@php
    [$icon, $iconClasses] = $categoryIcons[$doc->category] ?? ['description', 'bg-surface-container text-on-surface-variant'];
    [$pillClasses, $dotClasses] = $statusPills[$doc->status] ?? ['bg-[#F1F5F9] text-[#475569]', 'bg-[#64748B]'];
    $sizeLabel = $doc->size >= 1048576
        ? number_format($doc->size / 1048576, 1, ',', ' ') . ' Mo'
        : max(1, (int) round($doc->size / 1024)) . ' Ko';
@endphp
<tr class="h-14 hover:bg-surface-container-low transition-colors">
<td class="px-space-md">
<input class="rounded accent-primary cursor-pointer" form="zip-form" name="ids[]" type="checkbox" value="{{ $doc->id }}"/>
</td>
<td class="px-space-md">
<div class="flex items-center gap-space-sm">
<span class="material-symbols-outlined {{ $icon === 'description' ? 'text-error' : 'text-primary' }} text-headline-sm">{{ $icon }}</span>
<a class="font-headline-sm text-headline-sm text-on-surface font-semibold hover:text-primary cursor-pointer" href="{{ route('admin.documents.download', $doc) }}" target="_blank" rel="noopener">{{ $doc->name }}</a>
</div>
</td>
<td class="px-space-md">
<span class="font-body-md text-body-md text-on-surface font-medium">{{ $doc->employee?->name ?? 'Non rattaché' }}</span>
@if ($doc->employee?->cin || $doc->employee?->cnss_number)
<span class="block font-label-sm text-label-sm text-on-surface-variant">{{ $doc->employee->cin ? 'CIN: ' . $doc->employee->cin : '' }}{{ $doc->employee->cin && $doc->employee->cnss_number ? ' • ' : '' }}{{ $doc->employee->cnss_number ? 'CNSS: ' . $doc->employee->cnss_number : '' }}</span>
@endif
</td>
<td class="px-space-md">
<span class="font-body-sm text-body-sm text-on-surface">{{ App\Models\Document::CATEGORIES[$doc->category] ?? $doc->category }}</span>
</td>
<td class="px-space-md">
<span class="font-numeric-md text-numeric-md text-on-surface">{{ $sizeLabel }}</span>
<span class="block font-label-sm text-label-sm text-on-surface-variant">{{ $doc->created_at->format('d/m/Y') }}</span>
</td>
<td class="px-space-md">
<span class="h-6 px-2.5 rounded-full font-label-sm text-label-sm font-semibold inline-flex items-center gap-1.5 {{ $pillClasses }}">
<span class="w-1.5 h-1.5 rounded-full {{ $dotClasses }}"></span>
<span>{{ App\Models\Document::STATUSES[$doc->status] ?? $doc->status }}</span>
</span>
</td>
<td class="px-space-md text-right">
<div class="inline-flex items-center gap-1">
<a class="w-8 h-8 rounded-lg hover:bg-surface-container text-on-surface-variant hover:text-primary flex items-center justify-center transition-colors" href="{{ route('admin.documents.download', $doc) }}" target="_blank" rel="noopener" title="Aperçu">
<span class="material-symbols-outlined text-body-md">visibility</span>
</a>
<a class="w-8 h-8 rounded-lg hover:bg-surface-container text-on-surface-variant hover:text-on-surface flex items-center justify-center transition-colors" download href="{{ route('admin.documents.download', $doc) }}" title="Télécharger">
<span class="material-symbols-outlined text-body-md">download</span>
</a>
<button class="w-8 h-8 rounded-lg hover:bg-surface-container text-on-surface-variant hover:text-on-surface flex items-center justify-center transition-colors" type="button" title="Modifier"
    data-json="{{ json_encode(['id' => $doc->id, 'name' => $doc->name, 'employee_id' => $doc->employee_id, 'category' => $doc->category, 'status' => $doc->status, 'expires_at' => $doc->expires_at?->format('Y-m-d'), 'notes' => $doc->notes]) }}"
    onclick="openEditModal(this)">
<span class="material-symbols-outlined text-body-md">edit</span>
</button>
<form method="POST" action="{{ route('admin.documents.destroy', $doc) }}" onsubmit="return confirm('Supprimer ce document ? Le fichier sera effacé du disque.');">
@csrf
@method('DELETE')
<button class="w-8 h-8 rounded-lg hover:bg-error-container text-on-surface-variant hover:text-error flex items-center justify-center transition-colors" type="submit" title="Supprimer">
<span class="material-symbols-outlined text-body-md">delete</span>
</button>
</form>
</div>
</td>
</tr>
@empty
<tr>
<td class="px-space-md py-space-lg text-on-surface-variant" colspan="7">Aucun document.</td>
</tr>
@endforelse
</tbody>
</table>
</div>
<!-- Table Pagination -->
<div class="p-space-md bg-surface-container-low/50 flex items-center justify-between">
<span class="font-body-sm text-body-sm text-on-surface-variant">Affichage de 1 à {{ $documents->count() }} sur {{ $stats['total'] }} documents certifiés</span>
<div class="flex items-center gap-space-xs">
<button class="px-3 py-1 rounded bg-surface-container-lowest text-on-surface-variant hover:text-on-surface text-label-md font-label-md shadow-xs disabled:opacity-50" disabled="">Précédent</button>
<button class="px-3 py-1 rounded bg-primary-container text-on-primary text-label-md font-label-md">1</button>
<button class="px-3 py-1 rounded bg-surface-container-lowest text-on-surface-variant hover:text-on-surface text-label-md font-label-md shadow-xs disabled:opacity-50" disabled="">Suivant</button>
</div>
</div>
</div>
<!-- Tunisian Regulatory & Compliance Banner Footer -->
<div class="mt-space-xl bg-surface-container-low rounded-xl p-space-md flex flex-col md:flex-row items-start md:items-center justify-between gap-space-md">
<div class="flex items-center gap-space-md">
<div class="w-10 h-10 rounded-lg bg-tertiary-container/20 text-tertiary flex items-center justify-center shrink-0">
<span class="material-symbols-outlined text-headline-sm">gavel</span>
</div>
<div>
<span class="font-headline-sm text-headline-sm text-on-surface font-semibold block">Conservation Légale &amp; Traçabilité Numérique</span>
<p class="font-body-sm text-body-sm text-on-surface-variant">
          Respect du Décret-loi n° 2020-17 et des préconisations INPDP pour la protection des données personnelles en Tunisie. Horodatage électronique certifié ANSI/TunTrust.
        </p>
</div>
</div>
<div class="flex items-center gap-space-sm shrink-0">
<button class="px-space-md py-1.5 rounded-lg bg-surface-container-lowest hover:bg-surface-container text-on-surface font-label-md text-label-md shadow-xs transition-colors" type="button">
        Journal d'Audit
      </button>
<a class="px-space-md py-1.5 rounded-lg bg-surface-container-lowest hover:bg-surface-container text-on-surface font-label-md text-label-md shadow-xs transition-colors" href="{{ route('admin.reports.export', ['dataset' => 'documents']) }}">
        Exporter Registre
      </a>
</div>
</div>
</div>

<!-- ==================== MODAL: Téléverser ==================== -->
<div class="hidden fixed inset-0 z-50 items-center justify-center p-gutter" id="upload-modal">
<div class="absolute inset-0 bg-black/40" onclick="closeModal('upload-modal')"></div>
<div class="relative bg-surface-container-lowest rounded-xl shadow-2xl w-full max-w-xl max-h-[90vh] overflow-y-auto">
    <div class="p-space-lg border-b border-surface-container flex items-center justify-between sticky top-0 bg-surface-container-lowest">
        <h2 class="font-headline-sm text-headline-sm text-on-surface font-bold">Téléverser des Documents</h2>
        <button class="w-8 h-8 rounded-lg hover:bg-surface-container flex items-center justify-center text-on-surface-variant" type="button" onclick="closeModal('upload-modal')">
            <span class="material-symbols-outlined text-body-lg">close</span>
        </button>
    </div>
    <form method="POST" action="{{ route('admin.documents.store') }}" enctype="multipart/form-data" class="p-space-lg flex flex-col gap-space-md">
        @csrf
        <div>
            <label class="font-label-md text-label-md text-on-surface">Fichiers * (PDF, PNG, JPG, JPEG, DOCX — 25 Mo max)</label>
            <input class="mt-1 w-full font-body-md text-body-md text-on-surface" id="upload-files-input" name="files[]" type="file" accept=".pdf,.png,.jpg,.jpeg,.doc,.docx" multiple required/>
        </div>
        <div>
            <label class="font-label-md text-label-md text-on-surface">Collaborateur associé</label>
            <select class="mt-1 w-full h-10 px-3 rounded-lg bg-surface-container-low focus:outline-none focus:bg-surface-container font-body-md text-body-md text-on-surface" name="employee_id">
                <option value="">— Non rattaché —</option>
                @foreach ($employees as $employee)
                <option value="{{ $employee->id }}">{{ $employee->name }}{{ $employee->cin ? ' (' . $employee->cin . ')' : '' }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="font-label-md text-label-md text-on-surface">Catégorie *</label>
            <select class="mt-1 w-full h-10 px-3 rounded-lg bg-surface-container-low focus:outline-none focus:bg-surface-container font-body-md text-body-md text-on-surface" name="category">
                @foreach (App\Models\Document::CATEGORIES as $key => $label)
                <option value="{{ $key }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-space-md">
            <div>
                <label class="font-label-md text-label-md text-on-surface">Statut *</label>
                <select class="mt-1 w-full h-10 px-3 rounded-lg bg-surface-container-low focus:outline-none focus:bg-surface-container font-body-md text-body-md text-on-surface" name="status">
                    @foreach (App\Models\Document::STATUSES as $key => $label)
                    <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="font-label-md text-label-md text-on-surface">Date d'expiration</label>
                <input class="mt-1 w-full h-10 px-3 rounded-lg bg-surface-container-low focus:outline-none focus:bg-surface-container font-body-md text-body-md text-on-surface" name="expires_at" type="date"/>
            </div>
        </div>
        <div>
            <label class="font-label-md text-label-md text-on-surface">Notes</label>
            <textarea class="mt-1 w-full px-3 py-2 rounded-lg bg-surface-container-low focus:outline-none focus:bg-surface-container font-body-md text-body-md text-on-surface" name="notes" rows="2"></textarea>
        </div>
        <div class="flex justify-end gap-space-sm pt-space-xs">
            <button class="h-10 px-space-lg rounded-xl bg-surface-container-low hover:bg-surface-container text-on-surface font-label-lg text-label-lg transition-colors" type="button" onclick="closeModal('upload-modal')">Annuler</button>
            <button class="h-10 px-space-lg rounded-xl bg-primary-container hover:bg-primary text-on-primary font-label-lg text-label-lg transition-colors" type="submit">Téléverser</button>
        </div>
    </form>
</div>
</div>

<!-- ==================== MODAL: Modifier métadonnées ==================== -->
<div class="hidden fixed inset-0 z-50 items-center justify-center p-gutter" id="edit-modal">
<div class="absolute inset-0 bg-black/40" onclick="closeModal('edit-modal')"></div>
<div class="relative bg-surface-container-lowest rounded-xl shadow-2xl w-full max-w-xl max-h-[90vh] overflow-y-auto">
    <div class="p-space-lg border-b border-surface-container flex items-center justify-between sticky top-0 bg-surface-container-lowest">
        <h2 class="font-headline-sm text-headline-sm text-on-surface font-bold">Modifier le Document</h2>
        <button class="w-8 h-8 rounded-lg hover:bg-surface-container flex items-center justify-center text-on-surface-variant" type="button" onclick="closeModal('edit-modal')">
            <span class="material-symbols-outlined text-body-lg">close</span>
        </button>
    </div>
    <form method="POST" action="{{ route('admin.documents.update', '__ID__') }}" id="edit-form" class="p-space-lg flex flex-col gap-space-md">
        @csrf
        <input type="hidden" name="_method" value="PUT"/>
        <div>
            <label class="font-label-md text-label-md text-on-surface">Libellé du document *</label>
            <input class="mt-1 w-full h-10 px-3 rounded-lg bg-surface-container-low focus:outline-none focus:bg-surface-container font-body-md text-body-md text-on-surface" name="name" required/>
        </div>
        <div>
            <label class="font-label-md text-label-md text-on-surface">Collaborateur associé</label>
            <select class="mt-1 w-full h-10 px-3 rounded-lg bg-surface-container-low focus:outline-none focus:bg-surface-container font-body-md text-body-md text-on-surface" name="employee_id">
                <option value="">— Non rattaché —</option>
                @foreach ($employees as $employee)
                <option value="{{ $employee->id }}">{{ $employee->name }}{{ $employee->cin ? ' (' . $employee->cin . ')' : '' }}</option>
                @endforeach
            </select>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-space-md">
            <div>
                <label class="font-label-md text-label-md text-on-surface">Catégorie *</label>
                <select class="mt-1 w-full h-10 px-3 rounded-lg bg-surface-container-low focus:outline-none focus:bg-surface-container font-body-md text-body-md text-on-surface" name="category">
                    @foreach (App\Models\Document::CATEGORIES as $key => $label)
                    <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="font-label-md text-label-md text-on-surface">Statut *</label>
                <select class="mt-1 w-full h-10 px-3 rounded-lg bg-surface-container-low focus:outline-none focus:bg-surface-container font-body-md text-body-md text-on-surface" name="status">
                    @foreach (App\Models\Document::STATUSES as $key => $label)
                    <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div>
            <label class="font-label-md text-label-md text-on-surface">Date d'expiration</label>
            <input class="mt-1 w-full h-10 px-3 rounded-lg bg-surface-container-low focus:outline-none focus:bg-surface-container font-body-md text-body-md text-on-surface" name="expires_at" type="date"/>
        </div>
        <div>
            <label class="font-label-md text-label-md text-on-surface">Notes</label>
            <textarea class="mt-1 w-full px-3 py-2 rounded-lg bg-surface-container-low focus:outline-none focus:bg-surface-container font-body-md text-body-md text-on-surface" name="notes" rows="2"></textarea>
        </div>
        <div class="flex justify-end gap-space-sm pt-space-xs">
            <button class="h-10 px-space-lg rounded-xl bg-surface-container-low hover:bg-surface-container text-on-surface font-label-lg text-label-lg transition-colors" type="button" onclick="closeModal('edit-modal')">Annuler</button>
            <button class="h-10 px-space-lg rounded-xl bg-primary-container hover:bg-primary text-on-primary font-label-lg text-label-lg transition-colors" type="submit">Enregistrer</button>
        </div>
    </form>
</div>
</div>

<script>
  function switchView(viewType) {
    const grid = document.getElementById('documents-grid');
    const list = document.getElementById('documents-list');
    const gridBtn = document.getElementById('view-grid-btn');
    const listBtn = document.getElementById('view-list-btn');

    if (viewType === 'grid') {
      grid.classList.remove('hidden');
      list.classList.add('hidden');
      gridBtn.className = 'px-3 py-1.5 rounded-md font-label-md text-label-md flex items-center gap-1.5 bg-surface-container-lowest text-primary shadow-xs transition-all';
      listBtn.className = 'px-3 py-1.5 rounded-md font-label-md text-label-md flex items-center gap-1.5 text-on-surface-variant hover:text-on-surface transition-all';
    } else {
      grid.classList.add('hidden');
      list.classList.remove('hidden');
      listBtn.className = 'px-3 py-1.5 rounded-md font-label-md text-label-md flex items-center gap-1.5 bg-surface-container-lowest text-primary shadow-xs transition-all';
      gridBtn.className = 'px-3 py-1.5 rounded-md font-label-md text-label-md flex items-center gap-1.5 text-on-surface-variant hover:text-on-surface transition-all';
    }
  }

  function openModal(id) {
    document.getElementById(id).classList.remove('hidden');
    document.getElementById(id).classList.add('flex');
  }
  function closeModal(id) {
    document.getElementById(id).classList.add('hidden');
    document.getElementById(id).classList.remove('flex');
  }

  function openEditModal(button) {
    var form = document.getElementById('edit-form');
    var data = JSON.parse(button.dataset.json);
    Object.keys(data).forEach(function (key) {
      var field = form.querySelector('[name="' + key + '"]');
      if (field) field.value = data[key] === null ? '' : data[key];
    });
    form.action = "{{ route('admin.documents.update', '__ID__') }}".replace('__ID__', data.id);
    openModal('edit-modal');
  }

  // Drop zone : déposer des fichiers ouvre la modale d'upload
  const dropZone = document.getElementById('drop-zone');
  const uploadInput = document.getElementById('upload-files-input');
  if (dropZone) {
    ['dragenter', 'dragover'].forEach(eventName => {
      dropZone.addEventListener(eventName, (e) => {
        e.preventDefault();
        dropZone.classList.add('bg-primary/5', 'border', 'border-dashed', 'border-primary');
      }, false);
    });
    ['dragleave', 'drop'].forEach(eventName => {
      dropZone.addEventListener(eventName, (e) => {
        e.preventDefault();
        dropZone.classList.remove('bg-primary/5', 'border', 'border-dashed', 'border-primary');
      }, false);
    });
    dropZone.addEventListener('drop', (e) => {
      if (e.dataTransfer.files.length) {
        const dt = new DataTransfer();
        Array.from(e.dataTransfer.files).forEach(f => dt.items.add(f));
        uploadInput.files = dt.files;
        openModal('upload-modal');
      }
    }, false);
  }
  if (uploadInput) {
    uploadInput.addEventListener('change', function () {
      if (this.files.length) openModal('upload-modal');
    });
  }
</script>
</x-admin-shell>
