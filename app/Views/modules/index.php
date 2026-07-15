<?php
/**
 * Variables passed from ModulesController::index()
 *
 * @var array $modules
 */
?>

<section class="bg-[#f7f9fd] px-5 py-12 text-[#191c1f] sm:px-8 lg:px-16">
    <div class="mx-auto flex max-w-5xl flex-col gap-8">
        <div>
            <p class="text-sm font-semibold text-[#315f90]">Modules</p>
            <h1 class="mt-3 text-3xl font-semibold leading-tight text-black sm:text-4xl">
                Browse Modules
            </h1>
            <p class="mt-4 max-w-2xl text-base leading-7 text-[#444748]">
                Choose a module to view related discussions.
            </p>
        </div>

        <?php if (!empty($modules)): ?>
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <?php foreach ($modules as $module): ?>
                    <?php
                    $moduleCode = trim((string) ($module['code'] ?? ''));
                    $moduleName = trim((string) ($module['name'] ?? ''));

                    if ($moduleCode === '') {
                        continue;
                    }
                    ?>
                    <a href="<?= BASE_URL ?>/discussions?module=<?= rawurlencode($moduleCode) ?>"
                        class="group min-w-0 rounded-lg border border-[#c4c7c7] bg-white p-5 transition hover:border-black focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-black">
                        <span class="font-mono text-xs font-semibold tracking-[0.05em] text-[#001b3d]">
                            <?= htmlspecialchars($moduleCode, ENT_QUOTES, 'UTF-8') ?>
                        </span>
                        <h2 class="mt-3 break-words text-lg font-semibold leading-7 text-black" dir="auto">
                            <?= htmlspecialchars($moduleName !== '' ? $moduleName : $moduleCode, ENT_QUOTES, 'UTF-8') ?>
                        </h2>
                        <span class="mt-4 inline-flex text-sm font-semibold text-[#191c1f] group-hover:text-emerald-800">
                            View discussions
                        </span>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="rounded-lg border border-dashed border-[#c4c7c7] bg-white p-6 text-sm leading-6 text-[#444748]">
                No modules are available yet.
            </div>
        <?php endif; ?>
    </div>
</section>
