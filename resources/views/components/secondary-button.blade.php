<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center px-4 py-2 bg-white dark:bg-surface-container-high border border-gray-300 dark:border-outline-variant rounded-md font-semibold text-xs text-gray-700 dark:text-on-surface uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-surface-container-highest focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-surface-container-lowest disabled:opacity-25 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
