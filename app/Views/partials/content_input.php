<?php
/**
 * Shared discussion content input.
 *
 * @var string $contentInputType
 * @var string $contentInputValue
 * @var array $contentInputErrors
 * @var bool $showAttachmentField
 */

$contentInputMode = trim((string) ($contentInputType ?? 'post'));
$contentInputIsComment = $contentInputMode === 'comment';
$contentInputText = (string) ($contentInputValue ?? '');
$contentInputErrorData = is_array($contentInputErrors ?? null) ? $contentInputErrors : [];

$contentInputId = $contentInputIsComment ? 'comment-create' : 'post-create';
$contentInputFieldId = $contentInputIsComment ? 'reply-content' : 'content';
$contentInputLabel = $contentInputIsComment ? 'Comment content' : 'Description';
$contentInputContentError = trim((string) ($contentInputErrorData['content'] ?? ''));
$contentInputAttachmentError = trim((string) ($contentInputErrorData['attachment'] ?? ''));
$contentInputPlaceholder = $contentInputIsComment ? 'Share a clear explanation, useful resource, or next step.' : 'Body text';
$contentInputRows = $contentInputIsComment ? 6 : 7;
$contentInputMaxLength = $contentInputIsComment ? 5000 : null;
$contentInputRequired = $contentInputIsComment;
$contentInputShowAttachment = $contentInputIsComment || !empty($showAttachmentField);
$contentInputAllowVideo = !$contentInputIsComment;
$contentInputAttachmentAccept = $contentInputIsComment
    ? 'image/jpeg,image/png,image/gif,image/webp'
    : 'image/jpeg,image/png,image/gif,image/webp,video/mp4,video/webm,video/quicktime,.zip,.txt,.php,.js,.css,.html,.htm,.json,.xml,.sql,.py,.java,.c,.cpp,.cs,.md,.pdf,.doc,.docx';
$contentInputAttachmentHelp = $contentInputIsComment ? 'Drag and drop or upload an image' : 'Drag and drop or upload media/files';
$contentInputSubmitLabel = $contentInputIsComment ? 'Comment' : '';

$contentInputAttachmentId = $contentInputId . '-attachment';
$contentInputContentErrorId = $contentInputFieldId . '-error';
$contentInputAttachmentErrorId = $contentInputAttachmentId . '-error';
$contentInputHelpId = $contentInputAttachmentId . '-help';
$contentInputBorderClass = $contentInputContentError !== ''
    ? 'border-[#ba1a1a] focus-within:border-[#ba1a1a] focus-within:ring-[#ba1a1a]/10'
    : 'border-[#c4c7c7] focus-within:border-[#315f90] focus-within:ring-[#315f90]/15';
?>

<div data-content-input data-default-accept="<?= htmlspecialchars($contentInputAttachmentAccept, ENT_QUOTES, 'UTF-8') ?>">
    <label for="<?= htmlspecialchars($contentInputFieldId, ENT_QUOTES, 'UTF-8') ?>" class="sr-only">
        <?= htmlspecialchars($contentInputLabel, ENT_QUOTES, 'UTF-8') ?>
    </label>
    <div
        class="relative overflow-hidden rounded-[20px] border <?= $contentInputBorderClass ?> bg-white transition focus-within:ring-2"
        data-attachment-surface
    >
        <div class="flex min-h-12 items-center gap-1 border-b border-[#d1d3d5] px-3 text-[#667782]">
            <?php if ($contentInputShowAttachment): ?>
            <button
                type="button"
                class="inline-flex size-9 items-center justify-center rounded-full transition hover:bg-[#eef2f6] hover:text-[#27313a] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#315f90]"
                aria-label="Upload image"
                data-content-action="image"
            >
                <svg fill="currentColor" viewBox="0 0 20 20" class="size-4" aria-hidden="true">
                    <path d="M14.6 2H5.4A3.4 3.4 0 0 0 2 5.4v9.2A3.4 3.4 0 0 0 5.4 18h9.2a3.4 3.4 0 0 0 3.4-3.4V5.4A3.4 3.4 0 0 0 14.6 2ZM5.4 3.8h9.2c.882 0 1.6.718 1.6 1.6v6.088l-1.357-1.356a3.4 3.4 0 0 0-4.808 0L3.8 15.367V5.4c0-.882.718-1.6 1.6-1.6Zm5.35 3.7a1.75 1.75 0 1 1 3.5 0 1.75 1.75 0 0 1-3.5 0Z" />
                </svg>
            </button>
            <?php if ($contentInputAllowVideo): ?>
            <button
                type="button"
                class="inline-flex size-9 items-center justify-center rounded-full transition hover:bg-[#eef2f6] hover:text-[#27313a] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#315f90]"
                aria-label="Upload video"
                data-content-action="video"
            >
                <svg fill="currentColor" viewBox="0 0 20 20" class="size-4" aria-hidden="true">
                    <path d="M10 1.1a8.9 8.9 0 1 0 0 17.8 8.9 8.9 0 0 0 0-17.8Zm-2 5.35c0-.73.8-1.17 1.42-.79l4.12 2.55a.93.93 0 0 1 0 1.58l-4.12 2.55A.93.93 0 0 1 8 11.55v-5.1Z" />
                </svg>
            </button>
            <?php endif; ?>
            <?php endif; ?>
            <button
                type="button"
                class="inline-flex size-9 items-center justify-center rounded-full transition hover:bg-[#eef2f6] hover:text-[#27313a] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#315f90]"
                aria-label="Insert code block"
                data-content-action="code"
            >
                <svg fill="currentColor" viewBox="0 0 20 20" class="size-4" aria-hidden="true">
                    <path d="M8.704 17a.9.9 0 0 1-.88-1.087l2.594-12.201a.9.9 0 1 1 1.759.374L9.583 16.287a.9.9 0 0 1-.879.713Zm-2.567-1.764a.9.9 0 0 0 0-1.272L2.173 10l3.964-3.964a.9.9 0 1 0-1.273-1.272l-4.6 4.599a.9.9 0 0 0 0 1.272l4.6 4.6a.9.9 0 0 0 1.273.001Zm9 0 4.6-4.6a.9.9 0 0 0 0-1.272l-4.6-4.6a.9.9 0 1 0-1.273 1.272L17.828 10l-3.964 3.964a.9.9 0 0 0 1.273 1.272Z" />
                </svg>
            </button>
        </div>

        <textarea
            id="<?= htmlspecialchars($contentInputFieldId, ENT_QUOTES, 'UTF-8') ?>"
            name="content"
            rows="<?= htmlspecialchars($contentInputRows, ENT_QUOTES, 'UTF-8') ?>"
            <?= $contentInputMaxLength !== null ? 'maxlength="' . htmlspecialchars($contentInputMaxLength, ENT_QUOTES, 'UTF-8') . '"' : '' ?>
            <?= $contentInputRequired ? 'required' : '' ?>
            aria-describedby="<?= htmlspecialchars($contentInputContentErrorId, ENT_QUOTES, 'UTF-8') ?>"
            aria-invalid="<?= $contentInputContentError !== '' ? 'true' : 'false' ?>"
            class="min-h-38.4 w-full min-w-0 resize-y bg-white px-5 py-4 text-base leading-7 text-[#27313a] outline-none placeholder:text-[#667782]"
            placeholder="<?= htmlspecialchars($contentInputPlaceholder, ENT_QUOTES, 'UTF-8') ?>"
            data-content-field
        ><?= htmlspecialchars($contentInputText, ENT_QUOTES, 'UTF-8') ?></textarea>

        <?php if ($contentInputShowAttachment): ?>
        <input
            id="<?= htmlspecialchars($contentInputAttachmentId, ENT_QUOTES, 'UTF-8') ?>"
            name="attachment"
            type="file"
            accept="<?= htmlspecialchars($contentInputAttachmentAccept, ENT_QUOTES, 'UTF-8') ?>"
            aria-describedby="<?= htmlspecialchars($contentInputHelpId . ' ' . $contentInputAttachmentErrorId, ENT_QUOTES, 'UTF-8') ?>"
            aria-invalid="<?= $contentInputAttachmentError !== '' ? 'true' : 'false' ?>"
            class="sr-only"
            data-attachment-input
        >
        <label
            for="<?= htmlspecialchars($contentInputAttachmentId, ENT_QUOTES, 'UTF-8') ?>"
            class="absolute inset-0 z-10 hidden cursor-pointer flex-col items-center justify-center gap-3 rounded-[20px] border border-dashed <?= $contentInputAttachmentError !== '' ? 'border-[#ba1a1a]' : 'border-[#c4c7c7]' ?> bg-white px-5 py-8 text-center transition hover:border-[#315f90] hover:bg-[#f7f9fd]"
            data-attachment-dropzone
        >
            <span class="flex size-9 items-center justify-center rounded-full bg-[#e6edf5] text-[#27313a]" aria-hidden="true">
                <svg fill="currentColor" viewBox="0 0 20 20" class="size-4">
                    <path d="M10.3 16H6a5 5 0 0 1-.173-9.997 6.99 6.99 0 0 1 13.171 2.822c0 .49-.04.968-.125 1.434l-1.714-1.714a5.2 5.2 0 0 0-9.92-1.281l-.247.638L6 7.798a3.2 3.2 0 0 0 0 6.4h4.3V16Zm6.616-5.152-3.28-3.28a.9.9 0 0 0-1.273 0l-3.28 3.28a.9.9 0 0 0 1.272 1.272l1.744-1.743v7.117a.9.9 0 0 0 1.8 0v-7.117l1.744 1.743a.9.9 0 0 0 1.273-1.272Z" />
                </svg>
            </span>
            <span id="<?= htmlspecialchars($contentInputHelpId, ENT_QUOTES, 'UTF-8') ?>" class="text-sm leading-5 text-[#27313a]">
                <?= htmlspecialchars($contentInputAttachmentHelp, ENT_QUOTES, 'UTF-8') ?>
            </span>
        </label>
        <div class="mx-4 mb-4 hidden" data-attachment-preview aria-live="polite">
            <div class="flex flex-col gap-3 rounded-2xl border border-[#d1d3d5] bg-white p-3 sm:flex-row sm:items-center">
                <div class="min-h-40 w-full overflow-hidden rounded-2xl bg-black sm:w-56" data-attachment-preview-media></div>
                <div class="min-w-0 flex-1 text-left">
                    <span class="block truncate text-sm font-bold leading-5 text-[#27313a]" data-attachment-preview-name></span>
                    <span class="mt-1 block text-xs leading-5 text-[#5b6872]" data-attachment-preview-meta></span>
                </div>
                <button
                    type="button"
                    class="inline-flex h-9 shrink-0 items-center justify-center rounded-full bg-white px-4 text-sm font-bold text-[#ba1a1a] ring-1 ring-[#d1d3d5] transition hover:bg-[#fff0f0] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#ba1a1a]"
                    data-attachment-remove
                >
                    Remove
                </button>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($contentInputSubmitLabel !== ''): ?>
        <div class="flex justify-end border-t border-[#d1d3d5] px-3 py-3">
            <button
                type="submit"
                class="inline-flex h-10 items-center justify-center rounded-full bg-[#315f90] px-5 text-sm font-bold text-white transition hover:bg-[#244f7a] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#315f90]"
            >
                <?= htmlspecialchars($contentInputSubmitLabel, ENT_QUOTES, 'UTF-8') ?>
            </button>
        </div>
        <?php endif; ?>
    </div>

    <p
        id="<?= htmlspecialchars($contentInputContentErrorId, ENT_QUOTES, 'UTF-8') ?>"
        class="mt-2 <?= $contentInputContentError === '' ? 'hidden' : 'block' ?> text-sm leading-5 text-[#ba1a1a]"
        aria-live="polite"
    >
        <?= htmlspecialchars($contentInputContentError, ENT_QUOTES, 'UTF-8') ?>
    </p>
    <?php if ($contentInputShowAttachment): ?>
    <p
        id="<?= htmlspecialchars($contentInputAttachmentErrorId, ENT_QUOTES, 'UTF-8') ?>"
        class="mt-2 <?= $contentInputAttachmentError === '' ? 'hidden' : 'block' ?> text-sm leading-5 text-[#ba1a1a]"
        aria-live="polite"
    >
        <?= htmlspecialchars($contentInputAttachmentError, ENT_QUOTES, 'UTF-8') ?>
    </p>
    <?php endif; ?>
</div>
