@props(['active' => 'dashboard', 'title' => 'Administration RH'])

@php
    $user = auth()->user();
    $nameParts = preg_split('/\s+/', trim($user->name));
    $initials = strtoupper(substr($nameParts[0] ?? '', 0, 1) . substr($nameParts[1] ?? '', 0, 1));

    $navBase = 'flex items-center gap-space-md px-space-md py-2.5 rounded-xl font-label-lg text-label-lg transition-colors';
    $navIdle = 'text-on-surface-variant hover:bg-surface-container hover:text-on-surface';
    $navActive = 'bg-primary-container text-on-primary font-semibold';

    $links = [
        ['key' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'grid_view', 'route' => 'admin.dashboard'],
        ['key' => 'employees', 'label' => 'Employees', 'icon' => 'badge', 'route' => 'admin.employees'],
        ['key' => 'leaves', 'label' => 'Leaves', 'icon' => 'event_available', 'route' => 'admin.leaves'],
        ['key' => 'attendance', 'label' => 'Attendance', 'icon' => 'schedule', 'route' => 'admin.attendance'],
        ['key' => 'departments', 'label' => 'Departments', 'icon' => 'account_tree', 'route' => 'admin.departments'],
        ['key' => 'payroll', 'label' => 'Payroll', 'icon' => 'payments', 'route' => 'admin.payroll'],
        ['key' => 'recruitment', 'label' => 'Recruitment', 'icon' => 'work', 'route' => 'admin.recruitment'],
        ['key' => 'offers', 'label' => 'Job Offers', 'icon' => 'assignment', 'route' => 'admin.offers'],
        ['key' => 'documents', 'label' => 'Documents', 'icon' => 'description', 'route' => 'admin.documents'],
        ['key' => 'reports', 'label' => 'Reports', 'icon' => 'analytics', 'route' => 'admin.reports'],
        ['key' => 'settings', 'label' => 'Settings', 'icon' => 'settings', 'route' => 'admin.settings'],
    ];
@endphp

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <meta content="{{ csrf_token() }}" name="csrf-token"/>
    <title>{{ $title }} — HRM Pro</title>
    <link href="https://fonts.googleapis.com" rel="preconnect"/>
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&amp;display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
    <style>
        html, body {
            margin: 0;
            padding: 0;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }
        body {
            overscroll-behavior: none;
        }
        main > :first-child { margin-top: 0 !important; }
        main > :last-child { margin-bottom: 0 !important; }
        main { overflow-x: clip; }
        ::-webkit-scrollbar {
            display: none;
        }
    </style>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "inverse-primary": "#c3c0ff",
                        "surface-dim": "#d2d9f4",
                        "tertiary-container": "#006e4b",
                        "surface-container-high": "#e2e7ff",
                        "on-tertiary-fixed-variant": "#005236",
                        "background": "#faf8ff",
                        "surface-container": "#eaedff",
                        "secondary-container": "#d0e1fb",
                        "on-primary-fixed-variant": "#3323cc",
                        "on-tertiary": "#ffffff",
                        "tertiary": "#005338",
                        "on-tertiary-container": "#67f4b7",
                        "on-surface-variant": "#464555",
                        "primary-fixed": "#e2dfff",
                        "inverse-surface": "#283044",
                        "surface-bright": "#faf8ff",
                        "primary-fixed-dim": "#c3c0ff",
                        "tertiary-fixed": "#6ffbbe",
                        "surface-container-lowest": "#ffffff",
                        "primary": "#3525cd",
                        "tertiary-fixed-dim": "#4edea3",
                        "on-secondary-container": "#54647a",
                        "on-secondary": "#ffffff",
                        "surface-container-highest": "#dae2fd",
                        "outline-variant": "#c7c4d8",
                        "secondary-fixed-dim": "#b7c8e1",
                        "error": "#ba1a1a",
                        "on-secondary-fixed": "#0b1c30",
                        "inverse-on-surface": "#eef0ff",
                        "on-primary-fixed": "#0f0069",
                        "outline": "#777587",
                        "surface": "#faf8ff",
                        "secondary-fixed": "#d3e4fe",
                        "on-tertiary-fixed": "#002113",
                        "on-surface": "#131b2e",
                        "on-primary-container": "#dad7ff",
                        "on-error": "#ffffff",
                        "on-error-container": "#93000a",
                        "secondary": "#505f76",
                        "surface-tint": "#4d44e3",
                        "primary-container": "#4f46e5",
                        "error-container": "#ffdad6",
                        "on-secondary-fixed-variant": "#38485d",
                        "on-primary": "#ffffff",
                        "on-background": "#131b2e",
                        "surface-variant": "#dae2fd",
                        "surface-container-low": "#f2f3ff"
                    },
                    borderRadius: {
                        "DEFAULT": "0.125rem",
                        "lg": "0.25rem",
                        "xl": "0.5rem",
                        "full": "0.75rem"
                    },
                    spacing: {
                        "space-md": "0.75rem",
                        "space-xl": "1.5rem",
                        "gutter": "1.25rem",
                        "margin": "2rem",
                        "space-lg": "1rem",
                        "space-xs": "0.25rem",
                        "space-sm": "0.5rem",
                        "margin-mobile": "1rem",
                        "gutter-mobile": "0.75rem"
                    },
                    fontFamily: {
                        "label-md": ["Inter"],
                        "display-lg-mobile": ["Inter"],
                        "headline-lg": ["Inter"],
                        "numeric-md": ["Inter"],
                        "display-lg": ["Inter"],
                        "body-md": ["Inter"],
                        "body-lg": ["Inter"],
                        "body-sm": ["Inter"],
                        "headline-md": ["Inter"],
                        "label-lg": ["Inter"],
                        "label-sm": ["Inter"],
                        "headline-sm": ["Inter"]
                    },
                    fontSize: {
                        "label-md": ["12px", {"lineHeight": "16px", "letterSpacing": "0.01em", "fontWeight": "500"}],
                        "display-lg-mobile": ["26px", {"lineHeight": "34px", "letterSpacing": "-0.02em", "fontWeight": "700"}],
                        "headline-lg": ["24px", {"lineHeight": "32px", "letterSpacing": "-0.02em", "fontWeight": "600"}],
                        "numeric-md": ["14px", {"lineHeight": "20px", "fontWeight": "500"}],
                        "display-lg": ["32px", {"lineHeight": "40px", "letterSpacing": "-0.025em", "fontWeight": "700"}],
                        "body-md": ["14px", {"lineHeight": "20px", "fontWeight": "400"}],
                        "body-lg": ["16px", {"lineHeight": "24px", "fontWeight": "400"}],
                        "body-sm": ["13px", {"lineHeight": "18px", "fontWeight": "400"}],
                        "headline-md": ["20px", {"lineHeight": "28px", "letterSpacing": "-0.015em", "fontWeight": "600"}],
                        "label-lg": ["14px", {"lineHeight": "20px", "fontWeight": "500"}],
                        "label-sm": ["11px", {"lineHeight": "14px", "letterSpacing": "0.04em", "fontWeight": "600"}],
                        "headline-sm": ["16px", {"lineHeight": "24px", "letterSpacing": "-0.01em", "fontWeight": "600"}]
                    }
                }
            }
        };
    </script>
</head>
<body class="bg-background font-body-md text-body-md text-on-surface antialiased">

<!-- Sidebar -->
<aside class="fixed left-0 top-0 h-full w-64 bg-surface-container-lowest z-50 hidden lg:flex flex-col shadow-[0_1px_8px_rgba(0,0,0,0.04)]">
    <div class="h-16 px-space-lg flex items-center justify-between gap-space-sm">
        <div class="flex items-center gap-space-sm min-w-0">
            <div class="w-9 h-9 rounded-xl bg-primary-container flex items-center justify-center text-on-primary shadow-sm shrink-0">
                <span class="material-symbols-outlined text-[22px]">corporate_fare</span>
            </div>
            <div class="flex flex-col min-w-0">
                <span class="font-headline-sm text-headline-sm text-on-surface tracking-tight truncate">HRM Pro</span>
                <span class="font-label-sm text-label-sm text-on-surface-variant truncate">Tunisie HCM Suite</span>
            </div>
        </div>
    </div>
    <div class="px-space-md py-space-xs">
        <div class="flex items-center justify-between p-space-sm bg-surface-container-low rounded-xl">
            <div class="flex items-center gap-space-sm min-w-0">
                <span class="w-2 h-2 rounded-full bg-tertiary shrink-0"></span>
                <div class="flex flex-col min-w-0">
                    <span class="font-label-md text-label-md text-on-surface font-semibold truncate">Carthage Tech Solutions</span>
                    <span class="font-label-sm text-label-sm text-on-surface-variant truncate">SARL • Tunis</span>
                </div>
            </div>
            <span class="material-symbols-outlined text-body-md text-on-surface-variant">unfold_more</span>
        </div>
    </div>
    <div class="flex-1 overflow-y-auto px-space-md py-space-sm">
        <nav class="space-y-1">
            @foreach ($links as $link)
                <a
                    class="{{ $navBase }} {{ $active === $link['key'] ? $navActive : $navIdle }}"
                    @if ($active === $link['key']) aria-current="page" @endif
                    href="{{ route($link['route']) }}"
                >
                    <span class="material-symbols-outlined text-body-lg">{{ $link['icon'] }}</span>
                    <span>{{ $link['label'] }}</span>
                </a>
            @endforeach
        </nav>
    </div>
    <div class="p-space-md bg-surface-container-low m-space-md rounded-xl">
        <div class="flex items-center gap-space-sm">
            <span class="material-symbols-outlined text-primary text-body-lg">verified_user</span>
            <div class="flex flex-col min-w-0">
                <span class="font-label-sm text-label-sm text-on-surface font-semibold">Code du Travail TN</span>
                <span class="font-label-sm text-label-sm text-on-surface-variant">CNSS &amp; IRPP Conforme</span>
            </div>
        </div>
    </div>
</aside>

<div class="lg:pl-64">
    <!-- Top Header -->
    <header class="fixed top-0 left-0 lg:left-64 right-0 h-16 bg-surface-container-lowest/90 backdrop-blur-xl z-40 flex items-center justify-between gap-3 px-gutter shadow-[0_1px_8px_rgba(0,0,0,0.04)]">
        <div class="flex items-center gap-space-lg flex-1 max-w-lg min-w-0">
            <!-- Mobile brand -->
            <a class="lg:hidden flex items-center gap-space-sm shrink-0" href="{{ route('admin.dashboard') }}">
                <div class="w-9 h-9 rounded-xl bg-primary-container flex items-center justify-center text-on-primary shadow-sm">
                    <span class="material-symbols-outlined text-[20px]">corporate_fare</span>
                </div>
                <span class="font-label-lg font-semibold text-on-surface hidden sm:inline">HRM Pro</span>
            </a>
            <div class="relative w-full hidden sm:block">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-body-lg pointer-events-none">search</span>
                <input class="w-full h-10 pl-10 pr-12 rounded-xl bg-surface-container-low font-body-md text-body-md text-on-surface placeholder:text-on-surface-variant focus:outline-none focus:bg-surface-container" placeholder="Search employees, CNSS, CIN, files..." type="text"/>
                <span class="absolute right-3 top-1/2 -translate-y-1/2 font-label-sm text-label-sm text-on-surface-variant bg-surface-container-lowest px-1.5 py-0.5 rounded shadow-[0_1px_2px_rgba(0,0,0,0.05)]">⌘K</span>
            </div>
        </div>
        <div class="flex items-center gap-space-md shrink-0">
            <button class="h-10 px-space-lg rounded-xl bg-primary-container hover:bg-primary font-label-lg text-label-lg text-on-primary flex items-center gap-space-xs transition-colors" type="button">
                <span class="material-symbols-outlined text-body-md">add</span>
                <span class="hidden sm:inline">New Request</span>
            </button>
            <button aria-label="Notifications" class="relative w-10 h-10 rounded-xl bg-surface-container-low hover:bg-surface-container flex items-center justify-center text-on-surface-variant hover:text-on-surface transition-colors" type="button">
                <span class="material-symbols-outlined text-body-lg">notifications</span>
                <span class="absolute top-2 right-2 w-4 h-4 bg-error text-on-error font-label-sm text-label-sm rounded-full flex items-center justify-center font-bold">3</span>
            </button>
            <div class="relative">
                <button class="flex items-center gap-space-sm pl-space-xs pr-space-sm py-1 rounded-xl hover:bg-surface-container-low transition-colors" type="button" id="adminUserMenuBtn">
                    <div class="w-8 h-8 rounded-full bg-primary flex items-center justify-center shrink-0">
                        <span class="text-on-primary text-[11px] font-bold">{{ $initials ?: 'A' }}</span>
                    </div>
                    <div class="hidden md:flex flex-col text-left">
                        <span class="font-label-md text-label-md text-on-surface font-semibold">{{ $user->name }}</span>
                        <span class="font-label-sm text-label-sm text-on-surface-variant">HR Director</span>
                    </div>
                    <span class="material-symbols-outlined text-on-surface-variant text-body-md">expand_more</span>
                </button>
                <div class="hidden absolute right-0 top-full mt-2 w-56 bg-surface-container-lowest rounded-xl shadow-[0_4px_6px_-1px_rgba(15,23,42,0.07),0_2px_4px_-2px_rgba(15,23,42,0.05)] py-space-xs z-50" id="adminUserMenu">
                    <div class="px-space-md py-space-xs border-b border-surface-container">
                        <p class="font-label-md text-label-md text-on-surface font-semibold">{{ $user->name }}</p>
                        <p class="font-label-sm text-label-sm text-on-surface-variant truncate">{{ $user->email }}</p>
                    </div>
                    <a class="flex items-center gap-space-sm px-space-md py-2 font-body-sm text-body-sm text-on-surface-variant hover:bg-surface-container hover:text-on-surface transition-colors" href="{{ route('profile.edit') }}">
                        <span class="material-symbols-outlined text-body-md">person</span>
                        <span>My Profile</span>
                    </a>
                    <div class="my-space-xs border-t border-surface-container"></div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="w-full flex items-center gap-space-sm px-space-md py-2 font-body-sm text-body-sm text-error hover:bg-error-container hover:text-on-error-container transition-colors text-left" type="submit">
                            <span class="material-symbols-outlined text-body-md">logout</span>
                            <span>Log out</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <main class="relative pt-16 min-h-screen bg-background w-full px-gutter">
        {{ $slot }}
    </main>
</div>

<script>
    const adminUserMenuBtn = document.getElementById('adminUserMenuBtn');
    const adminUserMenu = document.getElementById('adminUserMenu');
    if (adminUserMenuBtn && adminUserMenu) {
        adminUserMenuBtn.addEventListener('click', (event) => {
            event.stopPropagation();
            adminUserMenu.classList.toggle('hidden');
        });
        document.addEventListener('click', () => adminUserMenu.classList.add('hidden'));
    }
</script>
</body>
</html>
