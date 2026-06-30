<?php
$replyAction = $replyAction ?? [];
$replyActionTone = (string) ($replyActionTone ?? 'default');
$replyActionId = (int) ($replyAction['id'] ?? 0);
$canEditReplyAction = !empty($replyAction['can_edit']);
$canDeleteReplyAction = !empty($replyAction['can_delete']);
$isAcceptedReplyAction = $replyActionTone === 'accepted';

if ($canEditReplyAction || $canDeleteReplyAction):
    $buttonClass = $isAcceptedReplyAction
        ? 'text-[#14532D] hover:bg-white/80 hover:text-[#166534] aria-expanded:bg-white aria-expanded:text-[#166534] focus-visible:outline-[#166534]'
        : 'text-[#111827] hover:bg-[#F3F4F6] hover:text-[#1E3A8A] aria-expanded:bg-[#F3F4F6] aria-expanded:text-[#1E3A8A] focus-visible:outline-[#1E3A8A]';
    $editClass = $isAcceptedReplyAction
        ? 'text-[#14532D] hover:bg-[#F0FDF4] focus-visible:outline-[#166534]'
        : 'text-[#1E3A8A] hover:bg-[#EEF2FF] focus-visible:outline-[#1E3A8A]';
?>
<div class="relative" data-action-menu>
    <button type="button"
        class="inline-flex size-8 items-center justify-center rounded-full transition duration-150 aria-expanded:-translate-y-0.5 focus-visible:outline-2 focus-visible:outline-offset-2 <?= $buttonClass ?>"
        aria-label="Open reply actions" aria-haspopup="menu" aria-expanded="false" data-action-menu-button>
        <svg viewBox="0 0 20 20" class="size-4" fill="none" aria-hidden="true">
            <path d="M5.2 10h.01M10 10h.01M14.8 10h.01" stroke="currentColor" stroke-width="2.4"
                stroke-linecap="round" />
        </svg>
    </button>

    <div class="invisible absolute right-0 top-[calc(100%+8px)] z-50 w-52 rounded-lg bg-white p-2 opacity-0 ring-1 ring-[#D1D5DB] shadow-[0_18px_38px_rgba(25,28,31,0.12)] transition duration-150 data-[open=true]:visible data-[open=true]:opacity-100"
        role="menu" data-action-menu-dropdown data-open="false">
        <?php if ($canEditReplyAction): ?>
        <a href="<?= htmlspecialchars((string) ($replyAction['edit_url'] ?? '#'), ENT_QUOTES, 'UTF-8') ?>"
            data-open-modal="reply-edit-modal-<?= $replyActionId ?>"
            class="flex min-h-11 items-center gap-3 rounded-md px-3 py-2 text-sm font-semibold transition duration-150 focus-visible:outline-2 focus-visible:outline-offset-1 <?= $editClass ?>"
            role="menuitem">
            <svg viewBox="0 0 20 20" class="size-4 shrink-0" fill="none" aria-hidden="true">
                <path d="M4.5 14.7 5.3 11l7.8-7.8a1.8 1.8 0 0 1 2.5 2.5L7.8 13.5l-3.3 1.2Z"
                    stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" />
                <path d="m11.8 4.5 3.1 3.1" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" />
            </svg>
            <span>Edit</span>
        </a>
        <?php endif; ?>

        <?php if ($canDeleteReplyAction): ?>
        <div class="<?= $canEditReplyAction ? 'mt-1 border-t border-[#E5E7EB] pt-1' : '' ?>">
            <a href="<?= htmlspecialchars((string) ($replyAction['delete_url'] ?? '#'), ENT_QUOTES, 'UTF-8') ?>"
                data-open-modal="reply-delete-modal-<?= $replyActionId ?>"
                class="flex min-h-11 items-center gap-3 rounded-md px-3 py-2 text-sm font-semibold text-[#B91C1C] transition duration-150 hover:bg-[#FEF2F2] focus-visible:outline-2 focus-visible:outline-offset-1 focus-visible:outline-[#B91C1C]"
                role="menuitem">
                <svg viewBox="0 0 20 20" class="size-4 shrink-0" fill="none" aria-hidden="true">
                    <path d="M4.5 6h11" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" />
                    <path
                        d="M8.2 4h3.6M6 6l.6 9.2A1.8 1.8 0 0 0 8.4 17h3.2a1.8 1.8 0 0 0 1.8-1.8L14 6"
                        stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                <span>Delete</span>
            </a>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php
endif;

unset(
    $replyAction,
    $replyActionTone,
    $replyActionId,
    $canEditReplyAction,
    $canDeleteReplyAction,
    $isAcceptedReplyAction,
    $buttonClass,
    $editClass
);
?>
