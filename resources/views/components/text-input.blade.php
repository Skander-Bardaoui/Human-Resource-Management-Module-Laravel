@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'border-gray-300 dark:border-outline-variant dark:bg-surface-container-low dark:text-on-surface focus:border-indigo-500 dark:focus:border-primary focus:ring-indigo-500 dark:focus:ring-primary rounded-md shadow-sm']) !!}>
