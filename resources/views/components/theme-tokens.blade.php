@php
    $section = $section ?? 'all';

    $light = [
        'inverse-primary' => '195 192 255',
        'surface-dim' => '210 217 244',
        'tertiary-container' => '0 110 75',
        'surface-container-high' => '226 231 255',
        'on-tertiary-fixed-variant' => '0 82 54',
        'background' => '250 248 255',
        'surface-container' => '234 237 255',
        'secondary-container' => '208 225 251',
        'on-primary-fixed-variant' => '51 35 204',
        'on-tertiary' => '255 255 255',
        'tertiary' => '0 83 56',
        'on-tertiary-container' => '103 244 183',
        'on-surface-variant' => '70 69 85',
        'primary-fixed' => '226 223 255',
        'inverse-surface' => '40 48 68',
        'surface-bright' => '250 248 255',
        'primary-fixed-dim' => '195 192 255',
        'tertiary-fixed' => '111 251 190',
        'surface-container-lowest' => '255 255 255',
        'primary' => '53 37 205',
        'tertiary-fixed-dim' => '78 222 163',
        'on-secondary-container' => '84 100 122',
        'on-secondary' => '255 255 255',
        'surface-container-highest' => '218 226 253',
        'outline-variant' => '199 196 216',
        'secondary-fixed-dim' => '183 200 225',
        'error' => '186 26 26',
        'on-secondary-fixed' => '11 28 48',
        'inverse-on-surface' => '238 240 255',
        'on-primary-fixed' => '15 0 105',
        'outline' => '119 117 135',
        'surface' => '250 248 255',
        'secondary-fixed' => '211 228 254',
        'on-tertiary-fixed' => '0 33 19',
        'on-surface' => '19 27 46',
        'on-primary-container' => '218 215 255',
        'on-error' => '255 255 255',
        'on-error-container' => '147 0 10',
        'secondary' => '80 95 118',
        'surface-tint' => '77 68 227',
        'primary-container' => '79 70 229',
        'error-container' => '255 218 214',
        'on-secondary-fixed-variant' => '56 72 93',
        'on-primary' => '255 255 255',
        'on-background' => '19 27 46',
        'surface-variant' => '218 226 253',
        'surface-container-low' => '242 243 255',
        'primary-fill' => '53 37 205',
    ];

    $dark = [
        'inverse-primary' => '79 70 229',
        'surface-dim' => '10 13 25',
        'tertiary-container' => '78 222 163',
        'surface-container-high' => '42 50 82',
        'on-tertiary-fixed-variant' => '78 222 163',
        'background' => '14 18 34',
        'surface-container' => '33 40 66',
        'secondary-container' => '42 56 80',
        'on-primary-fixed-variant' => '195 192 255',
        'on-tertiary' => '0 33 19',
        'tertiary' => '78 222 163',
        'on-tertiary-container' => '103 244 183',
        'on-surface-variant' => '163 168 195',
        'primary-fixed' => '43 38 112',
        'inverse-surface' => '223 227 248',
        'surface-bright' => '30 36 60',
        'primary-fixed-dim' => '195 192 255',
        'tertiary-fixed' => '11 61 44',
        'surface-container-lowest' => '21 26 46',
        'primary' => '167 161 255',
        'tertiary-fixed-dim' => '78 222 163',
        'on-secondary-container' => '183 200 225',
        'on-secondary' => '11 28 48',
        'surface-container-highest' => '52 60 94',
        'outline-variant' => '58 64 96',
        'secondary-fixed-dim' => '183 200 225',
        'error' => '255 180 171',
        'on-secondary-fixed' => '211 228 254',
        'inverse-on-surface' => '26 32 54',
        'on-primary-fixed' => '226 223 255',
        'outline' => '131 135 163',
        'surface' => '14 18 34',
        'secondary-fixed' => '42 56 80',
        'on-tertiary-fixed' => '111 251 190',
        'on-surface' => '225 229 247',
        'on-primary-container' => '226 223 255',
        'on-error' => '105 0 5',
        'on-error-container' => '255 218 214',
        'secondary' => '183 200 225',
        'surface-tint' => '167 161 255',
        'primary-container' => '79 70 229',
        'error-container' => '92 17 24',
        'on-secondary-fixed-variant' => '211 228 254',
        'on-primary' => '255 255 255',
        'on-background' => '225 229 247',
        'surface-variant' => '52 60 94',
        'surface-container-low' => '26 32 56',
        'primary-fill' => '91 82 232',
    ];

    $colorMap = [];
    foreach (array_keys($light) as $name) {
        $colorMap[$name] = "rgb(var(--c-{$name}) / <alpha-value>)";
    }

    $tailwindConfig = [
        'darkMode' => 'class',
        'theme' => [
            'extend' => [
                'colors' => $colorMap,
                'borderRadius' => [
                    'DEFAULT' => '0.125rem',
                    'lg' => '0.25rem',
                    'xl' => '0.5rem',
                    'full' => '0.75rem',
                ],
                'spacing' => [
                    'space-md' => '0.75rem',
                    'space-xl' => '1.5rem',
                    'gutter' => '1.25rem',
                    'margin' => '2rem',
                    'space-lg' => '1rem',
                    'space-xs' => '0.25rem',
                    'space-sm' => '0.5rem',
                    'margin-mobile' => '1rem',
                    'gutter-mobile' => '0.75rem',
                ],
                'fontFamily' => [
                    'label-md' => ['Inter'],
                    'display-lg-mobile' => ['Inter'],
                    'headline-lg' => ['Inter'],
                    'numeric-md' => ['Inter'],
                    'display-lg' => ['Inter'],
                    'body-md' => ['Inter'],
                    'body-lg' => ['Inter'],
                    'body-sm' => ['Inter'],
                    'headline-md' => ['Inter'],
                    'label-lg' => ['Inter'],
                    'label-sm' => ['Inter'],
                    'headline-sm' => ['Inter'],
                ],
                'fontSize' => [
                    'label-md' => ['12px', ['lineHeight' => '16px', 'letterSpacing' => '0.01em', 'fontWeight' => '500']],
                    'display-lg-mobile' => ['26px', ['lineHeight' => '34px', 'letterSpacing' => '-0.02em', 'fontWeight' => '700']],
                    'headline-lg' => ['24px', ['lineHeight' => '32px', 'letterSpacing' => '-0.02em', 'fontWeight' => '600']],
                    'numeric-md' => ['14px', ['lineHeight' => '20px', 'fontWeight' => '500']],
                    'display-lg' => ['32px', ['lineHeight' => '40px', 'letterSpacing' => '-0.025em', 'fontWeight' => '700']],
                    'body-md' => ['14px', ['lineHeight' => '20px', 'fontWeight' => '400']],
                    'body-lg' => ['16px', ['lineHeight' => '24px', 'fontWeight' => '400']],
                    'body-sm' => ['13px', ['lineHeight' => '18px', 'fontWeight' => '400']],
                    'headline-md' => ['20px', ['lineHeight' => '28px', 'letterSpacing' => '-0.015em', 'fontWeight' => '600']],
                    'label-lg' => ['14px', ['lineHeight' => '20px', 'fontWeight' => '500']],
                    'label-sm' => ['11px', ['lineHeight' => '14px', 'letterSpacing' => '0.04em', 'fontWeight' => '600']],
                    'headline-sm' => ['16px', ['lineHeight' => '24px', 'letterSpacing' => '-0.01em', 'fontWeight' => '600']],
                ],
            ],
        ],
    ];
@endphp

@if ($section === 'bootstrap' || $section === 'all')
<script>
    (function () {
        var STORAGE_KEY = 'hrm-theme';
        var root = document.documentElement;
        var media = window.matchMedia('(prefers-color-scheme: dark)');
        var ORDER = ['light', 'dark', 'system'];
        var LABELS = { light: 'Clair', dark: 'Sombre', system: 'Système' };
        var ICONS = { light: 'light_mode', dark: 'dark_mode', system: 'contrast' };

        function storedMode() {
            try {
                var value = localStorage.getItem(STORAGE_KEY);
                return ORDER.indexOf(value) !== -1 ? value : 'system';
            } catch (e) {
                return 'system';
            }
        }

        function effectiveDark(mode) {
            return mode === 'dark' || (mode === 'system' && media.matches);
        }

        function syncToggles() {
            var mode = root.dataset.theme || 'system';
            var label = 'Changer le thème (actuel : ' + LABELS[mode] + ')';
            document.querySelectorAll('[data-theme-toggle]').forEach(function (button) {
                var icon = button.querySelector('[data-theme-icon]');
                if (icon) icon.textContent = ICONS[mode];
                button.setAttribute('aria-label', label);
                button.setAttribute('title', label);
                button.setAttribute('aria-pressed', effectiveDark(mode) ? 'true' : 'false');
            });
        }

        function apply(mode, persist) {
            if (ORDER.indexOf(mode) === -1) mode = 'system';
            var dark = effectiveDark(mode);
            root.classList.toggle('dark', dark);
            root.dataset.theme = mode;
            root.style.colorScheme = dark ? 'dark' : 'light';
            if (persist) {
                try {
                    localStorage.setItem(STORAGE_KEY, mode);
                } catch (e) {}
            }
            syncToggles();
        }

        function cycle() {
            var mode = root.dataset.theme || 'system';
            var next = ORDER[(ORDER.indexOf(mode) + 1) % ORDER.length];
            root.classList.add('theme-transition');
            apply(next, true);
            window.setTimeout(function () {
                root.classList.remove('theme-transition');
            }, 260);
        }

        apply(storedMode(), false);

        document.addEventListener('click', function (event) {
            var trigger = event.target.closest ? event.target.closest('[data-theme-toggle]') : null;
            if (!trigger) return;
            event.preventDefault();
            cycle();
        });

        var onSystemChange = function () {
            if ((root.dataset.theme || 'system') === 'system') apply('system', false);
        };
        if (media.addEventListener) {
            media.addEventListener('change', onSystemChange);
        } else if (media.addListener) {
            media.addListener(onSystemChange);
        }

        document.addEventListener('DOMContentLoaded', function () {
            syncToggles();
            window.requestAnimationFrame(function () {
                window.requestAnimationFrame(function () {
                    root.classList.add('theme-ready');
                });
            });
        });
    })();
</script>
@endif

@if ($section === 'styles' || $section === 'config' || $section === 'colors' || $section === 'all')
<style>
    :root {
        color-scheme: light;
        --c-inverse-primary: {{ $light['inverse-primary'] }};
        --c-surface-dim: {{ $light['surface-dim'] }};
        --c-tertiary-container: {{ $light['tertiary-container'] }};
        --c-surface-container-high: {{ $light['surface-container-high'] }};
        --c-on-tertiary-fixed-variant: {{ $light['on-tertiary-fixed-variant'] }};
        --c-background: {{ $light['background'] }};
        --c-surface-container: {{ $light['surface-container'] }};
        --c-secondary-container: {{ $light['secondary-container'] }};
        --c-on-primary-fixed-variant: {{ $light['on-primary-fixed-variant'] }};
        --c-on-tertiary: {{ $light['on-tertiary'] }};
        --c-tertiary: {{ $light['tertiary'] }};
        --c-on-tertiary-container: {{ $light['on-tertiary-container'] }};
        --c-on-surface-variant: {{ $light['on-surface-variant'] }};
        --c-primary-fixed: {{ $light['primary-fixed'] }};
        --c-inverse-surface: {{ $light['inverse-surface'] }};
        --c-surface-bright: {{ $light['surface-bright'] }};
        --c-primary-fixed-dim: {{ $light['primary-fixed-dim'] }};
        --c-tertiary-fixed: {{ $light['tertiary-fixed'] }};
        --c-surface-container-lowest: {{ $light['surface-container-lowest'] }};
        --c-primary: {{ $light['primary'] }};
        --c-tertiary-fixed-dim: {{ $light['tertiary-fixed-dim'] }};
        --c-on-secondary-container: {{ $light['on-secondary-container'] }};
        --c-on-secondary: {{ $light['on-secondary'] }};
        --c-surface-container-highest: {{ $light['surface-container-highest'] }};
        --c-outline-variant: {{ $light['outline-variant'] }};
        --c-secondary-fixed-dim: {{ $light['secondary-fixed-dim'] }};
        --c-error: {{ $light['error'] }};
        --c-on-secondary-fixed: {{ $light['on-secondary-fixed'] }};
        --c-inverse-on-surface: {{ $light['inverse-on-surface'] }};
        --c-on-primary-fixed: {{ $light['on-primary-fixed'] }};
        --c-outline: {{ $light['outline'] }};
        --c-surface: {{ $light['surface'] }};
        --c-secondary-fixed: {{ $light['secondary-fixed'] }};
        --c-on-tertiary-fixed: {{ $light['on-tertiary-fixed'] }};
        --c-on-surface: {{ $light['on-surface'] }};
        --c-on-primary-container: {{ $light['on-primary-container'] }};
        --c-on-error: {{ $light['on-error'] }};
        --c-on-error-container: {{ $light['on-error-container'] }};
        --c-secondary: {{ $light['secondary'] }};
        --c-surface-tint: {{ $light['surface-tint'] }};
        --c-primary-container: {{ $light['primary-container'] }};
        --c-error-container: {{ $light['error-container'] }};
        --c-on-secondary-fixed-variant: {{ $light['on-secondary-fixed-variant'] }};
        --c-on-primary: {{ $light['on-primary'] }};
        --c-on-background: {{ $light['on-background'] }};
        --c-surface-variant: {{ $light['surface-variant'] }};
        --c-surface-container-low: {{ $light['surface-container-low'] }};
        --c-primary-fill: {{ $light['primary-fill'] }};
    }

    .dark {
        color-scheme: dark;
        --c-inverse-primary: {{ $dark['inverse-primary'] }};
        --c-surface-dim: {{ $dark['surface-dim'] }};
        --c-tertiary-container: {{ $dark['tertiary-container'] }};
        --c-surface-container-high: {{ $dark['surface-container-high'] }};
        --c-on-tertiary-fixed-variant: {{ $dark['on-tertiary-fixed-variant'] }};
        --c-background: {{ $dark['background'] }};
        --c-surface-container: {{ $dark['surface-container'] }};
        --c-secondary-container: {{ $dark['secondary-container'] }};
        --c-on-primary-fixed-variant: {{ $dark['on-primary-fixed-variant'] }};
        --c-on-tertiary: {{ $dark['on-tertiary'] }};
        --c-tertiary: {{ $dark['tertiary'] }};
        --c-on-tertiary-container: {{ $dark['on-tertiary-container'] }};
        --c-on-surface-variant: {{ $dark['on-surface-variant'] }};
        --c-primary-fixed: {{ $dark['primary-fixed'] }};
        --c-inverse-surface: {{ $dark['inverse-surface'] }};
        --c-surface-bright: {{ $dark['surface-bright'] }};
        --c-primary-fixed-dim: {{ $dark['primary-fixed-dim'] }};
        --c-tertiary-fixed: {{ $dark['tertiary-fixed'] }};
        --c-surface-container-lowest: {{ $dark['surface-container-lowest'] }};
        --c-primary: {{ $dark['primary'] }};
        --c-tertiary-fixed-dim: {{ $dark['tertiary-fixed-dim'] }};
        --c-on-secondary-container: {{ $dark['on-secondary-container'] }};
        --c-on-secondary: {{ $dark['on-secondary'] }};
        --c-surface-container-highest: {{ $dark['surface-container-highest'] }};
        --c-outline-variant: {{ $dark['outline-variant'] }};
        --c-secondary-fixed-dim: {{ $dark['secondary-fixed-dim'] }};
        --c-error: {{ $dark['error'] }};
        --c-on-secondary-fixed: {{ $dark['on-secondary-fixed'] }};
        --c-inverse-on-surface: {{ $dark['inverse-on-surface'] }};
        --c-on-primary-fixed: {{ $dark['on-primary-fixed'] }};
        --c-outline: {{ $dark['outline'] }};
        --c-surface: {{ $dark['surface'] }};
        --c-secondary-fixed: {{ $dark['secondary-fixed'] }};
        --c-on-tertiary-fixed: {{ $dark['on-tertiary-fixed'] }};
        --c-on-surface: {{ $dark['on-surface'] }};
        --c-on-primary-container: {{ $dark['on-primary-container'] }};
        --c-on-error: {{ $dark['on-error'] }};
        --c-on-error-container: {{ $dark['on-error-container'] }};
        --c-secondary: {{ $dark['secondary'] }};
        --c-surface-tint: {{ $dark['surface-tint'] }};
        --c-primary-container: {{ $dark['primary-container'] }};
        --c-error-container: {{ $dark['error-container'] }};
        --c-on-secondary-fixed-variant: {{ $dark['on-secondary-fixed-variant'] }};
        --c-on-primary: {{ $dark['on-primary'] }};
        --c-on-background: {{ $dark['on-background'] }};
        --c-surface-variant: {{ $dark['surface-variant'] }};
        --c-surface-container-low: {{ $dark['surface-container-low'] }};
        --c-primary-fill: {{ $dark['primary-fill'] }};
    }

    html {
        background-color: rgb(var(--c-background));
    }

    html.theme-ready.theme-transition *,
    html.theme-ready.theme-transition *::before,
    html.theme-ready.theme-transition *::after {
        transition-property: background-color, border-color, color, fill, stroke, box-shadow !important;
        transition-duration: 200ms !important;
        transition-timing-function: ease !important;
    }

    .dark .shadow-sm,
    .dark .shadow {
        box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.6), 0 0 0 1px rgb(255 255 255 / 0.04);
    }

    .dark .shadow-md {
        box-shadow: 0 6px 16px -6px rgb(0 0 0 / 0.6), 0 0 0 1px rgb(255 255 255 / 0.05);
    }

    .dark .shadow-lg {
        box-shadow: 0 12px 28px -12px rgb(0 0 0 / 0.65), 0 0 0 1px rgb(255 255 255 / 0.05);
    }

    .dark .shadow-xl {
        box-shadow: 0 18px 40px -16px rgb(0 0 0 / 0.7), 0 0 0 1px rgb(255 255 255 / 0.05);
    }

    .dark .shadow-2xl {
        box-shadow: 0 28px 64px -20px rgb(0 0 0 / 0.75), 0 0 0 1px rgb(255 255 255 / 0.06);
    }

    .dark [class*="shadow-["] {
        box-shadow: 0 8px 24px -10px rgb(0 0 0 / 0.65), 0 0 0 1px rgb(255 255 255 / 0.05);
    }

    .dark [class*="shadow-[0_-"] {
        box-shadow: 0 -10px 28px -12px rgb(0 0 0 / 0.7), 0 0 0 1px rgb(255 255 255 / 0.05);
    }

    @media (min-width: 1024px) {
        .dark .auth-form-panel.shadow-lg {
            box-shadow: none;
        }
    }

    .dark [class~="bg-primary-fixed/30"] {
        background-color: rgb(167 161 255 / 0.12);
    }

    .dark [class~="bg-primary/5"] {
        background-color: rgb(167 161 255 / 0.08);
    }

    .dark [class~="bg-tertiary/5"] {
        background-color: rgb(78 222 163 / 0.08);
    }

    .dark [class~="bg-tertiary-fixed-dim/20"] {
        background-color: rgb(78 222 163 / 0.12);
    }

    .dark input:-webkit-autofill,
    .dark input:-webkit-autofill:hover,
    .dark input:-webkit-autofill:focus {
        -webkit-text-fill-color: rgb(var(--c-on-surface));
        -webkit-box-shadow: 0 0 0 1000px rgb(var(--c-surface-container-low)) inset;
        caret-color: rgb(var(--c-on-surface));
    }

    .dark input[type="date"]::-webkit-calendar-picker-indicator,
    .dark input[type="time"]::-webkit-calendar-picker-indicator,
    .dark input[type="datetime-local"]::-webkit-calendar-picker-indicator,
    .dark input[type="month"]::-webkit-calendar-picker-indicator,
    .dark input[type="week"]::-webkit-calendar-picker-indicator {
        filter: invert(0.85);
    }

    .dark .custom-scrollbar::-webkit-scrollbar-thumb {
        background: rgb(var(--c-outline-variant));
    }

    .dark .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: rgb(var(--c-outline));
    }

    .dark .auth-form-panel {
        background-color: rgb(var(--c-surface-container-lowest));
        border-color: rgb(var(--c-outline-variant) / 0.6);
    }

    .dark .auth-form-panel .bg-white {
        background-color: rgb(var(--c-surface-container-low));
    }

    .dark .auth-form-panel [class~="bg-white"][class~="text-slate-900"] {
        background-color: rgb(var(--c-surface-container-highest));
    }

    .dark .auth-form-panel [class~="bg-white"][class~="text-slate-400"] {
        background-color: rgb(var(--c-surface-container-lowest));
    }

    .dark .auth-form-panel .bg-slate-50 {
        background-color: rgb(var(--c-surface-container-low));
    }

    .dark .auth-form-panel .bg-slate-100 {
        background-color: rgb(var(--c-surface-container));
    }

    .dark .auth-form-panel .text-slate-900,
    .dark .auth-form-panel .text-slate-800,
    .dark .auth-form-panel .text-slate-700 {
        color: rgb(var(--c-on-surface));
    }

    .dark .auth-form-panel .text-slate-600,
    .dark .auth-form-panel .text-slate-500 {
        color: rgb(var(--c-on-surface-variant));
    }

    .dark .auth-form-panel .text-slate-400 {
        color: rgb(var(--c-outline));
    }

    .dark .auth-form-panel .text-brand-600 {
        color: rgb(var(--c-primary));
    }

    .dark .auth-form-panel .text-rose-600 {
        color: rgb(var(--c-error));
    }

    .dark .auth-form-panel .bg-emerald-50 {
        background-color: rgb(var(--c-tertiary) / 0.15);
    }

    .dark .auth-form-panel .text-emerald-800 {
        color: rgb(var(--c-tertiary-fixed-dim));
    }

    .dark .auth-form-panel .border-slate-100 {
        border-color: rgb(var(--c-outline-variant) / 0.5);
    }

    .dark .auth-form-panel .border-slate-200,
    .dark .auth-form-panel [class~="border-slate-200/80"],
    .dark .auth-form-panel [class~="border-slate-200/90"] {
        border-color: rgb(var(--c-outline-variant) / 0.55);
    }

    .dark .auth-form-panel .border-slate-300 {
        border-color: rgb(var(--c-outline-variant));
    }

    .dark .auth-form-panel .border-emerald-200 {
        border-color: rgb(var(--c-tertiary) / 0.35);
    }

    .dark .auth-form-panel [class~="hover:bg-slate-50"]:hover,
    .dark .auth-form-panel [class~="hover:bg-slate-100"]:hover,
    .dark .auth-form-panel [class~="hover:bg-slate-200"]:hover {
        background-color: rgb(var(--c-surface-container-high));
    }

    .dark .auth-form-panel [class~="hover:text-slate-900"]:hover,
    .dark .auth-form-panel [class~="hover:text-slate-800"]:hover,
    .dark .auth-form-panel [class~="hover:text-slate-600"]:hover {
        color: rgb(var(--c-on-surface));
    }

    .dark .auth-form-panel input:not([type="checkbox"]):not([type="radio"]),
    .dark .auth-form-panel select,
    .dark .auth-form-panel textarea {
        background-color: rgb(var(--c-surface-container-low));
        border-color: rgb(var(--c-outline-variant));
        color: rgb(var(--c-on-surface));
    }

    .dark .auth-form-panel input::placeholder,
    .dark .auth-form-panel textarea::placeholder {
        color: rgb(var(--c-on-surface-variant) / 0.8);
    }

    .dark .auth-form-panel input[type="checkbox"],
    .dark .auth-form-panel input[type="radio"] {
        background-color: rgb(var(--c-surface-container-low));
        border-color: rgb(var(--c-outline-variant));
    }

    .dark .auth-form-panel [class~="hover:text-brand-700"]:hover {
        color: rgb(var(--c-primary));
    }
</style>
@endif

@if ($section === 'colors' || $section === 'all')
<script>
    window.HRM_THEME_COLORS = @json($colorMap);
</script>
@endif

@if ($section === 'config' || $section === 'all')
<script>
    tailwind.config = @json($tailwindConfig);
</script>
@endif
