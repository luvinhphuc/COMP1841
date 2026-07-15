<?php
/**
 * @var string $mode
 * @var string $formAction
 * @var array $user
 * @var array $errors
 * @var array $roles
 * @var int $currentUserId
 * @var string $csrfToken
 */

$isCreate = $mode === 'create';
$fieldError = static function (string $field) use ($errors) {
    return trim((string) ($errors[$field] ?? ''));
};
$inputClass = static function (string $field) use ($fieldError) {
    return $fieldError($field) !== '' ? 'border-[#b3261e]' : 'border-[#c4c7c7]';
};
?>

<section class="min-h-screen bg-[#f6f7fb] px-5 py-8 text-[#172033] sm:px-8 lg:px-12">
    <div class="mx-auto flex max-w-4xl flex-col gap-8">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.14em] text-[#0b57d0]">Administration</p>
            <h1 class="mt-2 text-3xl font-bold tracking-tight">
                <?= $isCreate ? 'Create User' : 'Edit User' ?>
            </h1>
        </div>

        <?php require ROOT_PATH . '/app/Views/admin/partials/navigation.php'; ?>

        <form action="<?= htmlspecialchars($formAction, ENT_QUOTES, 'UTF-8') ?>" method="post"
            class="grid gap-5 rounded-xl border border-[#dfe5ef] bg-white p-6 shadow-sm sm:grid-cols-2" novalidate>
            <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">

            <?php if ($fieldError('general') !== ''): ?>
            <div class="rounded-lg border border-[#b3261e] bg-[#fff2f1] px-4 py-3 text-sm text-[#8c1d18] sm:col-span-2" role="alert">
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
                    type="<?= $field === 'email' ? 'email' : 'text' ?>"
                    required maxlength="<?= $maxlength ?>" autocomplete="<?= htmlspecialchars($autocomplete, ENT_QUOTES, 'UTF-8') ?>"
                    value="<?= htmlspecialchars((string) ($user[$field] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                    aria-invalid="<?= $fieldError($field) !== '' ? 'true' : 'false' ?>"
                    class="h-11 rounded-lg border <?= $inputClass($field) ?> px-3 font-normal focus:border-black focus:outline-none">
                <?php if ($fieldError($field) !== ''): ?>
                <span class="font-normal text-[#b3261e]" role="alert">
                    <?= htmlspecialchars($fieldError($field), ENT_QUOTES, 'UTF-8') ?>
                </span>
                <?php endif; ?>
            </label>
            <?php endforeach; ?>

            <?php if ($isCreate): ?>
            <?php foreach (['password' => 'Password', 'confirm_password' => 'Confirm password'] as $field => $label): ?>
            <label class="grid gap-2 text-sm font-semibold">
                <?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?>
                <input name="<?= htmlspecialchars($field, ENT_QUOTES, 'UTF-8') ?>" type="password"
                    required maxlength="128" autocomplete="new-password"
                    aria-invalid="<?= $fieldError($field) !== '' ? 'true' : 'false' ?>"
                    class="h-11 rounded-lg border <?= $inputClass($field) ?> px-3 font-normal focus:border-black focus:outline-none">
                <?php if ($fieldError($field) !== ''): ?>
                <span class="font-normal text-[#b3261e]" role="alert">
                    <?= htmlspecialchars($fieldError($field), ENT_QUOTES, 'UTF-8') ?>
                </span>
                <?php endif; ?>
            </label>
            <?php endforeach; ?>
            <?php endif; ?>

            <label class="grid gap-2 text-sm font-semibold sm:col-span-2">
                Role
                <select name="role"
                    class="h-11 rounded-lg border <?= $inputClass('role') ?> bg-white px-3 font-normal focus:border-black focus:outline-none"
                    <?= !$isCreate && (int) $user['id'] === (int) $currentUserId ? 'disabled' : '' ?>>
                    <?php foreach ($roles as $role): ?>
                    <option value="<?= htmlspecialchars($role, ENT_QUOTES, 'UTF-8') ?>"
                        <?= ($user['role'] ?? '') === $role ? 'selected' : '' ?>>
                        <?= htmlspecialchars(ucfirst($role), ENT_QUOTES, 'UTF-8') ?>
                    </option>
                    <?php endforeach; ?>
                </select>
                <?php if (!$isCreate && (int) $user['id'] === (int) $currentUserId): ?>
                <input type="hidden" name="role" value="admin">
                <span class="font-normal text-[#444748]">You cannot remove your own admin role.</span>
                <?php elseif ($fieldError('role') !== ''): ?>
                <span class="font-normal text-[#b3261e]" role="alert">
                    <?= htmlspecialchars($fieldError('role'), ENT_QUOTES, 'UTF-8') ?>
                </span>
                <?php endif; ?>
            </label>

            <div class="flex gap-3 sm:col-span-2">
                <button type="submit" class="rounded-lg bg-[#0b57d0] px-5 py-3 text-sm font-semibold text-white hover:bg-[#0847ad]">
                    <?= $isCreate ? 'Create user' : 'Save changes' ?>
                </button>
                <a href="<?= BASE_URL ?>/admin/users" class="rounded-lg border border-[#c4c7c7] bg-white px-5 py-3 text-sm font-semibold hover:border-black">Cancel</a>
            </div>
        </form>
    </div>
</section>
