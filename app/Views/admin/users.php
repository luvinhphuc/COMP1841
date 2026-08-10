<?php
/**
 * Variables passed from Admin\UserController::index()
 *
 * @var string $adminSection
 * @var array $targetUserRecords
 * @var array $pagination
 * @var int $authUserId
 * @var string $csrfToken
 */
?>

<section class="ui-page">
    <div class="ui-container flex flex-col gap-6">
        <div>
            <div data-motion-intro>
                <p class="ui-eyebrow">Administration</p>
                <h1 class="ui-page-title">Manage users</h1>
                <p class="ui-page-description mt-2 text-sm">View, edit, and manage user accounts across the discussion
                    platform.</p>
            </div>
        </div>

        <?php require ROOT_PATH . '/app/Views/admin/partials/navigation.php'; ?>

        <section class="ui-table-shell" data-motion-reveal>
            <div
                    class="flex flex-col gap-2 border-b border-ui-border px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-base font-bold">All users</h2>
                    <p class="mt-1 text-xs text-ui-muted">Deleted accounts are anonymized while their posts and replies
                        remain available.</p>
                </div>
                <span class="w-fit rounded-full bg-ui-brand-soft px-3 py-1 text-xs font-bold text-brand-royal">User
                    directory</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[820px] text-left text-sm">
                    <thead class="ui-table-head">
                        <tr>
                            <th class="px-5 py-3">User</th>
                            <th class="px-5 py-3">Username</th>
                            <th class="px-5 py-3">Email</th>
                            <th class="px-5 py-3 text-center">Role</th>
                            <th class="px-5 py-3 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($targetUserRecords)): ?>
                            <?php foreach ($targetUserRecords as $targetUserRecord): ?>
                                <?php $targetUserRole = strtolower((string)($targetUserRecord['role'] ?? 'student')); ?>
                                <tr class="ui-table-row">
                                    <td class="px-5 py-4">
                                        <div class="flex items-center gap-3">
                                    <span
                                            class="flex size-9 items-center justify-center rounded-full bg-ui-brand-soft text-xs font-bold text-brand-royal">
                                        <?= htmlspecialchars(strtoupper(substr((string)($targetUserRecord['full_name'] ?? 'U'), 0, 1)), ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                            <div><span
                                                        class="font-semibold text-ui-ink"><?= htmlspecialchars($targetUserRecord['full_name'] ?? '', ENT_QUOTES, 'UTF-8') ?></span><?php if ((int)($targetUserRecord['id'] ?? 0) === (int)$authUserId): ?>
                                                    <span
                                                            class="ml-2 text-xs font-semibold text-brand-royal">You</span><?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4 text-ui-muted">
                                        @<?= htmlspecialchars($targetUserRecord['username'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                                    <td class="px-5 py-4 text-ui-muted">
                                        <?= htmlspecialchars($targetUserRecord['email'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                                    <td class="px-5 py-4 text-center"><span
                                                class="ui-badge <?= $targetUserRole === 'admin' ? 'ui-badge-danger' : ($targetUserRole === 'tutor' ? 'ui-badge-tutor' : 'ui-badge-brand') ?>"><?= htmlspecialchars(ucfirst($targetUserRole), ENT_QUOTES, 'UTF-8') ?></span>
                                    </td>
                                    <td class="px-5 py-4 text-center">
                                        <?php
                                        $targetUserId = (int)($targetUserRecord['id'] ?? 0);
                                        $targetUserActionItems = [
                                            ['label' => 'Edit', 'icon' => 'edit', 'tone' => 'brand', 'href' => BASE_URL . '/admin/users/edit/' . $targetUserId],
                                        ];
                                        if ($targetUserId !== (int)$authUserId) {
                                            $targetUserActionItems[] = [
                                                'label' => 'Delete',
                                                'icon' => 'delete',
                                                'tone' => 'danger',
                                                'modal_id' => 'confirmation-modal',
                                                'confirm_title' => 'Delete ' . ($targetUserRecord['full_name'] ?? 'this user') . '?',
                                                'confirm_message' => 'This account will be permanently anonymized and cannot be restored. Its posts and replies will remain available.',
                                                'confirm_action' => BASE_URL . '/admin/users/delete/' . $targetUserId,
                                                'confirm_submit_label' => 'Delete',
                                            ];
                                        }
                                        $actionMenuConfig = [
                                            'id' => 'admin-user-actions-' . $targetUserId,
                                            'label' => 'Open actions for ' . ($targetUserRecord['full_name'] ?? 'user'),
                                            'items' => $targetUserActionItems,
                                        ];
                                        require ROOT_PATH . '/app/Views/components/action_menu.php';
                                        unset($actionMenuConfig, $targetUserActionItems);
                                        ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="px-5 py-12 text-center text-ui-muted">No users found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php
            $paginationConfig = ['item_label' => 'Users', 'data' => $pagination];
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
