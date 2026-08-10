<?php
/**
 * Variables passed from PostController::create()
 *
 * @var array $errors
 * @var array $old
 * @var array $modules
 * @var string $formAction
 * @var string $formTitle
 * @var string $submitLabel
 * @var string $cancelUrl
 * @var string $csrfToken
 */

$fieldError = static function (array $errors, string $field) {
    return trim((string) ($errors[$field] ?? ''));
};

$fieldBorder = static function (array $errors, string $field) {
    return trim((string) ($errors[$field] ?? '')) !== ''
        ? 'border-ui-danger'
        : 'border-ui-border-strong';
};

$formTitleValue = (string) ($old['title'] ?? '');
$formModuleId = (string) ($old['module_id'] ?? '');
$pageTitle = $formTitle;
?>

<section class="ui-page">
    <div class="ui-container-narrow flex max-w-[980px] flex-col gap-6">
        <div class="flex flex-wrap items-start justify-between gap-4" data-motion-intro>
            <div>
                <p class="ui-eyebrow">Coursework Forum</p>
                <h1 class="ui-page-title">
                    <?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?>
                </h1>

                <div class="mt-4">
                    <label for="module_id" class="sr-only">Module</label>
                    <select id="module_id" name="module_id" form="discussion-create-form"
                        aria-describedby="module_id-error"
                        aria-invalid="<?= $fieldError($errors, 'module_id') !== '' ? 'true' : 'false' ?>"
                        class="ui-input mt-4 h-11 max-w-full rounded-lg pr-9 font-semibold <?= $fieldBorder($errors, 'module_id') ?>">
                        <option value="">Select module</option>
                        <?php foreach ($modules as $module): ?>
                        <?php
                            $moduleId = (string) ($module['id'] ?? '');
                            $moduleCode = (string) ($module['code'] ?? 'MODULE');
                            $moduleName = (string) ($module['name'] ?? 'Module');
                        ?>
                        <option value="<?= htmlspecialchars($moduleId, ENT_QUOTES, 'UTF-8') ?>"
                            <?= $formModuleId === $moduleId ? 'selected' : '' ?>>
                            <?= htmlspecialchars($moduleCode . ' - ' . $moduleName, ENT_QUOTES, 'UTF-8') ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                    <p id="module_id-error"
                        class="ui-field-error <?= $fieldError($errors, 'module_id') === '' ? 'hidden' : 'block' ?>"
                        aria-live="polite">
                        <?= htmlspecialchars($fieldError($errors, 'module_id'), ENT_QUOTES, 'UTF-8') ?>
                    </p>
                </div>
            </div>

            <a href="<?= htmlspecialchars($cancelUrl, ENT_QUOTES, 'UTF-8') ?>" class="ui-button ui-button-ghost">
                Back to posts
            </a>
        </div>

        <form id="discussion-create-form" action="<?= htmlspecialchars($formAction, ENT_QUOTES, 'UTF-8') ?>"
            method="post" enctype="multipart/form-data" class="box-border" data-motion-reveal novalidate>
            <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">

            <?php if (!empty($errors['general'])): ?>
            <div class="ui-alert ui-alert-error mb-5" role="alert">
                <?= htmlspecialchars($errors['general'], ENT_QUOTES, 'UTF-8') ?>
            </div>
            <?php endif; ?>

            <div class="grid gap-5">
                <div>
                    <label for="title" class="sr-only">Title</label>
                    <input id="title" name="title" type="text"
                        value="<?= htmlspecialchars($formTitleValue, ENT_QUOTES, 'UTF-8') ?>"
                        aria-describedby="title-error"
                        aria-invalid="<?= $fieldError($errors, 'title') !== '' ? 'true' : 'false' ?>"
                        class="ui-input h-14 min-w-0 px-5 text-base <?= $fieldBorder($errors, 'title') ?>"
                        placeholder="Title*">
                    <p id="title-error"
                        class="ui-field-error <?= $fieldError($errors, 'title') === '' ? 'hidden' : 'block' ?>"
                        aria-live="polite">
                        <?= htmlspecialchars($fieldError($errors, 'title'), ENT_QUOTES, 'UTF-8') ?>
                    </p>
                </div>

                <?php
                    $contentInputConfig = [
                        'variant' => 'discussion',
                        'id' => 'content',
                        'value' => (string) ($old['content'] ?? ''),
                        'errors' => [
                            'content' => $fieldError($errors, 'content'),
                            'attachments' => $fieldError($errors, 'attachments'),
                        ],
                    ];
                    require ROOT_PATH . '/app/Views/discussions/partials/content_input.php';
                ?>
            </div>

            <div class="mt-5 flex justify-end gap-3">
                <a href="<?= htmlspecialchars($cancelUrl, ENT_QUOTES, 'UTF-8') ?>"
                    class="ui-button ui-button-secondary">
                    Cancel
                </a>
                <button type="submit" class="ui-button ui-button-primary">
                    <?= htmlspecialchars($submitLabel, ENT_QUOTES, 'UTF-8') ?>
                </button>
            </div>
        </form>
    </div>
</section>
