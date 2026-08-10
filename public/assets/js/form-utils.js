/** Shared form-state helpers for errors, submission locking, and summary focus. */
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
            }
        };

        const showErrorSummary = (message) => {
            if (!errorSummary) {
                return;
            }

            errorSummary.textContent = message;
            errorSummary.className = 'ui-alert ui-alert-error mb-5';
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
