<?php
/**
 * Variables passed from Admin\ContactController::index()
 *
 * @var string $adminSection
 * @var array $contacts
 * @var array $filters
 * @var array $pagination
 * @var bool $loadError
 * @var string $csrfToken
 */

$filters = is_array($filters ?? null) ? $filters : [];
$contacts = is_array($contacts ?? null) ? $contacts : [];
$hasActiveFilters = trim((string)($filters['q'] ?? '')) !== ''
    || trim((string)($filters['status'] ?? '')) !== '';
$statusLabels = [
    'unread' => 'Unread',
    'read' => 'Read',
    'resolved' => 'Resolved',
];
$statusClasses = [
    'unread' => 'ui-badge-brand',
    'read' => 'ui-badge-neutral',
    'resolved' => 'ui-badge-success',
];
?>

<section class="ui-page">
    <div class="ui-container flex flex-col gap-6">
        <div data-motion-intro>
            <p class="ui-eyebrow">Administration</p>
            <h1 class="ui-page-title">Contact messages</h1>
            <p class="ui-page-description mt-2 text-sm">Review messages submitted by guests and registered users.</p>
        </div>

        <?php require ROOT_PATH . '/app/Views/admin/partials/navigation.php'; ?>

        <section class="ui-card p-5" data-motion-reveal>
            <div class="mb-4">
                <h2 class="text-sm font-bold">Filter contact messages</h2>
                <p class="mt-1 text-xs text-ui-muted">Search by sender name, email, or subject.</p>
            </div>
            <form action="<?= BASE_URL ?>/admin/contacts" method="get"
                  class="grid gap-3 md:grid-cols-[minmax(240px,2fr)_1fr_auto] md:items-end">
                <input type="hidden" name="per_page" value="<?= (int)($pagination['per_page'] ?? 5) ?>">
                <label class="grid gap-2 text-xs font-bold uppercase tracking-[0.06em] text-ui-muted">
                    Keyword
                    <input name="q" value="<?= htmlspecialchars($filters['q'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                           placeholder="Search messages..."
                           class="ui-input min-h-10 py-1 font-normal normal-case tracking-normal">
                </label>
                <label class="grid gap-2 text-xs font-bold uppercase tracking-[0.06em] text-ui-muted">
                    Status
                    <select name="status" class="ui-input min-h-10 py-1 font-normal normal-case tracking-normal">
                        <option value="">All statuses</option>
                        <?php foreach ($statusLabels as $status => $label): ?>
                            <option value="<?= htmlspecialchars($status, ENT_QUOTES, 'UTF-8') ?>"
                                <?= ($filters['status'] ?? '') === $status ? 'selected' : '' ?>>
                                <?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <div class="flex gap-2">
                    <button type="submit" class="ui-button ui-button-primary">Apply filters</button>
                    <?php if ($hasActiveFilters): ?>
                        <a href="<?= BASE_URL ?>/admin/contacts" class="ui-button ui-button-secondary">Clear</a>
                    <?php endif; ?>
                </div>
            </form>
        </section>

        <section class="ui-table-shell" data-motion-reveal>
            <div
                    class="flex flex-col gap-2 border-b border-ui-border px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-base font-bold">Submitted messages</h2>
                    <p class="mt-1 text-xs text-ui-muted">Newest contact messages appear first.</p>
                </div>
                <span class="w-fit rounded-full bg-ui-brand-soft px-3 py-1 text-xs font-bold text-brand-royal">Contact
                    inbox</span>
            </div>

            <?php if (!empty($loadError)): ?>
                <div class="m-5 ui-alert ui-alert-error" role="alert">
                    Contact messages could not be loaded right now. Please try again.
                </div>
            <?php endif; ?>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[980px] text-left text-sm">
                    <thead class="ui-table-head">
                        <tr>
                            <th class="px-5 py-3 text-center">Sender</th>
                            <th class="px-5 py-3 text-center">Subject</th>
                            <th class="px-5 py-3 text-center">Account</th>
                            <th class="px-5 py-3 text-center">Status</th>
                            <th class="px-5 py-3 text-center">Submitted</th>
                            <th class="px-5 py-3 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($contacts) && empty($loadError)): ?>
                            <?php foreach ($contacts as $contact): ?>
                                <?php
                                $contactId = (int)($contact['id'] ?? 0);
                                $status = array_key_exists($contact['status'] ?? '', $statusLabels)
                                    ? $contact['status']
                                    : 'unread';
                                $accountId = (int)($contact['account_id'] ?? 0);
                                $senderName = trim((string)($contact['name'] ?? ''));
                                $senderEmail = trim((string)($contact['email'] ?? ''));
                                ?>
                                <tr class="ui-table-row">
                                    <td class="max-w-xs px-5 py-4">
                                        <p class="font-semibold text-ui-ink">
                                            <?= htmlspecialchars($senderName !== '' ? $senderName : 'Unknown sender', ENT_QUOTES, 'UTF-8') ?>
                                        </p>
                                        <p class="mt-1 text-xs text-ui-muted">
                                            <?= htmlspecialchars($senderEmail, ENT_QUOTES, 'UTF-8') ?>
                                        </p>
                                    </td>
                                    <td class="max-w-sm px-5 py-4 font-semibold text-ui-ink">
                                        <a href="<?= BASE_URL ?>/admin/contacts/show/<?= $contactId ?>"
                                           class="hover:text-brand-royal hover:underline focus-visible:rounded-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-royal focus-visible:ring-offset-2">
                                            <?= htmlspecialchars($contact['subject'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                                        </a>
                                    </td>
                                    <td class="px-5 py-4 text-center">
                                        <?php if ($accountId > 0): ?>
                                            <span class="ui-badge ui-badge-brand">Registered</span>
                                            <span class="mt-1 block text-xs text-ui-muted">
                                    <?= htmlspecialchars($contact['account_username'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                                </span>
                                        <?php else: ?>
                                            <span class="ui-badge ui-badge-neutral">Guest</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-5 py-4 text-center">
                                <span
                                        class="ui-badge <?= htmlspecialchars($statusClasses[$status], ENT_QUOTES, 'UTF-8') ?>">
                                    <?= htmlspecialchars($statusLabels[$status], ENT_QUOTES, 'UTF-8') ?>
                                </span>
                                    </td>
                                    <td class="whitespace-nowrap px-5 py-4 text-center text-ui-muted">
                                        <?= htmlspecialchars($contact['created_at'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                                    </td>
                                    <td class="px-5 py-4 text-center">
                                        <?php
                                        $contactActionItems = [];
                                        if ($status === 'unread') {
                                            $contactActionItems[] = [
                                                'label' => 'Mark read',
                                                'icon' => 'check',
                                                'tone' => 'brand',
                                                'action' => BASE_URL . '/admin/contacts/read/' . $contactId,
                                                'csrf_token' => $csrfToken,
                                            ];
                                        }
                                        if ($status !== 'resolved') {
                                            $contactActionItems[] = [
                                                'label' => 'Resolve',
                                                'icon' => 'check',
                                                'tone' => 'success',
                                                'action' => BASE_URL . '/admin/contacts/status/' . $contactId,
                                                'csrf_token' => $csrfToken,
                                                'fields' => ['status' => 'resolved'],
                                            ];
                                        }
                                        $contactActionItems[] = [
                                            'label' => 'Delete',
                                            'icon' => 'delete',
                                            'tone' => 'danger',
                                            'modal_id' => 'confirmation-modal',
                                            'confirm_title' => 'Delete this contact message?',
                                            'confirm_message' => 'This contact message will be permanently deleted.',
                                            'confirm_detail' => (string)($contact['subject'] ?? ''),
                                            'confirm_action' => BASE_URL . '/admin/contacts/delete/' . $contactId,
                                            'confirm_submit_label' => 'Delete',
                                        ];
                                        $actionMenuConfig = [
                                            'id' => 'admin-contact-actions-' . $contactId,
                                            'label' => 'Open actions for contact message',
                                            'items' => $contactActionItems,
                                        ];
                                        require ROOT_PATH . '/app/Views/components/action_menu.php';
                                        unset($actionMenuConfig, $contactActionItems);
                                        ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php elseif (empty($loadError)): ?>
                            <tr>
                                <td colspan="6" class="px-5 py-12 text-center text-ui-muted">
                                    <?php if ($hasActiveFilters): ?>
                                        <p>No contact messages match the selected filters.</p>
                                        <a href="<?= BASE_URL ?>/admin/contacts"
                                           class="mt-2 inline-block font-semibold text-brand-royal hover:underline">
                                            Clear filters
                                        </a>
                                    <?php else: ?>
                                        No contact messages have been submitted yet.
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php
            $paginationConfig = ['item_label' => 'Contact messages', 'data' => $pagination];
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
