<?php
$errors = $errors ?? [];
$old = $old ?? [];
$modules = $modules ?? [];

$fieldError = static function (array $errors, string $field) {
    return trim((string) ($errors[$field] ?? ''));
};

$fieldBorder = static function (array $errors, string $field) {
    return trim((string) ($errors[$field] ?? '')) !== ''
        ? 'border-[#ba1a1a] focus:border-[#ba1a1a] focus:ring-[#ba1a1a]/10'
        : 'border-[#c4c7c7] focus:border-[#315f90] focus:ring-[#315f90]/15';
};

$oldTitle = (string) ($old['title'] ?? '');
$oldModuleId = (string) ($old['module_id'] ?? '');
$oldContent = (string) ($old['content'] ?? '');
$formMode = (string) ($formMode ?? 'create');
$formAction = (string) ($formAction ?? BASE_URL . '/discussions/store');
$formTitle = (string) ($formTitle ?? 'Create discussion');
$submitLabel = (string) ($submitLabel ?? 'Post');
$cancelUrl = (string) ($cancelUrl ?? BASE_URL . '/discussions');
$showAttachmentField = (bool) ($showAttachmentField ?? true);
$csrfToken = (string) ($csrfToken ?? '');
?>

<section class="box-border min-h-[calc(100vh-80px)] bg-white text-[#27313a]">
    <div class="mx-auto box-border flex max-w-[980px] flex-col gap-6 px-5 py-8 sm:px-8 lg:px-10">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold leading-8 text-[#27313a] sm:text-[26px]">
                    <?= htmlspecialchars($formTitle, ENT_QUOTES, 'UTF-8') ?>
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
                            <option value="<?= htmlspecialchars($moduleId, ENT_QUOTES, 'UTF-8') ?>" <?= $oldModuleId === $moduleId ? 'selected' : '' ?>>
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
                Back to discussions
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
                    <?= htmlspecialchars((string) $errors['general'], ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php endif; ?>

            <nav class="flex items-end gap-7 border-b border-[#d1d3d5]" aria-label="Composer sections">
                <a
                    href="#content"
                    class="relative -mb-px inline-flex h-12 items-center border-b-[3px] border-[#315f90] px-1 text-sm font-bold text-[#27313a] focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-[#315f90]"
                >
                    Text
                </a>
                <?php if ($showAttachmentField): ?>
                    <a
                        href="#attachment"
                        class="relative -mb-px inline-flex h-12 items-center border-b-[3px] border-transparent px-1 text-sm font-bold text-[#5b6872] transition hover:text-[#27313a] focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-[#315f90]"
                    >
                        Images, video & files
                    </a>
                <?php endif; ?>
            </nav>

            <div class="mt-5 grid gap-5">
                <div>
                    <label for="title" class="sr-only">Title</label>
                    <input
                        id="title"
                        name="title"
                        type="text"
                        value="<?= htmlspecialchars($oldTitle, ENT_QUOTES, 'UTF-8') ?>"
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

                <div>
                    <label for="content" class="sr-only">Description</label>
                    <textarea
                        id="content"
                        name="content"
                        rows="9"
                        aria-describedby="content-error"
                        aria-invalid="<?= $fieldError($errors, 'content') !== '' ? 'true' : 'false' ?>"
                        class="min-h-[154px] w-full min-w-0 resize-y rounded-[20px] border <?= $fieldBorder($errors, 'content') ?> bg-white px-5 py-4 text-base leading-7 text-[#27313a] outline-none transition placeholder:text-[#667782] focus:ring-2"
                        placeholder="Body text"
                    ><?= htmlspecialchars($oldContent, ENT_QUOTES, 'UTF-8') ?></textarea>
                    <p
                        id="content-error"
                        class="mt-2 <?= $fieldError($errors, 'content') === '' ? 'hidden' : 'block' ?> text-sm leading-5 text-[#ba1a1a]"
                        aria-live="polite"
                    >
                        <?= htmlspecialchars($fieldError($errors, 'content'), ENT_QUOTES, 'UTF-8') ?>
                    </p>
                </div>

                <?php if ($showAttachmentField): ?>
                <div id="attachment" class="rounded-[20px] border border-[#d1d3d5] bg-[#f7f9fd] p-4">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <label for="attachment" class="block text-sm font-bold leading-5 text-[#27313a]">
                                Attachment
                            </label>
                            <p id="attachment-help" class="mt-1 text-sm leading-5 text-[#5b6872]">
                                Optional image, video, document, zip, or code file.
                            </p>
                        </div>
                    </div>

                    <input
                        id="attachment"
                        name="attachment"
                        type="file"
                        accept="image/jpeg,image/png,image/gif,image/webp,video/mp4,video/webm,video/quicktime,.zip,.txt,.php,.js,.css,.html,.htm,.json,.xml,.sql,.py,.java,.c,.cpp,.cs,.md,.pdf,.doc,.docx"
                        aria-describedby="attachment-help attachment-error"
                        aria-invalid="<?= $fieldError($errors, 'attachment') !== '' ? 'true' : 'false' ?>"
                        class="mt-4 block w-full min-w-0 rounded-xl border <?= $fieldBorder($errors, 'attachment') ?> bg-white px-4 py-3 text-sm text-[#27313a] outline-none transition file:mr-4 file:rounded-full file:border-0 file:bg-[#e6edf5] file:px-4 file:py-2 file:text-sm file:font-bold file:text-[#27313a] hover:file:bg-[#d7e5f4] focus:ring-2"
                    >
                    <p
                        id="attachment-error"
                        class="mt-2 <?= $fieldError($errors, 'attachment') === '' ? 'hidden' : 'block' ?> text-sm leading-5 text-[#ba1a1a]"
                        aria-live="polite"
                    >
                        <?= htmlspecialchars($fieldError($errors, 'attachment'), ENT_QUOTES, 'UTF-8') ?>
                    </p>
                </div>
                <?php endif; ?>
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
