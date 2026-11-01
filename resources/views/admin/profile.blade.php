@php
    $user = auth()->user();
    $nameParts = preg_split('/\s+/', trim($user->name));
    $initials = strtoupper(substr($nameParts[0] ?? '', 0, 1) . substr($nameParts[1] ?? '', 0, 1));
    $createdLabel = $user->created_at?->locale('fr')->translatedFormat('j F Y');
    $updatedLabel = $user->updated_at?->locale('fr')->translatedFormat('j F Y à H:i');
@endphp

<x-admin-shell active="profile" title="Mon Profil Administrateur">
<div class="max-w-[1200px] mx-auto py-space-xl flex flex-col gap-space-lg">
    <div class="flex flex-col gap-space-xs">
        <div class="flex items-center gap-space-xs text-on-surface-variant font-label-md text-label-md flex-wrap">
            <a class="hover:text-primary transition-colors flex items-center gap-1" href="{{ route('admin.dashboard') }}">
                <span class="material-symbols-outlined text-[15px]">home</span>
                <span>Administration</span>
            </a>
            <span class="text-outline-variant">/</span>
            <span class="text-primary font-semibold">Mon Profil Administrateur</span>
        </div>
        <h1 class="font-headline-lg text-headline-lg text-on-surface tracking-tight">Mon Profil Administrateur</h1>
        <p class="font-body-md text-body-md text-on-surface-variant max-w-3xl">
            Gérez votre identité RH, vos identifiants de connexion et consultez le périmètre d'accès de votre compte d'administration.
        </p>
    </div>

    @if (in_array(session('status'), ['profile-updated', 'password-updated'], true))
        <div class="rounded-xl bg-tertiary-fixed text-on-tertiary-fixed px-space-lg py-space-md flex items-center gap-space-sm shadow-sm" role="status">
            <span class="material-symbols-outlined text-[20px]">check_circle</span>
            <span class="font-label-lg text-label-lg">
                {{ session('status') === 'password-updated' ? 'Votre mot de passe a été mis à jour.' : 'Vos informations ont été enregistrées.' }}
            </span>
        </div>
    @endif

    <!-- Identity Overview -->
    <section class="rounded-xl bg-surface-container-lowest p-space-xl shadow-sm relative overflow-hidden">
        <div class="absolute -right-20 -bottom-24 w-80 h-80 rounded-full bg-primary/5 pointer-events-none blur-3xl"></div>
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-space-lg relative z-10">
            <div class="flex items-start gap-space-lg min-w-0">
                <div class="w-20 h-20 rounded-xl bg-primary-fill flex items-center justify-center shadow-sm shrink-0">
                    <span class="text-on-primary text-headline-lg font-bold">{{ $initials ?: 'A' }}</span>
                </div>
                <div class="flex flex-col gap-1 min-w-0">
                    <div class="flex flex-wrap items-center gap-space-xs">
                        <h2 class="font-headline-md text-headline-md text-on-surface truncate">{{ $user->name }}</h2>
                        <span class="px-2.5 py-0.5 rounded-full bg-secondary-container text-on-secondary-fixed font-label-sm text-label-sm font-semibold flex items-center gap-1">
                            <span class="material-symbols-outlined text-[13px]">admin_panel_settings</span>
                            Administrateur RH
                        </span>
                    </div>
                    <span class="font-body-sm text-body-sm text-on-surface-variant truncate">{{ $user->email }}</span>
                    <div class="flex flex-wrap items-center gap-space-xs mt-1">
                        <span class="px-2 py-0.5 rounded-full bg-surface-container-high text-on-surface font-label-sm text-label-sm">
                            Identifiant&nbsp;: <span class="font-numeric-md text-numeric-md font-semibold">#{{ $user->id }}</span>
                        </span>
                        <span class="px-2 py-0.5 rounded-full bg-surface-container-high text-on-surface-variant font-label-sm text-label-sm">
                            Membre depuis {{ $user->created_at?->format('m/Y') ?? '—' }}
                        </span>
                        <span class="px-2 py-0.5 rounded-full bg-tertiary-fixed text-on-tertiary-fixed font-label-sm text-label-sm flex items-center gap-1 font-medium">
                            <span class="w-1.5 h-1.5 rounded-full bg-tertiary"></span>
                            Compte actif
                        </span>
                    </div>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-space-sm shrink-0">
                <a class="h-10 px-space-lg rounded-xl bg-surface-container text-on-surface font-label-lg text-label-lg flex items-center gap-space-xs hover:bg-surface-container-high transition-all" href="{{ route('admin.dashboard') }}">
                    <span class="material-symbols-outlined text-[18px]">dashboard</span>
                    <span>Tableau de bord</span>
                </a>
                <a class="h-10 px-space-lg rounded-xl bg-primary-fill text-on-primary font-label-lg text-label-lg flex items-center gap-space-xs hover:bg-primary-container shadow-sm transition-all" href="#securite">
                    <span class="material-symbols-outlined text-[18px]">shield_person</span>
                    <span>Sécurité du compte</span>
                </a>
            </div>
        </div>
    </section>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-space-lg items-start">
        <div class="xl:col-span-2 flex flex-col gap-space-lg">
            <!-- Account information -->
            <section class="rounded-xl bg-surface-container-lowest p-space-xl shadow-sm" id="informations">
                <div class="flex items-start gap-space-sm">
                    <div class="w-10 h-10 rounded-xl bg-primary-fixed text-primary flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-[22px]">badge</span>
                    </div>
                    <div>
                        <h2 class="font-headline-sm text-headline-sm text-on-surface font-semibold">Informations du compte</h2>
                        <p class="font-body-sm text-body-sm text-on-surface-variant">Nom affiché et adresse e-mail utilisés pour la connexion.</p>
                    </div>
                </div>

                <form class="mt-space-lg flex flex-col gap-space-md" method="POST" action="{{ route('profile.update') }}">
                    @csrf
                    @method('PATCH')

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-space-md">
                        <div>
                            <x-input-label for="admin-profile-name" value="Nom complet" />
                            <x-text-input class="mt-1 block w-full" id="admin-profile-name" name="name" type="text" :value="old('name', $user->name)" required autofocus autocomplete="name" />
                            <x-input-error class="mt-2" :messages="$errors->get('name')" />
                        </div>
                        <div>
                            <x-input-label for="admin-profile-email" value="Adresse e-mail" />
                            <x-text-input class="mt-1 block w-full" id="admin-profile-email" name="email" type="email" :value="old('email', $user->email)" required autocomplete="username" />
                            <x-input-error class="mt-2" :messages="$errors->get('email')" />
                        </div>
                    </div>

                    <div class="flex items-center justify-end">
                        <button class="h-10 px-space-lg rounded-xl bg-primary-fill text-on-primary font-label-lg text-label-lg hover:bg-primary-container transition-colors flex items-center gap-space-xs" type="submit">
                            <span class="material-symbols-outlined text-[18px]">save</span>
                            <span>Enregistrer</span>
                        </button>
                    </div>
                </form>

                <div class="mt-space-lg pt-space-lg border-t border-surface-container grid grid-cols-1 sm:grid-cols-2 gap-space-md">
                    <div class="flex flex-col gap-0.5">
                        <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Identifiant</span>
                        <span class="font-numeric-md text-numeric-md text-on-surface font-semibold">#{{ $user->id }}</span>
                    </div>
                    <div class="flex flex-col gap-0.5">
                        <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Rôle</span>
                        <span class="font-body-sm text-body-sm text-on-surface font-medium">Administrateur RH</span>
                    </div>
                    <div class="flex flex-col gap-0.5">
                        <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Compte créé le</span>
                        <span class="font-body-sm text-body-sm text-on-surface">{{ $createdLabel ?? '—' }}</span>
                    </div>
                    <div class="flex flex-col gap-0.5">
                        <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Dernière mise à jour</span>
                        <span class="font-body-sm text-body-sm text-on-surface">{{ $updatedLabel ?? '—' }}</span>
                    </div>
                </div>
            </section>

            <!-- Security -->
            <section class="rounded-xl bg-surface-container-lowest p-space-xl shadow-sm" id="securite">
                <div class="flex items-start gap-space-sm">
                    <div class="w-10 h-10 rounded-xl bg-secondary-container text-on-secondary-fixed flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-[22px]">lock</span>
                    </div>
                    <div>
                        <h2 class="font-headline-sm text-headline-sm text-on-surface font-semibold">Sécurité du compte</h2>
                        <p class="font-body-sm text-body-sm text-on-surface-variant">Choisissez un mot de passe long et unique pour protéger l'accès administrateur.</p>
                    </div>
                </div>

                <form class="mt-space-lg flex flex-col gap-space-md" method="POST" action="{{ route('password.update') }}">
                    @csrf
                    @method('PUT')

                    <div>
                        <x-input-label for="admin-current-password" value="Mot de passe actuel" />
                        <x-text-input class="mt-1 block w-full" id="admin-current-password" name="current_password" type="password" autocomplete="current-password" />
                        <x-input-error class="mt-2" :messages="$errors->updatePassword->get('current_password')" />
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-space-md">
                        <div>
                            <x-input-label for="admin-new-password" value="Nouveau mot de passe" />
                            <x-text-input class="mt-1 block w-full" id="admin-new-password" name="password" type="password" autocomplete="new-password" />
                            <x-input-error class="mt-2" :messages="$errors->updatePassword->get('password')" />
                        </div>
                        <div>
                            <x-input-label for="admin-password-confirmation" value="Confirmation" />
                            <x-text-input class="mt-1 block w-full" id="admin-password-confirmation" name="password_confirmation" type="password" autocomplete="new-password" />
                            <x-input-error class="mt-2" :messages="$errors->updatePassword->get('password_confirmation')" />
                        </div>
                    </div>

                    <div class="flex items-center justify-end">
                        <button class="h-10 px-space-lg rounded-xl bg-surface-container text-on-surface font-label-lg text-label-lg hover:bg-surface-container-high transition-colors flex items-center gap-space-xs" type="submit">
                            <span class="material-symbols-outlined text-[18px]">key</span>
                            <span>Mettre à jour le mot de passe</span>
                        </button>
                    </div>
                </form>

                <div class="mt-space-lg pt-space-lg border-t border-surface-container">
                    <div class="flex items-center gap-1.5 mb-space-md">
                        <span class="material-symbols-outlined text-[18px] text-on-surface-variant">passkey</span>
                        <span class="font-label-md text-label-md text-on-surface font-semibold">Protection de session</span>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-space-sm">
                        <div class="rounded-lg bg-surface-container-low p-space-sm flex flex-col gap-0.5">
                            <span class="font-label-sm text-label-sm text-on-surface-variant">Jeton d'accès</span>
                            <span class="font-body-sm text-body-sm text-on-surface font-medium">Expire après {{ $security['access_ttl'] }} minutes</span>
                        </div>
                        <div class="rounded-lg bg-surface-container-low p-space-sm flex flex-col gap-0.5">
                            <span class="font-label-sm text-label-sm text-on-surface-variant">Renouvellement</span>
                            <span class="font-body-sm text-body-sm text-on-surface font-medium">{{ $security['refresh_days'] }} jours glissants</span>
                        </div>
                        <div class="rounded-lg bg-surface-container-low p-space-sm flex flex-col gap-0.5">
                            <span class="font-label-sm text-label-sm text-on-surface-variant">Session mémorisée</span>
                            <span class="font-body-sm text-body-sm text-on-surface font-medium">{{ $security['remember_days'] }} jours</span>
                        </div>
                        <div class="rounded-lg bg-surface-container-low p-space-sm flex flex-col gap-0.5">
                            <span class="font-label-sm text-label-sm text-on-surface-variant">Cookies</span>
                            <span class="font-body-sm text-body-sm text-on-surface font-medium">HttpOnly · SameSite Lax</span>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Module access -->
            <section class="rounded-xl bg-surface-container-lowest p-space-xl shadow-sm" id="modules">
                <div class="flex items-start justify-between gap-space-md">
                    <div class="flex items-start gap-space-sm">
                        <div class="w-10 h-10 rounded-xl bg-tertiary-fixed text-on-tertiary-fixed flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-[22px]">apps</span>
                        </div>
                        <div>
                            <h2 class="font-headline-sm text-headline-sm text-on-surface font-semibold">Modules d'administration</h2>
                            <p class="font-body-sm text-body-sm text-on-surface-variant">Accès direct à chaque domaine RH, avec les éléments en cours de traitement.</p>
                        </div>
                    </div>
                </div>

                <div class="mt-space-lg grid grid-cols-1 sm:grid-cols-2 gap-space-md">
                    @foreach ($modules as $module)
                        <a class="group flex items-start justify-between gap-space-sm rounded-xl border border-surface-container p-space-md hover:border-primary/40 hover:bg-surface-container-low transition-colors" href="{{ $module['url'] }}">
                            <div class="flex items-start gap-space-sm min-w-0">
                                <div class="w-10 h-10 rounded-lg {{ $module['tone'] }} flex items-center justify-center shrink-0">
                                    <span class="material-symbols-outlined text-[20px]">{{ $module['icon'] }}</span>
                                </div>
                                <div class="flex flex-col min-w-0">
                                    <span class="font-label-lg text-label-lg text-on-surface font-semibold group-hover:text-primary transition-colors">{{ $module['label'] }}</span>
                                    <span class="font-body-sm text-body-sm text-on-surface-variant leading-snug">{{ $module['description'] }}</span>
                                </div>
                            </div>
                            <div class="flex flex-col items-end gap-1 shrink-0">
                                @if (! is_null($module['count']))
                                    <span class="px-2 py-0.5 rounded-full font-label-sm text-label-sm font-medium {{ $module['count'] > 0 ? 'bg-secondary-container text-on-secondary-fixed' : 'bg-surface-container text-on-surface-variant' }}">
                                        {{ $module['count'] }} {{ $module['countLabel'] }}
                                    </span>
                                @endif
                                <span class="material-symbols-outlined text-[18px] text-on-surface-variant group-hover:text-primary transition-colors">arrow_outward</span>
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>
        </div>

        <aside class="flex flex-col gap-space-lg">
            <section class="rounded-xl bg-surface-container-lowest p-space-lg shadow-sm flex flex-col gap-space-md">
                <div class="flex items-center justify-between">
                    <span class="font-label-sm text-label-sm uppercase tracking-wider text-on-surface-variant font-semibold">Détails du compte</span>
                    <span class="px-2 py-0.5 rounded-full bg-tertiary-fixed text-on-tertiary-fixed font-label-sm text-label-sm">Actif</span>
                </div>
                <div class="flex flex-col divide-y divide-surface-container">
                    <div class="flex items-center justify-between py-2">
                        <span class="font-body-sm text-body-sm text-on-surface-variant">Identifiant</span>
                        <span class="font-numeric-md text-numeric-md text-on-surface font-semibold">#{{ $user->id }}</span>
                    </div>
                    <div class="flex items-center justify-between py-2 gap-space-sm">
                        <span class="font-body-sm text-body-sm text-on-surface-variant">E-mail</span>
                        <span class="font-body-sm text-body-sm text-on-surface font-medium truncate max-w-[180px]">{{ $user->email }}</span>
                    </div>
                    <div class="flex items-center justify-between py-2">
                        <span class="font-body-sm text-body-sm text-on-surface-variant">Rôle</span>
                        <span class="font-body-sm text-body-sm text-on-surface font-medium">Administrateur RH</span>
                    </div>
                    <div class="flex items-center justify-between py-2">
                        <span class="font-body-sm text-body-sm text-on-surface-variant">Compte créé</span>
                        <span class="font-body-sm text-body-sm text-on-surface font-medium">{{ $user->created_at?->format('d/m/Y') ?? '—' }}</span>
                    </div>
                    <div class="flex items-center justify-between py-2">
                        <span class="font-body-sm text-body-sm text-on-surface-variant">Dernière activité</span>
                        <span class="font-body-sm text-body-sm text-on-surface font-medium">{{ $user->updated_at?->format('d/m/Y H:i') ?? '—' }}</span>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="w-full h-9 rounded-xl bg-surface-container text-error font-label-md text-label-md hover:bg-error-container hover:text-on-error-container transition-colors flex items-center justify-center gap-1" type="submit">
                        <span class="material-symbols-outlined text-[16px]">logout</span>
                        <span>Se déconnecter</span>
                    </button>
                </form>
            </section>

            <section class="rounded-xl bg-surface-container-low p-space-lg flex flex-col gap-space-sm">
                <div class="flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[20px] text-primary">verified_user</span>
                    <span class="font-label-lg text-label-lg text-on-surface font-semibold">Périmètre d'accès</span>
                </div>
                <div class="flex flex-col gap-2 font-body-sm text-body-sm text-on-surface-variant">
                    <div class="flex items-start gap-2">
                        <span class="material-symbols-outlined text-[16px] text-tertiary shrink-0">check_circle</span>
                        <span>Accès complet aux 8 modules RH de l'entreprise.</span>
                    </div>
                    <div class="flex items-start gap-2">
                        <span class="material-symbols-outlined text-[16px] text-tertiary shrink-0">check_circle</span>
                        <span>Validation des congés, anomalies de pointage et bulletins.</span>
                    </div>
                    <div class="flex items-start gap-2">
                        <span class="material-symbols-outlined text-[16px] text-tertiary shrink-0">check_circle</span>
                        <span>Export des rapports CNSS, IRPP et virements bancaires.</span>
                    </div>
                </div>
            </section>

            <section class="rounded-xl bg-surface-container-low p-space-lg flex items-start gap-space-sm">
                <span class="material-symbols-outlined text-primary text-[20px] shrink-0">info</span>
                <div class="flex flex-col gap-1">
                    <span class="font-label-md text-label-md text-on-surface font-semibold">Profil collaborateur</span>
                    <p class="font-body-sm text-body-sm text-on-surface-variant">
                        Le profil carrière (compétences, documents, entretiens) est réservé aux comptes collaborateurs liés à une fiche employé.
                    </p>
                </div>
            </section>
        </aside>
    </div>
</div>
</x-admin-shell>
