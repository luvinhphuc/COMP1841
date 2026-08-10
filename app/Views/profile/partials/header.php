<?php
/**
 * @var array{
 *     avatar_url: string|null,
 *     full_name: string,
 *     avatar_initial: string,
 *     username: string,
 *     role: string,
 *     member_since_raw: string,
 *     member_since: string
 * } $profileHeader
 */
?>

<header class="flex flex-col gap-5 sm:flex-row sm:items-center" aria-labelledby="profile-identity-heading"
    data-motion-intro>
    <?php if (($profileHeader['avatar_url'] ?? null) !== null): ?>
    <img src="<?= htmlspecialchars($profileHeader['avatar_url'], ENT_QUOTES, 'UTF-8') ?>"
        alt="<?= htmlspecialchars(($profileHeader['full_name'] ?? 'User') . ' avatar', ENT_QUOTES, 'UTF-8') ?>"
        class="size-24 shrink-0 rounded-full border border-ui-border-strong object-cover sm:size-28">
    <?php else: ?>
    <span
        class="flex size-24 shrink-0 items-center justify-center rounded-full border border-ui-border-strong bg-brand-royal text-2xl font-semibold text-white sm:size-28"
        aria-hidden="true">
        <?= htmlspecialchars($profileHeader['avatar_initial'] ?? 'S', ENT_QUOTES, 'UTF-8') ?>
    </span>
    <?php endif; ?>

    <div class="min-w-0">
        <p class="text-sm font-semibold text-brand-royal">
            @<?= htmlspecialchars($profileHeader['username'] ?? '', ENT_QUOTES, 'UTF-8') ?>
        </p>
        <div class="flex items-center">
            <?php
            $profileHeaderRole = strtolower((string)($profileHeader['role'] ?? 'student'));
            $profileHeaderRoleBadgeClass = match ($profileHeaderRole) {
                'admin' => 'ui-badge-danger',
                'tutor' => 'ui-badge-tutor',
                default => 'ui-badge-brand',
            };
            ?>
            <h1 id="profile-identity-heading"
                class="mt-1 break-words font-sans text-4xl font-bold leading-tight text-ui-ink sm:text-5xl">
                <?= htmlspecialchars($profileHeader['full_name'] ?? 'Student', ENT_QUOTES, 'UTF-8') ?>
            </h1>
            <span class="ui-badge ml-6 h-6 text-sm <?= $profileHeaderRoleBadgeClass ?>">
                <?= htmlspecialchars(ucfirst($profileHeaderRole), ENT_QUOTES, 'UTF-8') ?>
            </span>
        </div>
        <p class="mt-2 text-sm leading-6 text-ui-text">
            Member since
            <time datetime="<?= htmlspecialchars($profileHeader['member_since_raw'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                <?= htmlspecialchars($profileHeader['member_since'] ?? 'Unknown', ENT_QUOTES, 'UTF-8') ?>
            </time>
        </p>
    </div>
</header>