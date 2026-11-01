<x-auth-shell tab="login" title="Nouveau mot de passe">
    <div class="mb-6">
        <div class="w-12 h-12 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center mb-4 ring-1 ring-brand-100">
            <span class="material-symbols-outlined text-[26px]">password</span>
        </div>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Choisissez un nouveau mot de passe</h1>
        <p class="text-sm text-slate-500 mt-1.5">
            Votre nouveau mot de passe remplacera immédiatement l'ancien. Utilisez au moins 8 caractères, avec un mélange de lettres et de chiffres.
        </p>
    </div>

    <form class="space-y-4" method="POST" action="{{ route('password.store') }}">
        @csrf

        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div>
            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5" for="reset-email">
                Adresse e-mail
            </label>
            <div class="relative rounded-lg shadow-sm">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <span class="material-symbols-outlined text-[20px]">mail</span>
                </div>
                <input class="block w-full pl-10 pr-4 py-3 rounded-lg border {{ $errors->has('email') ? 'border-rose-400' : 'border-slate-300' }} text-slate-900 placeholder-slate-400 text-sm focus:ring-2 focus:ring-brand-600 focus:border-brand-600 transition-colors"
                       id="reset-email" name="email" type="email" value="{{ old('email', $request->email) }}"
                       required autocomplete="username"/>
            </div>
            @error('email')
                <p class="mt-1.5 text-xs font-medium text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5" for="reset-password">
                Nouveau mot de passe
            </label>
            <div class="relative rounded-lg shadow-sm">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <span class="material-symbols-outlined text-[20px]">lock</span>
                </div>
                <input class="block w-full pl-10 pr-12 py-3 rounded-lg border {{ $errors->has('password') ? 'border-rose-400' : 'border-slate-300' }} text-slate-900 placeholder-slate-400 text-sm focus:ring-2 focus:ring-brand-600 focus:border-brand-600 transition-colors"
                       id="reset-password" name="password" type="password" placeholder="••••••••••••"
                       required autofocus autocomplete="new-password"/>
                <button aria-label="Afficher ou masquer le mot de passe" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 focus:outline-none" onclick="togglePasswordVisibility('reset-password', this)" type="button">
                    <span class="material-symbols-outlined text-[20px]">visibility_off</span>
                </button>
            </div>
            @error('password')
                <p class="mt-1.5 text-xs font-medium text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5" for="reset-password-confirmation">
                Confirmer le mot de passe
            </label>
            <div class="relative rounded-lg shadow-sm">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <span class="material-symbols-outlined text-[20px]">lock_reset</span>
                </div>
                <input class="block w-full pl-10 pr-12 py-3 rounded-lg border {{ $errors->has('password_confirmation') ? 'border-rose-400' : 'border-slate-300' }} text-slate-900 placeholder-slate-400 text-sm focus:ring-2 focus:ring-brand-600 focus:border-brand-600 transition-colors"
                       id="reset-password-confirmation" name="password_confirmation" type="password" placeholder="••••••••••••"
                       required autocomplete="new-password"/>
                <button aria-label="Afficher ou masquer la confirmation" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 focus:outline-none" onclick="togglePasswordVisibility('reset-password-confirmation', this)" type="button">
                    <span class="material-symbols-outlined text-[20px]">visibility_off</span>
                </button>
            </div>
            @error('password_confirmation')
                <p class="mt-1.5 text-xs font-medium text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="pt-2">
            <button class="w-full py-3.5 px-4 bg-brand-600 hover:bg-brand-700 active:bg-brand-800 text-white font-semibold text-sm rounded-lg shadow-md hover:shadow-lg transition-all duration-150 flex items-center justify-center gap-2 group" type="submit">
                <span class="material-symbols-outlined text-[20px]">task_alt</span>
                <span>Enregistrer le nouveau mot de passe</span>
            </button>
        </div>
    </form>

    <div class="mt-6 rounded-xl bg-slate-50 border border-slate-200 p-4 flex items-start gap-3">
        <span class="material-symbols-outlined text-[20px] text-slate-400 shrink-0">shield</span>
        <p class="text-xs text-slate-600 leading-relaxed">
            Par sécurité, toutes vos sessions actives seront déconnectées après la modification.
        </p>
    </div>

    <div class="mt-6 text-center">
        <a class="inline-flex items-center gap-1.5 text-xs sm:text-sm font-semibold text-brand-600 hover:text-brand-700 hover:underline transition-colors" href="{{ route('login') }}">
            <span class="material-symbols-outlined text-[18px]">arrow_back</span>
            <span>Retour à la connexion</span>
        </a>
    </div>
</x-auth-shell>
