@php
    $accentMap = ['received' => 'bg-primary', 'offer' => 'bg-tertiary'];
    $accent = $accentMap[$candidate->stage] ?? null;
    $interview = $candidate->interviews->sortBy('scheduled_at')->first();
    $initials = strtoupper(implode('', array_map(fn($p) => substr($p, 0, 1), array_slice(preg_split('/\s+/', trim($candidate->name)), 0, 2))));
    $skillTags = array_values(array_filter(array_map('trim', explode(',', $candidate->skills ?? ''))));
    $isNew = $candidate->created_at && $candidate->created_at->diffInHours(now()) < 24;
    $frMonths = ['janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre'];
@endphp

<div
    class="p-space-md rounded-xl bg-surface-container-lowest shadow-sm hover:shadow-md transition-all cursor-grab active:cursor-grabbing group flex flex-col gap-space-sm relative overflow-hidden{{ $candidate->stage === 'hired' ? ' bg-gradient-to-b from-surface-container-lowest to-surface-container-low/40' : '' }}"
    draggable="true"
    data-candidate-id="{{ $candidate->id }}"
>
    @if ($accent)
    <div class="absolute top-0 left-0 bottom-0 w-1 {{ $accent }}"></div>
    @endif
    <div class="flex items-start justify-between gap-space-sm">
        <div class="flex items-center gap-space-sm min-w-0">
            @if ($candidate->avatar_url)
            <img class="w-10 h-10 rounded-full object-cover shrink-0 ring-2 {{ $candidate->stage === 'hired' ? 'ring-tertiary' : 'ring-surface-container' }}" src="{{ $candidate->avatar_url }}" alt="{{ $candidate->name }}"/>
            @else
            <div class="w-10 h-10 rounded-full bg-primary-container text-on-primary flex items-center justify-center shrink-0 font-label-lg text-label-lg font-bold ring-2 {{ $candidate->stage === 'hired' ? 'ring-tertiary' : 'ring-surface-container' }}">{{ $initials ?: '?' }}</div>
            @endif
            <div class="flex flex-col min-w-0">
                <span class="font-label-lg text-label-lg text-on-surface font-bold truncate group-hover:text-primary transition-colors">{{ $candidate->name }}</span>
                <span class="font-body-sm text-body-sm text-on-surface-variant truncate">{{ $candidate->position }}</span>
            </div>
        </div>
        @if ($candidate->stage === 'hired')
        <span class="w-6 h-6 rounded-full bg-tertiary text-on-tertiary flex items-center justify-center shrink-0">
            <span class="material-symbols-outlined text-body-sm" style="font-variation-settings: 'FILL' 1;">done</span>
        </span>
        @elseif ($isNew)
        <span class="px-2 py-0.5 rounded-full bg-secondary-container text-on-secondary-fixed font-label-sm text-label-sm font-bold shrink-0">Nouveau</span>
        @elseif ($candidate->match_percentage !== null && $candidate->match_percentage >= 90)
        <span class="px-2 py-0.5 rounded-full bg-tertiary text-on-tertiary font-label-sm text-label-sm font-bold shrink-0">{{ $candidate->match_percentage }}% Top</span>
        @elseif ($candidate->match_percentage !== null)
        <span class="px-2 py-0.5 rounded-full bg-secondary-container text-on-secondary-fixed font-label-sm text-label-sm font-bold shrink-0">{{ $candidate->match_percentage }}%</span>
        @endif
    </div>
    @if ($candidate->match_percentage !== null || $candidate->experience_years !== null || $candidate->contract_type_target)
    <div class="flex items-center justify-between py-1 px-2 rounded-lg bg-surface-container-low font-label-sm text-label-sm">
        <div class="flex items-center gap-1 text-tertiary font-bold">
            @if ($candidate->match_percentage !== null)
            <span class="material-symbols-outlined text-body-sm" style="font-variation-settings: 'FILL' 1;">bolt</span>
            <span>Match {{ $candidate->match_percentage }}%</span>
            @endif
        </div>
        @if ($candidate->experience_years !== null)
        <span class="text-on-surface-variant flex items-center gap-1">
            <span class="material-symbols-outlined text-body-sm">history_edu</span> Exp. {{ $candidate->experience_years }} an{{ $candidate->experience_years > 1 ? 's' : '' }}
        </span>
        @elseif ($candidate->contract_type_target)
        <span class="text-on-surface-variant font-medium">Contrat {{ $candidate->contract_type_target }} visé</span>
        @endif
    </div>
    @endif
    @if (count($skillTags))
    <div class="flex flex-wrap gap-1">
        @foreach ($skillTags as $tag)
        <span class="px-2 py-0.5 rounded bg-surface-container text-on-surface-variant font-label-sm text-label-sm">{{ $tag }}</span>
        @endforeach
    </div>
    @endif
    @if ($candidate->stage === 'hired' && $candidate->hired_at)
    <div class="flex flex-col gap-1 p-2 rounded-lg bg-surface-container font-label-sm text-label-sm">
        <div class="flex items-center justify-between text-tertiary font-bold">
            <span>Contrat {{ $candidate->contract_type_target ?: 'CDI' }} Signé</span>
            <span class="material-symbols-outlined text-body-sm">verified_user</span>
        </div>
        <div class="flex items-center gap-1 text-on-surface-variant">
            <span class="material-symbols-outlined text-body-sm text-primary">calendar_today</span>
            <span>Intégration : <strong>{{ $candidate->hired_at->format('d') }} {{ $frMonths[$candidate->hired_at->format('n') - 1] }} {{ $candidate->hired_at->format('Y') }}</strong></span>
        </div>
    </div>
    @endif
    @if ($interview)
    <div class="flex items-center gap-2 p-2 rounded-lg bg-surface-container-high text-on-surface font-label-sm text-label-sm">
        <span class="material-symbols-outlined text-primary text-body-md animate-pulse">event</span>
        <div class="flex flex-col min-w-0 flex-1">
            <span class="font-semibold text-primary truncate">{{ $interview->scheduled_at->format('d') }} {{ $frMonths[$interview->scheduled_at->format('n') - 1] }} • {{ $interview->scheduled_at->format('H:i') }}</span>
            <span class="text-on-surface-variant text-label-sm">{{ $interview->interviewer }} ({{ App\Models\Interview::TYPES[$interview->type] ?? $interview->type }})</span>
        </div>
        <form method="POST" action="{{ route('admin.recruitment.interviews.destroy', $interview) }}" onsubmit="return confirm('Supprimer cet entretien ?');">
            @csrf
            @method('DELETE')
            <button class="p-1 text-on-surface-variant hover:text-error transition-colors" type="submit" title="Supprimer l'entretien">
                <span class="material-symbols-outlined text-body-sm">close</span>
            </button>
        </form>
    </div>
    @endif
    @if ($candidate->salary_claim !== null)
    <div class="flex items-center justify-between text-label-sm text-on-surface-variant">
        <span>Prétention salariale:</span>
        <span class="font-numeric-md text-numeric-md text-on-surface font-bold">{{ number_format($candidate->salary_claim, 0, ',', ' ') }} TND Net</span>
    </div>
    @endif
    @if ($candidate->availability)
    <div class="flex items-center gap-1.5 text-tertiary font-label-sm text-label-sm font-semibold">
        <span class="material-symbols-outlined text-body-sm">check_circle</span>
        <span class="truncate">{{ $candidate->availability }}</span>
    </div>
    @endif
    @if ($candidate->notes)
    <p class="font-body-sm text-body-sm text-on-surface-variant line-clamp-2">{{ $candidate->notes }}</p>
    @endif
    @if ($candidate->stage === 'hired')
    <div class="flex items-center justify-between pt-space-xs border-t border-surface-container">
        <span class="font-label-sm text-label-sm text-on-surface-variant">Dossier CNSS généré</span>
        <div class="flex items-center gap-1">
            @if ($candidate->onboarded_at)
            <span class="px-2 py-1 rounded bg-tertiary-fixed/40 text-tertiary font-label-sm text-label-sm font-semibold flex items-center gap-1">
                <span class="material-symbols-outlined text-body-sm" style="font-variation-settings: 'FILL' 1;">check_circle</span>
                <span>Onboardé</span>
            </span>
            @else
            <a class="px-2 py-1 rounded bg-primary-container text-on-primary font-label-sm text-label-sm font-semibold hover:bg-primary-fill transition-colors flex items-center gap-1" href="{{ route('admin.employees', ['onboard' => $candidate->id]) }}" title="Créer la fiche collaborateur">
                <span>Onboarding</span>
                <span class="material-symbols-outlined text-body-sm">arrow_forward</span>
            </a>
            @endif
            <button class="p-1 text-on-surface-variant hover:text-primary transition-colors" type="button" title="Modifier" data-json="{{ json_encode($candidate->only(['id', 'name', 'email', 'phone', 'position', 'job_offer_id', 'avatar_url', 'match_percentage', 'experience_years', 'skills', 'source', 'salary_claim', 'availability', 'contract_type_target', 'stage', 'notes'])) }}" onclick="openCandidateModal(this)">
                <span class="material-symbols-outlined text-body-md">edit</span>
            </button>
            <form method="POST" action="{{ route('admin.recruitment.candidates.destroy', $candidate) }}" onsubmit="return confirm('Supprimer ce candidat ?');">
                @csrf
                @method('DELETE')
                <button class="p-1 text-on-surface-variant hover:text-error transition-colors" type="submit" title="Supprimer">
                    <span class="material-symbols-outlined text-body-md">delete</span>
                </button>
            </form>
        </div>
    </div>
    @else
    <div class="flex items-center justify-between pt-space-xs border-t border-surface-container font-label-sm text-label-sm text-on-surface-variant">
        <span class="flex items-center gap-1 truncate">
            <span class="material-symbols-outlined text-body-sm text-primary">link</span>
            <span class="truncate">{{ $candidate->source ?? '—' }}</span>
        </span>
        <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
            <button class="p-1 hover:text-primary transition-colors" type="button" title="Planifier un entretien" data-candidate-id="{{ $candidate->id }}" data-candidate-name="{{ $candidate->name }}" onclick="openInterviewModal(this)">
                <span class="material-symbols-outlined text-body-md">event</span>
            </button>
            <button class="p-1 hover:text-primary transition-colors" type="button" title="Modifier" data-json="{{ json_encode($candidate->only(['id', 'name', 'email', 'phone', 'position', 'job_offer_id', 'avatar_url', 'match_percentage', 'experience_years', 'skills', 'source', 'salary_claim', 'availability', 'contract_type_target', 'stage', 'notes'])) }}" onclick="openCandidateModal(this)">
                <span class="material-symbols-outlined text-body-md">edit</span>
            </button>
            <form method="POST" action="{{ route('admin.recruitment.candidates.destroy', $candidate) }}" onsubmit="return confirm('Supprimer ce candidat ?');">
                @csrf
                @method('DELETE')
                <button class="p-1 hover:text-error transition-colors" type="submit" title="Supprimer">
                    <span class="material-symbols-outlined text-body-md">delete</span>
                </button>
            </form>
        </div>
    </div>
    @endif
</div>
