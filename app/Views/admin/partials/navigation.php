<?php
$adminNavigationLinks = [
    'overview' => ['label' => 'Overview', 'url' => BASE_URL . '/admin'],
    'users' => ['label' => 'Users', 'url' => BASE_URL . '/admin/users'],
    'modules' => ['label' => 'Modules', 'url' => BASE_URL . '/admin/modules'],
    'posts' => ['label' => 'Posts', 'url' => BASE_URL . '/admin/posts'],
];
?>

<nav class="flex flex-wrap gap-1 rounded-xl border border-[#dfe5ef] bg-white p-1 shadow-sm" aria-label="Admin navigation">
    <?php foreach ($adminNavigationLinks as $key => $link): ?>
        <a href="<?= htmlspecialchars($link['url'], ENT_QUOTES, 'UTF-8') ?>"
            class="rounded-lg px-4 py-2 text-sm font-semibold transition <?= ($adminSection ?? '') === $key ? 'bg-[#e8f0ff] text-[#0b57d0]' : 'text-[#667085] hover:bg-[#f5f7fb] hover:text-[#172033]' ?>"
            <?= ($adminSection ?? '') === $key ? 'aria-current="page"' : '' ?>>
            <?= htmlspecialchars($link['label'], ENT_QUOTES, 'UTF-8') ?>
        </a>
    <?php endforeach; ?>
</nav>
