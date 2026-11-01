@props([
    'label',
    'value',
    'icon' => 'analytics',
    'tone' => 'primary',
    'hint' => null,
])

@php
    $tones = [
        'primary' => 'bg-primary-container/10 text-primary',
        'secondary' => 'bg-secondary-container/40 text-secondary',
        'tertiary' => 'bg-tertiary/10 text-tertiary',
        'neutral' => 'bg-surface-container text-on-surface-variant',
        'error' => 'bg-error-container text-on-error-container',
    ];
    $iconClasses = $tones[$tone] ?? $tones['primary'];
@endphp

<div {{ $attributes->merge(['class' => 'rounded-xl bg-surface-container-lowest p-5 shadow-sm']) }}>
    <div class="flex items-start justify-between gap-3">
        <p class="font-label-sm text-label-sm uppercase tracking-wider text-on-surface-variant">{{ $label }}</p>
        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg {{ $iconClasses }}" aria-hidden="true">
            <span class="material-symbols-outlined text-headline-sm">{{ $icon }}</span>
        </span>
    </div>
    <p class="mt-3 font-display-lg text-display-lg font-bold tracking-tight text-on-surface">{{ $value }}</p>
    @if ($hint)
        <p class="mt-2 font-label-sm text-label-sm text-on-surface-variant">{{ $hint }}</p>
    @endif
</div>
