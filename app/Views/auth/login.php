<?php
$errors = $errors ?? [];
$old = $old ?? [];
$success = $success ?? null;
$hasFieldErrors = $hasFieldErrors ?? false;
?>

<section class="min-h-screen bg-[#F7F8FB] px-4 py-8 [font-family:Inter,ui-sans-serif,system-ui,sans-serif] text-[#111827] sm:px-6 lg:px-10 lg:py-10">
    <div class="mx-auto flex w-full max-w-[520px] justify-center lg:min-h-[720px]">
        <section class="flex items-center">
            <div class="w-full rounded-[20px] bg-white p-5 ring-1 ring-[#E5E7EB] sm:p-8 lg:p-10">
                <div class="mb-7">
                    <p class="text-sm font-semibold text-[#1E3A8A]">Sign in</p>
                    <h2 class="mt-2 text-3xl font-semibold leading-tight tracking-[-0.01em] text-[#0F172A]">
                        Welcome back
                    </h2>
                    <p class="mt-3 text-sm leading-6 text-[#4B5563]">
                        Use your username to continue with coursework discussions.
                    </p>
                </div>

                <?php if (!empty($success)): ?>
                    <div class="mb-5 rounded-2xl bg-[#F0FDF4] p-4 text-sm leading-6 text-[#166534] ring-1 ring-[#BBF7D0]" role="status">
                        <?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($errors['general'])): ?>
                    <div class="mb-5 rounded-2xl bg-[#FEF2F2] p-4 text-sm leading-6 text-[#991B1B] ring-1 ring-[#FECACA]" role="alert">
                        <?= htmlspecialchars($errors['general'], ENT_QUOTES, 'UTF-8') ?>
                    </div>
                <?php endif; ?>

                <p id="login-error-summary"
                   class="<?= $hasFieldErrors ? 'mb-5 rounded-2xl bg-[#FEF2F2] p-4 text-sm leading-6 text-[#991B1B] ring-1 ring-[#FECACA]' : 'sr-only' ?>"
                   <?= $hasFieldErrors ? 'role="alert" tabindex="-1"' : '' ?>>
                    <?= $hasFieldErrors ? 'Please fix the highlighted fields and try again.' : '' ?>
                </p>

                <form id="login-form" action="<?= BASE_URL ?>/login/authenticate" method="post" class="grid gap-5" novalidate>
                    <div class="min-w-0">
                        <label for="username" class="mb-2 block text-sm font-semibold text-[#111827]">Username</label>
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
                               class="h-12 w-full min-w-0 rounded-2xl border-0 bg-[#F7F8FB] px-4 text-base text-[#111827] outline-none ring-1 <?= !empty($errors['username']) ? 'ring-[#DC2626] focus:ring-[#DC2626]/40' : 'ring-[#E5E7EB] focus:ring-[#2563EB]/30' ?> transition duration-200 placeholder:text-[#4B5563] focus:ring-2">
                        <p id="username-error"
                           class="mt-2 <?= empty($errors['username']) ? 'hidden' : 'flex' ?> min-w-0 items-start gap-1.5 break-words text-sm leading-5 text-[#B91C1C]"
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
                        <label for="password" class="mb-2 block text-sm font-semibold text-[#111827]">Password</label>
                        <input id="password"
                               name="password"
                               type="password"
                               autocomplete="current-password"
                               maxlength="128"
                               dir="ltr"
                               aria-describedby="password-error"
                               aria-invalid="<?= !empty($errors['password']) ? 'true' : 'false' ?>"
                               required
                               class="h-12 w-full min-w-0 rounded-2xl border-0 bg-[#F7F8FB] px-4 text-base text-[#111827] outline-none ring-1 <?= !empty($errors['password']) ? 'ring-[#DC2626] focus:ring-[#DC2626]/40' : 'ring-[#E5E7EB] focus:ring-[#2563EB]/30' ?> transition duration-200 placeholder:text-[#4B5563] focus:ring-2">
                        <p id="password-error"
                           class="mt-2 <?= empty($errors['password']) ? 'hidden' : 'flex' ?> min-w-0 items-start gap-1.5 break-words text-sm leading-5 text-[#B91C1C]"
                           data-error-for="password" aria-live="polite">
                            <svg viewBox="0 0 16 16" class="mt-0.5 size-3.5 shrink-0" fill="none" aria-hidden="true" data-error-icon>
                                <circle cx="8" cy="8" r="6.5" stroke="currentColor" stroke-width="1.5" />
                                <path d="M8 4.8v3.6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                                <path d="M8 11.1h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                            </svg>
                            <span data-error-message><?= htmlspecialchars($errors['password'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
                        </p>
                    </div>

                    <button type="submit"
                            data-submit-label="Sign in"
                            data-loading-label="Signing in..."
                            class="mt-1 inline-flex min-h-12 min-w-0 items-center justify-center rounded-2xl bg-[#1E3A8A] px-6 py-3 text-center text-sm font-semibold text-white transition duration-200 hover:bg-[#172E70] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#1E3A8A] disabled:cursor-not-allowed disabled:bg-[#9CA3AF]">
                        Sign in
                    </button>

                    <p class="text-sm leading-6 text-[#4B5563]">
                        Don't have an account?
                        <a href="<?= BASE_URL ?>/register" class="font-semibold text-[#1E3A8A] underline-offset-4 hover:underline">
                            Create account &rarr;
                        </a>
                    </p>
                </form>
            </div>
        </section>
    </div>
</section>
