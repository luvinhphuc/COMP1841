<?php
/**
 * Variables passed from AdminController::index()
 *
 * @var string $adminSection
 * @var int $userCount
 * @var int $moduleCount
 * @var array $discussionCounts
 */
?>

<section class="ui-page">
    <div class="ui-container flex flex-col gap-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between" data-motion-intro>
            <div>
                <p class="ui-eyebrow">Administration</p>
                <h1 class="ui-page-title">Admin overview</h1>
                <p class="ui-page-description mt-2 text-sm">Platform statistics and quick management shortcuts.</p>
            </div>
            <a href="<?= BASE_URL ?>/discussions/create" class="ui-button ui-button-primary">
                + New Discussion
            </a>
        </div>

        <?php require ROOT_PATH . '/app/Views/admin/partials/navigation.php'; ?>

        <?php
        $stats = [
            ['label' => 'Total users', 'value' => $userCount, 'note' => 'Registered accounts', 'url' => BASE_URL . '/admin/users', 'color' => 'text-brand-royal'],
            ['label' => 'Total modules', 'value' => $moduleCount, 'note' => 'Course modules', 'url' => BASE_URL . '/admin/modules', 'color' => 'text-brand-royal'],
            ['label' => 'Active discussions', 'value' => $discussionCounts['total'] ?? 0, 'note' => 'Visible discussions', 'url' => BASE_URL . '/admin/posts', 'color' => 'text-ui-success'],
            ['label' => 'Open discussions', 'value' => $discussionCounts['open'] ?? 0, 'note' => 'Awaiting solutions', 'url' => BASE_URL . '/admin/posts?status=open', 'color' => 'text-ui-warning'],
            ['label' => 'Solved discussions', 'value' => $discussionCounts['solved'] ?? 0, 'note' => 'Completed discussions', 'url' => BASE_URL . '/admin/posts?status=solved', 'color' => 'text-ui-success'],
        ];
        ?>
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-5" data-motion-list>
            <?php foreach ($stats as $stat): ?>
            <a href="<?= htmlspecialchars($stat['url'], ENT_QUOTES, 'UTF-8') ?>"
                class="ui-card ui-card-interactive group p-5" data-motion-item>
                <div class="flex items-start justify-between gap-3">
                    <span
                        class="text-xs font-bold uppercase tracking-[0.08em] text-ui-muted"><?= htmlspecialchars($stat['label'], ENT_QUOTES, 'UTF-8') ?></span>
                    <span
                        class="flex size-7 items-center justify-center rounded-lg bg-ui-neutral-soft <?= $stat['color'] ?>"
                        aria-hidden="true">↗</span>
                </div>
                <strong
                    class="mt-4 block text-3xl font-bold tracking-tight text-ui-ink"><?= number_format((int) $stat['value']) ?></strong>
                <span
                    class="mt-2 block text-xs text-ui-muted"><?= htmlspecialchars($stat['note'], ENT_QUOTES, 'UTF-8') ?></span>
            </a>
            <?php endforeach; ?>
        </div>

        <section class="ui-card p-5" data-motion-reveal>
            <h2 class="text-base font-bold text-ui-ink">Quick actions</h2>
            <p class="mt-1 text-xs text-ui-muted">Open the area you need to manage.</p>
            <div class="mt-4 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                <a href="<?= BASE_URL ?>/admin/users" class="ui-button ui-button-secondary p-4">Manage users</a>
                <a href="<?= BASE_URL ?>/admin/modules" class="ui-button ui-button-secondary p-4">Add module</a>
                <a href="<?= BASE_URL ?>/admin/posts" class="ui-button ui-button-secondary p-4">Review discussions</a>
                <a href="<?= BASE_URL ?>/profile" class="ui-button ui-button-secondary p-4">My profile</a>
            </div>
        </section>
    </div>
</section>