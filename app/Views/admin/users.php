<section class="min-h-screen bg-[#f6f7fb] px-5 py-8 text-[#172033] sm:px-8 lg:px-12">
    <div class="mx-auto flex max-w-7xl flex-col gap-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.14em] text-[#0b57d0]">Administration</p>
                <h1 class="mt-2 text-3xl font-bold tracking-tight">Manage Users</h1>
                <p class="mt-2 text-sm text-[#667085]">View, create, edit, and manage user accounts across the discussion platform.</p>
            </div>
            <a href="<?= BASE_URL ?>/admin/users/create"
                class="inline-flex min-h-11 w-fit items-center justify-center rounded-lg bg-[#0b57d0] px-5 text-sm font-semibold text-white hover:bg-[#0847ad]">
                Add user
            </a>
        </div>

        <?php require ROOT_PATH . '/app/Views/admin/partials/navigation.php'; ?>

        <section class="overflow-hidden rounded-xl border border-[#dfe5ef] bg-white shadow-sm">
            <div class="flex flex-col gap-2 border-b border-[#e8edf5] px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                <div><h2 class="text-base font-bold">All users</h2><p class="mt-1 text-xs text-[#7a8699]">Accounts with posts or replies cannot be deleted.</p></div>
                <span class="w-fit rounded-full bg-[#edf3ff] px-3 py-1 text-xs font-bold text-[#0b57d0]">5 users per page</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[820px] text-left text-sm">
                    <thead class="bg-[#f8faff] text-[11px] uppercase tracking-[0.08em] text-[#7a8699]">
                        <tr><th class="px-5 py-3">User</th><th class="px-5 py-3">Username</th><th class="px-5 py-3">Email</th><th class="px-5 py-3">Role</th><th class="px-5 py-3">Actions</th></tr>
                    </thead>
                    <tbody>
                    <?php if (!empty($users)): ?>
                        <?php foreach ($users as $user): ?>
                            <?php $role = strtolower((string) ($user['role'] ?? 'student')); ?>
                            <tr class="border-t border-[#edf0f5] hover:bg-[#fbfcff]">
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <span class="flex size-9 items-center justify-center rounded-full bg-[#e8f0ff] text-xs font-bold text-[#0b57d0]">
                                            <?= htmlspecialchars(strtoupper(substr((string) ($user['name'] ?? 'U'), 0, 1)), ENT_QUOTES, 'UTF-8') ?>
                                        </span>
                                        <div><span class="font-semibold text-[#172033]"><?= htmlspecialchars($user['name'] ?? '', ENT_QUOTES, 'UTF-8') ?></span><?php if ((int) ($user['id'] ?? 0) === (int) $currentUserId): ?><span class="ml-2 text-xs font-semibold text-[#0b57d0]">You</span><?php endif; ?></div>
                                    </div>
                                </td>
                                <td class="px-5 py-4 text-[#667085]">@<?= htmlspecialchars($user['username'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                                <td class="px-5 py-4 text-[#667085]"><?= htmlspecialchars($user['email'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                                <td class="px-5 py-4"><span class="rounded-full px-2.5 py-1 text-xs font-bold <?= $role === 'admin' ? 'bg-[#fdeceb] text-[#b3261e]' : ($role === 'tutor' ? 'bg-[#eee9fb] text-[#6646a3]' : 'bg-[#e8f0ff] text-[#0b57d0]') ?>"><?= htmlspecialchars(ucfirst($role), ENT_QUOTES, 'UTF-8') ?></span></td>
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <a href="<?= BASE_URL ?>/admin/users/edit/<?= (int) $user['id'] ?>" class="font-semibold text-[#0b57d0] hover:underline">Edit</a>
                                        <?php if ((int) ($user['id'] ?? 0) !== (int) $currentUserId): ?>
                                            <?php $confirmDeleteModalId = 'admin-delete-user-' . (int) $user['id']; ?>
                                            <button type="button" data-open-modal="<?= htmlspecialchars($confirmDeleteModalId, ENT_QUOTES, 'UTF-8') ?>"
                                                class="font-semibold text-[#c5221f] hover:underline">Delete</button>
                                            <?php
                                            $confirmDeleteTitle = 'Delete ' . ($user['name'] ?? 'this user') . '?';
                                            $confirmDeleteMessage = 'This account will be permanently deleted if it has no posts or replies.';
                                            $confirmDeleteUrl = BASE_URL . '/admin/users/delete/' . (int) $user['id'];
                                            require ROOT_PATH . '/app/Views/partials/confirm_delete_modal.php';
                                            ?>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="px-5 py-12 text-center text-[#7a8699]">No users found.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="px-5 pb-5"><?php require ROOT_PATH . '/app/Views/admin/partials/pagination.php'; ?></div>
        </section>
    </div>
</section>
