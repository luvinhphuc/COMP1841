<?php
/**
 * Variables passed from Admin\UserController::edit()
 *
 * @var string $adminSection
 * @var string $formAction
 * @var array $targetUserData
 * @var array $targetUserErrors
 * @var array $roles
 * @var int $authUserId
 * @var string $csrfToken
 */

$fieldError = static function (string $field) use ($targetUserErrors) {
    return trim((string) ($targetUserErrors[$field] ?? ''));
};
$inputClass = static function (string $field) use ($fieldError) {
    return $fieldError($field) !== '' ? 'border-ui-danger' : 'border-ui-border-strong';
};
?>

<section class="ui-page">
    <div class="ui-container-narrow flex max-w-4xl flex-col gap-8">
        <div data-motion-intro>
            <p class="ui-eyebrow">Administration</p>
            <h1 class="ui-page-title">Edit user</h1>
        </div>

        <?php require ROOT_PATH . '/app/Views/admin/partials/navigation.php'; ?>

        <form action="<?= htmlspecialchars($formAction, ENT_QUOTES, 'UTF-8') ?>" method="post"
            class="ui-card grid gap-5 p-6 sm:grid-cols-2" data-motion-reveal novalidate>
            <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">

            <?php if ($fieldError('general') !== ''): ?>
            <div class="ui-alert ui-alert-error sm:col-span-2" role="alert">
                <?= htmlspecialchars($fieldError('general'), ENT_QUOTES, 'UTF-8') ?>
            </div>
            <?php endif; ?>

            <?php foreach ([
                'first_name' => ['First name', 50, 'given-name'],
                'last_name' => ['Last name', 50, 'family-name'],
                'username' => ['Username', 75, 'username'],
                'email' => ['Email', 150, 'email'],
            ] as $field => [$label, $maxlength, $autocomplete]): ?>
            <label class="grid gap-2 text-sm font-semibold">
                <?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?>
                <input name="<?= htmlspecialchars($field, ENT_QUOTES, 'UTF-8') ?>"
                    type="<?= $field === 'email' ? 'email' : 'text' ?>" required maxlength="<?= $maxlength ?>"
                    autocomplete="<?= htmlspecialchars($autocomplete, ENT_QUOTES, 'UTF-8') ?>"
                    value="<?= htmlspecialchars((string) ($targetUserData[$field] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                    aria-invalid="<?= $fieldError($field) !== '' ? 'true' : 'false' ?>"
                    class="ui-input <?= $inputClass($field) ?> font-normal">
                <?php if ($fieldError($field) !== ''): ?>
                <span class="font-normal text-ui-danger" role="alert">
                    <?= htmlspecialchars($fieldError($field), ENT_QUOTES, 'UTF-8') ?>
                </span>
                <?php endif; ?>
            </label>
            <?php endforeach; ?>

            <label class="grid gap-2 text-sm font-semibold sm:col-span-2">
                Role
                <select name="role" class="ui-input <?= $inputClass('role') ?> font-normal"
                    <?= (int) $targetUserData['id'] === (int) $authUserId ? 'disabled' : '' ?>>
                    <?php foreach ($roles as $role): ?>
                    <option value="<?= htmlspecialchars($role, ENT_QUOTES, 'UTF-8') ?>"
                        <?= ($targetUserData['role'] ?? '') === $role ? 'selected' : '' ?>>
                        <?= htmlspecialchars(ucfirst($role), ENT_QUOTES, 'UTF-8') ?>
                    </option>
                    <?php endforeach; ?>
                </select>
                <?php if ((int) $targetUserData['id'] === (int) $authUserId): ?>
                <input type="hidden" name="role" value="admin">
                <span class="font-normal text-ui-text">You cannot remove your own admin role.</span>
                <?php elseif ($fieldError('role') !== ''): ?>
                <span class="font-normal text-ui-danger" role="alert">
                    <?= htmlspecialchars($fieldError('role'), ENT_QUOTES, 'UTF-8') ?>
                </span>
                <?php endif; ?>
            </label>

            <div class="flex gap-3 sm:col-span-2">
                <button type="submit" class="ui-button ui-button-primary">
                    Save changes
                </button>
                <a href="<?= BASE_URL ?>/admin/users" class="ui-button ui-button-secondary">Cancel</a>
            </div>
        </form>
    </div>
</section>