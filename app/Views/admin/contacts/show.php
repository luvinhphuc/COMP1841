<?php
/**
 * Variables passed from Admin\ContactController::show()
 *
 * @var string $adminSection
 * @var array $contact
 * @var string $csrfToken
 */

$contact = is_array($contact ?? null) ? $contact : [];
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
$status = array_key_exists($contact['status'] ?? '', $statusLabels)
    ? $contact['status']
    : 'unread';
$accountId = (int) ($contact['account_id'] ?? 0);
$accountName = trim((string) ($contact['account_full_name'] ?? ''));
$accountUsername = trim((string) ($contact['account_username'] ?? ''));
?>

<section class="ui-page">
    <div class="ui-container flex flex-col gap-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between" data-motion-intro>
            <div>
                <p class="ui-eyebrow">Administration</p>
                <h1 class="ui-page-title">Contact message</h1>
                <p class="ui-page-description mt-2 text-sm">Read and manage the complete submitted message.</p>
            </div>
            <a href="<?= BASE_URL ?>/admin/contacts" class="ui-button ui-button-secondary">Back to messages</a>
        </div>

        <?php require ROOT_PATH . '/app/Views/admin/partials/navigation.php'; ?>

        <div class="grid items-start gap-5 lg:grid-cols-[minmax(0,1fr)_320px]" data-motion-reveal>
            <article class="ui-card p-5 sm:p-6">
                <div
                    class="flex flex-col gap-3 border-b border-ui-border pb-5 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <p class="ui-eyebrow">Subject</p>
                        <h2 class="mt-2 text-2xl font-bold leading-tight text-ui-ink">
                            <?= htmlspecialchars($contact['subject'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                        </h2>
                    </div>
                    <span class="ui-badge <?= htmlspecialchars($statusClasses[$status], ENT_QUOTES, 'UTF-8') ?>">
                        <?= htmlspecialchars($statusLabels[$status], ENT_QUOTES, 'UTF-8') ?>
                    </span>
                </div>

                <dl class="grid gap-4 border-b border-ui-border py-5 sm:grid-cols-2">
                    <div>
                        <dt class="text-xs font-bold uppercase tracking-[0.06em] text-ui-muted">Sender</dt>
                        <dd class="mt-1 font-semibold text-ui-ink">
                            <?= htmlspecialchars($contact['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs font-bold uppercase tracking-[0.06em] text-ui-muted">Email</dt>
                        <dd class="mt-1 break-all font-semibold text-ui-ink">
                            <?= htmlspecialchars($contact['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                        </dd>
                    </div>
                </dl>

                <div class="pt-5">
                    <h3 class="text-sm font-bold uppercase tracking-[0.06em] text-ui-muted">Message</h3>
                    <div class="mt-3 break-words text-sm leading-7 text-ui-text">
                        <?= nl2br(htmlspecialchars($contact['message'] ?? '', ENT_QUOTES, 'UTF-8')) ?>
                    </div>
                </div>
            </article>

            <aside class="grid gap-5">
                <section class="ui-card p-5">
                    <h2 class="text-base font-bold text-ui-ink">Sender account</h2>
                    <?php if ($accountId > 0): ?>
                    <p class="mt-3 font-semibold text-ui-ink">
                        <?= htmlspecialchars($accountName !== '' ? $accountName : $accountUsername, ENT_QUOTES, 'UTF-8') ?>
                    </p>
                    <p class="mt-1 text-sm text-ui-muted">
                        @<?= htmlspecialchars($accountUsername, ENT_QUOTES, 'UTF-8') ?>
                    </p>
                    <?php if (trim((string) ($contact['account_role'] ?? '')) !== ''): ?>
                    <p class="mt-1 text-sm text-ui-muted">
                        <?= htmlspecialchars(ucfirst((string) $contact['account_role']), ENT_QUOTES, 'UTF-8') ?>
                    </p>
                    <?php endif; ?>
                    <a href="<?= BASE_URL ?>/admin/users/edit/<?= $accountId ?>"
                        class="mt-4 inline-block font-semibold text-brand-royal hover:underline">View account</a>
                    <?php else: ?>
                    <p class="mt-3 text-sm text-ui-muted">Guest</p>
                    <?php endif; ?>
                </section>

                <section class="ui-card p-5">
                    <h2 class="text-base font-bold text-ui-ink">Message details</h2>
                    <dl class="mt-4 grid gap-4 text-sm">
                        <div>
                            <dt class="font-semibold text-ui-muted">Submitted</dt>
                            <dd class="mt-1 text-ui-ink">
                                <?= htmlspecialchars($contact['created_at'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                            </dd>
                        </div>
                        <?php if (trim((string) ($contact['updated_at'] ?? '')) !== ''): ?>
                        <div>
                            <dt class="font-semibold text-ui-muted">Last updated</dt>
                            <dd class="mt-1 text-ui-ink">
                                <?= htmlspecialchars($contact['updated_at'], ENT_QUOTES, 'UTF-8') ?>
                            </dd>
                        </div>
                        <?php endif; ?>
                    </dl>
                </section>

                <section class="ui-card p-5">
                    <h2 class="text-base font-bold text-ui-ink">Update status</h2>
                    <form action="<?= BASE_URL ?>/admin/contacts/status/<?= (int) ($contact['id'] ?? 0) ?>"
                        method="post" class="mt-4 grid gap-3">
                        <input type="hidden" name="_csrf_token"
                            value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                        <label for="contact-status" class="text-xs font-bold uppercase tracking-[0.06em] text-ui-muted">
                            Status
                        </label>
                        <select id="contact-status" name="status" class="ui-input">
                            <?php foreach ($statusLabels as $statusValue => $label): ?>
                            <option value="<?= htmlspecialchars($statusValue, ENT_QUOTES, 'UTF-8') ?>"
                                <?= $status === $statusValue ? 'selected' : '' ?>>
                                <?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" class="ui-button ui-button-primary">Save status</button>
                    </form>
                </section>

                <button type="button" data-open-modal="confirmation-modal"
                    data-confirm-title="Delete this contact message?"
                    data-confirm-message="This contact message will be permanently deleted."
                    data-confirm-detail="<?= htmlspecialchars((string) ($contact['subject'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                    data-confirm-action="<?= htmlspecialchars(BASE_URL . '/admin/contacts/delete/' . (int) ($contact['id'] ?? 0), ENT_QUOTES, 'UTF-8') ?>"
                    data-confirm-submit-label="Delete" class="ui-button ui-button-danger w-full">Delete message</button>
            </aside>
        </div>
        <?php
        $confirmationModalConfig = ['csrf_token' => $csrfToken];
        require ROOT_PATH . '/app/Views/components/confirmation_modal.php';
        ?>
    </div>
</section>