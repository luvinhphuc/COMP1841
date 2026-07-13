<?php
/**
 * Variables passed by a parent view before including this partial
 *
 * @var array $postCard
 * @var bool $postCardAnimated
 */
$postCardReplyCount = (int) ($postCard['replies'] ?? $postCard['reply_count'] ?? 0);
$postCardReplyCountText = (string) $postCardReplyCount;
$postCardStatus = trim((string) ($postCard['status'] ?? 'Open'));
$postCardStatus = $postCardStatus !== '' ? $postCardStatus : 'Open';
$postCardStatusTone = trim((string) ($postCard['status_tone'] ?? ''));
$postCardStatusTone = $postCardStatusTone !== '' ? $postCardStatusTone : (strtolower($postCardStatus) === 'solved' ? 'green' : 'neutral');
$postCardUrl = trim((string) ($postCard['url'] ?? '#'));
$postCardUrl = $postCardUrl !== '' ? $postCardUrl : '#';
$postCardTitle = trim((string) ($postCard['title'] ?? 'Untitled discussion'));
$postCardTitle = $postCardTitle !== '' ? $postCardTitle : 'Untitled discussion';
$postCardImage = trim((string) ($postCard['image'] ?? ''));
$postCardMediaType = trim((string) ($postCard['media_type'] ?? ''));
$postCardAvatarUrl = trim((string) ($postCard['avatar_url'] ?? ''));
$postCardReplyLabel = $postCardReplyCount === 1 ? 'reply' : 'replies';
$postCardViewCount = trim((string) ($postCard['views'] ?? $postCard['view_count'] ?? '0'));
$postCardViewCount = $postCardViewCount !== '' ? $postCardViewCount : '0';
$postCardViewLabel = $postCardViewCount === '1' ? 'view' : 'views';
$postCardTime = trim((string) ($postCard['created_at'] ?? $postCard['time'] ?? 'Recently'));
$postCardTime = $postCardTime !== '' ? $postCardTime : 'Recently';
$postCardIsAnimated = !empty($postCardAnimated);
?>

<article
    class="group rounded-xl border border-[#c4c7c7] bg-white p-5 transition duration-200 hover:border-black focus-within:ring-2 focus-within:ring-black/10 sm:p-6"
    <?php if ($postCardIsAnimated): ?>data-dashboard-card<?php endif; ?>>
    <div class="flex flex-col gap-5">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div class="flex min-w-0 flex-wrap gap-2">
                <span
                    class="inline-flex max-w-full items-center rounded bg-[#d6e3ff] px-2 py-1 font-mono text-xs font-medium tracking-[0.05em] text-[#001b3d]">
                    <span
                        class="truncate"><?= htmlspecialchars(($postCard['module'] ?? 'MODULE'), ENT_QUOTES, 'UTF-8') ?></span>
                </span>
                <span
                    class="inline-flex items-center gap-1 rounded px-2 py-1 text-xs font-medium tracking-[0.05em] <?= $postCardStatusTone === 'green' ? 'bg-[#dcfce7] text-[#166534]' : 'bg-[#e6e8ec] text-[#444748]' ?>">
                    <?php if ($postCardStatusTone === 'green'): ?>
                    <svg viewBox="0 0 16 16" class="size-3.5 shrink-0" fill="none" aria-hidden="true">
                        <path d="m4 8.2 2.4 2.4L12 5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                    <?php endif; ?>
                    <?= htmlspecialchars($postCardStatus, ENT_QUOTES, 'UTF-8') ?>
                </span>
            </div>

            <time class="text-sm font-medium text-[#444748]">
                <?= htmlspecialchars($postCardTime, ENT_QUOTES, 'UTF-8') ?>
            </time>
        </div>

        <div class="grid gap-4 sm:grid-cols-[minmax(0,1fr)_auto]">
            <div class="min-w-0">
                <?php if (!empty($postCard['module_name'])): ?>
                <p class="text-sm font-medium leading-6 text-[#4B5563]" dir="auto">
                    <?= htmlspecialchars($postCard['module_name'], ENT_QUOTES, 'UTF-8') ?>
                </p>
                <?php endif; ?>
                <h3 class="mt-1 warp-break-words text-xl font-semibold leading-7 text-[#0F172A]" dir="auto">
                    <a href="<?= htmlspecialchars($postCardUrl, ENT_QUOTES, 'UTF-8') ?>"
                        class="rounded-sm transition duration-200 hover:text-emerald-800 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-black">
                        <?= htmlspecialchars($postCardTitle, ENT_QUOTES, 'UTF-8') ?>
                    </a>
                </h3>
                <p class="mt-2 line-clamp-2 max-w-2xl warp-break-words text-base leading-6 text-[#4B5563]" dir="auto">
                    <?= htmlspecialchars(($postCard['excerpt'] ?? 'No preview is available yet.'), ENT_QUOTES, 'UTF-8') ?>
                </p>
            </div>

            <?php if ($postCardImage !== ''): ?>
            <a href="<?= htmlspecialchars($postCardUrl, ENT_QUOTES, 'UTF-8') ?>"
                class="flex h-26 w-full items-center justify-center overflow-hidden rounded-md bg-[#f7f9fd] text-xs font-semibold text-[#444748] transition duration-200 hover:bg-[#eef1f6] hover:text-[#191c1f] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-black sm:w-[156px]"
                aria-label="Open discussion with attached media: <?= htmlspecialchars($postCardTitle, ENT_QUOTES, 'UTF-8') ?>">
                <?php if ($postCardMediaType === 'image'): ?>
                <img src="<?= htmlspecialchars($postCardImage, ENT_QUOTES, 'UTF-8') ?>"
                    alt="<?= htmlspecialchars(($postCard['preview_alt'] ?? $postCardTitle), ENT_QUOTES, 'UTF-8') ?>"
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

        <div class="flex flex-wrap items-center justify-between gap-4 border-t border-[#e6e8ec] pt-4">
            <div class="flex min-w-0 items-center gap-3">
                <?php if ($postCardAvatarUrl !== ''): ?>
                    <img src="<?= htmlspecialchars($postCardAvatarUrl, ENT_QUOTES, 'UTF-8') ?>"
                        alt="<?= htmlspecialchars(($postCard['author'] ?? 'Student') . ' avatar', ENT_QUOTES, 'UTF-8') ?>"
                        class="size-9 shrink-0 rounded-full object-cover">
                <?php else: ?>
                    <span
                        class="flex size-9 shrink-0 items-center justify-center rounded-full bg-black text-xs font-semibold text-white"
                        aria-hidden="true">
                        <?= htmlspecialchars(($postCard['avatar'] ?? 'S'), ENT_QUOTES, 'UTF-8') ?>
                    </span>
                <?php endif; ?>
                <p class="min-w-0 warp-break-words text-sm leading-6 text-[#4B5563]" dir="auto">
                    <span class="font-semibold text-[#111827]">
                        <?= htmlspecialchars(($postCard['author'] ?? 'Student'), ENT_QUOTES, 'UTF-8') ?>
                    </span>
                    <?php if (!empty($postCard['author_handle'])): ?>
                    <span class="ml-1"><?= htmlspecialchars($postCard['author_handle'], ENT_QUOTES, 'UTF-8') ?></span>
                    <?php endif; ?>
                </p>
            </div>

            <div class="flex items-center gap-4 text-sm font-medium text-[#444748]" aria-label="Discussion engagement">
                <span class="inline-flex items-center gap-1.5">
                    <svg viewBox="0 0 18 18" class="size-4 shrink-0" fill="none" aria-hidden="true">
                        <path d="M4 5.5h10v6H7.4L4 14.2V5.5Z" stroke="currentColor" stroke-width="1.4"
                            stroke-linejoin="round" />
                    </svg>
                    <?= htmlspecialchars($postCardReplyCountText, ENT_QUOTES, 'UTF-8') ?>
                    <span><?= htmlspecialchars($postCardReplyLabel, ENT_QUOTES, 'UTF-8') ?></span>
                </span>
                <span class="inline-flex items-center gap-1.5">
                    <svg viewBox="0 0 20 20" class="size-4 shrink-0" fill="none" aria-hidden="true">
                        <path d="M2.5 10s2.7-4.5 7.5-4.5 7.5 4.5 7.5 4.5-2.7 4.5-7.5 4.5S2.5 10 2.5 10Z"
                            stroke="currentColor" stroke-width="1.5" />
                        <circle cx="10" cy="10" r="1.8" stroke="currentColor" stroke-width="1.5" />
                    </svg>
                    <?= htmlspecialchars($postCardViewCount, ENT_QUOTES, 'UTF-8') ?>
                    <span><?= htmlspecialchars($postCardViewLabel, ENT_QUOTES, 'UTF-8') ?></span>
                </span>
            </div>
        </div>
    </div>
</article>
