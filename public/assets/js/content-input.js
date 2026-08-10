/** Initialises each rich content input once and keeps its toolbar state scoped locally. */
export const initContentInputs = (root = document) => {
    const contentInputRoots = [
        ...(root instanceof Element && root.matches('[data-content-input]') ? [root] : []),
        ...Array.from(root.querySelectorAll('[data-content-input]')),
    ];

    contentInputRoots.forEach((contentInputRoot) => {
        if (contentInputRoot.dataset.contentInputInitialised === 'true') {
            return;
        }

        const contentField = contentInputRoot.querySelector('[data-content-field]');
        const codeButton = contentInputRoot.querySelector('[data-content-action="code"]');
        const contentSurface = contentInputRoot.querySelector('[data-content-surface]');
        const contentError = contentInputRoot.querySelector('[data-content-error]');

        if (!(contentField instanceof HTMLTextAreaElement) || !codeButton) {
            return;
        }

        contentInputRoot.dataset.contentInputInitialised = 'true';

        const showContentError = (message) => {
            if (!(contentError instanceof HTMLElement)) {
                return;
            }

            contentError.textContent = message;
            contentError.classList.remove('hidden');
            contentError.classList.add('block');
            contentField.setAttribute('aria-invalid', 'true');

            contentSurface?.classList.remove(
                'border-ui-border-strong',
                'focus-within:border-brand-blue',
                'focus-within:ring-brand-blue/15',
            );
            contentSurface?.classList.add(
                'border-ui-danger',
                'focus-within:border-ui-danger',
                'focus-within:ring-ui-danger/10',
            );
        };

        const clearContentError = () => {
            if (!(contentError instanceof HTMLElement)) {
                return;
            }

            contentError.textContent = '';
            contentError.classList.add('hidden');
            contentError.classList.remove('block');
            contentField.setAttribute('aria-invalid', 'false');

            contentSurface?.classList.remove(
                'border-ui-danger',
                'focus-within:border-ui-danger',
                'focus-within:ring-ui-danger/10',
            );
            contentSurface?.classList.add(
                'border-ui-border-strong',
                'focus-within:border-brand-blue',
                'focus-within:ring-brand-blue/15',
            );
        };

        const requiredMessage = contentField.dataset.requiredMessage || 'Please complete this field.';

        contentField.addEventListener('invalid', (event) => {
            if (!contentField.validity.valueMissing) {
                return;
            }

            event.preventDefault();
            showContentError(requiredMessage);
            contentField.focus();
        });

        contentField.addEventListener('input', () => {
            if (contentField.value.trim() !== '') {
                clearContentError();
            }
        });

        contentField.form?.addEventListener('submit', (event) => {
            if (!contentField.required || contentField.value.trim() !== '') {
                return;
            }

            event.preventDefault();
            showContentError(requiredMessage);
            contentField.focus();
        });

        codeButton.addEventListener('click', () => {
            const selectionStart = contentField.selectionStart;
            const selectionEnd = contentField.selectionEnd;
            const selectedText = contentField.value.slice(selectionStart, selectionEnd);
            const codeText = selectedText !== '' ? selectedText : 'code';
            const codeBlock = `<pre>\n${codeText}\n</pre>`;

            contentField.setRangeText(codeBlock, selectionStart, selectionEnd, 'end');
            contentField.focus();

            if (selectedText === '') {
                const cursorStart = selectionStart + '<pre>\n'.length;
                const cursorEnd = cursorStart + codeText.length;
                contentField.setSelectionRange(cursorStart, cursorEnd);
            }
        });
    });
};

initContentInputs();
