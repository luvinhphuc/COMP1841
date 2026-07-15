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

    const lengthOf = (value) => Array.from(value).length;
    const limitTo = (value, maxLength) =>
        Array.from(value).slice(0, maxLength).join('');

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
            formUi.setError(field.name, '');
            field.dataset.limitError = 'false';
        }
    };

    fieldNames.forEach((name) => {
        const field = form.elements[name];

        if (!field || !maxLengths[name]) {
            return;
        }

        field.addEventListener('beforeinput', enforceMaxLength);
        field.addEventListener('input', clearLimitError);
    });

    form.addEventListener('submit', (event) => {
        if (form.dataset.submitting === 'true') {
            event.preventDefault();
            return;
        }

        let hasError = false;
        let firstInvalidField = null;
        const firstName = form.first_name.value.trim();
        const lastName = form.last_name.value.trim();
        const username = form.username.value.trim();
        const email = form.email.value.trim();
        const password = form.password.value;
        const confirmPassword = form.confirm_password.value;
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        const usernamePattern = /^[A-Za-z0-9_.-]+$/;

        const markInvalid = (name, message) => {
            formUi.setError(name, message);
            hasError = true;
            firstInvalidField = firstInvalidField || form.elements[name];
        };

        formUi.clearFieldErrors(fieldNames);
        formUi.clearErrorSummary();

        if (!firstName) {
            markInvalid('first_name', 'First name is required.');
        } else if (firstName.length > maxLengths.first_name) {
            markInvalid('first_name', 'First name must be 50 characters or fewer.');
        }

        if (!lastName) {
            markInvalid('last_name', 'Last name is required.');
        } else if (lastName.length > maxLengths.last_name) {
            markInvalid('last_name', 'Last name must be 50 characters or fewer.');
        }

        if (!username) {
            markInvalid('username', 'Username is required.');
        } else if (username.length > maxLengths.username) {
            markInvalid('username', 'Username must be 75 characters or fewer.');
        } else if (!usernamePattern.test(username)) {
            markInvalid(
                'username',
                'Use only letters, numbers, underscores, dots, or hyphens.',
            );
        }

        if (!email) {
            markInvalid('email', 'Email is required.');
        } else if (email.length > maxLengths.email) {
            markInvalid('email', 'Email must be 150 characters or fewer.');
        } else if (!emailPattern.test(email)) {
            markInvalid('email', 'Please enter a valid email address.');
        }

        if (!password) {
            markInvalid('password', 'Password is required.');
        } else if (password.length < 8) {
            markInvalid('password', 'Password must be at least 8 characters.');
        } else if (password.length > maxLengths.password) {
            markInvalid('password', 'Password must be 128 characters or fewer.');
        }

        if (!confirmPassword) {
            markInvalid('confirm_password', 'Please confirm your password.');
        } else if (confirmPassword.length > maxLengths.confirm_password) {
            markInvalid(
                'confirm_password',
                'Confirm password must be 128 characters or fewer.',
            );
        } else if (password !== confirmPassword) {
            markInvalid('confirm_password', 'Passwords do not match.');
        }

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
