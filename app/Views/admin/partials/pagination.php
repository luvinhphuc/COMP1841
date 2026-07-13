<?php if (($pagination['total'] ?? 1) > 1): ?>
    <nav class="mt-5 flex items-center justify-between gap-4 border-t border-[#e8edf5] pt-4" aria-label="Pagination">
        <?php if (!empty($pagination['has_previous'])): ?>
            <a href="<?= htmlspecialchars($pagination['previous_url'], ENT_QUOTES, 'UTF-8') ?>"
                class="rounded-lg border border-[#d6deea] bg-white px-4 py-2 text-sm font-semibold text-[#475467] hover:border-[#0b57d0] hover:text-[#0b57d0]">
                Previous
            </a>
        <?php else: ?>
            <span class="rounded-lg border border-[#edf0f5] bg-[#f8faff] px-4 py-2 text-sm text-[#98a2b3]" aria-disabled="true">
                Previous
            </span>
        <?php endif; ?>

        <span class="text-sm font-semibold text-[#667085]" aria-current="page">
            Page <?= (int) ($pagination['current'] ?? 1) ?> of <?= (int) ($pagination['total'] ?? 1) ?>
        </span>

        <?php if (!empty($pagination['has_next'])): ?>
            <a href="<?= htmlspecialchars($pagination['next_url'], ENT_QUOTES, 'UTF-8') ?>"
                class="rounded-lg border border-[#d6deea] bg-white px-4 py-2 text-sm font-semibold text-[#475467] hover:border-[#0b57d0] hover:text-[#0b57d0]">
                Next
            </a>
        <?php else: ?>
            <span class="rounded-lg border border-[#edf0f5] bg-[#f8faff] px-4 py-2 text-sm text-[#98a2b3]" aria-disabled="true">
                Next
            </span>
        <?php endif; ?>
    </nav>
<?php endif; ?>
