<?php
/**
 * Variables passed from Admin\ModuleController::edit()
 *
 * @var string $adminSection
 * @var array $module
 * @var string $csrfToken
 */
?>

<section class="ui-page">
    <div class="ui-container-narrow flex max-w-4xl flex-col gap-8">
        <div data-motion-intro>
            <p class="ui-eyebrow">Administration</p>
            <h1 class="ui-page-title">Edit module</h1>
        </div>
        <?php require ROOT_PATH . '/app/Views/admin/partials/navigation.php'; ?>

        <form action="<?= BASE_URL ?>/admin/modules/update/<?= (int) $module['id'] ?>" method="post"
            class="ui-card grid gap-5 p-6" data-motion-reveal>
            <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
            <label class="grid gap-2 text-sm font-semibold">Code
                <input name="code" required maxlength="20"
                    value="<?= htmlspecialchars($module['code'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    class="ui-input font-normal">
            </label>
            <label class="grid gap-2 text-sm font-semibold">Name
                <input name="name" required maxlength="150"
                    value="<?= htmlspecialchars($module['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    class="ui-input font-normal">
            </label>
            <label class="grid gap-2 text-sm font-semibold">Description
                <textarea name="description" rows="5"
                    class="ui-input resize-y font-normal"><?= htmlspecialchars($module['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
            </label>
            <div class="flex gap-3">
                <button type="submit" class="ui-button ui-button-primary">Save changes</button>
                <a href="<?= BASE_URL ?>/admin/modules" class="ui-button ui-button-secondary">Cancel</a>
            </div>
        </form>
    </div>
</section>