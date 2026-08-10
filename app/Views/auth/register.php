<?php
/**
 * Variables passed from AuthController::register()
 *
 * @var array $errors
 * @var array $old
 * @var bool $hasFieldErrors
 * @var string $csrfToken
 */
?>

<section class="ui-page px-4 py-8 sm:px-6 lg:px-10 lg:py-10">
    <div class="mx-auto flex w-full max-w-[560px] justify-center lg:min-h-[760px]">
        <section class="flex items-center gap-80">
            <div class="w-full px-5 lg:w-[700px] lg:px-0">
                <img class="h-auto w-full" src="<?= BASE_URL ?>/assets/svg/register.svg" alt="Contact illustration">
            </div>

            <div class="ui-card w-full p-5 sm:p-8 lg:p-10" data-motion-intro>
                <div class="mb-7">
                    <p class="ui-eyebrow">Register</p>
                    <h1 class="mt-2 text-3xl font-bold leading-tight tracking-[-0.01em] text-ui-ink">
                        Create account
                    </h1>
                    <p class="mt-3 text-sm leading-6 text-ui-text">
                        Use a valid email so discussions stay connected to your account.
                    </p>
                </div>

                <?php if (!empty($errors['general'])): ?>
                <div class="ui-alert ui-alert-error mb-5" role="alert">
                    <?= htmlspecialchars($errors['general'], ENT_QUOTES, 'UTF-8') ?>
                </div>
                <?php endif; ?>

                <p id="register-error-summary"
                    class="<?= $hasFieldErrors ? 'ui-alert ui-alert-error mb-5' : 'sr-only' ?>"
                    <?= $hasFieldErrors ? 'role="alert" tabindex="-1"' : '' ?>>
                    <?= $hasFieldErrors ? 'Please fix the highlighted fields and try again.' : '' ?>
                </p>

                <form id="register-form" action="<?= BASE_URL ?>/register/store" method="post" class="grid gap-4"
                    novalidate>
                    <input type="hidden" name="_csrf_token"
                        value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                    <div class="grid min-w-0 gap-4 sm:grid-cols-2">
                        <div class="min-w-0">
                            <label for="first_name" class="ui-label">First name</label>
                            <input id="first_name" name="first_name" type="text"
                                value="<?= htmlspecialchars($old['first_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                autocomplete="given-name" maxlength="50" dir="auto" aria-describedby="first_name-error"
                                aria-invalid="<?= !empty($errors['first_name']) ? 'true' : 'false' ?>" required
                                class="ui-input h-12 min-w-0 bg-ui-canvas px-4 text-base">
                            <p id="first_name-error"
                                class="ui-field-error <?= empty($errors['first_name']) ? 'hidden' : 'flex' ?> min-w-0 items-start gap-1.5 break-words"
                                data-error-for="first_name" aria-live="polite">
                                <svg viewBox="0 0 16 16" class="mt-0.5 size-3.5 shrink-0" fill="none" aria-hidden="true"
                                    data-error-icon>
                                    <circle cx="8" cy="8" r="6.5" stroke="currentColor" stroke-width="1.5" />
                                    <path d="M8 4.8v3.6" stroke="currentColor" stroke-width="1.5"
                                        stroke-linecap="round" />
                                    <path d="M8 11.1h.01" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" />
                                </svg>
                                <span
                                    data-error-message><?= htmlspecialchars($errors['first_name'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
                            </p>
                        </div>

                        <div class="min-w-0">
                            <label for="last_name" class="ui-label">Last name</label>
                            <input id="last_name" name="last_name" type="text"
                                value="<?= htmlspecialchars($old['last_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                autocomplete="family-name" maxlength="50" dir="auto" aria-describedby="last_name-error"
                                aria-invalid="<?= !empty($errors['last_name']) ? 'true' : 'false' ?>" required
                                class="ui-input h-12 min-w-0 bg-ui-canvas px-4 text-base">
                            <p id="last_name-error"
                                class="ui-field-error <?= empty($errors['last_name']) ? 'hidden' : 'flex' ?> min-w-0 items-start gap-1.5 break-words"
                                data-error-for="last_name" aria-live="polite">
                                <svg viewBox="0 0 16 16" class="mt-0.5 size-3.5 shrink-0" fill="none" aria-hidden="true"
                                    data-error-icon>
                                    <circle cx="8" cy="8" r="6.5" stroke="currentColor" stroke-width="1.5" />
                                    <path d="M8 4.8v3.6" stroke="currentColor" stroke-width="1.5"
                                        stroke-linecap="round" />
                                    <path d="M8 11.1h.01" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" />
                                </svg>
                                <span
                                    data-error-message><?= htmlspecialchars($errors['last_name'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
                            </p>
                        </div>
                    </div>

                    <div class="min-w-0">
                        <label for="username" class="ui-label">Username</label>
                        <input id="username" name="username" type="text"
                            value="<?= htmlspecialchars($old['username'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                            autocomplete="username" autocapitalize="none" spellcheck="false" inputmode="text"
                            maxlength="75" dir="ltr" aria-describedby="username-error"
                            aria-invalid="<?= !empty($errors['username']) ? 'true' : 'false' ?>" required
                            class="ui-input h-12 min-w-0 bg-ui-canvas px-4 text-base">
                        <p id="username-error"
                            class="ui-field-error <?= empty($errors['username']) ? 'hidden' : 'flex' ?> min-w-0 items-start gap-1.5 break-words"
                            data-error-for="username" aria-live="polite">
                            <svg viewBox="0 0 16 16" class="mt-0.5 size-3.5 shrink-0" fill="none" aria-hidden="true"
                                data-error-icon>
                                <circle cx="8" cy="8" r="6.5" stroke="currentColor" stroke-width="1.5" />
                                <path d="M8 4.8v3.6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                                <path d="M8 11.1h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                            </svg>
                            <span
                                data-error-message><?= htmlspecialchars($errors['username'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
                        </p>
                    </div>

                    <div class="min-w-0">
                        <label for="email" class="ui-label">Email</label>
                        <input id="email" name="email" type="email"
                            value="<?= htmlspecialchars($old['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                            autocomplete="email" autocapitalize="none" spellcheck="false" inputmode="email"
                            maxlength="150" dir="ltr" aria-describedby="email-error"
                            aria-invalid="<?= !empty($errors['email']) ? 'true' : 'false' ?>" required
                            class="ui-input h-12 min-w-0 bg-ui-canvas px-4 text-base">
                        <p id="email-error"
                            class="ui-field-error <?= empty($errors['email']) ? 'hidden' : 'flex' ?> min-w-0 items-start gap-1.5 break-words"
                            data-error-for="email" aria-live="polite">
                            <svg viewBox="0 0 16 16" class="mt-0.5 size-3.5 shrink-0" fill="none" aria-hidden="true"
                                data-error-icon>
                                <circle cx="8" cy="8" r="6.5" stroke="currentColor" stroke-width="1.5" />
                                <path d="M8 4.8v3.6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                                <path d="M8 11.1h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                            </svg>
                            <span
                                data-error-message><?= htmlspecialchars($errors['email'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
                        </p>
                    </div>

                    <div class="grid min-w-0 gap-4 sm:grid-cols-2">
                        <div class="min-w-0">
                            <label for="password" class="ui-label">Password</label>
                            <input id="password" name="password" type="password" autocomplete="new-password"
                                maxlength="128" dir="ltr" aria-describedby="password-hints-content password-error"
                                aria-invalid="<?= !empty($errors['password']) ? 'true' : 'false' ?>" required
                                class="ui-input h-12 min-w-0 bg-ui-canvas px-4 text-base">
                            <div class="mt-2 flex gap-0.5 overflow-hidden rounded-full" data-password-meter
                                aria-hidden="true">
                                <?php for ($passwordMeterSegment = 0; $passwordMeterSegment < 4; $passwordMeterSegment++): ?>
                                <span class="h-1.5 flex-1 bg-ui-border transition" data-password-meter-segment></span>
                                <?php endfor; ?>
                            </div>
                            <p id="password-error"
                                class="ui-field-error <?= empty($errors['password']) ? 'hidden' : 'flex' ?> min-w-0 items-start gap-1.5 break-words"
                                data-error-for="password" aria-live="polite">
                                <svg viewBox="0 0 16 16" class="mt-0.5 size-3.5 shrink-0" fill="none" aria-hidden="true"
                                    data-error-icon>
                                    <circle cx="8" cy="8" r="6.5" stroke="currentColor" stroke-width="1.5" />
                                    <path d="M8 4.8v3.6" stroke="currentColor" stroke-width="1.5"
                                        stroke-linecap="round" />
                                    <path d="M8 11.1h.01" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" />
                                </svg>
                                <span
                                    data-error-message><?= htmlspecialchars($errors['password'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
                            </p>

                        </div>

                        <div class="min-w-0">
                            <label for="confirm_password" class="ui-label">Confirm password</label>
                            <input id="confirm_password" name="confirm_password" type="password"
                                autocomplete="new-password" maxlength="128" dir="ltr"
                                aria-describedby="confirm_password-status confirm_password-error"
                                aria-invalid="<?= !empty($errors['confirm_password']) ? 'true' : 'false' ?>" required
                                class="ui-input h-12 min-w-0 bg-ui-canvas px-4 text-base">
                            <p id="confirm_password-error"
                                class="ui-field-error <?= empty($errors['confirm_password']) ? 'hidden' : 'flex' ?> min-w-0 items-start gap-1.5 break-words"
                                data-error-for="confirm_password" aria-live="polite">
                                <svg viewBox="0 0 16 16" class="mt-0.5 size-3.5 shrink-0" fill="none" aria-hidden="true"
                                    data-error-icon>
                                    <circle cx="8" cy="8" r="6.5" stroke="currentColor" stroke-width="1.5" />
                                    <path d="M8 4.8v3.6" stroke="currentColor" stroke-width="1.5"
                                        stroke-linecap="round" />
                                    <path d="M8 11.1h.01" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" />
                                </svg>
                                <span
                                    data-error-message><?= htmlspecialchars($errors['confirm_password'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
                            </p>
                            <p id="confirm_password-status" class="mt-2 hidden items-center gap-1.5 text-sm"
                                data-confirm-password-status aria-live="polite">
                                <svg class="size-4 shrink-0" viewBox="0 0 16 16" fill="none" aria-hidden="true"
                                    data-check>
                                    <circle cx="8" cy="8" r="6.5" stroke="currentColor" stroke-width="1.5" />
                                    <path d="m5 8 2 2 4-4" stroke="currentColor" stroke-width="1.5"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <svg class="size-4 shrink-0" viewBox="0 0 16 16" fill="none" aria-hidden="true"
                                    data-uncheck>
                                    <circle cx="8" cy="8" r="6.5" stroke="currentColor" stroke-width="1.5" />
                                    <path d="m6 6 4 4m0-4-4 4" stroke="currentColor" stroke-width="1.5"
                                        stroke-linecap="round" />
                                </svg>
                                <span data-status-message></span>
                            </p>
                        </div>

                        <div id="password-hints-content"
                            class="hidden rounded-xl border border-ui-border bg-ui-brand-soft p-4 sm:col-span-2"
                            data-password-hints aria-live="polite">
                            <div
                                class="flex flex-wrap items-center justify-between gap-2 border-b border-ui-border pb-3">
                                <p class="text-sm font-semibold text-ui-ink">Password requirements</p>
                                <p class="rounded-full bg-white px-3 py-1 text-xs text-ui-text">
                                    Status:
                                    <span class="font-semibold text-ui-ink" data-password-status>Empty</span>
                                </p>
                            </div>
                            <ul class="mt-3 grid gap-2 text-sm text-ui-text sm:grid-cols-2">
                                <li class="flex items-center gap-x-2 rounded-lg border border-ui-border bg-white px-3 py-2"
                                    data-password-rule="required">
                                    <svg class="hidden size-4 shrink-0" viewBox="0 0 16 16" fill="none"
                                        aria-hidden="true" data-check>
                                        <circle cx="8" cy="8" r="6.5" stroke="currentColor" stroke-width="1.5" />
                                        <path d="m5 8 2 2 4-4" stroke="currentColor" stroke-width="1.5"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <svg class="size-4 shrink-0" viewBox="0 0 16 16" fill="none" aria-hidden="true"
                                        data-uncheck>
                                        <circle cx="8" cy="8" r="6.5" stroke="currentColor" stroke-width="1.5" />
                                        <path d="m6 6 4 4m0-4-4 4" stroke="currentColor" stroke-width="1.5"
                                            stroke-linecap="round" />
                                    </svg>
                                    Password is required.
                                </li>
                                <li class="flex items-center gap-x-2 rounded-lg border border-ui-border bg-white px-3 py-2"
                                    data-password-rule="length">
                                    <svg class="hidden size-4 shrink-0" viewBox="0 0 16 16" fill="none"
                                        aria-hidden="true" data-check>
                                        <circle cx="8" cy="8" r="6.5" stroke="currentColor" stroke-width="1.5" />
                                        <path d="m5 8 2 2 4-4" stroke="currentColor" stroke-width="1.5"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <svg class="size-4 shrink-0" viewBox="0 0 16 16" fill="none" aria-hidden="true"
                                        data-uncheck>
                                        <circle cx="8" cy="8" r="6.5" stroke="currentColor" stroke-width="1.5" />
                                        <path d="m6 6 4 4m0-4-4 4" stroke="currentColor" stroke-width="1.5"
                                            stroke-linecap="round" />
                                    </svg>
                                    Between 8 and 128 characters.
                                </li>
                            </ul>
                        </div>
                    </div>

                    <p class="text-xs leading-5 text-ui-text">
                        By creating an account, you agree to use the platform for Greenwich coursework discussion and
                        academic support. Read our
                        <a href="<?= BASE_URL ?>/privacy-policy"
                            class="font-semibold text-brand-royal underline underline-offset-4">Privacy Policy</a>
                        to understand how your account and activity information is handled.
                    </p>

                    <button type="submit" data-submit-label="Create account" data-loading-label="Creating account..."
                        class="ui-button ui-button-primary mt-1 min-h-12 min-w-0 px-6">
                        Create account
                    </button>

                    <p class="text-sm leading-6 text-ui-text">
                        Already have an account?
                        <a href="<?= BASE_URL ?>/login"
                            class="font-semibold text-brand-royal underline-offset-4 hover:underline">
                            Login &rarr;
                        </a>
                    </p>
                </form>
            </div>
        </section>
    </div>
</section>
