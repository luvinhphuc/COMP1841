<?php
/**
 * @var array $modules
 * @var array $selectedModuleIds
 * @var array $moduleErrors
 * @var string $csrfToken
 */

$selectedLookup = array_fill_keys(array_map('intval', $selectedModuleIds), true);
$generalError = trim((string) ($moduleErrors['general'] ?? ''));
$selectionError = trim((string) ($moduleErrors['module_ids'] ?? ''));
?>

<section class="min-h-[calc(100vh-80px)] bg-[#f7f9fd] px-5 py-10 text-[#191c1f] sm:px-8 lg:px-16 lg:py-14">
    <div class="mx-auto max-w-5xl">
        <div class="max-w-3xl">
            <p class="text-sm font-semibold uppercase tracking-[0.12em] text-[#315f90]">Getting started</p>
            <h1 class="mt-3 text-3xl font-semibold leading-tight text-black sm:text-4xl">Choose your modules</h1>
            <p class="mt-4 text-base leading-7 text-[#444748]">
                Select the modules you are currently studying. These modules will appear on your dashboard,
                and you can update them later in Preferences.
            </p>
        </div>

        <form action="<?= BASE_URL ?>/onboarding/modules/save" method="post" class="mt-8" novalidate>
            <input type="hidden" name="_csrf_token"
                value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">

            <?php if ($generalError !== ''): ?>
            <div class="mb-5 rounded-xl border border-[#ba1a1a] bg-[#ba1a1a]/5 px-4 py-3 text-sm leading-6 text-[#8f1111]"
                role="alert">
                <?= htmlspecialchars($generalError, ENT_QUOTES, 'UTF-8') ?>
            </div>
            <?php endif; ?>

            <fieldset aria-describedby="module-selection-help module-selection-error">
                <legend class="text-xl font-semibold leading-7">Available modules</legend>
                <p id="module-selection-help" class="mt-1 text-sm leading-6 text-[#444748]">
                    Choose at least one module to continue.
                </p>

                <?php if ($selectionError !== ''): ?>
                <p id="module-selection-error" class="mt-3 text-sm font-semibold text-[#ba1a1a]" role="alert">
                    <?= htmlspecialchars($selectionError, ENT_QUOTES, 'UTF-8') ?>
                </p>
                <?php endif; ?>

                <?php if (!empty($modules)): ?>
                <div class="mt-5 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <?php foreach ($modules as $module): ?>
                    <?php
                    $moduleId = (int) ($module['id'] ?? 0);
                    $moduleCode = trim((string) ($module['code'] ?? ''));
                    $moduleName = trim((string) ($module['name'] ?? ''));
                    $description = trim((string) ($module['description'] ?? ''));
                    $checked = isset($selectedLookup[$moduleId]);

                    if ($moduleId <= 0 || $moduleCode === '') {
                        continue;
                    }
                    ?>
                    <label class="group relative flex min-h-48 cursor-pointer flex-col rounded-xl border border-[#c4c7c7] bg-white p-5 transition hover:border-[#315f90] has-[:checked]:border-[#315f90] has-[:checked]:bg-[#eef4ff] has-[:focus-visible]:outline-2 has-[:focus-visible]:outline-offset-2 has-[:focus-visible]:outline-[#315f90]">
                        <span class="flex items-start justify-between gap-4">
                            <span class="rounded bg-[#d6e3ff] px-2 py-1 font-mono text-xs font-semibold tracking-[0.05em] text-[#001b3d]">
                                <?= htmlspecialchars($moduleCode, ENT_QUOTES, 'UTF-8') ?>
                            </span>
                            <input type="checkbox" name="module_ids[]" value="<?= $moduleId ?>"
                                <?= $checked ? 'checked' : '' ?>
                                class="size-5 shrink-0 accent-[#315f90]">
                        </span>
                        <span class="mt-4 text-lg font-semibold leading-7 text-black">
                            <?= htmlspecialchars($moduleName !== '' ? $moduleName : $moduleCode, ENT_QUOTES, 'UTF-8') ?>
                        </span>
                        <?php if ($description !== ''): ?>
                        <span class="mt-2 text-sm leading-6 text-[#444748]">
                            <?= htmlspecialchars($description, ENT_QUOTES, 'UTF-8') ?>
                        </span>
                        <?php endif; ?>
                    </label>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <div class="mt-5 rounded-xl border border-dashed border-[#c4c7c7] bg-white p-6 text-sm leading-6 text-[#444748]">
                    No modules are available right now.
                </div>
                <?php endif; ?>
            </fieldset>

            <div class="mt-8 flex justify-end">
                <button type="submit"
                    class="inline-flex min-h-12 items-center justify-center rounded-lg bg-black px-6 text-sm font-semibold text-white transition hover:bg-[#2a2d2f] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-black">
                    Save and continue
                </button>
            </div>
        </form>
    </div>
</section>
