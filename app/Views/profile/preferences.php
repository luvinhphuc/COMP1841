<?php
/**
 * Variables passed from ProfileController::preferences()
 *
 * @var array $authUser
 * @var array $profileHeader
 * @var string $profileActiveTab
 * @var array $profileErrors
 * @var array $profileOld
 * @var array $avatarErrors
 * @var array $passwordErrors
 * @var string|null $authAvatarUrl
 * @var string $authAvatarInitial
 * @var string $csrfToken
 */

$fieldError = static function (array $errors, string $field) {
    return trim((string)($errors[$field] ?? ''));
};

$fieldBorder = static function (array $errors, string $field) {
    return trim((string)($errors[$field] ?? '')) !== '' ? 'border-ui-danger' : 'border-ui-border-strong';
};

$profileFirstName = (string)($profileOld['first_name'] ?? $authUser['first_name'] ?? '');
$profileLastName = (string)($profileOld['last_name'] ?? $authUser['last_name'] ?? '');
$profileUsername = (string)($profileOld['username'] ?? $authUser['username'] ?? '');
$profileEmail = (string)($profileOld['email'] ?? $authUser['email'] ?? '');
?>

<section class="ui-page"
    style="background: url('<?= BASE_URL ?>/assets/svg/logo-inset.svg') no-repeat calc(100% + 250px) calc(150px - 100%) / 800px auto;">

    <div class="ui-container flex flex-col gap-6">
        <?php require ROOT_PATH . '/app/Views/profile/partials/header.php'; ?>
        <?php require ROOT_PATH . '/app/Views/profile/partials/navigation.php'; ?>

        <section aria-labelledby="profile-preferences-heading">
            <div data-motion-intro>
                <h2 id="profile-preferences-heading" class="text-2xl font-semibold text-ui-ink">Preferences</h2>
                <p class="mt-1 text-sm leading-6 text-ui-text">Update your profile information, avatar, and
                    password.</p>
            </div>

            <div class="mt-5 grid gap-6 lg:grid-cols-2" data-motion-list>
                <section class="ui-card p-5 sm:p-6" aria-labelledby="profile-heading" data-motion-item>
                    <h2 id="profile-heading" class="text-xl font-semibold leading-7 text-ui-ink">
                        Profile information
                    </h2>
                    <p class="mt-1 text-sm leading-6 text-ui-text">Manage the name, username, and email on your
                        account.</p>

                    <form action="<?= BASE_URL ?>/profile/preferences/profile" method="post" class="mt-6" novalidate>
                        <input type="hidden" name="_csrf_token"
                            value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">

                        <?php if ($fieldError($profileErrors, 'general') !== ''): ?>
                        <div class="ui-alert ui-alert-error mb-5" role="alert">
                            <?= htmlspecialchars($fieldError($profileErrors, 'general'), ENT_QUOTES, 'UTF-8') ?>
                        </div>
                        <?php endif; ?>

                        <div class="grid gap-5 sm:grid-cols-2">
                            <div>
                                <label for="first_name" class="text-sm font-semibold text-ui-ink">First name</label>
                                <input id="first_name" name="first_name" type="text" maxlength="50"
                                    autocomplete="given-name"
                                    value="<?= htmlspecialchars($profileFirstName, ENT_QUOTES, 'UTF-8') ?>"
                                    aria-describedby="first_name-error"
                                    aria-invalid="<?= $fieldError($profileErrors, 'first_name') !== '' ? 'true' : 'false' ?>"
                                    class="ui-input mt-2 <?= $fieldBorder($profileErrors, 'first_name') ?>">
                                <p id="first_name-error"
                                    class="mt-2 <?= $fieldError($profileErrors, 'first_name') === '' ? 'hidden' : 'block' ?> text-sm leading-5 text-ui-danger"
                                    aria-live="polite">
                                    <?= htmlspecialchars($fieldError($profileErrors, 'first_name'), ENT_QUOTES, 'UTF-8') ?>
                                </p>
                            </div>

                            <div>
                                <label for="last_name" class="text-sm font-semibold text-ui-ink">Last name</label>
                                <input id="last_name" name="last_name" type="text" maxlength="50"
                                    autocomplete="family-name"
                                    value="<?= htmlspecialchars($profileLastName, ENT_QUOTES, 'UTF-8') ?>"
                                    aria-describedby="last_name-error"
                                    aria-invalid="<?= $fieldError($profileErrors, 'last_name') !== '' ? 'true' : 'false' ?>"
                                    class="ui-input mt-2 <?= $fieldBorder($profileErrors, 'last_name') ?>">
                                <p id="last_name-error"
                                    class="mt-2 <?= $fieldError($profileErrors, 'last_name') === '' ? 'hidden' : 'block' ?> text-sm leading-5 text-ui-danger"
                                    aria-live="polite">
                                    <?= htmlspecialchars($fieldError($profileErrors, 'last_name'), ENT_QUOTES, 'UTF-8') ?>
                                </p>
                            </div>
                        </div>

                        <div class="mt-5">
                            <label for="username" class="text-sm font-semibold text-ui-ink">Username</label>
                            <input id="username" name="username" type="text" maxlength="75" autocomplete="username"
                                value="<?= htmlspecialchars($profileUsername, ENT_QUOTES, 'UTF-8') ?>"
                                aria-describedby="username-help username-error"
                                aria-invalid="<?= $fieldError($profileErrors, 'username') !== '' ? 'true' : 'false' ?>"
                                class="ui-input mt-2 <?= $fieldBorder($profileErrors, 'username') ?>">
                            <p id="username-help" class="mt-2 text-xs leading-5 text-ui-text">
                                Letters, numbers, underscores, dots, and hyphens only.
                            </p>
                            <p id="username-error"
                                class="mt-1 <?= $fieldError($profileErrors, 'username') === '' ? 'hidden' : 'block' ?> text-sm leading-5 text-ui-danger"
                                aria-live="polite">
                                <?= htmlspecialchars($fieldError($profileErrors, 'username'), ENT_QUOTES, 'UTF-8') ?>
                            </p>
                        </div>

                        <div class="mt-5">
                            <label for="email" class="text-sm font-semibold text-ui-ink">Email</label>
                            <input id="email" name="email" type="email" maxlength="150" autocomplete="email"
                                value="<?= htmlspecialchars($profileEmail, ENT_QUOTES, 'UTF-8') ?>"
                                aria-describedby="email-error"
                                aria-invalid="<?= $fieldError($profileErrors, 'email') !== '' ? 'true' : 'false' ?>"
                                class="ui-input mt-2 <?= $fieldBorder($profileErrors, 'email') ?>">
                            <p id="email-error"
                                class="mt-2 <?= $fieldError($profileErrors, 'email') === '' ? 'hidden' : 'block' ?> text-sm leading-5 text-ui-danger"
                                aria-live="polite">
                                <?= htmlspecialchars($fieldError($profileErrors, 'email'), ENT_QUOTES, 'UTF-8') ?>
                            </p>
                        </div>

                        <div class="mt-6 flex justify-end">
                            <button type="submit" class="ui-button ui-button-primary">
                                Save profile
                            </button>
                        </div>
                    </form>
                </section>

                <section class="ui-card p-5 sm:p-6" aria-labelledby="avatar-heading" data-motion-item>
                    <h2 id="avatar-heading" class="text-xl font-semibold leading-7 text-ui-ink">Avatar</h2>
                    <p class="mt-1 text-sm leading-6 text-ui-text">Choose a JPG, PNG, GIF, or WebP image up to 2 MB.</p>

                    <form action="<?= BASE_URL ?>/profile/preferences/avatar" method="post"
                        enctype="multipart/form-data" class="mt-6" novalidate>
                        <input type="hidden" name="_csrf_token"
                            value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">

                        <?php if ($fieldError($avatarErrors, 'general') !== ''): ?>
                        <div class="ui-alert ui-alert-error mb-5" role="alert">
                            <?= htmlspecialchars($fieldError($avatarErrors, 'general'), ENT_QUOTES, 'UTF-8') ?>
                        </div>
                        <?php endif; ?>

                        <div class="flex flex-col gap-5 sm:flex-row sm:items-center">
                            <?php if ($authAvatarUrl !== null): ?>
                            <img src="<?= htmlspecialchars($authAvatarUrl, ENT_QUOTES, 'UTF-8') ?>" alt="Current avatar"
                                class="size-24 shrink-0 rounded-full border border-ui-border-strong object-cover">
                            <?php else: ?>
                            <span
                                class="flex size-24 shrink-0 items-center justify-center rounded-full border border-ui-border-strong bg-brand-royal text-2xl font-semibold text-white"
                                aria-label="Current avatar initials">
                                <?= htmlspecialchars($authAvatarInitial, ENT_QUOTES, 'UTF-8') ?>
                            </span>
                            <?php endif; ?>
                            <div class="min-w-0 flex-1">
                                <label for="avatar" class="text-sm font-semibold text-ui-ink">Avatar image</label>
                                <input id="avatar" name="avatar" type="file"
                                    accept=".jpg,.jpeg,.png,.gif,.webp,image/jpeg,image/png,image/gif,image/webp"
                                    aria-describedby="avatar-error"
                                    aria-invalid="<?= $fieldError($avatarErrors, 'avatar') !== '' ? 'true' : 'false' ?>"
                                    class="mt-2 block w-full rounded-lg border <?= $fieldBorder($avatarErrors, 'avatar') ?> bg-white text-sm text-ui-text file:mr-4 file:border-0 file:bg-ui-neutral-soft file:px-4 file:py-3 file:text-sm file:font-semibold file:text-ui-ink hover:file:bg-ui-neutral-soft">
                                <p id="avatar-error"
                                    class="mt-2 <?= $fieldError($avatarErrors, 'avatar') === '' ? 'hidden' : 'block' ?> text-sm leading-5 text-ui-danger"
                                    aria-live="polite">
                                    <?= htmlspecialchars($fieldError($avatarErrors, 'avatar'), ENT_QUOTES, 'UTF-8') ?>
                                </p>
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end">
                            <button type="submit" class="ui-button ui-button-primary">
                                Update avatar
                            </button>
                        </div>
                    </form>
                </section>

                <section class="ui-card flex justify-between p-5 sm:p-6 lg:col-span-2"
                    aria-labelledby="password-heading" data-motion-item>
                    <div class="mx-auto" data-motion-item>
                        <img src="<?= BASE_URL ?>/assets/svg/change_password.svg" class="w-[300px]"
                            alt="Change password">
                    </div>
                    <div class="max-w-2xl">
                        <h2 id="password-heading" class="text-xl font-semibold leading-7 text-ui-ink">Password</h2>
                        <p class="mt-1 text-sm leading-6 text-ui-text">Use your current password to choose a new
                            one.</p>

                        <form action="<?= BASE_URL ?>/profile/preferences/password" method="post" class="mt-6"
                            novalidate>
                            <input type="hidden" name="_csrf_token"
                                value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">

                            <?php if ($fieldError($passwordErrors, 'general') !== ''): ?>
                            <div class="ui-alert ui-alert-error mb-5" role="alert">
                                <?= htmlspecialchars($fieldError($passwordErrors, 'general'), ENT_QUOTES, 'UTF-8') ?>
                            </div>
                            <?php endif; ?>

                            <div>
                                <label for="current_password" class="text-sm font-semibold text-ui-ink">Current
                                    password</label>
                                <input id="current_password" name="current_password" type="password"
                                    autocomplete="current-password" aria-describedby="current_password-error"
                                    aria-invalid="<?= $fieldError($passwordErrors, 'current_password') !== '' ? 'true' : 'false' ?>"
                                    class="ui-input mt-2 <?= $fieldBorder($passwordErrors, 'current_password') ?>">
                                <p id="current_password-error"
                                    class="mt-2 <?= $fieldError($passwordErrors, 'current_password') === '' ? 'hidden' : 'block' ?> text-sm leading-5 text-ui-danger"
                                    aria-live="polite">
                                    <?= htmlspecialchars($fieldError($passwordErrors, 'current_password'), ENT_QUOTES, 'UTF-8') ?>
                                </p>
                            </div>

                            <div class="mt-5 grid gap-5 sm:grid-cols-2">
                                <div>
                                    <label for="new_password" class="text-sm font-semibold text-ui-ink">New
                                        password</label>
                                    <input id="new_password" name="new_password" type="password" minlength="8"
                                        maxlength="128" autocomplete="new-password"
                                        aria-describedby="new_password-error"
                                        aria-invalid="<?= $fieldError($passwordErrors, 'new_password') !== '' ? 'true' : 'false' ?>"
                                        class="ui-input mt-2 <?= $fieldBorder($passwordErrors, 'new_password') ?>">
                                    <p id="new_password-error"
                                        class="mt-2 <?= $fieldError($passwordErrors, 'new_password') === '' ? 'hidden' : 'block' ?> text-sm leading-5 text-ui-danger"
                                        aria-live="polite">
                                        <?= htmlspecialchars($fieldError($passwordErrors, 'new_password'), ENT_QUOTES, 'UTF-8') ?>
                                    </p>
                                </div>

                                <div>
                                    <label for="confirm_password" class="text-sm font-semibold text-ui-ink">Confirm new
                                        password</label>
                                    <input id="confirm_password" name="confirm_password" type="password" maxlength="128"
                                        autocomplete="new-password" aria-describedby="confirm_password-error"
                                        aria-invalid="<?= $fieldError($passwordErrors, 'confirm_password') !== '' ? 'true' : 'false' ?>"
                                        class="ui-input mt-2 <?= $fieldBorder($passwordErrors, 'confirm_password') ?>">
                                    <p id="confirm_password-error"
                                        class="mt-2 <?= $fieldError($passwordErrors, 'confirm_password') === '' ? 'hidden' : 'block' ?> text-sm leading-5 text-ui-danger"
                                        aria-live="polite">
                                        <?= htmlspecialchars($fieldError($passwordErrors, 'confirm_password'), ENT_QUOTES, 'UTF-8') ?>
                                    </p>
                                </div>
                            </div>

                            <div class="mt-6 flex justify-end">
                                <button type="submit" class="ui-button ui-button-primary">
                                    Change password
                                </button>
                            </div>
                        </form>
                    </div>
                </section>
            </div>
        </section>
    </div>
</section>