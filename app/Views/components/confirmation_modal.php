<?php
/**
 * Shared confirmation modal. Triggers provide the changing content through
 * data-confirm-* attributes; a page should render this component once.
 *
 * @var array{
 *     id?: string,
 *     csrf_token: string,
 *     title?: string,
 *     message?: string,
 *     detail?: string,
 *     action?: string,
 *     submit_label?: string,
 *     initial_open?: bool
 * } $confirmationModalConfig
 */

$confirmationModalConfigData = is_array($confirmationModalConfig ?? null) ? $confirmationModalConfig : [];
$confirmationModalId = trim((string) ($confirmationModalConfigData['id'] ?? 'confirmation-modal'));
$confirmationModalCsrfToken = (string) ($confirmationModalConfigData['csrf_token'] ?? '');

if ($confirmationModalId === '' || $confirmationModalCsrfToken === '') {
    return;
}

$confirmationModalTitle = trim((string) ($confirmationModalConfigData['title'] ?? 'Please confirm'));
$confirmationModalMessage = trim((string) ($confirmationModalConfigData['message'] ?? 'Are you sure you want to continue?'));
$confirmationModalDetail = trim((string) ($confirmationModalConfigData['detail'] ?? ''));
$confirmationModalAction = trim((string) ($confirmationModalConfigData['action'] ?? ''));
$confirmationModalSubmitLabel = trim((string) ($confirmationModalConfigData['submit_label'] ?? 'Confirm')) ?: 'Confirm';
$confirmationModalInitialOpen = !empty($confirmationModalConfigData['initial_open']) && $confirmationModalAction !== '';
$confirmationModalTitleId = $confirmationModalId . '-title';
?>
<dialog id="<?= htmlspecialchars($confirmationModalId, ENT_QUOTES, 'UTF-8') ?>"
    aria-labelledby="<?= htmlspecialchars($confirmationModalTitleId, ENT_QUOTES, 'UTF-8') ?>" data-modal
    data-confirmation-modal <?= $confirmationModalInitialOpen ? 'data-initial-open="true"' : '' ?>
    class="fixed inset-0 m-0 size-auto max-h-none max-w-none overflow-y-auto bg-transparent p-0 text-ui-ink backdrop:bg-gray-500/75">
    <div data-modal-backdrop class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
        <div class="relative w-full overflow-hidden rounded-lg bg-white text-left shadow-xl sm:my-8 sm:max-w-lg">
            <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div
                        class="mx-auto flex size-12 shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:size-10">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"
                            class="size-6 text-red-600">
                            <path
                                d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"
                                stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </div>
                    <div class="mt-3 min-w-0 text-center sm:ml-4 sm:mt-0 sm:text-left">
                        <h2 id="<?= htmlspecialchars($confirmationModalTitleId, ENT_QUOTES, 'UTF-8') ?>"
                            data-confirmation-title class="text-base font-semibold text-gray-900">
                            <?= htmlspecialchars($confirmationModalTitle, ENT_QUOTES, 'UTF-8') ?>
                        </h2>
                        <p data-confirmation-message class="mt-2 text-sm leading-6 text-gray-500">
                            <?= htmlspecialchars($confirmationModalMessage, ENT_QUOTES, 'UTF-8') ?>
                        </p>
                        <p data-confirmation-detail
                            class="mt-3 max-h-28 overflow-y-auto rounded-md bg-gray-50 px-3 py-2 text-sm text-gray-700 ring-1 ring-inset ring-gray-200 <?= $confirmationModalDetail === '' ? 'hidden' : '' ?>"
                            dir="auto">
                            <?= htmlspecialchars($confirmationModalDetail, ENT_QUOTES, 'UTF-8') ?>
                        </p>
                    </div>
                </div>
            </div>
            <form action="<?= htmlspecialchars($confirmationModalAction, ENT_QUOTES, 'UTF-8') ?>" method="post"
                data-confirmation-form class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                <input type="hidden" name="_csrf_token"
                    value="<?= htmlspecialchars($confirmationModalCsrfToken, ENT_QUOTES, 'UTF-8') ?>">
                <button type="submit" data-confirmation-submit <?= $confirmationModalAction === '' ? 'disabled' : '' ?>
                    class="inline-flex w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-red-500 disabled:cursor-not-allowed disabled:opacity-50 sm:ml-3 sm:w-auto">
                    <?= htmlspecialchars($confirmationModalSubmitLabel, ENT_QUOTES, 'UTF-8') ?>
                </button>
                <button type="button" data-close-modal
                    class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-xs ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto">
                    Cancel
                </button>
            </form>
        </div>
    </div>
</dialog>