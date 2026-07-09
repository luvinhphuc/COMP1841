window.FormUtils = (() => {
    const createFormUi = (form, options = {}) => {
        const errorSummary = form.ownerDocument.querySelector(
            options.errorSummarySelector,
        );
        const submitButton = form.querySelector('button[type="submit"]');

        const setError = (name, message) => {
            const error = form.querySelector(`[data-error-for='${name}']`);
            const field = form.elements[name];

            if (error) {
                const messageTarget = error.querySelector('[data-error-message]');

                if (messageTarget) {
                    messageTarget.textContent = message;
                } else {
                    error.textContent = message;
                }

                error.classList.toggle('hidden', !message);
                error.classList.toggle('flex', !!message);
            }

            if (field) {
                field.setAttribute('aria-invalid', message ? 'true' : 'false');

                if (message) {
                    field.classList.add(
                        'ring-[#DC2626]',
                        'focus:ring-[#DC2626]/40',
                    );
                    field.classList.remove(
                        'ring-[#E5E7EB]',
                        'focus:ring-[#2563EB]/30',
                    );
                } else {
                    field.classList.remove(
                        'ring-[#DC2626]',
                        'focus:ring-[#DC2626]/40',
                    );
                    field.classList.add(
                        'ring-[#E5E7EB]',
                        'focus:ring-[#2563EB]/30',
                    );
                }
            }
        };

        const showErrorSummary = (message) => {
            if (!errorSummary) {
                return;
            }

            errorSummary.textContent = message;
            errorSummary.className =
                'mb-5 rounded-2xl bg-[#FEF2F2] p-4 text-sm leading-6 text-[#991B1B] ring-1 ring-[#FECACA]';
            errorSummary.setAttribute('role', 'alert');
            errorSummary.setAttribute('tabindex', '-1');
        };

        const clearErrorSummary = () => {
            if (!errorSummary) {
                return;
            }

            errorSummary.textContent = '';
            errorSummary.className = 'sr-only';
            errorSummary.removeAttribute('role');
            errorSummary.removeAttribute('tabindex');
        };

        const resetSubmitButton = () => {
            if (!submitButton) {
                return;
            }

            form.dataset.submitting = 'false';
            submitButton.disabled = false;
            submitButton.textContent =
                submitButton.dataset.submitLabel || options.submitLabel || 'Submit';
        };

        const setSubmitButtonLoading = () => {
            if (!submitButton) {
                return;
            }

            form.dataset.submitting = 'true';
            submitButton.disabled = true;
            submitButton.textContent =
                submitButton.dataset.loadingLabel || options.loadingLabel || 'Submitting...';
        };

        const clearFieldErrors = (fieldNames) => {
            fieldNames.forEach((name) => setError(name, ''));
        };

        return {
            setError,
            showErrorSummary,
            clearErrorSummary,
            resetSubmitButton,
            setSubmitButtonLoading,
            clearFieldErrors,
        };
    };

    return { createFormUi };
})();
