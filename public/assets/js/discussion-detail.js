/** Coordinates sharing, nested-reply targeting, and progressive reply-editor disclosure. */
(() => {
    const shareButton = document.querySelector('[data-share-discussion]');
    const replyTargets = Array.from(document.querySelectorAll('[data-reply-target]'));
    const replyParentInput = document.querySelector('#reply-parent-id');
    const replyParentPreview = document.querySelector('[data-reply-parent-preview]');
    const replyingToUsername = document.querySelector('[data-replying-to-username]');
    const replyTextarea = document.querySelector('#reply-content');
    const replyEditor = document.querySelector('#reply-editor');
    const replyEditorOpenButton = document.querySelector('[data-reply-editor-open]');
    const replyEditorPanel = document.querySelector('[data-reply-editor-panel]');
    const replyEditorCancelButton = document.querySelector('[data-reply-editor-cancel]');

    if (!shareButton && replyTargets.length === 0 && !replyEditorOpenButton) {
        return;
    }

    const setReplyEditorOpen = (isOpen, shouldFocus = true) => {
        if (!replyEditorOpenButton || !replyEditorPanel) {
            return;
        }

        replyEditorOpenButton.classList.toggle('hidden', isOpen);
        replyEditorOpenButton.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        replyEditorPanel.classList.toggle('hidden', !isOpen);

        if (!shouldFocus) {
            return;
        }

        window.requestAnimationFrame(() => {
            if (isOpen && replyTextarea) {
                replyTextarea.focus({ preventScroll: true });
                return;
            }

            if (!isOpen) {
                replyEditorOpenButton.focus({ preventScroll: true });
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
        setReplyEditorOpen(true, false);

        if (replyEditor) {
            replyEditor.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }

        if (replyTextarea) {
            window.requestAnimationFrame(() => {
                replyTextarea.focus({ preventScroll: true });
            });
        }
    };

    replyEditorOpenButton?.addEventListener('click', () => {
        setReplyEditorOpen(true);
    });

    replyEditorCancelButton?.addEventListener('click', () => {
        clearReplyTarget();
        setReplyEditorOpen(false);
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

        const replyEditorLink = target.closest('a[href="#reply-editor"]');

        if (replyEditorLink && replyEditorOpenButton) {
            event.preventDefault();
            setReplyEditorOpen(true, false);
            replyEditor?.scrollIntoView({ behavior: 'smooth', block: 'start' });

            window.requestAnimationFrame(() => {
                replyTextarea?.focus({ preventScroll: true });
            });
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
