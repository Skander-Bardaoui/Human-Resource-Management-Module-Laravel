@php
    $currentEmployee = $currentEmployee ?? null;
    $collaboratorNotifications = $collaboratorNotifications ?? [];
    $collaboratorNotificationCount = $collaboratorNotificationCount ?? count($collaboratorNotifications);

    $toasts = [];

    if (session('success')) {
        $toasts[] = ['kind' => 'success', 'icon' => 'check_circle', 'message' => session('success')];
    }

    if (session('error')) {
        $toasts[] = ['kind' => 'error', 'icon' => 'error', 'message' => session('error')];
    }

    if (session('status')) {
        $toasts[] = ['kind' => 'info', 'icon' => 'info', 'message' => session('status')];
    }

    if (isset($errors) && $errors->any()) {
        $toasts[] = ['kind' => 'validation', 'icon' => 'report', 'message' => $errors->first()];
    }
@endphp

@if (count($toasts))
    <div class="fixed top-20 right-4 z-[70] flex flex-col gap-2 w-[min(92vw,26rem)]" id="hrmFlashToasts" role="status" aria-live="polite">
        @foreach ($toasts as $toast)
            @php
                $tone = match ($toast['kind']) {
                    'success' => 'border-tertiary-container/40 bg-tertiary-fixed text-on-tertiary-fixed',
                    'error', 'validation' => 'border-error/40 bg-error-container text-on-error-container',
                    default => 'border-outline-variant bg-surface-container-lowest text-on-surface',
                };
            @endphp
            <div class="hrm-flash-toast flex items-start gap-2.5 px-4 py-3 rounded-xl border shadow-lg {{ $tone }} transition-all duration-300" data-flash-toast>
                <span class="material-symbols-outlined text-[20px] shrink-0">{{ $toast['icon'] }}</span>
                <span class="font-body-sm text-body-sm flex-1">{{ $toast['message'] }}</span>
                <button class="shrink-0 opacity-70 hover:opacity-100" type="button" aria-label="Fermer" onclick="this.closest('[data-flash-toast]').remove()">
                    <span class="material-symbols-outlined text-[18px]">close</span>
                </button>
            </div>
        @endforeach
    </div>

    <script>
        (() => {
            const dismiss = () => {
                document.querySelectorAll('[data-flash-toast]').forEach((toast) => {
                    toast.classList.add('opacity-0', 'translate-y-[-6px]');
                    setTimeout(() => toast.remove(), 250);
                });
            };

            setTimeout(dismiss, 6000);
        })();
    </script>
@endif
