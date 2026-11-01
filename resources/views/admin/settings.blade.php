<x-admin-shell active="settings" title="Paramètres de l'Entreprise & Plateforme">
<div class="flex flex-col w-full pb-16 space-y-8">
<!-- Breadcrumb & Top Bar -->
<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pt-6">
<div class="flex flex-col space-y-1">
<nav class="flex items-center gap-2 font-label-sm text-label-sm text-on-surface-variant mb-1">
<a class="hover:text-primary transition-colors" href="#">Accueil</a>
<span class="material-symbols-outlined text-xs">chevron_right</span>
<span class="text-on-surface font-semibold">Configuration &amp; Paramètres</span>
</nav>
<div class="flex items-center gap-3">
<h1 class="font-display-lg text-display-lg text-on-surface tracking-tight">Paramètres de l'Entreprise &amp; Plateforme</h1>
<span class="bg-surface-container-high text-primary font-label-sm text-label-sm px-2.5 py-1 rounded-full uppercase font-bold tracking-wider">v2.4 TN-Gov</span>
</div>
<p class="font-body-md text-body-md text-on-surface-variant max-w-2xl">
        Configuration centrale des politiques RH, matrice des droits d'accès, conformité au Code du Travail tunisien et sécurité des flux de paie.
      </p>
</div>
<!-- Action Group -->
<div class="flex items-center gap-3 self-start md:self-center">
<button class="h-10 px-4 rounded-xl bg-surface-container-low hover:bg-surface-container text-on-surface font-label-lg text-label-lg flex items-center gap-2 transition-all shadow-sm" type="button">
<span class="material-symbols-outlined text-body-lg">history</span>
<span>Journal d'audit</span>
</button>
<button class="h-10 px-6 rounded-xl bg-primary-container hover:bg-primary-fill text-on-primary font-label-lg text-label-lg flex items-center gap-2 shadow-sm transition-all active:scale-95" id="save-btn" type="button">
<span class="material-symbols-outlined text-body-lg">save</span>
<span>Enregistrer les Modifications</span>
</button>
</div>
</div>
<!-- Navigation Segment Bar -->
<div class="w-full bg-surface-container-lowest rounded-xl p-1.5 shadow-sm overflow-x-auto">
<div class="flex items-center min-w-max gap-1">
<button class="flex items-center gap-2 px-5 py-2.5 rounded-lg bg-primary-container text-on-primary font-label-lg text-label-lg shadow-sm transition-all" type="button">
<span class="material-symbols-outlined text-body-md">manage_accounts</span>
<span>Rôles &amp; Permissions</span>
</button>
<button class="flex items-center gap-2 px-5 py-2.5 rounded-lg text-on-surface-variant hover:text-on-surface hover:bg-surface-container-low font-label-lg text-label-lg transition-all" type="button">
<span class="material-symbols-outlined text-body-md">calendar_today</span>
<span>Types de Congés &amp; Politiques</span>
</button>
<button class="flex items-center gap-2 px-5 py-2.5 rounded-lg text-on-surface-variant hover:text-on-surface hover:bg-surface-container-low font-label-lg text-label-lg transition-all" type="button">
<span class="material-symbols-outlined text-body-md">domain</span>
<span>Profil Entreprise &amp; CNSS</span>
</button>
<button class="flex items-center gap-2 px-5 py-2.5 rounded-lg text-on-surface-variant hover:text-on-surface hover:bg-surface-container-low font-label-lg text-label-lg transition-all" type="button">
<span class="material-symbols-outlined text-body-md">account_balance</span>
<span>Barèmes Fiscaux &amp; IRPP (Tunisie)</span>
</button>
<button class="flex items-center gap-2 px-5 py-2.5 rounded-lg text-on-surface-variant hover:text-on-surface hover:bg-surface-container-low font-label-lg text-label-lg transition-all" type="button">
<span class="material-symbols-outlined text-body-md">hub</span>
<span>Notifications &amp; Intégrations</span>
</button>
</div>
</div>
<!-- Main Grid Layout -->
<div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
<!-- Left Main Panel: Permissions Matrix (8 cols) -->
<div class="lg:col-span-8 space-y-6">
<!-- Informational Context Alert -->
<div class="relative overflow-hidden bg-surface-container-lowest rounded-xl p-5 shadow-sm">
<div class="absolute top-0 left-0 w-1.5 h-full bg-primary-container"></div>
<div class="flex items-start gap-4">
<div class="w-10 h-10 rounded-xl bg-secondary-container text-on-secondary-fixed flex items-center justify-center shrink-0">
<span class="material-symbols-outlined text-body-lg">shield</span>
</div>
<div class="flex-1">
<h3 class="font-headline-sm text-headline-sm text-on-surface mb-1">Matrice RBAC (Contrôle d'Accès Basé sur les Rôles)</h3>
<p class="font-body-sm text-body-sm text-on-surface-variant leading-relaxed">
              Les permissions définies ci-dessous régissent les privilèges opérationnels sur le périmètre de Carthage Tech Solutions. Toute modification entre en vigueur instantanément pour les sessions actives des salariés et gestionnaires.
            </p>
</div>
<button class="text-primary hover:text-on-primary-fixed-variant font-label-md text-label-md flex items-center gap-1 shrink-0">
<span>+ Ajouter Rôle</span>
</button>
</div>
</div>
<!-- Matrix Container -->
<div class="bg-surface-container-lowest rounded-xl shadow-sm overflow-hidden">
<!-- Matrix Sticky Header -->
<div class="grid grid-cols-12 bg-surface-container-low px-6 py-4 sticky top-16 z-20">
<div class="col-span-5 flex flex-col justify-center">
<span class="font-label-md text-label-md uppercase tracking-wider text-on-surface-variant font-bold">Modules &amp; Capacités</span>
<span class="font-label-sm text-label-sm text-on-surface-variant">Actions autorisées par profil</span>
</div>
<div class="col-span-7 grid grid-cols-4 gap-2 text-center items-center">
<div class="flex flex-col items-center">
<span class="font-label-md text-label-md text-on-surface font-semibold truncate w-full">Admin Global</span>
<span class="font-label-sm text-label-sm text-tertiary-container font-semibold">Tous droits</span>
</div>
<div class="flex flex-col items-center">
<span class="font-label-md text-label-md text-on-surface font-semibold truncate w-full">RH / Paie</span>
<span class="font-label-sm text-label-sm text-on-surface-variant">Spécialiste</span>
</div>
<div class="flex flex-col items-center">
<span class="font-label-md text-label-md text-on-surface font-semibold truncate w-full">Manager (N+1)</span>
<span class="font-label-sm text-label-sm text-on-surface-variant">Superviseur</span>
</div>
<div class="flex flex-col items-center">
<span class="font-label-md text-label-md text-on-surface font-semibold truncate w-full">Collaborateur</span>
<span class="font-label-sm text-label-sm text-on-surface-variant">Salarié</span>
</div>
</div>
</div>
<!-- Section 1: Module Collaborateurs -->
<div class="px-6 py-3 bg-surface-container-low/50 flex items-center justify-between">
<div class="flex items-center gap-2">
<span class="material-symbols-outlined text-primary text-body-md">badge</span>
<span class="font-label-lg text-label-lg font-bold text-on-surface">Module 01 • Gestion du Personnel &amp; Dossiers</span>
</div>
<span class="font-label-sm text-label-sm text-on-surface-variant">Code du Travail Art. 6-12</span>
</div>
<div class="divide-y divide-surface-container">
<!-- Row 1 -->
<div class="grid grid-cols-12 px-6 py-3.5 hover:bg-surface-container-low/40 items-center transition-colors">
<div class="col-span-5 pr-4">
<div class="font-body-md text-body-md text-on-surface font-medium">Consulter le trombinoscope &amp; effectif</div>
<div class="font-body-sm text-body-sm text-on-surface-variant">Liste globale des salariés, statuts et affectations</div>
</div>
<div class="col-span-7 grid grid-cols-4 gap-2 place-items-center">
<input checked="" class="w-4 h-4 rounded text-primary-container focus:ring-0 accent-primary cursor-not-allowed opacity-80" disabled="" type="checkbox"/>
<input checked="" class="w-4 h-4 rounded text-primary-container focus:ring-0 accent-primary cursor-pointer" type="checkbox"/>
<input checked="" class="w-4 h-4 rounded text-primary-container focus:ring-0 accent-primary cursor-pointer" type="checkbox"/>
<input checked="" class="w-4 h-4 rounded text-primary-container focus:ring-0 accent-primary cursor-pointer" type="checkbox"/>
</div>
</div>
<!-- Row 2 -->
<div class="grid grid-cols-12 px-6 py-3.5 hover:bg-surface-container-low/40 items-center transition-colors">
<div class="col-span-5 pr-4">
<div class="font-body-md text-body-md text-on-surface font-medium">Créer et modifier des fiches salariés</div>
<div class="font-body-sm text-body-sm text-on-surface-variant">Affectation département, matricule interne, état civil</div>
</div>
<div class="col-span-7 grid grid-cols-4 gap-2 place-items-center">
<input checked="" class="w-4 h-4 rounded text-primary-container focus:ring-0 accent-primary cursor-not-allowed opacity-80" disabled="" type="checkbox"/>
<input checked="" class="w-4 h-4 rounded text-primary-container focus:ring-0 accent-primary cursor-pointer" type="checkbox"/>
<input class="w-4 h-4 rounded text-primary-container focus:ring-0 accent-primary cursor-pointer" type="checkbox"/>
<input class="w-4 h-4 rounded text-primary-container focus:ring-0 accent-primary cursor-not-allowed opacity-40" disabled="" type="checkbox"/>
</div>
</div>
<!-- Row 3 -->
<div class="grid grid-cols-12 px-6 py-3.5 hover:bg-surface-container-low/40 items-center transition-colors bg-surface-container-low/20">
<div class="col-span-5 pr-4">
<div class="flex items-center gap-2">
<span class="font-body-md text-body-md text-on-surface font-medium">Données salariales &amp; bancaires confidentielles</span>
<span class="bg-error-container text-on-error-container font-label-sm text-label-sm px-1.5 py-0.5 rounded">Sensible</span>
</div>
<div class="font-body-sm text-body-sm text-on-surface-variant">RIB, salaire brut contractuel, primes variables</div>
</div>
<div class="col-span-7 grid grid-cols-4 gap-2 place-items-center">
<input checked="" class="w-4 h-4 rounded text-primary-container focus:ring-0 accent-primary cursor-not-allowed opacity-80" disabled="" type="checkbox"/>
<input checked="" class="w-4 h-4 rounded text-primary-container focus:ring-0 accent-primary cursor-pointer" type="checkbox"/>
<input class="w-4 h-4 rounded text-primary-container focus:ring-0 accent-primary cursor-pointer" type="checkbox"/>
<input class="w-4 h-4 rounded text-primary-container focus:ring-0 accent-primary cursor-not-allowed opacity-40" disabled="" type="checkbox"/>
</div>
</div>
<!-- Row 4 -->
<div class="grid grid-cols-12 px-6 py-3.5 hover:bg-surface-container-low/40 items-center transition-colors">
<div class="col-span-5 pr-4">
<div class="font-body-md text-body-md text-on-surface font-medium">Supprimer / Clôturer dossier collaborateur</div>
<div class="font-body-sm text-body-sm text-on-surface-variant">Archivage légal, solde de tout compte et STC</div>
</div>
<div class="col-span-7 grid grid-cols-4 gap-2 place-items-center">
<input checked="" class="w-4 h-4 rounded text-primary-container focus:ring-0 accent-primary cursor-not-allowed opacity-80" disabled="" type="checkbox"/>
<input class="w-4 h-4 rounded text-primary-container focus:ring-0 accent-primary cursor-pointer" type="checkbox"/>
<input class="w-4 h-4 rounded text-primary-container focus:ring-0 accent-primary cursor-not-allowed opacity-40" disabled="" type="checkbox"/>
<input class="w-4 h-4 rounded text-primary-container focus:ring-0 accent-primary cursor-not-allowed opacity-40" disabled="" type="checkbox"/>
</div>
</div>
</div>
<!-- Section 2: Module Congés -->
<div class="px-6 py-3 bg-surface-container-low/50 flex items-center justify-between">
<div class="flex items-center gap-2">
<span class="material-symbols-outlined text-primary text-body-md">beach_access</span>
<span class="font-label-lg text-label-lg font-bold text-on-surface">Module 02 • Congés &amp; Absences Réglementaires</span>
</div>
<span class="font-label-sm text-label-sm text-on-surface-variant">Convention collective Syntec &amp; Métallurgie TN</span>
</div>
<div class="divide-y divide-surface-container">
<!-- Row 1 -->
<div class="grid grid-cols-12 px-6 py-3.5 hover:bg-surface-container-low/40 items-center transition-colors">
<div class="col-span-5 pr-4">
<div class="font-body-md text-body-md text-on-surface font-medium">Déposer une demande de congé / repos</div>
<div class="font-body-sm text-body-sm text-on-surface-variant">Annuel payé, sans solde, maternité, exceptionnel</div>
</div>
<div class="col-span-7 grid grid-cols-4 gap-2 place-items-center">
<input checked="" class="w-4 h-4 rounded text-primary-container focus:ring-0 accent-primary cursor-not-allowed opacity-80" disabled="" type="checkbox"/>
<input checked="" class="w-4 h-4 rounded text-primary-container focus:ring-0 accent-primary cursor-pointer" type="checkbox"/>
<input checked="" class="w-4 h-4 rounded text-primary-container focus:ring-0 accent-primary cursor-pointer" type="checkbox"/>
<input checked="" class="w-4 h-4 rounded text-primary-container focus:ring-0 accent-primary cursor-pointer" type="checkbox"/>
</div>
</div>
<!-- Row 2 -->
<div class="grid grid-cols-12 px-6 py-3.5 hover:bg-surface-container-low/40 items-center transition-colors">
<div class="col-span-5 pr-4">
<div class="font-body-md text-body-md text-on-surface font-medium">Valider les demandes d'équipe (Validation N+1)</div>
<div class="font-body-sm text-body-sm text-on-surface-variant">Vérification de l'impact planning et arbitrage direct</div>
</div>
<div class="col-span-7 grid grid-cols-4 gap-2 place-items-center">
<input checked="" class="w-4 h-4 rounded text-primary-container focus:ring-0 accent-primary cursor-not-allowed opacity-80" disabled="" type="checkbox"/>
<input checked="" class="w-4 h-4 rounded text-primary-container focus:ring-0 accent-primary cursor-pointer" type="checkbox"/>
<input checked="" class="w-4 h-4 rounded text-primary-container focus:ring-0 accent-primary cursor-pointer" type="checkbox"/>
<input class="w-4 h-4 rounded text-primary-container focus:ring-0 accent-primary cursor-not-allowed opacity-40" disabled="" type="checkbox"/>
</div>
</div>
<!-- Row 3 -->
<div class="grid grid-cols-12 px-6 py-3.5 hover:bg-surface-container-low/40 items-center transition-colors">
<div class="col-span-5 pr-4">
<div class="font-body-md text-body-md text-on-surface font-medium">Validation finale RH &amp; décompte paie (N+2)</div>
<div class="font-body-sm text-body-sm text-on-surface-variant">Confirmation formelle avec impact bulletin</div>
</div>
<div class="col-span-7 grid grid-cols-4 gap-2 place-items-center">
<input checked="" class="w-4 h-4 rounded text-primary-container focus:ring-0 accent-primary cursor-not-allowed opacity-80" disabled="" type="checkbox"/>
<input checked="" class="w-4 h-4 rounded text-primary-container focus:ring-0 accent-primary cursor-pointer" type="checkbox"/>
<input class="w-4 h-4 rounded text-primary-container focus:ring-0 accent-primary cursor-pointer" type="checkbox"/>
<input class="w-4 h-4 rounded text-primary-container focus:ring-0 accent-primary cursor-not-allowed opacity-40" disabled="" type="checkbox"/>
</div>
</div>
<!-- Row 4 -->
<div class="grid grid-cols-12 px-6 py-3.5 hover:bg-surface-container-low/40 items-center transition-colors">
<div class="col-span-5 pr-4">
<div class="font-body-md text-body-md text-on-surface font-medium">Ajuster manuellement les reliquats &amp; soldes</div>
<div class="font-body-sm text-body-sm text-on-surface-variant">Régularisation rétroactive d'exercice</div>
</div>
<div class="col-span-7 grid grid-cols-4 gap-2 place-items-center">
<input checked="" class="w-4 h-4 rounded text-primary-container focus:ring-0 accent-primary cursor-not-allowed opacity-80" disabled="" type="checkbox"/>
<input checked="" class="w-4 h-4 rounded text-primary-container focus:ring-0 accent-primary cursor-pointer" type="checkbox"/>
<input class="w-4 h-4 rounded text-primary-container focus:ring-0 accent-primary cursor-not-allowed opacity-40" disabled="" type="checkbox"/>
<input class="w-4 h-4 rounded text-primary-container focus:ring-0 accent-primary cursor-not-allowed opacity-40" disabled="" type="checkbox"/>
</div>
</div>
</div>
<!-- Section 3: Module Paie & CNSS -->
<div class="px-6 py-3 bg-surface-container-low/50 flex items-center justify-between">
<div class="flex items-center gap-2">
<span class="material-symbols-outlined text-primary text-body-md">payments</span>
<span class="font-label-lg text-label-lg font-bold text-on-surface">Module 03 • Moteur de Paie, IRPP &amp; Déclarations CNSS</span>
</div>
<span class="font-label-sm text-label-sm text-on-surface-variant">Trésorerie &amp; Impôts TN</span>
</div>
<div class="divide-y divide-surface-container">
<!-- Row 1 -->
<div class="grid grid-cols-12 px-6 py-3.5 hover:bg-surface-container-low/40 items-center transition-colors">
<div class="col-span-5 pr-4">
<div class="font-body-md text-body-md text-on-surface font-medium">Génération des bulletins de paie mensuels</div>
<div class="font-body-sm text-body-sm text-on-surface-variant">Calcul des cotisations salariales (9.18%) et patronales (16.57%)</div>
</div>
<div class="col-span-7 grid grid-cols-4 gap-2 place-items-center">
<input checked="" class="w-4 h-4 rounded text-primary-container focus:ring-0 accent-primary cursor-not-allowed opacity-80" disabled="" type="checkbox"/>
<input checked="" class="w-4 h-4 rounded text-primary-container focus:ring-0 accent-primary cursor-pointer" type="checkbox"/>
<input class="w-4 h-4 rounded text-primary-container focus:ring-0 accent-primary cursor-not-allowed opacity-40" disabled="" type="checkbox"/>
<input class="w-4 h-4 rounded text-primary-container focus:ring-0 accent-primary cursor-not-allowed opacity-40" disabled="" type="checkbox"/>
</div>
</div>
<!-- Row 2 -->
<div class="grid grid-cols-12 px-6 py-3.5 hover:bg-surface-container-low/40 items-center transition-colors">
<div class="col-span-5 pr-4">
<div class="font-body-md text-body-md text-on-surface font-medium">Consulter les états récapitulatifs IRPP &amp; CSS</div>
<div class="font-body-sm text-body-sm text-on-surface-variant">Bordereaux de retenues à la source trimestriels</div>
</div>
<div class="col-span-7 grid grid-cols-4 gap-2 place-items-center">
<input checked="" class="w-4 h-4 rounded text-primary-container focus:ring-0 accent-primary cursor-not-allowed opacity-80" disabled="" type="checkbox"/>
<input checked="" class="w-4 h-4 rounded text-primary-container focus:ring-0 accent-primary cursor-pointer" type="checkbox"/>
<input class="w-4 h-4 rounded text-primary-container focus:ring-0 accent-primary cursor-pointer" type="checkbox"/>
<input class="w-4 h-4 rounded text-primary-container focus:ring-0 accent-primary cursor-not-allowed opacity-40" disabled="" type="checkbox"/>
</div>
</div>
<!-- Row 3 -->
<div class="grid grid-cols-12 px-6 py-3.5 hover:bg-surface-container-low/40 items-center transition-colors">
<div class="col-span-5 pr-4">
<div class="font-body-md text-body-md text-on-surface font-medium">Export virements bancaires (Norme SIBTEL TN)</div>
<div class="font-body-sm text-body-sm text-on-surface-variant">Fichiers interbancaires structurés pour télétransmission</div>
</div>
<div class="col-span-7 grid grid-cols-4 gap-2 place-items-center">
<input checked="" class="w-4 h-4 rounded text-primary-container focus:ring-0 accent-primary cursor-not-allowed opacity-80" disabled="" type="checkbox"/>
<input checked="" class="w-4 h-4 rounded text-primary-container focus:ring-0 accent-primary cursor-pointer" type="checkbox"/>
<input class="w-4 h-4 rounded text-primary-container focus:ring-0 accent-primary cursor-not-allowed opacity-40" disabled="" type="checkbox"/>
<input class="w-4 h-4 rounded text-primary-container focus:ring-0 accent-primary cursor-not-allowed opacity-40" disabled="" type="checkbox"/>
</div>
</div>
</div>
<!-- Section 4: Module Pointage & Présence -->
<div class="px-6 py-3 bg-surface-container-low/50 flex items-center justify-between">
<div class="flex items-center gap-2">
<span class="material-symbols-outlined text-primary text-body-md">schedule</span>
<span class="font-label-lg text-label-lg font-bold text-on-surface">Module 04 • Pointage, Retards &amp; Présence Réelle</span>
</div>
<span class="font-label-sm text-label-sm text-on-surface-variant">Badgeuses biométriques &amp; Mobile</span>
</div>
<div class="divide-y divide-surface-container">
<div class="grid grid-cols-12 px-6 py-3.5 hover:bg-surface-container-low/40 items-center transition-colors">
<div class="col-span-5 pr-4">
<div class="font-body-md text-body-md text-on-surface font-medium">Enregistrer pointages quotidiens (In / Out)</div>
<div class="font-body-sm text-body-sm text-on-surface-variant">Badges virtuels, géolocalisation ou badgeuse physique</div>
</div>
<div class="col-span-7 grid grid-cols-4 gap-2 place-items-center">
<input checked="" class="w-4 h-4 rounded text-primary-container focus:ring-0 accent-primary cursor-not-allowed opacity-80" disabled="" type="checkbox"/>
<input checked="" class="w-4 h-4 rounded text-primary-container focus:ring-0 accent-primary cursor-pointer" type="checkbox"/>
<input checked="" class="w-4 h-4 rounded text-primary-container focus:ring-0 accent-primary cursor-pointer" type="checkbox"/>
<input checked="" class="w-4 h-4 rounded text-primary-container focus:ring-0 accent-primary cursor-pointer" type="checkbox"/>
</div>
</div>
<div class="grid grid-cols-12 px-6 py-3.5 hover:bg-surface-container-low/40 items-center transition-colors">
<div class="col-span-5 pr-4">
<div class="font-body-md text-body-md text-on-surface font-medium">Rectifier les anomalies de pointage d'équipe</div>
<div class="font-body-sm text-body-sm text-on-surface-variant">Oublis de badgeage, sorties exceptionnelles autorisées</div>
</div>
<div class="col-span-7 grid grid-cols-4 gap-2 place-items-center">
<input checked="" class="w-4 h-4 rounded text-primary-container focus:ring-0 accent-primary cursor-not-allowed opacity-80" disabled="" type="checkbox"/>
<input checked="" class="w-4 h-4 rounded text-primary-container focus:ring-0 accent-primary cursor-pointer" type="checkbox"/>
<input checked="" class="w-4 h-4 rounded text-primary-container focus:ring-0 accent-primary cursor-pointer" type="checkbox"/>
<input class="w-4 h-4 rounded text-primary-container focus:ring-0 accent-primary cursor-not-allowed opacity-40" disabled="" type="checkbox"/>
</div>
</div>
</div>
<!-- Section 5: Documents RH -->
<div class="px-6 py-3 bg-surface-container-low/50 flex items-center justify-between">
<div class="flex items-center gap-2">
<span class="material-symbols-outlined text-primary text-body-md">description</span>
<span class="font-label-lg text-label-lg font-bold text-on-surface">Module 05 • Coffre-fort Numérique &amp; Documents Légaux</span>
</div>
<span class="font-label-sm text-label-sm text-on-surface-variant">Signatures &amp; Contrats</span>
</div>
<div class="divide-y divide-surface-container">
<div class="grid grid-cols-12 px-6 py-3.5 hover:bg-surface-container-low/40 items-center transition-colors">
<div class="col-span-5 pr-4">
<div class="font-body-md text-body-md text-on-surface font-medium">Accès au coffre-fort personnel (Bulletins, attestations)</div>
<div class="font-body-sm text-body-sm text-on-surface-variant">Consultation et téléchargement des pièces personnelles</div>
</div>
<div class="col-span-7 grid grid-cols-4 gap-2 place-items-center">
<input checked="" class="w-4 h-4 rounded text-primary-container focus:ring-0 accent-primary cursor-not-allowed opacity-80" disabled="" type="checkbox"/>
<input checked="" class="w-4 h-4 rounded text-primary-container focus:ring-0 accent-primary cursor-pointer" type="checkbox"/>
<input checked="" class="w-4 h-4 rounded text-primary-container focus:ring-0 accent-primary cursor-pointer" type="checkbox"/>
<input checked="" class="w-4 h-4 rounded text-primary-container focus:ring-0 accent-primary cursor-pointer" type="checkbox"/>
</div>
</div>
<div class="grid grid-cols-12 px-6 py-3.5 hover:bg-surface-container-low/40 items-center transition-colors">
<div class="col-span-5 pr-4">
<div class="font-body-md text-body-md text-on-surface font-medium">Téléverser et faire signer contrats &amp; avenants</div>
<div class="font-body-sm text-body-sm text-on-surface-variant">Contrats CDD, CDI, SIVP, Karama sous validation RH</div>
</div>
<div class="col-span-7 grid grid-cols-4 gap-2 place-items-center">
<input checked="" class="w-4 h-4 rounded text-primary-container focus:ring-0 accent-primary cursor-not-allowed opacity-80" disabled="" type="checkbox"/>
<input checked="" class="w-4 h-4 rounded text-primary-container focus:ring-0 accent-primary cursor-pointer" type="checkbox"/>
<input class="w-4 h-4 rounded text-primary-container focus:ring-0 accent-primary cursor-pointer" type="checkbox"/>
<input class="w-4 h-4 rounded text-primary-container focus:ring-0 accent-primary cursor-not-allowed opacity-40" disabled="" type="checkbox"/>
</div>
</div>
</div>
<!-- Matrix Footer summary -->
<div class="p-4 bg-surface-container-low/30 flex items-center justify-between text-on-surface-variant">
<div class="flex items-center gap-2 font-label-sm text-label-sm">
<span class="material-symbols-outlined text-body-md text-tertiary">check_circle</span>
<span>Toutes les modifications appliquées sont consignées dans le registre de traçabilité légale.</span>
</div>
<button class="text-primary hover:underline font-label-sm text-label-sm font-semibold" type="button">
            Réinitialiser aux valeurs par défaut
          </button>
</div>
</div>
</div>
<!-- Right Column: Institutional Identity & Compliance Cards (4 cols) -->
<div class="lg:col-span-4 space-y-6">
<!-- Card 1: Enterprise Profile & Identification -->
<div class="bg-surface-container-lowest rounded-xl p-5 shadow-sm space-y-4">
<div class="flex items-start justify-between">
<div class="flex items-center gap-3">
<div class="w-10 h-10 rounded-xl bg-surface-container-high text-primary flex items-center justify-center font-bold">
              CT
            </div>
<div>
<h2 class="font-headline-sm text-headline-sm text-on-surface font-bold leading-tight">Carthage Tech Solutions</h2>
<span class="font-label-sm text-label-sm text-on-surface-variant">SARL • Capital 120 000 TND</span>
</div>
</div>
<span class="bg-tertiary-fixed text-on-tertiary-fixed font-label-sm text-label-sm px-2 py-0.5 rounded-full font-semibold">
            Actif
          </span>
</div>
<div class="space-y-3 pt-2">
<div class="flex flex-col bg-surface-container-low p-3 rounded-lg">
<span class="font-label-sm text-label-sm text-on-surface-variant font-medium">Matricule Fiscal (RNE)</span>
<span class="font-numeric-md text-numeric-md text-on-surface font-semibold tracking-wider">1428590/A/M/000</span>
</div>
<div class="flex flex-col bg-surface-container-low p-3 rounded-lg">
<div class="flex items-center justify-between">
<span class="font-label-sm text-label-sm text-on-surface-variant font-medium">Affiliation CNSS</span>
<span class="material-symbols-outlined text-tertiary text-base">verified</span>
</div>
<span class="font-numeric-md text-numeric-md text-on-surface font-semibold tracking-wider">29481900-12</span>
</div>
<div class="flex flex-col bg-surface-container-low p-3 rounded-lg">
<span class="font-label-sm text-label-sm text-on-surface-variant font-medium">Siège Social &amp; Direction</span>
<span class="font-body-sm text-body-sm text-on-surface font-medium leading-snug">
              Immeuble Le Grand Lac, Rue du Lac Biwa, Les Berges du Lac 2, 1053 Tunis
            </span>
</div>
</div>
<button class="w-full py-2 bg-surface-container-low hover:bg-surface-container text-on-surface font-label-md text-label-md rounded-lg flex items-center justify-center gap-1.5 transition-colors" type="button">
<span class="material-symbols-outlined text-body-md">edit_square</span>
<span>Modifier l'immatriculation</span>
</button>
</div>
<!-- Card 2: Legal Fiscal Scale 2025 -->
<div class="bg-surface-container-lowest rounded-xl p-5 shadow-sm space-y-4">
<div class="flex items-center justify-between">
<div class="flex items-center gap-2">
<div class="w-8 h-8 rounded-lg bg-primary-fixed text-primary flex items-center justify-center">
<span class="material-symbols-outlined text-body-md">balance</span>
</div>
<h3 class="font-headline-sm text-headline-sm text-on-surface">IRPP &amp; Barèmes 2025</h3>
</div>
<span class="text-tertiary font-label-sm text-label-sm bg-tertiary-fixed px-2 py-0.5 rounded-full font-bold">Loi de Finances</span>
</div>
<p class="font-body-sm text-body-sm text-on-surface-variant">
          Application des tranches d'imposition conformes au barème révisé 2025 avec abattements forfaitaires automatiques :
        </p>
<!-- Mini Fiscal Visualization -->
<div class="space-y-2 bg-surface-container-low/50 p-3 rounded-lg">
<div class="flex justify-between items-center text-xs pb-1">
<span class="font-medium text-on-surface-variant">Tranche 0 à 5 000 TND</span>
<span class="font-bold text-tertiary">0%</span>
</div>
<div class="w-full bg-surface-container-high h-1.5 rounded-full overflow-hidden">
<div class="bg-tertiary h-full w-[15%]"></div>
</div>
<div class="flex justify-between items-center text-xs pt-1 pb-1">
<span class="font-medium text-on-surface-variant">5 001 à 20 000 TND</span>
<span class="font-bold text-primary">26%</span>
</div>
<div class="w-full bg-surface-container-high h-1.5 rounded-full overflow-hidden">
<div class="bg-primary h-full w-[45%]"></div>
</div>
<div class="flex justify-between items-center text-xs pt-1 pb-1">
<span class="font-medium text-on-surface-variant">20 001 à 30 000 TND</span>
<span class="font-bold text-on-primary-fixed-variant">28%</span>
</div>
<div class="w-full bg-surface-container-high h-1.5 rounded-full overflow-hidden">
<div class="bg-on-primary-fixed-variant h-full w-[65%]"></div>
</div>
<div class="flex justify-between items-center text-xs pt-1 pb-1">
<span class="font-medium text-on-surface-variant">30 001 à 50 000 TND</span>
<span class="font-bold text-secondary">32%</span>
</div>
<div class="w-full bg-surface-container-high h-1.5 rounded-full overflow-hidden">
<div class="bg-secondary h-full w-[80%]"></div>
</div>
<div class="flex justify-between items-center text-xs pt-1 pb-1">
<span class="font-medium text-on-surface-variant">&gt; 50 000 TND</span>
<span class="font-bold text-error">35%</span>
</div>
<div class="w-full bg-surface-container-high h-1.5 rounded-full overflow-hidden">
<div class="bg-error h-full w-[100%]"></div>
</div>
</div>
<div class="space-y-2">
<div class="flex items-center justify-between text-body-sm text-on-surface">
<span class="flex items-center gap-1.5">
<span class="material-symbols-outlined text-xs text-primary">check</span>
              Abattement Chef de Famille (300 TND)
            </span>
<span class="font-bold text-tertiary">Actif</span>
</div>
<div class="flex items-center justify-between text-body-sm text-on-surface">
<span class="flex items-center gap-1.5">
<span class="material-symbols-outlined text-xs text-primary">check</span>
              Déduction Enfants à Charge (100 à 2000 TND)
            </span>
<span class="font-bold text-tertiary">Actif</span>
</div>
<div class="flex items-center justify-between text-body-sm text-on-surface">
<span class="flex items-center gap-1.5">
<span class="material-symbols-outlined text-xs text-primary">check</span>
              Cotisation Solidarité Sociale (CSS 1%)
            </span>
<span class="font-bold text-tertiary">Calcul auto</span>
</div>
</div>
<button class="w-full text-center text-primary font-label-md text-label-md hover:underline pt-1" type="button">
          Consulter le simulateur d'impact fiscal →
        </button>
</div>
<!-- Card 3: Security & Multi-Factor Authentication -->
<div class="bg-surface-container-lowest rounded-xl p-5 shadow-sm space-y-4">
<div class="flex items-center justify-between">
<div class="flex items-center gap-2">
<div class="w-8 h-8 rounded-lg bg-surface-container-high text-on-surface flex items-center justify-center">
<span class="material-symbols-outlined text-body-md">lock</span>
</div>
<h3 class="font-headline-sm text-headline-sm text-on-surface">Sécurité &amp; Politiques SSO</h3>
</div>
<span class="material-symbols-outlined text-tertiary">lock_open_right</span>
</div>
<div class="space-y-3">
<div class="flex items-center justify-between p-3 bg-surface-container-low rounded-lg">
<div class="flex flex-col">
<span class="font-label-md text-label-md text-on-surface font-semibold">2FA Authenticator (TOTP)</span>
<span class="font-body-sm text-body-sm text-on-surface-variant">Obligatoire pour Profils Admin &amp; RH</span>
</div>
<label class="relative inline-flex items-center cursor-pointer">
<input checked="" class="sr-only peer" type="checkbox"/>
<div class="w-11 h-6 bg-surface-container-highest peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary-container"></div>
</label>
</div>
<div class="flex items-center justify-between p-3 bg-surface-container-low rounded-lg">
<div class="flex flex-col">
<span class="font-label-md text-label-md text-on-surface font-semibold">SSO Google Workspace</span>
<span class="font-body-sm text-body-sm text-on-surface-variant">Domaine @carthagetech.tn</span>
</div>
<span class="font-label-sm text-label-sm font-bold text-tertiary bg-tertiary-fixed px-2 py-0.5 rounded">Connecté</span>
</div>
<div class="flex items-center justify-between p-3 bg-surface-container-low rounded-lg">
<div class="flex flex-col">
<span class="font-label-md text-label-md text-on-surface font-semibold">Expiration de session</span>
<span class="font-body-sm text-body-sm text-on-surface-variant">Déconnexion automatique</span>
</div>
<select class="bg-surface-container-lowest text-on-surface font-body-sm text-body-sm rounded px-2 py-1 focus:ring-1 focus:ring-primary outline-none">
<option>15 minutes</option>
<option selected="">30 minutes</option>
<option>1 heure</option>
</select>
</div>
</div>
<div class="pt-2 text-center">
<a class="text-on-surface-variant hover:text-on-surface font-label-sm text-label-sm flex items-center justify-center gap-1" href="#">
<span class="material-symbols-outlined text-xs">vpn_key</span>
<span>Gérer les clés d'API bancaires &amp; Webhooks</span>
</a>
</div>
</div>
</div>
</div>
<!-- Micro-Toast Notification Container (Client-Side trigger) -->
<div class="fixed bottom-6 right-6 bg-on-background text-inverse-on-surface px-5 py-3.5 rounded-xl shadow-xl flex items-center gap-3 transform translate-y-24 opacity-0 transition-all duration-300 z-50 pointer-events-none" id="toast">
<span class="material-symbols-outlined text-tertiary-fixed text-body-lg">check_circle</span>
<div class="flex flex-col">
<span class="font-label-md text-label-md font-bold">Modifications enregistrées avec succès</span>
<span class="font-body-sm text-body-sm opacity-80">La matrice de sécurité et les barèmes ont été propagés.</span>
</div>
</div>
<script>
    (function() {
      const saveBtn = document.getElementById('save-btn');
      const toast = document.getElementById('toast');

      if (saveBtn && toast) {
        saveBtn.addEventListener('click', function() {
          const originalContent = saveBtn.innerHTML;
          saveBtn.disabled = true;
          saveBtn.innerHTML = '<span class="material-symbols-outlined animate-spin text-body-lg">sync</span><span>Enregistrement...</span>';
          
          setTimeout(function() {
            saveBtn.disabled = false;
            saveBtn.innerHTML = originalContent;
            
            // Show Toast
            toast.classList.remove('translate-y-24', 'opacity-0', 'pointer-events-none');
            toast.classList.add('translate-y-0', 'opacity-100');
            
            setTimeout(function() {
              toast.classList.add('translate-y-24', 'opacity-0', 'pointer-events-none');
              toast.classList.remove('translate-y-0', 'opacity-100');
            }, 3500);
          }, 600);
        });
      }
    })();
  </script>
</div>
</x-admin-shell>
