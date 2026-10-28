@props([
    'value',
    'decimals' => 3,
    'signed' => false,
    'suffix' => 'TND',
])

<span {{ $attributes->merge(['class' => 'font-numeric-md tabular-nums']) }}>
    {{ $value->format($decimals, $signed) }}@if ($suffix) {{ $suffix }}@endif
</span>
