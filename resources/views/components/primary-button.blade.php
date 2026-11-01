<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-primary-container border border-transparent rounded-md font-semibold text-xs text-white dark:text-on-primary uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-primary-fill focus:bg-gray-700 dark:focus:bg-primary-fill active:bg-gray-900 dark:active:bg-primary-fill focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-surface-container-lowest transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
