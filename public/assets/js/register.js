/** Provides immediate registration validation while leaving final validation to the server. */
(() => {
    const form = document.querySelector('#register-form');

    if (!form) {
        return;
    }

    const fieldNames = [
        'first_name',
        'last_name',
        'username',
        'email',
        'password',
        'confirm_password',
    ];
    const formUi = window.FormUtils.createFormUi(form, {
        errorSummarySelector: '#register-error-summary',
        submitLabel: 'Create account',
        loadingLabel: 'Creating account...',
    });
    const maxLengths = {
        first_name: 50,
        last_name: 50,
        username: 75,
        email: 150,
        password: 128,
        confirm_password: 128,
    };
    const maxLengthMessages = {
        first_name: 'First name must be 50 characters or fewer.',
        last_name: 'Last name must be 50 characters or fewer.',
        username: 'Username must be 75 characters or fewer.',
        email: 'Email must be 150 characters or fewer.',
        password: 'Password must be 128 characters or fewer.',
        confirm_password: 'Confirm password must be 128 characters or fewer.',
    };
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    const usernamePattern = /^[A-Za-z0-9_.-]+$/;

    // Array.from counts Unicode code points more accurately than String.length.
    const lengthOf = (value) => Array.from(value).length;
    const limitTo = (value, maxLength) =>
        Array.from(value).slice(0, maxLength).join('');
    const passwordField = form.elements.password;
    const confirmationField = form.elements.confirm_password;
    const passwordStatus = form.querySelector('[data-password-status]');
    const passwordHints = form.querySelector('[data-password-hints]');
    const passwordMeterSegments = [
        ...form.querySelectorAll('[data-password-meter-segment]'),
    ];
    const requiredRule = form.querySelector('[data-password-rule="required"]');
    const lengthRule = form.querySelector('[data-password-rule="length"]');
    const confirmationStatus = form.querySelector(
        '[data-confirm-password-status]',
    );

    const getFieldError = (name) => {
        const field = form.elements[name];

        if (!field) {
            return '';
        }

        const value = field.value;
        const trimmedValue = value.trim();

        switch (name) {
            case 'first_name':
                if (!trimmedValue) {
                    return 'First name is required.';
                }

                return lengthOf(trimmedValue) > maxLengths.first_name
                    ? maxLengthMessages.first_name
                    : '';
            case 'last_name':
                if (!trimmedValue) {
                    return 'Last name is required.';
                }

                return lengthOf(trimmedValue) > maxLengths.last_name
                    ? maxLengthMessages.last_name
                    : '';
            case 'username':
                if (!trimmedValue) {
                    return 'Username is required.';
                }

                if (lengthOf(trimmedValue) > maxLengths.username) {
                    return maxLengthMessages.username;
                }

                return usernamePattern.test(trimmedValue)
                    ? ''
                    : 'Use only letters, numbers, underscores, dots, or hyphens.';
            case 'email':
                if (!trimmedValue) {
                    return 'Email is required.';
                }

                if (lengthOf(trimmedValue) > maxLengths.email) {
                    return maxLengthMessages.email;
                }

                return emailPattern.test(trimmedValue)
                    ? ''
                    : 'Please enter a valid email address.';
            case 'password':
                if (!value) {
                    return 'Password is required.';
                }

                if (lengthOf(value) < 8) {
                    return 'Password must be at least 8 characters.';
                }

                return lengthOf(value) > maxLengths.password
                    ? maxLengthMessages.password
                    : '';
            case 'confirm_password':
                if (!value) {
                    return 'Please confirm your password.';
                }

                if (lengthOf(value) > maxLengths.confirm_password) {
                    return maxLengthMessages.confirm_password;
                }

                return value === passwordField.value
                    ? ''
                    : 'Passwords do not match.';
            default:
                return '';
        }
    };

    const clearSummaryWhenResolved = () => {
        const hasFieldError = fieldNames.some(
            (name) => form.elements[name]?.getAttribute('aria-invalid') === 'true',
        );

        if (!hasFieldError) {
            formUi.clearErrorSummary();
        }
    };

    const revalidateInvalidField = (name) => {
        const field = form.elements[name];

        if (
            !field ||
            field.getAttribute('aria-invalid') !== 'true' ||
            field.dataset.limitError === 'true'
        ) {
            return;
        }

        formUi.setError(name, getFieldError(name));
        clearSummaryWhenResolved();
    };

    const updateRule = (rule, isValid) => {
        if (!rule) {
            return;
        }

        rule.classList.toggle('text-ui-success', isValid);
        rule.querySelector('[data-check]')?.classList.toggle('hidden', !isValid);
        rule.querySelector('[data-uncheck]')?.classList.toggle('hidden', isValid);
    };

    const updatePasswordHints = () => {
        const passwordLength = lengthOf(passwordField.value);
        const hasPassword = passwordLength > 0;
        const hasValidLength = passwordLength >= 8 && passwordLength <= 128;
        const filledSegments = hasPassword
            ? Math.min(4, Math.ceil((Math.min(passwordLength, 8) / 8) * 4))
            : 0;

        updateRule(requiredRule, hasPassword);
        updateRule(lengthRule, hasValidLength);

        passwordMeterSegments.forEach((segment, index) => {
            const isFilled = index < filledSegments;

            segment.classList.toggle('bg-ui-border', !isFilled);
            segment.classList.toggle(
                'bg-brand-royal',
                isFilled && !hasValidLength,
            );
            segment.classList.toggle('bg-ui-success', isFilled && hasValidLength);
        });

        if (!passwordStatus) {
            return;
        }

        passwordStatus.classList.toggle('text-ui-success', hasValidLength);

        if (!hasPassword) {
            passwordStatus.textContent = 'Empty';
        } else if (passwordLength < 8) {
            const remaining = 8 - passwordLength;
            passwordStatus.textContent = `${remaining} more character${remaining === 1 ? '' : 's'} needed`;
        } else if (passwordLength > 128) {
            passwordStatus.textContent = 'Too long';
        } else {
            passwordStatus.textContent = 'Meets requirements';
        }
    };

    const updateConfirmationStatus = () => {
        if (!confirmationStatus) {
            return;
        }

        const confirmation = confirmationField.value;
        const passwordsMatch =
            passwordField.value !== '' && confirmation === passwordField.value;
        const isVisible = confirmation !== '';

        confirmationStatus.classList.toggle('hidden', !isVisible);
        confirmationStatus.classList.toggle('flex', isVisible);
        confirmationStatus.classList.toggle('text-ui-success', passwordsMatch);
        confirmationStatus.classList.toggle(
            'text-ui-danger',
            isVisible && !passwordsMatch,
        );
        confirmationStatus
            .querySelector('[data-check]')
            ?.classList.toggle('hidden', !passwordsMatch);
        confirmationStatus
            .querySelector('[data-uncheck]')
            ?.classList.toggle('hidden', passwordsMatch);

        const message = confirmationStatus.querySelector('[data-status-message]');

        if (message) {
            message.textContent = passwordsMatch
                ? 'Passwords match.'
                : 'Passwords do not match.';
        }
    };

    const enforceMaxLength = (event) => {
        const field = event.currentTarget;
        const maxLength = maxLengths[field.name];

        if (!maxLength || !event.inputType?.startsWith('insert')) {
            return;
        }

        const incomingText = event.data || '';
        const selectionStart = field.selectionStart ?? field.value.length;
        const selectionEnd = field.selectionEnd ?? field.value.length;
        const selectedText = field.value.slice(selectionStart, selectionEnd);
        const remainingLength =
            maxLength - (lengthOf(field.value) - lengthOf(selectedText));
        const nextValue =
            field.value.slice(0, selectionStart) +
            incomingText +
            field.value.slice(selectionEnd);

        if (lengthOf(nextValue) <= maxLength) {
            if (field.dataset.limitError === 'true') {
                formUi.setError(field.name, '');
                field.dataset.limitError = 'false';
            }

            return;
        }

        event.preventDefault();
        field.dataset.limitError = 'true';
        formUi.setError(field.name, maxLengthMessages[field.name]);

        if (!incomingText || remainingLength <= 0) {
            return;
        }

        const allowedText = limitTo(incomingText, remainingLength);
        const nextSelectionStart = selectionStart + allowedText.length;
        field.value =
            field.value.slice(0, selectionStart) +
            allowedText +
            field.value.slice(selectionEnd);
        field.setSelectionRange(nextSelectionStart, nextSelectionStart);
    };

    const clearLimitError = (event) => {
        const field = event.currentTarget;

        if (
            field.dataset.limitError === 'true' &&
            lengthOf(field.value) < maxLengths[field.name]
        ) {
            field.dataset.limitError = 'false';
            formUi.setError(field.name, getFieldError(field.name));
            clearSummaryWhenResolved();
        }
    };

    fieldNames.forEach((name) => {
        const field = form.elements[name];

        if (!field || !maxLengths[name]) {
            return;
        }

        field.addEventListener('beforeinput', enforceMaxLength);
        field.addEventListener('input', clearLimitError);
        field.addEventListener('input', () => revalidateInvalidField(name));
    });

    passwordField.addEventListener('input', () => {
        updatePasswordHints();
        updateConfirmationStatus();
        revalidateInvalidField('confirm_password');
    });
    passwordField.addEventListener('focus', () => {
        passwordHints?.classList.remove('hidden');
    });
    confirmationField.addEventListener('input', updateConfirmationStatus);
    updatePasswordHints();
    updateConfirmationStatus();

    form.addEventListener('submit', (event) => {
        if (form.dataset.submitting === 'true') {
            event.preventDefault();
            return;
        }

        let hasError = false;
        let firstInvalidField = null;
        const markInvalid = (name, message) => {
            formUi.setError(name, message);
            hasError = true;
            firstInvalidField = firstInvalidField || form.elements[name];
        };

        formUi.clearFieldErrors(fieldNames);
        formUi.clearErrorSummary();

        fieldNames.forEach((name) => {
            const message = getFieldError(name);

            if (message) {
                markInvalid(name, message);
            }
        });

        if (hasError) {
            event.preventDefault();
            formUi.showErrorSummary(
                'Please fill the highlighted fields to create your account.',
            );

            if (firstInvalidField) {
                firstInvalidField.focus();
            }

            return;
        }

        formUi.setSubmitButtonLoading();
    });

    window.addEventListener('pageshow', formUi.resetSubmitButton);
})();
