<?php
/**
 * Variables passed from DiscussionController::show()
 *
 * @var array $discussion
 * @var array $replies
 * @var array|null $acceptedReply
 * @var array $modalReplies
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
    return trim((string)($errors[$field] ?? ''));
};

$fieldRing = static function (array $errors, string $field) {
    return trim((string)($errors[$field] ?? '')) !== '' ? 'border-ui-danger' : 'border-ui-border-strong';
};
?>

<section class="ui-page">
    <div class="ui-container flex max-w-[1180px] flex-col gap-8">
        <header class="max-w-4xl" data-motion-intro>
            <a href="<?= htmlspecialchars($discussion['back_url'], ENT_QUOTES, 'UTF-8') ?>"
                class="ui-button ui-button-secondary">
                <svg viewBox="0 0 20 20" class="size-4 shrink-0" fill="none" aria-hidden="true">
                    <path d="M12.5 5 7.5 10l5 5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"
                        stroke-linejoin="round" />
                </svg>
                Back to discussions
            </a>

            <div class="mt-6 flex flex-wrap gap-2">
                <a href="<?= htmlspecialchars($discussion['module_url'], ENT_QUOTES, 'UTF-8') ?>"
                    class="ui-badge ui-badge-brand max-w-full font-mono transition hover:text-brand-blue">
                    <span
                        class="truncate"><?= htmlspecialchars($discussion['module_code'], ENT_QUOTES, 'UTF-8') ?></span>
                </a>
                <span
                    class="ui-badge <?= $discussion['status_tone'] === 'green' ? 'ui-badge-success' : 'ui-badge-neutral' ?>">
                    <?php if ($discussion['status_tone'] === 'green'): ?>
                    <svg viewBox="0 0 16 16" class="size-3.5 shrink-0" fill="none" aria-hidden="true">
                        <path d="m4 8.2 2.4 2.4L12 5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                    <?php endif; ?>
                    <?= htmlspecialchars($discussion['status'], ENT_QUOTES, 'UTF-8') ?>
                </span>
            </div>

            <h1 class="ui-page-title mt-4 sm:text-5xl" dir="auto">
                <?= htmlspecialchars($discussion['title'], ENT_QUOTES, 'UTF-8') ?>
            </h1>

            <div class="mt-5 flex flex-wrap items-center gap-4 text-sm text-ui-text">
                <span class="inline-flex min-w-0 items-center gap-3">
                    <?php if (!empty($discussion['author_avatar_url'])): ?>
                    <img src="<?= htmlspecialchars($discussion['author_avatar_url'], ENT_QUOTES, 'UTF-8') ?>"
                        alt="<?= htmlspecialchars($discussion['author_full_name'] . ' avatar', ENT_QUOTES, 'UTF-8') ?>"
                        class="size-10 shrink-0 rounded-full object-cover">
                    <?php else: ?>
                    <span
                        class="flex size-10 shrink-0 items-center justify-center rounded-full bg-brand-royal text-sm font-semibold text-white"
                        aria-hidden="true">
                        <?= htmlspecialchars($discussion['author_initial'], ENT_QUOTES, 'UTF-8') ?>
                    </span>
                    <?php endif; ?>
                    <span class="min-w-0 ">
                        <span class="font-semibold text-ui-ink">
                            <?= htmlspecialchars($discussion['author_full_name'], ENT_QUOTES, 'UTF-8') ?>
                        </span>
                        <?php if (!empty($discussion['author_handle'])): ?>
                        <span
                            class="ml-1"><?= htmlspecialchars($discussion['author_handle'], ENT_QUOTES, 'UTF-8') ?></span>
                        <?php endif; ?>
                    </span>
                </span>
                <span><?= htmlspecialchars($discussion['created_at_formatted'], ENT_QUOTES, 'UTF-8') ?></span>
            </div>
        </header>

        <div class="grid gap-8 lg:grid-cols-[minmax(0,1fr)_320px]">
            <div class="flex min-w-0 flex-col gap-6">
                <!-- Discussion content and actions -->
                <article class="ui-card p-5 sm:p-7" aria-labelledby="question-content-heading" data-motion-reveal>
                    <div class="flex justify-between border-b border-ui-border pb-5">
                        <h2 id="question-content-heading" class="text-lg font-semibold text-ui-ink">Question</h2>
                        <?php
                        $discussionActionItems = [];

                        if (!empty($discussion['can_edit'])) {
                            $discussionActionItems[] = ['label' => 'Edit', 'icon' => 'edit', 'tone' => 'brand', 'href' => $discussion['edit_url'], 'modal_id' => 'discussion-edit-modal'];
                        }

                        if (!empty($discussion['can_mark_as_solved'])) {
                            $discussionActionItems[] = ['label' => 'Mark as solved', 'icon' => 'check', 'tone' => 'success', 'action' => $discussion['mark_as_solved_url'], 'csrf_token' => $csrfToken];
                        }

                        if (!empty($discussion['can_delete'])) {
                            $discussionActionItems[] = [
                                'label' => 'Delete',
                                'icon' => 'delete',
                                'tone' => 'danger',
                                'href' => $discussion['delete_url'],
                                'modal_id' => 'confirmation-modal',
                                'confirm_title' => 'Delete discussion?',
                                'confirm_message' => 'This discussion, its replies, and views will be permanently removed.',
                                'confirm_detail' => (string)$discussion['title'],
                                'confirm_action' => (string)$discussion['destroy_url'],
                                'confirm_submit_label' => 'Delete',
                            ];
                        }

                        $discussionActionItems[] = ['label' => 'Reply', 'icon' => 'reply', 'tone' => 'default', 'href' => '#reply-editor'];
                        $discussionActionItems[] = ['label' => 'Share', 'icon' => 'share', 'tone' => 'default', 'share_text' => 'Copied'];
                        $actionMenuConfig = ['id' => 'discussion-actions', 'label' => 'Open discussion actions', 'items' => $discussionActionItems];
                        require ROOT_PATH . '/app/Views/components/action_menu.php';
                        ?>
                    </div>

                    <div class="grid gap-4 pt-5 text-base leading-7 text-ui-ink">
                        <?php
                        $contentSegments = $discussion['content_segments'];
                        require ROOT_PATH . '/app/Views/discussions/partials/content_segments.php';
                        ?>
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
                            <figure class="overflow-hidden rounded-xl bg-ui-canvas ring-1 ring-ui-border">
                                <button type="button"
                                    class="block w-full cursor-zoom-in focus-visible:outline-2 focus-visible:outline-offset-[-3px] focus-visible:outline-brand-blue"
                                    aria-label="View <?= htmlspecialchars($attachmentName, ENT_QUOTES, 'UTF-8') ?> full size"
                                    data-image-viewer-trigger
                                    data-image-viewer-group="discussion-<?= (int)$discussion['id'] ?>"
                                    data-image-viewer-src="<?= htmlspecialchars($attachmentUrl, ENT_QUOTES, 'UTF-8') ?>"
                                    data-image-viewer-alt="<?= htmlspecialchars($attachmentName, ENT_QUOTES, 'UTF-8') ?>">
                                    <img src="<?= htmlspecialchars($attachmentUrl, ENT_QUOTES, 'UTF-8') ?>"
                                        alt="<?= htmlspecialchars($attachmentName, ENT_QUOTES, 'UTF-8') ?>"
                                        class="max-h-[520px] w-full object-contain">
                                </button>
                            </figure>
                            <?php elseif ($attachmentType === 'video'): ?>
                            <figure class="overflow-hidden rounded-xl bg-ui-ink ring-1 ring-ui-border">
                                <video controls preload="metadata" class="max-h-[520px] w-full">
                                    <source src="<?= htmlspecialchars($attachmentUrl, ENT_QUOTES, 'UTF-8') ?>"
                                        type="<?= htmlspecialchars($attachment['mime_type'], ENT_QUOTES, 'UTF-8') ?>">
                                    Your browser cannot play this video.
                                </video>
                                <figcaption
                                    class="border-t border-ui-border bg-white px-4 py-3 text-sm leading-5 text-ui-text ">
                                    <?= htmlspecialchars($attachmentName, ENT_QUOTES, 'UTF-8') ?>
                                    <?php if (!empty($attachment['size'])): ?>
                                    <span class="ml-1">&middot;
                                        <?= htmlspecialchars($attachment['size'], ENT_QUOTES, 'UTF-8') ?></span>
                                    <?php endif; ?>
                                </figcaption>
                            </figure>
                            <?php else: ?>
                            <a href="<?= htmlspecialchars($attachmentUrl, ENT_QUOTES, 'UTF-8') ?>"
                                class="flex min-w-0 flex-wrap items-center justify-between gap-3 rounded-xl bg-ui-canvas px-4 py-3 ring-1 ring-ui-border transition duration-200 hover:ring-brand-blue"
                                target="_blank" rel="noopener">
                                <span class="min-w-0">
                                    <span class="block text-sm font-semibold leading-5 text-ui-ink ">
                                        <?= htmlspecialchars($attachmentName, ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                    <span class="mt-1 block text-xs leading-4 text-ui-text">
                                        <?= htmlspecialchars($attachment['size'], ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                </span>
                                <span class="shrink-0 text-sm font-semibold text-brand-royal">Open file</span>
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
                    $acceptedReplyId = (int)$acceptedReply['id'];
                    $canEditAcceptedReply = !empty($acceptedReply['can_edit']);
                    $canDeleteAcceptedReply = !empty($acceptedReply['can_delete']);
                    $acceptedReplyParentUsername = trim((string)$acceptedReply['parent_author_username']);
                    $acceptedReplyTargetUsername = trim((string)$acceptedReply['author_username']);

                    if ($acceptedReplyTargetUsername === '') {
                        $acceptedReplyTargetUsername = ltrim(trim((string)$acceptedReply['author_handle']), '@');
                    }
                    ?>
                <section id="reply-<?= htmlspecialchars($acceptedReply['id'], ENT_QUOTES, 'UTF-8') ?>"
                    class="rounded-xl border border-ui-success-border bg-ui-success-soft p-5 sm:p-6"
                    aria-labelledby="accepted-answer-heading" data-motion-reveal>
                    <div class="flex items-start gap-4 h-full">
                        <div class="flex flex-col justify-between h-full">
                            <span
                                class="flex size-11 shrink-0 items-center justify-center rounded-full bg-ui-success text-white"
                                aria-hidden="true">
                                <svg viewBox="0 0 20 20" class="size-5" fill="none">
                                    <path d="m5 10.3 3.1 3.1L15 6.6" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </span>
                            <?php if (!empty($discussion['author_avatar_url'])): ?>
                            <img src="<?= htmlspecialchars($discussion['author_avatar_url'], ENT_QUOTES, 'UTF-8') ?>"
                                alt="<?= htmlspecialchars($discussion['author_full_name'] . ' avatar', ENT_QUOTES, 'UTF-8') ?>"
                                class="size-10 shrink-0 rounded-full object-cover">
                            <?php else: ?>
                            <span
                                class="flex size-10 shrink-0 items-center justify-center rounded-full bg-brand-royal text-sm font-semibold text-white"
                                aria-hidden="true">
                                <?= htmlspecialchars($discussion['author_initial'], ENT_QUOTES, 'UTF-8') ?>
                            </span>
                            <?php endif; ?>
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-start justify-between gap-3">
                                <h2 id="accepted-answer-heading" class="text-lg font-semibold text-ui-success">
                                    Accepted
                                    answer</h2>
                                <?php
                                    $acceptedActionItems = [];

                                    if (!empty($acceptedReply['can_unmark_as_solved'])) {
                                        $acceptedActionItems[] = ['label' => 'Not a solution', 'icon' => 'unmark', 'tone' => 'success', 'action' => $acceptedReply['unmark_as_solved_url'], 'csrf_token' => $csrfToken];
                                    }

                                    if ($canEditAcceptedReply) {
                                        $acceptedActionItems[] = ['label' => 'Edit', 'icon' => 'edit', 'tone' => 'success', 'href' => $acceptedReply['edit_url'], 'modal_id' => 'reply-edit-modal-' . $acceptedReplyId];
                                    }

                                    if ($canDeleteAcceptedReply) {
                                        $acceptedActionItems[] = [
                                            'label' => 'Delete',
                                            'icon' => 'delete',
                                            'tone' => 'danger',
                                            'href' => $acceptedReply['delete_url'],
                                            'modal_id' => 'confirmation-modal',
                                            'confirm_title' => 'Delete reply?',
                                            'confirm_message' => 'This reply will be permanently removed from the discussion.',
                                            'confirm_detail' => (string)$acceptedReply['content'],
                                            'confirm_action' => (string)$acceptedReply['destroy_url'],
                                            'confirm_submit_label' => 'Delete',
                                        ];
                                    }

                                    $actionMenuConfig = ['id' => 'accepted-reply-actions-' . $acceptedReplyId, 'label' => 'Open reply actions', 'tone' => 'success', 'items' => $acceptedActionItems];
                                    require ROOT_PATH . '/app/Views/components/action_menu.php';
                                    ?>
                            </div>
                            <?php if ($acceptedReplyParentUsername !== ''): ?>
                            <p class="mb-2 text-xs font-semibold text-ui-muted">
                                Replying to
                                @<?= htmlspecialchars($acceptedReplyParentUsername, ENT_QUOTES, 'UTF-8') ?>
                            </p>
                            <?php endif; ?>
                            <div class="mt-2 grid gap-4 text-base leading-7 text-ui-success">
                                <?php
                                    $contentSegments = $acceptedReply['content_segments'];
                                    require ROOT_PATH . '/app/Views/discussions/partials/content_segments.php';
                                    ?>
                            </div>
                            <?php foreach ($acceptedReply['attachments'] as $attachment): ?>
                            <figure class="mt-4 overflow-hidden rounded-2xl bg-white/80 ring-1 ring-ui-success-border">
                                <button type="button"
                                    class="block w-full cursor-zoom-in focus-visible:outline-2 focus-visible:outline-offset-[-3px] focus-visible:outline-ui-success"
                                    aria-label="View <?= htmlspecialchars($attachment['name'], ENT_QUOTES, 'UTF-8') ?> full size"
                                    data-image-viewer-trigger data-image-viewer-group="reply-<?= $acceptedReplyId ?>"
                                    data-image-viewer-src="<?= htmlspecialchars($attachment['url'], ENT_QUOTES, 'UTF-8') ?>"
                                    data-image-viewer-alt="<?= htmlspecialchars($attachment['name'], ENT_QUOTES, 'UTF-8') ?>">
                                    <img src="<?= htmlspecialchars($attachment['url'], ENT_QUOTES, 'UTF-8') ?>"
                                        alt="<?= htmlspecialchars($attachment['name'], ENT_QUOTES, 'UTF-8') ?>"
                                        class="max-h-[420px] w-full object-contain">
                                </button>
                            </figure>
                            <?php endforeach; ?>
                            <p class="mt-4 text-sm font-medium text-ui-success">
                                <?= htmlspecialchars($acceptedReply['author_full_name'], ENT_QUOTES, 'UTF-8') ?>
                                &middot;
                                <?= htmlspecialchars($acceptedReply['created_at_formatted'], ENT_QUOTES, 'UTF-8') ?>
                            </p>
                        </div>
                    </div>
                </section>
                <?php endif; ?>

                <!-- Replies -->

                <section class="flex flex-col gap-4" id="replies" aria-labelledby="replies-heading" data-motion-list>
                    <div class="flex items-center justify-between gap-3">
                        <h2 id="replies-heading" class="text-xl font-semibold text-ui-ink">Comments</h2>
                        <span class="text-sm font-medium text-ui-text"><?= (int)$discussion['reply_count'] ?>
                            total</span>
                    </div>

                    <?php
                    $replyEditorStartsOpen = $isLoggedIn
                        && (!empty($replyErrors) || trim((string)($replyOld['content'] ?? '')) !== '');
                    ?>
                    <section id="reply-editor" class="<?= $isLoggedIn ? '' : 'ui-card p-5 sm:p-6' ?>"
                        aria-labelledby="reply-editor-heading" data-motion-item>
                        <?php if ($isLoggedIn): ?>
                        <h2 id="reply-editor-heading" class="sr-only">Add a comment</h2>

                        <button type="button"
                            class="<?= $replyEditorStartsOpen ? 'hidden ' : '' ?>flex min-h-14 w-full items-center rounded-full border border-ui-border-strong bg-white px-5 text-left text-base text-ui-muted shadow-ui-sm transition hover:border-brand-blue hover:text-ui-text focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-blue"
                            aria-controls="reply-editor-panel"
                            aria-expanded="<?= $replyEditorStartsOpen ? 'true' : 'false' ?>" data-reply-editor-open>
                            Join the question
                        </button>

                        <div id="reply-editor-panel" class="<?= $replyEditorStartsOpen ? '' : 'hidden' ?>"
                            data-reply-editor-panel>
                            <?php if (!empty($replyErrors['general'])): ?>
                            <div class="mb-3 rounded-2xl border border-ui-danger-border bg-ui-danger-soft px-4 py-3 text-sm leading-6 text-ui-danger"
                                role="alert">
                                <?= htmlspecialchars($replyErrors['general'], ENT_QUOTES, 'UTF-8') ?>
                            </div>
                            <?php endif; ?>

                            <form action="<?= BASE_URL ?>/discussions/reply" method="post" enctype="multipart/form-data"
                                class="grid gap-3">
                                <input type="hidden" name="_csrf_token"
                                    value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                                <input type="hidden" name="post_id"
                                    value="<?= htmlspecialchars($discussion['id'], ENT_QUOTES, 'UTF-8') ?>">
                                <input id="reply-parent-id" type="hidden" name="parent_reply_id" value="">
                                <input type="hidden" name="slug"
                                    value="<?= htmlspecialchars($discussion['slug'], ENT_QUOTES, 'UTF-8') ?>">
                                <div id="reply-parent-preview"
                                    class="hidden items-center justify-between gap-3 rounded-lg bg-ui-brand-soft px-4 py-3 text-sm text-brand-royal"
                                    data-reply-parent-preview>
                                    <span class="font-semibold">Replying to @<span
                                            data-replying-to-username></span></span>
                                    <button type="button"
                                        class="inline-flex min-h-8 items-center rounded-md px-2 text-sm font-semibold text-brand-royal transition duration-150 hover:bg-white focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-blue"
                                        data-clear-reply-target>
                                        Cancel
                                    </button>
                                </div>
                                <?php
                                    $contentInputConfig = [
                                        'variant' => 'reply',
                                        'id' => 'reply-content',
                                        'value' => (string)($replyOld['content'] ?? ''),
                                        'errors' => $replyErrors,
                                        'cancel_label' => 'Cancel',
                                    ];
                                    require ROOT_PATH . '/app/Views/discussions/partials/content_input.php';
                                    ?>
                            </form>
                        </div>
                        <?php else: ?>
                        <div class="grid gap-4 sm:grid-cols-[minmax(0,1fr)_auto] sm:items-center">
                            <div>
                                <h2 id="reply-editor-heading" class="text-xl font-semibold text-ui-ink">Log in to
                                    join
                                    the discussion.</h2>
                                <p class="mt-2 text-sm leading-6 text-ui-text">Use your Greenwich account to reply,
                                    follow
                                    activity, and keep your coursework conversations organized.</p>
                            </div>
                            <a href="<?= BASE_URL ?>/login" class="ui-button ui-button-primary min-h-12">
                                Log in
                            </a>
                        </div>
                        <?php endif; ?>
                    </section>

                    <?php if (!empty($replies)): ?>
                    <?php foreach ($replies as $reply): ?>
                    <?php
                            $replyId = (int)$reply['id'];
                            $canEditReply = !empty($reply['can_edit']);
                            $canDeleteReply = !empty($reply['can_delete']);
                            $parentReplyUsername = trim((string)$reply['parent_author_username']);
                            $replyTargetUsername = trim((string)$reply['author_username']);

                            if ($replyTargetUsername === '') {
                                $replyTargetUsername = ltrim(trim((string)$reply['author_handle']), '@');
                            }
                            ?>
                    <article id="reply-<?= htmlspecialchars($reply['id'], ENT_QUOTES, 'UTF-8') ?>"
                        class="ui-card flex gap-3 p-5 sm:p-6" data-motion-item>
                        <?php
                                $replyRole = strtolower(trim((string)($reply['role'] ?? 'student')));
                                $replyRoleBadgeClass = $replyRole === 'admin' ? 'ui-badge-danger' : ($replyRole === 'tutor' ? 'ui-badge-tutor' : 'ui-badge-neutral');
                                ?>
                        <?php if (!empty($reply['author_avatar_url'])): ?>
                        <img src="<?= htmlspecialchars($reply['author_avatar_url'], ENT_QUOTES, 'UTF-8') ?>"
                            alt="<?= htmlspecialchars($reply['author_full_name'] . ' avatar', ENT_QUOTES, 'UTF-8') ?>"
                            class="size-11 shrink-0 rounded-full object-cover">
                        <?php else: ?>
                        <span
                            class="flex size-11 shrink-0 items-center justify-center rounded-full bg-ui-brand-soft text-sm font-semibold text-brand-royal"
                            aria-hidden="true">
                            <?= htmlspecialchars($reply['author_initial'], ENT_QUOTES, 'UTF-8') ?>
                        </span>
                        <?php endif; ?>
                        <div class="w-full">
                            <div class="flex flex-wrap items-start justify-between gap-3">
                                <div class="flex flex-wrap items-center gap-x-2 gap-y-1">
                                    <h3 class="font-semibold text-ui-ink " dir="auto">
                                        <?= htmlspecialchars($reply['author_full_name'], ENT_QUOTES, 'UTF-8') ?>
                                    </h3>
                                    <span class="ui-badge <?= $replyRoleBadgeClass ?>">
                                        <?= htmlspecialchars($reply['role'], ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                    <span class="text-sm text-ui-muted">
                                        <?= htmlspecialchars($reply['created_at_formatted'], ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                </div>
                                <?php
                                        $replyActionItems = [];

                                        if ($canEditReply) {
                                            $replyActionItems[] = ['label' => 'Edit', 'icon' => 'edit', 'tone' => 'brand', 'href' => $reply['edit_url'], 'modal_id' => 'reply-edit-modal-' . $replyId];
                                        }

                                        if ($canDeleteReply) {
                                            $replyActionItems[] = [
                                                'label' => 'Delete',
                                                'icon' => 'delete',
                                                'tone' => 'danger',
                                                'href' => $reply['delete_url'],
                                                'modal_id' => 'confirmation-modal',
                                                'confirm_title' => 'Delete reply?',
                                                'confirm_message' => 'This reply will be permanently removed from the discussion.',
                                                'confirm_detail' => (string)$reply['content'],
                                                'confirm_action' => (string)$reply['destroy_url'],
                                                'confirm_submit_label' => 'Delete',
                                            ];
                                        }

                                        $actionMenuConfig = ['id' => 'reply-actions-' . $replyId, 'label' => 'Open reply actions', 'items' => $replyActionItems];
                                        require ROOT_PATH . '/app/Views/components/action_menu.php';
                                        ?>
                            </div>
                            <?php if ($parentReplyUsername !== ''): ?>
                            <p class="mb-2 text-xs font-semibold text-ui-muted">
                                Replying to
                                @<?= htmlspecialchars($parentReplyUsername, ENT_QUOTES, 'UTF-8') ?>
                            </p>
                            <?php endif; ?>
                            <div class="grid gap-4 text-base leading-7 text-ui-ink">
                                <?php
                                        $contentSegments = $reply['content_segments'];
                                        require ROOT_PATH . '/app/Views/discussions/partials/content_segments.php';
                                        ?>
                            </div>
                            <?php foreach ($reply['attachments'] as $attachment): ?>
                            <figure class="mt-4 overflow-hidden rounded-2xl bg-ui-canvas ring-1 ring-ui-border">
                                <button type="button"
                                    class="block w-full cursor-zoom-in focus-visible:outline-2 focus-visible:outline-offset-[-3px] focus-visible:outline-brand-blue"
                                    aria-label="View <?= htmlspecialchars($attachment['name'], ENT_QUOTES, 'UTF-8') ?> full size"
                                    data-image-viewer-trigger data-image-viewer-group="reply-<?= $replyId ?>"
                                    data-image-viewer-src="<?= htmlspecialchars($attachment['url'], ENT_QUOTES, 'UTF-8') ?>"
                                    data-image-viewer-alt="<?= htmlspecialchars($attachment['name'], ENT_QUOTES, 'UTF-8') ?>">
                                    <img src="<?= htmlspecialchars($attachment['url'], ENT_QUOTES, 'UTF-8') ?>"
                                        alt="<?= htmlspecialchars($attachment['name'], ENT_QUOTES, 'UTF-8') ?>"
                                        class="max-h-[420px] w-full object-contain">
                                </button>
                            </figure>
                            <?php endforeach; ?>

                            <div class="flex">
                                <?php if ($isLoggedIn && $replyId > 0 && $replyTargetUsername !== ''): ?>
                                <button type="button"
                                    class="px-4 mt-3 inline-flex min-h-9 items-center rounded-lg text-sm font-semibold text-brand-royal transition duration-150 hover:bg-ui-brand-soft focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-blue"
                                    data-reply-target
                                    data-reply-id="<?= htmlspecialchars($replyId, ENT_QUOTES, 'UTF-8') ?>"
                                    data-reply-username="<?= htmlspecialchars($replyTargetUsername, ENT_QUOTES, 'UTF-8') ?>">
                                    <svg class="mr-2" fill="currentColor" height="16" icon-name="comment"
                                        viewBox="0 0 20 20" width="16" xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M10 1a9 9 0 00-9 9c0 1.947.79 3.58 1.935 4.957L.231 17.661A.784.784 0 00.785 19H10a9 9 0 009-9 9 9 0 00-9-9zm0 16.2H6.162c-.994.004-1.907.053-3.045.144l-.076-.188a36.981 36.981 0 002.328-2.087l-1.05-1.263C3.297 12.576 2.8 11.331 2.8 10c0-3.97 3.23-7.2 7.2-7.2s7.2 3.23 7.2 7.2-3.23 7.2-7.2 7.2z">
                                        </path>
                                    </svg>
                                    Reply
                                </button>
                                <?php if (!empty($reply['can_mark_as_solved'])): ?>
                                <form
                                    action="<?= htmlspecialchars($reply['mark_as_solved_url'], ENT_QUOTES, 'UTF-8') ?>"
                                    method="post" class="mt-4">
                                    <input type="hidden" name="_csrf_token"
                                        value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                                    <button type="submit"
                                        class="inline-flex min-h-10 items-center gap-2 rounded-lg px-4 py-2 text-sm font-semibold text-brand-royal transition hover:bg-ui-success-soft hover:text-ui-success focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-ui-success-border">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                            viewBox="0 0 512 512" role="img" aria-label="Verified badge icon">
                                            <path
                                                d="M 246.5 27.5 L 245.5 28.5 L 241.5 28.5 L 240.5 29.5 L 235.5 30.5 L 222.5 37.5 L 184.5 70.5 L 178.5 73.5 L 176.5 73.5 L 172.5 75.5 L 125.5 75.5 L 124.5 76.5 L 115.5 77.5 L 100.5 84.5 L 87.5 96.5 L 79.5 109.5 L 79.5 111.5 L 76.5 118.5 L 76.5 122.5 L 75.5 123.5 L 75.5 170.5 L 74.5 171.5 L 74.5 174.5 L 73.5 175.5 L 72.5 180.5 L 68.5 186.5 L 62.5 192.5 L 62.5 193.5 L 40.5 218.5 L 35.5 225.5 L 30.5 235.5 L 29.5 241.5 L 28.5 242.5 L 28.5 246.5 L 27.5 247.5 L 27.5 264.5 L 28.5 265.5 L 28.5 269.5 L 29.5 270.5 L 30.5 276.5 L 36.5 288.5 L 56.5 311.5 L 61.5 318.5 L 67.5 324.5 L 73.5 334.5 L 74.5 340.5 L 75.5 341.5 L 75.5 388.5 L 76.5 389.5 L 76.5 393.5 L 77.5 394.5 L 79.5 402.5 L 84.5 411.5 L 96.5 424.5 L 100.5 427.5 L 110.5 432.5 L 112.5 432.5 L 119.5 435.5 L 124.5 435.5 L 125.5 436.5 L 172.5 436.5 L 173.5 437.5 L 178.5 438.5 L 185.5 442.5 L 223.5 475.5 L 228.5 477.5 L 230.5 479.5 L 232.5 479.5 L 235.5 481.5 L 237.5 481.5 L 241.5 483.5 L 245.5 483.5 L 246.5 484.5 L 265.5 484.5 L 266.5 483.5 L 270.5 483.5 L 271.5 482.5 L 276.5 481.5 L 279.5 479.5 L 281.5 479.5 L 283.5 477.5 L 289.5 474.5 L 327.5 441.5 L 335.5 437.5 L 337.5 437.5 L 338.5 436.5 L 383.5 436.5 L 384.5 435.5 L 390.5 435.5 L 391.5 434.5 L 398.5 433.5 L 401.5 431.5 L 405.5 430.5 L 407.5 428.5 L 413.5 425.5 L 420.5 419.5 L 429.5 407.5 L 432.5 401.5 L 432.5 399.5 L 434.5 395.5 L 434.5 392.5 L 435.5 391.5 L 435.5 386.5 L 436.5 385.5 L 436.5 339.5 L 437.5 338.5 L 438.5 333.5 L 442.5 326.5 L 472.5 292.5 L 477.5 285.5 L 482.5 274.5 L 482.5 271.5 L 484.5 266.5 L 484.5 257.5 L 485.5 256.5 L 485.5 254.5 L 484.5 253.5 L 484.5 245.5 L 483.5 244.5 L 483.5 240.5 L 477.5 226.5 L 468.5 214.5 L 443.5 186.5 L 438.5 178.5 L 438.5 176.5 L 436.5 172.5 L 436.5 126.5 L 435.5 125.5 L 435.5 120.5 L 434.5 119.5 L 434.5 116.5 L 433.5 115.5 L 432.5 110.5 L 429.5 104.5 L 423.5 95.5 L 416.5 88.5 L 409.5 83.5 L 401.5 79.5 L 399.5 79.5 L 395.5 77.5 L 392.5 77.5 L 391.5 76.5 L 386.5 76.5 L 385.5 75.5 L 339.5 75.5 L 338.5 74.5 L 333.5 73.5 L 326.5 69.5 L 298.5 44.5 L 283.5 33.5 L 279.5 32.5 L 276.5 30.5 L 274.5 30.5 L 270.5 28.5 L 266.5 28.5 L 265.5 27.5 Z M 249.5 58.5 L 262.5 58.5 L 263.5 59.5 L 268.5 60.5 L 309.5 95.5 L 322.5 102.5 L 324.5 102.5 L 328.5 104.5 L 331.5 104.5 L 332.5 105.5 L 336.5 105.5 L 337.5 106.5 L 383.5 106.5 L 384.5 107.5 L 390.5 108.5 L 396.5 112.5 L 402.5 119.5 L 405.5 127.5 L 405.5 173.5 L 406.5 174.5 L 406.5 178.5 L 407.5 179.5 L 407.5 182.5 L 408.5 183.5 L 410.5 191.5 L 417.5 203.5 L 450.5 241.5 L 452.5 245.5 L 453.5 252.5 L 454.5 253.5 L 453.5 263.5 L 451.5 266.5 L 451.5 268.5 L 431.5 291.5 L 431.5 292.5 L 425.5 298.5 L 425.5 299.5 L 419.5 305.5 L 414.5 312.5 L 409.5 322.5 L 409.5 324.5 L 407.5 328.5 L 407.5 331.5 L 406.5 332.5 L 406.5 337.5 L 405.5 338.5 L 405.5 384.5 L 404.5 385.5 L 403.5 390.5 L 398.5 397.5 L 393.5 401.5 L 387.5 404.5 L 383.5 404.5 L 382.5 405.5 L 336.5 405.5 L 335.5 406.5 L 331.5 406.5 L 330.5 407.5 L 324.5 408.5 L 310.5 415.5 L 272.5 448.5 L 265.5 452.5 L 262.5 452.5 L 261.5 453.5 L 250.5 453.5 L 249.5 452.5 L 244.5 451.5 L 237.5 446.5 L 200.5 414.5 L 190.5 409.5 L 188.5 409.5 L 181.5 406.5 L 177.5 406.5 L 176.5 405.5 L 129.5 405.5 L 128.5 404.5 L 124.5 404.5 L 115.5 399.5 L 109.5 392.5 L 106.5 385.5 L 106.5 340.5 L 105.5 339.5 L 105.5 333.5 L 104.5 332.5 L 104.5 329.5 L 103.5 328.5 L 101.5 320.5 L 95.5 309.5 L 80.5 292.5 L 75.5 285.5 L 69.5 279.5 L 69.5 278.5 L 63.5 272.5 L 58.5 262.5 L 58.5 249.5 L 61.5 242.5 L 94.5 203.5 L 100.5 193.5 L 100.5 191.5 L 102.5 188.5 L 102.5 186.5 L 104.5 182.5 L 104.5 179.5 L 105.5 178.5 L 105.5 172.5 L 106.5 171.5 L 106.5 126.5 L 111.5 116.5 L 119.5 109.5 L 127.5 106.5 L 175.5 106.5 L 176.5 105.5 L 180.5 105.5 L 181.5 104.5 L 184.5 104.5 L 190.5 101.5 L 192.5 101.5 L 198.5 98.5 L 209.5 90.5 L 239.5 63.5 Z M 347.5 197.5 L 343.5 192.5 L 337.5 189.5 L 328.5 189.5 L 324.5 191.5 L 230.5 285.5 L 188.5 243.5 L 184.5 241.5 L 182.5 241.5 L 181.5 240.5 L 175.5 240.5 L 167.5 244.5 L 164.5 248.5 L 164.5 250.5 L 163.5 251.5 L 163.5 260.5 L 164.5 261.5 L 164.5 263.5 L 167.5 267.5 L 218.5 318.5 L 222.5 321.5 L 227.5 322.5 L 228.5 323.5 L 235.5 322.5 L 239.5 320.5 L 345.5 214.5 L 348.5 208.5 L 348.5 200.5 L 347.5 199.5 Z"
                                                fill="currentColor" fill-rule="evenodd" clip-rule="evenodd" />
                                        </svg>
                                        <span>Mark as solution</span>
                                    </button>
                                </form>
                                <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </article>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <div class="ui-empty-state">
                        <h3 class="font-semibold text-ui-ink">No comments yet</h3>
                        <p class="mt-2 text-sm leading-6 text-ui-text">Be the first to share a useful direction or
                            resource.</p>
                    </div>
                    <?php endif; ?>
                </section>


            </div>

            <!-- Discussion metadata -->
            <aside class="flex flex-col border-ui-border lg:sticky lg:top-28 lg:self-start lg:border-l lg:pl-8"
                data-motion-reveal aria-label="Discussion sidebar">
                <section class="ui-card p-5" aria-labelledby="about-discussion-heading">
                    <h2 id="about-discussion-heading" class="text-base font-semibold text-ui-ink">About this
                        discussion</h2>
                    <dl class="mt-4 grid gap-3 text-sm leading-6">
                        <div>
                            <dt class="font-medium text-ui-text">Module</dt>
                            <dd class="mt-1 font-semibold text-ui-ink ">
                                <?= htmlspecialchars($discussion['module_name'], ENT_QUOTES, 'UTF-8') ?>
                            </dd>
                        </div>
                        <div>
                            <dt class="font-medium text-ui-text">Category</dt>
                            <dd class="mt-1 font-semibold text-ui-ink">Coursework question</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-ui-text">Last activity</dt>
                            <dd class="mt-1 font-semibold text-ui-ink">
                                <?= htmlspecialchars($discussion['updated_at_formatted'], ENT_QUOTES, 'UTF-8') ?>
                            </dd>
                        </div>
                    </dl>
                </section>
            </aside>
        </div>

        <!-- Discussion edit modal -->
        <?php if (!empty($discussion['can_edit'])): ?>
        <dialog id="discussion-edit-modal" data-modal
            <?= $openModalId === 'discussion-edit-modal' ? 'data-initial-open="true"' : '' ?>
            class="m-auto w-[min(680px,calc(100vw-32px))] rounded-2xl bg-white p-0 text-ui-ink ring-1 ring-ui-border shadow-ui-overlay backdrop:bg-ui-ink/45">
            <div class="max-h-[min(760px,calc(100vh-48px))] overflow-y-auto p-5 sm:p-6">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 id="discussion-edit-modal-title" class="text-xl font-semibold leading-7 text-ui-ink">
                            Edit discussion
                        </h2>
                        <p class="mt-1 text-sm leading-6 text-ui-text">
                            Update the question details without leaving this discussion.
                        </p>
                    </div>
                    <button type="button" data-close-modal
                        class="inline-flex size-9 shrink-0 items-center justify-center rounded-full text-ui-text transition duration-150 hover:bg-ui-neutral-soft hover:text-ui-ink focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-blue"
                        aria-label="Close edit discussion modal">
                        <svg viewBox="0 0 20 20" class="size-4" fill="none" aria-hidden="true">
                            <path d="m5 5 10 10M15 5 5 15" stroke="currentColor" stroke-width="1.8"
                                stroke-linecap="round" />
                        </svg>
                    </button>
                </div>

                <?php if (!empty($discussionEditErrors['general'])): ?>
                <div class="mt-4 rounded-2xl bg-ui-danger-soft p-4 text-sm leading-6 text-ui-danger ring-1 ring-ui-danger-border"
                    role="alert">
                    <?= htmlspecialchars($discussionEditErrors['general'], ENT_QUOTES, 'UTF-8') ?>
                </div>
                <?php endif; ?>

                <form action="<?= htmlspecialchars($discussion['update_url'], ENT_QUOTES, 'UTF-8') ?>" method="post"
                    class="mt-5 grid gap-4" novalidate>
                    <input type="hidden" name="_csrf_token"
                        value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">

                    <div>
                        <label for="discussion-edit-title" class="block text-sm font-semibold text-ui-ink">Title</label>
                        <input id="discussion-edit-title" name="title" type="text"
                            value="<?= htmlspecialchars($discussionEditTitle, ENT_QUOTES, 'UTF-8') ?>"
                            aria-describedby="discussion-edit-title-error"
                            aria-invalid="<?= $fieldError($discussionEditErrors, 'title') !== '' ? 'true' : 'false' ?>"
                            class="ui-input mt-2 h-12 bg-ui-canvas text-base <?= $fieldRing($discussionEditErrors, 'title') ?>">
                        <p id="discussion-edit-title-error"
                            class="mt-2 <?= $fieldError($discussionEditErrors, 'title') === '' ? 'hidden' : 'block' ?> text-sm leading-5 text-ui-danger"
                            aria-live="polite">
                            <?= htmlspecialchars($fieldError($discussionEditErrors, 'title'), ENT_QUOTES, 'UTF-8') ?>
                        </p>
                    </div>

                    <div>
                        <label for="discussion-edit-module"
                            class="block text-sm font-semibold text-ui-ink">Module</label>
                        <select id="discussion-edit-module" name="module_id"
                            aria-describedby="discussion-edit-module-error"
                            aria-invalid="<?= $fieldError($discussionEditErrors, 'module_id') !== '' ? 'true' : 'false' ?>"
                            class="ui-input mt-2 h-12 bg-ui-canvas text-base <?= $fieldRing($discussionEditErrors, 'module_id') ?>">
                            <option value="">Select module</option>
                            <?php foreach ($modules as $module): ?>
                            <?php
                                    $moduleId = (string)($module['id'] ?? '');
                                    $moduleCode = (string)($module['code'] ?? 'MODULE');
                                    $moduleName = (string)($module['name'] ?? 'Module');
                                    ?>
                            <option value="<?= htmlspecialchars($moduleId, ENT_QUOTES, 'UTF-8') ?>"
                                <?= $discussionEditModuleId === $moduleId ? 'selected' : '' ?>>
                                <?= htmlspecialchars($moduleCode . ' - ' . $moduleName, ENT_QUOTES, 'UTF-8') ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                        <p id="discussion-edit-module-error"
                            class="mt-2 <?= $fieldError($discussionEditErrors, 'module_id') === '' ? 'hidden' : 'block' ?> text-sm leading-5 text-ui-danger"
                            aria-live="polite">
                            <?= htmlspecialchars($fieldError($discussionEditErrors, 'module_id'), ENT_QUOTES, 'UTF-8') ?>
                        </p>
                    </div>

                    <?php
                        $contentInputConfig = [
                            'variant' => 'edit-discussion',
                            'id' => 'discussion-edit-content',
                            'value' => $discussionEditContent,
                            'errors' => ['content' => $fieldError($discussionEditErrors, 'content')],
                        ];
                        require ROOT_PATH . '/app/Views/discussions/partials/content_input.php';
                        ?>

                    <div class="flex flex-wrap justify-end gap-3 pt-1">
                        <button type="button" data-close-modal class="ui-button ui-button-secondary">
                            Cancel
                        </button>
                        <button type="submit" class="ui-button ui-button-primary">
                            Save changes
                        </button>
                    </div>
                </form>
            </div>
        </dialog>
        <?php endif; ?>

        <!-- Reply edit modals -->
        <?php foreach ($modalReplies as $replyModal): ?>
        <?php
            $replyModalId = (int)$replyModal['id'];
            $replyModalDomId = htmlspecialchars($replyModalId, ENT_QUOTES, 'UTF-8');
            $isActiveReplyEdit = $activeReplyEditId === $replyModalId;
            $currentReplyErrors = $isActiveReplyEdit ? $replyEditErrors : [];
            $currentReplyContent = (string)$replyModal['content'];

            if ($isActiveReplyEdit && array_key_exists('content', $replyEditOld)) {
                $currentReplyContent = (string)$replyEditOld['content'];
            }
            ?>
        <?php if (!empty($replyModal['can_edit'])): ?>
        <dialog id="reply-edit-modal-<?= $replyModalDomId ?>" data-modal
            <?= $openModalId === 'reply-edit-modal-' . $replyModalId ? 'data-initial-open="true"' : '' ?>
            class="m-auto w-[min(640px,calc(100vw-32px))] rounded-2xl bg-white p-0 text-ui-ink ring-1 ring-ui-border shadow-ui-overlay backdrop:bg-ui-ink/45">
            <div class="p-5 sm:p-6">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 id="reply-edit-modal-title-<?= $replyModalDomId ?>"
                            class="text-xl font-semibold leading-7 text-ui-ink">Edit reply</h2>
                        <p class="mt-1 text-sm leading-6 text-ui-text">
                            Refine your reply while staying in the discussion context.
                        </p>
                    </div>
                    <button type="button" data-close-modal
                        class="inline-flex size-9 shrink-0 items-center justify-center rounded-full text-ui-text transition duration-150 hover:bg-ui-neutral-soft hover:text-ui-ink focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-blue"
                        aria-label="Close edit reply modal">
                        <svg viewBox="0 0 20 20" class="size-4" fill="none" aria-hidden="true">
                            <path d="m5 5 10 10M15 5 5 15" stroke="currentColor" stroke-width="1.8"
                                stroke-linecap="round" />
                        </svg>
                    </button>
                </div>

                <?php if (!empty($currentReplyErrors['general'])): ?>
                <div class="mt-4 rounded-2xl bg-ui-danger-soft p-4 text-sm leading-6 text-ui-danger ring-1 ring-ui-danger-border"
                    role="alert">
                    <?= htmlspecialchars($currentReplyErrors['general'], ENT_QUOTES, 'UTF-8') ?>
                </div>
                <?php endif; ?>

                <form action="<?= htmlspecialchars($replyModal['update_url'], ENT_QUOTES, 'UTF-8') ?>" method="post"
                    class="mt-5 grid gap-4">
                    <input type="hidden" name="_csrf_token"
                        value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                    <?php
                            $contentInputConfig = [
                                'variant' => 'edit-reply',
                                'id' => 'reply-edit-content-' . $replyModalId,
                                'value' => $currentReplyContent,
                                'errors' => ['content' => $fieldError($currentReplyErrors, 'content')],
                            ];
                            require ROOT_PATH . '/app/Views/discussions/partials/content_input.php';
                            ?>

                    <div class="flex flex-wrap justify-end gap-3">
                        <button type="button" data-close-modal class="ui-button ui-button-secondary">
                            Cancel
                        </button>
                        <button type="submit" class="ui-button ui-button-primary">
                            Save changes
                        </button>
                    </div>
                </form>
            </div>
        </dialog>
        <?php endif; ?>

        <?php endforeach; ?>

        <?php
        $confirmationModalConfig = ['csrf_token' => $csrfToken];

        if ($openModalId === 'discussion-delete-modal' && !empty($discussion['can_delete'])) {
            $confirmationModalConfig += [
                'title' => 'Delete discussion?',
                'message' => 'This discussion, its replies, and views will be permanently removed.',
                'detail' => (string)$discussion['title'],
                'action' => (string)$discussion['destroy_url'],
                'submit_label' => 'Delete',
                'initial_open' => true,
            ];
        } elseif (preg_match('/^reply-delete-modal-(\d+)$/', $openModalId, $deleteModalMatches) === 1) {
            $initialDeleteReplyId = (int)$deleteModalMatches[1];

            foreach ($modalReplies as $replyModal) {
                if ((int)($replyModal['id'] ?? 0) !== $initialDeleteReplyId || empty($replyModal['can_delete'])) {
                    continue;
                }

                $confirmationModalConfig += [
                    'title' => 'Delete reply?',
                    'message' => 'This reply will be permanently removed from the discussion.',
                    'detail' => (string)($replyModal['content'] ?? ''),
                    'action' => (string)($replyModal['destroy_url'] ?? ''),
                    'submit_label' => 'Delete',
                    'initial_open' => true,
                ];
                break;
            }
        }

        require ROOT_PATH . '/app/Views/components/confirmation_modal.php';
        ?>
    </div>
</section>