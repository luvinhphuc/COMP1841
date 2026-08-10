<?php
/**
 * Shared action dropdown for discussion, reply, and administration actions.
 *
 * @var array{
 *     id: string,
 *     label?: string,
 *     tone?: 'default'|'success',
 *     items: array<int, array{
 *         label: string,
 *         icon?: string,
 *         tone?: 'default'|'brand'|'danger'|'success',
 *         href?: string,
 *         modal_id?: string,
 *         confirm_title?: string,
 *         confirm_message?: string,
 *         confirm_detail?: string,
 *         confirm_action?: string,
 *         confirm_submit_label?: string,
 *         action?: string,
 *         csrf_token?: string,
 *         fields?: array<string, scalar>,
 *         disabled?: bool,
 *         title?: string,
 *         share_text?: string
 *     }>
 * } $actionMenuConfig
 */

$actionMenuConfigData = is_array($actionMenuConfig ?? null) ? $actionMenuConfig : [];
$actionMenuId = trim((string) ($actionMenuConfigData['id'] ?? ''));
$actionMenuLabel = trim((string) ($actionMenuConfigData['label'] ?? 'Open actions'));
$actionMenuItems = array_values(array_filter(
    is_array($actionMenuConfigData['items'] ?? null) ? $actionMenuConfigData['items'] : [],
    static fn ($actionMenuItem) => is_array($actionMenuItem)
        && trim((string) ($actionMenuItem['label'] ?? '')) !== ''
));

if ($actionMenuId === '' || $actionMenuItems === []) {
    return;
}

$actionMenuTriggerTone = trim((string) ($actionMenuConfigData['tone'] ?? 'default'));
$actionMenuTriggerClass = $actionMenuTriggerTone === 'success'
    ? 'text-ui-success hover:bg-white/80 hover:text-ui-success aria-expanded:bg-white aria-expanded:text-ui-success focus-visible:outline-ui-success'
    : 'text-ui-ink hover:bg-ui-neutral-soft hover:text-brand-royal aria-expanded:bg-ui-neutral-soft aria-expanded:text-brand-royal focus-visible:outline-brand-blue';
$actionMenuBaseItemClass = 'flex min-h-11 w-full items-center gap-3 rounded-md px-3 py-2 text-left text-sm font-semibold transition duration-150 focus-visible:outline-2 focus-visible:outline-offset-1';
$actionMenuItemToneClasses = [
    'default' => 'text-ui-ink hover:bg-ui-canvas focus-visible:outline-brand-blue',
    'brand' => 'text-brand-royal hover:bg-ui-brand-soft focus-visible:outline-brand-blue',
    'danger' => 'text-ui-danger hover:bg-ui-danger-soft focus-visible:outline-ui-danger',
    'success' => 'text-ui-success hover:bg-ui-success-soft focus-visible:outline-ui-success',
];
?>

<div class="relative" data-dropdown data-dropdown-close-on-select="true">
    <button type="button"
        class="inline-flex size-8 items-center justify-center rounded-full transition duration-150 aria-expanded:-translate-y-0.5 focus-visible:outline-2 focus-visible:outline-offset-2 <?= $actionMenuTriggerClass ?>"
        aria-label="<?= htmlspecialchars($actionMenuLabel, ENT_QUOTES, 'UTF-8') ?>"
        aria-controls="<?= htmlspecialchars($actionMenuId, ENT_QUOTES, 'UTF-8') ?>" aria-expanded="false"
        data-dropdown-trigger>
        <svg viewBox="0 0 20 20" class="size-4" fill="none" aria-hidden="true">
            <path d="M5.2 10h.01M10 10h.01M14.8 10h.01" stroke="currentColor" stroke-width="2.4"
                stroke-linecap="round" />
        </svg>
    </button>

    <div id="<?= htmlspecialchars($actionMenuId, ENT_QUOTES, 'UTF-8') ?>"
        class="ui-card invisible fixed z-[100] w-52 p-2 opacity-0 shadow-ui-overlay transition duration-150 data-[open=true]:visible data-[open=true]:opacity-100"
        data-dropdown-panel data-dropdown-floating data-open="false">
        <?php foreach ($actionMenuItems as $actionMenuItemIndex => $actionMenuItem): ?>
        <?php
            $actionMenuItemLabel = trim((string) ($actionMenuItem['label'] ?? ''));
            $actionMenuItemIcon = trim((string) ($actionMenuItem['icon'] ?? ''));
            $actionMenuItemTone = trim((string) ($actionMenuItem['tone'] ?? 'default'));
            $actionMenuItemClass = $actionMenuBaseItemClass . ' '
                . ($actionMenuItemToneClasses[$actionMenuItemTone] ?? $actionMenuItemToneClasses['default']);
            $actionMenuItemAction = trim((string) ($actionMenuItem['action'] ?? ''));
            $actionMenuItemHref = trim((string) ($actionMenuItem['href'] ?? '#'));
            $actionMenuItemModalId = trim((string) ($actionMenuItem['modal_id'] ?? ''));
            $actionMenuItemConfirmTitle = trim((string) ($actionMenuItem['confirm_title'] ?? ''));
            $actionMenuItemConfirmMessage = trim((string) ($actionMenuItem['confirm_message'] ?? ''));
            $actionMenuItemConfirmDetail = trim((string) ($actionMenuItem['confirm_detail'] ?? ''));
            $actionMenuItemConfirmAction = trim((string) ($actionMenuItem['confirm_action'] ?? ''));
            $actionMenuItemConfirmSubmitLabel = trim((string) ($actionMenuItem['confirm_submit_label'] ?? ''));
            $actionMenuItemFields = is_array($actionMenuItem['fields'] ?? null) ? $actionMenuItem['fields'] : [];
            $actionMenuItemDisabled = !empty($actionMenuItem['disabled']);
            $actionMenuItemTitle = trim((string) ($actionMenuItem['title'] ?? ''));
            $actionMenuItemIsShareAction = array_key_exists('share_text', $actionMenuItem);
            $actionMenuItemDividerClass = $actionMenuItemTone === 'danger' && $actionMenuItemIndex > 0
                ? 'mt-1 border-t border-ui-border pt-1'
                : '';
            ?>
        <div class="<?= $actionMenuItemDividerClass ?>">
            <?php if ($actionMenuItemDisabled): ?>
            <button type="button" class="<?= $actionMenuItemClass ?> cursor-not-allowed opacity-45" disabled
                <?= $actionMenuItemTitle !== '' ? 'title="' . htmlspecialchars($actionMenuItemTitle, ENT_QUOTES, 'UTF-8') . '"' : '' ?>>
                <?php elseif ($actionMenuItemAction !== ''): ?>
            <form action="<?= htmlspecialchars($actionMenuItemAction, ENT_QUOTES, 'UTF-8') ?>" method="post">
                <input type="hidden" name="_csrf_token"
                    value="<?= htmlspecialchars((string) ($actionMenuItem['csrf_token'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                <?php foreach ($actionMenuItemFields as $actionMenuFieldName => $actionMenuFieldValue): ?>
                <input type="hidden" name="<?= htmlspecialchars((string) $actionMenuFieldName, ENT_QUOTES, 'UTF-8') ?>"
                    value="<?= htmlspecialchars((string) $actionMenuFieldValue, ENT_QUOTES, 'UTF-8') ?>">
                <?php endforeach; ?>
                <button type="submit" class="<?= $actionMenuItemClass ?>">
                    <?php elseif ($actionMenuItemIsShareAction): ?>
                    <button type="button" class="<?= $actionMenuItemClass ?>" data-share-discussion
                        data-share-label="<?= htmlspecialchars($actionMenuItemLabel, ENT_QUOTES, 'UTF-8') ?>"
                        data-shared-label="<?= htmlspecialchars((string) ($actionMenuItem['share_text'] ?? 'Copied'), ENT_QUOTES, 'UTF-8') ?>">
                        <?php else: ?>
                        <a href="<?= htmlspecialchars($actionMenuItemHref, ENT_QUOTES, 'UTF-8') ?>"
                            class="<?= $actionMenuItemClass ?>"
                            <?= $actionMenuItemModalId !== '' ? 'data-open-modal="' . htmlspecialchars($actionMenuItemModalId, ENT_QUOTES, 'UTF-8') . '"' : '' ?>
                            <?= $actionMenuItemConfirmTitle !== '' ? 'data-confirm-title="' . htmlspecialchars($actionMenuItemConfirmTitle, ENT_QUOTES, 'UTF-8') . '"' : '' ?>
                            <?= $actionMenuItemConfirmMessage !== '' ? 'data-confirm-message="' . htmlspecialchars($actionMenuItemConfirmMessage, ENT_QUOTES, 'UTF-8') . '"' : '' ?>
                            <?= $actionMenuItemConfirmDetail !== '' ? 'data-confirm-detail="' . htmlspecialchars($actionMenuItemConfirmDetail, ENT_QUOTES, 'UTF-8') . '"' : '' ?>
                            <?= $actionMenuItemConfirmAction !== '' ? 'data-confirm-action="' . htmlspecialchars($actionMenuItemConfirmAction, ENT_QUOTES, 'UTF-8') . '"' : '' ?>
                            <?= $actionMenuItemConfirmSubmitLabel !== '' ? 'data-confirm-submit-label="' . htmlspecialchars($actionMenuItemConfirmSubmitLabel, ENT_QUOTES, 'UTF-8') . '"' : '' ?>>
                            <?php endif; ?>

                            <?php if ($actionMenuItemIcon === 'edit'): ?>
                            <svg viewBox="0 0 20 20" class="size-4 shrink-0" fill="none" aria-hidden="true">
                                <path d="M4.5 14.7 5.3 11l7.8-7.8a1.8 1.8 0 0 1 2.5 2.5L7.8 13.5l-3.3 1.2Z"
                                    stroke="currentColor" stroke-width="1.6" stroke-linecap="round"
                                    stroke-linejoin="round" />
                                <path d="m11.8 4.5 3.1 3.1" stroke="currentColor" stroke-width="1.6"
                                    stroke-linecap="round" />
                            </svg>
                            <?php elseif ($actionMenuItemIcon === 'delete'): ?>
                            <svg viewBox="0 0 20 20" class="size-4 shrink-0" fill="none" aria-hidden="true">
                                <path d="M4.5 6h11" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" />
                                <path d="M8.2 4h3.6M6 6l.6 9.2A1.8 1.8 0 0 0 8.4 17h3.2a1.8 1.8 0 0 0 1.8-1.8L14 6"
                                    stroke="currentColor" stroke-width="1.6" stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>
                            <?php elseif ($actionMenuItemIcon === 'reply'): ?>
                            <svg viewBox="0 0 20 20" class="size-4 shrink-0 text-brand-royal" fill="none"
                                aria-hidden="true">
                                <path d="M8 6 4.5 9.5 8 13" stroke="currentColor" stroke-width="1.7"
                                    stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M5 9.5h6.3A4.2 4.2 0 0 1 15.5 13.7V15" stroke="currentColor" stroke-width="1.7"
                                    stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <?php elseif ($actionMenuItemIcon === 'share'): ?>
                            <svg viewBox="0 0 20 20" class="size-4 shrink-0 text-brand-royal" fill="none"
                                aria-hidden="true">
                                <path d="M7.2 11.1 12.8 14M12.8 6 7.2 8.9" stroke="currentColor" stroke-width="1.6"
                                    stroke-linecap="round" />
                                <circle cx="5.5" cy="10" r="2" stroke="currentColor" stroke-width="1.6" />
                                <circle cx="14.5" cy="5.2" r="2" stroke="currentColor" stroke-width="1.6" />
                                <circle cx="14.5" cy="14.8" r="2" stroke="currentColor" stroke-width="1.6" />
                            </svg>
                            <?php elseif ($actionMenuItemIcon === 'check'): ?>
                            <svg viewBox="0 0 20 20" class="size-4 shrink-0" fill="none" aria-hidden="true">
                                <path d="m4.5 10 3.2 3.2 7.8-7.8" stroke="currentColor" stroke-width="1.8"
                                    stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <?php elseif ($actionMenuItemIcon === 'unmark'): ?>
                            <svg viewBox="0 0 20 20" class="size-4 shrink-0" fill="none" aria-hidden="true">
                                <path d="M5 7.5h10M7.5 4.5 5 7.5l2.5 3M15 12.5H5M12.5 15.5l2.5-3-2.5-3"
                                    stroke="currentColor" stroke-width="1.6" stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>
                            <?php elseif ($actionMenuItemIcon === 'view'): ?>
                            <svg viewBox="0 0 20 20" class="size-4 shrink-0" fill="none" aria-hidden="true">
                                <path d="M2.8 10s2.6-4.5 7.2-4.5 7.2 4.5 7.2 4.5-2.6 4.5-7.2 4.5S2.8 10 2.8 10Z"
                                    stroke="currentColor" stroke-width="1.6" stroke-linejoin="round" />
                                <circle cx="10" cy="10" r="2.1" stroke="currentColor" stroke-width="1.6" />
                            </svg>
                            <?php endif; ?>
                            <span<?= $actionMenuItemIsShareAction ? ' data-share-text' : '' ?>>
                                <?= htmlspecialchars($actionMenuItemLabel, ENT_QUOTES, 'UTF-8') ?></span>

                                <?php if ($actionMenuItemDisabled): ?>
                    </button>
                    <?php elseif ($actionMenuItemAction !== ''): ?>
                    </button>
            </form>
            <?php elseif ($actionMenuItemIsShareAction): ?>
            </button>
            <?php else: ?>
            </a>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>
</div>
