<?php

use App\Helpers\ContentInputHelper;

/**
 * Shared content editor for discussion and reply forms.
 *
 * @var array{
 *     variant?: 'discussion'|'reply'|'edit-discussion'|'edit-reply',
 *     id?: string,
 *     value?: string,
 *     errors?: array{content?: string, attachments?: string},
 *     submit_label?: string,
 *     cancel_label?: string
 * } $contentInputConfig
 */

$contentInputConfigData = is_array($contentInputConfig ?? null) ? $contentInputConfig : [];
$contentInputData = ContentInputHelper::normalise($contentInputConfigData);
$contentInputFieldId = $contentInputData['field_id'];
$contentInputValue = $contentInputData['value'];
$contentInputLabel = $contentInputData['label'];
$contentInputPlaceholder = $contentInputData['placeholder'];
$contentInputRows = $contentInputData['rows'];
$contentInputMaxLength = $contentInputData['maxlength'];
$contentInputIsRequired = $contentInputData['required'];
$contentInputUploadProfile = $contentInputData['upload_profile'];
$contentInputShouldShowToolbar = $contentInputData['show_toolbar'];
$contentInputSubmitLabel = $contentInputData['submit_label'];
$contentInputCancelLabel = trim((string) ($contentInputConfigData['cancel_label'] ?? ''));
$contentInputContentError = $contentInputData['content_error'];
$contentInputAttachmentsError = $contentInputData['attachments_error'];
$contentInputShouldShowAttachments = $contentInputData['show_attachments'];
$contentInputShouldAllowVideo = $contentInputData['allow_video'];
$contentInputAttachmentAccept = $contentInputData['attachment_accept'];
$contentInputAttachmentHelp = $contentInputData['attachment_help'];
$contentInputRequiredMessage = in_array($contentInputData['variant'], ['reply', 'edit-reply'], true)
    ? 'Please write a reply before posting.'
    : 'Please complete this field.';

$contentInputAttachmentId = $contentInputFieldId . '-attachments';
$contentInputContentErrorId = $contentInputFieldId . '-error';
$contentInputAttachmentsErrorId = $contentInputAttachmentId . '-error';
$contentInputHelpId = $contentInputAttachmentId . '-help';
$contentInputBorderClass = $contentInputContentError !== ''
    ? 'border-ui-danger focus-within:border-ui-danger focus-within:ring-ui-danger/10'
    : 'border-ui-border-strong focus-within:border-brand-blue focus-within:ring-brand-blue/15';
?>

<div data-content-input <?php if ($contentInputShouldShowAttachments): ?> data-attachment
    data-attachment-profile="<?= htmlspecialchars($contentInputUploadProfile, ENT_QUOTES, 'UTF-8') ?>" <?php endif; ?>>
    <label for="<?= htmlspecialchars($contentInputFieldId, ENT_QUOTES, 'UTF-8') ?>" class="sr-only">
        <?= htmlspecialchars($contentInputLabel, ENT_QUOTES, 'UTF-8') ?>
    </label>
    <div class="relative overflow-hidden rounded-xl border <?= $contentInputBorderClass ?> bg-white transition focus-within:ring-3"
        data-content-surface data-attachment-surface>
        <?php if ($contentInputShouldShowToolbar): ?>
        <div class="flex min-h-12 items-center gap-1 border-b border-ui-border px-3 text-ui-muted">
            <?php if ($contentInputShouldShowAttachments): ?>
            <button type="button" class="ui-icon-button size-9 rounded-full" aria-label="Upload image"
                data-attachment-action="image">
                <svg fill="currentColor" viewBox="0 0 20 20" class="size-4" aria-hidden="true">
                    <path
                        d="M14.6 2H5.4A3.4 3.4 0 0 0 2 5.4v9.2A3.4 3.4 0 0 0 5.4 18h9.2a3.4 3.4 0 0 0 3.4-3.4V5.4A3.4 3.4 0 0 0 14.6 2ZM5.4 3.8h9.2c.882 0 1.6.718 1.6 1.6v6.088l-1.357-1.356a3.4 3.4 0 0 0-4.808 0L3.8 15.367V5.4c0-.882.718-1.6 1.6-1.6Zm5.35 3.7a1.75 1.75 0 1 1 3.5 0 1.75 1.75 0 0 1-3.5 0Z" />
                </svg>
            </button>
            <?php if ($contentInputShouldAllowVideo): ?>
            <button type="button" class="ui-icon-button size-9 rounded-full" aria-label="Upload video"
                data-attachment-action="video">
                <svg fill="currentColor" viewBox="0 0 20 20" class="size-4" aria-hidden="true">
                    <path
                        d="M10 1.1a8.9 8.9 0 1 0 0 17.8 8.9 8.9 0 0 0 0-17.8Zm-2 5.35c0-.73.8-1.17 1.42-.79l4.12 2.55a.93.93 0 0 1 0 1.58l-4.12 2.55A.93.93 0 0 1 8 11.55v-5.1Z" />
                </svg>
            </button>
            <button type="button" class="ui-icon-button size-9 rounded-full" aria-label="Upload files"
                data-attachment-action="files">
                <svg fill="none" viewBox="0 0 20 20" class="size-4" aria-hidden="true">
                    <path d="m7.25 10.75 4.9-4.9a2.3 2.3 0 1 1 3.25 3.25l-6.3 6.3a4 4 0 0 1-5.65-5.65l6.1-6.1"
                        stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </button>
            <?php endif; ?>
            <?php endif; ?>
            <button type="button" class="ui-icon-button size-9 rounded-full" aria-label="Insert code block"
                data-content-action="code">
                <svg fill="currentColor" viewBox="0 0 20 20" class="size-4" aria-hidden="true">
                    <path
                        d="M8.704 17a.9.9 0 0 1-.88-1.087l2.594-12.201a.9.9 0 1 1 1.759.374L9.583 16.287a.9.9 0 0 1-.879.713Zm-2.567-1.764a.9.9 0 0 0 0-1.272L2.173 10l3.964-3.964a.9.9 0 1 0-1.273-1.272l-4.6 4.599a.9.9 0 0 0 0 1.272l4.6 4.6a.9.9 0 0 0 1.273.001Zm9 0 4.6-4.6a.9.9 0 0 0 0-1.272l-4.6-4.6a.9.9 0 1 0-1.273 1.272L17.828 10l-3.964 3.964a.9.9 0 0 0 1.273 1.272Z" />
                </svg>
            </button>
        </div>
        <?php endif; ?>

        <textarea id="<?= htmlspecialchars($contentInputFieldId, ENT_QUOTES, 'UTF-8') ?>" name="content"
            rows="<?= htmlspecialchars($contentInputRows, ENT_QUOTES, 'UTF-8') ?>"
            <?= $contentInputMaxLength !== null ? 'maxlength="' . htmlspecialchars($contentInputMaxLength, ENT_QUOTES, 'UTF-8') . '"' : '' ?>
            <?= $contentInputIsRequired ? 'required' : '' ?>
            aria-describedby="<?= htmlspecialchars($contentInputContentErrorId, ENT_QUOTES, 'UTF-8') ?>"
            aria-invalid="<?= $contentInputContentError !== '' ? 'true' : 'false' ?>"
            class="min-h-40 w-full min-w-0 resize-y bg-white px-5 py-4 text-base leading-7 text-ui-ink outline-none placeholder:text-ui-muted"
            placeholder="<?= htmlspecialchars($contentInputPlaceholder, ENT_QUOTES, 'UTF-8') ?>"
            data-required-message="<?= htmlspecialchars($contentInputRequiredMessage, ENT_QUOTES, 'UTF-8') ?>"
            data-content-field><?= htmlspecialchars($contentInputValue, ENT_QUOTES, 'UTF-8') ?></textarea>

        <?php if ($contentInputShouldShowAttachments): ?>
        <input id="<?= htmlspecialchars($contentInputAttachmentId, ENT_QUOTES, 'UTF-8') ?>" name="attachments[]"
            type="file" accept="<?= htmlspecialchars($contentInputAttachmentAccept, ENT_QUOTES, 'UTF-8') ?>"
            aria-describedby="<?= htmlspecialchars($contentInputHelpId . ' ' . $contentInputAttachmentsErrorId, ENT_QUOTES, 'UTF-8') ?>"
            aria-invalid="<?= $contentInputAttachmentsError !== '' ? 'true' : 'false' ?>" class="sr-only" multiple
            data-attachment-input>
        <label for="<?= htmlspecialchars($contentInputAttachmentId, ENT_QUOTES, 'UTF-8') ?>"
            class="absolute inset-0 z-10 hidden cursor-pointer flex-col items-center justify-center gap-3 rounded-xl border border-dashed <?= $contentInputAttachmentsError !== '' ? 'border-ui-danger' : 'border-ui-border-strong' ?> bg-white px-5 py-8 text-center transition hover:border-brand-blue hover:bg-ui-canvas"
            data-attachment-dropzone>
            <span class="flex size-9 items-center justify-center rounded-full bg-ui-brand-soft text-brand-royal"
                aria-hidden="true">
                <svg fill="currentColor" viewBox="0 0 20 20" class="size-4">
                    <path
                        d="M10.3 16H6a5 5 0 0 1-.173-9.997 6.99 6.99 0 0 1 13.171 2.822c0 .49-.04.968-.125 1.434l-1.714-1.714a5.2 5.2 0 0 0-9.92-1.281l-.247.638L6 7.798a3.2 3.2 0 0 0 0 6.4h4.3V16Zm6.616-5.152-3.28-3.28a.9.9 0 0 0-1.273 0l-3.28 3.28a.9.9 0 0 0 1.272 1.272l1.744-1.743v7.117a.9.9 0 0 0 1.8 0v-7.117l1.744 1.743a.9.9 0 0 0 1.273-1.272Z" />
                </svg>
            </span>
            <span id="<?= htmlspecialchars($contentInputHelpId, ENT_QUOTES, 'UTF-8') ?>"
                class="text-sm leading-5 text-ui-text">
                <?= htmlspecialchars($contentInputAttachmentHelp, ENT_QUOTES, 'UTF-8') ?>
            </span>
        </label>
        <div class="mx-4 mb-4 hidden" data-attachment-preview aria-live="polite">
            <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-5" data-attachment-preview-list></div>
        </div>
        <?php endif; ?>

        <?php if ($contentInputSubmitLabel !== ''): ?>
        <div class="flex justify-end gap-2 border-t border-ui-border px-3 py-3">
            <?php if ($contentInputCancelLabel !== ''): ?>
            <button type="button" class="ui-button ui-button-secondary rounded-full" data-reply-editor-cancel>
                <?= htmlspecialchars($contentInputCancelLabel, ENT_QUOTES, 'UTF-8') ?>
            </button>
            <?php endif; ?>
            <button type="submit" class="ui-button ui-button-primary rounded-full">
                <?= htmlspecialchars($contentInputSubmitLabel, ENT_QUOTES, 'UTF-8') ?>
            </button>
        </div>
        <?php endif; ?>
    </div>

    <p id="<?= htmlspecialchars($contentInputContentErrorId, ENT_QUOTES, 'UTF-8') ?>"
        class="ui-field-error <?= $contentInputContentError === '' ? 'hidden' : 'block' ?>" aria-live="polite"
        data-content-error>
        <?= htmlspecialchars($contentInputContentError, ENT_QUOTES, 'UTF-8') ?>
    </p>
    <?php if ($contentInputShouldShowAttachments): ?>
    <p id="<?= htmlspecialchars($contentInputAttachmentsErrorId, ENT_QUOTES, 'UTF-8') ?>"
        class="ui-field-error <?= $contentInputAttachmentsError === '' ? 'hidden' : 'block' ?>" aria-live="polite"
        data-attachment-error>
        <?= htmlspecialchars($contentInputAttachmentsError, ENT_QUOTES, 'UTF-8') ?>
    </p>
    <?php endif; ?>
</div>
