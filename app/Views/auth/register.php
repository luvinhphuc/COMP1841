<?php
$errors = $errors ?? [];
$old = $old ?? [];
$hasFieldErrors = $hasFieldErrors ?? false;
?>

<section class="min-h-screen bg-[#F7F8FB] px-4 py-8 [font-family:Inter,ui-sans-serif,system-ui,sans-serif] text-[#111827] sm:px-6 lg:px-10 lg:py-10">
    <div class="mx-auto grid w-full max-w-[1240px] gap-8 lg:min-h-[760px] lg:grid-cols-[minmax(0,0.9fr)_minmax(460px,0.95fr)] lg:items-stretch">
        <section class="relative hidden overflow-hidden rounded-[20px] bg-[#0F172A] p-8 text-white lg:flex lg:flex-col lg:justify-between" aria-label="Greenwich community benefits">
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_70%_20%,rgba(37,99,235,0.35),transparent_30%),linear-gradient(135deg,rgba(15,23,42,1),rgba(30,58,138,0.96))]" aria-hidden="true"></div>
            <div class="relative">
                <p class="text-sm font-semibold text-[#BFDBFE]">Join the academic community</p>
                <h1 class="mt-5 max-w-xl text-4xl font-semibold leading-tight tracking-[-0.01em]">
                    Build your coursework profile from the first question.
                </h1>
                <p class="mt-4 max-w-lg text-base leading-7 text-[#DBEAFE]">
                    Create an account with your email to join module conversations and share knowledge with classmates.
                </p>
            </div>

            <div class="relative my-10 rounded-[20px] bg-white/10 p-5 ring-1 ring-white/15">
                <div class="grid grid-cols-3 gap-3">
                    <div class="rounded-2xl bg-white p-4 text-[#0F172A]">
                        <div class="mx-auto flex size-12 items-center justify-center rounded-full bg-[#DBEAFE] text-sm font-semibold text-[#1E3A8A]">AL</div>
                        <div class="mx-auto mt-4 h-2 w-16 rounded-full bg-[#D1D5DB]"></div>
                    </div>
                    <div class="rounded-2xl bg-[#DBEAFE] p-4 text-[#1E3A8A]">
                        <div class="mx-auto flex size-12 items-center justify-center rounded-full bg-white text-sm font-semibold">MP</div>
                        <div class="mx-auto mt-4 h-2 w-16 rounded-full bg-[#93C5FD]"></div>
                    </div>
                    <div class="rounded-2xl bg-white p-4 text-[#0F172A]">
                        <div class="mx-auto flex size-12 items-center justify-center rounded-full bg-[#EEF2FF] text-sm font-semibold text-[#1E3A8A]">VN</div>
                        <div class="mx-auto mt-4 h-2 w-16 rounded-full bg-[#D1D5DB]"></div>
                    </div>
                </div>
            </div>

            <ul class="relative grid gap-3 text-sm font-medium text-[#EFF6FF]">
                <li class="flex items-center gap-3">
                    <span class="flex size-6 items-center justify-center rounded-full bg-white text-[#1E3A8A]">&#10003;</span>
                    Join module discussions
                </li>
                <li class="flex items-center gap-3">
                    <span class="flex size-6 items-center justify-center rounded-full bg-white text-[#1E3A8A]">&#10003;</span>
                    Share knowledge
                </li>
                <li class="flex items-center gap-3">
                    <span class="flex size-6 items-center justify-center rounded-full bg-white text-[#1E3A8A]">&#10003;</span>
                    Build your academic profile
                </li>
            </ul>
        </section>

        <section class="flex items-center">
            <div class="w-full rounded-[20px] bg-white p-5 ring-1 ring-[#E5E7EB] sm:p-8 lg:p-10">
                <div class="mb-7">
                    <p class="text-sm font-semibold text-[#1E3A8A]">Register</p>
                    <h2 class="mt-2 text-3xl font-semibold leading-tight tracking-[-0.01em] text-[#0F172A]">
                        Create account
                    </h2>
                    <p class="mt-3 text-sm leading-6 text-[#4B5563]">
                        Use a valid email so discussions stay connected to your account.
                    </p>
                </div>

                <?php if (!empty($errors['general'])): ?>
                    <div class="mb-5 rounded-2xl bg-[#FEF2F2] p-4 text-sm leading-6 text-[#991B1B] ring-1 ring-[#FECACA]" role="alert">
                        <?= htmlspecialchars((string) $errors['general'], ENT_QUOTES, 'UTF-8') ?>
                    </div>
                <?php endif; ?>

                <p id="register-error-summary"
                    class="<?= $hasFieldErrors ? 'mb-5 rounded-2xl bg-[#FEF2F2] p-4 text-sm leading-6 text-[#991B1B] ring-1 ring-[#FECACA]' : 'sr-only' ?>"
                    <?= $hasFieldErrors ? 'role="alert" tabindex="-1"' : '' ?>>
                    <?= $hasFieldErrors ? 'Please fix the highlighted fields and try again.' : '' ?>
                </p>

                <form id="register-form" action="<?= BASE_URL ?>/register/store" method="post" enctype="multipart/form-data" class="grid gap-4" novalidate>
                    <div class="grid min-w-0 gap-4 sm:grid-cols-2">
                        <div class="min-w-0">
                            <label for="first_name" class="mb-2 block text-sm font-semibold leading-5 text-[#111827]">First name</label>
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
                                class="h-12 w-full min-w-0 rounded-2xl border-0 bg-[#F7F8FB] px-4 text-base text-[#111827] outline-none ring-1 <?= !empty($errors['first_name']) ? 'ring-[#DC2626] focus:ring-[#DC2626]/40' : 'ring-[#E5E7EB] focus:ring-[#2563EB]/30' ?> transition duration-200 focus:ring-2">
                            <p id="first_name-error" class="mt-2 <?= empty($errors['first_name']) ? 'hidden' : 'flex' ?> min-w-0 items-start gap-1.5 break-words text-sm leading-5 text-[#B91C1C]" data-error-for="first_name" aria-live="polite">
                                <svg viewBox="0 0 16 16" class="mt-0.5 size-3.5 shrink-0" fill="none" aria-hidden="true" data-error-icon>
                                    <circle cx="8" cy="8" r="6.5" stroke="currentColor" stroke-width="1.5" />
                                    <path d="M8 4.8v3.6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                                    <path d="M8 11.1h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                                </svg>
                                <span data-error-message><?= htmlspecialchars($errors['first_name'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
                            </p>
                        </div>

                        <div class="min-w-0">
                            <label for="last_name" class="mb-2 block text-sm font-semibold leading-5 text-[#111827]">Last name</label>
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
                                class="h-12 w-full min-w-0 rounded-2xl border-0 bg-[#F7F8FB] px-4 text-base text-[#111827] outline-none ring-1 <?= !empty($errors['last_name']) ? 'ring-[#DC2626] focus:ring-[#DC2626]/40' : 'ring-[#E5E7EB] focus:ring-[#2563EB]/30' ?> transition duration-200 focus:ring-2">
                            <p id="last_name-error" class="mt-2 <?= empty($errors['last_name']) ? 'hidden' : 'flex' ?> min-w-0 items-start gap-1.5 break-words text-sm leading-5 text-[#B91C1C]" data-error-for="last_name" aria-live="polite">
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
                        <label for="username" class="mb-2 block text-sm font-semibold leading-5 text-[#111827]">Username</label>
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
                            class="h-12 w-full min-w-0 rounded-2xl border-0 bg-[#F7F8FB] px-4 text-base text-[#111827] outline-none ring-1 <?= !empty($errors['username']) ? 'ring-[#DC2626] focus:ring-[#DC2626]/40' : 'ring-[#E5E7EB] focus:ring-[#2563EB]/30' ?> transition duration-200 focus:ring-2">
                        <p id="username-error" class="mt-2 <?= empty($errors['username']) ? 'hidden' : 'flex' ?> min-w-0 items-start gap-1.5 break-words text-sm leading-5 text-[#B91C1C]" data-error-for="username" aria-live="polite">
                            <svg viewBox="0 0 16 16" class="mt-0.5 size-3.5 shrink-0" fill="none" aria-hidden="true" data-error-icon>
                                <circle cx="8" cy="8" r="6.5" stroke="currentColor" stroke-width="1.5" />
                                <path d="M8 4.8v3.6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                                <path d="M8 11.1h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                            </svg>
                            <span data-error-message><?= htmlspecialchars($errors['username'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
                        </p>
                    </div>

                    <div class="min-w-0">
                        <label for="email" class="mb-2 block text-sm font-semibold leading-5 text-[#111827]">Email</label>
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
                            class="h-12 w-full min-w-0 rounded-2xl border-0 bg-[#F7F8FB] px-4 text-base text-[#111827] outline-none ring-1 <?= !empty($errors['email']) ? 'ring-[#DC2626] focus:ring-[#DC2626]/40' : 'ring-[#E5E7EB] focus:ring-[#2563EB]/30' ?> transition duration-200 focus:ring-2">
                        <p id="email-error" class="mt-2 <?= empty($errors['email']) ? 'hidden' : 'flex' ?> min-w-0 items-start gap-1.5 break-words text-sm leading-5 text-[#B91C1C]" data-error-for="email" aria-live="polite">
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
                            <label for="password" class="mb-2 block text-sm font-semibold leading-5 text-[#111827]">Password</label>
                            <input id="password"
                                name="password"
                                type="password"
                                autocomplete="new-password"
                                maxlength="128"
                                dir="ltr"
                                aria-describedby="password-error"
                                aria-invalid="<?= !empty($errors['password']) ? 'true' : 'false' ?>"
                                required
                                class="h-12 w-full min-w-0 rounded-2xl border-0 bg-[#F7F8FB] px-4 text-base text-[#111827] outline-none ring-1 <?= !empty($errors['password']) ? 'ring-[#DC2626] focus:ring-[#DC2626]/40' : 'ring-[#E5E7EB] focus:ring-[#2563EB]/30' ?> transition duration-200 focus:ring-2">
                            <p id="password-error" class="mt-2 <?= empty($errors['password']) ? 'hidden' : 'flex' ?> min-w-0 items-start gap-1.5 break-words text-sm leading-5 text-[#B91C1C]" data-error-for="password" aria-live="polite">
                                <svg viewBox="0 0 16 16" class="mt-0.5 size-3.5 shrink-0" fill="none" aria-hidden="true" data-error-icon>
                                    <circle cx="8" cy="8" r="6.5" stroke="currentColor" stroke-width="1.5" />
                                    <path d="M8 4.8v3.6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                                    <path d="M8 11.1h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                                </svg>
                                <span data-error-message><?= htmlspecialchars($errors['password'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
                            </p>
                        </div>

                        <div class="min-w-0">
                            <label for="confirm_password" class="mb-2 block text-sm font-semibold leading-5 text-[#111827]">Confirm password</label>
                            <input id="confirm_password"
                                name="confirm_password"
                                type="password"
                                autocomplete="new-password"
                                maxlength="128"
                                dir="ltr"
                                aria-describedby="confirm_password-error"
                                aria-invalid="<?= !empty($errors['confirm_password']) ? 'true' : 'false' ?>"
                                required
                                class="h-12 w-full min-w-0 rounded-2xl border-0 bg-[#F7F8FB] px-4 text-base text-[#111827] outline-none ring-1 <?= !empty($errors['confirm_password']) ? 'ring-[#DC2626] focus:ring-[#DC2626]/40' : 'ring-[#E5E7EB] focus:ring-[#2563EB]/30' ?> transition duration-200 focus:ring-2">
                            <p id="confirm_password-error" class="mt-2 <?= empty($errors['confirm_password']) ? 'hidden' : 'flex' ?> min-w-0 items-start gap-1.5 break-words text-sm leading-5 text-[#B91C1C]" data-error-for="confirm_password" aria-live="polite">
                                <svg viewBox="0 0 16 16" class="mt-0.5 size-3.5 shrink-0" fill="none" aria-hidden="true" data-error-icon>
                                    <circle cx="8" cy="8" r="6.5" stroke="currentColor" stroke-width="1.5" />
                                    <path d="M8 4.8v3.6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                                    <path d="M8 11.1h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                                </svg>
                                <span data-error-message><?= htmlspecialchars($errors['confirm_password'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
                            </p>
                        </div>
                    </div>

                    <p class="text-xs leading-5 text-[#4B5563]">
                        By creating an account, you agree to use the platform for Greenwich coursework discussion and academic support.
                    </p>

                    <button type="submit" data-submit-label="Create account" data-loading-label="Creating account..."
                        class="mt-1 inline-flex min-h-12 min-w-0 items-center justify-center rounded-2xl bg-[#1E3A8A] px-6 py-3 text-center text-sm font-semibold text-white transition duration-200 hover:bg-[#172E70] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#1E3A8A] disabled:cursor-not-allowed disabled:bg-[#9CA3AF]">
                        Create account
                    </button>

                    <p class="text-sm leading-6 text-[#4B5563]">
                        Already have an account?
                        <a href="<?= BASE_URL ?>/login" class="font-semibold text-[#1E3A8A] underline-offset-4 hover:underline">
                            Login &rarr;
                        </a>
                    </p>
                </form>
            </div>
        </section>
    </div>
</section>
