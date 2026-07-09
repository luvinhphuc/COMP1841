(() => {
    const actionMenus = Array.from(document.querySelectorAll('[data-action-menu]'));
    const shareButton = document.querySelector('[data-share-discussion]');
    const modals = Array.from(document.querySelectorAll('[data-modal]'));
    const replyTargets = Array.from(document.querySelectorAll('[data-reply-target]'));
    const replyParentInput = document.querySelector('#reply-parent-id');
    const replyParentPreview = document.querySelector('[data-reply-parent-preview]');
    const replyingToUsername = document.querySelector('[data-replying-to-username]');
    const replyTextarea = document.querySelector('#reply-content');
    const replyEditor = document.querySelector('#reply-editor');
    let lastModalTrigger = null;

    if (!shareButton && actionMenus.length === 0 && modals.length === 0 && replyTargets.length === 0) {
        return;
    }

    const closeActionMenu = (menu) => {
        const button = menu.querySelector('[data-action-menu-button]');
        const dropdown = menu.querySelector('[data-action-menu-dropdown]');

        if (!button || !dropdown) {
            return;
        }

        button.setAttribute('aria-expanded', 'false');
        dropdown.dataset.open = 'false';
    };

    const closeAllActionMenus = (exceptMenu = null) => {
        actionMenus.forEach((menu) => {
            if (menu !== exceptMenu) {
                closeActionMenu(menu);
            }
        });
    };

    const focusFirstModalControl = (modal) => {
        const focusTarget = modal.querySelector('input:not([type="hidden"]), select, textarea, button:not([data-close-modal]), a[href]');

        if (focusTarget instanceof HTMLElement) {
            focusTarget.focus({ preventScroll: true });
        }
    };

    const openModal = (modal, trigger = null) => {
        if (!(modal instanceof HTMLDialogElement)) {
            return;
        }

        closeAllActionMenus();
        lastModalTrigger = trigger instanceof HTMLElement ? trigger : null;

        if (!modal.open) {
            if (typeof modal.showModal === 'function') {
                modal.showModal();
            } else {
                modal.setAttribute('open', '');
            }
        }

        window.requestAnimationFrame(() => {
            focusFirstModalControl(modal);
        });
    };

    const closeModal = (modal) => {
        if (!(modal instanceof HTMLDialogElement) || !modal.open) {
            return;
        }

        modal.close();
    };

    const clearReplyTarget = () => {
        if (replyParentInput) {
            replyParentInput.value = '';
        }

        if (replyingToUsername) {
            replyingToUsername.textContent = '';
        }

        if (replyParentPreview) {
            replyParentPreview.classList.add('hidden');
            replyParentPreview.classList.remove('flex');
        }
    };

    const setReplyTarget = (trigger) => {
        if (!replyParentInput || !replyParentPreview || !replyingToUsername) {
            return;
        }

        const replyId = trigger.dataset.replyId || '';
        const username = trigger.dataset.replyUsername || '';

        if (!replyId || !username) {
            clearReplyTarget();
            return;
        }

        replyParentInput.value = replyId;
        replyingToUsername.textContent = username;
        replyParentPreview.classList.remove('hidden');
        replyParentPreview.classList.add('flex');

        if (replyEditor) {
            replyEditor.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }

        if (replyTextarea) {
            window.requestAnimationFrame(() => {
                replyTextarea.focus({ preventScroll: true });
            });
        }
    };

    actionMenus.forEach((menu) => {
        const button = menu.querySelector('[data-action-menu-button]');
        const dropdown = menu.querySelector('[data-action-menu-dropdown]');

        if (!button || !dropdown) {
            return;
        }

        button.addEventListener('click', () => {
            const willOpen = button.getAttribute('aria-expanded') !== 'true';

            closeAllActionMenus(menu);
            button.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
            dropdown.dataset.open = willOpen ? 'true' : 'false';
        });

        dropdown.addEventListener('click', (event) => {
            const target = event.target;

            if (target instanceof Element && target.closest('a, button')) {
                closeActionMenu(menu);
            }
        });
    });

    document.addEventListener('click', (event) => {
        if (!(event.target instanceof Node)) {
            return;
        }

        if (!actionMenus.some((menu) => menu.contains(event.target))) {
            closeAllActionMenus();
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            closeAllActionMenus();
        }
    });

    document.addEventListener('click', (event) => {
        const target = event.target;

        if (!(target instanceof Element)) {
            return;
        }

        const replyTarget = target.closest('[data-reply-target]');

        if (replyTarget instanceof HTMLElement) {
            event.preventDefault();
            setReplyTarget(replyTarget);
            return;
        }

        const clearReplyTargetButton = target.closest('[data-clear-reply-target]');

        if (clearReplyTargetButton) {
            event.preventDefault();
            clearReplyTarget();
            return;
        }

        const modalTrigger = target.closest('[data-open-modal]');

        if (modalTrigger instanceof HTMLElement) {
            const modalId = modalTrigger.dataset.openModal || '';
            const modal = document.getElementById(modalId);

            if (modal instanceof HTMLDialogElement) {
                event.preventDefault();
                openModal(modal, modalTrigger);
            }

            return;
        }

        const closeTrigger = target.closest('[data-close-modal]');

        if (closeTrigger) {
            const modal = closeTrigger.closest('dialog');

            if (modal instanceof HTMLDialogElement) {
                closeModal(modal);
            }
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
            closeAllActionMenus();

            if (lastModalTrigger) {
                lastModalTrigger.focus({ preventScroll: true });
                lastModalTrigger = null;
            }
        });

        if (modal.dataset.initialOpen === 'true') {
            openModal(modal);
        }
    });

    if (!shareButton) {
        return;
    }

    const shareLabel = shareButton.querySelector('[data-share-text]');
    const originalLabel = shareButton.dataset.shareLabel || shareButton.textContent.trim();
    const sharedLabel = shareButton.dataset.sharedLabel || 'Copied';
    const setShareLabel = (label) => {
        if (shareLabel) {
            shareLabel.textContent = label;
            return;
        }

        shareButton.textContent = label;
    };

    const resetLabel = () => {
        window.setTimeout(() => {
            setShareLabel(originalLabel);
        }, 1800);
    };

    shareButton.addEventListener('click', async () => {
        const shareData = {
            title: document.title,
            url: window.location.href,
        };

        try {
            if (navigator.share) {
                await navigator.share(shareData);
                return;
            }

            await navigator.clipboard.writeText(window.location.href);
            setShareLabel(sharedLabel);
            resetLabel();
        } catch (error) {
            setShareLabel(originalLabel);
        }
    });
})();
