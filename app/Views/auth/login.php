<?php
/**
 * Variables passed from AuthController::login()
 *
 * @var array $errors
 * @var array $old
 * @var string|null $success
 * @var bool $hasFieldErrors
 * @var string $csrfToken
 */
?>

<section class="ui-page px-4 py-8 sm:px-6 lg:px-10 lg:py-10">
    <div class="mx-auto flex w-full max-w-[520px] justify-center lg:min-h-[720px]">
        <section class="flex items-center gap-20">
            <div class="w-full px-5 lg:w-[700px] lg:px-0">
                <img class="h-auto w-full" src="<?= BASE_URL ?>/assets/svg/learning.svg" alt="Contact illustration">
            </div>

            <div class="ui-card w-full p-5 sm:p-8 lg:p-10" data-motion-intro>
                <div class="mb-7">
                    <p class="ui-eyebrow">Sign in</p>
                    <h1 class="mt-2 text-3xl font-bold leading-tight tracking-[-0.01em] text-ui-ink">
                        Welcome back
                    </h1>
                    <p class="mt-3 text-sm leading-6 text-ui-text">
                        Use your username to continue with coursework discussions.
                    </p>
                </div>

                <?php if (!empty($success)): ?>
                <div class="ui-alert ui-alert-success mb-5" role="status">
                    <?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?>
                </div>
                <?php endif; ?>

                <?php if (!empty($errors['general'])): ?>
                <div class="ui-alert ui-alert-error mb-5" role="alert">
                    <?= htmlspecialchars($errors['general'], ENT_QUOTES, 'UTF-8') ?>
                </div>
                <?php endif; ?>

                <p id="login-error-summary" class="<?= $hasFieldErrors ? 'ui-alert ui-alert-error mb-5' : 'sr-only' ?>"
                    <?= $hasFieldErrors ? 'role="alert" tabindex="-1"' : '' ?>>
                    <?= $hasFieldErrors ? 'Please fill the highlighted fields up and try again.' : '' ?>
                </p>

                <form id="login-form" action="<?= BASE_URL ?>/login/authenticate" method="post" class="grid gap-5"
                    novalidate>
                    <input type="hidden" name="_csrf_token"
                        value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
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
                        <label for="password" class="ui-label">Password</label>
                        <input id="password" name="password" type="password" autocomplete="current-password"
                            maxlength="128" dir="ltr" aria-describedby="password-error"
                            aria-invalid="<?= !empty($errors['password']) ? 'true' : 'false' ?>" required
                            class="ui-input h-12 min-w-0 bg-ui-canvas px-4 text-base">
                        <p id="password-error"
                            class="ui-field-error <?= empty($errors['password']) ? 'hidden' : 'flex' ?> min-w-0 items-start gap-1.5 break-words"
                            data-error-for="password" aria-live="polite">
                            <svg viewBox="0 0 16 16" class="mt-0.5 size-3.5 shrink-0" fill="none" aria-hidden="true"
                                data-error-icon>
                                <circle cx="8" cy="8" r="6.5" stroke="currentColor" stroke-width="1.5" />
                                <path d="M8 4.8v3.6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                                <path d="M8 11.1h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                            </svg>
                            <span
                                data-error-message><?= htmlspecialchars($errors['password'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
                        </p>
                    </div>

                    <button type="submit" data-submit-label="Sign in" data-loading-label="Signing in..."
                        class="ui-button ui-button-primary mt-1 min-h-12 min-w-0 px-6">
                        Sign in
                    </button>

                    <p class="text-sm leading-6 text-ui-text">
                        Don't have an account?
                        <a href="<?= BASE_URL ?>/register"
                            class="font-semibold text-brand-royal underline-offset-4 hover:underline">
                            Create account &rarr;
                        </a>
                    </p>
                </form>
            </div>
        </section>
    </div>
</section>