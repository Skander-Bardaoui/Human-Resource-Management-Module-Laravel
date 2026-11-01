<!DOCTYPE html>

<html lang="fr"><head><meta charset="utf-8"/><meta content="width=device-width, initial-scale=1.0" name="viewport"/><meta content="web_standard" name="shell-type"/><title>HRM Pro - Logiciel RH &amp; Paie Tunisien</title><link href="https://fonts.googleapis.com" rel="preconnect"/><link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/><link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&amp;display=swap" rel="stylesheet"/><link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/><style>@layer base{html,body{margin:0;padding:0;}body{overscroll-behavior:none;}main>:first-child{margin-top:0!important;}main>:last-child{margin-bottom:0!important;}}::-webkit-scrollbar{display:none;}</style>@include('components.theme-tokens', ['section' => 'bootstrap'])<script src="https://cdn.tailwindcss.com"></script>@include('components.theme-tokens', ['section' => 'config'])<meta content="light dark" name="color-scheme"/></head><body class="bg-background font-body-md text-on-surface antialiased"><header class="fixed top-0 left-0 right-0 z-50 bg-surface-container-lowest/90 backdrop-blur-xl shadow-[0_1px_8px_rgba(0,0,0,0.04)]"><div class="h-20 max-w-[1536px] mx-auto px-margin-mobile md:px-margin flex items-center justify-between"><div class="flex items-center gap-space-xl"><a class="flex items-center gap-space-xs" data-path="accueil" href="#"><div class="w-9 h-9 rounded-xl bg-primary-fill flex items-center justify-center shadow-[0_1px_3px_0_rgba(15,23,42,0.05)]"><span class="material-symbols-outlined text-on-primary text-[22px]">corporate_fare</span></div><div class="flex flex-col"><span class="font-headline-sm text-headline-sm text-on-surface leading-none font-bold tracking-tight">HRM Pro</span><span class="font-label-sm text-label-sm text-primary uppercase tracking-wider font-semibold">Tunisie</span></div></a><nav class="hidden xl:flex items-center gap-space-xs" data-active-classes="bg-surface-container-high text-primary font-semibold rounded-lg"><a class="px-space-md py-space-xs font-label-lg text-label-lg text-on-surface-variant hover:text-on-surface hover:bg-surface-container-low transition-all" data-path="fonctionnalites" href="#">Fonctionnalités</a><a class="px-space-md py-space-xs font-label-lg text-label-lg text-on-surface-variant hover:text-on-surface hover:bg-surface-container-low transition-all" data-path="solutions-rh-paie" href="#">Solutions RH &amp; Paie</a><a class="px-space-md py-space-xs font-label-lg text-label-lg text-on-surface-variant hover:text-on-surface hover:bg-surface-container-low transition-all" data-path="portail-collaborateur" href="#">Portail Collaborateur</a><a class="px-space-md py-space-xs font-label-lg text-label-lg text-on-surface-variant hover:text-on-surface hover:bg-surface-container-low transition-all" data-path="tarifs" href="#">Tarifs</a><a class="px-space-md py-space-xs font-label-lg text-label-lg text-on-surface-variant hover:text-on-surface hover:bg-surface-container-low transition-all" data-path="temoignages" href="#">Témoignages</a></nav></div><div class="flex items-center gap-space-md"><button aria-label="Changer le thème" aria-pressed="false" class="w-9 h-9 rounded-xl flex items-center justify-center text-on-surface-variant hover:bg-surface-container hover:text-on-surface transition-colors shrink-0" data-theme-toggle type="button"><span class="material-symbols-outlined text-[20px]" data-theme-icon>contrast</span></button><a class="hidden sm:inline-flex items-center justify-center px-space-md py-space-xs rounded-xl bg-surface-container-lowest text-on-surface-variant hover:text-on-surface hover:bg-surface-container-low font-label-lg text-label-lg shadow-[0_1px_3px_0_rgba(15,23,42,0.05)] transition-all" data-path="connexion" href="{{ route('login') }}">Espace Client / Connexion</a><a class="inline-flex items-center justify-center px-space-lg py-space-xs rounded-xl bg-primary-container text-on-primary hover:bg-primary-fill font-label-lg text-label-lg font-semibold shadow-[0_1px_3px_0_rgba(15,23,42,0.05)] transition-all active:scale-[0.98]" data-path="demander-une-demo" href="#">Demander une Démo gratuite</a><div class="w-8 h-8 rounded-full bg-primary-fill flex items-center justify-center shadow-[0_1px_3px_0_rgba(15,23,42,0.05)]"><span class="material-symbols-outlined text-on-primary text-[18px]">person</span></div></div></div></header><main class="w-full pt-20 bg-background"><div class="flex flex-col w-full">
<!-- Hero Section -->
<section class="relative w-full overflow-hidden bg-surface-container-low/60 py-space-xl lg:py-24">
<!-- Ambient Backdrop Accents -->
<div class="pointer-events-none absolute -top-24 left-1/2 -translate-x-1/2 w-[900px] h-[550px] bg-gradient-to-tr from-primary/10 via-primary-container/15 to-transparent blur-3xl rounded-full"></div>
<div class="pointer-events-none absolute top-1/3 -right-24 w-96 h-96 bg-tertiary-fixed-dim/20 blur-3xl rounded-full"></div>
<div class="relative max-w-[1536px] mx-auto px-margin-mobile md:px-margin">
<div class="grid grid-cols-1 lg:grid-cols-12 gap-space-xl items-center">
<!-- Hero Copy -->
<div class="lg:col-span-6 flex flex-col gap-space-lg">
<!-- Compliance Live Badge -->
<div class="inline-flex items-center gap-space-xs self-start px-space-md py-space-xs rounded-full bg-surface-container-lowest shadow-sm">
<span class="relative flex h-2.5 w-2.5">
<span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-tertiary opacity-75"></span>
<span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-tertiary-container"></span>
</span>
<span class="font-label-sm text-label-sm text-tertiary-container uppercase tracking-wider font-semibold">
              Conformité Tunisie 2025 • Certifié CNSS &amp; IRPP • Données hébergées en Tunisie
            </span>
</div>
<!-- Master Title -->
<h1 class="font-display-lg text-display-lg md:text-[44px] md:leading-[52px] text-on-surface font-bold tracking-tight">
            La plateforme SIRH &amp; Paie qui révolutionne la gestion de vos talents en Tunisie.
          </h1>
<!-- Subtitle -->
<p class="font-body-lg text-body-lg text-on-surface-variant max-w-xl">
            Automatisez la paie au millime près, gérez congés et pointage biométrique, et offrez un portail collaborateur moderne à vos équipes. Adossé à la réglementation locale tunisienne.
          </p>
<!-- CTAs Group -->
<div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-space-md pt-space-xs">
<a class="inline-flex items-center justify-center gap-space-xs px-space-xl py-3 rounded-xl bg-primary-container text-on-primary hover:bg-primary-fill font-label-lg text-label-lg font-semibold shadow-md transition-all active:scale-[0.98]" href="#demo">
<span>Demander une démo personnalisée</span>
<span class="material-symbols-outlined text-[18px]">arrow_forward</span>
</a>
<button class="inline-flex items-center justify-center gap-space-xs px-space-lg py-3 rounded-xl bg-surface-container-lowest text-on-surface hover:bg-surface-container-high font-label-lg text-label-lg shadow-sm transition-all" onclick="document.getElementById('dual-toggle').scrollIntoView({behavior: 'smooth'})">
<span class="material-symbols-outlined text-primary text-[20px]">play_circle</span>
<span>Découvrir le Portail Collaborateur</span>
</button>
</div>
<!-- Micro Social Proof -->
<div class="flex items-center gap-space-sm pt-space-xs text-on-surface-variant">
<div class="flex -space-x-2 shrink-0">
<img class="w-8 h-8 rounded-full object-cover shadow-sm" data-alt="Headshot of Tunisian HR Director professional woman smiling in modern office" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAy-DJRWurF1MPpzU73RYJKgG2AvsxmvNX-4_hWtOAHVih2WUKAc8ECYODLuJ3ObMwYETLj0XS_GG-wBK73fJ_In_V5R3roBJ3qG2Qh52DPyl10-7Qb6JtbuftfmkYNptgH3JWeTXbznTBTXJBsCTn-v22MeRRBkYjnDTnyuwupp7Eh8e-s37pqLqPEkcfglfVSmhD5QZdqWiX74NxPsqtAHC_nzG9nLR-Usr5o1xfC0r8nbxZPAsnB"/>
<img class="w-8 h-8 rounded-full object-cover shadow-sm" data-alt="Headshot of a Tunisian tech startup founder wearing navy blazer" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCWDoRBfcojJ6YZDcmy1eoHdM-2cfHkf9xhCiCK8GyAqBO9hHIe9btcvC2xY1KEizsdtOVdAp6DUTuS-kZBqV1eQNxxTi8YZ6-ChZxyzeNTNZPp4040MhBcjBnZDET9l3gDTghCZuS-SI7cGZTEPqk0nJwUnvKWnorfhlIEZm_pIU1vslKfQfJ_Fof-pALib14zmqpUGPku-5o9Ju4DwAoE1ZuhMvWVX1k3I7Fv9zgQ8TMMok4OaRCo"/>
<img class="w-8 h-8 rounded-full object-cover shadow-sm" data-alt="Headshot of Tunisian senior payroll manager in professional business attire" src="https://lh3.googleusercontent.com/aida-public/AB6AXuA9PHW30vsGsr7TusmZnllfdQSKZCpD5K4i_m_DeVRhpkP4ZsVdvNCn9Ulim9Guyr5QxAcVnTovD3adkLWfJ6UYOvOeLtng8a1LeC-aRCwCsM62GzT6MupAy5Ae9VFRLztl94Dj0kOY_Q-7qOkFjqpT8C4JbGqBNXVe6I8TdjyJCiAPl3YLPNsYBjHZjN3zXGlh8WB0um3wczeiJKkAvEP_ZqU1y0Dt9xZ_JbrbDKuDvINnIfvE3WPf"/>
</div>
<div class="flex flex-col">
<div class="flex items-center gap-1 text-tertiary-container">
<span class="material-symbols-outlined text-[16px]" style="font-variation-settings: 'FILL' 1;">star</span>
<span class="material-symbols-outlined text-[16px]" style="font-variation-settings: 'FILL' 1;">star</span>
<span class="material-symbols-outlined text-[16px]" style="font-variation-settings: 'FILL' 1;">star</span>
<span class="material-symbols-outlined text-[16px]" style="font-variation-settings: 'FILL' 1;">star</span>
<span class="material-symbols-outlined text-[16px]" style="font-variation-settings: 'FILL' 1;">star</span>
<span class="font-label-sm text-label-sm font-bold text-on-surface ml-1">4.9/5</span>
</div>
<span class="font-body-sm text-body-sm text-on-surface-variant">
                Rejoignez plus de 450+ entreprises tunisiennes (startups, PME &amp; groupes)
              </span>
</div>
</div>
</div>
<!-- Hero Visual Mockup: Layered Cockpit -->
<div class="lg:col-span-6 relative mt-space-lg lg:mt-0">
<div class="relative bg-surface-container-lowest rounded-2xl shadow-xl p-space-lg lg:p-space-xl">
<!-- Simulated Chrome Window Bar -->
<div class="flex items-center justify-between pb-space-md mb-space-md border-b-0">
<div class="flex items-center gap-2">
<span class="w-3 h-3 rounded-full bg-error/70 inline-block"></span>
<span class="w-3 h-3 rounded-full bg-secondary-fixed-dim inline-block"></span>
<span class="w-3 h-3 rounded-full bg-tertiary-fixed-dim inline-block"></span>
<span class="font-label-sm text-label-sm text-on-surface-variant ml-2 font-mono">hrm-pro.tn/app/tableau-de-bord</span>
</div>
<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-tertiary-fixed text-on-tertiary-fixed font-label-sm text-label-sm font-semibold">
<span class="w-1.5 h-1.5 rounded-full bg-tertiary inline-block"></span>
                Télédéclaration Télé-CNSS T1-2025 Prête
              </span>
</div>
<!-- Top KPI Tiles -->
<div class="grid grid-cols-3 gap-space-sm mb-space-lg">
<div class="bg-surface-container-low rounded-xl p-space-md">
<span class="font-label-sm text-label-sm text-on-surface-variant block uppercase">Masse Salariale Brute</span>
<span class="font-headline-md text-headline-md text-on-surface font-bold tracking-tight block mt-1">142,850 <span class="font-label-sm text-label-sm font-normal text-on-surface-variant">TND</span></span>
<span class="font-label-sm text-label-sm text-tertiary font-semibold flex items-center gap-0.5 mt-1">
<span class="material-symbols-outlined text-[14px]">trending_up</span> +3.2% N-1
                </span>
</div>
<div class="bg-surface-container-low rounded-xl p-space-md">
<span class="font-label-sm text-label-sm text-on-surface-variant block uppercase">Taux d'Assiduité</span>
<span class="font-headline-md text-headline-md text-on-surface font-bold tracking-tight block mt-1">96.4%</span>
<span class="font-label-sm text-label-sm text-tertiary font-semibold flex items-center gap-0.5 mt-1">
<span class="material-symbols-outlined text-[14px]">check_circle</span> 46 présents
                </span>
</div>
<div class="bg-surface-container-low rounded-xl p-space-md">
<span class="font-label-sm text-label-sm text-on-surface-variant block uppercase">Collaborateurs</span>
<span class="font-headline-md text-headline-md text-on-surface font-bold tracking-tight block mt-1">48</span>
<span class="font-label-sm text-label-sm text-primary font-semibold flex items-center gap-0.5 mt-1">
<span class="material-symbols-outlined text-[14px]">person_add</span> 3 SIVP ANETI
                </span>
</div>
</div>
<!-- Executive Quick Payroll Table Preview -->
<div class="bg-surface-container-lowest rounded-xl shadow-sm p-space-md mb-space-md">
<div class="flex items-center justify-between mb-space-sm">
<div class="flex items-center gap-space-xs">
<span class="material-symbols-outlined text-primary text-[20px]">calculate</span>
<span class="font-headline-sm text-headline-sm text-on-surface font-bold">Clôture Paie Mars 2025</span>
</div>
<span class="font-label-sm text-label-sm px-2.5 py-1 rounded-full bg-surface-container-high text-primary font-semibold">Barème IRPP 2025 Actif</span>
</div>
<!-- Mini Table Rows -->
<div class="space-y-2 text-left">
<div class="flex items-center justify-between p-2 rounded-lg bg-surface-container-low/70">
<div class="flex items-center gap-2">
<div class="w-7 h-7 rounded-full bg-primary-container text-on-primary flex items-center justify-center font-label-sm text-label-sm font-bold">KB</div>
<div>
<p class="font-label-lg text-label-lg text-on-surface font-semibold leading-none">Karim Bouazizi</p>
<p class="font-body-sm text-body-sm text-on-surface-variant text-[11px] leading-tight">Matricule: 0418 • CIN: 08914***</p>
</div>
</div>
<div class="text-right">
<p class="font-numeric-md text-numeric-md font-bold text-on-surface">3,240.500 TND</p>
<p class="font-label-sm text-label-sm text-tertiary-container font-medium">Net viré SIBTEL</p>
</div>
</div>
<div class="flex items-center justify-between p-2 rounded-lg bg-surface-container-low/70">
<div class="flex items-center gap-2">
<div class="w-7 h-7 rounded-full bg-secondary-fixed text-on-secondary-fixed flex items-center justify-center font-label-sm text-label-sm font-bold">SM</div>
<div>
<p class="font-label-lg text-label-lg text-on-surface font-semibold leading-none">Sarra Marzouki</p>
<p class="font-body-sm text-body-sm text-on-surface-variant text-[11px] leading-tight">Matricule: 0419 • SIVP Exonéré</p>
</div>
</div>
<div class="text-right">
<p class="font-numeric-md text-numeric-md font-bold text-on-surface">1,850.000 TND</p>
<p class="font-label-sm text-label-sm text-primary font-medium">Net viré SIBTEL</p>
</div>
</div>
</div>
</div>
<!-- Overlapping Floating Collaborateur Self-Service Card -->
<div class="sm:absolute sm:-bottom-8 sm:-left-8 bg-surface-container-lowest rounded-xl shadow-xl p-space-md sm:w-80 border-0 bg-gradient-to-br from-surface-container-lowest to-surface-container-low">
<div class="flex items-center justify-between mb-space-xs">
<span class="inline-flex items-center gap-1 font-label-sm text-label-sm font-semibold text-primary">
<span class="material-symbols-outlined text-[16px]">fingerprint</span>
                  Badgeuse Virtuelle
                </span>
<span class="px-2 py-0.5 rounded-full bg-tertiary-fixed text-on-tertiary-fixed font-label-sm text-label-sm font-bold">08:58 • À l'heure</span>
</div>
<p class="font-body-sm text-body-sm text-on-surface-variant mb-space-xs">Pointage géolocalisé - Bureau Les Berges du Lac 2</p>
<div class="flex items-center justify-between pt-space-xs bg-surface-container-lowest rounded-lg p-2 shadow-sm">
<div class="flex items-center gap-2">
<span class="material-symbols-outlined text-primary text-[20px]">verified</span>
<span class="font-label-sm text-label-sm text-on-surface font-semibold">Fiche de paie TunTrust</span>
</div>
<span class="font-label-sm text-label-sm text-primary font-bold">Mars 2025.pdf</span>
</div>
</div>
</div>
</div>
</div>
</div>
</section>
<!-- Social Proof Logos Section -->
<section class="w-full bg-surface-container-lowest py-space-lg shadow-sm">
<div class="max-w-[1536px] mx-auto px-margin-mobile md:px-margin text-center">
<p class="font-label-md text-label-md text-on-surface-variant uppercase tracking-wider font-semibold mb-space-lg">
        Ils font confiance à HRM Pro pour leur gestion sociale &amp; paie en Tunisie
      </p>
<div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-7 gap-space-md items-center justify-items-center opacity-80">
<div class="flex items-center gap-2 py-space-xs px-space-md rounded-lg bg-surface-container-low hover:opacity-100 transition-opacity">
<span class="material-symbols-outlined text-primary text-[22px]">domain</span>
<span class="font-label-lg text-label-lg font-bold text-on-surface">Carthage Tech</span>
</div>
<div class="flex items-center gap-2 py-space-xs px-space-md rounded-lg bg-surface-container-low hover:opacity-100 transition-opacity">
<span class="material-symbols-outlined text-secondary text-[22px]">account_balance</span>
<span class="font-label-lg text-label-lg font-bold text-on-surface">BIAT Capital</span>
</div>
<div class="flex items-center gap-2 py-space-xs px-space-md rounded-lg bg-surface-container-low hover:opacity-100 transition-opacity">
<span class="material-symbols-outlined text-tertiary-container text-[22px]">satellite_alt</span>
<span class="font-label-lg text-label-lg font-bold text-on-surface">Telnet</span>
</div>
<div class="flex items-center gap-2 py-space-xs px-space-md rounded-lg bg-surface-container-low hover:opacity-100 transition-opacity">
<span class="material-symbols-outlined text-primary-container text-[22px]">hub</span>
<span class="font-label-lg text-label-lg font-bold text-on-surface">Smart Tunisia</span>
</div>
<div class="flex items-center gap-2 py-space-xs px-space-md rounded-lg bg-surface-container-low hover:opacity-100 transition-opacity">
<span class="material-symbols-outlined text-secondary text-[22px]">assured_workload</span>
<span class="font-label-lg text-label-lg font-bold text-on-surface">Amen Bank</span>
</div>
<div class="flex items-center gap-2 py-space-xs px-space-md rounded-lg bg-surface-container-low hover:opacity-100 transition-opacity">
<span class="material-symbols-outlined text-primary text-[22px]">terminal</span>
<span class="font-label-lg text-label-lg font-bold text-on-surface">Wevioo</span>
</div>
<div class="flex items-center gap-2 py-space-xs px-space-md rounded-lg bg-surface-container-low hover:opacity-100 transition-opacity">
<span class="material-symbols-outlined text-tertiary text-[22px]">receipt_long</span>
<span class="font-label-lg text-label-lg font-bold text-on-surface">Expensya</span>
</div>
</div>
</div>
</section>
<!-- Two Pillars / Dual Experience Toggle Showcase -->
<section class="w-full py-space-xl lg:py-24 bg-background" id="dual-toggle">
<div class="max-w-[1536px] mx-auto px-margin-mobile md:px-margin">
<div class="flex flex-col items-center text-center max-w-3xl mx-auto mb-space-xl">
<span class="px-space-md py-space-xs rounded-full bg-primary-fixed text-on-primary-fixed font-label-sm text-label-sm uppercase tracking-wider font-bold mb-space-sm">
          Double Interface Unifiée
        </span>
<h2 class="font-headline-lg text-headline-lg md:text-[34px] md:leading-[42px] font-bold text-on-surface tracking-tight">
          Une seule plateforme. Deux expériences conçues sur-mesure.
        </h2>
<p class="font-body-lg text-body-lg text-on-surface-variant mt-space-sm">
          Offrez une rigueur absolue à vos équipes administratives RH tout en enchantant le quotidien professionnel de vos collaborateurs sur desktop et mobile.
        </p>
<!-- Dynamic Toggle Segment -->
<div class="inline-flex p-1.5 rounded-2xl bg-surface-container-high mt-space-lg shadow-sm">
<button class="px-space-xl py-2.5 rounded-xl font-label-lg text-label-lg font-semibold bg-surface-container-lowest text-primary shadow-sm transition-all" id="tab-btn-rh" onclick="switchExperience('rh')">
<span class="flex items-center gap-2">
<span class="material-symbols-outlined text-[18px]">admin_panel_settings</span>
              Espace RH &amp; Direction
            </span>
</button>
<button class="px-space-xl py-2.5 rounded-xl font-label-lg text-label-lg font-semibold text-on-surface-variant hover:text-on-surface transition-all" id="tab-btn-collab" onclick="switchExperience('collab')">
<span class="flex items-center gap-2">
<span class="material-symbols-outlined text-[18px]">badge</span>
              Portail Collaborateur (Self-Service)
            </span>
</button>
</div>
</div>
<!-- Experience 1: RH & Direction Pane -->
<div class="grid grid-cols-1 lg:grid-cols-12 gap-space-xl items-center" id="pane-rh">
<div class="lg:col-span-5 flex flex-col gap-space-md">
<div class="flex items-center gap-2 text-primary font-label-md text-label-md uppercase font-bold tracking-wider">
<span class="material-symbols-outlined text-[20px]">account_tree</span>
            Pilotage Social &amp; Légal
          </div>
<h3 class="font-headline-lg text-headline-lg font-bold text-on-surface">
            Maîtrisez la paie tunisienne sans aucun tableur Excel
          </h3>
<p class="font-body-md text-body-md text-on-surface-variant">
            HRM Pro prend en charge la totalité de vos obligations légales : calculs précis au millime tunisien (TND), génération instantanée des fichiers bancaires par télécompensation SIBTEL et production des états comptables.
          </p>
<div class="space-y-space-md mt-space-xs">
<div class="flex items-start gap-space-sm bg-surface-container-lowest p-space-md rounded-xl shadow-sm">
<div class="w-9 h-9 rounded-lg bg-surface-container-high text-primary flex items-center justify-center shrink-0">
<span class="material-symbols-outlined text-[20px]">percent</span>
</div>
<div>
<h4 class="font-headline-sm text-headline-sm font-bold text-on-surface">Moteur Fiscal &amp; Cotisations 2025</h4>
<p class="font-body-sm text-body-sm text-on-surface-variant">Intégration du barème progressif IRPP 2025, CNSS Salariale (9.18%), CNSS Patronale (16.57%) et Contribution Sociale de Solidarité (CSS 1%).</p>
</div>
</div>
<div class="flex items-start gap-space-sm bg-surface-container-lowest p-space-md rounded-xl shadow-sm">
<div class="w-9 h-9 rounded-lg bg-surface-container-high text-tertiary flex items-center justify-center shrink-0">
<span class="material-symbols-outlined text-[20px]">cloud_upload</span>
</div>
<div>
<h4 class="font-headline-sm text-headline-sm font-bold text-on-surface">Télé-déclaration Télé-CNSS en 1 Clic</h4>
<p class="font-body-sm text-body-sm text-on-surface-variant">Export du fichier normé prêt à téléverser sur le portail de la CNSS tunisienne, avec vérification automatique de concordance.</p>
</div>
</div>
<div class="flex items-start gap-space-sm bg-surface-container-lowest p-space-md rounded-xl shadow-sm">
<div class="w-9 h-9 rounded-lg bg-surface-container-high text-secondary flex items-center justify-center shrink-0">
<span class="material-symbols-outlined text-[20px]">security</span>
</div>
<div>
<h4 class="font-headline-sm text-headline-sm font-bold text-on-surface">Gestion Documentaire Conforme INPDP</h4>
<p class="font-body-sm text-body-sm text-on-surface-variant">Contrats SIVP, Karama, CDI, CDD, avenants et CIN stockés selon la Loi n° 2004-63 sur la protection des données personnelles.</p>
</div>
</div>
</div>
</div>
<div class="lg:col-span-7 bg-surface-container-lowest p-space-xl rounded-2xl shadow-xl">
<div class="flex items-center justify-between pb-space-md mb-space-md">
<div>
<p class="font-headline-sm text-headline-sm font-bold text-on-surface">Simulation Bulletin de Paie Réglementaire</p>
<p class="font-body-sm text-body-sm text-on-surface-variant">Convention Collective Métallurgie &amp; TIC Tunisie</p>
</div>
<span class="px-space-sm py-1 rounded-full bg-tertiary-fixed text-on-tertiary-fixed font-label-sm text-label-sm font-bold">Statut : Validé</span>
</div>
<div class="bg-surface-container-low rounded-xl p-space-md space-y-space-xs font-mono text-[13px]">
<div class="flex justify-between text-on-surface-variant">
<span>Salaire de Base Brut</span>
<span class="font-bold text-on-surface font-numeric-md text-numeric-md">4,200.000 TND</span>
</div>
<div class="flex justify-between text-on-surface-variant">
<span>Prime de Présence &amp; Transport</span>
<span class="font-numeric-md text-numeric-md">180.000 TND</span>
</div>
<div class="flex justify-between text-on-surface-variant">
<span>Heures Supplémentaires (Majorées 150%)</span>
<span class="font-numeric-md text-numeric-md">245.500 TND</span>
</div>
<div class="h-px bg-surface-container-high my-space-xs"></div>
<div class="flex justify-between text-error font-semibold">
<span>Retenue CNSS Salarié (9.18%)</span>
<span class="font-numeric-md text-numeric-md">- 424.621 TND</span>
</div>
<div class="flex justify-between text-error font-semibold">
<span>Retenue IRPP (Nouveau Barème 2025)</span>
<span class="font-numeric-md text-numeric-md">- 612.350 TND</span>
</div>
<div class="flex justify-between text-error font-semibold">
<span>Contribution Sociale Solidarité (CSS 1%)</span>
<span class="font-numeric-md text-numeric-md">- 41.900 TND</span>
</div>
<div class="h-px bg-surface-container-high my-space-xs"></div>
<div class="flex justify-between bg-surface-container-lowest p-space-sm rounded-lg font-bold text-primary text-[15px]">
<span>NET À PAYER COLLABORATEUR</span>
<span class="font-numeric-md text-numeric-md text-primary">3,546.629 TND</span>
</div>
<div class="flex justify-between text-on-surface-variant text-[11px] pt-1">
<span>Cotisations Patronales Totales (16.57% + AT 1.2%)</span>
<span class="font-numeric-md text-numeric-md">821.848 TND</span>
</div>
</div>
</div>
</div>
<!-- Experience 2: Collaborateur Pane (Hidden initially, togglable) -->
<div class="hidden grid grid-cols-1 lg:grid-cols-12 gap-space-xl items-center" id="pane-collab">
<div class="lg:col-span-5 flex flex-col gap-space-md">
<div class="flex items-center gap-2 text-primary font-label-md text-label-md uppercase font-bold tracking-wider">
<span class="material-symbols-outlined text-[20px]">smartphone</span>
            Autonomie &amp; Expérience Collaborateur
          </div>
<h3 class="font-headline-lg text-headline-lg font-bold text-on-surface">
            Libérez votre service RH des sollicitations récurrentes
          </h3>
<p class="font-body-md text-body-md text-on-surface-variant">
            Chaque salarié dispose d'un espace sécurisé disponible sur smartphone et navigateur pour consulter ses fiches de paie, planifier ses congés et signer ses documents légaux.
          </p>
<div class="space-y-space-md mt-space-xs">
<div class="flex items-start gap-space-sm bg-surface-container-lowest p-space-md rounded-xl shadow-sm">
<div class="w-9 h-9 rounded-lg bg-surface-container-high text-primary flex items-center justify-center shrink-0">
<span class="material-symbols-outlined text-[20px]">lock</span>
</div>
<div>
<h4 class="font-headline-sm text-headline-sm font-bold text-on-surface">Coffre-fort Bulletins de Paie 24/7</h4>
<p class="font-body-sm text-body-sm text-on-surface-variant">Téléchargement immédiat des fiches chiffrées avec cachet électronique et traçabilité inviolable.</p>
</div>
</div>
<div class="flex items-start gap-space-sm bg-surface-container-lowest p-space-md rounded-xl shadow-sm">
<div class="w-9 h-9 rounded-lg bg-surface-container-high text-tertiary flex items-center justify-center shrink-0">
<span class="material-symbols-outlined text-[20px]">calendar_month</span>
</div>
<div>
<h4 class="font-headline-sm text-headline-sm font-bold text-on-surface">Solde de Congés Ouvrés en Direct</h4>
<p class="font-body-sm text-body-sm text-on-surface-variant">Calcul automatisé du droit légal (Code du travail tunisien : 1.5 jour par mois de travail effectif), congés maternité et maladie.</p>
</div>
</div>
<div class="flex items-start gap-space-sm bg-surface-container-lowest p-space-md rounded-xl shadow-sm">
<div class="w-9 h-9 rounded-lg bg-surface-container-high text-secondary flex items-center justify-center shrink-0">
<span class="material-symbols-outlined text-[20px]">qr_code_2</span>
</div>
<div>
<h4 class="font-headline-sm text-headline-sm font-bold text-on-surface">Attestations Signées avec QR Code</h4>
<p class="font-body-sm text-body-sm text-on-surface-variant">Attestation de travail, domiciliation de salaire bancaire et bordereau de congés générés en 5 secondes chrono.</p>
</div>
</div>
</div>
</div>
<div class="lg:col-span-7 bg-surface-container-lowest p-space-xl rounded-2xl shadow-xl">
<div class="max-w-md mx-auto bg-surface-container-low rounded-2xl p-space-lg shadow-inner">
<div class="flex items-center justify-between pb-space-sm border-b border-surface-container-high">
<div class="flex items-center gap-2">
<div class="w-10 h-10 rounded-full bg-primary-fill flex items-center justify-center text-on-primary font-bold">YB</div>
<div>
<h4 class="font-headline-sm text-headline-sm font-bold text-on-surface">Yassine Ben Amor</h4>
<p class="font-body-sm text-body-sm text-on-surface-variant">Ingénieur Logiciel Senior</p>
</div>
</div>
<span class="material-symbols-outlined text-primary text-[24px]">notifications</span>
</div>
<!-- Balances Widget -->
<div class="grid grid-cols-2 gap-space-sm my-space-md">
<div class="bg-surface-container-lowest p-space-sm rounded-xl">
<span class="font-label-sm text-label-sm text-on-surface-variant block">Solde Congés Payés</span>
<span class="font-headline-md text-headline-md font-bold text-tertiary-container block">18.5 Jours</span>
</div>
<div class="bg-surface-container-lowest p-space-sm rounded-xl">
<span class="font-label-sm text-label-sm text-on-surface-variant block">Récupération / Heures Sup</span>
<span class="font-headline-md text-headline-md font-bold text-primary block">6.0 Heures</span>
</div>
</div>
<!-- Quick Action Interactive Card -->
<div class="bg-surface-container-lowest p-space-md rounded-xl space-y-space-xs shadow-sm">
<div class="flex items-center justify-between">
<span class="font-label-lg text-label-lg font-bold text-on-surface">Demande d'absence immédiate</span>
<span class="font-label-sm text-label-sm text-primary font-semibold">Workflow N+1</span>
</div>
<div class="grid grid-cols-2 gap-2 text-left pt-2">
<div class="bg-surface-container-low p-2 rounded-lg">
<span class="font-label-sm text-label-sm text-on-surface-variant block">Début</span>
<span class="font-label-lg text-label-lg font-semibold text-on-surface">14 Avr 2025</span>
</div>
<div class="bg-surface-container-low p-2 rounded-lg">
<span class="font-label-sm text-label-sm text-on-surface-variant block">Retour</span>
<span class="font-label-lg text-label-lg font-semibold text-on-surface">18 Avr 2025</span>
</div>
</div>
<button class="w-full mt-2 py-2 rounded-lg bg-primary-container text-on-primary font-label-md text-label-md font-bold">
                Transmettre pour approbation
              </button>
</div>
</div>
</div>
</div>
</div>
</section>
<!-- 4 Core Functional Pillars Grid -->
<section class="w-full py-space-xl lg:py-24 bg-surface-container-low/40">
<div class="max-w-[1536px] mx-auto px-margin-mobile md:px-margin">
<div class="flex flex-col items-center text-center max-w-3xl mx-auto mb-space-xl">
<span class="px-space-md py-space-xs rounded-full bg-secondary-fixed text-on-secondary-fixed font-label-sm text-label-sm uppercase tracking-wider font-bold mb-space-sm">
          Architecture Modulaire &amp; Puissante
        </span>
<h2 class="font-display-lg text-display-lg font-bold text-on-surface tracking-tight">
          Conçu spécifiquement pour les exigences des entreprises tunisiennes
        </h2>
<p class="font-body-lg text-body-lg text-on-surface-variant mt-space-sm">
          Un progiciel pensé dès le premier jour pour répondre au Code du Travail (Loi n° 66-27) et aux télétransmissions bancaires et étatiques locales.
        </p>
</div>
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-space-lg">
<!-- Pillar 1 -->
<div class="bg-surface-container-lowest p-space-xl rounded-2xl shadow-sm hover:shadow-md transition-shadow flex flex-col justify-between">
<div>
<div class="w-12 h-12 rounded-xl bg-primary/10 text-primary flex items-center justify-center mb-space-md">
<span class="material-symbols-outlined text-[28px]">receipt</span>
</div>
<h3 class="font-headline-sm text-headline-sm font-bold text-on-surface mb-space-xs">
              Moteur de Paie &amp; Télé-déclarations
            </h3>
<p class="font-body-md text-body-md text-on-surface-variant mb-space-md">
              Génération automatique des fichiers bancaires conformes à la norme SIBTEL pour toutes les banques tunisiennes (BIAT, Attijari, BH, STB, UIB, etc.). Livre de paie paraphé et déclarations trimestrielles CNSS.
            </p>
</div>
<ul class="space-y-1.5 text-on-surface-variant font-label-md text-label-md">
<li class="flex items-center gap-1.5">
<span class="material-symbols-outlined text-tertiary-container text-[16px]">check</span>
              Fichiers virement bancaire SIBTEL
            </li>
<li class="flex items-center gap-1.5">
<span class="material-symbols-outlined text-tertiary-container text-[16px]">check</span>
              Livre de paie paraphé conforme
            </li>
<li class="flex items-center gap-1.5">
<span class="material-symbols-outlined text-tertiary-container text-[16px]">check</span>
              Gestion des avances et acomptes
            </li>
</ul>
</div>
<!-- Pillar 2 -->
<div class="bg-surface-container-lowest p-space-xl rounded-2xl shadow-sm hover:shadow-md transition-shadow flex flex-col justify-between">
<div>
<div class="w-12 h-12 rounded-xl bg-tertiary/10 text-tertiary-container flex items-center justify-center mb-space-md">
<span class="material-symbols-outlined text-[28px]">schedule</span>
</div>
<h3 class="font-headline-sm text-headline-sm font-bold text-on-surface mb-space-xs">
              Congés &amp; Suivi du Temps Réel
            </h3>
<p class="font-body-md text-body-md text-on-surface-variant mb-space-md">
              Synchronisation directe avec vos pointeuses biométriques ZKTeco, Anviz ou RFID. Décompte rigoureux des retards, des pauses non autorisées et calcul des heures supplémentaires majorées à 125% et 150%.
            </p>
</div>
<ul class="space-y-1.5 text-on-surface-variant font-label-md text-label-md">
<li class="flex items-center gap-1.5">
<span class="material-symbols-outlined text-tertiary-container text-[16px]">check</span>
              Passerelle pointeuses biométriques
            </li>
<li class="flex items-center gap-1.5">
<span class="material-symbols-outlined text-tertiary-container text-[16px]">check</span>
              Règles d'heures sup 125% / 150%
            </li>
<li class="flex items-center gap-1.5">
<span class="material-symbols-outlined text-tertiary-container text-[16px]">check</span>
              Gestion du travail posté (3x8)
            </li>
</ul>
</div>
<!-- Pillar 3 -->
<div class="bg-surface-container-lowest p-space-xl rounded-2xl shadow-sm hover:shadow-md transition-shadow flex flex-col justify-between">
<div>
<div class="w-12 h-12 rounded-xl bg-secondary-fixed text-on-secondary-fixed flex items-center justify-center mb-space-md">
<span class="material-symbols-outlined text-[28px]">group_add</span>
</div>
<h3 class="font-headline-sm text-headline-sm font-bold text-on-surface mb-space-xs">
              Recrutement &amp; Onboarding ANETI
            </h3>
<p class="font-body-md text-body-md text-on-surface-variant mb-space-md">
              Suivi des candidatures en pipeline Kanban. Édition automatique de contrats conformes au droit tunisien : SIVP avec attestation ANETI, contrats Karama, CDD, CDI et périodes d'essai renouvelables.
            </p>
</div>
<ul class="space-y-1.5 text-on-surface-variant font-label-md text-label-md">
<li class="flex items-center gap-1.5">
<span class="material-symbols-outlined text-tertiary-container text-[16px]">check</span>
              Modèles de contrats ANETI &amp; CDI
            </li>
<li class="flex items-center gap-1.5">
<span class="material-symbols-outlined text-tertiary-container text-[16px]">check</span>
              Suivi des dates d'échéance CIN &amp; Visa
            </li>
<li class="flex items-center gap-1.5">
<span class="material-symbols-outlined text-tertiary-container text-[16px]">check</span>
              Checklist onboarding matériel &amp; IT
            </li>
</ul>
</div>
<!-- Pillar 4 -->
<div class="bg-surface-container-lowest p-space-xl rounded-2xl shadow-sm hover:shadow-md transition-shadow flex flex-col justify-between">
<div>
<div class="w-12 h-12 rounded-xl bg-primary-fixed text-on-primary-fixed flex items-center justify-center mb-space-md">
<span class="material-symbols-outlined text-[28px]">policy</span>
</div>
<h3 class="font-headline-sm text-headline-sm font-bold text-on-surface mb-space-xs">
              Conformité INPDP &amp; Sécurité Souveraine
            </h3>
<p class="font-body-md text-body-md text-on-surface-variant mb-space-md">
              Hébergement souverain sur serveurs certifiés situés en Tunisie. Strictement conforme aux prérequis de l'Instance Nationale de Protection des Données Personnelles (INPDP) et horodatage certifié TunTrust.
            </p>
</div>
<ul class="space-y-1.5 text-on-surface-variant font-label-md text-label-md">
<li class="flex items-center gap-1.5">
<span class="material-symbols-outlined text-tertiary-container text-[16px]">check</span>
              Conformité Loi n° 2004-63 INPDP
            </li>
<li class="flex items-center gap-1.5">
<span class="material-symbols-outlined text-tertiary-container text-[16px]">check</span>
              Sauvegardes quotidiennes chiffrées
            </li>
<li class="flex items-center gap-1.5">
<span class="material-symbols-outlined text-tertiary-container text-[16px]">check</span>
              Authentification double facteur (2FA)
            </li>
</ul>
</div>
</div>
</div>
</section>
<!-- Interactive ROI & Metrics Section -->
<section class="w-full py-space-xl lg:py-20 bg-surface-container-lowest">
<div class="max-w-[1536px] mx-auto px-margin-mobile md:px-margin">
<div class="bg-gradient-to-r from-primary to-primary-container rounded-3xl p-space-xl lg:p-16 text-on-primary relative overflow-hidden shadow-xl">
<div class="absolute -right-10 -bottom-10 opacity-10 pointer-events-none">
<span class="material-symbols-outlined text-[320px]">trending_up</span>
</div>
<div class="grid grid-cols-1 lg:grid-cols-12 gap-space-xl items-center relative z-10">
<div class="lg:col-span-7 flex flex-col gap-space-md">
<span class="font-label-sm text-label-sm uppercase tracking-wider font-bold px-3 py-1 bg-on-primary/10 rounded-full self-start">
              Impact Opérationnel Mesuré
            </span>
<h2 class="font-display-lg text-display-lg md:text-[38px] md:leading-[46px] font-bold text-on-primary tracking-tight">
              Divisez par 4 le temps passé sur la clôture de paie de fin de mois.
            </h2>
<p class="font-body-lg text-body-lg text-on-primary-container max-w-xl">
              Fini les nuits blanches du 25 au 30 de chaque mois. HRM Pro recalcule les variables en tâche de fond et garantit une précision au millime près lors des contrôles fiscaux et CNSS.
            </p>
</div>
<div class="lg:col-span-5 grid grid-cols-1 sm:grid-cols-3 gap-space-md text-center">
<div class="bg-surface-container-lowest/10 backdrop-blur-md rounded-2xl p-space-md border-0">
<span class="font-display-lg text-display-lg font-bold text-on-primary block">-75%</span>
<span class="font-body-sm text-body-sm text-on-primary-container mt-1 block">Temps de traitement de la paie</span>
</div>
<div class="bg-surface-container-lowest/10 backdrop-blur-md rounded-2xl p-space-md border-0">
<span class="font-display-lg text-display-lg font-bold text-tertiary-fixed block">0</span>
<span class="font-body-sm text-body-sm text-on-primary-container mt-1 block">Erreur de calcul de retenue fiscale</span>
</div>
<div class="bg-surface-container-lowest/10 backdrop-blur-md rounded-2xl p-space-md border-0">
<span class="font-display-lg text-display-lg font-bold text-on-primary block">100%</span>
<span class="font-body-sm text-body-sm text-on-primary-container mt-1 block">Conformité inspection du travail</span>
</div>
</div>
</div>
</div>
</div>
</section>
<!-- Customer Testimonials & Case Studies -->
<section class="w-full py-space-xl lg:py-24 bg-background">
<div class="max-w-[1536px] mx-auto px-margin-mobile md:px-margin">
<div class="flex flex-col items-center text-center max-w-2xl mx-auto mb-space-xl">
<span class="px-space-md py-space-xs rounded-full bg-surface-container-high text-primary font-label-sm text-label-sm uppercase tracking-wider font-bold mb-space-sm">
          Retour d'Expérience Client
        </span>
<h2 class="font-headline-lg text-headline-lg font-bold text-on-surface tracking-tight">
          Approuvé par les Directeurs RH &amp; Financiers en Tunisie
        </h2>
</div>
<div class="grid grid-cols-1 md:grid-cols-2 gap-space-xl">
<!-- Testimonial 1 -->
<div class="bg-surface-container-lowest p-space-xl rounded-2xl shadow-sm flex flex-col justify-between">
<div>
<div class="flex items-center gap-1 text-tertiary-container mb-space-md">
<span class="material-symbols-outlined text-[20px]" style="font-variation-settings: 'FILL' 1;">star</span>
<span class="material-symbols-outlined text-[20px]" style="font-variation-settings: 'FILL' 1;">star</span>
<span class="material-symbols-outlined text-[20px]" style="font-variation-settings: 'FILL' 1;">star</span>
<span class="material-symbols-outlined text-[20px]" style="font-variation-settings: 'FILL' 1;">star</span>
<span class="material-symbols-outlined text-[20px]" style="font-variation-settings: 'FILL' 1;">star</span>
</div>
<blockquote class="font-body-lg text-body-lg text-on-surface italic mb-space-lg">
              « HRM Pro a transformé notre gestion RH. Les collaborateurs adorent leur portail mobile et notre clôture de paie se fait désormais en 30 minutes sans le moindre stress réglementaire. »
            </blockquote>
</div>
<div class="flex items-center gap-space-md pt-space-md border-t border-surface-container-high">
<img class="w-12 h-12 rounded-full object-cover shadow-sm" data-alt="Portrait photo of Amel Ben Salah, HR Director in Tunis wearing formal business jacket" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDbv5NwpZaFlZE8Ek8tRieTrXK41A1VJBWsPm1HzyUO2EgrIkE7R-6TVCF34A4ooMqIannRlq1Hb8tehW0i2B4W7bpDOXDzB6x7TLbWo9NsWF1NSkczVo5WsapVpYPnhsbin5ipF1RyGYL4tBs-GOIdTphMq1UelsHJvcHh4iZ922uhKfqv6txete_N4wTvv8LZx8qvc8lJ6u7vysMzNvRi0l8Hw-tFlF63cJoobZmFSeD2WJ2uw3Hc"/>
<div>
<p class="font-headline-sm text-headline-sm font-bold text-on-surface">Amel Ben Salah</p>
<p class="font-body-sm text-body-sm text-on-surface-variant">Directrice RH, Carthage Tech Solutions (180 salariés)</p>
</div>
</div>
</div>
<!-- Testimonial 2 -->
<div class="bg-surface-container-lowest p-space-xl rounded-2xl shadow-sm flex flex-col justify-between">
<div>
<div class="flex items-center gap-1 text-tertiary-container mb-space-md">
<span class="material-symbols-outlined text-[20px]" style="font-variation-settings: 'FILL' 1;">star</span>
<span class="material-symbols-outlined text-[20px]" style="font-variation-settings: 'FILL' 1;">star</span>
<span class="material-symbols-outlined text-[20px]" style="font-variation-settings: 'FILL' 1;">star</span>
<span class="material-symbols-outlined text-[20px]" style="font-variation-settings: 'FILL' 1;">star</span>
<span class="material-symbols-outlined text-[20px]" style="font-variation-settings: 'FILL' 1;">star</span>
</div>
<blockquote class="font-body-lg text-body-lg text-on-surface italic mb-space-lg">
              « Le fichier Télé-CNSS généré est parfait du premier coup. Plus aucun rejet au bureau régional de la CNSS et le fichier SIBTEL pour la BIAT est accepté instantanément. Un gain de trésorerie et de temps inestimable. »
            </blockquote>
</div>
<div class="flex items-center gap-space-md pt-space-md border-t border-surface-container-high">
<img class="w-12 h-12 rounded-full object-cover shadow-sm" data-alt="Portrait photo of Mehdi Trabelsi, Chief Financial Officer in modern office overlooking Tunis lake" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBmvyxTD_ZXZqpZCAIoaldocQ8PPtU1aja1sQ9tcEedpQG60yzIcZJZnSPNCPkA6N0vuJF2J6eTe56vSvcy07mK8hJgbkYhBSPQWzD_BAUSmAwItzpzalrIPua0AJqYdlkNvAU5tS-CsFdpR33i6yJoLWnvGvbtB9VqNh5oZP58XWeF_CIkjmRmEZHevoUDHdmIRe51J1ZPlxo893wMUNwi7lTtT1g49DDIHdnmCIDfqeL75VG8bawG"/>
<div>
<p class="font-headline-sm text-headline-sm font-bold text-on-surface">Mehdi Trabelsi</p>
<p class="font-body-sm text-body-sm text-on-surface-variant">Directeur Administratif &amp; Financier, Groupe MedAgri</p>
</div>
</div>
</div>
</div>
</div>
</section>
<!-- Transparent Pricing Plans for Tunisian Market -->
<section class="w-full py-space-xl lg:py-24 bg-surface-container-low/60">
<div class="max-w-[1536px] mx-auto px-margin-mobile md:px-margin">
<div class="flex flex-col items-center text-center max-w-3xl mx-auto mb-space-xl">
<span class="px-space-md py-space-xs rounded-full bg-primary-fixed text-on-primary-fixed font-label-sm text-label-sm uppercase tracking-wider font-bold mb-space-sm">
          Tarification Transparente en Dinars Tunisiens (TND)
        </span>
<h2 class="font-display-lg text-display-lg font-bold text-on-surface tracking-tight">
          Un abonnement clair, sans frais cachés
        </h2>
<p class="font-body-lg text-body-lg text-on-surface-variant mt-space-sm">
          Facturation mensuelle ou annuelle, calculée au prorata de vos effectifs actifs réels.
        </p>
</div>
<div class="grid grid-cols-1 lg:grid-cols-3 gap-space-lg items-stretch">
<!-- Starter Plan -->
<div class="bg-surface-container-lowest p-space-xl rounded-2xl shadow-sm flex flex-col justify-between">
<div>
<div class="flex items-center justify-between mb-space-xs">
<h3 class="font-headline-md text-headline-md font-bold text-on-surface">Starter</h3>
<span class="px-2 py-0.5 rounded-full bg-surface-container-high text-on-surface font-label-sm text-label-sm">PME &amp; Startups</span>
</div>
<p class="font-body-sm text-body-sm text-on-surface-variant mb-space-lg">Idéal pour automatiser la paie de 1 à 25 salariés en conformité totale.</p>
<div class="flex items-baseline gap-1 mb-space-lg">
<span class="font-display-lg text-display-lg font-bold text-on-surface">6</span>
<span class="font-headline-sm text-headline-sm font-semibold text-on-surface-variant">TND / salarié / mois</span>
</div>
<ul class="space-y-space-sm text-on-surface font-body-sm text-body-sm">
<li class="flex items-center gap-2">
<span class="material-symbols-outlined text-primary text-[18px]">check_circle</span>
                Calcul paie &amp; barème IRPP 2025
              </li>
<li class="flex items-center gap-2">
<span class="material-symbols-outlined text-primary text-[18px]">check_circle</span>
                Télé-déclaration trimestrielle CNSS
              </li>
<li class="flex items-center gap-2">
<span class="material-symbols-outlined text-primary text-[18px]">check_circle</span>
                Fichiers de virements bancaires SIBTEL
              </li>
<li class="flex items-center gap-2">
<span class="material-symbols-outlined text-primary text-[18px]">check_circle</span>
                Portail salarié (consultation fiches)
              </li>
</ul>
</div>
<a class="mt-space-xl w-full py-2.5 rounded-xl bg-surface-container-high text-primary hover:bg-surface-container-highest font-label-lg text-label-lg font-semibold text-center block transition-colors" href="#demo">
            Démarrer avec Starter
          </a>
</div>
<!-- Pro Plan (Hero Tier) -->
<div class="bg-surface-container-lowest p-space-xl rounded-2xl shadow-xl flex flex-col justify-between relative ring-2 ring-primary">
<div class="absolute -top-3.5 left-1/2 -translate-x-1/2 bg-primary-fill text-on-primary px- space-md py-0.5 rounded-full font-label-sm text-label-sm font-bold uppercase tracking-wider">
            Le plus populaire en Tunisie
          </div>
<div>
<div class="flex items-center justify-between mb-space-xs mt-2">
<h3 class="font-headline-md text-headline-md font-bold text-primary">Pro</h3>
<span class="px-2 py-0.5 rounded-full bg-primary-fixed text-on-primary-fixed font-label-sm text-label-sm font-bold">25 à 150 salariés</span>
</div>
<p class="font-body-sm text-body-sm text-on-surface-variant mb-space-lg">La suite complète SIRH, pointage biométrique et gestion des temps.</p>
<div class="flex items-baseline gap-1 mb-space-lg">
<span class="font-display-lg text-display-lg font-bold text-on-surface">9</span>
<span class="font-headline-sm text-headline-sm font-semibold text-on-surface-variant">TND / salarié / mois</span>
</div>
<ul class="space-y-space-sm text-on-surface font-body-sm text-body-sm">
<li class="flex items-center gap-2">
<span class="material-symbols-outlined text-primary text-[18px]">check_circle</span>
<strong>Tout le plan Starter inclus</strong>
</li>
<li class="flex items-center gap-2">
<span class="material-symbols-outlined text-primary text-[18px]">check_circle</span>
                Liaison pointeuses biométriques &amp; badgeuses
              </li>
<li class="flex items-center gap-2">
<span class="material-symbols-outlined text-primary text-[18px]">check_circle</span>
                Workflows de validation congés &amp; notes de frais
              </li>
<li class="flex items-center gap-2">
<span class="material-symbols-outlined text-primary text-[18px]">check_circle</span>
                Attestations de travail instantanées QR TunTrust
              </li>
<li class="flex items-center gap-2">
<span class="material-symbols-outlined text-primary text-[18px]">check_circle</span>
                Support téléphonique &amp; juriste RH dédié à Tunis
              </li>
</ul>
</div>
<a class="mt-space-xl w-full py-2.5 rounded-xl bg-primary-container text-on-primary hover:bg-primary-fill font-label-lg text-label-lg font-semibold text-center block shadow-md transition-all active:scale-[0.98]" href="#demo">
            Demander un essai Pro gratuit
          </a>
</div>
<!-- Enterprise Plan -->
<div class="bg-surface-container-lowest p-space-xl rounded-2xl shadow-sm flex flex-col justify-between">
<div>
<div class="flex items-center justify-between mb-space-xs">
<h3 class="font-headline-md text-headline-md font-bold text-on-surface">Enterprise</h3>
<span class="px-2 py-0.5 rounded-full bg-surface-container-high text-on-surface font-label-sm text-label-sm">+150 Salariés</span>
</div>
<p class="font-body-sm text-body-sm text-on-surface-variant mb-space-lg">Pour groupes multi-sociétés, usines et structures décentralisées.</p>
<div class="flex items-baseline gap-1 mb-space-lg">
<span class="font-headline-lg text-headline-lg font-bold text-on-surface">Sur Devis</span>
<span class="font-body-sm text-body-sm text-on-surface-variant">Accompagnement sur-mesure</span>
</div>
<ul class="space-y-space-sm text-on-surface font-body-sm text-body-sm">
<li class="flex items-center gap-2">
<span class="material-symbols-outlined text-primary text-[18px]">check_circle</span>
                Gestion multi-filiales &amp; multi-matricules CNSS
              </li>
<li class="flex items-center gap-2">
<span class="material-symbols-outlined text-primary text-[18px]">check_circle</span>
                Intégration API avec ERP (SAP, Sage, Odoo, Cegid)
              </li>
<li class="flex items-center gap-2">
<span class="material-symbols-outlined text-primary text-[18px]">check_circle</span>
                Hébergement dédié privé en Tunisie (DataXion)
              </li>
<li class="flex items-center gap-2">
<span class="material-symbols-outlined text-primary text-[18px]">check_circle</span>
                Gestionnaire de compte dédié &amp; SLA 99.98%
              </li>
</ul>
</div>
<a class="mt-space-xl w-full py-2.5 rounded-xl bg-surface-container-high text-on-surface hover:bg-surface-container-highest font-label-lg text-label-lg font-semibold text-center block transition-colors" href="#demo">
            Contacter notre équipe Grands Comptes
          </a>
</div>
</div>
</div>
</section>
<!-- Final High-Conversion CTA Banner & Demo Booking -->
<section class="w-full py-space-xl lg:py-24 bg-surface-container-lowest" id="demo">
<div class="max-w-[1536px] mx-auto px-margin-mobile md:px-margin">
<div class="bg-gradient-to-br from-primary via-primary-container to-inverse-surface rounded-3xl p-space-xl lg:p-16 text-on-primary shadow-2xl relative overflow-hidden">
<div class="grid grid-cols-1 lg:grid-cols-12 gap-space-xl items-center">
<div class="lg:col-span-7 flex flex-col gap-space-md">
<span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-on-primary/10 text-on-primary font-label-sm text-label-sm uppercase font-bold tracking-wider self-start">
<span class="w-2 h-2 rounded-full bg-tertiary-fixed"></span>
              Déploiement Garanti en Moins de 72h
            </span>
<h2 class="font-display-lg text-display-lg md:text-[40px] md:leading-[48px] font-bold text-on-primary tracking-tight">
              Prêt à moderniser vos Ressources Humaines en Tunisie ?
            </h2>
<p class="font-body-lg text-body-lg text-on-primary-container max-w-xl">
              Réservez une démonstration personnalisée de 20 minutes avec un expert en droit social tunisien. Nous analysons vos conventions collectives actuelles sans engagement.
            </p>
<div class="flex flex-wrap items-center gap-space-md pt-space-xs text-on-primary-container">
<div class="flex items-center gap-2">
<span class="material-symbols-outlined text-tertiary-fixed text-[20px]">verified</span>
<span class="font-label-md text-label-md">Essai gratuit 14 jours</span>
</div>
<div class="flex items-center gap-2">
<span class="material-symbols-outlined text-tertiary-fixed text-[20px]">sync</span>
<span class="font-label-md text-label-md">Reprise de l'historique paie offerte</span>
</div>
<div class="flex items-center gap-2">
<span class="material-symbols-outlined text-tertiary-fixed text-[20px]">gavel</span>
<span class="font-label-md text-label-md">Assurance conformité inspection</span>
</div>
</div>
</div>
<!-- Quick Interactive Booking Form -->
<div class="lg:col-span-5 bg-surface-container-lowest text-on-surface rounded-2xl p-space-xl shadow-xl">
<h3 class="font-headline-sm text-headline-sm font-bold text-on-surface mb-1">
              Planifier votre démonstration gratuite
            </h3>
<p class="font-body-sm text-body-sm text-on-surface-variant mb-space-md">
              Un spécialiste RH vous contactera sous 2 heures ouvrées.
            </p>
<form class="flex flex-col gap-space-sm" onsubmit="event.preventDefault(); alert('Merci ! Notre consultant RH vous contactera immédiatement.');">
<div>
<label class="font-label-sm text-label-sm text-on-surface-variant block mb-1">Nom et Prénom</label>
<input class="w-full px-space-md py-2 rounded-lg bg-surface-container-low text-on-surface font-body-md text-body-md focus:bg-surface-container-lowest focus:outline-none focus:ring-2 focus:ring-primary transition-all" placeholder="Ex: Mohamed Riahi" required="" type="text"/>
</div>
<div>
<label class="font-label-sm text-label-sm text-on-surface-variant block mb-1">Email Professionnel</label>
<input class="w-full px-space-md py-2 rounded-lg bg-surface-container-low text-on-surface font-body-md text-body-md focus:bg-surface-container-lowest focus:outline-none focus:ring-2 focus:ring-primary transition-all" placeholder="m.riahi@entreprise.tn" required="" type="email"/>
</div>
<div class="grid grid-cols-2 gap-space-sm">
<div>
<label class="font-label-sm text-label-sm text-on-surface-variant block mb-1">Téléphone (+216)</label>
<input class="w-full px-space-md py-2 rounded-lg bg-surface-container-low text-on-surface font-body-md text-body-md focus:bg-surface-container-lowest focus:outline-none focus:ring-2 focus:ring-primary transition-all" placeholder="71 000 000" required="" type="tel"/>
</div>
<div>
<label class="font-label-sm text-label-sm text-on-surface-variant block mb-1">Nombre d'employés</label>
<select class="w-full px-space-md py-2 rounded-lg bg-surface-container-low text-on-surface font-body-md text-body-md focus:bg-surface-container-lowest focus:outline-none focus:ring-2 focus:ring-primary transition-all">
<option>1 - 25 salariés</option>
<option>26 - 75 salariés</option>
<option>76 - 150 salariés</option>
<option>+150 salariés</option>
</select>
</div>
</div>
<button class="mt-space-sm w-full py-3 rounded-xl bg-primary-container text-on-primary hover:bg-primary-fill font-label-lg text-label-lg font-bold shadow-md transition-all active:scale-[0.98] flex items-center justify-center gap-2" type="submit">
<span>Confirmer ma session Démo</span>
<span class="material-symbols-outlined text-[18px]">calendar_today</span>
</button>
</form>
</div>
</div>
</div>
</div>
</section>
<!-- Interactive JavaScript for Tab Switching -->
<script>
    function switchExperience(type) {
      const paneRh = document.getElementById('pane-rh');
      const paneCollab = document.getElementById('pane-collab');
      const btnRh = document.getElementById('tab-btn-rh');
      const btnCollab = document.getElementById('tab-btn-collab');

      if (type === 'rh') {
        paneRh.classList.remove('hidden');
        paneCollab.classList.add('hidden');
        btnRh.className = 'px-space-xl py-2.5 rounded-xl font-label-lg text-label-lg font-semibold bg-surface-container-lowest text-primary shadow-sm transition-all';
        btnCollab.className = 'px-space-xl py-2.5 rounded-xl font-label-lg text-label-lg font-semibold text-on-surface-variant hover:text-on-surface transition-all';
      } else {
        paneRh.classList.add('hidden');
        paneCollab.classList.remove('hidden');
        btnCollab.className = 'px-space-xl py-2.5 rounded-xl font-label-lg text-label-lg font-semibold bg-surface-container-lowest text-primary shadow-sm transition-all';
        btnRh.className = 'px-space-xl py-2.5 rounded-xl font-label-lg text-label-lg font-semibold text-on-surface-variant hover:text-on-surface transition-all';
      }
    }
  </script>
</div></main><footer class="w-full bg-surface-container-low shadow-[0_-1px_8px_rgba(0,0,0,0.02)]"><div class="max-w-[1536px] mx-auto px-margin-mobile md:px-margin pt-space-xl pb-space-lg"><div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-space-xl pb-space-xl"><div class="lg:col-span-2 flex flex-col gap-space-md"><div class="flex items-center gap-space-xs"><div class="w-8 h-8 rounded-xl bg-primary-fill flex items-center justify-center"><span class="material-symbols-outlined text-on-primary text-[20px]">corporate_fare</span></div><span class="font-headline-sm text-headline-sm font-bold text-on-surface">HRM Pro Tunisie</span></div><p class="font-body-md text-body-md text-on-surface-variant max-w-sm">Plateforme SaaS de référence pour la gestion du capital humain, la paie déclarative et la conformité légale dédiée aux entreprises en Tunisie.</p><div class="flex items-start gap-space-xs text-on-surface-variant"><span class="material-symbols-outlined text-[18px] text-primary shrink-0">location_on</span><span class="font-body-sm text-body-sm text-on-surface-variant">Immeuble Blue Lake, Rue de la Feuille d'Érable, Les Berges du Lac 2, 1053 Tunis, Tunisie</span></div><div class="flex items-center gap-space-xs"><span class="material-symbols-outlined text-[18px] text-tertiary-container shrink-0">verified_user</span><span class="font-label-sm text-label-sm text-tertiary-container uppercase tracking-wider font-semibold">100% Conforme Législation Tunisienne</span></div></div><div class="flex flex-col gap-space-sm"><span class="font-label-lg text-label-lg font-bold text-on-surface uppercase tracking-wider">Solutions RH &amp; Paie</span><ul class="flex flex-col gap-space-xs"><li class="font-body-sm text-body-sm text-on-surface-variant hover:text-on-surface cursor-pointer transition-colors">Gestion de la Paie &amp; Bulletins</li><li class="font-body-sm text-body-sm text-on-surface-variant hover:text-on-surface cursor-pointer transition-colors">Déclarations Trimestrielles CNSS</li><li class="font-body-sm text-body-sm text-on-surface-variant hover:text-on-surface cursor-pointer transition-colors">Contrats &amp; Gestion des CIN / Matricules</li><li class="font-body-sm text-body-sm text-on-surface-variant hover:text-on-surface cursor-pointer transition-colors">Suivi des Congés &amp; Absences</li><li class="font-body-sm text-body-sm text-on-surface-variant hover:text-on-surface cursor-pointer transition-colors">Portail Libre-Service Collaborateur</li></ul></div><div class="flex flex-col gap-space-sm"><span class="font-label-lg text-label-lg font-bold text-on-surface uppercase tracking-wider">Navigation</span><ul class="flex flex-col gap-space-xs"><li class="font-body-sm text-body-sm text-on-surface-variant hover:text-on-surface cursor-pointer transition-colors">Fonctionnalités Clés</li><li class="font-body-sm text-body-sm text-on-surface-variant hover:text-on-surface cursor-pointer transition-colors">Simulateur &amp; Grille Tarifaire</li><li class="font-body-sm text-body-sm text-on-surface-variant hover:text-on-surface cursor-pointer transition-colors">Témoignages &amp; Études de Cas</li><li class="font-body-sm text-body-sm text-on-surface-variant hover:text-on-surface cursor-pointer transition-colors">Documentation &amp; Barèmes Fiscaux</li><li class="font-body-sm text-body-sm text-on-surface-variant hover:text-on-surface cursor-pointer transition-colors">Demande de Démonstration</li></ul></div><div class="flex flex-col gap-space-sm"><span class="font-label-lg text-label-lg font-bold text-on-surface uppercase tracking-wider">Cadre Réglementaire</span><ul class="flex flex-col gap-space-xs"><li class="font-body-sm text-body-sm text-on-surface-variant hover:text-on-surface cursor-pointer transition-colors">Code du Travail Tunisien</li><li class="font-body-sm text-body-sm text-on-surface-variant hover:text-on-surface cursor-pointer transition-colors">Protection Données INPDP</li><li class="font-body-sm text-body-sm text-on-surface-variant hover:text-on-surface cursor-pointer transition-colors">Immatriculation RNE Tunisie</li><li class="font-body-sm text-body-sm text-on-surface-variant hover:text-on-surface cursor-pointer transition-colors">Télédéclaration Télé-CNSS</li><li class="font-body-sm text-body-sm text-on-surface-variant hover:text-on-surface cursor-pointer transition-colors">Retenue à la Source (IRPP/CSS)</li></ul></div></div><div class="pt-space-lg flex flex-col md:flex-row items-center justify-between gap-space-md text-on-surface-variant"><p class="font-body-sm text-body-sm text-on-surface-variant">© 2025 HRM Pro Tunisie SARL. Tous droits réservés. Immatriculé au Registre National des Entreprises (RNE).</p><div class="flex items-center gap-space-md"><span class="font-body-sm text-body-sm text-on-surface-variant hover:text-on-surface cursor-pointer">Conformité INPDP</span><span class="font-body-sm text-body-sm text-on-surface-variant hover:text-on-surface cursor-pointer">Mentions Légales</span><span class="font-body-sm text-body-sm text-on-surface-variant hover:text-on-surface cursor-pointer">Conditions Générales</span><span class="font-body-sm text-body-sm text-on-surface-variant hover:text-on-surface cursor-pointer">Sécurité Hébergement</span></div></div></div></footer></body></html>