(() => {
    const actionMenus = Array.from(document.querySelectorAll('[data-action-menu]'));
    const shareButton = document.querySelector('[data-share-discussion]');
    const replyTargets = Array.from(document.querySelectorAll('[data-reply-target]'));
    const replyParentInput = document.querySelector('#reply-parent-id');
    const replyParentPreview = document.querySelector('[data-reply-parent-preview]');
    const replyingToUsername = document.querySelector('[data-replying-to-username]');
    const replyTextarea = document.querySelector('#reply-content');
    const replyEditor = document.querySelector('#reply-editor');

    if (!shareButton && actionMenus.length === 0 && replyTargets.length === 0) {
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
