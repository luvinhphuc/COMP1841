<?php
/**
 * Variables passed from Admin\ModuleController::index()
 *
 * @var string $adminSection
 * @var array $modules
 * @var array $pagination
 * @var string $csrfToken
 */
?>

<section class="ui-page">
    <div class="ui-container flex flex-col gap-6">
        <div data-motion-intro>
            <p class="ui-eyebrow">Administration</p>
            <h1 class="ui-page-title">Manage modules</h1>
            <p class="ui-page-description mt-2 text-sm">Configure and oversee academic course modules.</p>
        </div>

        <?php require ROOT_PATH . '/app/Views/admin/partials/navigation.php'; ?>

        <div class="grid items-start gap-5 lg:grid-cols-[minmax(0,2fr)_340px]" data-motion-reveal>
            <section class="ui-table-shell">
                <div class="flex items-center justify-between border-b border-ui-border px-5 py-4">
                    <div>
                        <h2 class="text-base font-bold">Course modules</h2>
                        <p class="mt-1 text-xs text-ui-muted">Browse and manage all available course modules.</p>
                    </div>
                    <span class="rounded-full bg-ui-brand-soft px-3 py-1 text-xs font-bold text-brand-royal">Module
                        list</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[680px] text-left text-sm">
                        <thead class="ui-table-head">
                            <tr>
                                <th class="px-5 py-3 font-bold">Module code</th>
                                <th class="px-5 py-3 font-bold">Module name</th>
                                <th class="px-5 py-3 text-center font-bold">Posts</th>
                                <th class="px-5 py-3 text-center font-bold">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($modules)): ?>
                                <?php foreach ($modules as $module): ?>
                                    <tr class="ui-table-row">
                                        <td class="px-5 py-4"><span
                                                    class="rounded-md bg-ui-neutral-soft px-2 py-1 font-mono text-xs font-bold text-ui-text"><?= htmlspecialchars($module['code'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
                                        </td>
                                        <td class="px-5 py-4 font-semibold text-ui-ink">
                                            <?= htmlspecialchars($module['name'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                                        <td class="px-5 py-4 text-center text-ui-muted"><?= (int)$module['discussion_count'] ?></td>
                                        <td class="px-5 py-4 text-center">
                                            <?php
                                            $moduleId = (int)($module['id'] ?? 0);
                                            $moduleHasPosts = (int)($module['discussion_count'] ?? 0) > 0;
                                            $actionMenuConfig = [
                                                'id' => 'admin-module-actions-' . $moduleId,
                                                'label' => 'Open actions for ' . ($module['code'] ?? 'module'),
                                                'items' => [
                                                    ['label' => 'Edit', 'icon' => 'edit', 'tone' => 'brand', 'href' => BASE_URL . '/admin/modules/edit/' . $moduleId],
                                                    [
                                                        'label' => 'Delete',
                                                        'icon' => 'delete',
                                                        'tone' => 'danger',
                                                        'disabled' => $moduleHasPosts,
                                                        'title' => $moduleHasPosts ? 'Modules with posts cannot be deleted' : '',
                                                        'modal_id' => 'confirmation-modal',
                                                        'confirm_title' => 'Delete ' . ($module['code'] ?? 'this module') . '?',
                                                        'confirm_message' => 'This module will be permanently deleted. Modules with posts cannot be deleted.',
                                                        'confirm_detail' => trim((string)($module['code'] ?? '') . ' - ' . (string)($module['name'] ?? '')),
                                                        'confirm_action' => BASE_URL . '/admin/modules/delete/' . $moduleId,
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
                                    <td colspan="4" class="px-5 py-12 text-center text-ui-muted">No modules found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <?php
                $paginationConfig = ['item_label' => 'Modules', 'data' => $pagination];
                require ROOT_PATH . '/app/Views/components/pagination.php';
                unset($paginationConfig);
                ?>
            </section>
            <?php
            $confirmationModalConfig = ['csrf_token' => $csrfToken];
            require ROOT_PATH . '/app/Views/components/confirmation_modal.php';
            ?>

            <aside class="ui-card p-5">
                <h2 class="text-base font-bold">Add new module</h2>
                <p class="mt-1 text-xs text-ui-muted">Create a module for student discussions.</p>
                <form action="<?= BASE_URL ?>/admin/modules/store" method="post" class="mt-5 grid gap-4">
                    <input type="hidden" name="_csrf_token"
                           value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                    <label class="grid gap-2 text-xs font-bold uppercase tracking-[0.06em] text-ui-muted">Module code
                        <input name="code" required maxlength="20" placeholder="e.g. COMP1841"
                               class="ui-input font-normal normal-case tracking-normal">
                    </label>
                    <label class="grid gap-2 text-xs font-bold uppercase tracking-[0.06em] text-ui-muted">Module name
                        <input name="name" required maxlength="150" placeholder="Web Programming 1"
                               class="ui-input font-normal normal-case tracking-normal">
                    </label>
                    <label class="grid gap-2 text-xs font-bold uppercase tracking-[0.06em] text-ui-muted">Description
                        <textarea name="description" rows="4" placeholder="Brief overview of the module"
                                  class="ui-input resize-y font-normal normal-case tracking-normal"></textarea>
                    </label>
                    <button type="submit" class="ui-button ui-button-primary mt-1">Create module</button>
                </form>
            </aside>
        </div>
    </div>
</section>
