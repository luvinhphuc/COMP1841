(() => {
    const form = document.querySelector('#login-form');

    if (!form) {
        return;
    }

    const fieldNames = ['username', 'password'];
    const formUi = window.FormUtils.createFormUi(form, {
        errorSummarySelector: '#login-error-summary',
        submitLabel: 'Sign in',
        loadingLabel: 'Signing in...',
    });
    const maxLengths = {
        username: 75,
        password: 128,
    };

    form.addEventListener('submit', (event) => {
        if (form.dataset.submitting === 'true') {
            event.preventDefault();
            return;
        }

        let hasError = false;
        let firstInvalidField = null;
        const username = form.username.value.trim();
        const password = form.password.value;

        const markInvalid = (name, message) => {
            formUi.setError(name, message);
            hasError = true;
            firstInvalidField = firstInvalidField || form.elements[name];
        };

        formUi.clearFieldErrors(fieldNames);
        formUi.clearErrorSummary();

        if (!username) {
            markInvalid('username', 'Username is required.');
        } else if (username.length > maxLengths.username) {
            markInvalid('username', 'Username must be 75 characters or fewer.');
        }

        if (!password) {
            markInvalid('password', 'Password is required.');
        } else if (password.length > maxLengths.password) {
            markInvalid('password', 'Password must be 128 characters or fewer.');
        }

        if (hasError) {
            event.preventDefault();
            formUi.showErrorSummary('Please fill the highlighted fields to sign in.');

            if (firstInvalidField) {
                firstInvalidField.focus();
            }

            return;
        }

        formUi.setSubmitButtonLoading();
    });

    window.addEventListener('pageshow', formUi.resetSubmitButton);
})();
