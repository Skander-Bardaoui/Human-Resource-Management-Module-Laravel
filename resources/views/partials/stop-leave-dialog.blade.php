<!-- Stop / cancel leave confirmation dialog (shared by admin and collaborator pages) -->
<div aria-hidden="true" class="fixed inset-0 z-[60] flex items-center justify-center p-space-md hidden" data-stop-dialog id="stop-leave-dialog">
<div class="absolute inset-0 bg-black/60 backdrop-blur-xs" data-stop-dialog-backdrop></div>
<div aria-labelledby="stop-leave-title" aria-modal="true" class="relative w-full max-w-md bg-surface-container-lowest rounded-2xl shadow-2xl overflow-hidden flex flex-col" role="dialog">
<div class="flex items-start gap-space-md p-space-xl pb-space-md">
<div class="w-11 h-11 rounded-full bg-error-container text-error flex items-center justify-center shrink-0">
<span class="material-symbols-outlined text-headline-sm" data-stop-icon>stop_circle</span>
</div>
<div class="flex flex-col min-w-0 flex-1">
<h2 class="font-headline-md text-headline-md text-on-surface tracking-tight" data-stop-title id="stop-leave-title">Arrêter le congé ?</h2>
<p class="font-body-sm text-body-sm text-on-surface-variant mt-0.5" data-stop-subtitle>Le congé a été approuvé par les RH.</p>
</div>
<button aria-label="Fermer" class="w-8 h-8 rounded-lg hover:bg-surface-container text-on-surface-variant hover:text-on-surface flex items-center justify-center transition-colors shrink-0" data-stop-dialog-close type="button">
<span class="material-symbols-outlined text-body-lg">close</span>
</button>
</div>
<div class="px-space-xl flex flex-col gap-space-md">
<div class="rounded-xl bg-surface-container-low/70 ring-1 ring-outline-variant/40 divide-y divide-outline-variant/30 overflow-hidden">
<div class="flex items-center justify-between gap-space-sm px-space-md py-2.5 hidden" data-stop-row="employee">
<span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Collaborateur</span>
<span class="font-body-md text-body-md text-on-surface font-medium text-right truncate" data-stop-employee></span>
</div>
<div class="flex items-center justify-between gap-space-sm px-space-md py-2.5">
<span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Type de congé</span>
<span class="font-body-md text-body-md text-on-surface font-medium text-right" data-stop-type>—</span>
</div>
<div class="flex items-center justify-between gap-space-sm px-space-md py-2.5">
<span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Période</span>
<span class="font-numeric-md text-numeric-md text-on-surface font-medium text-right" data-stop-period>—</span>
</div>
<div class="flex items-center justify-between gap-space-sm px-space-md py-2.5">
<span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Jours ouvré(s)</span>
<span class="font-body-md text-body-md text-on-surface font-semibold text-right" data-stop-days>—</span>
</div>
</div>
<div class="rounded-xl bg-error-container/40 p-space-md flex items-start gap-space-sm">
<span class="material-symbols-outlined text-error text-body-lg shrink-0 mt-0.5">info</span>
<p class="font-body-sm text-body-sm text-on-error-container" data-stop-consequence></p>
</div>
</div>
<div class="p-space-xl pt-space-lg flex flex-wrap items-center justify-end gap-space-sm">
<button class="h-10 px-space-lg rounded-xl bg-surface-container-low hover:bg-surface-container text-on-surface font-label-md text-label-md transition-colors" data-stop-dialog-close type="button">
          Retour
        </button>
<button class="h-10 px-space-lg rounded-xl bg-error hover:opacity-90 text-on-error font-label-md text-label-md font-semibold flex items-center gap-1.5 transition-all shadow-sm active:scale-95" data-stop-confirm type="button">
<span class="material-symbols-outlined text-body-md" data-stop-confirm-icon>stop_circle</span>
<span data-stop-confirm-label>Confirmer l'arrêt</span>
</button>
</div>
</div>
</div>
<script>
  (function() {
    const dialog = document.getElementById('stop-leave-dialog');

    if (!dialog) return;

    const employeeRow = dialog.querySelector('[data-stop-row="employee"]');
    const employeeValue = dialog.querySelector('[data-stop-employee]');
    const typeValue = dialog.querySelector('[data-stop-type]');
    const periodValue = dialog.querySelector('[data-stop-period]');
    const daysValue = dialog.querySelector('[data-stop-days]');
    const title = dialog.querySelector('[data-stop-title]');
    const subtitle = dialog.querySelector('[data-stop-subtitle]');
    const icon = dialog.querySelector('[data-stop-icon]');
    const consequence = dialog.querySelector('[data-stop-consequence]');
    const confirmLabel = dialog.querySelector('[data-stop-confirm-label]');
    const confirmIcon = dialog.querySelector('[data-stop-confirm-icon]');
    const confirmBtn = dialog.querySelector('[data-stop-confirm]');

    let pendingForm = null;
    let previousOverflow = null;

    const plural = (days) => days + ' jour' + (days > 1 ? 's' : '') + ' ouvré' + (days > 1 ? 's' : '');

    function fill(form) {
      const mode = form.dataset.mode || 'cancel';
      const days = parseInt(form.dataset.days || '0', 10);
      const employee = (form.dataset.employee || '').trim();
      const isCancel = mode === 'cancel';

      employeeRow.classList.toggle('hidden', employee === '');
      employeeValue.textContent = employee;
      typeValue.textContent = form.dataset.type || '—';
      periodValue.textContent = form.dataset.period || '—';
      daysValue.textContent = plural(days);

      title.textContent = isCancel ? 'Annuler la demande ?' : 'Arrêter le congé ?';
      subtitle.textContent = isCancel
        ? 'La demande n\'a pas encore été validée par les RH.'
        : 'Le congé a été approuvé par les RH.';
      icon.textContent = isCancel ? 'cancel' : 'stop_circle';
      confirmIcon.textContent = isCancel ? 'cancel' : 'stop_circle';
      confirmLabel.textContent = isCancel ? 'Confirmer l\'annulation' : 'Confirmer l\'arrêt';

      if (isCancel) {
        consequence.textContent = 'La demande sera annulée définitivement. Aucun jour ne sera décompté de votre solde.';
      } else if (mode === 'progress') {
        consequence.textContent = days === 0
          ? 'Le congé prendra fin aujourd\'hui. Aucun jour restant ne sera réintégré au solde.'
          : 'Le congé prendra fin aujourd\'hui. Les jours déjà consommés restent décomptés et ' + plural(days) + ' seront réintégrés au solde.';
      } else {
        consequence.textContent = 'Le congé approuvé sera annulé et ' + plural(days) + ' seront réintégrés au solde.';
      }
    }

    function open(form) {
      pendingForm = form;
      fill(form);
      previousOverflow = document.body.style.overflow;
      document.body.style.overflow = 'hidden';
      dialog.classList.remove('hidden');
      dialog.setAttribute('aria-hidden', 'false');
      dialog.querySelector('[data-stop-dialog-close]').focus();
    }

    function close() {
      dialog.classList.add('hidden');
      dialog.setAttribute('aria-hidden', 'true');
      document.body.style.overflow = previousOverflow || '';
      previousOverflow = null;

      if (pendingForm) {
        delete pendingForm.dataset.confirmed;
        pendingForm = null;
      }
    }

    document.addEventListener('submit', function(event) {
      const target = event.target;
      const form = target.matches && target.matches('form[data-stop-leave]') ? target : null;

      if (!form || form.dataset.confirmed === '1') return;

      event.preventDefault();
      open(form);
    }, true);

    dialog.querySelectorAll('[data-stop-dialog-close], [data-stop-dialog-backdrop]').forEach(function(element) {
      element.addEventListener('click', close);
    });

    document.addEventListener('keydown', function(event) {
      if (event.key === 'Escape' && !dialog.classList.contains('hidden')) close();
    });

    confirmBtn.addEventListener('click', function() {
      if (!pendingForm) return;

      const form = pendingForm;
      form.dataset.confirmed = '1';
      dialog.classList.add('hidden');
      dialog.setAttribute('aria-hidden', 'true');
      form.submit();
    });
  })();
</script>
