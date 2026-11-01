@php
    $punchOutRecord = $todayRecord ?? null;
    $punchOutCheckIn = $punchOutRecord?->checkInShort() ?? '—';
    $punchOutWorkedMinutes = $punchOutRecord?->liveWorkedMinutes() ?? 0;
    $punchOutWorked = intdiv(max(0, $punchOutWorkedMinutes), 60).'h '.str_pad((string) (max(0, $punchOutWorkedMinutes) % 60), 2, '0', STR_PAD_LEFT).'m';
    $punchOutDate = ($punchOutRecord?->work_date ?? now())->locale('fr')->translatedFormat('d F Y');
    $punchOutEstimated = '—';

    if ($punchOutRecord?->check_in) {
        [$punchH, $punchM] = array_pad(explode(':', (string) $punchOutRecord->check_in), 2, 0);
        $punchExit = (int) $punchH * 60 + (int) $punchM
            + (int) config('hrm.expected_daily_minutes')
            + (int) $punchOutRecord->break_minutes;
        $punchOutEstimated = sprintf('%02d:%02d', intdiv($punchExit, 60) % 24, $punchExit % 60);
    }

    $punchOutWindow = (int) config('hrm.regularization_window_days');
@endphp

<div class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 backdrop-blur-sm p-space-md" id="punch-out-modal" data-modal>
    <div class="w-full max-w-md rounded-2xl bg-surface-container-lowest shadow-2xl overflow-hidden" role="dialog" aria-modal="true" aria-labelledby="punch-out-title">
        <div class="relative overflow-hidden bg-primary-container px-space-xl py-space-lg text-on-primary">
            <div class="absolute -right-8 -top-10 w-28 h-28 rounded-full bg-white/10 pointer-events-none"></div>
            <div class="absolute right-16 -bottom-6 w-14 h-14 rounded-full bg-white/10 pointer-events-none"></div>
            <div class="relative flex items-start justify-between gap-space-sm">
                <div class="flex items-center gap-space-sm">
                    <div class="w-11 h-11 rounded-xl bg-white/15 flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-[24px]">logout</span>
                    </div>
                    <div>
                        <p class="font-label-sm text-label-sm uppercase tracking-wider text-white/70">Badgeuse virtuelle</p>
                        <h3 class="font-headline-sm text-headline-sm font-semibold" id="punch-out-title">Confirmer votre sortie</h3>
                        <p class="font-label-sm text-label-sm text-white/80 mt-0.5 capitalize">{{ $punchOutDate }}</p>
                    </div>
                </div>
                <button class="w-8 h-8 rounded-lg text-white/80 hover:bg-white/15 hover:text-white flex items-center justify-center transition-colors" data-close-punch-out type="button" aria-label="Fermer">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>
        </div>

        <form class="p-space-xl flex flex-col gap-space-md" method="POST" action="{{ route('collaborator.attendance.punch-out') }}">
            @csrf
            <p class="font-body-md text-body-md text-on-surface-variant">
                Vous êtes sur le point de clôturer votre journée. Vérifiez le récapitulatif avant de confirmer.
            </p>

            <div class="grid grid-cols-3 gap-space-sm">
                <div class="rounded-xl bg-surface-container-low p-space-sm text-center">
                    <span class="material-symbols-outlined text-[18px] text-tertiary-container">login</span>
                    <p class="font-label-sm text-label-sm text-on-surface-variant mt-1">Arrivée</p>
                    <p class="font-numeric-md text-numeric-md font-semibold text-on-surface">{{ $punchOutCheckIn }}</p>
                </div>
                <div class="rounded-xl bg-surface-container-low p-space-sm text-center">
                    <span class="material-symbols-outlined text-[18px] text-primary">timer</span>
                    <p class="font-label-sm text-label-sm text-on-surface-variant mt-1">Temps travaillé</p>
                    <p class="font-numeric-md text-numeric-md font-semibold text-on-surface">{{ $punchOutWorked }}</p>
                </div>
                <div class="rounded-xl bg-surface-container-low p-space-sm text-center">
                    <span class="material-symbols-outlined text-[18px] text-secondary">schedule</span>
                    <p class="font-label-sm text-label-sm text-on-surface-variant mt-1">Sortie estimée</p>
                    <p class="font-numeric-md text-numeric-md font-semibold text-on-surface">{{ $punchOutEstimated }}</p>
                </div>
            </div>

            <div class="flex items-start gap-2 rounded-lg bg-surface-container-low px-space-md py-space-sm text-on-surface-variant">
                <span class="material-symbols-outlined text-[18px] text-error shrink-0">info</span>
                <p class="font-body-sm text-body-sm">
                    Une fois confirmée, la sortie est verrouillée. En cas d'erreur, vous pourrez la régulariser dans les {{ $punchOutWindow }} jours depuis votre historique de pointage.
                </p>
            </div>

            <div class="flex items-center gap-space-sm pt-space-xs">
                <button class="flex-1 h-10 rounded-lg bg-surface-container-high text-on-surface font-label-lg text-label-lg font-medium hover:bg-surface-variant transition-colors" data-close-punch-out type="button">Annuler</button>
                <button class="flex-1 h-10 rounded-lg bg-primary-container text-on-primary font-label-lg text-label-lg font-semibold hover:bg-primary-fill transition-colors flex items-center justify-center gap-1.5" type="submit" data-confirm-punch-out>
                    <span class="material-symbols-outlined text-[18px]">logout</span>
                    <span data-confirm-label>Confirmer la sortie</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    (() => {
        const modal = document.getElementById('punch-out-modal');
        if (!modal) return;

        const confirmButton = modal.querySelector('[data-confirm-punch-out]');
        const confirmLabel = modal.querySelector('[data-confirm-label]');

        const open = () => {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            confirmButton?.focus();
        };

        const close = () => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        };

        document.querySelectorAll('[data-open-punch-out]').forEach((trigger) => {
            trigger.addEventListener('click', open);
        });

        modal.querySelectorAll('[data-close-punch-out]').forEach((trigger) => {
            trigger.addEventListener('click', close);
        });

        modal.addEventListener('click', (event) => {
            if (event.target === modal) close();
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && !modal.classList.contains('hidden')) close();
        });

        modal.querySelector('form')?.addEventListener('submit', () => {
            if (confirmButton) {
                confirmButton.disabled = true;
                confirmButton.classList.add('opacity-70', 'pointer-events-none');
            }
            if (confirmLabel) confirmLabel.textContent = 'Enregistrement…';
        });
    })();
</script>
