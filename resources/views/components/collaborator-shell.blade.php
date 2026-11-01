@props(['active' => 'dashboard', 'title' => 'Portail Collaborateur'])

@php
    $user = auth()->user();
    $currentEmployee = $currentEmployee ?? null;
    $collaboratorNotifications = $collaboratorNotifications ?? [];
    $collaboratorNotificationCount = $collaboratorNotificationCount ?? count($collaboratorNotifications);
    $nameParts = preg_split('/\s+/', trim($user->name));
    $firstName = $nameParts[0] ?? $user->name;
    $initials = strtoupper(substr($nameParts[0] ?? '', 0, 1) . substr($nameParts[1] ?? '', 0, 1));
    $matricule = $currentEmployee?->matricule() ?? \App\Support\CollaboratorContext::matricule(null, $user);

    $navBase = 'flex items-center gap-space-sm px-space-md py-space-sm rounded-xl font-label-lg text-label-lg transition-all';
    $navIdle = 'text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface';
    $navActive = 'bg-primary-container text-on-primary shadow-[0_1px_3px_0_rgba(15,23,42,0.05)]';

    $links = [
        ['key' => 'dashboard', 'label' => 'Mon Tableau de bord', 'icon' => 'dashboard', 'route' => 'dashboard'],
        ['key' => 'conges', 'label' => 'Mes Congés', 'icon' => 'event_available', 'route' => 'collaborator.conges'],
        ['key' => 'payslips', 'label' => 'Mes Fiches de Paie', 'icon' => 'payments', 'route' => 'collaborator.payslips'],
        ['key' => 'attendance', 'label' => 'Mon Pointage', 'icon' => 'timelapse', 'route' => 'collaborator.attendance'],
        ['key' => 'documents', 'label' => 'Mes Documents', 'icon' => 'folder_shared', 'route' => 'collaborator.documents'],
        ['key' => 'profile', 'label' => 'Mon Profil & Carrière', 'icon' => 'badge', 'route' => 'profile.edit'],
    ];

    $searchPages = collect($links)->map(fn ($link) => [
        'label' => $link['label'],
        'icon' => $link['icon'],
        'url' => route($link['route']),
        'keywords' => match ($link['key']) {
            'conges' => 'conge congé solde absence demande',
            'payslips' => 'paie fiche salaire bulletin',
            'attendance' => 'pointage presence présence heures',
            'documents' => 'document fichier contrat attestation',
            'profile' => 'profil carriere carrière cv',
            default => 'tableau de bord accueil',
        },
    ])->values();
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
        ::-webkit-scrollbar {
            display: none;
        }
        .hrm-invalid {
            box-shadow: 0 0 0 2px rgb(var(--c-error)) !important;
        }
    </style>
    @include('components.theme-tokens', ['section' => 'bootstrap'])
    <script src="https://cdn.tailwindcss.com"></script>
    @include('components.theme-tokens', ['section' => 'config'])
</head>
<body class="bg-surface font-body-md text-body-md text-on-surface overflow-x-hidden">

<!-- Sidebar -->
<div class="fixed inset-0 z-[45] bg-black/60 backdrop-blur-sm hidden lg:hidden" id="collabSidebarBackdrop"></div>
<aside class="fixed left-0 top-0 h-full w-64 bg-surface-container-low shadow-[0_1px_8px_rgba(0,0,0,0.04)] z-50 hidden lg:flex flex-col justify-between py-space-lg" id="collabSidebar">
    <div class="flex flex-col gap-space-lg">
        <div class="px-space-xl flex items-center justify-between gap-space-sm">
            <div class="flex items-center gap-space-sm">
                <a class="w-9 h-9 rounded-xl bg-primary-container flex items-center justify-center text-on-primary shadow-sm" href="{{ route('dashboard') }}">
                    <span class="material-symbols-outlined text-[22px]">corporate_fare</span>
                </a>
                <div class="flex flex-col">
                    <div class="flex items-center gap-space-xs">
                        <span class="font-headline-sm text-headline-sm text-on-surface">HRM Pro</span>
                        <span class="px-1.5 py-0.5 rounded-full bg-secondary-container text-on-secondary-fixed text-[10px] font-label-sm uppercase tracking-wider font-semibold">TN</span>
                    </div>
                    <span class="font-label-sm text-label-sm text-on-surface-variant leading-none">Portail Collaborateur</span>
                </div>
            </div>
            <button class="lg:hidden w-8 h-8 rounded-lg flex items-center justify-center text-on-surface-variant hover:text-on-surface hover:bg-surface-container-high transition-colors shrink-0" id="collabSidebarClose" type="button" aria-label="Fermer le menu">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>

        <div class="px-space-lg">
            <div class="px-space-sm py-space-xs rounded-xl bg-surface-container-high flex items-center justify-between">
                <div class="flex items-center gap-space-xs overflow-hidden">
                    <span class="material-symbols-outlined text-[16px] text-primary">apartment</span>
                    <span class="font-label-md text-label-md text-on-surface truncate">Carthage Tech Solutions</span>
                </div>
                <span class="w-2 h-2 rounded-full bg-tertiary-container shrink-0" title="Système actif"></span>
            </div>
        </div>

        <nav class="flex flex-col gap-1 px-space-md">
            @foreach ($links as $link)
                <a
                    class="{{ $navBase }} {{ $active === $link['key'] ? $navActive : $navIdle }}"
                    @if ($active === $link['key']) aria-current="page" @endif
                    href="{{ route($link['route']) }}"
                >
                    <span class="material-symbols-outlined text-[20px]">{{ $link['icon'] }}</span>
                    <span>{{ $link['label'] }}</span>
                </a>
            @endforeach
        </nav>
    </div>

    <div class="px-space-md flex flex-col gap-space-md">
        <div class="p-space-md rounded-xl bg-surface-container-lowest shadow-[0_1px_3px_0_rgba(15,23,42,0.05)]">
            <div class="flex items-start gap-space-xs mb-1.5">
                <span class="material-symbols-outlined text-[16px] text-tertiary-container shrink-0">verified_user</span>
                <div class="font-label-md text-label-md text-on-surface font-semibold">Conformité RH Tunisie</div>
            </div>
            <p class="font-body-sm text-body-sm text-on-surface-variant leading-tight">Conforme au Code du Travail TN &amp; protection des données INPDP.</p>
        </div>
        <div class="flex items-center justify-between px-space-sm pt-space-xs text-on-surface-variant font-label-sm text-label-sm">
            <span>v2.4.1 (TN-Loi)</span>
            <span class="flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-tertiary-container"></span>Sécurisé</span>
        </div>
    </div>
</aside>

<!-- Main shell -->
<div class="lg:pl-64">
    <!-- Top Header -->
    <header class="fixed top-0 left-0 lg:left-64 right-0 h-16 bg-surface/80 backdrop-blur-xl shadow-[0_1px_8px_rgba(0,0,0,0.04)] z-40 flex items-center justify-between px-4 sm:px-6 gap-3">
        <div class="flex items-center gap-3 min-w-0">
            <button aria-label="Ouvrir le menu" class="lg:hidden w-9 h-9 rounded-xl flex items-center justify-center text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface transition-colors shrink-0" id="collabMobileNavBtn" type="button">
                <span class="material-symbols-outlined text-[20px]">menu</span>
            </button>
            <!-- Mobile brand -->
            <a class="lg:hidden flex items-center gap-2 shrink-0" href="{{ route('dashboard') }}">
                <div class="w-9 h-9 rounded-xl bg-primary-container flex items-center justify-center text-on-primary shadow-sm">
                    <span class="material-symbols-outlined text-[20px]">corporate_fare</span>
                </div>
                <span class="font-label-lg font-semibold text-on-surface hidden sm:inline">HRM Pro</span>
            </a>
            <!-- Search -->
            <form class="relative hidden md:flex w-96 items-center gap-space-sm px-space-md py-space-xs rounded-xl bg-surface-container-low shadow-[0_1px_3px_0_rgba(15,23,42,0.05)] text-on-surface-variant" data-pages="{{ $searchPages->toJson() }}" id="collabSearchForm" role="search">
                <span class="material-symbols-outlined text-[18px]">search</span>
                <input class="flex-1 min-w-0 bg-transparent border-0 p-0 font-body-sm text-body-sm text-on-surface placeholder:text-on-surface-variant focus:outline-none focus:ring-0" id="collabSearchInput" placeholder="Rechercher un document, solde congé, fiche..." type="text" autocomplete="off" aria-label="Rechercher dans le portail"/>
                <span class="ml-auto font-label-sm text-label-sm px-1.5 py-0.5 rounded bg-surface-container-high text-on-surface">⌘K</span>
                <div class="hidden absolute left-0 right-0 top-full mt-2 rounded-xl bg-surface-container-lowest shadow-lg border border-surface-container-high py-1 z-50" id="collabSearchMenu"></div>
            </form>
        </div>

        <div class="flex items-center gap-3 sm:gap-space-lg shrink-0">
            <a class="h-10 px-3 sm:px-space-lg rounded-xl bg-primary-container text-on-primary font-label-lg text-label-lg flex items-center gap-space-xs hover:bg-primary-fill shadow-[0_1px_3px_0_rgba(15,23,42,0.05)] transition-all" href="{{ route('collaborator.conges') }}#nouvelle-demande">
                <span class="material-symbols-outlined text-[18px]">add</span>
                <span class="hidden sm:inline">Nouvelle Demande</span>
            </a>
            <button aria-label="Changer le thème" aria-pressed="false" class="w-9 h-9 rounded-xl flex items-center justify-center text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface transition-colors shrink-0" data-theme-toggle type="button">
                <span class="material-symbols-outlined text-[20px]" data-theme-icon>contrast</span>
            </button>
            <div class="relative flex items-center justify-center">
                <button aria-expanded="false" aria-label="Notifications" class="w-9 h-9 rounded-xl flex items-center justify-center text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface transition-colors" data-collab-dropdown-btn="collabNotificationsMenu" id="collabNotificationsBtn" type="button">
                    <span class="material-symbols-outlined text-[20px]">notifications</span>
                </button>
                <span class="absolute top-1 right-1 w-4 h-4 rounded-full bg-error text-on-error font-label-sm text-[10px] items-center justify-center font-bold pointer-events-none {{ $collaboratorNotificationCount > 0 ? 'flex' : 'hidden' }}">{{ $collaboratorNotificationCount }}</span>
                <div class="hidden absolute right-0 top-full mt-3 w-72 rounded-xl bg-surface-container-lowest shadow-lg border border-surface-container-high py-1 z-50" data-collab-dropdown id="collabNotificationsMenu">
                    <div class="flex items-center justify-between px-4 py-2 border-b border-surface-container-high">
                        <span class="font-label-lg text-label-lg text-on-surface font-semibold">Notifications</span>
                        <span class="font-label-sm text-label-sm text-on-surface-variant">{{ $collaboratorNotificationCount }} {{ $collaboratorNotificationCount > 1 ? 'nouvelles' : 'nouvelle' }}{{ $collaboratorNotificationCount === 0 ? 's' : '' }}</span>
                    </div>
                    @forelse ($collaboratorNotifications as $notification)
                        <a class="flex items-start gap-2 px-4 py-2.5 hover:bg-surface-container-low transition-colors" href="{{ $notification['url'] }}">
                            <span class="w-8 h-8 rounded-lg {{ $notification['tone'] }} flex items-center justify-center shrink-0">
                                <span class="material-symbols-outlined text-[18px]">{{ $notification['icon'] }}</span>
                            </span>
                            <span class="flex flex-col min-w-0">
                                <span class="font-label-lg text-label-lg text-on-surface font-semibold">{{ $notification['title'] }}</span>
                                <span class="font-label-sm text-label-sm text-on-surface-variant">{{ $notification['subtitle'] }}</span>
                            </span>
                        </a>
                    @empty
                        <div class="flex items-center gap-2 px-4 py-4 text-on-surface-variant">
                            <span class="material-symbols-outlined text-[18px]">notifications_off</span>
                            <span class="font-body-sm text-body-sm">Aucune notification pour le moment.</span>
                        </div>
                    @endforelse
                </div>
            </div>
            <div class="flex items-center gap-space-md pl-space-sm">
                <div class="hidden sm:flex flex-col text-right">
                    <span class="font-label-lg text-label-lg text-on-surface font-semibold leading-tight">{{ $user->name }}</span>
                    <div class="flex items-center justify-end gap-space-xs">
                        <span class="font-label-sm text-label-sm text-on-surface-variant">Collaborateur</span>
                        <span class="px-1.5 py-0.5 rounded bg-surface-container-high text-on-secondary-container font-label-sm text-[10px]">{{ $matricule }}</span>
                    </div>
                </div>
                <div class="w-8 h-8 rounded-full bg-primary-fill flex items-center justify-center shrink-0">
                    <span class="text-on-primary text-[11px] font-bold">{{ $initials ?: 'U' }}</span>
                </div>
                <div class="relative">
                    <button aria-expanded="false" class="w-6 h-6 rounded flex items-center justify-center text-on-surface-variant hover:text-on-surface hover:bg-surface-container-high transition-colors" data-collab-dropdown-btn="userMenu" title="Menu utilisateur" type="button" id="userMenuBtn">
                        <span class="material-symbols-outlined text-[18px]">keyboard_arrow_down</span>
                    </button>
                    <div class="hidden absolute right-0 mt-2 w-48 rounded-xl bg-surface-container-lowest shadow-lg border border-surface-container-high py-1 z-50" data-collab-dropdown id="userMenu">
                        <a class="flex items-center gap-2 px-4 py-2 text-label-lg text-on-surface hover:bg-surface-container-low transition-colors" href="{{ route('profile.edit') }}">
                            <span class="material-symbols-outlined text-[18px]">person</span>
                            Mon profil
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="w-full flex items-center gap-2 px-4 py-2 text-label-lg text-error hover:bg-surface-container-low transition-colors text-left" type="submit">
                                <span class="material-symbols-outlined text-[18px]">logout</span>
                                Déconnexion
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main class="w-full pt-16 bg-surface min-h-screen">
        @include('collaborator.partials.flash-toasts')
        {{ $slot }}
        @include('collaborator.partials.form-guard')
    </main>
</div>

<script>
    (() => {
        const searchForm = document.getElementById('collabSearchForm');
        const searchInput = document.getElementById('collabSearchInput');
        const searchMenu = document.getElementById('collabSearchMenu');
        const sidebar = document.getElementById('collabSidebar');
        const sidebarBackdrop = document.getElementById('collabSidebarBackdrop');
        const mobileNavBtn = document.getElementById('collabMobileNavBtn');
        const sidebarClose = document.getElementById('collabSidebarClose');

        let pages = [];
        try {
            pages = JSON.parse(searchForm?.dataset.pages || '[]');
        } catch (error) {
            pages = [];
        }

        const normalize = (value) => (value || '').toString().normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase();

        const hideSearch = () => {
            if (!searchMenu) return;
            searchMenu.classList.add('hidden');
            searchMenu.innerHTML = '';
        };

        const renderSearch = () => {
            if (!searchMenu || !searchInput) return;
            const query = normalize(searchInput.value.trim());
            searchMenu.innerHTML = '';

            if (!query) {
                searchMenu.classList.add('hidden');
                return;
            }

            const matches = pages
                .filter((page) => normalize(page.label + ' ' + page.keywords).includes(query))
                .slice(0, 5);

            if (!matches.length) {
                const empty = document.createElement('div');
                empty.className = 'px-4 py-3 font-body-sm text-body-sm text-on-surface-variant';
                empty.textContent = 'Aucune page ne correspond à votre recherche.';
                searchMenu.appendChild(empty);
            } else {
                matches.forEach((page) => {
                    const link = document.createElement('a');
                    link.className = 'flex items-center gap-2 px-4 py-2 hover:bg-surface-container-low transition-colors';
                    link.href = page.url;

                    const icon = document.createElement('span');
                    icon.className = 'material-symbols-outlined text-[18px] text-primary';
                    icon.textContent = page.icon;

                    const label = document.createElement('span');
                    label.className = 'font-label-lg text-label-lg text-on-surface';
                    label.textContent = page.label;

                    link.append(icon, label);
                    searchMenu.appendChild(link);
                });
            }

            searchMenu.classList.remove('hidden');
        };

        const closeDropdowns = (except = null) => {
            document.querySelectorAll('[data-collab-dropdown-btn]').forEach((btn) => {
                const menu = document.getElementById(btn.dataset.collabDropdownBtn);
                if (!menu || menu === except) return;
                menu.classList.add('hidden');
                btn.setAttribute('aria-expanded', 'false');
            });
        };

        document.querySelectorAll('[data-collab-dropdown-btn]').forEach((btn) => {
            const menu = document.getElementById(btn.dataset.collabDropdownBtn);
            if (!menu) return;
            btn.addEventListener('click', (event) => {
                event.stopPropagation();
                const willOpen = menu.classList.contains('hidden');
                closeDropdowns(willOpen ? menu : null);
                hideSearch();
                menu.classList.toggle('hidden', !willOpen);
                btn.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
            });
            menu.addEventListener('click', (event) => event.stopPropagation());
        });

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
            hideSearch();
            openSidebar();
        });
        sidebarClose?.addEventListener('click', closeSidebar);
        sidebarBackdrop?.addEventListener('click', closeSidebar);
        sidebar?.querySelectorAll('nav a').forEach((link) => link.addEventListener('click', closeSidebar));

        searchInput?.addEventListener('input', renderSearch);
        searchInput?.addEventListener('focus', () => {
            closeDropdowns();
            if (searchInput.value.trim()) renderSearch();
        });
        searchForm?.addEventListener('submit', (event) => {
            event.preventDefault();
            const first = searchMenu?.querySelector('a');
            if (first) window.location.href = first.href;
        });

        document.addEventListener('click', (event) => {
            closeDropdowns();
            if (searchForm && !searchForm.contains(event.target)) hideSearch();
        });

        document.addEventListener('keydown', (event) => {
            if ((event.metaKey || event.ctrlKey) && event.key.toLowerCase() === 'k') {
                event.preventDefault();
                if (searchInput) {
                    searchInput.focus();
                    searchInput.select();
                }
            }
            if (event.key === 'Escape') {
                closeDropdowns();
                hideSearch();
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
