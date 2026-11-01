<x-auth-shell tab="login" title="Mot de passe oublié">
    <div class="mb-6">
        <div class="w-12 h-12 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center mb-4 ring-1 ring-brand-100">
            <span class="material-symbols-outlined text-[26px]">lock_reset</span>
        </div>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Mot de passe oublié ?</h1>
        <p class="text-sm text-slate-500 mt-1.5">
            Indiquez l'adresse e-mail associée à votre compte. Nous vous enverrons un lien sécurisé pour choisir un nouveau mot de passe.
        </p>
    </div>

    @if (session('status'))
        <div class="mb-4 rounded-lg bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm text-emerald-800 flex items-start gap-2" role="status">
            <span class="material-symbols-outlined text-[18px] shrink-0">mark_email_read</span>
            <span>{{ session('status') }}</span>
        </div>
    @endif

    <form class="space-y-4" method="POST" action="{{ route('password.email') }}">
        @csrf

        <div>
            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5" for="forgot-email">
                Adresse e-mail professionnelle
            </label>
            <div class="relative rounded-lg shadow-sm">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <span class="material-symbols-outlined text-[20px]">mail</span>
                </div>
                <input class="block w-full pl-10 pr-4 py-3 rounded-lg border {{ $errors->has('email') ? 'border-rose-400' : 'border-slate-300' }} text-slate-900 placeholder-slate-400 text-sm focus:ring-2 focus:ring-brand-600 focus:border-brand-600 transition-colors"
                       id="forgot-email" name="email" type="email" value="{{ old('email') }}"
                       placeholder="nom@entreprise.tn" required autofocus autocomplete="username"/>
            </div>
            @error('email')
                <p class="mt-1.5 text-xs font-medium text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="pt-2">
            <button class="w-full py-3.5 px-4 bg-brand-600 hover:bg-brand-700 active:bg-brand-800 text-white font-semibold text-sm rounded-lg shadow-md hover:shadow-lg transition-all duration-150 flex items-center justify-center gap-2 group" type="submit">
                <span class="material-symbols-outlined text-[20px]">send</span>
                <span>Envoyer le lien de réinitialisation</span>
            </button>
        </div>
    </form>

    <div class="mt-6 rounded-xl bg-slate-50 border border-slate-200 p-4 flex items-start gap-3">
        <span class="material-symbols-outlined text-[20px] text-slate-400 shrink-0">schedule</span>
        <p class="text-xs text-slate-600 leading-relaxed">
            Le lien est valable <strong class="text-slate-800">60 minutes</strong>. Pensez à vérifier votre dossier Spam si l'e-mail n'apparaît pas dans votre boîte de réception.
        </p>
    </div>

    <div class="mt-6 text-center">
        <a class="inline-flex items-center gap-1.5 text-xs sm:text-sm font-semibold text-brand-600 hover:text-brand-700 hover:underline transition-colors" href="{{ route('login') }}">
            <span class="material-symbols-outlined text-[18px]">arrow_back</span>
            <span>Retour à la connexion</span>
        </a>
    </div>
</x-auth-shell>
