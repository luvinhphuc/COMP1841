<?php
/**
 * Variables passed from ModuleController::index()
 *
 * @var array $modules
 * @var string $searchQuery
 */
$moduleSearchValue = (string)($searchQuery ?? '');
$hasModuleSearch = trim($moduleSearchValue) !== '';
?>

<section class="ui-page">
    <div class="ui-container-narrow flex flex-col gap-8">
        <div data-motion-intro>
            <p class="ui-eyebrow">Modules</p>
            <h1 class="ui-page-title">
                Browse Modules
            </h1>
            <p class="ui-page-description">
                Choose a module to view related discussions.
            </p>

            <form action="<?= BASE_URL ?>/modules" method="get" role="search"
                class="mt-6 grid max-w-2xl gap-3 sm:grid-cols-[minmax(0,1fr)_auto] sm:items-start">
                <?php
                $searchBar = [
                    'id' => 'module-search',
                    'name' => 'q',
                    'value' => $moduleSearchValue,
                    'label' => 'Search modules',
                    'placeholder' => 'Search by module code or name',
                    'button_label' => 'Search',
                ];
                require ROOT_PATH . '/app/Views/components/search_bar.php';
                unset($searchBar);
                ?>
            </form>
        </div>

        <?php if (!empty($modules)): ?>
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3" data-motion-list>
            <?php foreach ($modules as $module): ?>
            <?php
                    $moduleCode = trim((string)($module['code'] ?? ''));
                    $moduleName = trim((string)($module['name'] ?? ''));

                    if ($moduleCode === '') {
                        continue;
                    }
                    ?>
            <a href="<?= BASE_URL ?>/discussions?module=<?= rawurlencode($moduleCode) ?>"
                class="ui-card ui-card-interactive group min-w-0 p-5" data-motion-item data-motion-lift>
                <span class="ui-badge ui-badge-brand rounded-md font-mono tracking-[0.05em]">
                    <?= htmlspecialchars($moduleCode, ENT_QUOTES, 'UTF-8') ?>
                </span>
                <h2 class="mt-3 break-words text-lg font-semibold leading-7 text-ui-ink" dir="auto">
                    <?= htmlspecialchars($moduleName !== '' ? $moduleName : $moduleCode, ENT_QUOTES, 'UTF-8') ?>
                </h2>
                <span class="mt-4 inline-flex text-sm font-semibold text-brand-royal group-hover:text-brand-blue">
                    View discussions
                </span>
            </a>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="ui-empty-state" data-motion-reveal>
            <?php if ($hasModuleSearch): ?>
            <p>No modules match “<?= htmlspecialchars($moduleSearchValue, ENT_QUOTES, 'UTF-8') ?>”.</p>
            <a href="<?= BASE_URL ?>/modules"
                class="mt-3 inline-flex font-semibold text-brand-royal underline decoration-ui-border-strong underline-offset-4 transition hover:text-brand-blue">
                Please look for other modules >
            </a>
            <?php else: ?>
            No modules are available yet.
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</section>