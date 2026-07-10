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
 * @var bool $showAttachmentField
 * @var string $csrfToken
 */

$fieldError = static function (array $errors, string $field) {
    return trim((string) ($errors[$field] ?? ''));
};

$fieldBorder = static function (array $errors, string $field) {
    return trim((string) ($errors[$field] ?? '')) !== ''
        ? 'border-[#ba1a1a] focus:border-[#ba1a1a] focus:ring-[#ba1a1a]/10'
        : 'border-[#c4c7c7] focus:border-[#315f90] focus:ring-[#315f90]/15';
};

$formTitleValue = (string) ($old['title'] ?? '');
$formModuleId = (string) ($old['module_id'] ?? '');
$pageTitle = $formTitle;
?>

<section class="box-border min-h-[calc(100vh-80px)] bg-white text-[#27313a]">
    <div class="mx-auto box-border flex max-w-245 flex-col gap-6 px-5 py-8 sm:px-8 lg:px-10">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold leading-8 text-[#27313a] sm:text-[26px]">
                    <?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?>
                </h1>

                <div class="mt-4">
                    <label for="module_id" class="sr-only">Module</label>
                    <select
                        id="module_id"
                        name="module_id"
                        form="discussion-create-form"
                        aria-describedby="module_id-error"
                        aria-invalid="<?= $fieldError($errors, 'module_id') !== '' ? 'true' : 'false' ?>"
                        class="h-10 max-w-full rounded-full border <?= $fieldBorder($errors, 'module_id') ?> bg-white px-4 pr-9 text-sm font-semibold text-[#27313a] outline-none transition focus:ring-2"
                    >
                        <option value="">Select module</option>
                        <?php foreach ($modules as $module): ?>
                        <?php
                            $moduleId = (string) ($module['id'] ?? '');
                            $moduleCode = (string) ($module['code'] ?? 'MODULE');
                            $moduleName = (string) ($module['name'] ?? 'Module');
                        ?>
                        <option
                            value="<?= htmlspecialchars($moduleId, ENT_QUOTES, 'UTF-8') ?>"
                            <?= $formModuleId === $moduleId ? 'selected' : '' ?>
                        >
                            <?= htmlspecialchars($moduleCode . ' - ' . $moduleName, ENT_QUOTES, 'UTF-8') ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                    <p
                        id="module_id-error"
                        class="mt-2 <?= $fieldError($errors, 'module_id') === '' ? 'hidden' : 'block' ?> text-sm leading-5 text-[#ba1a1a]"
                        aria-live="polite"
                    >
                        <?= htmlspecialchars($fieldError($errors, 'module_id'), ENT_QUOTES, 'UTF-8') ?>
                    </p>
                </div>
            </div>

            <a
                href="<?= htmlspecialchars($cancelUrl, ENT_QUOTES, 'UTF-8') ?>"
                class="inline-flex h-10 items-center rounded-full px-3 text-sm font-semibold text-[#315f90] transition hover:text-[#183f66] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#315f90]"
            >
                Back to posts
            </a>
        </div>

        <form
            id="discussion-create-form"
            action="<?= htmlspecialchars($formAction, ENT_QUOTES, 'UTF-8') ?>"
            method="post"
            enctype="multipart/form-data"
            class="box-border"
            novalidate
        >
            <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">

            <?php if (!empty($errors['general'])): ?>
            <div class="mb-5 rounded-xl border border-[#ba1a1a] bg-[#ba1a1a]/5 px-4 py-3 text-sm leading-6 text-[#8f1111]" role="alert">
                <?= htmlspecialchars($errors['general'], ENT_QUOTES, 'UTF-8') ?>
            </div>
            <?php endif; ?>

            <div class="grid gap-5">
                <div>
                    <label for="title" class="sr-only">Title</label>
                    <input
                        id="title"
                        name="title"
                        type="text"
                        value="<?= htmlspecialchars($formTitleValue, ENT_QUOTES, 'UTF-8') ?>"
                        aria-describedby="title-error"
                        aria-invalid="<?= $fieldError($errors, 'title') !== '' ? 'true' : 'false' ?>"
                        class="h-14 w-full min-w-0 rounded-[20px] border <?= $fieldBorder($errors, 'title') ?> bg-white px-5 text-base text-[#27313a] outline-none transition placeholder:text-[#667782] focus:ring-2"
                        placeholder="Title*"
                    >
                    <p
                        id="title-error"
                        class="mt-2 <?= $fieldError($errors, 'title') === '' ? 'hidden' : 'block' ?> text-sm leading-5 text-[#ba1a1a]"
                        aria-live="polite"
                    >
                        <?= htmlspecialchars($fieldError($errors, 'title'), ENT_QUOTES, 'UTF-8') ?>
                    </p>
                </div>

                <?php
                    $contentInputType = 'post';
                    $contentInputValue = (string) ($old['content'] ?? '');
                    $contentInputErrors = [
                        'content' => $fieldError($errors, 'content'),
                        'attachment' => $fieldError($errors, 'attachment'),
                    ];
                    require ROOT_PATH . '/app/Views/partials/content_input.php';
                ?>
            </div>

            <div class="mt-5 flex justify-end gap-3">
                <a
                    href="<?= htmlspecialchars($cancelUrl, ENT_QUOTES, 'UTF-8') ?>"
                    class="inline-flex h-10 items-center justify-center rounded-full bg-[#eef2f6] px-5 text-sm font-bold text-[#5b6872] transition hover:bg-[#e2e8ef] hover:text-[#27313a] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#315f90]"
                >
                    Cancel
                </a>
                <button
                    type="submit"
                    class="inline-flex h-10 items-center justify-center rounded-full bg-[#315f90] px-5 text-sm font-bold text-white transition hover:bg-[#244f7a] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#315f90]"
                >
                    <?= htmlspecialchars($submitLabel, ENT_QUOTES, 'UTF-8') ?>
                </button>
            </div>
        </form>
    </div>
</section>
