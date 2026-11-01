@props([
    'icon' => 'inbox',
    'title',
    'description' => null,
])

<div class="flex flex-col items-center justify-center rounded-xl border border-dashed border-outline-variant/60 bg-surface-container-lowest px-6 py-12 text-center">
    <span class="flex h-12 w-12 items-center justify-center rounded-full bg-surface-container text-on-surface-variant" aria-hidden="true">
        <span class="material-symbols-outlined text-headline-md">{{ $icon }}</span>
    </span>
    <h2 class="mt-4 font-headline-sm text-headline-sm font-semibold text-on-surface">{{ $title }}</h2>
    @if ($description)
        <p class="mt-1 max-w-md font-body-sm text-body-sm text-on-surface-variant">{{ $description }}</p>
    @endif
    @if (trim((string) $slot) !== '')
        <div class="mt-4">{{ $slot }}</div>
    @endif
</div>
