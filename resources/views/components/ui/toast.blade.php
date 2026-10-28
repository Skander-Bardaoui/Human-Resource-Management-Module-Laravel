@php
    $success = session('success');
    $error = session('error');
@endphp

@if ($success || $error)
    <div class="fixed bottom-4 right-4 z-[90] flex w-[calc(100%-2rem)] max-w-sm flex-col gap-2" aria-live="polite">
        @if ($success)
            <div class="flex items-start gap-space-sm rounded-xl bg-tertiary-fixed p-space-md text-on-tertiary-fixed shadow-lg" data-toast role="status">
                <span class="material-symbols-outlined text-body-lg shrink-0" aria-hidden="true">check_circle</span>
                <p class="flex-1 font-label-md text-label-md">{{ $success }}</p>
                <button class="rounded p-0.5 text-on-tertiary-fixed/70 hover:text-on-tertiary-fixed focus:outline-none focus:ring-2 focus:ring-on-tertiary-fixed" type="button" onclick="this.parentElement.remove()" aria-label="Fermer la notification">
                    <span class="material-symbols-outlined text-body-md">close</span>
                </button>
            </div>
        @endif

        @if ($error)
            <div class="flex items-start gap-space-sm rounded-xl bg-error-container p-space-md text-on-error-container shadow-lg" data-toast role="alert">
                <span class="material-symbols-outlined text-body-lg shrink-0" aria-hidden="true">error</span>
                <p class="flex-1 font-label-md text-label-md">{{ $error }}</p>
                <button class="rounded p-0.5 text-on-error-container/70 hover:text-on-error-container focus:outline-none focus:ring-2 focus:ring-on-error-container" type="button" onclick="this.parentElement.remove()" aria-label="Fermer la notification">
                    <span class="material-symbols-outlined text-body-md">close</span>
                </button>
            </div>
        @endif
    </div>

    <script>
        window.setTimeout(function () {
            document.querySelectorAll('[data-toast]').forEach(function (toast) {
                toast.remove();
            });
        }, 6000);
    </script>
@endif
