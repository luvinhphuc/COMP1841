(() => {
    const form = document.querySelector('#register-form');

    if (!form) {
        return;
    }

    const setError = (name, message) => {
        const error = form.querySelector(`[data-error-for="${name}"]`);

        if (error) {
            error.textContent = message;
        }
    };

    form.addEventListener('submit', (event) => {
        let hasError = false;
        const fullName = form.full_name.value.trim();
        const username = form.username.value.trim();
        const email = form.email.value.trim();
        const password = form.password.value;
        const confirmPassword = form.confirm_password.value;
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        ['full_name', 'username', 'email', 'password', 'confirm_password'].forEach((name) => setError(name, ''));

        if (!fullName) {
            setError('full_name', 'Full name is required.');
            hasError = true;
        }

        if (!username) {
            setError('username', 'Username is required.');
            hasError = true;
        }

        if (!email) {
            setError('email', 'Email is required.');
            hasError = true;
        } else if (!emailPattern.test(email)) {
            setError('email', 'Please enter a valid email address.');
            hasError = true;
        } else if (!email.toLowerCase().endsWith('@gre.ac.uk')) {
            setError('email', 'Please use your @gre.ac.uk email address.');
            hasError = true;
        }

        if (!password) {
            setError('password', 'Password is required.');
            hasError = true;
        }

        if (!confirmPassword) {
            setError('confirm_password', 'Please confirm your password.');
            hasError = true;
        } else if (password !== confirmPassword) {
            setError('confirm_password', 'Passwords do not match.');
            hasError = true;
        }

        if (hasError) {
            event.preventDefault();
        }
    });
})();
