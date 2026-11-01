@props(['tab' => 'login', 'title' => 'Connexion'])

<!DOCTYPE html>
<html class="h-full" lang="fr">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <meta content="{{ csrf_token() }}" name="csrf-token"/>
    <meta content="light dark" name="color-scheme"/>
    <title>{{ $title }} — HRM Pro Tunisie</title>
    <link href="https://fonts.googleapis.com" rel="preconnect"/>
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&amp;display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
    <style>
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }
        .custom-scrollbar::-webkit-scrollbar {
            width: 5px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #E2E8F0;
            border-radius: 9999px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #CBD5E1;
        }
    </style>
    @include('components.theme-tokens', ['section' => 'bootstrap'])
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    @include('components.theme-tokens', ['section' => 'colors'])
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        ...window.HRM_THEME_COLORS,
                        brand: {
                            50: '#EEF2FF',
                            100: '#E0E7FF',
                            200: '#C7D2FE',
                            500: '#6366F1',
                            600: '#4F46E5',
                            700: '#4338CA',
                            800: '#3730A3',
                            900: '#312E81',
                            950: '#1E1B4B'
                        },
                        tunisia: '#E70013',
                        emerald: {
                            400: '#34D399',
                            500: '#10B981',
                            600: '#059669'
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="h-full bg-slate-50 text-slate-800 dark:bg-surface dark:text-on-surface antialiased selection:bg-brand-500 selection:text-white">
<div class="min-h-screen w-full flex flex-col lg:flex-row overflow-x-hidden">

    <!-- LEFT PANEL: Authentication & Workspace Form -->
    <div class="auth-form-panel w-full lg:w-1/2 min-h-screen flex flex-col justify-between bg-white px-6 py-8 sm:px-12 md:px-16 lg:px-16 xl:px-20 border-r border-slate-200/80 custom-scrollbar overflow-y-auto z-10 shadow-lg lg:shadow-none">
        <div>
            <!-- Top Bar: Logo & Enterprise Status -->
            <div class="flex items-center justify-between pb-8">
                <a class="flex items-center gap-3" href="{{ url('/') }}">
                    <div class="w-11 h-11 rounded-xl bg-gradient-to-tr from-brand-700 via-brand-600 to-indigo-500 flex items-center justify-center text-white shadow-md shadow-brand-500/20 ring-1 ring-white/20">
                        <span class="material-symbols-outlined text-[24px]" style="font-variation-settings: 'FILL' 1;">corporate_fare</span>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-xl text-slate-900 tracking-tight">HRM Pro</span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-brand-50 text-brand-700 border border-brand-200 dark:bg-brand-500/15 dark:text-brand-200 dark:border-brand-500/30">TN v4.12</span>
                        </div>
                        <p class="text-xs text-slate-500 font-medium">Portail RH &amp; Entreprise Tunisie</p>
                    </div>
                </a>
                <div class="flex items-center gap-2">
                    <button aria-label="Changer le thème" aria-pressed="false" class="w-9 h-9 rounded-xl flex items-center justify-center text-on-surface-variant hover:bg-surface-container hover:text-on-surface transition-colors shrink-0" data-theme-toggle type="button">
                        <span class="material-symbols-outlined text-[20px]" data-theme-icon>contrast</span>
                    </button>
                    <div class="hidden sm:flex items-center gap-2 px-3 py-1.5 rounded-full bg-slate-50 border border-slate-200 text-xs text-slate-600">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span class="font-medium">Serveurs Sécurisés Tunis</span>
                    </div>
                </div>
            </div>

            <!-- Segmented Tab Switcher -->
            <div class="bg-slate-100 p-1 rounded-xl flex items-center max-w-md mx-auto mb-8 border border-slate-200/80">
                <a href="{{ route('login') }}" class="flex-1 py-2.5 px-4 rounded-lg font-medium text-sm transition-all duration-200 {{ $tab === 'login' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-600 hover:text-slate-900' }} flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">login</span>
                    <span>Se connecter</span>
                </a>
                <a href="{{ route('register') }}" class="flex-1 py-2.5 px-4 rounded-lg font-medium text-sm transition-all duration-200 {{ $tab === 'register' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-600 hover:text-slate-900' }} flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">person_add</span>
                    <span>Créer un compte</span>
                </a>
            </div>

            {{ $slot }}
        </div>

        <!-- Trust, Legal Compliance & Language Footer -->
        <div class="pt-8 mt-6 border-t border-slate-100">
            <div class="flex items-start gap-3 p-3.5 rounded-xl bg-slate-50 border border-slate-200/90 text-slate-700">
                <div class="w-8 h-8 rounded-lg bg-emerald-500/15 text-emerald-600 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[20px]" style="font-variation-settings: 'FILL' 1;">verified_user</span>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-slate-900">Conformité Légale Garantie</span>
                        <span class="text-[10px] uppercase font-bold tracking-wider px-1.5 py-0.5 rounded bg-emerald-100 text-emerald-800 dark:bg-emerald-500/15 dark:text-emerald-300">Homologué</span>
                    </div>
                    <p class="text-xs text-slate-600 mt-0.5 leading-relaxed">
                        Conforme au Code du Travail (Loi n° 66-27), barème IRPP actualisé et télé-déclarations trimestrielles CNSS Tunisie certifiées.
                    </p>
                </div>
            </div>
            <div class="mt-4 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-slate-500">
                <div class="flex items-center gap-3">
                    <button class="font-semibold text-brand-600 hover:text-brand-700" type="button">Français (TN)</button>
                    <span>•</span>
                    <button class="hover:text-slate-800 transition-colors" type="button">العربية</button>
                    <span>•</span>
                    <button class="hover:text-slate-800 transition-colors" type="button">English</button>
                </div>
                <div class="flex items-center gap-2">
                    <span>© 2025 HRM Pro Tunisie</span>
                    <span>•</span>
                    <a class="hover:underline" href="#">Support RH 24/7</a>
                </div>
            </div>
        </div>
    </div>

    <!-- RIGHT PANEL: Enterprise Showcase & Live Platform Preview -->
    <div class="w-full lg:w-1/2 min-h-screen bg-gradient-to-br from-brand-950 via-brand-900 to-brand-700 text-white relative overflow-hidden flex flex-col justify-between p-8 sm:p-12 lg:p-14 xl:p-16">
        <!-- Background Ambient Geometric SVG & Light Blurs -->
        <div class="absolute inset-0 pointer-events-none opacity-25">
            <svg class="w-full h-full" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <radialGradient cx="80%" cy="20%" id="grad1" r="60%">
                        <stop offset="0%" stop-color="#818CF8" stop-opacity="0.4"></stop>
                        <stop offset="100%" stop-color="#312E81" stop-opacity="0"></stop>
                    </radialGradient>
                    <pattern height="32" id="dot-grid" patternUnits="userSpaceOnUse" width="32">
                        <circle cx="2" cy="2" fill="#E0E7FF" fill-opacity="0.25" r="1.5"></circle>
                    </pattern>
                </defs>
                <rect fill="url(#dot-grid)" height="100%" width="100%"></rect>
                <circle cx="85%" cy="15%" fill="url(#grad1)" r="320"></circle>
                <circle cx="15%" cy="85%" fill="url(#grad1)" r="280"></circle>
            </svg>
        </div>

        <!-- Top Header Badge -->
        <div class="relative z-10 flex items-center justify-between">
            <div class="inline-flex items-center gap-2.5 px-3.5 py-1.5 rounded-full bg-white/10 backdrop-blur-md border border-white/15 text-xs text-white">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                <span class="font-medium">Système Opérationnel • Carthage Tech &amp; 450+ entreprises en Tunisie</span>
            </div>
            <div class="hidden xl:flex items-center gap-2 text-xs text-brand-200 bg-white/5 px-3 py-1.5 rounded-full backdrop-blur-sm border border-white/10">
                <span class="material-symbols-outlined text-[16px]">schedule</span>
                <span>Tunis (UTC+1)</span>
            </div>
        </div>

        <!-- Middle Showcase: Headlines & Glassmorphic SaaS Cards -->
        <div class="relative z-10 my-auto py-8">
            <div class="max-w-xl mb-8">
                <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded bg-indigo-500/30 text-indigo-200 text-xs font-semibold tracking-wide uppercase mb-3">
                    Plateforme RH &amp; Paie Cloud
                </div>
                <h2 class="text-3xl sm:text-4xl xl:text-[40px] font-black tracking-tight leading-tight text-white">
                    La référence SIRH pour les entreprises tunisiennes performantes.
                </h2>
                <p class="text-sm sm:text-base text-indigo-100/90 mt-3.5 leading-relaxed font-normal">
                    Automatisez la paie avec calcul CNSS/IRPP exact, gérez les congés, le pointage et la conformité juridique en toute sérénité.
                </p>
            </div>

            <div class="space-y-4 max-w-xl">
                <!-- Card 1: Payroll Success -->
                <div class="bg-white/95 backdrop-blur-xl text-slate-900 rounded-2xl p-5 shadow-2xl border border-white/20 transition-all duration-300 hover:shadow-indigo-500/20 hover:-translate-y-0.5">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3.5">
                            <div class="w-12 h-12 rounded-xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center shrink-0 ring-1 ring-emerald-500/20">
                                <span class="material-symbols-outlined text-[26px]" style="font-variation-settings: 'FILL' 1;">payments</span>
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-slate-900 text-sm sm:text-base">Paie Mars 2025 clôturée avec succès</span>
                                    <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[11px] font-bold">100% Validé</span>
                                </div>
                                <p class="text-xs text-slate-500 mt-0.5">48 bulletins générés • Virement bancaire télétransmis • Télé-déclaration CNSS prête</p>
                            </div>
                        </div>
                        <div class="text-right pl-3 shrink-0">
                            <span class="block text-xs font-medium text-slate-400">Total Net Viré</span>
                            <span class="text-base sm:text-lg font-extrabold text-brand-700 tracking-tight">142,850 TND</span>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Leave Request -->
                <div class="bg-white/90 backdrop-blur-xl text-slate-900 rounded-2xl p-4 sm:p-5 shadow-2xl border border-white/20 transition-all duration-300 hover:shadow-indigo-500/20 hover:-translate-y-0.5">
                    <div class="flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3.5 min-w-0">
                            <img alt="Mohamed Trabelsi" class="w-11 h-11 rounded-full object-cover ring-2 ring-indigo-200 shrink-0" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDFPGQkQyIcuEEIMEYYdKAL1Aw5xr9TW7T8i7IoutwiIiHa5A9jWjKtnoKkn7Y3sk6LyOCnkzJOU0AscdfJsjzpgDWz5emzev9c6T_dSnPNhvI_vZc0gjLhucSqlBzq9VodKGGOVbnyi3LOHKGXoBpAHCM6K_D-Msxju6S7ze_1gx-7CoTU09hD4yskZd0twNov23QLOB0uIEOWOI8p4CaIpGahjyu2FQzp1lZbF3PdsWQHIdyVFJXp"/>
                            <div class="min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="font-bold text-slate-900 text-sm truncate">Mohamed Trabelsi</span>
                                    <span class="px-2 py-0.5 rounded bg-brand-50 text-brand-700 font-semibold text-[11px]">Tech Lead</span>
                                </div>
                                <p class="text-xs text-slate-500 truncate mt-0.5">Congé Annuel • Solde restant: 16j</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 text-xs font-semibold border border-emerald-200">
                                <span class="material-symbols-outlined text-[14px]">check_circle</span>
                                <span>Approuvé par RH</span>
                            </span>
                            <span class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 text-xs font-medium hidden sm:inline-block">5 jours</span>
                        </div>
                    </div>
                </div>

                <!-- Card 3: Attendance Telemetry & Sparkline -->
                <div class="bg-white/90 backdrop-blur-xl text-slate-900 rounded-2xl p-4 sm:p-5 shadow-2xl border border-white/20 transition-all duration-300 hover:shadow-indigo-500/20 hover:-translate-y-0.5">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3.5">
                            <div class="w-11 h-11 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center shrink-0 ring-1 ring-brand-100">
                                <span class="material-symbols-outlined text-[24px]">how_to_reg</span>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-slate-500">Taux de présence aujourd'hui</p>
                                <div class="flex items-baseline gap-2 mt-0.5">
                                    <span class="text-2xl font-black text-slate-900">96.4%</span>
                                    <span class="text-xs font-bold text-emerald-600 flex items-center gap-0.5">
                                        <span class="material-symbols-outlined text-[16px]">trending_up</span> +1.8%
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="w-32 flex flex-col items-end">
                            <span class="text-[10px] text-slate-400 font-medium mb-1">Pointage biométrique</span>
                            <svg class="w-28 h-7 overflow-visible" fill="none" viewBox="0 0 100 25" xmlns="http://www.w3.org/2000/svg">
                                <path d="M0 20 L 15 16 L 30 18 L 45 10 L 60 13 L 80 5 L 100 2" stroke="#4F46E5" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"></path>
                                <circle cx="100" cy="2" fill="#4F46E5" r="3.5"></circle>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom Testimonial Banner -->
        <div class="relative z-10 pt-6 border-t border-white/15">
            <blockquote class="text-sm sm:text-base italic text-indigo-100 font-normal leading-relaxed">
                « HRM Pro a divisé par 3 le temps de traitement de notre paie mensuelle et fiabilisé à 100% nos bordereaux CNSS. Un gain de sérénité inestimable. »
            </blockquote>
            <div class="mt-3 flex items-center gap-3 text-xs text-indigo-200">
                <span class="font-semibold text-white">Amel Ben Salah</span>
                <span>•</span>
                <span>Directrice des Ressources Humaines, Carthage Tech Solutions (Tunis)</span>
            </div>
        </div>
    </div>
</div>

<script>
    function togglePasswordVisibility(inputId, btn) {
        const input = document.getElementById(inputId);
        const icon = btn.querySelector('.material-symbols-outlined');
        if (input.type === 'password') {
            input.type = 'text';
            icon.textContent = 'visibility';
        } else {
            input.type = 'password';
            icon.textContent = 'visibility_off';
        }
    }
</script>
</body>
</html>
