<?php
/**
 * Variables passed from ContactController::index()
 *
 * @var array $errors
 * @var array $old
 * @var bool $identityIsReadOnly
 * @var string $csrfToken
 */

$fieldError = static function (array $errors, string $field) {
    return trim((string)($errors[$field] ?? ''));
};

$fieldBorder = static function (array $errors, string $field) {
    return trim((string)($errors[$field] ?? '')) !== ''
        ? 'border-ui-danger'
        : 'border-ui-border-strong';
};

$name = (string)($old['name'] ?? '');
$email = (string)($old['email'] ?? '');
$subject = (string)($old['subject'] ?? '');
$message = (string)($old['message'] ?? '');
$readOnlyAttributes = $identityIsReadOnly ? ' readonly aria-readonly="true"' : '';
$identityInputTone = $identityIsReadOnly ? 'bg-ui-neutral-soft text-ui-muted' : 'bg-white text-ui-ink';
?>

<section class="ui-page">
    <div class=" flex w-full justify-center lg:min-h-[720px] flex-col lg:flex-row-reverse lg:items-center">
        <div class="mr-auto w-full px-5 lg:w-[660px] lg:px-0">
            <img class="h-auto w-full" src="<?= BASE_URL ?>/assets/svg/contact.svg" alt="Contact illustration">
        </div>

        <div class="ui-container-narrow flex-1 max-w-3xl">
            <div data-motion-intro>
                <p class="ui-eyebrow">Get in touch</p>
                <h1 class="ui-page-title">Contact us</h1>
                <p class="ui-page-description">
                    Send a message to the site administrator. We will use your email address if a reply is needed.
                    See the
                    <a href="<?= BASE_URL ?>/privacy-policy"
                        class="font-semibold text-brand-royal underline underline-offset-4">Privacy Policy</a>
                    for how contact details and messages are handled.
                </p>
            </div>

            <section class="ui-card mt-6 p-5 sm:p-6" aria-labelledby="contact-form-heading" data-motion-reveal>
                <h2 id="contact-form-heading" class="text-xl font-semibold leading-7 text-ui-ink">Your message</h2>

                <?php if ($identityIsReadOnly): ?>
                <p class="mt-1 text-sm leading-6 text-ui-text">
                    Your name and email are filled from your signed-in account.
                </p>
                <?php endif; ?>

                <form id="contact-form" action="<?= BASE_URL ?>/contact/store" method="post" class="mt-6" novalidate>
                    <input type="hidden" name="_csrf_token"
                        value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">

                    <?php if ($fieldError($errors, 'general') !== ''): ?>
                    <div class="ui-alert ui-alert-error mb-5" role="alert">
                        <?= htmlspecialchars($fieldError($errors, 'general'), ENT_QUOTES, 'UTF-8') ?>
                    </div>
                    <?php endif; ?>

                    <div class="grid gap-5 sm:grid-cols-2">
                        <div>
                            <label for="name" class="text-sm font-semibold text-ui-ink">Name</label>
                            <input id="name" name="name" type="text" maxlength="75" autocomplete="name" required
                                value="<?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?>"
                                aria-describedby="name-error"
                                aria-invalid="<?= $fieldError($errors, 'name') !== '' ? 'true' : 'false' ?>"
                                class="ui-input mt-2 <?= $fieldBorder($errors, 'name') ?> <?= $identityInputTone ?>"
                                <?= $readOnlyAttributes ?>>
                            <p id="name-error"
                                class="mt-2 <?= $fieldError($errors, 'name') === '' ? 'hidden' : 'block' ?> text-sm leading-5 text-ui-danger"
                                aria-live="polite">
                                <?= htmlspecialchars($fieldError($errors, 'name'), ENT_QUOTES, 'UTF-8') ?>
                            </p>
                        </div>

                        <div>
                            <label for="email" class="text-sm font-semibold text-ui-ink">Email</label>
                            <input id="email" name="email" type="email" maxlength="150" autocomplete="email" required
                                value="<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?>"
                                aria-describedby="email-error"
                                aria-invalid="<?= $fieldError($errors, 'email') !== '' ? 'true' : 'false' ?>"
                                class="ui-input mt-2 <?= $fieldBorder($errors, 'email') ?> <?= $identityInputTone ?>"
                                <?= $readOnlyAttributes ?>>
                            <p id="email-error"
                                class="mt-2 <?= $fieldError($errors, 'email') === '' ? 'hidden' : 'block' ?> text-sm leading-5 text-ui-danger"
                                aria-live="polite">
                                <?= htmlspecialchars($fieldError($errors, 'email'), ENT_QUOTES, 'UTF-8') ?>
                            </p>
                        </div>
                    </div>

                    <div class="mt-5">
                        <label for="subject" class="text-sm font-semibold text-ui-ink">Subject</label>
                        <input id="subject" name="subject" type="text" maxlength="255" required
                            value="<?= htmlspecialchars($subject, ENT_QUOTES, 'UTF-8') ?>"
                            aria-describedby="subject-error"
                            aria-invalid="<?= $fieldError($errors, 'subject') !== '' ? 'true' : 'false' ?>"
                            class="ui-input mt-2 <?= $fieldBorder($errors, 'subject') ?>">
                        <p id="subject-error"
                            class="mt-2 <?= $fieldError($errors, 'subject') === '' ? 'hidden' : 'block' ?> text-sm leading-5 text-ui-danger"
                            aria-live="polite">
                            <?= htmlspecialchars($fieldError($errors, 'subject'), ENT_QUOTES, 'UTF-8') ?>
                        </p>
                    </div>

                    <div class="mt-5">
                        <div class="flex items-center justify-between gap-4">
                            <label for="message" class="text-sm font-semibold text-ui-ink">Message</label>
                            <span class="text-xs text-ui-muted">Maximum 5000 characters</span>
                        </div>
                        <textarea id="message" name="message" rows="8" maxlength="5000" required
                            aria-describedby="message-error"
                            aria-invalid="<?= $fieldError($errors, 'message') !== '' ? 'true' : 'false' ?>"
                            class="ui-input mt-2 min-h-40 resize-y <?= $fieldBorder($errors, 'message') ?>"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></textarea>
                        <p id="message-error"
                            class="mt-2 <?= $fieldError($errors, 'message') === '' ? 'hidden' : 'block' ?> text-sm leading-5 text-ui-danger"
                            aria-live="polite">
                            <?= htmlspecialchars($fieldError($errors, 'message'), ENT_QUOTES, 'UTF-8') ?>
                        </p>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <button type="submit" class="ui-button ui-button-primary gap-2">
                            <span data-send-spinner
                                class="hidden size-4 shrink-0 animate-spin rounded-full border-2 border-white/40 border-t-white"
                                aria-hidden="true"></span>
                            <span data-send-label>Send message</span>
                        </button>
                    </div>
                </form>
            </section>
        </div>
    </div>

</section>
