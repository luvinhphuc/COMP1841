(() => {
    const modals = Array.from(document.querySelectorAll('[data-modal]'));

    if (modals.length === 0) {
        return;
    }

    let lastTrigger = null;

    const openModal = (modal, trigger) => {
        if (!(modal instanceof HTMLDialogElement)) {
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

    modals.forEach((modal) => {
        if (!(modal instanceof HTMLDialogElement)) {
            return;
        }

        modal.addEventListener('click', (event) => {
            if (event.target === modal) {
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
