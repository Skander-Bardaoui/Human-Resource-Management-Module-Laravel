@props([
    'icon' => 'inbox',
    'title' => 'Aucun élément',
    'text' => null,
])

<div class="flex flex-col items-center justify-center gap-space-xs py-space-xl px-space-lg text-center rounded-xl bg-surface-container-low">
    <div class="w-12 h-12 rounded-xl bg-surface-container-high text-on-surface-variant flex items-center justify-center">
        <span class="material-symbols-outlined text-[26px]">{{ $icon }}</span>
    </div>
    <span class="font-headline-sm text-headline-sm text-on-surface">{{ $title }}</span>
    @if ($text)
        <span class="font-body-sm text-body-sm text-on-surface-variant max-w-md">{{ $text }}</span>
    @endif
</div>
