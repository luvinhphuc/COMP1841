/** Locks the contact form during submission to prevent accidental duplicate messages. */
(() => {
    const form = document.querySelector('#contact-form');

    if (!form) {
        return;
    }

    const submitButton = form.querySelector('button[type="submit"]');
    const spinner = submitButton?.querySelector('[data-send-spinner]');
    const label = submitButton?.querySelector('[data-send-label]');

    if (!submitButton || !spinner || !label) {
        return;
    }

    const resetSubmitButton = () => {
        form.dataset.submitting = 'false';
        submitButton.disabled = false;
        submitButton.removeAttribute('aria-busy');
        spinner.classList.add('hidden');
        label.textContent = 'Send message';
    };

    form.addEventListener('submit', (event) => {
        if (form.dataset.submitting === 'true') {
            event.preventDefault();
            return;
        }

        form.dataset.submitting = 'true';
        submitButton.disabled = true;
        submitButton.setAttribute('aria-busy', 'true');
        spinner.classList.remove('hidden');
        label.textContent = 'Sending...';
    });

    window.addEventListener('pageshow', resetSubmitButton);
})();
