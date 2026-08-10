<?php
/**
 * Shared numbered pagination footer.
 *
 * @var array{
 *     item_label?: string,
 *     data: array{
 *         total_items?: int,
 *         has_previous?: bool,
 *         previous_url?: string,
 *         pages?: array<int, array{number: int, url?: string, current?: bool}>,
 *         has_next?: bool,
 *         next_url?: string,
 *         path?: string,
 *         query?: array<string, scalar>,
 *         per_page?: int,
 *         per_page_options?: array<int, int>
 *     }
 * } $paginationConfig
 */
$paginationConfigData = is_array($paginationConfig ?? null) ? $paginationConfig : [];
$paginationData = is_array($paginationConfigData['data'] ?? null) ? $paginationConfigData['data'] : [];
$paginationItemLabel = trim((string) ($paginationConfigData['item_label'] ?? 'Items'));
$paginationItemLabel = $paginationItemLabel !== '' ? $paginationItemLabel : 'Items';
$paginationButtonClass = 'inline-flex size-10 items-center justify-center rounded-lg border text-sm font-semibold transition';
?>
<nav class="grid gap-4 border-t border-ui-border px-5 py-4 xl:grid-cols-[1fr_auto_1fr] xl:items-center"
    aria-label="<?= htmlspecialchars($paginationItemLabel, ENT_QUOTES, 'UTF-8') ?> pagination">
    <p class="text-center text-sm font-semibold text-ui-ink xl:justify-self-start xl:text-left">
        Total <?= htmlspecialchars($paginationItemLabel, ENT_QUOTES, 'UTF-8') ?>:
        <?= (int) ($paginationData['total_items'] ?? 0) ?>
    </p>

    <div class="flex items-center justify-center gap-2">
        <?php if (!empty($paginationData['has_previous'])): ?>
        <a href="<?= htmlspecialchars((string) ($paginationData['previous_url'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
            class="ui-icon-button size-10" aria-label="Previous page">
            <svg viewBox="0 0 20 20" class="size-5" fill="none" aria-hidden="true">
                <path d="m12.5 15-5-5 5-5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"
                    stroke-linejoin="round" />
            </svg>
        </a>
        <?php else: ?>
        <span class="inline-flex size-10 items-center justify-center text-ui-muted/60" aria-disabled="true"
            aria-label="Previous page">
            <svg viewBox="0 0 20 20" class="size-5" fill="none" aria-hidden="true">
                <path d="m12.5 15-5-5 5-5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"
                    stroke-linejoin="round" />
            </svg>
        </span>
        <?php endif; ?>

        <?php foreach (($paginationData['pages'] ?? []) as $paginationPage): ?>
        <?php if (!empty($paginationPage['current'])): ?>
        <span class="<?= $paginationButtonClass ?> border-brand-blue/15 bg-ui-brand-soft text-brand-royal"
            aria-current="page" aria-label="Page <?= (int) $paginationPage['number'] ?>">
            <?= (int) $paginationPage['number'] ?>
        </span>
        <?php else: ?>
        <a href="<?= htmlspecialchars($paginationPage['url'], ENT_QUOTES, 'UTF-8') ?>"
            class="<?= $paginationButtonClass ?> border-ui-border bg-white text-ui-text hover:border-brand-blue hover:text-brand-royal"
            aria-label="Go to page <?= (int) $paginationPage['number'] ?>">
            <?= (int) $paginationPage['number'] ?>
        </a>
        <?php endif; ?>
        <?php endforeach; ?>

        <?php if (!empty($paginationData['has_next'])): ?>
        <a href="<?= htmlspecialchars((string) ($paginationData['next_url'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
            class="ui-icon-button size-10" aria-label="Next page">
            <svg viewBox="0 0 20 20" class="size-5" fill="none" aria-hidden="true">
                <path d="m7.5 5 5 5-5 5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"
                    stroke-linejoin="round" />
            </svg>
        </a>
        <?php else: ?>
        <span class="inline-flex size-10 items-center justify-center text-ui-muted/60" aria-disabled="true"
            aria-label="Next page">
            <svg viewBox="0 0 20 20" class="size-5" fill="none" aria-hidden="true">
                <path d="m7.5 5 5 5-5 5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"
                    stroke-linejoin="round" />
            </svg>
        </span>
        <?php endif; ?>
    </div>

    <form action="<?= htmlspecialchars((string) ($paginationData['path'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" method="get"
        class="flex items-center justify-center gap-3 xl:justify-self-end">
        <?php foreach (($paginationData['query'] ?? []) as $paginationQueryKey => $paginationQueryValue): ?>
        <input type="hidden" name="<?= htmlspecialchars((string) $paginationQueryKey, ENT_QUOTES, 'UTF-8') ?>"
            value="<?= htmlspecialchars((string) $paginationQueryValue, ENT_QUOTES, 'UTF-8') ?>">
        <?php endforeach; ?>
        <label for="pagination-per-page" class="whitespace-nowrap text-sm font-semibold text-ui-ink">
            Show per page:
        </label>
        <select id="pagination-per-page" name="per_page" onchange="this.form.submit()"
            class="ui-input min-h-10 w-auto py-1" aria-label="Items per page">
            <?php foreach (($paginationData['per_page_options'] ?? [5]) as $paginationPageLimit): ?>
            <option value="<?= (int) $paginationPageLimit ?>"
                <?= (int) $paginationPageLimit === (int) ($paginationData['per_page'] ?? 5) ? 'selected' : '' ?>>
                <?= (int) $paginationPageLimit ?>
            </option>
            <?php endforeach; ?>
        </select>
    </form>
</nav>