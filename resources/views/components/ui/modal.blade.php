@props([
    'id',
    'title',
    'description' => null,
])

@once
    <style>
        dialog::backdrop {
            background-color: rgba(19, 27, 46, 0.45);
        }
    </style>
@endonce

<dialog
    id="{{ $id }}"
    aria-labelledby="{{ $id }}-title"
    class="m-auto max-h-[calc(100vh-2rem)] w-[calc(100%-2rem)] max-w-lg overflow-y-auto rounded-2xl border-0 bg-surface-container-lowest p-0 text-on-surface shadow-2xl"
>
    <div class="flex items-start justify-between gap-4 border-b border-surface-container px-6 py-5">
        <div class="min-w-0">
            <h2 class="font-headline-md text-headline-md font-bold text-on-surface" id="{{ $id }}-title">{{ $title }}</h2>
            @if ($description)
                <p class="mt-1 font-body-sm text-body-sm text-on-surface-variant">{{ $description }}</p>
            @endif
        </div>
        <button
            class="shrink-0 rounded-lg p-1 text-on-surface-variant transition-colors hover:bg-surface-container hover:text-on-surface focus:outline-none focus:ring-2 focus:ring-primary"
            type="button"
            onclick="document.getElementById('{{ $id }}').close()"
            aria-label="Fermer la fenêtre"
        >
            <span class="material-symbols-outlined text-body-lg">close</span>
        </button>
    </div>

    <div class="px-6 py-5">
        {{ $slot }}
    </div>
</dialog>
