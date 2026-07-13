<section class="min-h-screen bg-[#f6f7fb] px-5 py-8 text-[#172033] sm:px-8 lg:px-12">
    <div class="mx-auto flex max-w-7xl flex-col gap-6">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.14em] text-[#0b57d0]">Administration</p>
            <h1 class="mt-2 text-3xl font-bold tracking-tight">Manage Posts</h1>
            <p class="mt-2 text-sm text-[#667085]">Review, filter, and moderate academic discussions across modules.</p>
        </div>
        <?php require ROOT_PATH . '/app/Views/admin/partials/navigation.php'; ?>

        <section class="rounded-xl border border-[#dfe5ef] bg-white p-5 shadow-sm">
            <div class="mb-4"><h2 class="text-sm font-bold">Filter discussions</h2><p class="mt-1 text-xs text-[#7a8699]">Narrow the table by keyword, status, or module.</p></div>
            <form action="<?= BASE_URL ?>/admin/posts" method="get" class="grid gap-3 md:grid-cols-[minmax(240px,2fr)_1fr_1fr_auto] md:items-end">
                <label class="grid gap-2 text-xs font-bold uppercase tracking-[0.06em] text-[#667085]">Keyword
                    <input name="q" value="<?= htmlspecialchars($filters['q'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="Search posts..." class="h-10 rounded-lg border border-[#d6deea] px-3 text-sm font-normal normal-case tracking-normal text-[#172033] focus:border-[#0b57d0] focus:outline-none">
                </label>
                <label class="grid gap-2 text-xs font-bold uppercase tracking-[0.06em] text-[#667085]">Status
                    <select name="status" class="h-10 rounded-lg border border-[#d6deea] bg-white px-3 text-sm font-normal normal-case tracking-normal text-[#172033]"><option value="">All statuses</option><option value="open" <?= ($filters['status'] ?? '') === 'open' ? 'selected' : '' ?>>Open</option><option value="solved" <?= ($filters['status'] ?? '') === 'solved' ? 'selected' : '' ?>>Solved</option></select>
                </label>
                <label class="grid gap-2 text-xs font-bold uppercase tracking-[0.06em] text-[#667085]">Module
                    <select name="module" class="h-10 rounded-lg border border-[#d6deea] bg-white px-3 text-sm font-normal normal-case tracking-normal text-[#172033]"><option value="">All modules</option><?php foreach ($modules as $module): ?><option value="<?= htmlspecialchars($module['code'] ?? '', ENT_QUOTES, 'UTF-8') ?>" <?= ($filters['module'] ?? '') === ($module['code'] ?? '') ? 'selected' : '' ?>><?= htmlspecialchars($module['code'] ?? '', ENT_QUOTES, 'UTF-8') ?></option><?php endforeach; ?></select>
                </label>
                <div class="flex gap-2"><button type="submit" class="h-10 rounded-lg bg-[#0b57d0] px-4 text-sm font-semibold text-white hover:bg-[#0847ad]">Apply filters</button><a href="<?= BASE_URL ?>/admin/posts" class="flex h-10 items-center rounded-lg border border-[#d6deea] px-3 text-sm font-semibold text-[#667085] hover:border-[#0b57d0]">Clear</a></div>
            </form>
        </section>

        <section class="overflow-hidden rounded-xl border border-[#dfe5ef] bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-[#e8edf5] px-5 py-4"><div><h2 class="text-base font-bold">Discussion posts</h2><p class="mt-1 text-xs text-[#7a8699]">Five posts are shown per page.</p></div><span class="rounded-full bg-[#edf3ff] px-3 py-1 text-xs font-bold text-[#0b57d0]">Moderation</span></div>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[920px] text-left text-sm">
                    <thead class="bg-[#f8faff] text-[11px] uppercase tracking-[0.08em] text-[#7a8699]"><tr><th class="px-5 py-3">Post title</th><th class="px-5 py-3">Author</th><th class="px-5 py-3">Module</th><th class="px-5 py-3">Status</th><th class="px-5 py-3">Actions</th></tr></thead>
                    <tbody>
                    <?php if (!empty($posts)): ?>
                        <?php foreach ($posts as $post): ?>
                            <?php $postUrl = \App\Helpers\FormatHelper::discussionDetailUrl($post['id'] ?? 0, $post['slug'] ?? ''); $isSolved = ($post['status'] ?? 'open') === 'solved'; ?>
                            <tr class="border-t border-[#edf0f5] hover:bg-[#fbfcff]">
                                <td class="max-w-md px-5 py-4 font-semibold text-[#172033]"><?= htmlspecialchars($post['title'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                                <td class="px-5 py-4 text-[#667085]"><?= htmlspecialchars($post['username'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                                <td class="px-5 py-4"><span class="rounded-md bg-[#eef2f7] px-2 py-1 font-mono text-xs font-bold text-[#475467]"><?= htmlspecialchars($post['module_code'] ?? '', ENT_QUOTES, 'UTF-8') ?></span></td>
                                <td class="px-5 py-4"><span class="rounded-full px-2.5 py-1 text-xs font-bold <?= $isSolved ? 'bg-[#e6f4ea] text-[#137333]' : 'bg-[#e8f0ff] text-[#0b57d0]' ?>"><?= $isSolved ? 'Solved' : 'Open' ?></span></td>
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <a href="<?= htmlspecialchars($postUrl, ENT_QUOTES, 'UTF-8') ?>" class="font-semibold text-[#0b57d0] hover:underline">View</a>
                                        <form action="<?= BASE_URL ?>/admin/posts/status/<?= (int) $post['id'] ?>" method="post">
                                            <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                                            <input type="hidden" name="status" value="<?= $isSolved ? 'open' : 'solved' ?>">
                                            <button type="submit" class="font-semibold text-[#0b57d0] hover:underline">Mark <?= $isSolved ? 'open' : 'solved' ?></button>
                                        </form>
                                        <?php $confirmDeleteModalId = 'admin-delete-post-' . (int) $post['id']; ?>
                                        <button type="button" data-open-modal="<?= htmlspecialchars($confirmDeleteModalId, ENT_QUOTES, 'UTF-8') ?>"
                                            class="font-semibold text-[#c5221f] hover:underline">Delete</button>
                                        <?php
                                        $confirmDeleteTitle = 'Delete this discussion?';
                                        $confirmDeleteMessage = 'This discussion will be removed from the public list together with its replies and attachments.';
                                        $confirmDeleteUrl = BASE_URL . '/admin/posts/delete/' . (int) $post['id'];
                                        require ROOT_PATH . '/app/Views/partials/confirm_delete_modal.php';
                                        ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="px-5 py-12 text-center text-[#7a8699]">No posts match these filters.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="px-5 pb-5"><?php require ROOT_PATH . '/app/Views/admin/partials/pagination.php'; ?></div>
        </section>
    </div>
</section>
