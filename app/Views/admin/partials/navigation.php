<?php
/** @var string $adminSection */

$adminNavigationLinks = [
    'overview' => ['label' => 'Overview', 'url' => BASE_URL . '/admin'],
    'users' => ['label' => 'Users', 'url' => BASE_URL . '/admin/users'],
    'modules' => ['label' => 'Modules', 'url' => BASE_URL . '/admin/modules'],
    'posts' => ['label' => 'Posts', 'url' => BASE_URL . '/admin/posts'],
    'contacts' => ['label' => 'Contact Messages', 'url' => BASE_URL . '/admin/contacts'],
];
?>

<nav class="ui-card flex flex-wrap gap-1 p-1" aria-label="Admin navigation">
    <?php foreach ($adminNavigationLinks as $adminNavigationKey => $adminNavigationLink): ?>
    <a href="<?= htmlspecialchars($adminNavigationLink['url'], ENT_QUOTES, 'UTF-8') ?>"
        class="min-h-10 rounded-lg px-4 py-2 text-sm font-semibold transition <?= ($adminSection ?? '') === $adminNavigationKey ? 'bg-ui-brand-soft text-brand-royal' : 'text-ui-muted hover:bg-ui-canvas hover:text-ui-ink' ?>"
        <?= ($adminSection ?? '') === $adminNavigationKey ? 'aria-current="page"' : '' ?>>
        <?= htmlspecialchars($adminNavigationLink['label'], ENT_QUOTES, 'UTF-8') ?>
    </a>
    <?php endforeach; ?>
</nav>