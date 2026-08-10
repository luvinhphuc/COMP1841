<?php
/**
 * @var string $profileActiveTab
 */
$profileTabs = [
    ['key' => 'summary', 'label' => 'Summary', 'url' => BASE_URL . '/profile'],
    ['key' => 'questions', 'label' => 'My Questions', 'url' => BASE_URL . '/profile/questions'],
    ['key' => 'preferences', 'label' => 'Preferences', 'url' => BASE_URL . '/profile/preferences'],
];
?>

<nav class="overflow-x-auto border-b border-ui-border" aria-label="Profile sections" data-motion-reveal>
    <div class="flex min-w-max gap-1">
        <?php foreach ($profileTabs as $profileTab): ?>
        <?php $profileTabIsActive = ($profileActiveTab ?? '') === $profileTab['key']; ?>
        <a href="<?= htmlspecialchars($profileTab['url'], ENT_QUOTES, 'UTF-8') ?>"
            class="border-b-2 px-4 py-3 text-sm font-semibold transition <?= $profileTabIsActive ? 'border-brand-blue text-brand-royal' : 'border-transparent text-ui-text hover:border-ui-border-strong hover:text-ui-ink' ?>"
            <?= $profileTabIsActive ? 'aria-current="page"' : '' ?>>
            <?= htmlspecialchars($profileTab['label'], ENT_QUOTES, 'UTF-8') ?>
        </a>
        <?php endforeach; ?>
    </div>
</nav>