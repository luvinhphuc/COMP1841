<?php
$errors = $errors ?? [];
$old = $old ?? [];
$hasFieldErrors = $hasFieldErrors ?? false;
?>

<section class="bg-[#f7f9fd] px-5 py-10 text-[#191c1f] sm:px-8 lg:py-14">
    <div
        class="mx-auto grid w-full max-w-[1120px] gap-8 lg:grid-cols-[minmax(0,0.9fr)_minmax(420px,1fr)] lg:items-start">
        <div class="hidden max-w-md lg:block" data-home-reveal>
            <p class="text-sm font-bold uppercase tracking-[0.16em] text-[#444748]">
                Greenwich Discussions
            </p>
            <h1 class="mt-4 text-4xl font-semibold leading-tight text-black">
                Create your coursework account
            </h1>
            <p class="mt-4 max-w-md text-base leading-7 text-[#444748]">
                Join module discussions with your University of Greenwich email, ask questions, and follow coursework
                conversations with your classmates.
            </p>
        </div>

        <div class="rounded-xl border border-[#c4c7c7] bg-white p-5 shadow-[0_18px_38px_rgba(25,28,31,0.06)] sm:p-8"
            data-home-reveal>
            <div class="mb-6">
                <p class="text-sm font-bold uppercase tracking-[0.12em] text-[#444748]">
                    Register
                </p>
                <h2 class="mt-2 text-3xl font-semibold leading-10 text-black">
                    Start your account
                </h2>
            </div>

            <?php if (!empty($errors['general'])): ?>
                <div class="mb-4 rounded-lg border border-[#ba1a1a] bg-[#fff8f7] px-4 py-3 text-sm leading-6 text-[#ba1a1a]"
                    role="alert">
                    <?= htmlspecialchars((string) $errors['general'], ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php endif; ?>

            <p id="register-error-summary"
                class="<?= $hasFieldErrors ? 'mb-5 rounded-lg border border-[#ba1a1a]/30 bg-[#ba1a1a]/5 p-4 text-sm leading-6 text-[#8f1111]' : 'sr-only' ?>"
                <?= $hasFieldErrors ? 'role="alert" tabindex="-1"' : '' ?>>
                <?= $hasFieldErrors ? 'Please fix the highlighted fields and try again.' : '' ?>
            </p>

            <form id="register-form" action="<?= BASE_URL ?>/register/store" method="post" enctype="multipart/form-data"
                class="grid gap-4" novalidate>
                <div class="grid min-w-0 gap-4 sm:grid-cols-2">
                    <div class="min-w-0">
                        <label for="first_name" class="mb-2 block text-sm font-semibold leading-5 text-[#191c1f]">
                            First name
                        </label>
                        <input id="first_name"
                            name="first_name"
                            type="text"
                            value="<?= htmlspecialchars($old['first_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                            autocomplete="given-name"
                            maxlength="35"
                            dir="auto"
                            aria-describedby="first_name-error"
                            aria-invalid="<?= !empty($errors['first_name']) ? 'true' : 'false' ?>"
                            required
                            class="h-12 w-full min-w-0 rounded-lg border <?= !empty($errors['first_name']) ? 'border-[#ba1a1a] focus:border-[#ba1a1a] focus:ring-[#ba1a1a]/10' : 'border-[#c4c7c7] focus:border-black focus:ring-black/10' ?> bg-white px-4 text-base text-[#191c1f] outline-none transition focus:ring-2">
                        <p id="first_name-error"
                            class="mt-2 <?= empty($errors['first_name']) ? 'hidden' : 'flex' ?> min-w-0 items-start gap-1.5 break-words text-sm leading-5 text-[#ba1a1a]"
                            data-error-for="first_name" aria-live="polite">
                            <svg viewBox="0 0 16 16" class="mt-0.5 size-3.5 shrink-0" fill="none" aria-hidden="true" data-error-icon>
                                <circle cx="8" cy="8" r="6.5" stroke="currentColor" stroke-width="1.5" />
                                <path d="M8 4.8v3.6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                                <path d="M8 11.1h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                            </svg>
                            <span data-error-message><?= htmlspecialchars($errors['first_name'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
                        </p>
                    </div>

                    <div class="min-w-0">
                        <label for="last_name" class="mb-2 block text-sm font-semibold leading-5 text-[#191c1f]">
                            Last name
                        </label>
                        <input id="last_name"
                            name="last_name"
                            type="text"
                            value="<?= htmlspecialchars($old['last_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                            autocomplete="family-name"
                            maxlength="35"
                            dir="auto"
                            aria-describedby="last_name-error"
                            aria-invalid="<?= !empty($errors['last_name']) ? 'true' : 'false' ?>"
                            required
                            class="h-12 w-full min-w-0 rounded-lg border <?= !empty($errors['last_name']) ? 'border-[#ba1a1a] focus:border-[#ba1a1a] focus:ring-[#ba1a1a]/10' : 'border-[#c4c7c7] focus:border-black focus:ring-black/10' ?> bg-white px-4 text-base text-[#191c1f] outline-none transition focus:ring-2">
                        <p id="last_name-error"
                            class="mt-2 <?= empty($errors['last_name']) ? 'hidden' : 'flex' ?> min-w-0 items-start gap-1.5 break-words text-sm leading-5 text-[#ba1a1a]"
                            data-error-for="last_name" aria-live="polite">
                            <svg viewBox="0 0 16 16" class="mt-0.5 size-3.5 shrink-0" fill="none" aria-hidden="true" data-error-icon>
                                <circle cx="8" cy="8" r="6.5" stroke="currentColor" stroke-width="1.5" />
                                <path d="M8 4.8v3.6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                                <path d="M8 11.1h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                            </svg>
                            <span data-error-message><?= htmlspecialchars($errors['last_name'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
                        </p>
                    </div>
                </div>

                <div class="min-w-0">
                    <label for="username" class="mb-2 block text-sm font-semibold leading-5 text-[#191c1f]">
                        Username
                    </label>
                    <input id="username"
                        name="username"
                        type="text"
                        value="<?= htmlspecialchars($old['username'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                        autocomplete="username"
                        autocapitalize="none"
                        spellcheck="false"
                        inputmode="text"
                        maxlength="75"
                        dir="ltr"
                        aria-describedby="username-error"
                        aria-invalid="<?= !empty($errors['username']) ? 'true' : 'false' ?>"
                        required
                        class="h-12 w-full min-w-0 rounded-lg border <?= !empty($errors['username']) ? 'border-[#ba1a1a] focus:border-[#ba1a1a] focus:ring-[#ba1a1a]/10' : 'border-[#c4c7c7] focus:border-black focus:ring-black/10' ?> bg-white px-4 text-base text-[#191c1f] outline-none transition focus:ring-2">
                    <p id="username-error"
                        class="mt-2 <?= empty($errors['username']) ? 'hidden' : 'flex' ?> min-w-0 items-start gap-1.5 break-words text-sm leading-5 text-[#ba1a1a]"
                        data-error-for="username" aria-live="polite">
                        <svg viewBox="0 0 16 16" class="mt-0.5 size-3.5 shrink-0" fill="none" aria-hidden="true" data-error-icon>
                            <circle cx="8" cy="8" r="6.5" stroke="currentColor" stroke-width="1.5" />
                            <path d="M8 4.8v3.6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                            <path d="M8 11.1h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                        </svg>
                        <span data-error-message><?= htmlspecialchars($errors['username'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
                    </p>
                </div>

                <div class="min-w-0">
                    <label for="email" class="mb-2 block text-sm font-semibold leading-5 text-[#191c1f]">
                        Email
                    </label>
                    <input id="email"
                        name="email"
                        type="email"
                        value="<?= htmlspecialchars($old['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                        autocomplete="email"
                        autocapitalize="none"
                        spellcheck="false"
                        inputmode="email"
                        maxlength="150"
                        dir="ltr"
                        aria-describedby="email-error"
                        aria-invalid="<?= !empty($errors['email']) ? 'true' : 'false' ?>"
                        required
                        class="h-12 w-full min-w-0 rounded-lg border <?= !empty($errors['email']) ? 'border-[#ba1a1a] focus:border-[#ba1a1a] focus:ring-[#ba1a1a]/10' : 'border-[#c4c7c7] focus:border-black focus:ring-black/10' ?> bg-white px-4 text-base text-[#191c1f] outline-none transition focus:ring-2">
                    <p id="email-error"
                        class="mt-2 <?= empty($errors['email']) ? 'hidden' : 'flex' ?> min-w-0 items-start gap-1.5 break-words text-sm leading-5 text-[#ba1a1a]"
                        data-error-for="email" aria-live="polite">
                        <svg viewBox="0 0 16 16" class="mt-0.5 size-3.5 shrink-0" fill="none" aria-hidden="true" data-error-icon>
                            <circle cx="8" cy="8" r="6.5" stroke="currentColor" stroke-width="1.5" />
                            <path d="M8 4.8v3.6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                            <path d="M8 11.1h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                        </svg>
                        <span data-error-message><?= htmlspecialchars($errors['email'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
                    </p>
                </div>

                <div class="grid min-w-0 gap-4 sm:grid-cols-2">
                    <div class="min-w-0">
                        <label for="password" class="mb-2 block text-sm font-semibold leading-5 text-[#191c1f]">
                            Password
                        </label>
                        <input id="password"
                            name="password"
                            type="password"
                            autocomplete="new-password"
                            maxlength="128"
                            dir="ltr"
                            aria-describedby="password-error"
                            aria-invalid="<?= !empty($errors['password']) ? 'true' : 'false' ?>"
                            required
                            class="h-12 w-full min-w-0 rounded-lg border <?= !empty($errors['password']) ? 'border-[#ba1a1a] focus:border-[#ba1a1a] focus:ring-[#ba1a1a]/10' : 'border-[#c4c7c7] focus:border-black focus:ring-black/10' ?> bg-white px-4 text-base text-[#191c1f] outline-none transition focus:ring-2">
                        <p id="password-error"
                            class="mt-2 <?= empty($errors['password']) ? 'hidden' : 'flex' ?> min-w-0 items-start gap-1.5 break-words text-sm leading-5 text-[#ba1a1a]"
                            data-error-for="password" aria-live="polite">
                            <svg viewBox="0 0 16 16" class="mt-0.5 size-3.5 shrink-0" fill="none" aria-hidden="true" data-error-icon>
                                <circle cx="8" cy="8" r="6.5" stroke="currentColor" stroke-width="1.5" />
                                <path d="M8 4.8v3.6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                                <path d="M8 11.1h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                            </svg>
                            <span data-error-message><?= htmlspecialchars($errors['password'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
                        </p>
                    </div>

                    <div class="min-w-0">
                        <label for="confirm_password" class="mb-2 block text-sm font-semibold leading-5 text-[#191c1f]">
                            Confirm password
                        </label>
                        <input id="confirm_password"
                            name="confirm_password"
                            type="password"
                            autocomplete="new-password"
                            maxlength="128"
                            dir="ltr"
                            aria-describedby="confirm_password-error"
                            aria-invalid="<?= !empty($errors['confirm_password']) ? 'true' : 'false' ?>"
                            required
                            class="h-12 w-full min-w-0 rounded-lg border <?= !empty($errors['confirm_password']) ? 'border-[#ba1a1a] focus:border-[#ba1a1a] focus:ring-[#ba1a1a]/10' : 'border-[#c4c7c7] focus:border-black focus:ring-black/10' ?> bg-white px-4 text-base text-[#191c1f] outline-none transition focus:ring-2">
                        <p id="confirm_password-error"
                            class="mt-2 <?= empty($errors['confirm_password']) ? 'hidden' : 'flex' ?> min-w-0 items-start gap-1.5 break-words text-sm leading-5 text-[#ba1a1a]"
                            data-error-for="confirm_password" aria-live="polite">
                            <svg viewBox="0 0 16 16" class="mt-0.5 size-3.5 shrink-0" fill="none" aria-hidden="true" data-error-icon>
                                <circle cx="8" cy="8" r="6.5" stroke="currentColor" stroke-width="1.5" />
                                <path d="M8 4.8v3.6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                                <path d="M8 11.1h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                            </svg>
                            <span data-error-message><?= htmlspecialchars($errors['confirm_password'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
                        </p>
                    </div>
                </div>

                <button type="submit" data-submit-label="Create Account" data-loading-label="Creating account..."
                    class="mt-2 inline-flex min-h-12 min-w-0 items-center justify-center rounded-lg bg-black px-6 py-3 text-center text-sm font-semibold tracking-[0.04em] text-white transition hover:bg-[#2a2d2f] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-black disabled:cursor-not-allowed disabled:bg-[#444748]">
                    Create Account
                </button>

                <p class="text-sm leading-6 text-[#444748]">
                    Already registered?
                    <a href="<?= BASE_URL ?>/login" class="font-semibold text-black underline-offset-4 hover:underline">
                        Sign in here.
                    </a>
                </p>
            </form>
        </div>
    </div>
</section>
