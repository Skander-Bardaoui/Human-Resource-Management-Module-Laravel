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

    $pendingLeaves = \App\Models\Conges::where('status', 'pending')->count();
    $attendanceAnomalies = \App\Models\AttendanceRecord::whereDate('work_date', today())->get()
        ->reject(fn ($record) => $record->anomaly_ignored)
        ->filter(fn ($record) => $record->hasMissingCheckout() || $record->isLateArrival() || $record->hasEarlyExit())
        ->count();
    $reviewPayslips = \App\Models\Payslip::where('period', now()->format('Y-m'))->where('status', 'review')->count();
    $notificationCount = $pendingLeaves + $attendanceAnomalies + $reviewPayslips;
@endphp

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <meta content="{{ csrf_token() }}" name="csrf-token"/>
    <meta content="light dark" name="color-scheme"/>
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
    @include('components.theme-tokens', ['section' => 'bootstrap'])
    <script src="https://cdn.tailwindcss.com"></script>
    @include('components.theme-tokens', ['section' => 'config'])
</head>
<body class="bg-background font-body-md text-body-md text-on-surface antialiased">

<!-- Sidebar -->
<div class="fixed inset-0 z-[45] bg-black/60 backdrop-blur-sm hidden lg:hidden" id="adminSidebarBackdrop"></div>
<aside class="fixed left-0 top-0 h-full w-64 bg-surface-container-lowest z-50 hidden lg:flex flex-col shadow-[0_1px_8px_rgba(0,0,0,0.04)]" id="adminSidebar">
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
        <button class="lg:hidden p-1 rounded-lg text-on-surface-variant hover:text-on-surface hover:bg-surface-container-low transition-colors" id="adminSidebarClose" type="button" aria-label="Fermer le menu">
            <span class="material-symbols-outlined text-body-lg">close</span>
        </button>
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
            <button aria-label="Ouvrir le menu" class="lg:hidden w-10 h-10 rounded-xl bg-surface-container-low hover:bg-surface-container flex items-center justify-center text-on-surface-variant hover:text-on-surface transition-colors shrink-0" id="adminMobileNavBtn" type="button">
                <span class="material-symbols-outlined text-body-lg">menu</span>
            </button>
            <!-- Mobile brand -->
            <a class="lg:hidden flex items-center gap-space-sm shrink-0" href="{{ route('admin.dashboard') }}">
                <div class="w-9 h-9 rounded-xl bg-primary-container flex items-center justify-center text-on-primary shadow-sm">
                    <span class="material-symbols-outlined text-[20px]">corporate_fare</span>
                </div>
                <span class="font-label-lg font-semibold text-on-surface hidden sm:inline">HRM Pro</span>
            </a>
            <form class="relative w-full hidden sm:block" action="{{ route('admin.employees') }}" method="GET" role="search">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-body-lg pointer-events-none">search</span>
                <input class="w-full h-10 pl-10 pr-12 rounded-xl bg-surface-container-low font-body-md text-body-md text-on-surface placeholder:text-on-surface-variant focus:outline-none focus:bg-surface-container" id="adminGlobalSearchInput" name="q" placeholder="Search employees, CNSS, CIN, files..." type="text" value="{{ request()->routeIs('admin.employees') ? request('q') : '' }}" autocomplete="off"/>
                <span class="absolute right-3 top-1/2 -translate-y-1/2 font-label-sm text-label-sm text-on-surface-variant bg-surface-container-lowest px-1.5 py-0.5 rounded shadow-[0_1px_2px_rgba(0,0,0,0.05)]">⌘K</span>
            </form>
        </div>
        <div class="flex items-center gap-space-md shrink-0">
            <div class="relative">
                <button aria-expanded="false" class="h-10 px-space-lg rounded-xl bg-primary-container hover:bg-primary-fill font-label-lg text-label-lg text-on-primary flex items-center gap-space-xs transition-colors" data-admin-dropdown-btn="adminNewRequestMenu" id="adminNewRequestBtn" type="button">
                    <span class="material-symbols-outlined text-body-md">add</span>
                    <span class="hidden sm:inline">New Request</span>
                </button>
                <div class="hidden absolute right-0 top-full mt-2 w-64 bg-surface-container-lowest rounded-xl shadow-[0_4px_6px_-1px_rgba(15,23,42,0.07),0_2px_4px_-2px_rgba(15,23,42,0.05)] py-space-xs z-50" data-admin-dropdown id="adminNewRequestMenu">
                    <p class="px-space-md pt-space-xs pb-space-sm font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wide">Quick Create</p>
                    <a class="flex items-start gap-space-sm px-space-md py-2 hover:bg-surface-container transition-colors" href="{{ route('admin.employees', ['new' => 1]) }}">
                        <span class="material-symbols-outlined text-body-lg text-primary">person_add</span>
                        <span class="flex flex-col min-w-0">
                            <span class="font-label-md text-label-md text-on-surface font-semibold">Add Employee</span>
                            <span class="font-label-sm text-label-sm text-on-surface-variant">Create a new employee record</span>
                        </span>
                    </a>
                    <a class="flex items-start gap-space-sm px-space-md py-2 hover:bg-surface-container transition-colors" href="{{ route('admin.leaves', ['new' => 1]) }}">
                        <span class="material-symbols-outlined text-body-lg text-primary">event_available</span>
                        <span class="flex flex-col min-w-0">
                            <span class="font-label-md text-label-md text-on-surface font-semibold">Request Leave</span>
                            <span class="font-label-sm text-label-sm text-on-surface-variant">Log a new leave request</span>
                        </span>
                    </a>
                    <a class="flex items-start gap-space-sm px-space-md py-2 hover:bg-surface-container transition-colors" href="{{ route('admin.recruitment', ['new' => 'offer']) }}">
                        <span class="material-symbols-outlined text-body-lg text-primary">work</span>
                        <span class="flex flex-col min-w-0">
                            <span class="font-label-md text-label-md text-on-surface font-semibold">Create Job Offer</span>
                            <span class="font-label-sm text-label-sm text-on-surface-variant">Publish a new vacancy</span>
                        </span>
                    </a>
                    <a class="flex items-start gap-space-sm px-space-md py-2 hover:bg-surface-container transition-colors" href="{{ route('admin.documents', ['new' => 1]) }}">
                        <span class="material-symbols-outlined text-body-lg text-primary">upload_file</span>
                        <span class="flex flex-col min-w-0">
                            <span class="font-label-md text-label-md text-on-surface font-semibold">Upload Document</span>
                            <span class="font-label-sm text-label-sm text-on-surface-variant">Add a file to the vault</span>
                        </span>
                    </a>
                </div>
            </div>
            <button aria-label="Changer le thème" aria-pressed="false" class="w-10 h-10 rounded-xl bg-surface-container-low hover:bg-surface-container flex items-center justify-center text-on-surface-variant hover:text-on-surface transition-colors shrink-0" data-theme-toggle type="button">
                <span class="material-symbols-outlined text-body-lg" data-theme-icon>contrast</span>
            </button>
            <div class="relative">
                <button aria-label="Notifications" aria-expanded="false" class="relative w-10 h-10 rounded-xl bg-surface-container-low hover:bg-surface-container flex items-center justify-center text-on-surface-variant hover:text-on-surface transition-colors" data-admin-dropdown-btn="adminNotificationsMenu" id="adminNotificationsBtn" type="button">
                    <span class="material-symbols-outlined text-body-lg">notifications</span>
                    @if ($notificationCount > 0)
                        <span class="absolute top-2 right-2 h-4 min-w-[16px] px-1 bg-error text-on-error font-label-sm text-label-sm rounded-full flex items-center justify-center font-bold">{{ $notificationCount > 9 ? '9+' : $notificationCount }}</span>
                    @endif
                </button>
                <div class="hidden absolute right-0 top-full mt-2 w-80 bg-surface-container-lowest rounded-xl shadow-[0_4px_6px_-1px_rgba(15,23,42,0.07),0_2px_4px_-2px_rgba(15,23,42,0.05)] py-space-xs z-50" data-admin-dropdown id="adminNotificationsMenu">
                    <div class="flex items-center justify-between px-space-md py-space-xs border-b border-surface-container">
                        <p class="font-label-md text-label-md text-on-surface font-semibold">Notifications</p>
                        <span class="font-label-sm text-label-sm text-on-surface-variant">{{ $notificationCount }} new</span>
                    </div>
                    @if ($pendingLeaves > 0)
                        <a class="flex items-start gap-space-sm px-space-md py-2 hover:bg-surface-container transition-colors" href="{{ route('admin.leaves', ['status' => 'pending']) }}">
                            <span class="w-8 h-8 rounded-lg bg-secondary-container flex items-center justify-center text-on-secondary-container shrink-0">
                                <span class="material-symbols-outlined text-body-lg">event_available</span>
                            </span>
                            <span class="flex flex-col min-w-0">
                                <span class="font-label-md text-label-md text-on-surface font-semibold">Leave requests</span>
                                <span class="font-label-sm text-label-sm text-on-surface-variant">{{ $pendingLeaves }} pending approval</span>
                            </span>
                        </a>
                    @endif
                    @if ($attendanceAnomalies > 0)
                        <a class="flex items-start gap-space-sm px-space-md py-2 hover:bg-surface-container transition-colors" href="{{ route('admin.attendance') }}">
                            <span class="w-8 h-8 rounded-lg bg-error-container flex items-center justify-center text-on-error-container shrink-0">
                                <span class="material-symbols-outlined text-body-lg">schedule</span>
                            </span>
                            <span class="flex flex-col min-w-0">
                                <span class="font-label-md text-label-md text-on-surface font-semibold">Attendance anomalies</span>
                                <span class="font-label-sm text-label-sm text-on-surface-variant">{{ $attendanceAnomalies }} to review today</span>
                            </span>
                        </a>
                    @endif
                    @if ($reviewPayslips > 0)
                        <a class="flex items-start gap-space-sm px-space-md py-2 hover:bg-surface-container transition-colors" href="{{ route('admin.payroll', ['status' => 'review']) }}">
                            <span class="w-8 h-8 rounded-lg bg-primary-fixed flex items-center justify-center text-primary shrink-0">
                                <span class="material-symbols-outlined text-body-lg">payments</span>
                            </span>
                            <span class="flex flex-col min-w-0">
                                <span class="font-label-md text-label-md text-on-surface font-semibold">Payslips in review</span>
                                <span class="font-label-sm text-label-sm text-on-surface-variant">{{ $reviewPayslips }} awaiting validation</span>
                            </span>
                        </a>
                    @endif
                    @if ($notificationCount === 0)
                        <div class="flex flex-col items-center gap-space-xs px-space-md py-space-lg text-center">
                            <span class="material-symbols-outlined text-body-lg text-tertiary">check_circle</span>
                            <p class="font-label-md text-label-md text-on-surface">All caught up</p>
                            <p class="font-label-sm text-label-sm text-on-surface-variant">No pending actions right now.</p>
                        </div>
                    @endif
                </div>
            </div>
            <div class="relative">
                <button aria-expanded="false" class="flex items-center gap-space-sm pl-space-xs pr-space-sm py-1 rounded-xl hover:bg-surface-container-low transition-colors" data-admin-dropdown-btn="adminUserMenu" type="button" id="adminUserMenuBtn">
                    <div class="w-8 h-8 rounded-full bg-primary-fill flex items-center justify-center shrink-0">
                        <span class="text-on-primary text-[11px] font-bold">{{ $initials ?: 'A' }}</span>
                    </div>
                    <div class="hidden md:flex flex-col text-left">
                        <span class="font-label-md text-label-md text-on-surface font-semibold">{{ $user->name }}</span>
                        <span class="font-label-sm text-label-sm text-on-surface-variant">HR Director</span>
                    </div>
                    <span class="material-symbols-outlined text-on-surface-variant text-body-md">expand_more</span>
                </button>
                <div class="hidden absolute right-0 top-full mt-2 w-56 bg-surface-container-lowest rounded-xl shadow-[0_4px_6px_-1px_rgba(15,23,42,0.07),0_2px_4px_-2px_rgba(15,23,42,0.05)] py-space-xs z-50" data-admin-dropdown id="adminUserMenu">
                    <div class="px-space-md py-space-xs border-b border-surface-container">
                        <p class="font-label-md text-label-md text-on-surface font-semibold">{{ $user->name }}</p>
                        <p class="font-label-sm text-label-sm text-on-surface-variant truncate">{{ $user->email }}</p>
                    </div>
                    <a class="flex items-center gap-space-sm px-space-md py-2 font-body-sm text-body-sm text-on-surface-variant hover:bg-surface-container hover:text-on-surface transition-colors" href="{{ route('admin.profile') }}">
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
    (() => {
        const dropdownButtons = document.querySelectorAll('[data-admin-dropdown-btn]');

        const closeDropdowns = (except = null) => {
            dropdownButtons.forEach((btn) => {
                const menu = document.getElementById(btn.dataset.adminDropdownBtn);
                if (!menu || menu === except) return;
                menu.classList.add('hidden');
                btn.setAttribute('aria-expanded', 'false');
            });
        };

        dropdownButtons.forEach((btn) => {
            const menu = document.getElementById(btn.dataset.adminDropdownBtn);
            if (!menu) return;
            btn.addEventListener('click', (event) => {
                event.stopPropagation();
                const willOpen = menu.classList.contains('hidden');
                closeDropdowns(willOpen ? menu : null);
                menu.classList.toggle('hidden', !willOpen);
                btn.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
            });
            menu.addEventListener('click', (event) => event.stopPropagation());
        });

        document.addEventListener('click', () => closeDropdowns());

        const sidebar = document.getElementById('adminSidebar');
        const sidebarBackdrop = document.getElementById('adminSidebarBackdrop');
        const mobileNavBtn = document.getElementById('adminMobileNavBtn');
        const sidebarClose = document.getElementById('adminSidebarClose');

        const openSidebar = () => {
            if (!sidebar || !sidebarBackdrop) return;
            sidebar.classList.remove('hidden');
            sidebar.classList.add('flex');
            sidebarBackdrop.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        };

        const closeSidebar = () => {
            if (!sidebar || !sidebarBackdrop) return;
            sidebar.classList.add('hidden');
            sidebar.classList.remove('flex');
            sidebarBackdrop.classList.add('hidden');
            document.body.style.overflow = '';
        };

        mobileNavBtn?.addEventListener('click', (event) => {
            event.stopPropagation();
            closeDropdowns();
            openSidebar();
        });
        sidebarClose?.addEventListener('click', closeSidebar);
        sidebarBackdrop?.addEventListener('click', closeSidebar);
        sidebar?.querySelectorAll('nav a').forEach((link) => link.addEventListener('click', closeSidebar));

        const searchInput = document.getElementById('adminGlobalSearchInput');
        document.addEventListener('keydown', (event) => {
            if ((event.metaKey || event.ctrlKey) && event.key.toLowerCase() === 'k') {
                event.preventDefault();
                searchInput?.focus();
                searchInput?.select();
            }
            if (event.key === 'Escape') {
                closeDropdowns();
                closeSidebar();
                if (document.activeElement === searchInput) searchInput.blur();
            }
        });

        window.addEventListener('resize', () => {
            if (window.innerWidth >= 1024) closeSidebar();
        });
    })();
</script>
</body>
</html>
