<script>
    (() => {
        const fieldContainer = (field) => {
            let node = field.parentElement;
            if (node && node.classList.contains('relative')) node = node.parentElement;

            return node;
        };

        const formatDate = (value) => {
            const parts = String(value || '').split('-');

            return parts.length === 3 ? `${parts[2]}/${parts[1]}/${parts[0]}` : value;
        };

        const messageFor = (field) => {
            const validity = field.validity;

            if (validity.valueMissing) {
                return field.tagName === 'SELECT' ? 'Veuillez sélectionner une option.' : 'Ce champ est obligatoire.';
            }

            if (validity.typeMismatch && field.type === 'email') {
                return 'Saisissez une adresse e-mail valide.';
            }

            if (validity.typeMismatch && field.type === 'url') {
                return 'Saisissez une adresse web valide.';
            }

            if (validity.badInput) {
                return field.type === 'number' ? 'Saisissez un montant valide.' : 'Saisissez une valeur valide.';
            }

            if (validity.rangeUnderflow) {
                return field.type === 'date'
                    ? `La date ne peut pas être antérieure au ${formatDate(field.min)}.`
                    : `La valeur minimale autorisée est ${field.min}.`;
            }

            if (validity.rangeOverflow) {
                return field.type === 'date'
                    ? `La date ne peut pas être postérieure au ${formatDate(field.max)}.`
                    : `La valeur maximale autorisée est ${field.max}.`;
            }

            if (validity.stepMismatch) {
                return `Saisissez un montant multiple de ${field.step}.`;
            }

            if (validity.tooShort) {
                return `Saisissez au moins ${field.minLength} caractères.`;
            }

            if (validity.tooLong) {
                return `Ne dépassez pas ${field.maxLength} caractères.`;
            }

            if (validity.patternMismatch) {
                return 'Le format saisi n’est pas valide.';
            }

            return 'La valeur saisie n’est pas valide.';
        };

        const clearError = (field) => {
            delete field.dataset.clientInvalid;
            field.style.boxShadow = '';
            if (field.tagName === 'SELECT') field.style.borderColor = '';
            fieldContainer(field)?.querySelectorAll('[data-client-error]').forEach((node) => node.remove());
        };

        const showError = (field, message) => {
            clearError(field);
            field.dataset.clientInvalid = '1';
            field.style.boxShadow = '0 0 0 2px rgb(var(--c-error))';
            if (field.tagName === 'SELECT') field.style.borderColor = 'rgb(var(--c-error))';

            const container = fieldContainer(field);
            if (!container) return;

            const hint = document.createElement('span');
            hint.className = 'font-label-sm text-label-sm text-error';
            hint.dataset.clientError = '1';
            hint.textContent = message;
            container.appendChild(hint);
        };

        document.querySelectorAll('form').forEach((form) => {
            form.noValidate = true;
        });

        document.addEventListener('input', (event) => {
            if (event.target.dataset?.clientInvalid) clearError(event.target);
        }, true);

        document.addEventListener('change', (event) => {
            if (event.target.dataset?.clientInvalid) clearError(event.target);
        }, true);

        document.addEventListener('submit', (event) => {
            const form = event.target;
            if (!(form instanceof HTMLFormElement) || form.noValidate !== true) return;

            const fields = Array.from(form.querySelectorAll('input, select, textarea'))
                .filter((field) => !field.disabled && !['hidden', 'submit', 'button', 'reset', 'file'].includes(field.type));

            let invalid = null;

            fields.forEach((field) => {
                if (field.checkValidity()) {
                    clearError(field);

                    return;
                }

                showError(field, messageFor(field));
                invalid = invalid || field;
            });

            if (!invalid) return;

            event.preventDefault();
            event.stopPropagation();

            const modal = form.closest('[data-modal]');
            if (modal) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }

            invalid.focus({ preventScroll: true });
            invalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }, true);
    })();
</script>
