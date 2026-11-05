<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>HRM Pro — Logiciel RH &amp; Paie tunisien</title>
    <meta name="description" content="HRM Pro : SIRH et paie tunisienne au millime. CNSS, IRPP 2025, congés, pointage et portail collaborateur. Conforme au Code du travail et à la Loi 66-27.">
    <meta name="color-scheme" content="light dark">
    <meta property="og:type" content="website">
    <meta property="og:title" content="HRM Pro — Logiciel RH &amp; Paie tunisien">
    <meta property="og:description" content="La paie tunisienne au millime près. CNSS, IRPP 2025, congés, pointage et portail collaborateur.">
    <meta property="og:image" content="{{ asset('images/office-team.webp') }}">
    <meta name="theme-color" content="#05070F">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Sora:wght@600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">

    @include('components.theme-tokens', ['section' => 'bootstrap'])
    <script src="https://cdn.tailwindcss.com"></script>
    @include('components.theme-tokens', ['section' => 'config'])
    @include('components.theme-tokens', ['section' => 'styles'])

    <style>
        html { scroll-behavior: smooth; }
        section[id] { scroll-margin-top: 96px; }
        .font-hero { font-family: 'Sora', Inter, sans-serif; letter-spacing: -0.035em; }

        .stage {
            background-color: #05070F;
            color: #F4F6FF;
            --stage-line: rgba(255, 255, 255, 0.08);
        }
        .stage-soft {
            background:
                radial-gradient(90% 60% at 80% 0%, rgba(76, 56, 190, 0.28), transparent 60%),
                radial-gradient(70% 60% at 0% 100%, rgba(14, 165, 233, 0.12), transparent 60%),
                #070B18;
        }

        .hero-video {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            opacity: 0.55;
            filter: saturate(1.15);
        }
        .hero-veil {
            background:
                radial-gradient(120% 90% at 75% 8%, rgba(109, 91, 255, 0.35), transparent 58%),
                radial-gradient(80% 70% at 5% 100%, rgba(14, 165, 233, 0.18), transparent 62%),
                linear-gradient(180deg, rgba(5, 7, 15, 0.45) 0%, rgba(5, 7, 15, 0.72) 55%, #05070F 100%);
        }
        .stage-grid {
            background-image:
                linear-gradient(rgba(255, 255, 255, 0.055) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, 0.055) 1px, transparent 1px);
            background-size: 64px 64px;
            -webkit-mask-image: radial-gradient(ellipse at 50% 25%, #000 25%, transparent 78%);
            mask-image: radial-gradient(ellipse at 50% 25%, #000 25%, transparent 78%);
        }
        .grain {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='160' height='160'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.85' numOctaves='2' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='160' height='160' filter='url(%23n)' opacity='0.35'/%3E%3C/svg%3E");
            opacity: 0.05;
            mix-blend-mode: overlay;
        }

        .aurora-blob {
            position: absolute;
            border-radius: 9999px;
            filter: blur(90px);
            opacity: 0.55;
            mix-blend-mode: screen;
            animation: drift 20s ease-in-out infinite alternate;
        }
        @keyframes drift {
            0% { transform: translate3d(0, 0, 0) scale(1); }
            50% { transform: translate3d(40px, -30px, 0) scale(1.12); }
            100% { transform: translate3d(-30px, 25px, 0) scale(0.96); }
        }

        .hero-title { line-height: 0.98; }
        .hero-title .line { display: block; overflow: hidden; padding-bottom: 0.07em; margin-bottom: -0.07em; }
        .hero-title .line-inner {
            display: block;
            transform: translateY(108%);
            animation: line-in 1.05s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            animation-delay: var(--line-delay, 0ms);
            will-change: transform;
        }
        @keyframes line-in { to { transform: translateY(0); } }

        .text-aurora {
            background-image: linear-gradient(100deg, #8B7CFF 0%, #C4B5FD 22%, #7DD3FC 45%, #8B7CFF 70%, #C4B5FD 100%);
            background-size: 250% 100%;
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            animation: hue-slide 10s linear infinite;
        }
        @keyframes hue-slide { to { background-position: 250% 0; } }

        .fade-up {
            opacity: 0;
            transform: translateY(16px);
            animation: fade-up 0.9s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            animation-delay: var(--fade-delay, 0ms);
        }
        @keyframes fade-up { to { opacity: 1; transform: none; } }

        #site-header { border-bottom: 1px solid transparent; transition: background-color 0.35s ease, border-color 0.35s ease, box-shadow 0.35s ease; }
        #site-header.is-scrolled {
            background-color: rgba(5, 7, 15, 0.82);
            border-bottom-color: rgba(255, 255, 255, 0.08);
            box-shadow: 0 18px 50px -30px rgba(0, 0, 0, 0.9);
            backdrop-filter: blur(18px);
        }
        .nav-link { position: relative; }
        .nav-link::after {
            content: '';
            position: absolute;
            left: 14px;
            right: 14px;
            bottom: 5px;
            height: 1px;
            background: linear-gradient(90deg, #A7A1FF, #7DD3FC);
            transform: scaleX(0);
            transform-origin: left;
            transition: transform 0.35s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .nav-link:hover::after, .nav-link.is-current::after { transform: scaleX(1); }
        .nav-link.is-current { color: #fff; }

        #mobile-menu { animation: menu-in 0.35s ease both; }
        @keyframes menu-in { from { opacity: 0; transform: translateY(-8px); } to { opacity: 1; transform: none; } }

        .btn-glow { box-shadow: 0 18px 40px -18px rgba(109, 91, 255, 0.9), inset 0 1px 0 rgba(255, 255, 255, 0.22); }
        .btn-shine { position: relative; overflow: hidden; }
        .btn-shine::after {
            content: '';
            position: absolute;
            top: -60%;
            left: -30%;
            width: 30%;
            height: 220%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.35), transparent);
            transform: rotate(18deg);
            transition: left 0.6s ease;
        }
        .btn-shine:hover::after { left: 110%; }

        .hero-glow {
            position: absolute;
            inset: 8% -6% -10% 12%;
            background: radial-gradient(closest-side, rgba(109, 91, 255, 0.55), rgba(14, 165, 233, 0.18) 60%, transparent 78%);
            filter: blur(50px);
            z-index: -1;
        }
        .tilt-wrap { perspective: 1200px; }
        .tilt {
            transform-style: preserve-3d;
            transform: rotateX(var(--rx, 0deg)) rotateY(var(--ry, 0deg));
            transition: transform 0.25s ease-out;
            will-change: transform;
        }
        .tilt-glare {
            background: radial-gradient(420px circle at var(--gx, 50%) var(--gy, 50%), rgba(255, 255, 255, 0.2), transparent 55%);
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        [data-tilt]:hover .tilt-glare { opacity: 1; }

        .browser-bar { background: linear-gradient(180deg, #151C38, #0D1328); }

        @keyframes float-y { 0%, 100% { transform: translateY(0) rotate(-1deg); } 50% { transform: translateY(-10px) rotate(-1deg); } }
        @keyframes float-y-alt { 0%, 100% { transform: translateY(0) rotate(1.5deg); } 50% { transform: translateY(-12px) rotate(1.5deg); } }
        .float-card { animation: float-y 7s ease-in-out infinite; }
        .float-card-alt { animation: float-y-alt 9s ease-in-out infinite; }

        .marquee-mask {
            -webkit-mask-image: linear-gradient(to right, transparent, #000 8%, #000 92%, transparent);
            mask-image: linear-gradient(to right, transparent, #000 8%, #000 92%, transparent);
        }
        .marquee-track { display: flex; width: max-content; animation: marquee-x 44s linear infinite; }
        .marquee-track.reverse { animation-direction: reverse; animation-duration: 56s; }
        .marquee-mask:hover .marquee-track, .marquee-mask:focus-within .marquee-track { animation-play-state: paused; }
        @keyframes marquee-x { from { transform: translateX(0); } to { transform: translateX(-50%); } }

        .tour-step { transition: opacity 0.45s ease; }
        @media (min-width: 1024px) {
            .tour-step { opacity: 0.32; }
            .tour-step.is-active { opacity: 1; }
        }
        .tour-screen {
            position: absolute;
            inset: 0;
            opacity: 0;
            transform: translateY(22px) scale(0.985);
            transition: opacity 0.55s ease, transform 0.75s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .tour-screen.is-active { opacity: 1; transform: none; }
        .tour-seg span { transform: scaleX(0); transform-origin: left; transition: transform 0.5s cubic-bezier(0.16, 1, 0.3, 1); }
        .tour-seg.is-active span { transform: scaleX(1); }

        .spotlight { position: relative; isolation: isolate; }
        .spotlight::before {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: inherit;
            background: radial-gradient(360px circle at var(--mx, 50%) var(--my, 50%), rgba(109, 91, 255, 0.16), transparent 62%);
            opacity: 0;
            transition: opacity 0.35s ease;
            z-index: -1;
            pointer-events: none;
        }
        .spotlight:hover::before { opacity: 1; }

        .keywords-track { display: flex; width: max-content; animation: marquee-x 34s linear infinite; }
        .keyword {
            font-family: 'Sora', Inter, sans-serif;
            font-weight: 800;
            letter-spacing: -0.03em;
            white-space: nowrap;
            color: transparent;
            -webkit-text-stroke: 1px rgba(255, 255, 255, 0.22);
        }
        .keyword.solid { color: #fff; -webkit-text-stroke: 0; }

        .stats-panel .stat-bar span {
            transform: scaleX(0);
            transform-origin: left;
            transition: transform 1.2s cubic-bezier(0.16, 1, 0.3, 1);
            transition-delay: var(--bar-delay, 0ms);
        }
        .stats-panel.is-visible .stat-bar span { transform: scaleX(var(--bar-w, 1)); }

        .carousel-track { scrollbar-width: none; -ms-overflow-style: none; }
        .carousel-track::-webkit-scrollbar { display: none; }
        .carousel-dot {
            width: 7px;
            height: 7px;
            border-radius: 9999px;
            background: rgb(var(--c-on-surface-variant) / 0.25);
            transition: width 0.3s ease, background-color 0.3s ease;
        }
        .carousel-dot.is-active { width: 26px; background: rgb(var(--c-primary)); }

        @property --angle { syntax: '<angle>'; inherits: false; initial-value: 0deg; }
        .price-pro {
            border: 1px solid transparent;
            background:
                linear-gradient(rgb(var(--c-surface-container-lowest)), rgb(var(--c-surface-container-lowest))) padding-box,
                conic-gradient(from var(--angle), rgba(109, 91, 255, 0.95), rgba(125, 211, 252, 0.85), rgba(78, 222, 163, 0.9), rgba(109, 91, 255, 0.95)) border-box;
            animation: border-spin 9s linear infinite;
        }
        @keyframes border-spin { to { --angle: 360deg; } }

        #cursor-glow {
            position: fixed;
            top: -260px;
            left: -260px;
            width: 520px;
            height: 520px;
            border-radius: 9999px;
            background: radial-gradient(circle, rgba(124, 58, 237, 0.16), transparent 65%);
            pointer-events: none;
            z-index: 30;
            opacity: 0;
            transition: opacity 0.5s ease;
        }
        #cursor-glow.is-on { opacity: 1; }

        .js [data-reveal] {
            opacity: 0;
            transform: translateY(28px);
            filter: blur(6px);
            transition: opacity 0.8s cubic-bezier(0.16, 1, 0.3, 1), transform 0.8s cubic-bezier(0.16, 1, 0.3, 1), filter 0.8s ease;
            transition-delay: var(--reveal-delay, 0ms);
        }
        .js [data-reveal].is-visible { opacity: 1; transform: none; filter: none; }

        .confetti {
            position: absolute;
            top: -16px;
            width: 7px;
            height: 13px;
            border-radius: 1px;
            opacity: 0;
            animation: confetti-fall var(--dur, 2.8s) cubic-bezier(0.2, 0.6, 0.35, 1) forwards;
            animation-delay: var(--delay, 0ms);
        }
        @keyframes confetti-fall {
            0% { transform: translate3d(0, -24px, 0) rotate(0deg); opacity: 1; }
            100% { transform: translate3d(var(--dx, 0px), 460px, 0) rotate(var(--rot, 540deg)); opacity: 0; }
        }

        .check-draw circle, .check-draw path { stroke-dasharray: 160; stroke-dashoffset: 160; animation: draw 0.8s ease forwards; }
        .check-draw path { animation-delay: 0.45s; }
        @keyframes draw { to { stroke-dashoffset: 0; } }

        :focus-visible { outline: 2px solid #A7A1FF; outline-offset: 3px; }

        @media (prefers-reduced-motion: reduce) {
            html { scroll-behavior: auto !important; }
            .hero-title .line-inner, .fade-up, .text-aurora, .aurora-blob, .float-card, .float-card-alt,
            .marquee-track, .keywords-track { animation: none !important; opacity: 1 !important; transform: none !important; }
            .js [data-reveal] { opacity: 1 !important; transform: none !important; filter: none !important; }
            .tilt, .tilt-glare { transform: none !important; }
            .tour-screen, .tour-seg span, .stat-bar span { transition: none !important; }
            .carousel-track { scroll-behavior: auto !important; }
            .price-pro { animation: none !important; }
            #cursor-glow { display: none !important; }
        }
    </style>
    <script>document.documentElement.classList.add('js');</script>
</head>
<body class="bg-background font-body-md text-on-surface antialiased overflow-x-hidden">

<a href="#contenu" class="sr-only focus:not-sr-only focus:fixed focus:left-3 focus:top-3 focus:z-[80] focus:rounded-xl focus:bg-white focus:px-4 focus:py-2 focus:font-label-lg focus:text-[#05070F]">Aller au contenu</a>

<div class="fixed inset-x-0 top-0 z-[70] h-[2px] bg-white/5" aria-hidden="true">
    <div id="scroll-progress" class="h-full w-0 bg-gradient-to-r from-[#8B7CFF] via-[#C4B5FD] to-[#7DD3FC]"></div>
</div>

<div id="cursor-glow" aria-hidden="true"></div>

<header id="site-header" class="fixed inset-x-0 top-0 z-50 bg-transparent">
    <div class="mx-auto flex h-20 max-w-[1280px] items-center justify-between gap-6 px-margin-mobile md:px-margin">
        <a href="#top" class="flex shrink-0 items-center gap-space-xs" aria-label="HRM Pro — retour en haut">
            <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-[#8B7CFF] to-[#4F46E5] shadow-[0_10px_30px_-12px_rgba(109,91,255,0.9)]">
                <span class="material-symbols-outlined text-[22px] text-white">corporate_fare</span>
            </span>
            <span class="flex flex-col">
                <span class="font-hero text-[18px] font-bold leading-none text-white">HRM Pro</span>
                <span class="mt-0.5 font-label-sm text-label-sm font-semibold uppercase tracking-[0.18em] text-[#A7A1FF]">Tunisie</span>
            </span>
        </a>

        <nav class="hidden items-center gap-1 lg:flex" aria-label="Navigation principale">
            <a class="nav-link rounded-lg px-space-md py-space-xs font-label-lg text-label-lg text-white/65 transition-colors hover:text-white" data-nav-link href="#experience">Produit</a>
            <a class="nav-link rounded-lg px-space-md py-space-xs font-label-lg text-label-lg text-white/65 transition-colors hover:text-white" data-nav-link href="#fonctionnalites">Fonctionnalités</a>
            <a class="nav-link rounded-lg px-space-md py-space-xs font-label-lg text-label-lg text-white/65 transition-colors hover:text-white" data-nav-link href="#demo-video">Démo</a>
            <a class="nav-link rounded-lg px-space-md py-space-xs font-label-lg text-label-lg text-white/65 transition-colors hover:text-white" data-nav-link href="#tarifs">Tarifs</a>
            <a class="nav-link rounded-lg px-space-md py-space-xs font-label-lg text-label-lg text-white/65 transition-colors hover:text-white" data-nav-link href="#temoignages">Témoignages</a>
        </nav>

        <div class="flex items-center gap-2">
            <button aria-label="Changer le thème" aria-pressed="false" class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl text-white/65 transition-colors hover:bg-white/10 hover:text-white" data-theme-toggle type="button">
                <span class="material-symbols-outlined text-[20px]" data-theme-icon>contrast</span>
            </button>
            <a class="hidden h-9 items-center justify-center rounded-xl px-space-md font-label-lg text-label-lg text-white/65 transition-colors hover:bg-white/10 hover:text-white sm:inline-flex" href="{{ route('login') }}">Connexion</a>
            <a class="hidden h-9 items-center justify-center rounded-xl bg-white px-space-lg font-label-lg text-label-lg font-semibold text-[#05070F] transition-transform hover:scale-[1.02] md:inline-flex" href="#demo">Demander une démo</a>
            <button aria-controls="mobile-menu" aria-expanded="false" aria-label="Ouvrir le menu" class="flex h-9 w-9 items-center justify-center rounded-xl text-white transition-colors hover:bg-white/10 lg:hidden" data-mobile-toggle type="button">
                <span class="material-symbols-outlined text-[22px]" data-mobile-icon>menu</span>
            </button>
        </div>
    </div>

    <div id="mobile-menu" class="hidden border-t border-white/10 bg-[#05070F]/95 backdrop-blur-xl lg:hidden">
        <nav class="mx-auto flex max-w-[1280px] flex-col gap-1 px-margin-mobile py-space-md" aria-label="Navigation mobile">
            <a class="rounded-lg px-space-md py-2.5 font-label-lg text-label-lg text-white/80" href="#experience">Produit</a>
            <a class="rounded-lg px-space-md py-2.5 font-label-lg text-label-lg text-white/80" href="#fonctionnalites">Fonctionnalités</a>
            <a class="rounded-lg px-space-md py-2.5 font-label-lg text-label-lg text-white/80" href="#demo-video">Démo</a>
            <a class="rounded-lg px-space-md py-2.5 font-label-lg text-label-lg text-white/80" href="#tarifs">Tarifs</a>
            <a class="rounded-lg px-space-md py-2.5 font-label-lg text-label-lg text-white/80" href="#temoignages">Témoignages</a>
            <div class="mt-2 flex flex-col gap-2 border-t border-white/10 pt-space-md">
                <a class="flex h-10 items-center justify-center rounded-xl bg-white/10 font-label-lg text-label-lg text-white" href="{{ route('login') }}">Connexion</a>
                <a class="flex h-10 items-center justify-center rounded-xl bg-white font-label-lg text-label-lg font-semibold text-[#05070F]" href="#demo">Demander une démo</a>
            </div>
        </nav>
    </div>
</header>

<main id="contenu">

    <!-- Hero -->
    <section id="top" class="stage relative flex min-h-[100svh] items-center overflow-hidden pb-16 pt-28 lg:pb-24 lg:pt-32">
        <video class="hero-video" autoplay muted loop playsinline preload="metadata" poster="{{ asset('images/video/hero-loop-poster.webp') }}" aria-hidden="true" tabindex="-1" disablepictureinpicture data-hero-video>
            <source src="{{ asset('videos/hero-loop.mp4') }}" type="video/mp4">
        </video>
        <div class="hero-veil pointer-events-none absolute inset-0"></div>
        <div class="stage-grid pointer-events-none absolute inset-0"></div>
        <div class="grain pointer-events-none absolute inset-0"></div>
        <div class="aurora-blob left-[-10%] top-[-8%] h-[420px] w-[420px] bg-[#6D5BFF]"></div>
        <div class="aurora-blob right-[-8%] top-[16%] h-[360px] w-[360px] bg-[#0EA5E9]" style="animation-delay:-7s;"></div>
        <div class="aurora-blob bottom-[-12%] left-[38%] h-[320px] w-[320px] bg-[#7C3AED]" style="animation-delay:-13s;"></div>

        <div class="relative z-10 mx-auto w-full max-w-[1280px] px-margin-mobile md:px-margin">
            <div class="grid items-center gap-14 lg:grid-cols-[1.04fr_0.96fr] lg:gap-10">
                <div class="max-w-[640px]">
                    <span class="fade-up inline-flex items-center gap-2 rounded-[999px] border border-white/15 bg-white/5 px-3 py-1.5 font-label-sm text-label-sm font-semibold text-white/75 backdrop-blur" style="--fade-delay:60ms;">
                        <span class="relative flex h-2 w-2">
                            <span class="absolute inline-flex h-full w-full animate-ping rounded-[999px] bg-[#4EDEA3] opacity-70"></span>
                            <span class="relative inline-flex h-2 w-2 rounded-[999px] bg-[#4EDEA3]"></span>
                        </span>
                        Conforme CNSS · IRPP 2025 · SIBTEL
                    </span>

                    <h1 class="hero-title mt-7 font-hero text-[42px] font-extrabold text-white sm:text-[58px] xl:text-[74px]">
                        <span class="line"><span class="line-inner" style="--line-delay:120ms;">La paie tunisienne,</span></span>
                        <span class="line"><span class="line-inner text-aurora" style="--line-delay:260ms;">au millime près.</span></span>
                    </h1>

                    <p class="fade-up mt-6 max-w-[520px] font-body-lg text-body-lg leading-relaxed text-white/60" style="--fade-delay:480ms;">
                        HRM Pro réunit SIRH, paie, congés, pointage et portail collaborateur. Une seule source de vérité, alignée sur le Code du travail tunisien et la Loi 66-27 — sans Excel ni ressaisie.
                    </p>

                    <div class="fade-up mt-8 flex flex-col gap-3 sm:flex-row sm:items-center" style="--fade-delay:600ms;">
                        <a class="btn-glow btn-shine inline-flex h-12 items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-[#6D5BFF] to-[#8B5CF6] px-6 font-label-lg text-label-lg font-semibold text-white transition-transform active:scale-[0.98]" data-magnetic href="#demo">
                            Demander une démo
                            <span class="material-symbols-outlined text-[20px]">arrow_forward</span>
                        </a>
                        <a class="inline-flex h-12 items-center justify-center gap-2 rounded-xl border border-white/15 bg-white/5 px-5 font-label-lg text-label-lg font-semibold text-white/90 backdrop-blur transition-colors hover:bg-white/10" href="#demo-video">
                            <span class="material-symbols-outlined text-[20px] text-[#A7A1FF]">play_circle</span>
                            Voir la démo · 20 s
                        </a>
                    </div>

                    <div class="fade-up mt-9 flex flex-wrap items-center gap-x-5 gap-y-3" style="--fade-delay:720ms;">
                        <div class="flex -space-x-2">
                            <img alt="Amel Ben Salah" class="h-9 w-9 rounded-[999px] border-2 border-[#05070F] object-cover" src="{{ asset('images/testimonials/amina.webp') }}" width="560" height="560" loading="lazy" decoding="async">
                            <img alt="Karim Chaabane" class="h-9 w-9 rounded-[999px] border-2 border-[#05070F] object-cover" src="{{ asset('images/testimonials/karim.webp') }}" width="560" height="560" loading="lazy" decoding="async">
                            <img alt="Sonia Mezghani" class="h-9 w-9 rounded-[999px] border-2 border-[#05070F] object-cover" src="{{ asset('images/testimonials/sonia.webp') }}" width="560" height="560" loading="lazy" decoding="async">
                        </div>
                        <div class="text-white/60">
                            <div class="flex items-center gap-1 text-[#F5B84B]">
                                <span class="material-symbols-outlined text-[16px]" style="font-variation-settings:'FILL' 1;">star</span>
                                <span class="material-symbols-outlined text-[16px]" style="font-variation-settings:'FILL' 1;">star</span>
                                <span class="material-symbols-outlined text-[16px]" style="font-variation-settings:'FILL' 1;">star</span>
                                <span class="material-symbols-outlined text-[16px]" style="font-variation-settings:'FILL' 1;">star</span>
                                <span class="material-symbols-outlined text-[16px]" style="font-variation-settings:'FILL' 1;">star</span>
                                <span class="ml-1 font-label-sm text-label-sm font-semibold text-white">4,8/5</span>
                            </div>
                            <p class="mt-0.5 font-label-sm text-label-sm">Adoptée par <strong class="text-white">450+</strong> entreprises tunisiennes</p>
                        </div>
                        <span class="hidden h-8 w-px bg-white/10 sm:block"></span>
                        <p class="flex items-center gap-1.5 font-label-sm text-label-sm text-white/50">
                            <span class="material-symbols-outlined text-[16px] text-[#4EDEA3]">verified_user</span>
                            Hébergé en Tunisie · INPDP
                        </p>
                    </div>
                </div>

                <div class="relative mx-auto w-full max-w-[620px]" data-hero-visual>
                    <div class="hero-glow"></div>
                    <div class="tilt-wrap" data-tilt>
                        <div class="tilt relative">
                            <div class="overflow-hidden rounded-[24px] border border-white/15 bg-[#0B1020] shadow-[0_50px_120px_-40px_rgba(0,0,0,0.9)] ring-1 ring-white/5">
                                <div class="browser-bar flex items-center gap-1.5 border-b border-white/10 px-3 py-2.5">
                                    <span class="h-2 w-2 rounded-[999px] bg-[#FF5F57]"></span>
                                    <span class="h-2 w-2 rounded-[999px] bg-[#FEBC2E]"></span>
                                    <span class="h-2 w-2 rounded-[999px] bg-[#28C840]"></span>
                                    <span class="ml-2 flex-1 truncate rounded-md bg-white/10 px-2 py-0.5 text-[10px] font-medium text-white/50">app.hrmpro.tn / dashboard</span>
                                    <span class="flex items-center gap-1 rounded-[999px] bg-[#4EDEA3]/15 px-2 py-0.5 text-[9px] font-semibold uppercase tracking-wider text-[#8FEBC8]">
                                        <span class="h-1.5 w-1.5 rounded-[999px] bg-[#4EDEA3]"></span> Live
                                    </span>
                                </div>
                                <div class="relative aspect-[16/11] overflow-hidden">
                                    <img class="h-full w-full object-cover object-top" src="{{ asset('images/screens/dashboard.webp') }}" alt="Tableau de bord RH de HRM Pro : effectif, congés et absences" width="1200" height="1402" loading="eager" decoding="async" fetchpriority="high">
                                    <div class="tilt-glare pointer-events-none absolute inset-0" data-glare></div>
                                </div>
                            </div>

                            <div class="float-card absolute -left-3 top-12 hidden w-52 rounded-2xl border border-white/15 bg-[#0B1020]/85 p-3.5 shadow-2xl backdrop-blur-md sm:block" data-float-depth="46">
                                <div class="flex items-center gap-2">
                                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-[#4EDEA3]/15 text-[#8FEBC8]">
                                        <span class="material-symbols-outlined text-[18px]">payments</span>
                                    </span>
                                    <div>
                                        <p class="font-label-sm text-[10px] uppercase tracking-wider text-white/45">Net à payer</p>
                                        <p class="font-numeric-md text-numeric-md font-bold text-white">2 543,879 TND</p>
                                    </div>
                                </div>
                                <p class="mt-2 font-label-sm text-[10px] text-white/45">Bulletin de mars · Karim Chaabane</p>
                            </div>

                            <div class="float-card-alt absolute -bottom-5 -right-2 hidden w-56 rounded-2xl border border-white/15 bg-[#0B1020]/85 p-3.5 shadow-2xl backdrop-blur-md sm:block" data-float-depth="34">
                                <div class="flex items-center gap-2">
                                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-[#8B7CFF]/20 text-[#C4B5FD]">
                                        <span class="material-symbols-outlined text-[18px]">verified</span>
                                    </span>
                                    <div>
                                        <p class="font-label-sm text-label-sm font-semibold text-white">CNSS &amp; IRPP à jour</p>
                                        <p class="font-label-sm text-[10px] text-white/45">Télé-déclaration SIBTEL confirmée</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <a class="absolute bottom-6 left-1/2 hidden -translate-x-1/2 flex-col items-center gap-2 text-white/40 transition-colors hover:text-white/80 lg:flex" href="#experience" aria-label="Découvrir la suite">
            <span class="font-label-sm text-[10px] uppercase tracking-[0.2em]">Découvrir</span>
            <span class="flex h-9 w-5 items-start justify-center rounded-[999px] border border-white/20 p-1">
                <span class="h-2 w-1 animate-bounce rounded-[999px] bg-white/60"></span>
            </span>
        </a>
    </section>

    <!-- Sectors marquee -->
    <section class="stage relative border-y border-white/10 py-7" aria-label="Secteurs d'activité">
        <p class="mb-5 text-center font-label-sm text-label-sm uppercase tracking-[0.22em] text-white/35">Conçu pour les équipes RH tunisiennes</p>
        <div class="marquee-mask overflow-hidden">
            <div class="marquee-track items-center">
                <div class="flex shrink-0 items-center">
                    <span class="flex items-center gap-2 px-7 font-label-lg text-label-lg font-semibold text-white/45"><span class="material-symbols-outlined text-[18px] text-white/25">account_balance</span>Banque &amp; Finance</span>
                    <span class="h-1 w-1 rounded-[999px] bg-white/15"></span>
                    <span class="flex items-center gap-2 px-7 font-label-lg text-label-lg font-semibold text-white/45"><span class="material-symbols-outlined text-[18px] text-white/25">factory</span>Industrie</span>
                    <span class="h-1 w-1 rounded-[999px] bg-white/15"></span>
                    <span class="flex items-center gap-2 px-7 font-label-lg text-label-lg font-semibold text-white/45"><span class="material-symbols-outlined text-[18px] text-white/25">health_and_safety</span>Santé</span>
                    <span class="h-1 w-1 rounded-[999px] bg-white/15"></span>
                    <span class="flex items-center gap-2 px-7 font-label-lg text-label-lg font-semibold text-white/45"><span class="material-symbols-outlined text-[18px] text-white/25">memory</span>Tech &amp; Télécom</span>
                    <span class="h-1 w-1 rounded-[999px] bg-white/15"></span>
                    <span class="flex items-center gap-2 px-7 font-label-lg text-label-lg font-semibold text-white/45"><span class="material-symbols-outlined text-[18px] text-white/25">shopping_bag</span>Distribution</span>
                </div>
                <div class="flex shrink-0 items-center" aria-hidden="true">
                    <span class="flex items-center gap-2 px-7 font-label-lg text-label-lg font-semibold text-white/45"><span class="material-symbols-outlined text-[18px] text-white/25">account_balance</span>Banque &amp; Finance</span>
                    <span class="h-1 w-1 rounded-[999px] bg-white/15"></span>
                    <span class="flex items-center gap-2 px-7 font-label-lg text-label-lg font-semibold text-white/45"><span class="material-symbols-outlined text-[18px] text-white/25">factory</span>Industrie</span>
                    <span class="h-1 w-1 rounded-[999px] bg-white/15"></span>
                    <span class="flex items-center gap-2 px-7 font-label-lg text-label-lg font-semibold text-white/45"><span class="material-symbols-outlined text-[18px] text-white/25">health_and_safety</span>Santé</span>
                    <span class="h-1 w-1 rounded-[999px] bg-white/15"></span>
                    <span class="flex items-center gap-2 px-7 font-label-lg text-label-lg font-semibold text-white/45"><span class="material-symbols-outlined text-[18px] text-white/25">memory</span>Tech &amp; Télécom</span>
                    <span class="h-1 w-1 rounded-[999px] bg-white/15"></span>
                    <span class="flex items-center gap-2 px-7 font-label-lg text-label-lg font-semibold text-white/45"><span class="material-symbols-outlined text-[18px] text-white/25">shopping_bag</span>Distribution</span>
                </div>
            </div>
        </div>
        <div class="marquee-mask mt-4 overflow-hidden">
            <div class="marquee-track reverse items-center">
                <div class="flex shrink-0 items-center">
                    <span class="flex items-center gap-2 px-7 font-label-lg text-label-lg font-semibold text-white/30"><span class="material-symbols-outlined text-[18px] text-white/20">apartment</span>BTP &amp; Immobilier</span>
                    <span class="h-1 w-1 rounded-[999px] bg-white/10"></span>
                    <span class="flex items-center gap-2 px-7 font-label-lg text-label-lg font-semibold text-white/30"><span class="material-symbols-outlined text-[18px] text-white/20">support_agent</span>Services</span>
                    <span class="h-1 w-1 rounded-[999px] bg-white/10"></span>
                    <span class="flex items-center gap-2 px-7 font-label-lg text-label-lg font-semibold text-white/30"><span class="material-symbols-outlined text-[18px] text-white/20">school</span>Éducation</span>
                    <span class="h-1 w-1 rounded-[999px] bg-white/10"></span>
                    <span class="flex items-center gap-2 px-7 font-label-lg text-label-lg font-semibold text-white/30"><span class="material-symbols-outlined text-[18px] text-white/20">local_shipping</span>Logistique</span>
                    <span class="h-1 w-1 rounded-[999px] bg-white/10"></span>
                    <span class="flex items-center gap-2 px-7 font-label-lg text-label-lg font-semibold text-white/30"><span class="material-symbols-outlined text-[18px] text-white/20">hotel</span>Hôtellerie</span>
                </div>
                <div class="flex shrink-0 items-center" aria-hidden="true">
                    <span class="flex items-center gap-2 px-7 font-label-lg text-label-lg font-semibold text-white/30"><span class="material-symbols-outlined text-[18px] text-white/20">apartment</span>BTP &amp; Immobilier</span>
                    <span class="h-1 w-1 rounded-[999px] bg-white/10"></span>
                    <span class="flex items-center gap-2 px-7 font-label-lg text-label-lg font-semibold text-white/30"><span class="material-symbols-outlined text-[18px] text-white/20">support_agent</span>Services</span>
                    <span class="h-1 w-1 rounded-[999px] bg-white/10"></span>
                    <span class="flex items-center gap-2 px-7 font-label-lg text-label-lg font-semibold text-white/30"><span class="material-symbols-outlined text-[18px] text-white/20">school</span>Éducation</span>
                    <span class="h-1 w-1 rounded-[999px] bg-white/10"></span>
                    <span class="flex items-center gap-2 px-7 font-label-lg text-label-lg font-semibold text-white/30"><span class="material-symbols-outlined text-[18px] text-white/20">local_shipping</span>Logistique</span>
                    <span class="h-1 w-1 rounded-[999px] bg-white/10"></span>
                    <span class="flex items-center gap-2 px-7 font-label-lg text-label-lg font-semibold text-white/30"><span class="material-symbols-outlined text-[18px] text-white/20">hotel</span>Hôtellerie</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Scroll-driven product tour -->
    <section id="experience" class="stage relative overflow-x-clip py-24 lg:py-32" data-tour>
        <div class="pointer-events-none absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-white/20 to-transparent"></div>
        <div class="pointer-events-none absolute -left-40 top-1/3 h-[500px] w-[500px] rounded-[999px] bg-[#4F46E5]/10 blur-[120px]"></div>

        <div class="mx-auto max-w-[1280px] px-margin-mobile md:px-margin">
            <div class="max-w-2xl" data-reveal>
                <span class="flex items-center gap-2 font-label-sm text-label-sm font-semibold uppercase tracking-[0.2em] text-[#A7A1FF]">
                    <span class="h-px w-8 bg-gradient-to-r from-[#A7A1FF] to-transparent"></span>
                    Visite guidée
                </span>
                <h2 class="mt-4 font-hero text-[34px] font-bold leading-[1.05] text-white sm:text-[46px]">Faites défiler.<br><span class="text-aurora">La plateforme se déroule.</span></h2>
                <p class="mt-5 max-w-lg font-body-lg text-body-lg text-white/50">Quatre étapes, une seule vérité. Suivez le parcours d'un cycle de paie, du bulletin à la télé-déclaration.</p>
            </div>

            <div class="mt-16 grid gap-10 lg:grid-cols-[0.92fr_1.08fr] lg:gap-16">
                <div class="relative">
                    <div class="space-y-8 lg:space-y-0">
                        <article class="tour-step flex flex-col justify-center py-2 lg:min-h-[64vh]" data-tour-step="0" data-tour-caption="Paie &amp; conformité" data-tour-kpi="48 bulletins · 0 anomalie">
                            <span class="font-hero text-[13px] font-bold tracking-[0.2em] text-[#A7A1FF]/70">01</span>
                            <h3 class="mt-3 font-hero text-2xl font-bold text-white sm:text-3xl">Paie &amp; conformité</h3>
                            <p class="mt-3 max-w-md font-body-md text-body-md leading-relaxed text-white/50">Bulletins au millime, CNSS 9,18 % / 16,57 %, IRPP 2025 et CSS 1 %. Contrôles automatiques avant chaque clôture.</p>
                            <ul class="mt-5 space-y-2 font-label-lg text-label-lg text-white/65">
                                <li class="flex items-center gap-2"><span class="material-symbols-outlined text-[18px] text-[#4EDEA3]">check_circle</span>Calculs conformes au Code du travail</li>
                                <li class="flex items-center gap-2"><span class="material-symbols-outlined text-[18px] text-[#4EDEA3]">check_circle</span>Télé-déclaration SIBTEL en un clic</li>
                                <li class="flex items-center gap-2"><span class="material-symbols-outlined text-[18px] text-[#4EDEA3]">check_circle</span>Journal d'audit de chaque cycle</li>
                            </ul>
                            <div class="mt-7 overflow-hidden rounded-2xl border border-white/10 bg-white/5 lg:hidden">
                                <img class="h-56 w-full object-cover object-top" src="{{ asset('images/screens/paie.webp') }}" alt="Module de paie de HRM Pro" width="1200" height="1396" loading="lazy" decoding="async">
                            </div>
                        </article>

                        <article class="tour-step flex flex-col justify-center py-2 lg:min-h-[64vh]" data-tour-step="1" data-tour-caption="Pointage &amp; présence" data-tour-kpi="Temps réel · Fuseau de Tunis">
                            <span class="font-hero text-[13px] font-bold tracking-[0.2em] text-[#A7A1FF]/70">02</span>
                            <h3 class="mt-3 font-hero text-2xl font-bold text-white sm:text-3xl">Pointage &amp; présence</h3>
                            <p class="mt-3 max-w-md font-body-md text-body-md leading-relaxed text-white/50">Badgeuse virtuelle, pauses, télétravail et régularisation des oublis. Les heures alimentent la paie sans ressaisie.</p>
                            <ul class="mt-5 space-y-2 font-label-lg text-label-lg text-white/65">
                                <li class="flex items-center gap-2"><span class="material-symbols-outlined text-[18px] text-[#4EDEA3]">check_circle</span>Arrivée, départ et pauses horodatés</li>
                                <li class="flex items-center gap-2"><span class="material-symbols-outlined text-[18px] text-[#4EDEA3]">check_circle</span>Vue temps réel de l'assiduité</li>
                                <li class="flex items-center gap-2"><span class="material-symbols-outlined text-[18px] text-[#4EDEA3]">check_circle</span>Régularisation en self-service</li>
                            </ul>
                            <div class="mt-7 overflow-hidden rounded-2xl border border-white/10 bg-white/5 lg:hidden">
                                <img class="h-56 w-full object-cover object-top" src="{{ asset('images/screens/pointage.webp') }}" alt="Pointage et badgeuse virtuelle dans HRM Pro" width="1200" height="1430" loading="lazy" decoding="async">
                            </div>
                        </article>

                        <article class="tour-step flex flex-col justify-center py-2 lg:min-h-[64vh]" data-tour-step="2" data-tour-caption="Portail collaborateur" data-tour-kpi="60 % d'e-mails RH en moins">
                            <span class="font-hero text-[13px] font-bold tracking-[0.2em] text-[#A7A1FF]/70">03</span>
                            <h3 class="mt-3 font-hero text-2xl font-bold text-white sm:text-3xl">Portail collaborateur</h3>
                            <p class="mt-3 max-w-md font-body-md text-body-md leading-relaxed text-white/50">Congés, bulletins PDF et attestations accessibles 24/7 sur mobile, avec validation hiérarchique automatique.</p>
                            <ul class="mt-5 space-y-2 font-label-lg text-label-lg text-white/65">
                                <li class="flex items-center gap-2"><span class="material-symbols-outlined text-[18px] text-[#4EDEA3]">check_circle</span>Demandes et soldes en self-service</li>
                                <li class="flex items-center gap-2"><span class="material-symbols-outlined text-[18px] text-[#4EDEA3]">check_circle</span>Bulletins consultables sur mobile</li>
                                <li class="flex items-center gap-2"><span class="material-symbols-outlined text-[18px] text-[#4EDEA3]">check_circle</span>Validations hiérarchiques fluides</li>
                            </ul>
                            <div class="mt-7 overflow-hidden rounded-2xl border border-white/10 bg-white/5 lg:hidden">
                                <img class="h-56 w-full object-cover object-top" src="{{ asset('images/screens/conges.webp') }}" alt="Portail collaborateur : demandes de congés dans HRM Pro" width="1200" height="1119" loading="lazy" decoding="async">
                            </div>
                        </article>

                        <article class="tour-step flex flex-col justify-center py-2 lg:min-h-[64vh]" data-tour-step="3" data-tour-caption="Documents &amp; conformité" data-tour-kpi="384 documents · chiffrés">
                            <span class="font-hero text-[13px] font-bold tracking-[0.2em] text-[#A7A1FF]/70">04</span>
                            <h3 class="mt-3 font-hero text-2xl font-bold text-white sm:text-3xl">Documents &amp; conformité</h3>
                            <p class="mt-3 max-w-md font-body-md text-body-md leading-relaxed text-white/50">Coffre-fort numérique, conservation légale et traçabilité INPDP. Chaque document signé reste retrouvable en secondes.</p>
                            <ul class="mt-5 space-y-2 font-label-lg text-label-lg text-white/65">
                                <li class="flex items-center gap-2"><span class="material-symbols-outlined text-[18px] text-[#4EDEA3]">check_circle</span>Chiffrement et journal d'audit</li>
                                <li class="flex items-center gap-2"><span class="material-symbols-outlined text-[18px] text-[#4EDEA3]">check_circle</span>Contrats, CIN et bulletins centralisés</li>
                                <li class="flex items-center gap-2"><span class="material-symbols-outlined text-[18px] text-[#4EDEA3]">check_circle</span>Conforme INPDP &amp; Loi 66-27</li>
                            </ul>
                            <div class="mt-7 overflow-hidden rounded-2xl border border-white/10 bg-white/5 lg:hidden">
                                <img class="h-56 w-full object-cover object-top" src="{{ asset('images/screens/documents.webp') }}" alt="Gestion documentaire sécurisée dans HRM Pro" width="1200" height="1148" loading="lazy" decoding="async">
                            </div>
                        </article>
                    </div>
                </div>

                <div class="hidden lg:block">
                    <div class="sticky top-28">
                        <div class="relative overflow-hidden rounded-[26px] border border-white/15 bg-[#0B1020] shadow-[0_50px_120px_-40px_rgba(0,0,0,0.9)] ring-1 ring-white/5">
                            <div class="browser-bar flex items-center gap-1.5 border-b border-white/10 px-3 py-2.5">
                                <span class="h-2 w-2 rounded-[999px] bg-[#FF5F57]"></span>
                                <span class="h-2 w-2 rounded-[999px] bg-[#FEBC2E]"></span>
                                <span class="h-2 w-2 rounded-[999px] bg-[#28C840]"></span>
                                <span class="ml-2 flex-1 truncate rounded-md bg-white/10 px-2 py-0.5 text-[10px] font-medium text-white/50">app.hrmpro.tn / espace RH</span>
                            </div>
                            <div class="relative aspect-[16/11] overflow-hidden bg-[#0B1020]">
                                <img class="tour-screen is-active" data-tour-screen="0" src="{{ asset('images/screens/paie.webp') }}" alt="Module de paie de HRM Pro" width="1200" height="1396" loading="lazy" decoding="async">
                                <img class="tour-screen" data-tour-screen="1" src="{{ asset('images/screens/pointage.webp') }}" alt="Pointage et assiduité dans HRM Pro" width="1200" height="1430" loading="lazy" decoding="async">
                                <img class="tour-screen" data-tour-screen="2" src="{{ asset('images/screens/conges.webp') }}" alt="Portail collaborateur de HRM Pro" width="1200" height="1119" loading="lazy" decoding="async">
                                <img class="tour-screen" data-tour-screen="3" src="{{ asset('images/screens/documents.webp') }}" alt="Gestion documentaire de HRM Pro" width="1200" height="1148" loading="lazy" decoding="async">
                                <div class="pointer-events-none absolute inset-0 bg-gradient-to-t from-[#05070F]/70 via-transparent to-transparent"></div>
                                <div class="absolute inset-x-4 bottom-4 flex items-center justify-between gap-3 rounded-xl border border-white/10 bg-[#05070F]/70 px-4 py-2.5 backdrop-blur">
                                    <span class="font-label-sm text-label-sm font-semibold text-white" id="tour-caption">Paie &amp; conformité</span>
                                    <span class="rounded-[999px] bg-[#4EDEA3]/15 px-2.5 py-0.5 font-label-sm text-[10px] font-semibold text-[#8FEBC8]" id="tour-kpi">48 bulletins · 0 anomalie</span>
                                </div>
                            </div>
                        </div>
                        <div class="mt-5 grid grid-cols-4 gap-2" aria-hidden="true">
                            <span class="tour-seg is-active h-1 overflow-hidden rounded-[999px] bg-white/10"><span class="block h-full bg-gradient-to-r from-[#8B7CFF] to-[#7DD3FC]"></span></span>
                            <span class="tour-seg h-1 overflow-hidden rounded-[999px] bg-white/10"><span class="block h-full bg-gradient-to-r from-[#8B7CFF] to-[#7DD3FC]"></span></span>
                            <span class="tour-seg h-1 overflow-hidden rounded-[999px] bg-white/10"><span class="block h-full bg-gradient-to-r from-[#8B7CFF] to-[#7DD3FC]"></span></span>
                            <span class="tour-seg h-1 overflow-hidden rounded-[999px] bg-white/10"><span class="block h-full bg-gradient-to-r from-[#8B7CFF] to-[#7DD3FC]"></span></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features bento -->
    <section id="fonctionnalites" class="relative overflow-hidden bg-surface-container-low/40 py-24 md:py-32">
        <div class="pointer-events-none absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-outline-variant/60 to-transparent"></div>
        <div class="pointer-events-none absolute -right-32 top-10 h-[420px] w-[420px] rounded-[999px] bg-primary-container/10 blur-[100px]"></div>

        <div class="relative mx-auto max-w-[1280px] px-margin-mobile md:px-margin">
            <div class="flex flex-col items-start justify-between gap-5 md:flex-row md:items-end" data-reveal>
                <div class="max-w-2xl">
                    <span class="flex items-center gap-2 font-label-sm text-label-sm font-semibold uppercase tracking-[0.2em] text-primary">
                        <span class="h-px w-8 bg-gradient-to-r from-primary to-transparent"></span>
                        Fonctionnalités
                    </span>
                    <h2 class="mt-4 font-hero text-[34px] font-bold leading-[1.05] text-on-surface sm:text-[46px]">Tout le cycle RH.<br>Une seule vérité.</h2>
                    <p class="mt-5 max-w-lg font-body-lg text-body-lg text-on-surface-variant">De l'embauche au bulletin de paie, chaque module partage les mêmes données — et les mêmes contrôles de conformité.</p>
                </div>
                <a class="group inline-flex items-center gap-1 font-label-lg text-label-lg font-semibold text-primary" href="#demo">Voir en action<span class="material-symbols-outlined text-[18px] transition-transform group-hover:translate-x-1">arrow_forward</span></a>
            </div>

            <div class="mt-12 grid gap-5 md:grid-cols-3">
                <article class="spotlight group relative overflow-hidden rounded-2xl bg-surface-container-lowest p-6 shadow-sm ring-1 ring-black/5 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl md:col-span-2 dark:ring-white/10" data-reveal data-spotlight>
                    <div class="relative z-10 flex flex-col gap-6 sm:flex-row sm:items-center">
                        <div class="max-w-sm">
                            <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-primary-container text-on-primary"><span class="material-symbols-outlined">payments</span></span>
                            <h3 class="mt-4 font-headline-sm text-headline-sm font-semibold text-on-surface">Paie tunisienne, au millime</h3>
                            <p class="mt-2 font-body-md text-body-md text-on-surface-variant">Primes, retenues, IRPP 2025 et CSS calculés automatiquement. Bulletins conformes, archivés et exportables en un clic.</p>
                            <span class="mt-4 inline-flex items-center gap-1.5 rounded-[999px] bg-surface-container-low px-2.5 py-1 font-label-sm text-label-sm font-semibold text-on-surface-variant"><span class="h-1.5 w-1.5 rounded-[999px] bg-tertiary"></span>Aperçu réel du produit</span>
                        </div>
                        <div class="relative flex-1 overflow-hidden rounded-xl ring-1 ring-black/5 dark:ring-white/10">
                            <img class="h-48 w-full object-cover object-top transition-transform duration-700 group-hover:scale-[1.04]" src="{{ asset('images/screens/paie.webp') }}" alt="Aperçu de la paie dans HRM Pro" width="1200" height="1396" loading="lazy" decoding="async">
                            <div class="pointer-events-none absolute inset-0 bg-gradient-to-t from-black/35 to-transparent"></div>
                        </div>
                    </div>
                </article>

                <article class="spotlight group rounded-2xl bg-surface-container-lowest p-6 shadow-sm ring-1 ring-black/5 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl dark:ring-white/10" data-reveal data-spotlight style="--reveal-delay:80ms;">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-primary-container/[0.12] text-primary"><span class="material-symbols-outlined">account_balance</span></span>
                    <h3 class="mt-4 font-headline-sm text-headline-sm font-semibold text-on-surface">CNSS &amp; IRPP à jour</h3>
                    <p class="mt-2 font-body-md text-body-md text-on-surface-variant">Barèmes maintenus et contrôle de conformité avant chaque télé-déclaration.</p>
                    <ul class="mt-4 space-y-1.5 font-label-sm text-label-sm text-on-surface-variant">
                        <li class="flex items-center gap-1.5"><span class="material-symbols-outlined text-[16px] text-tertiary-container">check_circle</span>Salariale 9,18 %</li>
                        <li class="flex items-center gap-1.5"><span class="material-symbols-outlined text-[16px] text-tertiary-container">check_circle</span>Patronale 16,57 %</li>
                        <li class="flex items-center gap-1.5"><span class="material-symbols-outlined text-[16px] text-tertiary-container">check_circle</span>CSS 1 % · IRPP 2025</li>
                    </ul>
                </article>

                <article class="spotlight group rounded-2xl bg-surface-container-lowest p-6 shadow-sm ring-1 ring-black/5 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl dark:ring-white/10" data-reveal data-spotlight>
                    <div class="relative mb-4 overflow-hidden rounded-xl ring-1 ring-black/5 dark:ring-white/10">
                        <img class="h-28 w-full object-cover object-top transition-transform duration-700 group-hover:scale-[1.04]" src="{{ asset('images/screens/pointage.webp') }}" alt="Pointage et badgeuse virtuelle dans HRM Pro" width="1200" height="1430" loading="lazy" decoding="async">
                    </div>
                    <h3 class="font-headline-sm text-headline-sm font-semibold text-on-surface">Pointage &amp; badgeuse</h3>
                    <p class="mt-2 font-body-md text-body-md text-on-surface-variant">Présences, pauses et télétravail en temps réel, avec régularisation des oublis.</p>
                </article>

                <article class="spotlight group rounded-2xl bg-surface-container-lowest p-6 shadow-sm ring-1 ring-black/5 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl dark:ring-white/10" data-reveal data-spotlight style="--reveal-delay:80ms;">
                    <div class="relative mb-4 overflow-hidden rounded-xl ring-1 ring-black/5 dark:ring-white/10">
                        <img class="h-28 w-full object-cover object-top transition-transform duration-700 group-hover:scale-[1.04]" src="{{ asset('images/screens/conges.webp') }}" alt="Gestion des congés dans HRM Pro" width="1200" height="1119" loading="lazy" decoding="async">
                    </div>
                    <h3 class="font-headline-sm text-headline-sm font-semibold text-on-surface">Congés &amp; absences</h3>
                    <p class="mt-2 font-body-md text-body-md text-on-surface-variant">Soldes automatiques, validations hiérarchiques et calendrier d'équipe partagé.</p>
                </article>

                <article class="spotlight group rounded-2xl bg-surface-container-lowest p-6 shadow-sm ring-1 ring-black/5 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl dark:ring-white/10" data-reveal data-spotlight style="--reveal-delay:160ms;">
                    <div class="relative mb-4 overflow-hidden rounded-xl ring-1 ring-black/5 dark:ring-white/10">
                        <img class="h-28 w-full object-cover object-top transition-transform duration-700 group-hover:scale-[1.04]" src="{{ asset('images/screens/recrutement.webp') }}" alt="Recrutement et onboarding dans HRM Pro" width="1200" height="1041" loading="lazy" decoding="async">
                    </div>
                    <h3 class="font-headline-sm text-headline-sm font-semibold text-on-surface">Recrutement &amp; onboarding</h3>
                    <p class="mt-2 font-body-md text-body-md text-on-surface-variant">Offres, pipeline candidats et intégration des nouveaux collaborateurs au même endroit.</p>
                </article>

                <article class="spotlight overflow-hidden rounded-2xl bg-surface-container-lowest p-6 shadow-sm ring-1 ring-black/5 md:col-span-3 dark:ring-white/10" data-reveal data-spotlight>
                    <div class="relative z-10 flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
                        <div class="flex items-start gap-4">
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-tertiary-fixed text-on-tertiary-fixed"><span class="material-symbols-outlined">lock</span></span>
                            <div>
                                <h3 class="font-headline-sm text-headline-sm font-semibold text-on-surface">Conformité INPDP &amp; Loi 66-27</h3>
                                <p class="mt-1 max-w-xl font-body-md text-body-md text-on-surface-variant">Données hébergées en Tunisie, journal d'audit complet, gestion des consentements et droits des collaborateurs.</p>
                                <div class="mt-4 flex flex-wrap items-center gap-2">
                                    <span class="rounded-[999px] border border-outline-variant/40 px-3 py-1 font-label-sm text-label-sm font-semibold text-on-surface-variant">INPDP</span>
                                    <span class="rounded-[999px] border border-outline-variant/40 px-3 py-1 font-label-sm text-label-sm font-semibold text-on-surface-variant">Loi 66-27</span>
                                    <span class="rounded-[999px] border border-outline-variant/40 px-3 py-1 font-label-sm text-label-sm font-semibold text-on-surface-variant">Code du travail</span>
                                    <span class="rounded-[999px] border border-outline-variant/40 px-3 py-1 font-label-sm text-label-sm font-semibold text-on-surface-variant">SIBTEL</span>
                                </div>
                            </div>
                        </div>
                        <div class="hidden w-full max-w-[300px] shrink-0 overflow-hidden rounded-xl ring-1 ring-black/5 md:block dark:ring-white/10">
                            <img class="h-32 w-full object-cover object-top" src="{{ asset('images/screens/documents.webp') }}" alt="Gestion documentaire sécurisée dans HRM Pro" width="1200" height="1148" loading="lazy" decoding="async">
                        </div>
                    </div>
                </article>
            </div>
        </div>
    </section>

    <!-- Keywords marquee -->
    <section class="stage overflow-hidden border-y border-white/10 py-6" aria-hidden="true">
        <div class="keywords-track items-center">
            <div class="flex shrink-0 items-center gap-8 pr-8">
                <span class="keyword text-[52px] sm:text-[76px]">PAIE AU MILLIME</span>
                <span class="keyword solid text-[52px] sm:text-[76px]" style="color:#A7A1FF;">✦</span>
                <span class="keyword text-[52px] sm:text-[76px]">CNSS</span>
                <span class="keyword solid text-[52px] sm:text-[76px]" style="color:#4EDEA3;">✦</span>
                <span class="keyword text-[52px] sm:text-[76px]">IRPP 2025</span>
                <span class="keyword solid text-[52px] sm:text-[76px]" style="color:#7DD3FC;">✦</span>
                <span class="keyword text-[52px] sm:text-[76px]">POINTAGE</span>
            </div>
            <div class="flex shrink-0 items-center gap-8 pr-8" aria-hidden="true">
                <span class="keyword text-[52px] sm:text-[76px]">PAIE AU MILLIME</span>
                <span class="keyword solid text-[52px] sm:text-[76px]" style="color:#A7A1FF;">✦</span>
                <span class="keyword text-[52px] sm:text-[76px]">CNSS</span>
                <span class="keyword solid text-[52px] sm:text-[76px]" style="color:#4EDEA3;">✦</span>
                <span class="keyword text-[52px] sm:text-[76px]">IRPP 2025</span>
                <span class="keyword solid text-[52px] sm:text-[76px]" style="color:#7DD3FC;">✦</span>
                <span class="keyword text-[52px] sm:text-[76px]">POINTAGE</span>
            </div>
        </div>
    </section>

    <!-- Demo video -->
    <section id="demo-video" class="stage stage-soft relative overflow-hidden py-24 md:py-32">
        <div class="pointer-events-none absolute inset-0 stage-grid opacity-40"></div>
        <div class="relative mx-auto max-w-[1100px] px-margin-mobile text-center md:px-margin">
            <div data-reveal>
                <span class="inline-flex items-center gap-2 rounded-[999px] border border-white/15 bg-white/5 px-3 py-1.5 font-label-sm text-label-sm font-semibold text-white/70 backdrop-blur">
                    <span class="material-symbols-outlined text-[16px]">play_circle</span> Démo produit · 20 s
                </span>
                <h2 class="mx-auto mt-5 max-w-2xl font-hero text-[34px] font-bold leading-[1.05] text-white sm:text-[48px]">20 secondes pour <span class="text-aurora">tout comprendre.</span></h2>
                <p class="mx-auto mt-4 max-w-xl font-body-lg text-body-lg text-white/50">Paie, CNSS, congés, pointage : le parcours complet, sans son et sans détour.</p>
            </div>

            <div class="relative mt-12" data-reveal style="--reveal-delay:120ms;">
                <div class="pointer-events-none absolute -inset-8 rounded-[40px] bg-[#6D5BFF]/15 blur-3xl"></div>
                <div class="relative overflow-hidden rounded-[26px] border border-white/15 bg-black shadow-[0_60px_140px_-50px_rgba(0,0,0,0.95)]" data-video-frame>
                    <div class="relative aspect-video">
                        <img class="absolute inset-0 h-full w-full object-cover" src="{{ asset('images/video/demo-poster.webp') }}" alt="Aperçu de la démo produit HRM Pro" width="1280" height="720" loading="lazy" decoding="async" data-video-poster>
                        <video class="relative hidden h-full w-full object-cover" playsinline preload="none" poster="{{ asset('images/video/demo-poster.webp') }}">
                            <source data-src="{{ asset('videos/demo.mp4') }}" type="video/mp4">
                        </video>
                        <div class="pointer-events-none absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-black/20"></div>
                        <button class="group absolute inset-0 flex items-center justify-center" aria-label="Lire la démo vidéo HRM Pro" data-video-play type="button">
                            <span class="absolute h-24 w-24 rounded-[999px] bg-white/10 animate-ping"></span>
                            <span class="relative flex h-16 w-16 items-center justify-center rounded-[999px] bg-white text-[#05070F] shadow-2xl transition-transform duration-300 group-hover:scale-110">
                                <span class="material-symbols-outlined text-[34px]" style="font-variation-settings:'FILL' 1;">play_arrow</span>
                            </span>
                        </button>
                        <span class="pointer-events-none absolute bottom-4 left-4 rounded-[999px] bg-black/55 px-2.5 py-1 font-label-sm text-[11px] font-semibold text-white/85 backdrop-blur">Sans son · 20 s</span>
                    </div>
                </div>
            </div>

            <div class="mt-9 flex flex-wrap items-center justify-center gap-x-6 gap-y-3 font-label-lg text-label-lg text-white/60" data-reveal style="--reveal-delay:200ms;">
                <span class="flex items-center gap-2"><span class="material-symbols-outlined text-[18px] text-[#4EDEA3]">check_circle</span>Paie, CNSS et IRPP en un flux</span>
                <span class="flex items-center gap-2"><span class="material-symbols-outlined text-[18px] text-[#4EDEA3]">check_circle</span>Portail adopté en une semaine</span>
                <span class="flex items-center gap-2"><span class="material-symbols-outlined text-[18px] text-[#4EDEA3]">check_circle</span>Zéro tableur</span>
            </div>
        </div>
    </section>

    <!-- Stats -->
    <section class="py-20 md:py-28" aria-label="Chiffres clés">
        <div class="mx-auto max-w-[1280px] px-margin-mobile md:px-margin">
            <div class="stats-panel grid gap-8 rounded-[26px] border border-outline-variant/30 bg-surface-container-lowest p-8 shadow-sm sm:grid-cols-2 sm:p-10 lg:grid-cols-4" data-reveal>
                <div>
                    <p class="font-hero text-[42px] font-extrabold leading-none text-on-surface"><span data-counter data-target="450" data-suffix="+">450+</span></p>
                    <p class="mt-2 font-label-md text-label-md text-on-surface-variant">entreprises équipées</p>
                    <div class="stat-bar mt-4 h-1 overflow-hidden rounded-[999px] bg-outline-variant/25"><span class="block h-full rounded-[999px] bg-gradient-to-r from-primary to-[#7DD3FC]" style="--bar-w:0.82;"></span></div>
                </div>
                <div>
                    <p class="font-hero text-[42px] font-extrabold leading-none text-on-surface"><span data-counter data-target="99.98" data-decimals="2" data-suffix=" %">99,98 %</span></p>
                    <p class="mt-2 font-label-md text-label-md text-on-surface-variant">précision de paie</p>
                    <div class="stat-bar mt-4 h-1 overflow-hidden rounded-[999px] bg-outline-variant/25"><span class="block h-full rounded-[999px] bg-gradient-to-r from-primary to-[#7DD3FC]" style="--bar-w:0.99; --bar-delay:120ms;"></span></div>
                </div>
                <div>
                    <p class="font-hero text-[42px] font-extrabold leading-none text-on-surface"><span data-counter data-target="2" data-suffix=" h">2 h</span></p>
                    <p class="mt-2 font-label-md text-label-md text-on-surface-variant">gagnées par cycle de paie</p>
                    <div class="stat-bar mt-4 h-1 overflow-hidden rounded-[999px] bg-outline-variant/25"><span class="block h-full rounded-[999px] bg-gradient-to-r from-primary to-[#7DD3FC]" style="--bar-w:0.64; --bar-delay:240ms;"></span></div>
                </div>
                <div>
                    <p class="font-hero text-[42px] font-extrabold leading-none text-on-surface"><span data-counter data-target="6" data-suffix=" TND">6 TND</span></p>
                    <p class="mt-2 font-label-md text-label-md text-on-surface-variant">par salarié, dès</p>
                    <div class="stat-bar mt-4 h-1 overflow-hidden rounded-[999px] bg-outline-variant/25"><span class="block h-full rounded-[999px] bg-gradient-to-r from-primary to-[#7DD3FC]" style="--bar-w:0.44; --bar-delay:360ms;"></span></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <section id="temoignages" class="bg-surface-container-low/40 py-24 md:py-28">
        <div class="mx-auto max-w-[1280px] px-margin-mobile md:px-margin">
            <div class="flex flex-col items-start justify-between gap-5 md:flex-row md:items-end" data-reveal>
                <div class="max-w-2xl">
                    <span class="flex items-center gap-2 font-label-sm text-label-sm font-semibold uppercase tracking-[0.2em] text-primary">
                        <span class="h-px w-8 bg-gradient-to-r from-primary to-transparent"></span>
                        Témoignages
                    </span>
                    <h2 class="mt-4 font-hero text-[34px] font-bold leading-[1.05] text-on-surface sm:text-[46px]">Des équipes RH qui ont<br>retrouvé du temps.</h2>
                </div>
                <div class="flex items-center gap-2 lg:hidden">
                    <button class="flex h-10 w-10 items-center justify-center rounded-[999px] border border-outline-variant/40 text-on-surface transition-colors hover:bg-surface-container-high disabled:opacity-40" data-carousel-prev type="button" aria-label="Témoignage précédent">
                        <span class="material-symbols-outlined text-[20px]">arrow_back</span>
                    </button>
                    <button class="flex h-10 w-10 items-center justify-center rounded-[999px] border border-outline-variant/40 text-on-surface transition-colors hover:bg-surface-container-high disabled:opacity-40" data-carousel-next type="button" aria-label="Témoignage suivant">
                        <span class="material-symbols-outlined text-[20px]">arrow_forward</span>
                    </button>
                </div>
            </div>

            <div class="mt-10 overflow-hidden" data-reveal style="--reveal-delay:100ms;">
                <div class="carousel-track flex snap-x snap-mandatory gap-5 overflow-x-auto scroll-smooth pb-5" data-carousel>
                    <figure class="carousel-card w-[86%] shrink-0 snap-center rounded-2xl border border-outline-variant/30 bg-surface-container-lowest p-7 shadow-sm sm:w-[48%] lg:w-[32%]" data-carousel-card>
                        <div class="flex items-center justify-between">
                            <div class="flex gap-0.5 text-[#F5B84B]">
                                <span class="material-symbols-outlined text-[18px]" style="font-variation-settings:'FILL' 1;">star</span>
                                <span class="material-symbols-outlined text-[18px]" style="font-variation-settings:'FILL' 1;">star</span>
                                <span class="material-symbols-outlined text-[18px]" style="font-variation-settings:'FILL' 1;">star</span>
                                <span class="material-symbols-outlined text-[18px]" style="font-variation-settings:'FILL' 1;">star</span>
                                <span class="material-symbols-outlined text-[18px]" style="font-variation-settings:'FILL' 1;">star</span>
                            </div>
                            <span class="material-symbols-outlined text-[32px] text-primary/20">format_quote</span>
                        </div>
                        <blockquote class="mt-4 min-h-[132px] font-body-md text-body-md leading-relaxed text-on-surface-variant">« Nous avons divisé par trois le temps de traitement de la paie. Les télé-déclarations CNSS et IRPP partent sans erreur, et mon équipe se concentre enfin sur l'humain. »</blockquote>
                        <figcaption class="mt-5 flex items-center gap-3 border-t border-outline-variant/30 pt-4">
                            <img class="h-11 w-11 rounded-[999px] object-cover" src="{{ asset('images/testimonials/amina.webp') }}" alt="Portrait d'Amel Ben Salah" width="560" height="560" loading="lazy" decoding="async">
                            <div>
                                <p class="font-label-lg text-label-lg font-semibold text-on-surface">Amel Ben Salah</p>
                                <p class="font-label-sm text-label-sm text-on-surface-variant">DRH · Carthage Tech Solutions</p>
                            </div>
                        </figcaption>
                    </figure>

                    <figure class="carousel-card w-[86%] shrink-0 snap-center rounded-2xl border border-outline-variant/30 bg-surface-container-lowest p-7 shadow-sm sm:w-[48%] lg:w-[32%]" data-carousel-card>
                        <div class="flex items-center justify-between">
                            <div class="flex gap-0.5 text-[#F5B84B]">
                                <span class="material-symbols-outlined text-[18px]" style="font-variation-settings:'FILL' 1;">star</span>
                                <span class="material-symbols-outlined text-[18px]" style="font-variation-settings:'FILL' 1;">star</span>
                                <span class="material-symbols-outlined text-[18px]" style="font-variation-settings:'FILL' 1;">star</span>
                                <span class="material-symbols-outlined text-[18px]" style="font-variation-settings:'FILL' 1;">star</span>
                                <span class="material-symbols-outlined text-[18px]" style="font-variation-settings:'FILL' 1;">star</span>
                            </div>
                            <span class="material-symbols-outlined text-[32px] text-primary/20">format_quote</span>
                        </div>
                        <blockquote class="mt-4 min-h-[132px] font-body-md text-body-md leading-relaxed text-on-surface-variant">« La paie tunisienne au millime, sans retouche Excel. Le contrôle de conformité nous a fait gagner une sérénité totale avant chaque clôture. »</blockquote>
                        <figcaption class="mt-5 flex items-center gap-3 border-t border-outline-variant/30 pt-4">
                            <img class="h-11 w-11 rounded-[999px] object-cover" src="{{ asset('images/testimonials/karim.webp') }}" alt="Portrait de Karim Chaabane" width="560" height="560" loading="lazy" decoding="async">
                            <div>
                                <p class="font-label-lg text-label-lg font-semibold text-on-surface">Karim Chaabane</p>
                                <p class="font-label-sm text-label-sm text-on-surface-variant">Directeur Finance · BIAT Capital</p>
                            </div>
                        </figcaption>
                    </figure>

                    <figure class="carousel-card w-[86%] shrink-0 snap-center rounded-2xl border border-outline-variant/30 bg-surface-container-lowest p-7 shadow-sm sm:w-[48%] lg:w-[32%]" data-carousel-card>
                        <div class="flex items-center justify-between">
                            <div class="flex gap-0.5 text-[#F5B84B]">
                                <span class="material-symbols-outlined text-[18px]" style="font-variation-settings:'FILL' 1;">star</span>
                                <span class="material-symbols-outlined text-[18px]" style="font-variation-settings:'FILL' 1;">star</span>
                                <span class="material-symbols-outlined text-[18px]" style="font-variation-settings:'FILL' 1;">star</span>
                                <span class="material-symbols-outlined text-[18px]" style="font-variation-settings:'FILL' 1;">star</span>
                                <span class="material-symbols-outlined text-[18px]" style="font-variation-settings:'FILL' 1;">star</span>
                            </div>
                            <span class="material-symbols-outlined text-[32px] text-primary/20">format_quote</span>
                        </div>
                        <blockquote class="mt-4 min-h-[132px] font-body-md text-body-md leading-relaxed text-on-surface-variant">« Le portail collaborateur a réduit de 60 % les e-mails RH. Congés, bulletins, pointage : tout est self-service et adopté dès la première semaine. »</blockquote>
                        <figcaption class="mt-5 flex items-center gap-3 border-t border-outline-variant/30 pt-4">
                            <img class="h-11 w-11 rounded-[999px] object-cover" src="{{ asset('images/testimonials/sonia.webp') }}" alt="Portrait de Sonia Mezghani" width="560" height="560" loading="lazy" decoding="async">
                            <div>
                                <p class="font-label-lg text-label-lg font-semibold text-on-surface">Sonia Mezghani</p>
                                <p class="font-label-sm text-label-sm text-on-surface-variant">Responsable RH · Telnet</p>
                            </div>
                        </figcaption>
                    </figure>
                </div>
                <div class="mt-2 flex items-center justify-center gap-1.5 lg:hidden" data-carousel-dots aria-hidden="true"></div>
            </div>
        </div>
    </section>

    <!-- Pricing -->
    <section id="tarifs" class="py-24 md:py-28">
        <div class="mx-auto max-w-[1280px] px-margin-mobile md:px-margin">
            <div class="mx-auto max-w-2xl text-center" data-reveal>
                <span class="inline-flex items-center gap-2 font-label-sm text-label-sm font-semibold uppercase tracking-[0.2em] text-primary">
                    <span class="h-px w-8 bg-gradient-to-r from-transparent to-primary"></span>
                    Tarifs transparents
                    <span class="h-px w-8 bg-gradient-to-r from-primary to-transparent"></span>
                </span>
                <h2 class="mt-4 font-hero text-[34px] font-bold leading-[1.05] text-on-surface sm:text-[46px]">Un prix simple, par salarié.</h2>
                <p class="mt-4 font-body-lg text-body-lg text-on-surface-variant">Sans frais d'installation. Sans engagement de longue durée.</p>
            </div>

            <div class="mt-12 grid items-stretch gap-5 lg:grid-cols-3">
                <article class="flex flex-col rounded-2xl bg-surface-container-lowest p-7 shadow-sm ring-1 ring-black/5 transition-all duration-300 hover:-translate-y-1 hover:shadow-lg dark:ring-white/10" data-reveal>
                    <h3 class="font-headline-sm text-headline-sm font-semibold text-on-surface">Starter</h3>
                    <p class="mt-1 font-body-sm text-body-sm text-on-surface-variant">Pour structurer la paie d'une PME.</p>
                    <p class="mt-5 flex items-baseline gap-1"><span class="font-hero text-4xl font-extrabold text-on-surface">6</span><span class="font-label-md text-label-md text-on-surface-variant">TND / salarié / mois</span></p>
                    <ul class="mt-6 flex-1 space-y-2.5 font-body-sm text-body-sm text-on-surface-variant">
                        <li class="flex items-start gap-2"><span class="material-symbols-outlined text-[18px] text-tertiary-container">check_circle</span>Jusqu'à 50 collaborateurs</li>
                        <li class="flex items-start gap-2"><span class="material-symbols-outlined text-[18px] text-tertiary-container">check_circle</span>Bulletins de paie illimités</li>
                        <li class="flex items-start gap-2"><span class="material-symbols-outlined text-[18px] text-tertiary-container">check_circle</span>Portail collaborateur</li>
                        <li class="flex items-start gap-2"><span class="material-symbols-outlined text-[18px] text-tertiary-container">check_circle</span>Congés &amp; absences</li>
                        <li class="flex items-start gap-2"><span class="material-symbols-outlined text-[18px] text-tertiary-container">check_circle</span>Support par e-mail</li>
                    </ul>
                    <a class="mt-7 flex h-11 items-center justify-center rounded-xl bg-surface-container-high font-label-lg text-label-lg font-semibold text-on-surface transition-colors hover:bg-surface-variant" href="#demo">Commencer</a>
                </article>

                <article class="price-pro relative flex flex-col rounded-2xl p-7 shadow-xl lg:-mt-3 lg:mb-3" data-reveal style="--reveal-delay:80ms;">
                    <span class="absolute -top-3 left-1/2 -translate-x-1/2 rounded-[999px] bg-gradient-to-r from-[#6D5BFF] to-[#8B5CF6] px-3 py-1 font-label-sm text-[11px] font-bold uppercase tracking-wider text-white shadow-lg">Le plus populaire</span>
                    <h3 class="font-headline-sm text-headline-sm font-semibold text-on-surface">Pro</h3>
                    <p class="mt-1 font-body-sm text-body-sm text-on-surface-variant">Pour les équipes RH qui veulent tout automatiser.</p>
                    <p class="mt-5 flex items-baseline gap-1"><span class="font-hero text-4xl font-extrabold text-on-surface">9</span><span class="font-label-md text-label-md text-on-surface-variant">TND / salarié / mois</span></p>
                    <ul class="mt-6 flex-1 space-y-2.5 font-body-sm text-body-sm text-on-surface-variant">
                        <li class="flex items-start gap-2"><span class="material-symbols-outlined text-[18px] text-primary">check_circle</span>Tout le plan Starter</li>
                        <li class="flex items-start gap-2"><span class="material-symbols-outlined text-[18px] text-primary">check_circle</span>Pointage &amp; badgeuse virtuelle</li>
                        <li class="flex items-start gap-2"><span class="material-symbols-outlined text-[18px] text-primary">check_circle</span>Recrutement &amp; onboarding</li>
                        <li class="flex items-start gap-2"><span class="material-symbols-outlined text-[18px] text-primary">check_circle</span>Télé-déclarations CNSS / IRPP</li>
                        <li class="flex items-start gap-2"><span class="material-symbols-outlined text-[18px] text-primary">check_circle</span>Exports comptables &amp; CNSS</li>
                        <li class="flex items-start gap-2"><span class="material-symbols-outlined text-[18px] text-primary">check_circle</span>Support prioritaire</li>
                    </ul>
                    <a class="btn-glow mt-7 flex h-11 items-center justify-center rounded-xl bg-gradient-to-r from-[#6D5BFF] to-[#8B5CF6] font-label-lg text-label-lg font-semibold text-white transition-transform hover:scale-[1.01]" data-magnetic href="#demo">Choisir Pro</a>
                </article>

                <article class="flex flex-col rounded-2xl bg-surface-container-lowest p-7 shadow-sm ring-1 ring-black/5 transition-all duration-300 hover:-translate-y-1 hover:shadow-lg dark:ring-white/10" data-reveal style="--reveal-delay:160ms;">
                    <h3 class="font-headline-sm text-headline-sm font-semibold text-on-surface">Enterprise</h3>
                    <p class="mt-1 font-body-sm text-body-sm text-on-surface-variant">Pour les groupes et structures multi-entités.</p>
                    <p class="mt-5 flex items-baseline gap-1"><span class="font-hero text-2xl font-extrabold text-on-surface">Sur devis</span></p>
                    <ul class="mt-6 flex-1 space-y-2.5 font-body-sm text-body-sm text-on-surface-variant">
                        <li class="flex items-start gap-2"><span class="material-symbols-outlined text-[18px] text-tertiary-container">check_circle</span>Multi-entités &amp; conventions</li>
                        <li class="flex items-start gap-2"><span class="material-symbols-outlined text-[18px] text-tertiary-container">check_circle</span>SSO &amp; permissions avancées</li>
                        <li class="flex items-start gap-2"><span class="material-symbols-outlined text-[18px] text-tertiary-container">check_circle</span>Intégrations ERP sur mesure</li>
                        <li class="flex items-start gap-2"><span class="material-symbols-outlined text-[18px] text-tertiary-container">check_circle</span>Accompagnement dédié</li>
                        <li class="flex items-start gap-2"><span class="material-symbols-outlined text-[18px] text-tertiary-container">check_circle</span>SLA 99,9 %</li>
                    </ul>
                    <a class="mt-7 flex h-11 items-center justify-center rounded-xl bg-surface-container-high font-label-lg text-label-lg font-semibold text-on-surface transition-colors hover:bg-surface-variant" href="#demo">Demander un devis</a>
                </article>
            </div>
        </div>
    </section>

    <!-- Demo / contact -->
    <section id="demo" class="stage stage-soft relative overflow-hidden py-24 md:py-28">
        <div class="pointer-events-none absolute inset-0 stage-grid opacity-30"></div>
        <div class="pointer-events-none absolute -left-20 bottom-0 h-[420px] w-[420px] rounded-[999px] bg-[#6D5BFF]/15 blur-[110px]"></div>

        <div class="relative mx-auto max-w-[1180px] px-margin-mobile md:px-margin">
            <div class="overflow-hidden rounded-[28px] border border-white/10 bg-white/[0.03] shadow-[0_60px_140px_-60px_rgba(0,0,0,0.9)] backdrop-blur" data-reveal>
                <div class="grid lg:grid-cols-2">
                    <div class="relative p-7 sm:p-10">
                        <div id="confetti" class="pointer-events-none absolute inset-0 overflow-hidden" aria-hidden="true"></div>
                        <span class="font-label-sm text-label-sm font-semibold uppercase tracking-[0.2em] text-[#A7A1FF]">Démo personnalisée</span>
                        <h2 class="mt-3 font-hero text-[32px] font-bold text-white sm:text-[38px]">Parlons de votre paie.</h2>
                        <p class="mt-3 max-w-md font-body-md text-body-md text-white/55">30 minutes avec un consultant RH. Sans engagement, réponse sous 24 h ouvrées.</p>

                        <form class="mt-7 flex flex-col gap-4" id="demo-form" novalidate>
                            <div class="grid gap-4 sm:grid-cols-2">
                                <div class="flex flex-col gap-1.5">
                                    <label class="font-label-md text-label-md font-medium text-white/70" for="demo-name">Nom complet *</label>
                                    <input class="h-11 rounded-xl border border-white/10 bg-white/5 px-3.5 font-body-md text-body-md text-white placeholder:text-white/25 transition-colors focus:border-[#8B7CFF] focus:bg-white/[0.07] focus:outline-none focus:ring-2 focus:ring-[#6D5BFF]/40" id="demo-name" name="name" placeholder="Amel Ben Salah" required type="text" autocomplete="name">
                                </div>
                                <div class="flex flex-col gap-1.5">
                                    <label class="font-label-md text-label-md font-medium text-white/70" for="demo-email">E-mail professionnel *</label>
                                    <input class="h-11 rounded-xl border border-white/10 bg-white/5 px-3.5 font-body-md text-body-md text-white placeholder:text-white/25 transition-colors focus:border-[#8B7CFF] focus:bg-white/[0.07] focus:outline-none focus:ring-2 focus:ring-[#6D5BFF]/40" id="demo-email" name="email" placeholder="amel@entreprise.tn" required type="email" autocomplete="email">
                                </div>
                            </div>
                            <div class="grid gap-4 sm:grid-cols-2">
                                <div class="flex flex-col gap-1.5">
                                    <label class="font-label-md text-label-md font-medium text-white/70" for="demo-company">Entreprise *</label>
                                    <input class="h-11 rounded-xl border border-white/10 bg-white/5 px-3.5 font-body-md text-body-md text-white placeholder:text-white/25 transition-colors focus:border-[#8B7CFF] focus:bg-white/[0.07] focus:outline-none focus:ring-2 focus:ring-[#6D5BFF]/40" id="demo-company" name="company" placeholder="Carthage Tech Solutions" required type="text" autocomplete="organization">
                                </div>
                                <div class="flex flex-col gap-1.5">
                                    <label class="font-label-md text-label-md font-medium text-white/70" for="demo-phone">Téléphone</label>
                                    <input class="h-11 rounded-xl border border-white/10 bg-white/5 px-3.5 font-body-md text-body-md text-white placeholder:text-white/25 transition-colors focus:border-[#8B7CFF] focus:bg-white/[0.07] focus:outline-none focus:ring-2 focus:ring-[#6D5BFF]/40" id="demo-phone" name="phone" placeholder="+216 20 000 000" type="tel" autocomplete="tel">
                                </div>
                            </div>
                            <div class="flex flex-col gap-1.5">
                                <label class="font-label-md text-label-md font-medium text-white/70" for="demo-size">Taille de l'équipe</label>
                                <select class="h-11 rounded-xl border border-white/10 bg-white/5 px-3 font-body-md text-body-md text-white transition-colors focus:border-[#8B7CFF] focus:bg-white/[0.07] focus:outline-none focus:ring-2 focus:ring-[#6D5BFF]/40 [&>option]:bg-[#0B1020]" id="demo-size" name="size">
                                    <option value="1-20">1 à 20 collaborateurs</option>
                                    <option value="21-50" selected>21 à 50 collaborateurs</option>
                                    <option value="51-150">51 à 150 collaborateurs</option>
                                    <option value="150+">Plus de 150 collaborateurs</option>
                                </select>
                            </div>
                            <button class="btn-glow btn-shine mt-1 flex h-12 items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-[#6D5BFF] to-[#8B5CF6] font-label-lg text-label-lg font-semibold text-white transition-transform active:scale-[0.98]" data-magnetic type="submit">
                                Demander ma démo
                                <span class="material-symbols-outlined text-[20px]">arrow_forward</span>
                            </button>
                            <p class="text-center font-label-sm text-label-sm text-white/35">Vos données restent confidentielles et hébergées en Tunisie (INPDP).</p>
                        </form>

                        <div class="hidden flex-col items-center gap-3 rounded-2xl border border-[#4EDEA3]/25 bg-[#4EDEA3]/10 p-8 text-center" id="demo-success" role="status" aria-live="polite">
                            <span class="flex h-16 w-16 items-center justify-center rounded-[999px] bg-[#4EDEA3]/15 text-[#8FEBC8]">
                                <svg class="check-draw h-8 w-8" viewBox="0 0 52 52" fill="none" aria-hidden="true">
                                    <circle cx="26" cy="26" r="24" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
                                    <path d="M15 27.5 22.5 35 37 19" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                            <h3 class="font-headline-sm text-headline-sm font-semibold text-white">Merci, votre demande est enregistrée.</h3>
                            <p class="max-w-sm font-body-md text-body-md text-white/55">Notre consultant RH vous contacte sous 24 h ouvrées pour planifier votre démonstration.</p>
                        </div>
                    </div>

                    <div class="relative hidden min-h-[540px] lg:block">
                        <img class="absolute inset-0 h-full w-full object-cover" src="{{ asset('images/office-team.webp') }}" alt="Équipe RH collaborant dans un bureau à Tunis" width="1400" height="875" loading="lazy" decoding="async">
                        <div class="absolute inset-0 bg-gradient-to-t from-[#05070F] via-[#05070F]/25 to-transparent"></div>
                        <div class="absolute inset-x-6 bottom-6 rounded-2xl border border-white/15 bg-white/10 p-5 backdrop-blur-md">
                            <div class="flex items-center gap-2">
                                <span class="flex -space-x-2">
                                    <img class="h-8 w-8 rounded-[999px] border-2 border-white/20 object-cover" src="{{ asset('images/testimonials/amina.webp') }}" alt="" width="560" height="560" loading="lazy" decoding="async">
                                    <img class="h-8 w-8 rounded-[999px] border-2 border-white/20 object-cover" src="{{ asset('images/testimonials/karim.webp') }}" alt="" width="560" height="560" loading="lazy" decoding="async">
                                    <img class="h-8 w-8 rounded-[999px] border-2 border-white/20 object-cover" src="{{ asset('images/testimonials/sonia.webp') }}" alt="" width="560" height="560" loading="lazy" decoding="async">
                                </span>
                                <p class="font-label-sm text-label-sm font-semibold text-white/70">450+ équipes RH accompagnées</p>
                            </div>
                            <p class="mt-3 font-headline-sm text-headline-sm font-semibold text-white">Un consultant RH dédié, à Tunis.</p>
                            <div class="mt-3 flex items-center gap-2 font-label-sm text-label-sm text-white/70">
                                <span class="material-symbols-outlined text-[18px] text-[#4EDEA3]">schedule</span>
                                Réponse sous 24 h ouvrées
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

</main>

<footer class="stage border-t border-white/10">
    <div class="mx-auto max-w-[1280px] px-margin-mobile py-14 md:px-margin">
        <div class="grid gap-10 md:grid-cols-[1.4fr_1fr_1fr_1fr]">
            <div>
                <div class="flex items-center gap-2">
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-[#8B7CFF] to-[#4F46E5]">
                        <span class="material-symbols-outlined text-[22px] text-white">corporate_fare</span>
                    </span>
                    <div>
                        <p class="font-hero text-lg font-bold leading-none text-white">HRM Pro</p>
                        <p class="mt-0.5 font-label-sm text-label-sm font-semibold uppercase tracking-[0.18em] text-[#A7A1FF]">Tunisie</p>
                    </div>
                </div>
                <p class="mt-4 max-w-xs font-body-sm text-body-sm text-white/45">La plateforme SIRH &amp; Paie conforme au Code du travail tunisien, à la Loi 66-27 et aux exigences INPDP.</p>
                <div class="mt-5 flex items-center gap-2 font-label-sm text-label-sm text-white/45">
                    <span class="h-2 w-2 rounded-[999px] bg-[#4EDEA3]"></span>
                    Tous les systèmes opérationnels
                </div>
            </div>
            <div>
                <p class="font-label-md text-label-md font-semibold uppercase tracking-wider text-white/80">Produit</p>
                <ul class="mt-3 space-y-2 font-body-sm text-body-sm text-white/45">
                    <li><a class="transition-colors hover:text-white" href="#experience">Solutions RH &amp; Paie</a></li>
                    <li><a class="transition-colors hover:text-white" href="#fonctionnalites">Fonctionnalités</a></li>
                    <li><a class="transition-colors hover:text-white" href="#tarifs">Tarifs</a></li>
                    <li><a class="transition-colors hover:text-white" href="#demo-video">Démo</a></li>
                </ul>
            </div>
            <div>
                <p class="font-label-md text-label-md font-semibold uppercase tracking-wider text-white/80">Entreprise</p>
                <ul class="mt-3 space-y-2 font-body-sm text-body-sm text-white/45">
                    <li><a class="transition-colors hover:text-white" href="#temoignages">Témoignages</a></li>
                    <li><a class="transition-colors hover:text-white" href="{{ route('login') }}">Espace client</a></li>
                    <li><a class="transition-colors hover:text-white" href="#demo">Nous contacter</a></li>
                </ul>
            </div>
            <div>
                <p class="font-label-md text-label-md font-semibold uppercase tracking-wider text-white/80">Conformité</p>
                <ul class="mt-3 space-y-2 font-body-sm text-body-sm text-white/45">
                    <li>Code du travail TN</li>
                    <li>CNSS &amp; IRPP 2025</li>
                    <li>Télé-déclaration SIBTEL</li>
                    <li>INPDP · Loi 66-27</li>
                </ul>
            </div>
        </div>

        <p class="mt-12 select-none bg-gradient-to-b from-white/[0.09] to-transparent bg-clip-text font-hero text-[16vw] font-extrabold leading-[0.8] tracking-tighter text-transparent md:text-[11vw]" aria-hidden="true">HRM PRO</p>

        <div class="mt-8 flex flex-col items-center justify-between gap-3 border-t border-white/10 pt-6 font-label-sm text-label-sm text-white/35 sm:flex-row">
            <p>&copy; {{ date('Y') }} HRM Pro · Carthage Tech Solutions. Tous droits réservés.</p>
            <p class="flex items-center gap-1.5"><span class="h-2 w-2 rounded-[999px] bg-[#4EDEA3]"></span> Conçu et hébergé en Tunisie</p>
        </div>
    </div>
</footer>

<script>
    (() => {
        const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        const finePointer = window.matchMedia('(pointer: fine)').matches;
        const $ = (selector, scope = document) => scope.querySelector(selector);
        const $$ = (selector, scope = document) => Array.from(scope.querySelectorAll(selector));

        // Scroll progress + sticky header
        const header = $('#site-header');
        const progress = $('#scroll-progress');
        const syncScroll = () => {
            const max = document.documentElement.scrollHeight - window.innerHeight;
            if (progress) progress.style.width = (max > 0 ? (window.scrollY / max) * 100 : 0).toFixed(2) + '%';
            header.classList.toggle('is-scrolled', window.scrollY > 24);
        };
        syncScroll();
        window.addEventListener('scroll', syncScroll, { passive: true });
        window.addEventListener('resize', syncScroll, { passive: true });

        // Mobile menu
        const mobileToggle = $('[data-mobile-toggle]');
        const mobileMenu = $('#mobile-menu');
        const mobileIcon = $('[data-mobile-icon]');
        if (mobileToggle && mobileMenu) {
            const setMenu = (open) => {
                mobileMenu.classList.toggle('hidden', !open);
                mobileToggle.setAttribute('aria-expanded', String(open));
                if (mobileIcon) mobileIcon.textContent = open ? 'close' : 'menu';
            };
            mobileToggle.addEventListener('click', () => setMenu(mobileMenu.classList.contains('hidden')));
            $$('#mobile-menu a').forEach((link) => link.addEventListener('click', () => setMenu(false)));
            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape' && !mobileMenu.classList.contains('hidden')) setMenu(false);
            });
        }

        // Active nav link
        const navLinks = $$('[data-nav-link]');
        const navSections = navLinks.map((link) => $(link.getAttribute('href'))).filter(Boolean);
        if (navSections.length && 'IntersectionObserver' in window) {
            const visibleSections = new Set();
            const navObserver = new IntersectionObserver((entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) visibleSections.add(entry.target.id);
                    else visibleSections.delete(entry.target.id);
                });
                const current = navLinks.map((link) => link.getAttribute('href').slice(1)).find((id) => visibleSections.has(id));
                navLinks.forEach((link) => link.classList.toggle('is-current', !!current && link.getAttribute('href') === '#' + current));
            }, { rootMargin: '-45% 0px -50% 0px', threshold: 0 });
            navSections.forEach((section) => navObserver.observe(section));
        }

        // Reveal on scroll
        const revealables = $$('[data-reveal]');
        if (!reduceMotion && 'IntersectionObserver' in window) {
            const revealObserver = new IntersectionObserver((entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-visible');
                        revealObserver.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.12, rootMargin: '0px 0px -6% 0px' });
            revealables.forEach((element) => revealObserver.observe(element));
        } else {
            revealables.forEach((element) => element.classList.add('is-visible'));
        }

        // Counters
        const animateCounter = (element) => {
            const target = parseFloat(element.dataset.target || '0');
            const decimals = parseInt(element.dataset.decimals || '0', 10);
            const suffix = element.dataset.suffix || '';
            const formatter = new Intl.NumberFormat('fr-FR', {
                minimumFractionDigits: decimals,
                maximumFractionDigits: decimals,
            });
            const duration = reduceMotion ? 0 : 1600;
            const start = performance.now();
            const step = (now) => {
                const progressValue = duration === 0 ? 1 : Math.min(1, (now - start) / duration);
                const eased = 1 - Math.pow(1 - progressValue, 3);
                element.textContent = formatter.format(target * eased) + suffix;
                if (progressValue < 1) requestAnimationFrame(step);
            };
            requestAnimationFrame(step);
        };
        const counters = $$('[data-counter]');
        if (counters.length) {
            if (reduceMotion || !('IntersectionObserver' in window)) {
                counters.forEach(animateCounter);
            } else {
                const counterObserver = new IntersectionObserver((entries) => {
                    entries.forEach((entry) => {
                        if (entry.isIntersecting) {
                            animateCounter(entry.target);
                            counterObserver.unobserve(entry.target);
                        }
                    });
                }, { threshold: 0.5 });
                counters.forEach((counter) => counterObserver.observe(counter));
            }
        }

        // Scroll-driven product tour
        const tour = $('[data-tour]');
        if (tour && window.matchMedia('(min-width: 1024px)').matches) {
            const steps = $$('[data-tour-step]', tour);
            const screens = $$('[data-tour-screen]', tour);
            const segments = $$('.tour-seg', tour);
            const caption = $('#tour-caption');
            const kpi = $('#tour-kpi');
            let current = -1;

            const activateTour = (index) => {
                if (index === current || !steps[index]) return;
                current = index;
                steps.forEach((step, i) => step.classList.toggle('is-active', i === index));
                screens.forEach((screen, i) => screen.classList.toggle('is-active', i === index));
                segments.forEach((segment, i) => segment.classList.toggle('is-active', i === index));
                if (caption) caption.textContent = steps[index].dataset.tourCaption || '';
                if (kpi) kpi.textContent = steps[index].dataset.tourKpi || '';
            };

            activateTour(0);
            const tourObserver = new IntersectionObserver((entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) activateTour(Number(entry.target.dataset.tourStep));
                });
            }, { rootMargin: '-46% 0px -46% 0px', threshold: 0 });
            steps.forEach((step) => tourObserver.observe(step));
        }

        // Hero 3D tilt + glare
        const tiltWrap = $('[data-tilt]');
        if (tiltWrap && !reduceMotion && finePointer) {
            const tilt = $('.tilt', tiltWrap);
            const glare = $('[data-glare]', tiltWrap);
            const resetTilt = () => {
                tilt.style.setProperty('--rx', '0deg');
                tilt.style.setProperty('--ry', '0deg');
            };
            tiltWrap.addEventListener('pointermove', (event) => {
                const rect = tiltWrap.getBoundingClientRect();
                const px = (event.clientX - rect.left) / rect.width - 0.5;
                const py = (event.clientY - rect.top) / rect.height - 0.5;
                tilt.style.setProperty('--ry', (px * 11).toFixed(2) + 'deg');
                tilt.style.setProperty('--rx', (-py * 8).toFixed(2) + 'deg');
                if (glare) {
                    glare.style.setProperty('--gx', ((px + 0.5) * 100).toFixed(1) + '%');
                    glare.style.setProperty('--gy', ((py + 0.5) * 100).toFixed(1) + '%');
                }
            });
            tiltWrap.addEventListener('pointerleave', resetTilt);
        }

        // Magnetic buttons
        if (!reduceMotion && finePointer) {
            $$('[data-magnetic]').forEach((element) => {
                element.addEventListener('pointermove', (event) => {
                    const rect = element.getBoundingClientRect();
                    const x = ((event.clientX - rect.left) / rect.width - 0.5) * 8;
                    const y = ((event.clientY - rect.top) / rect.height - 0.5) * 6;
                    element.style.transform = 'translate3d(' + x.toFixed(1) + 'px,' + y.toFixed(1) + 'px,0)';
                });
                element.addEventListener('pointerleave', () => { element.style.transform = ''; });
            });
        }

        // Cursor glow
        if (!reduceMotion && finePointer) {
            const glow = $('#cursor-glow');
            if (glow) {
                let targetX = window.innerWidth / 2;
                let targetY = window.innerHeight / 2;
                let currentX = targetX;
                let currentY = targetY;
                window.addEventListener('pointermove', (event) => {
                    targetX = event.clientX;
                    targetY = event.clientY;
                    glow.classList.add('is-on');
                }, { passive: true });
                const renderGlow = () => {
                    currentX += (targetX - currentX) * 0.12;
                    currentY += (targetY - currentY) * 0.12;
                    glow.style.transform = 'translate3d(' + currentX.toFixed(1) + 'px,' + currentY.toFixed(1) + 'px,0)';
                    requestAnimationFrame(renderGlow);
                };
                requestAnimationFrame(renderGlow);
            }
        }

        // Bento spotlight
        $$('[data-spotlight]').forEach((card) => {
            card.addEventListener('pointermove', (event) => {
                const rect = card.getBoundingClientRect();
                card.style.setProperty('--mx', (event.clientX - rect.left) + 'px');
                card.style.setProperty('--my', (event.clientY - rect.top) + 'px');
            });
        });

        // Hero video fallback
        const heroVideo = $('[data-hero-video]');
        if (heroVideo) {
            const connection = navigator.connection || {};
            const isSmallScreen = window.matchMedia('(max-width: 767px)').matches;
            if (reduceMotion || connection.saveData || isSmallScreen) {
                heroVideo.removeAttribute('autoplay');
                try { heroVideo.pause(); } catch (error) {}
                heroVideo.setAttribute('preload', 'none');
                const heroSource = heroVideo.querySelector('source');
                if (heroSource) heroSource.removeAttribute('src');
                try { heroVideo.load(); } catch (error) {}
            } else {
                const playback = heroVideo.play();
                if (playback && typeof playback.catch === 'function') {
                    playback.catch(() => heroVideo.classList.add('is-blocked'));
                }
            }
        }

        // Testimonials carousel
        const track = $('[data-carousel]');
        if (track) {
            const cards = $$('[data-carousel-card]', track);
            const dotsWrap = $('[data-carousel-dots]');
            const prev = $('[data-carousel-prev]');
            const next = $('[data-carousel-next]');
            const gap = 20;
            const stepSize = () => (cards[0] ? cards[0].getBoundingClientRect().width + gap : track.clientWidth);
            const dots = cards.map((_, index) => {
                const dot = document.createElement('button');
                dot.type = 'button';
                dot.className = 'carousel-dot';
                dot.setAttribute('aria-label', 'Aller au témoignage ' + (index + 1));
                dot.addEventListener('click', () => track.scrollTo({ left: index * stepSize(), behavior: reduceMotion ? 'auto' : 'smooth' }));
                if (dotsWrap) dotsWrap.appendChild(dot);
                return dot;
            });
            const updateCarousel = () => {
                const index = Math.round(track.scrollLeft / stepSize());
                dots.forEach((dot, i) => dot.classList.toggle('is-active', i === index));
                if (prev) prev.disabled = track.scrollLeft <= 2;
                if (next) next.disabled = track.scrollLeft >= track.scrollWidth - track.clientWidth - 2;
            };
            if (prev) prev.addEventListener('click', () => track.scrollBy({ left: -stepSize(), behavior: reduceMotion ? 'auto' : 'smooth' }));
            if (next) next.addEventListener('click', () => track.scrollBy({ left: stepSize(), behavior: reduceMotion ? 'auto' : 'smooth' }));
            track.addEventListener('scroll', updateCarousel, { passive: true });
            window.addEventListener('resize', updateCarousel, { passive: true });
            updateCarousel();
        }

        // Demo video (lazy)
        const videoFrame = $('[data-video-frame]');
        if (videoFrame) {
            const video = $('video', videoFrame);
            const playButton = $('[data-video-play]', videoFrame);
            const poster = $('[data-video-poster]', videoFrame);
            playButton?.addEventListener('click', () => {
                const source = $('source', video);
                if (source && source.dataset.src && !source.getAttribute('src')) {
                    source.src = source.dataset.src;
                    video.load();
                }
                video.classList.remove('hidden');
                poster?.classList.add('hidden');
                playButton.classList.add('hidden');
                video.controls = true;
                video.play().catch(() => {});
            });
        }

        // Demo form + confetti
        const spawnConfetti = () => {
            if (reduceMotion) return;
            const layer = $('#confetti');
            if (!layer) return;
            const colors = ['#8B7CFF', '#C4B5FD', '#7DD3FC', '#4EDEA3', '#F5B84B', '#FFFFFF'];
            for (let i = 0; i < 36; i++) {
                const piece = document.createElement('span');
                piece.className = 'confetti';
                piece.style.left = (Math.random() * 100).toFixed(2) + '%';
                piece.style.background = colors[i % colors.length];
                piece.style.setProperty('--dx', (Math.random() * 260 - 130).toFixed(0) + 'px');
                piece.style.setProperty('--dur', (2.1 + Math.random() * 1.7).toFixed(2) + 's');
                piece.style.setProperty('--delay', (Math.random() * 0.45).toFixed(2) + 's');
                piece.style.setProperty('--rot', (Math.random() * 900 - 450).toFixed(0) + 'deg');
                layer.appendChild(piece);
                window.setTimeout(() => piece.remove(), 4500);
            }
        };

        const demoForm = $('#demo-form');
        if (demoForm) {
            demoForm.addEventListener('submit', (event) => {
                event.preventDefault();
                if (!demoForm.reportValidity()) return;
                demoForm.classList.add('hidden');
                const success = $('#demo-success');
                if (success) {
                    success.classList.remove('hidden');
                    success.classList.add('flex');
                    success.scrollIntoView({ behavior: reduceMotion ? 'auto' : 'smooth', block: 'center' });
                }
                spawnConfetti();
            });
        }
    })();
</script>

</body>
</html>
