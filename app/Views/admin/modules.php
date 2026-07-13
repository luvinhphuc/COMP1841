<section class="min-h-screen bg-[#f6f7fb] px-5 py-8 text-[#172033] sm:px-8 lg:px-12">
    <div class="mx-auto flex max-w-7xl flex-col gap-6">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.14em] text-[#0b57d0]">Administration</p>
            <h1 class="mt-2 text-3xl font-bold tracking-tight">Manage Modules</h1>
            <p class="mt-2 text-sm text-[#667085]">Configure and oversee academic course modules.</p>
        </div>

        <?php require ROOT_PATH . '/app/Views/admin/partials/navigation.php'; ?>

        <div class="grid items-start gap-5 lg:grid-cols-[minmax(0,2fr)_340px]">
            <section class="overflow-hidden rounded-xl border border-[#dfe5ef] bg-white shadow-sm">
                <div class="flex items-center justify-between border-b border-[#e8edf5] px-5 py-4">
                    <div>
                        <h2 class="text-base font-bold">Course modules</h2>
                        <p class="mt-1 text-xs text-[#7a8699]">Five modules are shown per page.</p>
                    </div>
                    <span class="rounded-full bg-[#edf3ff] px-3 py-1 text-xs font-bold text-[#0b57d0]">Module list</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[680px] text-left text-sm">
                        <thead class="bg-[#f8faff] text-[11px] uppercase tracking-[0.08em] text-[#7a8699]">
                            <tr><th class="px-5 py-3 font-bold">Module code</th><th class="px-5 py-3 font-bold">Module name</th><th class="px-5 py-3 font-bold">Posts</th><th class="px-5 py-3 font-bold">Actions</th></tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($modules)): ?>
                                <?php foreach ($modules as $module): ?>
                                    <tr class="border-t border-[#edf0f5] hover:bg-[#fbfcff]">
                                        <td class="px-5 py-4"><span class="rounded-md bg-[#eef2f7] px-2 py-1 font-mono text-xs font-bold text-[#475467]"><?= htmlspecialchars($module['code'] ?? '', ENT_QUOTES, 'UTF-8') ?></span></td>
                                        <td class="px-5 py-4 font-semibold text-[#172033]"><?= htmlspecialchars($module['name'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                                        <td class="px-5 py-4 text-[#667085]"><?= (int) ($module['post_count'] ?? 0) ?></td>
                                        <td class="px-5 py-4">
                                            <div class="flex items-center gap-3">
                                                <a href="<?= BASE_URL ?>/admin/modules/edit/<?= (int) $module['id'] ?>" class="font-semibold text-[#0b57d0] hover:underline">Edit</a>
                                                <?php $confirmDeleteModalId = 'admin-delete-module-' . (int) $module['id']; ?>
                                                <button type="button" data-open-modal="<?= htmlspecialchars($confirmDeleteModalId, ENT_QUOTES, 'UTF-8') ?>"
                                                    class="font-semibold text-[#c5221f] hover:underline disabled:cursor-not-allowed disabled:text-[#b3bac5]" <?= (int) ($module['post_count'] ?? 0) > 0 ? 'disabled title="Modules with posts cannot be deleted"' : '' ?>>Delete</button>
                                                <?php if ((int) ($module['post_count'] ?? 0) === 0): ?>
                                                    <?php
                                                    $confirmDeleteTitle = 'Delete ' . ($module['code'] ?? 'this module') . '?';
                                                    $confirmDeleteMessage = 'This module will be permanently deleted. Modules with posts cannot be deleted.';
                                                    $confirmDeleteUrl = BASE_URL . '/admin/modules/delete/' . (int) $module['id'];
                                                    require ROOT_PATH . '/app/Views/partials/confirm_delete_modal.php';
                                                    ?>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="4" class="px-5 py-12 text-center text-[#7a8699]">No modules found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <div class="px-5 pb-5"><?php require ROOT_PATH . '/app/Views/admin/partials/pagination.php'; ?></div>
            </section>

            <aside class="rounded-xl border border-[#dfe5ef] bg-white p-5 shadow-sm">
                <h2 class="text-base font-bold">Add new module</h2>
                <p class="mt-1 text-xs text-[#7a8699]">Create a module for student discussions.</p>
                <form action="<?= BASE_URL ?>/admin/modules/store" method="post" class="mt-5 grid gap-4">
                    <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                    <label class="grid gap-2 text-xs font-bold uppercase tracking-[0.06em] text-[#667085]">Module code
                        <input name="code" required maxlength="20" placeholder="e.g. COMP1841" class="h-11 rounded-lg border border-[#d6deea] px-3 text-sm font-normal normal-case tracking-normal text-[#172033] focus:border-[#0b57d0] focus:outline-none">
                    </label>
                    <label class="grid gap-2 text-xs font-bold uppercase tracking-[0.06em] text-[#667085]">Module name
                        <input name="name" required maxlength="150" placeholder="Web Programming 1" class="h-11 rounded-lg border border-[#d6deea] px-3 text-sm font-normal normal-case tracking-normal text-[#172033] focus:border-[#0b57d0] focus:outline-none">
                    </label>
                    <label class="grid gap-2 text-xs font-bold uppercase tracking-[0.06em] text-[#667085]">Description
                        <textarea name="description" rows="4" placeholder="Brief overview of the module" class="rounded-lg border border-[#d6deea] px-3 py-2 text-sm font-normal normal-case tracking-normal text-[#172033] focus:border-[#0b57d0] focus:outline-none"></textarea>
                    </label>
                    <button type="submit" class="mt-1 h-11 rounded-lg bg-[#0b57d0] px-5 text-sm font-semibold text-white hover:bg-[#0847ad]">Create module</button>
                </form>
            </aside>
        </div>
    </div>
</section>
