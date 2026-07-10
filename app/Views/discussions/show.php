<?php
/**
 * Variables passed from DiscussionsController::show()
 *
 * @var array $discussion
 * @var array $replies
 * @var array|null $acceptedReply
 * @var array $modalReplies
 * @var array $relatedDiscussions
 * @var array $replyErrors
 * @var array $replyOld
 * @var array $modules
 * @var array $discussionEditErrors
 * @var string $discussionEditTitle
 * @var string $discussionEditModuleId
 * @var string $discussionEditContent
 * @var array $replyEditErrors
 * @var array $replyEditOld
 * @var int $activeReplyEditId
 * @var string $openModalId
 * @var bool $isLoggedIn
 * @var string $csrfToken
 */

$fieldError = static function (array $errors, string $field) {
    return trim((string) ($errors[$field] ?? ''));
};

$fieldRing = static function (array $errors, string $field) {
    return trim((string) ($errors[$field] ?? '')) !== ''
        ? 'ring-[#DC2626] focus:ring-[#DC2626]/40'
        : 'ring-[#E5E7EB] focus:ring-[#2563EB]/30';
};
?>

<section class="box-border min-h-screen bg-[#F7F8FB] px-4 py-8 font-sans text-[#111827] sm:px-6 lg:px-10 lg:py-10">
    <div class="mx-auto flex max-w-[1180px] flex-col gap-8">
        <header class="max-w-4xl">
            <a href="<?= htmlspecialchars($discussion['back_url'], ENT_QUOTES, 'UTF-8') ?>"
                class="inline-flex min-h-10 items-center gap-2 rounded-lg bg-white px-4 text-sm font-semibold text-[#1E3A8A] ring-1 ring-[#E5E7EB] transition duration-200 hover:bg-[#EEF2FF] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#1E3A8A]">
                <svg viewBox="0 0 20 20" class="size-4 shrink-0" fill="none" aria-hidden="true">
                    <path d="M12.5 5 7.5 10l5 5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"
                        stroke-linejoin="round" />
                </svg>
                Back to discussions
            </a>

            <div class="mt-6 flex flex-wrap gap-2">
                <a href="<?= htmlspecialchars($discussion['module_url'], ENT_QUOTES, 'UTF-8') ?>"
                    class="inline-flex max-w-full items-center rounded-full bg-[#EEF2FF] px-3 py-1 font-mono text-xs font-semibold text-[#1E3A8A] transition duration-200 hover:bg-[#DBEAFE] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#1E3A8A]">
                    <span
                        class="truncate"><?= htmlspecialchars($discussion['module'], ENT_QUOTES, 'UTF-8') ?></span>
                </a>
                <span
                    class="inline-flex items-center gap-1 rounded-full px-3 py-1 text-xs font-semibold <?= $discussion['status_tone'] === 'green' ? 'bg-[#DCFCE7] text-[#166534]' : 'bg-[#F3F4F6] text-[#374151]' ?>">
                    <?php if ($discussion['status_tone'] === 'green'): ?>
                    <svg viewBox="0 0 16 16" class="size-3.5 shrink-0" fill="none" aria-hidden="true">
                        <path d="m4 8.2 2.4 2.4L12 5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                    <?php endif; ?>
                    <?= htmlspecialchars($discussion['status'], ENT_QUOTES, 'UTF-8') ?>
                </span>
            </div>

            <h1 class="mt-4 text-3xl font-semibold text-[#0F172A] sm:text-5xl" dir="auto">
                <?= htmlspecialchars($discussion['title'], ENT_QUOTES, 'UTF-8') ?>
            </h1>

            <div class="mt-5 flex flex-wrap items-center gap-4 text-sm text-[#4B5563]">
                <span class="inline-flex min-w-0 items-center gap-3">
                    <span
                        class="flex size-10 shrink-0 items-center justify-center rounded-full bg-[#1E3A8A] text-sm font-semibold text-white"
                        aria-hidden="true">
                        <?= htmlspecialchars($discussion['avatar'], ENT_QUOTES, 'UTF-8') ?>
                    </span>
                    <span class="min-w-0 ">
                        <span class="font-semibold text-[#111827]">
                            <?= htmlspecialchars($discussion['author'], ENT_QUOTES, 'UTF-8') ?>
                        </span>
                        <?php if (!empty($discussion['author_handle'])): ?>
                        <span
                            class="ml-1"><?= htmlspecialchars($discussion['author_handle'], ENT_QUOTES, 'UTF-8') ?></span>
                        <?php endif; ?>
                    </span>
                </span>
                <span><?= htmlspecialchars($discussion['created_at'], ENT_QUOTES, 'UTF-8') ?></span>
            </div>
        </header>

        <div class="grid gap-8 lg:grid-cols-[minmax(0,1fr)_320px]">
            <div class="flex min-w-0 flex-col gap-6">
                <!-- Discussion content and actions -->
                <article class="bg-white p-5 rounded-2xl sm:p-7 border border-gray-200"
                    aria-labelledby="question-content-heading">
                    <div class="flex justify-between pb-5 border-b border-gray-200">
                        <h2 id="question-content-heading" class="text-lg font-semibold text-[#0F172A]">Question</h2>
                        <div class="relative" data-action-menu>
                            <button type="button"
                                class="inline-flex size-8 items-center justify-center rounded-4xl text-[#111827] transition duration-150 hover:bg-gray-200 hover:text-[#1E3A8A] aria-expanded:-translate-y-0.5 aria-expanded:text-[#1E3A8A] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#1E3A8A]"
                                aria-label="Open discussion actions" aria-haspopup="menu" aria-expanded="false"
                                data-action-menu-button>
                                <svg viewBox="0 0 20 20" class="size-4" fill="none" aria-hidden="true">
                                    <path d="M5.2 10h.01M10 10h.01M14.8 10h.01" stroke="currentColor" stroke-width="2.4"
                                        stroke-linecap="round" />
                                </svg>
                            </button>
                            <!-- Post actions -->
                            <div class="invisible absolute right-0 top-[calc(100%+8px)] z-50 w-52 rounded-lg bg-white p-2 opacity-0 ring-1 ring-[#D1D5DB] shadow-[0_18px_38px_rgba(25,28,31,0.12)] transition duration-150 data-[open=true]:visible data-[open=true]:opacity-100"
                                role="menu" data-action-menu-dropdown data-open="false">
                                <?php if (!empty($discussion['can_edit'])): ?>
                                <a href="<?= htmlspecialchars($discussion['edit_url'], ENT_QUOTES, 'UTF-8') ?>"
                                    data-open-modal="discussion-edit-modal"
                                    class="flex min-h-11 items-center gap-3 rounded-md px-3 py-2 text-sm font-semibold text-[#1E3A8A] transition duration-150 hover:bg-[#EEF2FF] focus-visible:outline-2 focus-visible:outline-offset-1 focus-visible:outline-[#1E3A8A]"
                                    role="menuitem">
                                    <svg viewBox="0 0 20 20" class="size-4 shrink-0" fill="none" aria-hidden="true">
                                        <path d="M4.5 14.7 5.3 11l7.8-7.8a1.8 1.8 0 0 1 2.5 2.5L7.8 13.5l-3.3 1.2Z"
                                            stroke="currentColor" stroke-width="1.6" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                        <path d="m11.8 4.5 3.1 3.1" stroke="currentColor" stroke-width="1.6"
                                            stroke-linecap="round" />
                                    </svg>
                                    <span>Edit</span>
                                </a>
                                <?php endif; ?>
                                <?php if (!empty($discussion['can_delete'])): ?>
                                <a href="<?= htmlspecialchars($discussion['delete_url'], ENT_QUOTES, 'UTF-8') ?>"
                                    data-open-modal="discussion-delete-modal"
                                    class="flex min-h-11 items-center gap-3 rounded-md px-3 py-2 text-sm font-semibold text-[#B91C1C] transition duration-150 hover:bg-[#FEF2F2] focus-visible:outline-2 focus-visible:outline-offset-1 focus-visible:outline-[#B91C1C]"
                                    role="menuitem">
                                    <svg viewBox="0 0 20 20" class="size-4 shrink-0" fill="none" aria-hidden="true">
                                        <path d="M4.5 6h11" stroke="currentColor" stroke-width="1.6"
                                            stroke-linecap="round" />
                                        <path
                                            d="M8.2 4h3.6M6 6l.6 9.2A1.8 1.8 0 0 0 8.4 17h3.2a1.8 1.8 0 0 0 1.8-1.8L14 6"
                                            stroke="currentColor" stroke-width="1.6" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                    </svg>
                                    <span>Delete</span>
                                </a>
                                <?php endif; ?>
                                <a href="#reply-editor"
                                    class="flex min-h-11 items-center gap-3 rounded-md px-3 py-2 text-sm font-semibold text-[#111827] transition duration-150 hover:bg-[#F7F8FB] focus-visible:outline-2 focus-visible:outline-offset-1 focus-visible:outline-[#1E3A8A]"
                                    role="menuitem">
                                    <svg viewBox="0 0 20 20" class="size-4 shrink-0 text-[#1E3A8A]" fill="none"
                                        aria-hidden="true">
                                        <path d="M8 6 4.5 9.5 8 13" stroke="currentColor" stroke-width="1.7"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                        <path d="M5 9.5h6.3A4.2 4.2 0 0 1 15.5 13.7V15" stroke="currentColor"
                                            stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <span>Reply</span>
                                </a>
                                <button type="button"
                                    class="flex min-h-11 w-full items-center gap-3 rounded-md px-3 py-2 text-left text-sm font-semibold text-[#111827] transition duration-150 hover:bg-[#F7F8FB] focus-visible:outline-2 focus-visible:outline-offset-1 focus-visible:outline-[#1E3A8A]"
                                    role="menuitem" data-share-discussion data-share-label="Share"
                                    data-shared-label="Copied">
                                    <svg viewBox="0 0 20 20" class="size-4 shrink-0 text-[#1E3A8A]" fill="none"
                                        aria-hidden="true">
                                        <path d="M7.2 11.1 12.8 14M12.8 6 7.2 8.9" stroke="currentColor"
                                            stroke-width="1.6" stroke-linecap="round" />
                                        <circle cx="5.5" cy="10" r="2" stroke="currentColor" stroke-width="1.6" />
                                        <circle cx="14.5" cy="5.2" r="2" stroke="currentColor" stroke-width="1.6" />
                                        <circle cx="14.5" cy="14.8" r="2" stroke="currentColor" stroke-width="1.6" />
                                    </svg>
                                    <span data-share-text>Share</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="grid gap-4 pt-5 text-base leading-7 text-[#111827]">
                        <?php foreach ($discussion['content_segments'] as $contentSegment): ?>
                        <?php
                            $segmentType = $contentSegment['type'];
                            $segmentContent = $contentSegment['content'];
                        ?>
                        <?php if (trim((string) $segmentContent) !== ''): ?>
                        <?php if ($segmentType === 'code'): ?>
                        <pre class="overflow-x-auto rounded-xl bg-[#0F172A] px-4 py-3 text-sm leading-6 text-[#E5E7EB]"><code><?= htmlspecialchars($segmentContent, ENT_QUOTES, 'UTF-8') ?></code></pre>
                        <?php else: ?>
                        <p><?= htmlspecialchars($segmentContent, ENT_QUOTES, 'UTF-8') ?></p>
                        <?php endif; ?>
                        <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                    <?php if (!empty($discussion['attachments'])): ?>
                    <section aria-labelledby="discussion-attachments-heading">
                        <h2 id="discussion-attachments-heading" class="sr-only">Discussion attachments</h2>
                        <div class="mt-4 grid gap-4">
                            <?php foreach ($discussion['attachments'] as $attachment): ?>
                            <?php
                                    $attachmentType = $attachment['type'];
                                    $attachmentUrl = $attachment['url'];
                                    $attachmentName = $attachment['name'];
                                    ?>

                            <?php if ($attachmentType === 'image'): ?>
                            <figure class="overflow-hidden rounded-2xl bg-[#F7F8FB] ring-1 ring-[#E5E7EB]">
                                <img src="<?= htmlspecialchars($attachmentUrl, ENT_QUOTES, 'UTF-8') ?>"
                                    alt="<?= htmlspecialchars($attachmentName, ENT_QUOTES, 'UTF-8') ?>"
                                    class="max-h-[520px] w-full object-contain">
                            </figure>
                            <?php elseif ($attachmentType === 'video'): ?>
                            <figure class="overflow-hidden rounded-2xl bg-black ring-1 ring-[#E5E7EB]">
                                <video controls preload="metadata" class="max-h-[520px] w-full">
                                    <source src="<?= htmlspecialchars($attachmentUrl, ENT_QUOTES, 'UTF-8') ?>"
                                        type="<?= htmlspecialchars($attachment['mime_type'], ENT_QUOTES, 'UTF-8') ?>">
                                    Your browser cannot play this video.
                                </video>
                                <figcaption
                                    class="border-t border-[#E5E7EB] bg-white px-4 py-3 text-sm leading-5 text-[#4B5563] ">
                                    <?= htmlspecialchars($attachmentName, ENT_QUOTES, 'UTF-8') ?>
                                    <?php if (!empty($attachment['size'])): ?>
                                    <span class="ml-1">&middot;
                                        <?= htmlspecialchars($attachment['size'], ENT_QUOTES, 'UTF-8') ?></span>
                                    <?php endif; ?>
                                </figcaption>
                            </figure>
                            <?php else: ?>
                            <a href="<?= htmlspecialchars($attachmentUrl, ENT_QUOTES, 'UTF-8') ?>"
                                class="flex min-w-0 flex-wrap items-center justify-between gap-3 rounded-2xl bg-[#F7F8FB] px-4 py-3 ring-1 ring-[#E5E7EB] transition duration-200 hover:ring-[#2563EB] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#1E3A8A]"
                                target="_blank" rel="noopener">
                                <span class="min-w-0">
                                    <span class="block text-sm font-semibold leading-5 text-[#111827] ">
                                        <?= htmlspecialchars($attachmentName, ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                    <span class="mt-1 block text-xs leading-4 text-[#4B5563]">
                                        <?= htmlspecialchars($attachment['size'], ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                </span>
                                <span class="shrink-0 text-sm font-semibold text-[#1E3A8A]">Open file</span>
                            </a>
                            <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    </section>
                    <?php endif; ?>
                </article>

                <!-- Accepted reply -->
                <?php if ($acceptedReply !== null): ?>
                <?php
                    $acceptedReplyId = (int) $acceptedReply['id'];
                    $acceptedReplyDomId = htmlspecialchars($acceptedReplyId, ENT_QUOTES, 'UTF-8');
                    $canEditAcceptedReply = !empty($acceptedReply['can_edit']);
                    $canDeleteAcceptedReply = !empty($acceptedReply['can_delete']);
                    $acceptedReplyParentUsername = trim((string) $acceptedReply['parent_author_username']);
                    $acceptedReplyTargetUsername = trim((string) $acceptedReply['author_username']);

                    if ($acceptedReplyTargetUsername === '') {
                        $acceptedReplyTargetUsername = ltrim(trim((string) $acceptedReply['author_handle']), '@');
                    }
                ?>
                <section id="reply-<?= htmlspecialchars($acceptedReply['id'], ENT_QUOTES, 'UTF-8') ?>"
                    class="rounded-xl bg-[#F0FDF4] p-5 ring-1 ring-[#BBF7D0] sm:p-6"
                    aria-labelledby="accepted-answer-heading">
                    <div class="flex items-start gap-4">
                        <span
                            class="flex size-11 shrink-0 items-center justify-center rounded-full bg-[#16A34A] text-white"
                            aria-hidden="true">
                            <svg viewBox="0 0 20 20" class="size-5" fill="none">
                                <path d="m5 10.3 3.1 3.1L15 6.6" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </span>
                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-start justify-between gap-3">
                                <h2 id="accepted-answer-heading" class="text-lg font-semibold text-[#14532D]">
                                    Accepted
                                    answer</h2>
                                <?php if ($canEditAcceptedReply || $canDeleteAcceptedReply): ?>
                                <div class="relative" data-action-menu>
                                    <button type="button"
                                        class="inline-flex size-8 items-center justify-center rounded-full text-[#14532D] transition duration-150 hover:bg-white/80 hover:text-[#166534] aria-expanded:-translate-y-0.5 aria-expanded:bg-white aria-expanded:text-[#166534] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#166534]"
                                        aria-label="Open reply actions" aria-haspopup="menu" aria-expanded="false"
                                        data-action-menu-button>
                                        <svg viewBox="0 0 20 20" class="size-4" fill="none" aria-hidden="true">
                                            <path d="M5.2 10h.01M10 10h.01M14.8 10h.01" stroke="currentColor"
                                                stroke-width="2.4" stroke-linecap="round" />
                                        </svg>
                                    </button>

                                    <div class="invisible absolute right-0 top-[calc(100%+8px)] z-50 w-52 rounded-lg bg-white p-2 opacity-0 ring-1 ring-[#D1D5DB] shadow-[0_18px_38px_rgba(25,28,31,0.12)] transition duration-150 data-[open=true]:visible data-[open=true]:opacity-100"
                                        role="menu" data-action-menu-dropdown data-open="false">
                                        <?php if ($canEditAcceptedReply): ?>
                                        <a href="<?= htmlspecialchars($acceptedReply['edit_url'], ENT_QUOTES, 'UTF-8') ?>"
                                            data-open-modal="reply-edit-modal-<?= $acceptedReplyDomId ?>"
                                            class="flex min-h-11 items-center gap-3 rounded-md px-3 py-2 text-sm font-semibold text-[#14532D] transition duration-150 hover:bg-[#F0FDF4] focus-visible:outline-2 focus-visible:outline-offset-1 focus-visible:outline-[#166534]"
                                            role="menuitem">
                                            <svg viewBox="0 0 20 20" class="size-4 shrink-0" fill="none"
                                                aria-hidden="true">
                                                <path
                                                    d="M4.5 14.7 5.3 11l7.8-7.8a1.8 1.8 0 0 1 2.5 2.5L7.8 13.5l-3.3 1.2Z"
                                                    stroke="currentColor" stroke-width="1.6" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                                <path d="m11.8 4.5 3.1 3.1" stroke="currentColor" stroke-width="1.6"
                                                    stroke-linecap="round" />
                                            </svg>
                                            <span>Edit</span>
                                        </a>
                                        <?php endif; ?>

                                        <?php if ($canDeleteAcceptedReply): ?>
                                        <div
                                            class="<?= $canEditAcceptedReply ? 'mt-1 border-t border-[#E5E7EB] pt-1' : '' ?>">
                                            <a href="<?= htmlspecialchars($acceptedReply['delete_url'], ENT_QUOTES, 'UTF-8') ?>"
                                                data-open-modal="reply-delete-modal-<?= $acceptedReplyDomId ?>"
                                                class="flex min-h-11 items-center gap-3 rounded-md px-3 py-2 text-sm font-semibold text-[#B91C1C] transition duration-150 hover:bg-[#FEF2F2] focus-visible:outline-2 focus-visible:outline-offset-1 focus-visible:outline-[#B91C1C]"
                                                role="menuitem">
                                                <svg viewBox="0 0 20 20" class="size-4 shrink-0" fill="none"
                                                    aria-hidden="true">
                                                    <path d="M4.5 6h11" stroke="currentColor" stroke-width="1.6"
                                                        stroke-linecap="round" />
                                                    <path
                                                        d="M8.2 4h3.6M6 6l.6 9.2A1.8 1.8 0 0 0 8.4 17h3.2a1.8 1.8 0 0 0 1.8-1.8L14 6"
                                                        stroke="currentColor" stroke-width="1.6" stroke-linecap="round"
                                                        stroke-linejoin="round" />
                                                </svg>
                                                <span>Delete</span>
                                            </a>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
                            <?php if ($acceptedReplyParentUsername !== ''): ?>
                            <p class="mb-2 text-xs font-semibold text-[#64748B]">
                                Replying to @<?= htmlspecialchars($acceptedReplyParentUsername, ENT_QUOTES, 'UTF-8') ?>
                            </p>
                            <?php endif; ?>
                            <p class="mt-2 text-base text-[#14532D] " dir="auto">
                                <?= htmlspecialchars($acceptedReply['content'], ENT_QUOTES, 'UTF-8') ?>
                            </p>
                            <?php foreach ($acceptedReply['attachments'] as $attachment): ?>
                            <figure class="mt-4 overflow-hidden rounded-2xl bg-white/80 ring-1 ring-[#BBF7D0]">
                                <img
                                    src="<?= htmlspecialchars($attachment['url'], ENT_QUOTES, 'UTF-8') ?>"
                                    alt="<?= htmlspecialchars($attachment['name'], ENT_QUOTES, 'UTF-8') ?>"
                                    class="max-h-[420px] w-full object-contain"
                                >
                            </figure>
                            <?php endforeach; ?>
                            <?php if ($isLoggedIn && $acceptedReplyId > 0 && $acceptedReplyTargetUsername !== ''): ?>
                            <button type="button"
                                class="mt-3 inline-flex min-h-9 items-center rounded-lg px-3 text-sm font-semibold text-[#166534] transition duration-150 hover:bg-white/70 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#166534]"
                                data-reply-target
                                data-reply-id="<?= htmlspecialchars($acceptedReplyId, ENT_QUOTES, 'UTF-8') ?>"
                                data-reply-username="<?= htmlspecialchars($acceptedReplyTargetUsername, ENT_QUOTES, 'UTF-8') ?>">
                                Reply
                            </button>
                            <?php endif; ?>
                            <p class="mt-4 text-sm font-medium text-[#166534]">
                                <?= htmlspecialchars($acceptedReply['author'], ENT_QUOTES, 'UTF-8') ?>
                                &middot;
                                <?= htmlspecialchars($acceptedReply['created_at'], ENT_QUOTES, 'UTF-8') ?>
                            </p>
                        </div>
                    </div>
                </section>
                <?php endif; ?>

                <!-- Replies -->

                <section class="flex flex-col gap-4" id="replies" aria-labelledby="replies-heading">
                    <div class="flex items-center justify-between gap-3">
                        <h2 id="replies-heading" class="text-xl font-semibold text-[#0F172A]">Comments</h2>
                        <span
                            class="text-sm font-medium text-[#4B5563]"><?= htmlspecialchars($discussion['replies'], ENT_QUOTES, 'UTF-8') ?>
                            total</span>
                    </div>

                    <section id="reply-editor" class="rounded-2xl border border-[#e6e8ec] bg-white p-5 sm:p-6"
                        aria-labelledby="reply-editor-heading">
                        <?php if ($isLoggedIn): ?>
                        <h2 id="reply-editor-heading" class="text-lg font-semibold text-[#0F172A]">Add a comment</h2>

                        <?php if (!empty($replyErrors['general'])): ?>
                        <div class="mt-4 rounded-2xl border border-[#FECACA] bg-[#FEF2F2] py-3 text-sm leading-6 text-[#991B1B]"
                            role="alert">
                            <?= htmlspecialchars($replyErrors['general'], ENT_QUOTES, 'UTF-8') ?>
                        </div>
                        <?php endif; ?>

                        <form action="<?= BASE_URL ?>/discussions/reply" method="post" enctype="multipart/form-data"
                            class="mt-4 grid gap-3">
                            <input type="hidden" name="_csrf_token"
                                value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                            <input type="hidden" name="post_id"
                                value="<?= htmlspecialchars($discussion['id'], ENT_QUOTES, 'UTF-8') ?>">
                            <input id="reply-parent-id" type="hidden" name="parent_reply_id" value="">
                            <input type="hidden" name="slug"
                                value="<?= htmlspecialchars($discussion['slug'], ENT_QUOTES, 'UTF-8') ?>">
                            <div id="reply-parent-preview"
                                class="hidden items-center justify-between gap-3 rounded-lg bg-[#EEF2FF] px-4 py-3 text-sm text-[#1E3A8A]"
                                data-reply-parent-preview>
                                <span class="font-semibold">Replying to @<span data-replying-to-username></span></span>
                                <button type="button"
                                    class="inline-flex min-h-8 items-center rounded-md px-2 text-sm font-semibold text-[#1E3A8A] transition duration-150 hover:bg-white focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#1E3A8A]"
                                    data-clear-reply-target>
                                    Cancel
                                </button>
                            </div>
                            <?php
                                $contentInputType = 'comment';
                                $contentInputValue = (string) ($replyOld['content'] ?? '');
                                $contentInputErrors = $replyErrors;
                                require ROOT_PATH . '/app/Views/partials/content_input.php';
                            ?>
                        </form>
                        <?php else: ?>
                        <div class="grid gap-4 sm:grid-cols-[minmax(0,1fr)_auto] sm:items-center">
                            <div>
                                <h2 id="reply-editor-heading" class="text-xl font-semibold text-[#0F172A]">Log in to
                                    join
                                    the discussion.</h2>
                                <p class="mt-2 text-sm leading-6 text-[#4B5563]">Use your Greenwich account to reply,
                                    follow
                                    activity, and keep your coursework conversations organized.</p>
                            </div>
                            <a href="<?= BASE_URL ?>/login"
                                class="inline-flex min-h-12 items-center justify-center rounded-lg bg-[#1E3A8A] px-5 text-sm font-semibold text-white transition duration-200 hover:bg-[#172E70] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#1E3A8A]">
                                Log in
                            </a>
                        </div>
                        <?php endif; ?>
                    </section>

                    <?php if (!empty($replies)): ?>
                    <?php foreach ($replies as $reply): ?>
                    <?php
                        $replyId = (int) $reply['id'];
                        $replyDomId = htmlspecialchars($replyId, ENT_QUOTES, 'UTF-8');
                        $canEditReply = !empty($reply['can_edit']);
                        $canDeleteReply = !empty($reply['can_delete']);
                        $parentReplyUsername = trim((string) $reply['parent_author_username']);
                        $replyTargetUsername = trim((string) $reply['author_username']);

                        if ($replyTargetUsername === '') {
                            $replyTargetUsername = ltrim(trim((string) $reply['author_handle']), '@');
                        }
                    ?>
                    <article id="reply-<?= htmlspecialchars($reply['id'], ENT_QUOTES, 'UTF-8') ?>"
                        class="flex gap-3 rounded-2xl border border-[#e6e8ec] bg-white p-5 sm:p-6">
                        <span
                            class="flex size-11 items-center justify-center rounded-full bg-[#DBEAFE] text-sm font-semibold text-[#1E3A8A]"
                            aria-hidden="true">
                            <?= htmlspecialchars($reply['avatar'], ENT_QUOTES, 'UTF-8') ?>
                        </span>
                        <div class="w-full">
                            <div class="flex flex-wrap items-start justify-between gap-3">
                                <div class="flex flex-wrap items-center gap-x-2 gap-y-1">
                                    <h3 class="font-semibold text-[#111827] " dir="auto">
                                        <?= htmlspecialchars($reply['author'], ENT_QUOTES, 'UTF-8') ?>
                                    </h3>
                                    <span
                                        class="rounded-full bg-[#F3F4F6] px-2 py-0.5 text-xs font-semibold text-[#374151]">
                                        <?= htmlspecialchars($reply['role'], ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                    <span class="text-sm text-[#6B7280]">
                                        <?= htmlspecialchars($reply['created_at'], ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                </div>
                                <?php if ($canEditReply || $canDeleteReply): ?>
                                <div class="relative" data-action-menu>
                                    <button type="button"
                                        class="inline-flex size-8 items-center justify-center rounded-full text-[#111827] transition duration-150 hover:bg-[#F3F4F6] hover:text-[#1E3A8A] aria-expanded:-translate-y-0.5 aria-expanded:bg-[#F3F4F6] aria-expanded:text-[#1E3A8A] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#1E3A8A]"
                                        aria-label="Open reply actions" aria-haspopup="menu" aria-expanded="false"
                                        data-action-menu-button>
                                        <svg viewBox="0 0 20 20" class="size-4" fill="none" aria-hidden="true">
                                            <path d="M5.2 10h.01M10 10h.01M14.8 10h.01" stroke="currentColor"
                                                stroke-width="2.4" stroke-linecap="round" />
                                        </svg>
                                    </button>

                                    <div class="invisible absolute right-0 top-[calc(100%+8px)] z-50 w-52 rounded-lg bg-white p-2 opacity-0 ring-1 ring-[#D1D5DB] shadow-[0_18px_38px_rgba(25,28,31,0.12)] transition duration-150 data-[open=true]:visible data-[open=true]:opacity-100"
                                        role="menu" data-action-menu-dropdown data-open="false">
                                        <?php if ($canEditReply): ?>
                                        <a href="<?= htmlspecialchars($reply['edit_url'], ENT_QUOTES, 'UTF-8') ?>"
                                            data-open-modal="reply-edit-modal-<?= $replyDomId ?>"
                                            class="flex min-h-11 items-center gap-3 rounded-md px-3 py-2 text-sm font-semibold text-[#1E3A8A] transition duration-150 hover:bg-[#EEF2FF] focus-visible:outline-2 focus-visible:outline-offset-1 focus-visible:outline-[#1E3A8A]"
                                            role="menuitem">
                                            <svg viewBox="0 0 20 20" class="size-4 shrink-0" fill="none"
                                                aria-hidden="true">
                                                <path
                                                    d="M4.5 14.7 5.3 11l7.8-7.8a1.8 1.8 0 0 1 2.5 2.5L7.8 13.5l-3.3 1.2Z"
                                                    stroke="currentColor" stroke-width="1.6" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                                <path d="m11.8 4.5 3.1 3.1" stroke="currentColor" stroke-width="1.6"
                                                    stroke-linecap="round" />
                                            </svg>
                                            <span>Edit</span>
                                        </a>
                                        <?php endif; ?>

                                        <?php if ($canDeleteReply): ?>
                                        <div class="<?= $canEditReply ? 'mt-1 border-t border-[#E5E7EB] pt-1' : '' ?>">
                                            <a href="<?= htmlspecialchars($reply['delete_url'], ENT_QUOTES, 'UTF-8') ?>"
                                                data-open-modal="reply-delete-modal-<?= $replyDomId ?>"
                                                class="flex min-h-11 items-center gap-3 rounded-md px-3 py-2 text-sm font-semibold text-[#B91C1C] transition duration-150 hover:bg-[#FEF2F2] focus-visible:outline-2 focus-visible:outline-offset-1 focus-visible:outline-[#B91C1C]"
                                                role="menuitem">
                                                <svg viewBox="0 0 20 20" class="size-4 shrink-0" fill="none"
                                                    aria-hidden="true">
                                                    <path d="M4.5 6h11" stroke="currentColor" stroke-width="1.6"
                                                        stroke-linecap="round" />
                                                    <path
                                                        d="M8.2 4h3.6M6 6l.6 9.2A1.8 1.8 0 0 0 8.4 17h3.2a1.8 1.8 0 0 0 1.8-1.8L14 6"
                                                        stroke="currentColor" stroke-width="1.6" stroke-linecap="round"
                                                        stroke-linejoin="round" />
                                                </svg>
                                                <span>Delete</span>
                                            </a>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
                            <?php if ($parentReplyUsername !== ''): ?>
                            <p class="mb-2 text-xs font-semibold text-[#64748B]">
                                Replying to @<?= htmlspecialchars($parentReplyUsername, ENT_QUOTES, 'UTF-8') ?>
                            </p>
                            <?php endif; ?>
                            <p class="text-base text-[#111827] " dir="auto">
                                <?= htmlspecialchars($reply['content'], ENT_QUOTES, 'UTF-8') ?></p>
                            <?php foreach ($reply['attachments'] as $attachment): ?>
                            <figure class="mt-4 overflow-hidden rounded-2xl bg-[#F7F8FB] ring-1 ring-[#E5E7EB]">
                                <img
                                    src="<?= htmlspecialchars($attachment['url'], ENT_QUOTES, 'UTF-8') ?>"
                                    alt="<?= htmlspecialchars($attachment['name'], ENT_QUOTES, 'UTF-8') ?>"
                                    class="max-h-[420px] w-full object-contain"
                                >
                            </figure>
                            <?php endforeach; ?>
                            <?php if ($isLoggedIn && $replyId > 0 && $replyTargetUsername !== ''): ?>
                            <button type="button"
                                class="mt-3 inline-flex min-h-9 items-center rounded-lg text-sm font-semibold text-[#1E3A8A] transition duration-150 hover:bg-[#EEF2FF] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#1E3A8A]"
                                data-reply-target data-reply-id="<?= htmlspecialchars($replyId, ENT_QUOTES, 'UTF-8') ?>"
                                data-reply-username="<?= htmlspecialchars($replyTargetUsername, ENT_QUOTES, 'UTF-8') ?>">
                                <svg class="mr-2" fill="currentColor" height="16" icon-name="comment"
                                    viewBox="0 0 20 20" width="16" xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M10 1a9 9 0 00-9 9c0 1.947.79 3.58 1.935 4.957L.231 17.661A.784.784 0 00.785 19H10a9 9 0 009-9 9 9 0 00-9-9zm0 16.2H6.162c-.994.004-1.907.053-3.045.144l-.076-.188a36.981 36.981 0 002.328-2.087l-1.05-1.263C3.297 12.576 2.8 11.331 2.8 10c0-3.97 3.23-7.2 7.2-7.2s7.2 3.23 7.2 7.2-3.23 7.2-7.2 7.2z">
                                    </path>
                                </svg>
                                Reply
                            </button>
                            <?php endif; ?>
                        </div>
                    </article>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <div class="rounded-2xl border  border-dashed border-[#CBD5E1] bg-white p-5">
                        <h3 class="font-semibold text-[#0F172A]">No comments yet</h3>
                        <p class="mt-2 text-sm leading-6 text-[#4B5563]">Be the first to share a useful direction or
                            resource.</p>
                    </div>
                    <?php endif; ?>
                </section>


            </div>

            <!-- Discussion metadata and related discussions -->
            <aside class="flex flex-col border-[#e6e8ec] lg:sticky lg:top-28 lg:self-start lg:border-l lg:pl-8"
                aria-label="Discussion sidebar">
                <section class="border-b border-[#e6e8ec] pb-5" aria-labelledby="about-discussion-heading">
                    <h2 id="about-discussion-heading" class="text-base font-semibold text-[#0F172A]">About this
                        discussion</h2>
                    <dl class="mt-4 grid gap-3 text-sm leading-6">
                        <div>
                            <dt class="font-medium text-[#4B5563]">Module</dt>
                            <dd class="mt-1 font-semibold text-[#111827] ">
                                <?= htmlspecialchars($discussion['module_name'], ENT_QUOTES, 'UTF-8') ?>
                            </dd>
                        </div>
                        <div>
                            <dt class="font-medium text-[#4B5563]">Category</dt>
                            <dd class="mt-1 font-semibold text-[#111827]">Coursework question</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-[#4B5563]">Last activity</dt>
                            <dd class="mt-1 font-semibold text-[#111827]">
                                <?= htmlspecialchars($discussion['updated_at'], ENT_QUOTES, 'UTF-8') ?>
                            </dd>
                        </div>
                    </dl>
                </section>

                <section class="pt-5" aria-labelledby="related-discussions-heading">
                    <h2 id="related-discussions-heading" class="text-base font-semibold text-[#0F172A]">Related
                        discussions</h2>
                    <div class="mt-4 grid gap-4">
                        <?php if (!empty($relatedDiscussions)): ?>
                        <?php foreach ($relatedDiscussions as $related): ?>
                        <a href="<?= htmlspecialchars($related['url'], ENT_QUOTES, 'UTF-8') ?>"
                            class="block rounded-md transition duration-200 hover:text-[#1E3A8A] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#1E3A8A]">
                            <span class="block text-sm font-semibold leading-5 text-[#111827] " dir="auto">
                                <?= htmlspecialchars($related['title'], ENT_QUOTES, 'UTF-8') ?>
                            </span>
                            <span class="mt-1 block text-xs font-medium text-[#4B5563]">
                                <?= htmlspecialchars($related['replies'], ENT_QUOTES, 'UTF-8') ?>
                                <?= $related['replies'] === 1 ? 'reply' : 'replies' ?>
                            </span>
                        </a>
                        <?php endforeach; ?>
                        <?php else: ?>
                        <p class="text-sm leading-6 text-[#4B5563]">Similar questions will appear as more
                            discussions
                            become active.</p>
                        <?php endif; ?>
                    </div>
                </section>
            </aside>
        </div>

        <!-- Discussion edit modal -->
        <?php if (!empty($discussion['can_edit'])): ?>
        <dialog id="discussion-edit-modal" data-modal
            <?= $openModalId === 'discussion-edit-modal' ? 'data-initial-open="true"' : '' ?>
            class="m-auto w-[min(680px,calc(100vw-32px))] rounded-[20px] bg-white p-0 text-[#111827] ring-1 ring-[#E5E7EB] shadow-[0_24px_64px_rgba(15,23,42,0.2)] backdrop:bg-[#0F172A]/45">
            <div class="max-h-[min(760px,calc(100vh-48px))] overflow-y-auto p-5 sm:p-6">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 id="discussion-edit-modal-title" class="text-xl font-semibold leading-7 text-[#0F172A]">
                            Edit discussion
                        </h2>
                        <p class="mt-1 text-sm leading-6 text-[#4B5563]">
                            Update the question details without leaving this discussion.
                        </p>
                    </div>
                    <button type="button" data-close-modal
                        class="inline-flex size-9 shrink-0 items-center justify-center rounded-full text-[#4B5563] transition duration-150 hover:bg-[#F3F4F6] hover:text-[#111827] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#1E3A8A]"
                        aria-label="Close edit discussion modal">
                        <svg viewBox="0 0 20 20" class="size-4" fill="none" aria-hidden="true">
                            <path d="m5 5 10 10M15 5 5 15" stroke="currentColor" stroke-width="1.8"
                                stroke-linecap="round" />
                        </svg>
                    </button>
                </div>

                <?php if (!empty($discussionEditErrors['general'])): ?>
                <div class="mt-4 rounded-2xl bg-[#FEF2F2] p-4 text-sm leading-6 text-[#991B1B] ring-1 ring-[#FECACA]"
                    role="alert">
                    <?= htmlspecialchars($discussionEditErrors['general'], ENT_QUOTES, 'UTF-8') ?>
                </div>
                <?php endif; ?>

                <form action="<?= htmlspecialchars($discussion['update_url'], ENT_QUOTES, 'UTF-8') ?>"
                    method="post" class="mt-5 grid gap-4" novalidate>
                    <input type="hidden" name="_csrf_token"
                        value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">

                    <div>
                        <label for="discussion-edit-title"
                            class="block text-sm font-semibold text-[#111827]">Title</label>
                        <input id="discussion-edit-title" name="title" type="text"
                            value="<?= htmlspecialchars($discussionEditTitle, ENT_QUOTES, 'UTF-8') ?>"
                            aria-describedby="discussion-edit-title-error"
                            aria-invalid="<?= $fieldError($discussionEditErrors, 'title') !== '' ? 'true' : 'false' ?>"
                            class="mt-2 h-12 w-full rounded-2xl border-0 bg-[#F7F8FB] px-4 text-base text-[#111827] outline-none ring-1 <?= $fieldRing($discussionEditErrors, 'title') ?> transition duration-200 focus:ring-2">
                        <p id="discussion-edit-title-error"
                            class="mt-2 <?= $fieldError($discussionEditErrors, 'title') === '' ? 'hidden' : 'block' ?> text-sm leading-5 text-[#B91C1C]"
                            aria-live="polite">
                            <?= htmlspecialchars($fieldError($discussionEditErrors, 'title'), ENT_QUOTES, 'UTF-8') ?>
                        </p>
                    </div>

                    <div>
                        <label for="discussion-edit-module"
                            class="block text-sm font-semibold text-[#111827]">Module</label>
                        <select id="discussion-edit-module" name="module_id"
                            aria-describedby="discussion-edit-module-error"
                            aria-invalid="<?= $fieldError($discussionEditErrors, 'module_id') !== '' ? 'true' : 'false' ?>"
                            class="mt-2 h-12 w-full rounded-2xl border-0 bg-[#F7F8FB] px-4 text-base text-[#111827] outline-none ring-1 <?= $fieldRing($discussionEditErrors, 'module_id') ?> transition duration-200 focus:ring-2">
                            <option value="">Select module</option>
                            <?php foreach ($modules as $module): ?>
                            <?php
                                    $moduleId = (string) ($module['id'] ?? '');
                                    $moduleCode = (string) ($module['code'] ?? 'MODULE');
                                    $moduleName = (string) ($module['name'] ?? 'Module');
                                    ?>
                            <option value="<?= htmlspecialchars($moduleId, ENT_QUOTES, 'UTF-8') ?>"
                                <?= $discussionEditModuleId === $moduleId ? 'selected' : '' ?>>
                                <?= htmlspecialchars($moduleCode . ' - ' . $moduleName, ENT_QUOTES, 'UTF-8') ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                        <p id="discussion-edit-module-error"
                            class="mt-2 <?= $fieldError($discussionEditErrors, 'module_id') === '' ? 'hidden' : 'block' ?> text-sm leading-5 text-[#B91C1C]"
                            aria-live="polite">
                            <?= htmlspecialchars($fieldError($discussionEditErrors, 'module_id'), ENT_QUOTES, 'UTF-8') ?>
                        </p>
                    </div>

                    <div>
                        <label for="discussion-edit-content"
                            class="block text-sm font-semibold text-[#111827]">Description</label>
                        <textarea id="discussion-edit-content" name="content" rows="8"
                            aria-describedby="discussion-edit-content-error"
                            aria-invalid="<?= $fieldError($discussionEditErrors, 'content') !== '' ? 'true' : 'false' ?>"
                            class="mt-2 min-h-40 w-full resize-y rounded-2xl border-0 bg-[#F7F8FB] px-4 py-3 text-base leading-7 text-[#111827] outline-none ring-1 <?= $fieldRing($discussionEditErrors, 'content') ?> transition duration-200 focus:ring-2"><?= htmlspecialchars($discussionEditContent, ENT_QUOTES, 'UTF-8') ?></textarea>
                        <p id="discussion-edit-content-error"
                            class="mt-2 <?= $fieldError($discussionEditErrors, 'content') === '' ? 'hidden' : 'block' ?> text-sm leading-5 text-[#B91C1C]"
                            aria-live="polite">
                            <?= htmlspecialchars($fieldError($discussionEditErrors, 'content'), ENT_QUOTES, 'UTF-8') ?>
                        </p>
                    </div>

                    <div class="flex flex-wrap justify-end gap-3 pt-1">
                        <button type="button" data-close-modal
                            class="inline-flex min-h-10 items-center justify-center rounded-2xl bg-[#EEF2FF] px-4 text-sm font-semibold text-[#1E3A8A] transition duration-200 hover:bg-[#DBEAFE] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#1E3A8A]">
                            Cancel
                        </button>
                        <button type="submit"
                            class="inline-flex min-h-10 items-center justify-center rounded-2xl bg-[#1E3A8A] px-4 text-sm font-semibold text-white transition duration-200 hover:bg-[#172E70] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#1E3A8A]">
                            Save changes
                        </button>
                    </div>
                </form>
            </div>
        </dialog>
        <?php endif; ?>

        <!-- Discussion delete modal -->
        <?php if (!empty($discussion['can_delete'])): ?>
        <dialog id="discussion-delete-modal" data-modal
            <?= $openModalId === 'discussion-delete-modal' ? 'data-initial-open="true"' : '' ?>
            class="m-auto w-[min(560px,calc(100vw-32px))] rounded-[20px] bg-white p-0 text-[#111827] ring-1 ring-[#E5E7EB] shadow-[0_24px_64px_rgba(15,23,42,0.2)] backdrop:bg-[#0F172A]/45">
            <div class="p-5 sm:p-6">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 id="discussion-delete-modal-title" class="text-xl font-semibold leading-7 text-[#0F172A]">
                            Delete discussion
                        </h2>
                        <p class="mt-2 text-sm leading-6 text-[#4B5563]">
                            This discussion, its replies, views, and notifications will be removed from the app.
                        </p>
                    </div>
                    <button type="button" data-close-modal
                        class="inline-flex size-9 shrink-0 items-center justify-center rounded-full text-[#4B5563] transition duration-150 hover:bg-[#F3F4F6] hover:text-[#111827] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#B91C1C]"
                        aria-label="Close delete discussion modal">
                        <svg viewBox="0 0 20 20" class="size-4" fill="none" aria-hidden="true">
                            <path d="m5 5 10 10M15 5 5 15" stroke="currentColor" stroke-width="1.8"
                                stroke-linecap="round" />
                        </svg>
                    </button>
                </div>

                <div class="mt-5 rounded-2xl bg-[#F7F8FB] p-4 text-sm leading-6 text-[#111827] ring-1 ring-[#E5E7EB] "
                    dir="auto">
                    <?= htmlspecialchars($discussion['title'], ENT_QUOTES, 'UTF-8') ?>
                </div>

                <form action="<?= htmlspecialchars($discussion['destroy_url'], ENT_QUOTES, 'UTF-8') ?>"
                    method="post" class="mt-6 flex flex-wrap justify-end gap-3">
                    <input type="hidden" name="_csrf_token"
                        value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                    <button type="button" data-close-modal
                        class="inline-flex min-h-10 items-center justify-center rounded-2xl bg-[#EEF2FF] px-4 text-sm font-semibold text-[#1E3A8A] transition duration-200 hover:bg-[#DBEAFE] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#1E3A8A]">
                        Cancel
                    </button>
                    <button type="submit"
                        class="inline-flex min-h-10 items-center justify-center rounded-2xl bg-[#B91C1C] px-4 text-sm font-semibold text-white transition duration-200 hover:bg-[#991B1B] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#B91C1C]">
                        Delete
                    </button>
                </form>
            </div>
        </dialog>
        <?php endif; ?>

        <!-- Reply edit and delete modals -->
        <?php foreach ($modalReplies as $replyModal): ?>
        <?php
            $replyModalId = (int) $replyModal['id'];
            $replyModalDomId = htmlspecialchars($replyModalId, ENT_QUOTES, 'UTF-8');
            $isActiveReplyEdit = $activeReplyEditId === $replyModalId;
            $currentReplyErrors = $isActiveReplyEdit ? $replyEditErrors : [];
            $currentReplyContent = (string) $replyModal['content'];

            if ($isActiveReplyEdit && array_key_exists('content', $replyEditOld)) {
                $currentReplyContent = (string) $replyEditOld['content'];
            }
            ?>
        <?php if (!empty($replyModal['can_edit'])): ?>
        <dialog id="reply-edit-modal-<?= $replyModalDomId ?>" data-modal
            <?= $openModalId === 'reply-edit-modal-' . $replyModalId ? 'data-initial-open="true"' : '' ?>
            class="m-auto w-[min(640px,calc(100vw-32px))] rounded-[20px] bg-white p-0 text-[#111827] ring-1 ring-[#E5E7EB] shadow-[0_24px_64px_rgba(15,23,42,0.2)] backdrop:bg-[#0F172A]/45">
            <div class="p-5 sm:p-6">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 id="reply-edit-modal-title-<?= $replyModalDomId ?>"
                            class="text-xl font-semibold leading-7 text-[#0F172A]">Edit reply</h2>
                        <p class="mt-1 text-sm leading-6 text-[#4B5563]">
                            Refine your reply while staying in the discussion context.
                        </p>
                    </div>
                    <button type="button" data-close-modal
                        class="inline-flex size-9 shrink-0 items-center justify-center rounded-full text-[#4B5563] transition duration-150 hover:bg-[#F3F4F6] hover:text-[#111827] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#1E3A8A]"
                        aria-label="Close edit reply modal">
                        <svg viewBox="0 0 20 20" class="size-4" fill="none" aria-hidden="true">
                            <path d="m5 5 10 10M15 5 5 15" stroke="currentColor" stroke-width="1.8"
                                stroke-linecap="round" />
                        </svg>
                    </button>
                </div>

                <?php if (!empty($currentReplyErrors['general'])): ?>
                <div class="mt-4 rounded-2xl bg-[#FEF2F2] p-4 text-sm leading-6 text-[#991B1B] ring-1 ring-[#FECACA]"
                    role="alert">
                    <?= htmlspecialchars($currentReplyErrors['general'], ENT_QUOTES, 'UTF-8') ?>
                </div>
                <?php endif; ?>

                <form action="<?= htmlspecialchars($replyModal['update_url'], ENT_QUOTES, 'UTF-8') ?>"
                    method="post" class="mt-5 grid gap-4">
                    <input type="hidden" name="_csrf_token"
                        value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                    <div>
                        <label for="reply-edit-content-<?= $replyModalDomId ?>" class="sr-only">Reply
                            content</label>
                        <textarea id="reply-edit-content-<?= $replyModalDomId ?>" name="content" rows="8"
                            maxlength="5000" required
                            aria-describedby="reply-edit-content-error-<?= $replyModalDomId ?>"
                            aria-invalid="<?= $fieldError($currentReplyErrors, 'content') !== '' ? 'true' : 'false' ?>"
                            class="min-h-36 w-full resize-y rounded-2xl border-0 bg-[#F7F8FB] px-4 py-3 text-base leading-7 text-[#111827] outline-none ring-1 <?= $fieldRing($currentReplyErrors, 'content') ?> transition duration-200 focus:ring-2"><?= htmlspecialchars($currentReplyContent, ENT_QUOTES, 'UTF-8') ?></textarea>
                        <p id="reply-edit-content-error-<?= $replyModalDomId ?>"
                            class="mt-2 <?= $fieldError($currentReplyErrors, 'content') === '' ? 'hidden' : 'block' ?> text-sm leading-5 text-[#B91C1C]"
                            aria-live="polite">
                            <?= htmlspecialchars($fieldError($currentReplyErrors, 'content'), ENT_QUOTES, 'UTF-8') ?>
                        </p>
                    </div>

                    <div class="flex flex-wrap justify-end gap-3">
                        <button type="button" data-close-modal
                            class="inline-flex min-h-10 items-center justify-center rounded-2xl bg-[#EEF2FF] px-4 text-sm font-semibold text-[#1E3A8A] transition duration-200 hover:bg-[#DBEAFE] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#1E3A8A]">
                            Cancel
                        </button>
                        <button type="submit"
                            class="inline-flex min-h-10 items-center justify-center rounded-2xl bg-[#1E3A8A] px-4 text-sm font-semibold text-white transition duration-200 hover:bg-[#172E70] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#1E3A8A]">
                            Save changes
                        </button>
                    </div>
                </form>
            </div>
        </dialog>
        <?php endif; ?>

        <?php if (!empty($replyModal['can_delete'])): ?>
        <dialog id="reply-delete-modal-<?= $replyModalDomId ?>" data-modal
            <?= $openModalId === 'reply-delete-modal-' . $replyModalId ? 'data-initial-open="true"' : '' ?>
            class="m-auto w-[min(560px,calc(100vw-32px))] rounded-[20px] bg-white p-0 text-[#111827] ring-1 ring-[#E5E7EB] shadow-[0_24px_64px_rgba(15,23,42,0.2)] backdrop:bg-[#0F172A]/45">
            <div class="p-5 sm:p-6">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 id="reply-delete-modal-title-<?= $replyModalDomId ?>"
                            class="text-xl font-semibold leading-7 text-[#0F172A]">Delete reply</h2>
                        <p class="mt-2 text-sm leading-6 text-[#4B5563]">
                            This reply will be removed from the discussion and related notifications will be
                            cleared.
                        </p>
                    </div>
                    <button type="button" data-close-modal
                        class="inline-flex size-9 shrink-0 items-center justify-center rounded-full text-[#4B5563] transition duration-150 hover:bg-[#F3F4F6] hover:text-[#111827] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#B91C1C]"
                        aria-label="Close delete reply modal">
                        <svg viewBox="0 0 20 20" class="size-4" fill="none" aria-hidden="true">
                            <path d="m5 5 10 10M15 5 5 15" stroke="currentColor" stroke-width="1.8"
                                stroke-linecap="round" />
                        </svg>
                    </button>
                </div>

                <div class="mt-5 rounded-2xl bg-[#F7F8FB] p-4 text-sm leading-6 text-[#111827] ring-1 ring-[#E5E7EB] "
                    dir="auto">
                    <?= htmlspecialchars($replyModal['content'], ENT_QUOTES, 'UTF-8') ?>
                </div>

                <form action="<?= htmlspecialchars($replyModal['destroy_url'], ENT_QUOTES, 'UTF-8') ?>"
                    method="post" class="mt-6 flex flex-wrap justify-end gap-3">
                    <input type="hidden" name="_csrf_token"
                        value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                    <button type="button" data-close-modal
                        class="inline-flex min-h-10 items-center justify-center rounded-2xl bg-[#EEF2FF] px-4 text-sm font-semibold text-[#1E3A8A] transition duration-200 hover:bg-[#DBEAFE] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#1E3A8A]">
                        Cancel
                    </button>
                    <button type="submit"
                        class="inline-flex min-h-10 items-center justify-center rounded-2xl bg-[#B91C1C] px-4 text-sm font-semibold text-white transition duration-200 hover:bg-[#991B1B] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#B91C1C]">
                        Delete
                    </button>
                </form>
            </div>
        </dialog>
        <?php endif; ?>
        <?php endforeach; ?>
    </div>
</section>
