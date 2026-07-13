<?php
/**
 * Reusable delete confirmation modal.
 *
 * @var string $confirmDeleteModalId
 * @var string $confirmDeleteTitle
 * @var string $confirmDeleteMessage
 * @var string $confirmDeleteUrl
 * @var string $csrfToken
 */
?>
<dialog id="<?= htmlspecialchars($confirmDeleteModalId, ENT_QUOTES, 'UTF-8') ?>" data-modal
    class="m-auto w-[min(560px,calc(100vw-32px))] rounded-[20px] bg-white p-0 text-[#172033] ring-1 ring-[#dfe5ef] shadow-[0_24px_64px_rgba(15,23,42,0.2)] backdrop:bg-[#0f172a]/45">
    <div class="p-5 sm:p-6">
        <div class="flex items-start justify-between gap-4">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.12em] text-[#c5221f]">Confirm deletion</p>
                <h2 class="mt-2 text-xl font-bold text-[#172033]">
                    <?= htmlspecialchars($confirmDeleteTitle, ENT_QUOTES, 'UTF-8') ?>
                </h2>
                <p class="mt-2 text-sm leading-6 text-[#667085]">
                    <?= htmlspecialchars($confirmDeleteMessage, ENT_QUOTES, 'UTF-8') ?>
                </p>
            </div>
            <button type="button" data-close-modal
                class="inline-flex size-9 shrink-0 items-center justify-center rounded-full text-[#667085] transition hover:bg-[#f3f6fb] hover:text-[#172033]"
                aria-label="Close confirmation dialog">
                <svg viewBox="0 0 20 20" class="size-4" fill="none" aria-hidden="true">
                    <path d="m5 5 10 10M15 5 5 15" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
                </svg>
            </button>
        </div>

        <form action="<?= htmlspecialchars($confirmDeleteUrl, ENT_QUOTES, 'UTF-8') ?>" method="post"
            class="mt-6 flex flex-wrap justify-end gap-3">
            <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
            <button type="button" data-close-modal
                class="inline-flex min-h-10 items-center justify-center rounded-lg border border-[#d6deea] bg-white px-4 text-sm font-semibold text-[#475467] transition hover:border-[#0b57d0] hover:text-[#0b57d0]">
                Cancel
            </button>
            <button type="submit"
                class="inline-flex min-h-10 items-center justify-center rounded-lg bg-[#c5221f] px-4 text-sm font-semibold text-white transition hover:bg-[#a50e0a]">
                Delete
            </button>
        </form>
    </div>
</dialog>
