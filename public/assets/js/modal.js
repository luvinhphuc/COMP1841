/** Connects data-driven modal triggers to native dialogs and restores focus on close. */
(() => {
    const modals = Array.from(document.querySelectorAll('[data-modal]'));

    if (modals.length === 0) {
        return;
    }

    let lastTrigger = null;

    const populateConfirmationModal = (modal, trigger) => {
        if (!modal.hasAttribute('data-confirmation-modal') || !(trigger instanceof HTMLElement)) {
            return true;
        }

        const form = modal.querySelector('[data-confirmation-form]');
        const title = modal.querySelector('[data-confirmation-title]');
        const message = modal.querySelector('[data-confirmation-message]');
        const detail = modal.querySelector('[data-confirmation-detail]');
        const submit = modal.querySelector('[data-confirmation-submit]');
        const action = trigger.dataset.confirmAction || '';

        if (!(form instanceof HTMLFormElement) || action === '') {
            return false;
        }

        form.action = action;

        if (title) {
            title.textContent = trigger.dataset.confirmTitle || 'Please confirm';
        }

        if (message) {
            message.textContent = trigger.dataset.confirmMessage || 'Are you sure you want to continue?';
        }

        if (detail) {
            const detailText = trigger.dataset.confirmDetail || '';
            detail.textContent = detailText;
            detail.classList.toggle('hidden', detailText === '');
        }

        if (submit instanceof HTMLButtonElement) {
            submit.textContent = trigger.dataset.confirmSubmitLabel || 'Confirm';
            submit.disabled = false;
        }

        return true;
    };

    const openModal = (modal, trigger) => {
        if (!(modal instanceof HTMLDialogElement)) {
            return;
        }

        if (!populateConfirmationModal(modal, trigger)) {
            return;
        }

        lastTrigger = trigger instanceof HTMLElement ? trigger : null;

        if (!modal.open) {
            if (typeof modal.showModal === 'function') {
                modal.showModal();
            } else {
                modal.setAttribute('open', '');
            }
        }

        const focusTarget = modal.querySelector('input:not([type="hidden"]), select, textarea, button:not([data-close-modal]), a[href]');

        if (focusTarget instanceof HTMLElement) {
            window.requestAnimationFrame(() => focusTarget.focus({ preventScroll: true }));
        }
    };

    const closeModal = (modal) => {
        if (!(modal instanceof HTMLDialogElement) || !modal.open) {
            return;
        }

        modal.close();
    };

    document.addEventListener('click', (event) => {
        const target = event.target;

        if (!(target instanceof Element)) {
            return;
        }

        const trigger = target.closest('[data-open-modal]');

        if (trigger instanceof HTMLElement) {
            const modal = document.getElementById(trigger.dataset.openModal || '');

            if (modal instanceof HTMLDialogElement) {
                event.preventDefault();
                openModal(modal, trigger);
            }

            return;
        }

        const closeTrigger = target.closest('[data-close-modal]');

        if (closeTrigger) {
            closeModal(closeTrigger.closest('dialog'));
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key !== 'Escape') {
            return;
        }

        const openModalElement = modals.find((modal) => modal instanceof HTMLDialogElement && modal.open);

        if (openModalElement instanceof HTMLDialogElement) {
            event.preventDefault();
            closeModal(openModalElement);
        }
    });

    modals.forEach((modal) => {
        if (!(modal instanceof HTMLDialogElement)) {
            return;
        }

        modal.addEventListener('click', (event) => {
            if (event.target === modal || (event.target instanceof Element && event.target.hasAttribute('data-modal-backdrop'))) {
                closeModal(modal);
            }
        });

        modal.addEventListener('close', () => {
            if (lastTrigger) {
                lastTrigger.focus({ preventScroll: true });
                lastTrigger = null;
            }
        });

        if (modal.dataset.initialOpen === 'true') {
            openModal(modal, null);
        }
    });
})();
