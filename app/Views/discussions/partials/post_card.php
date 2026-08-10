<?php
use App\Helpers\FormatHelper;

/**
 * Variables passed by a parent view before including this partial
 *
 * @var array{
 *     module_code: string,
 *     module_name: string,
 *     status: string,
 *     status_tone: string,
 *     created_at: string,
 *     title: string,
 *     excerpt: string,
 *     author_full_name: string,
 *     author_handle: string,
 *     author_initial: string,
 *     author_avatar_url?: string|null,
 *     reply_count: int,
 *     view_count: int,
 *     attachment_preview_url?: string|null,
 *     attachment_type: string,
 *     attachment_preview_alt: string,
 *     url: string
 * } $discussionCard
 * @var bool $discussionCardAnimated
 */
$discussionCardReplyCount = (int) $discussionCard['reply_count'];
$discussionCardReplyCountText = (string) $discussionCardReplyCount;
$discussionCardStatus = (string) $discussionCard['status'];
$discussionCardStatusTone = (string) $discussionCard['status_tone'];
$discussionCardUrl = (string) $discussionCard['url'];
$discussionCardTitle = (string) $discussionCard['title'];
$discussionCardAttachmentPreviewUrl = (string) ($discussionCard['attachment_preview_url'] ?? '');
$discussionCardAttachmentType = (string) $discussionCard['attachment_type'];
$discussionCardAuthorAvatarUrl = (string) ($discussionCard['author_avatar_url'] ?? '');
$discussionCardReplyLabel = $discussionCardReplyCount === 1 ? 'reply' : 'replies';
$discussionCardViewCount = (int) $discussionCard['view_count'];
$discussionCardViewCountText = (string) FormatHelper::compactNumber($discussionCardViewCount);
$discussionCardViewLabel = $discussionCardViewCount === 1 ? 'view' : 'views';
$discussionCardCreatedAt = FormatHelper::textOr(
    FormatHelper::relativeTime((string) $discussionCard['created_at']),
    'Recently'
);
$discussionCardIsAnimated = !empty($discussionCardAnimated);
?>

<article class="ui-card ui-card-interactive group p-5 focus-within:ring-2 focus-within:ring-brand-blue/20 sm:p-6"
    data-motion-item data-motion-lift <?php if ($discussionCardIsAnimated): ?>data-dashboard-card<?php endif; ?>>
    <div class="flex flex-col gap-5">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div class="flex min-w-0 flex-wrap gap-2">
                <span class="ui-badge ui-badge-brand max-w-full rounded-md font-mono tracking-[0.05em]">
                    <span
                        class="truncate"><?= htmlspecialchars($discussionCard['module_code'], ENT_QUOTES, 'UTF-8') ?></span>
                </span>
                <span
                    class="ui-badge <?= $discussionCardStatusTone === 'green' ? 'ui-badge-success' : 'ui-badge-neutral' ?> tracking-[0.04em]">
                    <?php if ($discussionCardStatusTone === 'green'): ?>
                    <svg viewBox="0 0 16 16" class="size-3.5 shrink-0" fill="none" aria-hidden="true">
                        <path d="m4 8.2 2.4 2.4L12 5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                    <?php endif; ?>
                    <?= htmlspecialchars($discussionCardStatus, ENT_QUOTES, 'UTF-8') ?>
                </span>
            </div>

            <time class="text-sm font-medium text-ui-muted">
                <?= htmlspecialchars($discussionCardCreatedAt, ENT_QUOTES, 'UTF-8') ?>
            </time>
        </div>

        <div class="grid gap-4 sm:grid-cols-[minmax(0,1fr)_auto]">
            <div class="min-w-0">
                <?php if ($discussionCard['module_name'] !== ''): ?>
                <p class="text-sm font-medium leading-6 text-ui-text" dir="auto">
                    <?= htmlspecialchars($discussionCard['module_name'], ENT_QUOTES, 'UTF-8') ?>
                </p>
                <?php endif; ?>
                <h3 class="mt-1 break-words text-xl font-semibold leading-7 text-ui-ink" dir="auto">
                    <a href="<?= htmlspecialchars($discussionCardUrl, ENT_QUOTES, 'UTF-8') ?>"
                        class="rounded-sm transition duration-200 hover:text-brand-royal">
                        <?= htmlspecialchars($discussionCardTitle, ENT_QUOTES, 'UTF-8') ?>
                    </a>
                </h3>
                <p class="mt-2 line-clamp-2 max-w-2xl break-words text-base leading-6 text-ui-text" dir="auto">
                    <?= htmlspecialchars($discussionCard['excerpt'], ENT_QUOTES, 'UTF-8') ?>
                </p>
            </div>

            <?php if ($discussionCardAttachmentPreviewUrl !== ''): ?>
            <a href="<?= htmlspecialchars($discussionCardUrl, ENT_QUOTES, 'UTF-8') ?>"
                class="flex h-26 w-full items-center justify-center overflow-hidden rounded-lg bg-ui-canvas text-xs font-semibold text-ui-text transition duration-200 hover:text-brand-royal sm:w-[156px]"
                aria-label="Open discussion with attached media: <?= htmlspecialchars($discussionCardTitle, ENT_QUOTES, 'UTF-8') ?>">
                <?php if ($discussionCardAttachmentType === 'image'): ?>
                <img src="<?= htmlspecialchars($discussionCardAttachmentPreviewUrl, ENT_QUOTES, 'UTF-8') ?>"
                    alt="<?= htmlspecialchars($discussionCard['attachment_preview_alt'], ENT_QUOTES, 'UTF-8') ?>"
                    class="h-full w-full object-cover transition duration-200 group-hover:scale-[1.02]" loading="lazy"
                    decoding="async">
                <?php else: ?>
                <span class="inline-flex items-center gap-2 px-3">
                    <svg viewBox="0 0 18 18" class="size-4 shrink-0" fill="none" aria-hidden="true">
                        <rect x="3" y="4" width="12" height="10" rx="1.6" stroke="currentColor" stroke-width="1.4" />
                        <path d="m4.5 12 3-3 2.2 2.2 1.2-1.2 2.6 2" stroke="currentColor" stroke-width="1.4"
                            stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    Media
                </span>
                <?php endif; ?>
            </a>
            <?php endif; ?>
        </div>

        <div class="flex flex-wrap items-center justify-between gap-4 border-t border-ui-border pt-4">
            <div class="flex min-w-0 items-center gap-3">
                <?php if ($discussionCardAuthorAvatarUrl !== ''): ?>
                <img src="<?= htmlspecialchars($discussionCardAuthorAvatarUrl, ENT_QUOTES, 'UTF-8') ?>"
                    alt="<?= htmlspecialchars($discussionCard['author_full_name'] . ' avatar', ENT_QUOTES, 'UTF-8') ?>"
                    class="size-9 shrink-0 rounded-full object-cover">
                <?php else: ?>
                <span
                    class="flex size-9 shrink-0 items-center justify-center rounded-full bg-brand-royal text-xs font-semibold text-white"
                    aria-hidden="true">
                    <?= htmlspecialchars($discussionCard['author_initial'], ENT_QUOTES, 'UTF-8') ?>
                </span>
                <?php endif; ?>
                <p class="min-w-0 break-words text-sm leading-6 text-ui-text" dir="auto">
                    <span class="font-semibold text-ui-ink">
                        <?= htmlspecialchars($discussionCard['author_full_name'], ENT_QUOTES, 'UTF-8') ?>
                    </span>
                    <?php if ($discussionCard['author_handle'] !== ''): ?>
                    <span
                        class="ml-1"><?= htmlspecialchars($discussionCard['author_handle'], ENT_QUOTES, 'UTF-8') ?></span>
                    <?php endif; ?>
                </p>
            </div>

            <div class="flex items-center gap-4 text-sm font-medium text-ui-muted" aria-label="Discussion engagement">
                <span class="inline-flex items-center gap-1.5">
                    <svg viewBox="0 0 18 18" class="size-4 shrink-0" fill="none" aria-hidden="true">
                        <path d="M4 5.5h10v6H7.4L4 14.2V5.5Z" stroke="currentColor" stroke-width="1.4"
                            stroke-linejoin="round" />
                    </svg>
                    <?= htmlspecialchars($discussionCardReplyCountText, ENT_QUOTES, 'UTF-8') ?>
                    <span><?= htmlspecialchars($discussionCardReplyLabel, ENT_QUOTES, 'UTF-8') ?></span>
                </span>
                <span class="inline-flex items-center gap-1.5">
                    <svg viewBox="0 0 20 20" class="size-4 shrink-0" fill="none" aria-hidden="true">
                        <path d="M2.5 10s2.7-4.5 7.5-4.5 7.5 4.5 7.5 4.5-2.7 4.5-7.5 4.5S2.5 10 2.5 10Z"
                            stroke="currentColor" stroke-width="1.5" />
                        <circle cx="10" cy="10" r="1.8" stroke="currentColor" stroke-width="1.5" />
                    </svg>
                    <?= htmlspecialchars($discussionCardViewCountText, ENT_QUOTES, 'UTF-8') ?>
                    <span><?= htmlspecialchars($discussionCardViewLabel, ENT_QUOTES, 'UTF-8') ?></span>
                </span>
            </div>
        </div>
    </div>
</article>