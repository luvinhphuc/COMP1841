(() => {
    const passwordFields = document.querySelectorAll('input[type="password"]');

    const eyeIcon = `
        <svg viewBox="0 0 24 24" class="size-5" fill="none" aria-hidden="true">
            <path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
            <circle cx="12" cy="12" r="2.75" stroke="currentColor" stroke-width="1.8"/>
        </svg>
    `;
    const eyeOffIcon = `
        <svg viewBox="0 0 24 24" class="size-5" fill="none" aria-hidden="true">
            <path d="m3 3 18 18M10.6 6.1A9.4 9.4 0 0 1 12 6c6 0 9.5 6 9.5 6a16 16 0 0 1-2.1 2.8M6.3 6.3A16.6 16.6 0 0 0 2.5 12s3.5 6 9.5 6a9.4 9.4 0 0 0 3.2-.5M9.9 9.9a3 3 0 0 0 4.2 4.2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    `;

    passwordFields.forEach((field) => {
        const wrapper = document.createElement('div');
        const toggle = document.createElement('button');
        const hasTopMargin = field.classList.contains('mt-2');

        wrapper.className = `relative${hasTopMargin ? ' mt-2' : ''}`;
        toggle.type = 'button';
        toggle.className =
            'absolute right-0 top-0 flex h-full w-8 items-center justify-center rounded-lg text-[#4B5563] transition hover:text-[#111827] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#1E3A8A]';
        toggle.setAttribute('aria-label', 'Show password');
        toggle.setAttribute('aria-pressed', 'false');
        toggle.innerHTML = eyeIcon;

        if (hasTopMargin) {
            field.classList.remove('mt-2');
        }

        field.classList.add('pr-9');
        field.before(wrapper);
        wrapper.append(field, toggle);

        toggle.addEventListener('click', () => {
            const isPasswordVisible = field.type === 'text';

            field.type = isPasswordVisible ? 'password' : 'text';
            toggle.setAttribute(
                'aria-label',
                isPasswordVisible ? 'Show password' : 'Hide password',
            );
            toggle.setAttribute('aria-pressed', String(!isPasswordVisible));
            toggle.innerHTML = isPasswordVisible ? eyeIcon : eyeOffIcon;
        });
    });
})();
