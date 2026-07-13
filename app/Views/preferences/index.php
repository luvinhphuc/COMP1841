<?php
/**
 * Variables passed from PreferencesController::index()
 *
 * @var array $user
 * @var array $profileErrors
 * @var array $profileOld
 * @var array $avatarErrors
 * @var array $passwordErrors
 * @var bool $showModulePreferences
 * @var array $selectedModules
 * @var string|null $authAvatarUrl
 * @var string $authAvatarInitial
 * @var string $csrfToken
 */

$fieldError = static function (array $errors, string $field) {
    return trim((string) ($errors[$field] ?? ''));
};

$fieldBorder = static function (array $errors, string $field) {
    return trim((string) ($errors[$field] ?? '')) !== ''
        ? 'border-[#ba1a1a] focus:border-[#ba1a1a] focus:ring-[#ba1a1a]/10'
        : 'border-[#c4c7c7] focus:border-[#315f90] focus:ring-[#315f90]/15';
};

$firstName = (string) ($profileOld['first_name'] ?? $user['first_name'] ?? '');
$lastName = (string) ($profileOld['last_name'] ?? $user['last_name'] ?? '');
$username = (string) ($profileOld['username'] ?? $user['username'] ?? '');
?>

<section class="min-h-[calc(100vh-80px)] bg-[#f7f9fd] text-[#27313a]">
    <div class="mx-auto flex max-w-5xl flex-col gap-6 px-5 py-8 sm:px-8 lg:px-10 lg:py-10">
        <div>
            <p class="text-sm font-semibold uppercase tracking-[0.12em] text-[#315f90]">Account</p>
            <h1 class="mt-2 text-2xl font-bold leading-8 text-[#191c1f] sm:text-[26px]">Preferences</h1>
            <p class="mt-2 max-w-2xl text-sm leading-6 text-[#444748]">
                Update your profile information, modules, avatar, and password.
            </p>
        </div>

        <div class="grid gap-6 lg:grid-cols-2">
            <section class="rounded-xl border border-[#c4c7c7] bg-white p-5 sm:p-6" aria-labelledby="profile-heading">
                <h2 id="profile-heading" class="text-xl font-semibold leading-7 text-[#191c1f]">
                    Profile information
                </h2>
                <p class="mt-1 text-sm leading-6 text-[#444748]">Manage the name and username shown on your account.</p>

                <form action="<?= BASE_URL ?>/preferences/profile" method="post" class="mt-6" novalidate>
                    <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">

                    <?php if ($fieldError($profileErrors, 'general') !== ''): ?>
                    <div class="mb-5 rounded-xl border border-[#ba1a1a] bg-[#ba1a1a]/5 px-4 py-3 text-sm leading-6 text-[#8f1111]" role="alert">
                        <?= htmlspecialchars($fieldError($profileErrors, 'general'), ENT_QUOTES, 'UTF-8') ?>
                    </div>
                    <?php endif; ?>

                    <div class="grid gap-5 sm:grid-cols-2">
                        <div>
                            <label for="first_name" class="text-sm font-semibold text-[#191c1f]">First name</label>
                            <input
                                id="first_name"
                                name="first_name"
                                type="text"
                                maxlength="50"
                                autocomplete="given-name"
                                value="<?= htmlspecialchars($firstName, ENT_QUOTES, 'UTF-8') ?>"
                                aria-describedby="first_name-error"
                                aria-invalid="<?= $fieldError($profileErrors, 'first_name') !== '' ? 'true' : 'false' ?>"
                                class="mt-2 h-11 w-full rounded-lg border <?= $fieldBorder($profileErrors, 'first_name') ?> bg-white px-3 text-sm text-[#27313a] outline-none transition focus:ring-2"
                            >
                            <p
                                id="first_name-error"
                                class="mt-2 <?= $fieldError($profileErrors, 'first_name') === '' ? 'hidden' : 'block' ?> text-sm leading-5 text-[#ba1a1a]"
                                aria-live="polite"
                            >
                                <?= htmlspecialchars($fieldError($profileErrors, 'first_name'), ENT_QUOTES, 'UTF-8') ?>
                            </p>
                        </div>

                        <div>
                            <label for="last_name" class="text-sm font-semibold text-[#191c1f]">Last name</label>
                            <input
                                id="last_name"
                                name="last_name"
                                type="text"
                                maxlength="50"
                                autocomplete="family-name"
                                value="<?= htmlspecialchars($lastName, ENT_QUOTES, 'UTF-8') ?>"
                                aria-describedby="last_name-error"
                                aria-invalid="<?= $fieldError($profileErrors, 'last_name') !== '' ? 'true' : 'false' ?>"
                                class="mt-2 h-11 w-full rounded-lg border <?= $fieldBorder($profileErrors, 'last_name') ?> bg-white px-3 text-sm text-[#27313a] outline-none transition focus:ring-2"
                            >
                            <p
                                id="last_name-error"
                                class="mt-2 <?= $fieldError($profileErrors, 'last_name') === '' ? 'hidden' : 'block' ?> text-sm leading-5 text-[#ba1a1a]"
                                aria-live="polite"
                            >
                                <?= htmlspecialchars($fieldError($profileErrors, 'last_name'), ENT_QUOTES, 'UTF-8') ?>
                            </p>
                        </div>
                    </div>

                    <div class="mt-5">
                        <label for="username" class="text-sm font-semibold text-[#191c1f]">Username</label>
                        <input
                            id="username"
                            name="username"
                            type="text"
                            maxlength="50"
                            autocomplete="username"
                            value="<?= htmlspecialchars($username, ENT_QUOTES, 'UTF-8') ?>"
                            aria-describedby="username-help username-error"
                            aria-invalid="<?= $fieldError($profileErrors, 'username') !== '' ? 'true' : 'false' ?>"
                            class="mt-2 h-11 w-full rounded-lg border <?= $fieldBorder($profileErrors, 'username') ?> bg-white px-3 text-sm text-[#27313a] outline-none transition focus:ring-2"
                        >
                        <p id="username-help" class="mt-2 text-xs leading-5 text-[#444748]">
                            Letters, numbers, underscores, dots, and hyphens only.
                        </p>
                        <p
                            id="username-error"
                            class="mt-1 <?= $fieldError($profileErrors, 'username') === '' ? 'hidden' : 'block' ?> text-sm leading-5 text-[#ba1a1a]"
                            aria-live="polite"
                        >
                            <?= htmlspecialchars($fieldError($profileErrors, 'username'), ENT_QUOTES, 'UTF-8') ?>
                        </p>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <button type="submit" class="inline-flex h-10 items-center justify-center rounded-lg bg-[#315f90] px-5 text-sm font-bold text-white transition hover:bg-[#244f7a] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#315f90]">
                            Save profile
                        </button>
                    </div>
                </form>
            </section>

            <section class="rounded-xl border border-[#c4c7c7] bg-white p-5 sm:p-6" aria-labelledby="avatar-heading">
                <h2 id="avatar-heading" class="text-xl font-semibold leading-7 text-[#191c1f]">Avatar</h2>
                <p class="mt-1 text-sm leading-6 text-[#444748]">Choose a JPG, PNG, GIF, or WebP image up to 2 MB.</p>

                <form action="<?= BASE_URL ?>/preferences/avatar" method="post" enctype="multipart/form-data" class="mt-6" novalidate>
                    <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">

                    <?php if ($fieldError($avatarErrors, 'general') !== ''): ?>
                    <div class="mb-5 rounded-xl border border-[#ba1a1a] bg-[#ba1a1a]/5 px-4 py-3 text-sm leading-6 text-[#8f1111]" role="alert">
                        <?= htmlspecialchars($fieldError($avatarErrors, 'general'), ENT_QUOTES, 'UTF-8') ?>
                    </div>
                    <?php endif; ?>

                    <div class="flex flex-col gap-5 sm:flex-row sm:items-center">
                        <?php if ($authAvatarUrl !== null): ?>
                            <img
                                src="<?= htmlspecialchars($authAvatarUrl, ENT_QUOTES, 'UTF-8') ?>"
                                alt="Current avatar"
                                class="size-24 shrink-0 rounded-full border border-[#c4c7c7] object-cover"
                            >
                        <?php else: ?>
                            <span
                                class="flex size-24 shrink-0 items-center justify-center rounded-full border border-[#c4c7c7] bg-[#1E3A8A] text-2xl font-semibold text-white"
                                aria-label="Current avatar initials"
                            >
                                <?= htmlspecialchars($authAvatarInitial, ENT_QUOTES, 'UTF-8') ?>
                            </span>
                        <?php endif; ?>
                        <div class="min-w-0 flex-1">
                            <label for="avatar" class="text-sm font-semibold text-[#191c1f]">Avatar image</label>
                            <input
                                id="avatar"
                                name="avatar"
                                type="file"
                                accept=".jpg,.jpeg,.png,.gif,.webp,image/jpeg,image/png,image/gif,image/webp"
                                aria-describedby="avatar-error"
                                aria-invalid="<?= $fieldError($avatarErrors, 'avatar') !== '' ? 'true' : 'false' ?>"
                                class="mt-2 block w-full rounded-lg border <?= $fieldBorder($avatarErrors, 'avatar') ?> bg-white text-sm text-[#444748] file:mr-4 file:border-0 file:bg-[#eef2f6] file:px-4 file:py-3 file:text-sm file:font-semibold file:text-[#27313a] hover:file:bg-[#e2e8ef]"
                            >
                            <p
                                id="avatar-error"
                                class="mt-2 <?= $fieldError($avatarErrors, 'avatar') === '' ? 'hidden' : 'block' ?> text-sm leading-5 text-[#ba1a1a]"
                                aria-live="polite"
                            >
                                <?= htmlspecialchars($fieldError($avatarErrors, 'avatar'), ENT_QUOTES, 'UTF-8') ?>
                            </p>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <button type="submit" class="inline-flex h-10 items-center justify-center rounded-lg bg-[#315f90] px-5 text-sm font-bold text-white transition hover:bg-[#244f7a] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#315f90]">
                            Update avatar
                        </button>
                    </div>
                </form>
            </section>

            <?php if ($showModulePreferences): ?>
            <section class="rounded-xl border border-[#c4c7c7] bg-white p-5 sm:p-6 lg:col-span-2"
                aria-labelledby="modules-heading">
                <div class="flex flex-col justify-between gap-5 sm:flex-row sm:items-start">
                    <div>
                        <h2 id="modules-heading" class="text-xl font-semibold leading-7 text-[#191c1f]">My Modules</h2>
                        <p class="mt-1 text-sm leading-6 text-[#444748]">
                            These modules are displayed on your dashboard.
                        </p>
                    </div>
                    <a href="<?= BASE_URL ?>/preferences/modules"
                        class="inline-flex min-h-10 shrink-0 items-center justify-center rounded-lg bg-[#315f90] px-5 text-sm font-bold text-white transition hover:bg-[#244f7a] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#315f90]">
                        Manage modules
                    </a>
                </div>

                <?php if (!empty($selectedModules)): ?>
                <ul class="mt-5 grid gap-3 sm:grid-cols-2 lg:grid-cols-3" aria-label="Selected modules">
                    <?php foreach ($selectedModules as $module): ?>
                    <li class="min-w-0 rounded-lg border border-[#d6deea] bg-[#f8faff] px-4 py-3">
                        <span class="block font-mono text-xs font-semibold tracking-[0.05em] text-[#315f90]">
                            <?= htmlspecialchars((string) ($module['code'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
                        </span>
                        <span class="mt-1 block truncate text-sm font-semibold text-[#191c1f]">
                            <?= htmlspecialchars((string) ($module['name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
                        </span>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <?php else: ?>
                <p class="mt-5 rounded-lg border border-dashed border-[#c4c7c7] p-4 text-sm leading-6 text-[#444748]">
                    No modules are currently available.
                </p>
                <?php endif; ?>
            </section>
            <?php endif; ?>

            <section class="rounded-xl border border-[#c4c7c7] bg-white p-5 sm:p-6 lg:col-span-2" aria-labelledby="password-heading">
                <div class="max-w-2xl">
                    <h2 id="password-heading" class="text-xl font-semibold leading-7 text-[#191c1f]">Password</h2>
                    <p class="mt-1 text-sm leading-6 text-[#444748]">Use your current password to choose a new one.</p>

                    <form action="<?= BASE_URL ?>/preferences/password" method="post" class="mt-6" novalidate>
                        <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">

                        <?php if ($fieldError($passwordErrors, 'general') !== ''): ?>
                        <div class="mb-5 rounded-xl border border-[#ba1a1a] bg-[#ba1a1a]/5 px-4 py-3 text-sm leading-6 text-[#8f1111]" role="alert">
                            <?= htmlspecialchars($fieldError($passwordErrors, 'general'), ENT_QUOTES, 'UTF-8') ?>
                        </div>
                        <?php endif; ?>

                        <div>
                            <label for="current_password" class="text-sm font-semibold text-[#191c1f]">Current password</label>
                            <input
                                id="current_password"
                                name="current_password"
                                type="password"
                                autocomplete="current-password"
                                aria-describedby="current_password-error"
                                aria-invalid="<?= $fieldError($passwordErrors, 'current_password') !== '' ? 'true' : 'false' ?>"
                                class="mt-2 h-11 w-full rounded-lg border <?= $fieldBorder($passwordErrors, 'current_password') ?> bg-white px-3 text-sm text-[#27313a] outline-none transition focus:ring-2"
                            >
                            <p
                                id="current_password-error"
                                class="mt-2 <?= $fieldError($passwordErrors, 'current_password') === '' ? 'hidden' : 'block' ?> text-sm leading-5 text-[#ba1a1a]"
                                aria-live="polite"
                            >
                                <?= htmlspecialchars($fieldError($passwordErrors, 'current_password'), ENT_QUOTES, 'UTF-8') ?>
                            </p>
                        </div>

                        <div class="mt-5 grid gap-5 sm:grid-cols-2">
                            <div>
                                <label for="new_password" class="text-sm font-semibold text-[#191c1f]">New password</label>
                                <input
                                    id="new_password"
                                    name="new_password"
                                    type="password"
                                    minlength="8"
                                    maxlength="128"
                                    autocomplete="new-password"
                                    aria-describedby="new_password-error"
                                    aria-invalid="<?= $fieldError($passwordErrors, 'new_password') !== '' ? 'true' : 'false' ?>"
                                    class="mt-2 h-11 w-full rounded-lg border <?= $fieldBorder($passwordErrors, 'new_password') ?> bg-white px-3 text-sm text-[#27313a] outline-none transition focus:ring-2"
                                >
                                <p
                                    id="new_password-error"
                                    class="mt-2 <?= $fieldError($passwordErrors, 'new_password') === '' ? 'hidden' : 'block' ?> text-sm leading-5 text-[#ba1a1a]"
                                    aria-live="polite"
                                >
                                    <?= htmlspecialchars($fieldError($passwordErrors, 'new_password'), ENT_QUOTES, 'UTF-8') ?>
                                </p>
                            </div>

                            <div>
                                <label for="confirm_password" class="text-sm font-semibold text-[#191c1f]">Confirm new password</label>
                                <input
                                    id="confirm_password"
                                    name="confirm_password"
                                    type="password"
                                    maxlength="128"
                                    autocomplete="new-password"
                                    aria-describedby="confirm_password-error"
                                    aria-invalid="<?= $fieldError($passwordErrors, 'confirm_password') !== '' ? 'true' : 'false' ?>"
                                    class="mt-2 h-11 w-full rounded-lg border <?= $fieldBorder($passwordErrors, 'confirm_password') ?> bg-white px-3 text-sm text-[#27313a] outline-none transition focus:ring-2"
                                >
                                <p
                                    id="confirm_password-error"
                                    class="mt-2 <?= $fieldError($passwordErrors, 'confirm_password') === '' ? 'hidden' : 'block' ?> text-sm leading-5 text-[#ba1a1a]"
                                    aria-live="polite"
                                >
                                    <?= htmlspecialchars($fieldError($passwordErrors, 'confirm_password'), ENT_QUOTES, 'UTF-8') ?>
                                </p>
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end">
                            <button type="submit" class="inline-flex h-10 items-center justify-center rounded-lg bg-[#315f90] px-5 text-sm font-bold text-white transition hover:bg-[#244f7a] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#315f90]">
                                Change password
                            </button>
                        </div>
                    </form>
                </div>
            </section>
        </div>
    </div>
</section>
