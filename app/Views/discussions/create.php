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
$formContent = (string) ($old['content'] ?? '');
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
                    <select id="module_id" name="module_id" form="discussion-create-form"
                        aria-describedby="module_id-error"
                        aria-invalid="<?= $fieldError($errors, 'module_id') !== '' ? 'true' : 'false' ?>"
                        class="h-10 max-w-full rounded-full border <?= $fieldBorder($errors, 'module_id') ?> bg-white px-4 pr-9 text-sm font-semibold text-[#27313a] outline-none transition focus:ring-2">
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
                        class="mt-2 <?= $fieldError($errors, 'module_id') === '' ? 'hidden' : 'block' ?> text-sm leading-5 text-[#ba1a1a]"
                        aria-live="polite">
                        <?= htmlspecialchars($fieldError($errors, 'module_id'), ENT_QUOTES, 'UTF-8') ?>
                    </p>
                </div>
            </div>

            <a href="<?= htmlspecialchars($cancelUrl, ENT_QUOTES, 'UTF-8') ?>"
                class="inline-flex h-10 items-center rounded-full px-3 text-sm font-semibold text-[#315f90] transition hover:text-[#183f66] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#315f90]">
                Back to posts
            </a>
        </div>

        <form id="discussion-create-form" action="<?= htmlspecialchars($formAction, ENT_QUOTES, 'UTF-8') ?>"
            method="post" enctype="multipart/form-data" class="box-border" novalidate>
            <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">

            <?php if (!empty($errors['general'])): ?>
            <div class="mb-5 rounded-xl border border-[#ba1a1a] bg-[#ba1a1a]/5 px-4 py-3 text-sm leading-6 text-[#8f1111]"
                role="alert">
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
                        class="h-14 w-full min-w-0 rounded-[20px] border <?= $fieldBorder($errors, 'title') ?> bg-white px-5 text-base text-[#27313a] outline-none transition placeholder:text-[#667782] focus:ring-2"
                        placeholder="Title*">
                    <p id="title-error"
                        class="mt-2 <?= $fieldError($errors, 'title') === '' ? 'hidden' : 'block' ?> text-sm leading-5 text-[#ba1a1a]"
                        aria-live="polite">
                        <?= htmlspecialchars($fieldError($errors, 'title'), ENT_QUOTES, 'UTF-8') ?>
                    </p>
                </div>

                <div>
                    <label for="content" class="sr-only">Description</label>
                    <div class="relative overflow-hidden rounded-[20px] border <?= $fieldBorder($errors, 'content') ?> bg-white transition focus-within:ring-2"
                        data-attachment-surface>
                        <div class="flex min-h-12 items-center gap-1 border-b border-[#d1d3d5] px-3 text-[#667782]">
                            <?php if ($showAttachmentField): ?>
                            <button type="button"
                                class="inline-flex size-9 items-center justify-center rounded-full transition hover:bg-[#eef2f6] hover:text-[#27313a] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#315f90]"
                                aria-label="Upload image" data-composer-action="image">
                                <svg fill="currentColor" viewBox="0 0 20 20" class="size-4" aria-hidden="true">
                                    <path
                                        d="M14.6 2H5.4A3.4 3.4 0 002 5.4v9.2A3.4 3.4 0 005.4 18h9.2a3.4 3.4 0 003.4-3.4V5.4A3.4 3.4 0 0014.6 2zM5.4 3.8h9.2c.882 0 1.6.718 1.6 1.6v9.2c0 .484-.22.913-.561 1.207l-5.675-5.675a3.39 3.39 0 00-2.404-.996c-.87 0-1.74.332-2.404.996L3.8 11.488V5.4c0-.882.718-1.6 1.6-1.6zM3.8 14.6v-.567l2.629-2.628a1.59 1.59 0 011.131-.469c.427 0 .829.166 1.131.469l4.795 4.795H5.4c-.882 0-1.6-.718-1.6-1.6zm6.95-7.1a1.75 1.75 0 113.5 0 1.75 1.75 0 01-3.5 0z" />
                                </svg>
                            </button>
                            <button type="button"
                                class="inline-flex size-9 items-center justify-center rounded-full transition hover:bg-[#eef2f6] hover:text-[#27313a] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#315f90]"
                                aria-label="Upload video" data-composer-action="video">
                                <svg fill="currentColor" viewBox="0 0 20 20" class="size-4" aria-hidden="true">
                                    <path
                                        d="M8.797 14a1.78 1.78 0 01-.865-.224A1.816 1.816 0 017 12.187V7.812c0-.662.358-1.27.932-1.589a1.786 1.786 0 011.83.06L13.17 8.47c.521.334.831.906.831 1.53 0 .622-.31 1.194-.83 1.528l-3.408 2.187a1.79 1.79 0 01-.965.283V14zm0-6.188v4.375L12.204 10 8.797 7.812zM10 18.9c-1.516 0-2.946-.186-4.372-.57a5.626 5.626 0 01-3.958-3.956 16.605 16.605 0 01-.57-4.37c0-1.514.187-2.944.57-4.37a5.626 5.626 0 013.958-3.958 16.902 16.902 0 018.744 0 5.626 5.626 0 013.958 3.956c.384 1.428.57 2.858.57 4.371 0 1.513-.187 2.943-.57 4.37a5.626 5.626 0 01-3.958 3.957c-1.426.384-2.856.57-4.372.57zm-6.592-4.994a3.818 3.818 0 002.686 2.686 15.12 15.12 0 007.81 0 3.814 3.814 0 002.686-2.686c.342-1.273.508-2.55.508-3.903 0-1.353-.166-2.63-.508-3.904a3.818 3.818 0 00-2.686-2.686 15.12 15.12 0 00-7.81 0A3.814 3.814 0 003.408 6.1a14.841 14.841 0 00-.508 3.903c0 1.353.166 2.63.508 3.903v.001z" />
                                </svg>
                            </button>
                            <?php endif; ?>
                            <button type="button"
                                class="inline-flex size-9 items-center justify-center rounded-full transition hover:bg-[#eef2f6] hover:text-[#27313a] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#315f90]"
                                aria-label="Insert code block" data-composer-action="code">
                                <svg fill="currentColor" viewBox="0 0 20 20" class="size-4" aria-hidden="true">
                                    <path
                                        d="M8.704 17a.9.9 0 01-.88-1.087l2.594-12.201a.9.9 0 111.759.374L9.583 16.287a.9.9 0 01-.879.713zm-2.567-1.764a.898.898 0 000-1.272L2.173 10l3.964-3.964a.9.9 0 10-1.273-1.272l-4.6 4.599a.898.898 0 000 1.272l4.6 4.6a.897.897 0 001.274 0l-.001.001zm9 0l4.6-4.6a.898.898 0 000-1.272l-4.6-4.6a.9.9 0 10-1.273 1.272L17.828 10l-3.964 3.964a.898.898 0 00.637 1.536c.231 0 .46-.088.636-.264z" />
                                </svg>
                            </button>
                        </div>
                        <textarea id="content" name="content" rows="7" aria-describedby="content-error"
                            aria-invalid="<?= $fieldError($errors, 'content') !== '' ? 'true' : 'false' ?>"
                            class="min-h-38.4 w-full min-w-0 resize-y bg-white px-5 py-4 text-base leading-7 text-[#27313a] outline-none placeholder:text-[#667782]"
                            placeholder="Body text"><?= htmlspecialchars($formContent, ENT_QUOTES, 'UTF-8') ?></textarea>

                        <?php if ($showAttachmentField): ?>
                        <input id="attachment-file" name="attachment" type="file"
                            accept="image/jpeg,image/png,image/gif,image/webp,video/mp4,video/webm,video/quicktime,.zip,.txt,.php,.js,.css,.html,.htm,.json,.xml,.sql,.py,.java,.c,.cpp,.cs,.md,.pdf,.doc,.docx"
                            aria-describedby="attachment-media-help attachment-error"
                            aria-invalid="<?= $fieldError($errors, 'attachment') !== '' ? 'true' : 'false' ?>"
                            class="sr-only">
                        <label for="attachment-file"
                            class="absolute inset-0 z-10 hidden cursor-pointer flex-col items-center justify-center gap-3 rounded-[20px] border border-dashed <?= $fieldError($errors, 'attachment') !== '' ? 'border-[#ba1a1a]' : 'border-[#c4c7c7]' ?> bg-white px-5 py-8 text-center transition hover:border-[#315f90] hover:bg-[#f7f9fd] focus-within:border-[#315f90]"
                            data-attachment-dropzone>
                            <span class="flex flex-col items-center justify-center gap-3" data-attachment-empty>
                                <span
                                    class="flex size-9 items-center justify-center rounded-full bg-[#e6edf5] text-[#27313a]"
                                    aria-hidden="true">
                                    <svg fill="currentColor" viewBox="0 0 20 20" class="size-4">
                                        <path
                                            d="M10.3 16H6c-2.757 0-5-2.243-5-5a5.006 5.006 0 0 1 4.827-4.997c1.226-2.516 3.634-4.067 6.348-4.001a6.991 6.991 0 0 1 6.823 6.823 6.65 6.65 0 0 1-.125 1.434l-1.714-1.714c-.229-2.617-2.366-4.678-5.028-4.744-2.161-.059-4.058 1.307-4.892 3.463l-.247.638S6.448 7.798 6 7.798a3.204 3.204 0 0 0-3.2 3.2c0 1.764 1.436 3.2 3.2 3.2h4.3V16Zm6.616-5.152-3.28-3.28a.901.901 0 0 0-1.273 0l-3.28 3.28a.898.898 0 0 0 0 1.272.898.898 0 0 0 1.272 0l1.744-1.743v7.117a.9.9 0 0 0 1.8 0v-7.117l1.744 1.743a.898.898 0 0 0 1.272 0 .898.898 0 0 0 .001-1.272Z" />
                                    </svg>
                                </span>
                                <span id="attachment-media-help" class="text-sm leading-5 text-[#27313a]">
                                    Drag and drop or upload media/files
                                </span>
                            </span>
                        </label>
                        <div class="mx-4 mb-4 hidden" data-attachment-preview aria-live="polite">
                            <div
                                class="flex flex-col gap-3 rounded-2xl border border-[#d1d3d5] bg-white p-3 sm:flex-row sm:items-center">
                                <div class="min-h-40 w-full overflow-hidden rounded-2xl bg-black sm:w-56"
                                    data-attachment-preview-media></div>
                                <div class="min-w-0 flex-1 text-left">
                                    <span class="block truncate text-sm font-bold leading-5 text-[#27313a]"
                                        data-attachment-preview-name></span>
                                    <span class="mt-1 block text-xs leading-5 text-[#5b6872]"
                                        data-attachment-preview-meta></span>
                                </div>
                                <button type="button"
                                    class="inline-flex h-9 shrink-0 items-center justify-center rounded-full bg-white px-4 text-sm font-bold text-[#ba1a1a] ring-1 ring-[#d1d3d5] transition hover:bg-[#fff0f0] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#ba1a1a]"
                                    data-attachment-remove>
                                    <svg class="mr-2" fill="currentColor" height="16" icon-name="delete"
                                        viewBox="0 0 20 20" width="16" xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M15.2 15.7c0 .83-.67 1.5-1.5 1.5H6.3c-.83 0-1.5-.67-1.5-1.5V7.6H3v8.1C3 17.52 4.48 19 6.3 19h7.4c1.82 0 3.3-1.48 3.3-3.3V7.6h-1.8v8.1zM17.5 5.8c.5 0 .9-.4.9-.9S18 4 17.5 4h-3.63c-.15-1.68-1.55-3-3.27-3H9.4C7.68 1 6.28 2.32 6.13 4H2.5c-.5 0-.9.4-.9.9s.4.9.9.9h15zM7.93 4c.14-.68.75-1.2 1.47-1.2h1.2c.72 0 1.33.52 1.47 1.2H7.93z">
                                        </path>
                                    </svg>
                                    Remove
                                </button>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                    <p id="content-error"
                        class="mt-2 <?= $fieldError($errors, 'content') === '' ? 'hidden' : 'block' ?> text-sm leading-5 text-[#ba1a1a]"
                        aria-live="polite">
                        <?= htmlspecialchars($fieldError($errors, 'content'), ENT_QUOTES, 'UTF-8') ?>
                    </p>
                    <?php if ($showAttachmentField): ?>
                    <p id="attachment-error"
                        class="mt-2 <?= $fieldError($errors, 'attachment') === '' ? 'hidden' : 'block' ?> text-sm leading-5 text-[#ba1a1a]"
                        aria-live="polite">
                        <?= htmlspecialchars($fieldError($errors, 'attachment'), ENT_QUOTES, 'UTF-8') ?>
                    </p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="mt-5 flex justify-end gap-3">
                <a href="<?= htmlspecialchars($cancelUrl, ENT_QUOTES, 'UTF-8') ?>"
                    class="inline-flex h-10 items-center justify-center rounded-full bg-[#eef2f6] px-5 text-sm font-bold text-[#5b6872] transition hover:bg-[#e2e8ef] hover:text-[#27313a] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#315f90]">
                    Cancel
                </a>
                <button type="submit"
                    class="inline-flex h-10 items-center justify-center rounded-full bg-[#315f90] px-5 text-sm font-bold text-white transition hover:bg-[#244f7a] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#315f90]">
                    <?= htmlspecialchars($submitLabel, ENT_QUOTES, 'UTF-8') ?>
                </button>
            </div>
        </form>
    </div>
</section>