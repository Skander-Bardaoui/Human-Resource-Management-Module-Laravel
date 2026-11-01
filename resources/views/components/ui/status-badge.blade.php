@props([
    'tone' => 'neutral',
    'label',
])

@php
    $tones = [
        'success' => 'bg-tertiary/10 text-tertiary',
        'error' => 'bg-error-container text-on-error-container',
        'warning' => 'bg-secondary-container text-on-secondary-container',
        'neutral' => 'bg-surface-container text-on-surface-variant',
        'primary' => 'bg-primary-container/10 text-primary',
    ];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 font-label-sm text-label-sm font-semibold '.($tones[$tone] ?? $tones['neutral'])]) }}>
    <span class="h-1.5 w-1.5 rounded-full bg-current" aria-hidden="true"></span>
    {{ $label }}
</span>
