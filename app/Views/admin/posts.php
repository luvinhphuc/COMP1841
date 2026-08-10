<?php
/**
 * Variables passed from Admin\PostController::index()
 *
 * @var string $adminSection
 * @var array $discussions
 * @var array $modules
 * @var array $filters
 * @var array $pagination
 * @var string $csrfToken
 */
?>

<section class="ui-page">
    <div class="ui-container flex flex-col gap-6">
        <div data-motion-intro>
            <p class="ui-eyebrow">Administration</p>
            <h1 class="ui-page-title">Manage discussions</h1>
            <p class="ui-page-description mt-2 text-sm">Review, filter, and moderate academic discussions across
                modules.</p>
        </div>
        <?php require ROOT_PATH . '/app/Views/admin/partials/navigation.php'; ?>

        <section class="ui-card p-5" data-motion-reveal>
            <div class="mb-4">
                <h2 class="text-sm font-bold">Filter discussions</h2>
                <p class="mt-1 text-xs text-ui-muted">Narrow the table by keyword, status, or module.</p>
            </div>
            <form action="<?= BASE_URL ?>/admin/posts" method="get"
                  class="grid gap-3 md:grid-cols-[minmax(240px,2fr)_1fr_1fr_auto] md:items-end">
                <input type="hidden" name="per_page" value="<?= (int)($pagination['per_page'] ?? 5) ?>">
                <label class="grid gap-2 text-xs font-bold uppercase tracking-[0.06em] text-ui-muted">Keyword
                    <input name="q" value="<?= htmlspecialchars($filters['q'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                           placeholder="Search discussions..."
                           class="ui-input min-h-10 py-1 font-normal normal-case tracking-normal">
                </label>
                <label class="grid gap-2 text-xs font-bold uppercase tracking-[0.06em] text-ui-muted">Status
                    <select name="status" class="ui-input min-h-10 py-1 font-normal normal-case tracking-normal">
                        <option value="">All statuses</option>
                        <option value="open" <?= ($filters['status'] ?? '') === 'open' ? 'selected' : '' ?>>Open
                        </option>
                        <option value="solved" <?= ($filters['status'] ?? '') === 'solved' ? 'selected' : '' ?>>Solved
                        </option>
                    </select>
                </label>
                <label class="grid gap-2 text-xs font-bold uppercase tracking-[0.06em] text-ui-muted">Module
                    <select name="module" class="ui-input min-h-10 py-1 font-normal normal-case tracking-normal">
                        <option value="">All modules</option><?php foreach ($modules as $module): ?>
                            <option
                                    value="<?= htmlspecialchars($module['code'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                <?= ($filters['module'] ?? '') === ($module['code'] ?? '') ? 'selected' : '' ?>>
                                <?= htmlspecialchars($module['code'] ?? '', ENT_QUOTES, 'UTF-8') ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <div class="flex gap-2">
                    <button type="submit" class="ui-button ui-button-primary">Apply
                        filters
                    </button>
                    <a href="<?= BASE_URL ?>/admin/posts"
                       class="ui-button ui-button-secondary">Clear</a></div>
            </form>
        </section>

        <section class="ui-table-shell" data-motion-reveal>
            <div class="flex items-center justify-between border-b border-ui-border px-5 py-4">
                <div>
                    <h2 class="text-base font-bold">Discussions</h2>
                    <p class="mt-1 text-xs text-ui-muted">Browse and moderate all matching discussions.</p>
                </div>
                <span
                        class="rounded-full bg-ui-brand-soft px-3 py-1 text-xs font-bold text-brand-royal">Moderation</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[920px] text-left text-sm">
                    <thead class="ui-table-head">
                        <tr>
                            <th class="px-5 py-3 ">Discussion title</th>
                            <th class="px-5 py-3 text-center">Author</th>
                            <th class="px-5 py-3 text-center">Module</th>
                            <th class="px-5 py-3 text-center">Status</th>
                            <th class="px-5 py-3 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($discussions)): ?>
                            <?php foreach ($discussions as $discussion): ?>
                                <?php $discussionUrl = \App\Helpers\FormatHelper::discussionDetailUrl($discussion['id'] ?? 0, $discussion['slug'] ?? '');
                                $isDiscussionSolved = ($discussion['status'] ?? 'open') === 'solved'; ?>
                                <tr class="ui-table-row">
                                    <td class="max-w-md px-5 py-4 font-semibold text-ui-ink">
                                        <?= htmlspecialchars($discussion['title'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                                    <td class="px-5 py-4 text-center text-ui-muted">
                                        <?= htmlspecialchars($discussion['username'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                                    <td class="px-5 py-4 text-center"><span
                                                class="rounded-md bg-ui-neutral-soft px-2 py-1 font-mono text-xs font-bold text-ui-text"><?= htmlspecialchars($discussion['module_code'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
                                    </td>
                                    <td class="px-5 py-4 text-center"><span
                                                class="ui-badge <?= $isDiscussionSolved ? 'ui-badge-success' : 'ui-badge-brand' ?>"><?= $isDiscussionSolved ? 'Solved' : 'Open' ?></span>
                                    </td>
                                    <td class="px-5 py-4 text-center">
                                        <?php
                                        $discussionId = (int)($discussion['id'] ?? 0);
                                        $actionMenuConfig = [
                                            'id' => 'admin-discussion-actions-' . $discussionId,
                                            'label' => 'Open actions for ' . ($discussion['title'] ?? 'discussion'),
                                            'items' => [
                                                ['label' => 'View', 'icon' => 'view', 'tone' => 'brand', 'href' => $discussionUrl],
                                                [
                                                    'label' => 'Delete',
                                                    'icon' => 'delete',
                                                    'tone' => 'danger',
                                                    'modal_id' => 'confirmation-modal',
                                                    'confirm_title' => 'Delete this discussion?',
                                                    'confirm_message' => 'This discussion will be removed together with its replies and attachments.',
                                                    'confirm_detail' => (string)($discussion['title'] ?? ''),
                                                    'confirm_action' => BASE_URL . '/admin/posts/delete/' . $discussionId,
                                                    'confirm_submit_label' => 'Delete',
                                                ],
                                            ],
                                        ];
                                        require ROOT_PATH . '/app/Views/components/action_menu.php';
                                        unset($actionMenuConfig);
                                        ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="px-5 py-12 text-center text-ui-muted">No discussions match these
                                    filters.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php
            $paginationConfig = ['item_label' => 'Discussions', 'data' => $pagination];
            require ROOT_PATH . '/app/Views/components/pagination.php';
            unset($paginationConfig);
            ?>
        </section>
        <?php
        $confirmationModalConfig = ['csrf_token' => $csrfToken];
        require ROOT_PATH . '/app/Views/components/confirmation_modal.php';
        ?>
    </div>
</section>
