<!-- Portal access toggle confirmation dialog -->
<div aria-hidden="true" class="fixed inset-0 z-[60] flex items-center justify-center p-space-md hidden" data-access-dialog id="access-toggle-dialog">
<div class="absolute inset-0 bg-black/60 backdrop-blur-xs" data-access-dialog-backdrop></div>
<div aria-labelledby="access-toggle-title" aria-modal="true" class="relative w-full max-w-md bg-surface-container-lowest rounded-2xl shadow-2xl overflow-hidden flex flex-col" role="dialog">
<div class="flex items-start gap-space-md p-space-xl pb-space-md">
<div class="w-11 h-11 rounded-full bg-error-container text-error flex items-center justify-center shrink-0" data-access-icon-wrapper>
<span class="material-symbols-outlined text-headline-sm" data-access-icon>lock</span>
</div>
<div class="flex flex-col min-w-0 flex-1">
<h2 class="font-headline-md text-headline-md text-on-surface tracking-tight" data-access-title id="access-toggle-title">Désactiver l'accès au portail ?</h2>
<p class="font-body-sm text-body-sm text-on-surface-variant mt-0.5" data-access-subtitle>Le collaborateur ne pourra plus se connecter.</p>
</div>
<button aria-label="Fermer" class="w-8 h-8 rounded-lg hover:bg-surface-container text-on-surface-variant hover:text-on-surface flex items-center justify-center transition-colors shrink-0" data-access-dialog-close type="button">
<span class="material-symbols-outlined text-body-lg">close</span>
</button>
</div>
<div class="px-space-xl flex flex-col gap-space-md">
<div class="rounded-xl bg-surface-container-low/70 ring-1 ring-outline-variant/40 divide-y divide-outline-variant/30 overflow-hidden">
<div class="flex items-center justify-between gap-space-sm px-space-md py-2.5">
<span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Collaborateur</span>
<span class="font-body-md text-body-md text-on-surface font-medium text-right truncate" data-access-employee>—</span>
</div>
<div class="flex items-center justify-between gap-space-sm px-space-md py-2.5">
<span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Compte portail</span>
<span class="font-body-md text-body-md text-on-surface font-medium text-right truncate" data-access-email>—</span>
</div>
<div class="flex items-center justify-between gap-space-sm px-space-md py-2.5">
<span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Accès actuel</span>
<span class="font-body-md text-body-md text-on-surface font-semibold text-right" data-access-state>—</span>
</div>
</div>
<div class="rounded-xl bg-surface-container-low p-space-md flex items-start gap-space-sm">
<span class="material-symbols-outlined text-error text-body-lg shrink-0 mt-0.5" data-access-consequence-icon>info</span>
<p class="font-body-sm text-body-sm text-on-surface-variant" data-access-consequence></p>
</div>
</div>
<div class="p-space-xl pt-space-lg flex flex-wrap items-center justify-end gap-space-sm">
<button class="h-10 px-space-lg rounded-xl bg-surface-container-low hover:bg-surface-container text-on-surface font-label-md text-label-md transition-colors" data-access-dialog-close type="button">
          Retour
        </button>
<button class="h-10 px-space-lg rounded-xl bg-error hover:opacity-90 text-on-error font-label-md text-label-md font-semibold flex items-center gap-1.5 transition-all shadow-sm active:scale-95" data-access-confirm type="button">
<span class="material-symbols-outlined text-body-md" data-access-confirm-icon>lock</span>
<span data-access-confirm-label>Désactiver l'accès</span>
</button>
</div>
</div>
</div>
<script>
  (function() {
    const dialog = document.getElementById('access-toggle-dialog');

    if (!dialog) return;

    const employeeValue = dialog.querySelector('[data-access-employee]');
    const emailValue = dialog.querySelector('[data-access-email]');
    const stateValue = dialog.querySelector('[data-access-state]');
    const title = dialog.querySelector('[data-access-title]');
    const subtitle = dialog.querySelector('[data-access-subtitle]');
    const icon = dialog.querySelector('[data-access-icon]');
    const iconWrapper = dialog.querySelector('[data-access-icon-wrapper]');
    const consequence = dialog.querySelector('[data-access-consequence]');
    const consequenceIcon = dialog.querySelector('[data-access-consequence-icon]');
    const confirmLabel = dialog.querySelector('[data-access-confirm-label]');
    const confirmIcon = dialog.querySelector('[data-access-confirm-icon]');
    const confirmBtn = dialog.querySelector('[data-access-confirm]');

    let pendingForm = null;
    let previousOverflow = null;

    function fill(form) {
      const enable = (form.dataset.action || 'disable') === 'enable';

      employeeValue.textContent = form.dataset.employee || '—';
      emailValue.textContent = form.dataset.accountEmail || '—';
      stateValue.textContent = enable ? 'Désactivé' : 'Actif';

      title.textContent = enable ? 'Réactiver l\'accès au portail ?' : 'Désactiver l\'accès au portail ?';
      subtitle.textContent = enable
        ? 'Le collaborateur pourra à nouveau se connecter à son espace.'
        : 'La connexion sera bloquée immédiatement.';
      icon.textContent = enable ? 'how_to_reg' : 'lock';
      confirmIcon.textContent = enable ? 'how_to_reg' : 'lock';
      confirmLabel.textContent = enable ? 'Réactiver l\'accès' : 'Désactiver l\'accès';

      iconWrapper.classList.toggle('bg-error-container', !enable);
      iconWrapper.classList.toggle('text-error', !enable);
      iconWrapper.classList.toggle('bg-primary-container', enable);
      iconWrapper.classList.toggle('text-on-primary-container', enable);

      consequenceIcon.textContent = enable ? 'check_circle' : 'info';
      consequenceIcon.classList.toggle('text-error', !enable);
      consequenceIcon.classList.toggle('text-primary', enable);

      consequence.textContent = enable
        ? 'Le compte portail sera réactivé immédiatement. Aucun autre paramètre du profil RH n\'est modifié.'
        : 'Toutes les sessions actives seront révoquées immédiatement et le collaborateur ne pourra plus se connecter. Le profil RH reste inchangé.';

      confirmBtn.classList.toggle('bg-error', !enable);
      confirmBtn.classList.toggle('text-on-error', !enable);
      confirmBtn.classList.toggle('bg-primary-fill', enable);
      confirmBtn.classList.toggle('text-on-primary', enable);
    }

    function open(form) {
      pendingForm = form;
      fill(form);
      previousOverflow = document.body.style.overflow;
      document.body.style.overflow = 'hidden';
      dialog.classList.remove('hidden');
      dialog.setAttribute('aria-hidden', 'false');
      dialog.querySelector('[data-access-dialog-close]').focus();
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
      const form = target.matches && target.matches('form[data-access-toggle]') ? target : null;

      if (!form || form.dataset.confirmed === '1') return;

      event.preventDefault();
      open(form);
    }, true);

    dialog.querySelectorAll('[data-access-dialog-close], [data-access-dialog-backdrop]').forEach(function(element) {
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
