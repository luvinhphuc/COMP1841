<section class="min-h-screen bg-[#f6f7fb] px-5 py-8 text-[#172033] sm:px-8 lg:px-12">
    <div class="mx-auto flex max-w-7xl flex-col gap-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.14em] text-[#0b57d0]">Administration</p>
                <h1 class="mt-2 text-3xl font-bold tracking-tight text-[#172033]">Admin Overview</h1>
                <p class="mt-2 text-sm text-[#667085]">Platform statistics and quick management shortcuts.</p>
            </div>
            <a href="<?= BASE_URL ?>/discussions/create" class="inline-flex h-10 items-center justify-center rounded-lg bg-[#0b57d0] px-4 text-sm font-semibold text-white hover:bg-[#0847ad]">
                + New Discussion
            </a>
        </div>

        <?php require ROOT_PATH . '/app/Views/admin/partials/navigation.php'; ?>

        <?php
        $stats = [
            ['label' => 'Total users', 'value' => $userCount, 'note' => 'Registered accounts', 'url' => BASE_URL . '/admin/users', 'color' => 'text-[#0b57d0]'],
            ['label' => 'Total modules', 'value' => $moduleCount, 'note' => 'Course modules', 'url' => BASE_URL . '/admin/modules', 'color' => 'text-[#7b5cc7]'],
            ['label' => 'Active posts', 'value' => $postCounts['total'] ?? 0, 'note' => 'Visible discussions', 'url' => BASE_URL . '/admin/posts', 'color' => 'text-[#137333]'],
            ['label' => 'Open posts', 'value' => $postCounts['open'] ?? 0, 'note' => 'Awaiting solutions', 'url' => BASE_URL . '/admin/posts?status=open', 'color' => 'text-[#b06000]'],
            ['label' => 'Solved posts', 'value' => $postCounts['solved'] ?? 0, 'note' => 'Completed discussions', 'url' => BASE_URL . '/admin/posts?status=solved', 'color' => 'text-[#137333]'],
        ];
        ?>
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
            <?php foreach ($stats as $stat): ?>
                <a href="<?= htmlspecialchars($stat['url'], ENT_QUOTES, 'UTF-8') ?>"
                    class="group rounded-xl border border-[#dfe5ef] bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-[#b8c6dc] hover:shadow-md">
                    <div class="flex items-start justify-between gap-3">
                        <span class="text-xs font-bold uppercase tracking-[0.08em] text-[#7a8699]"><?= htmlspecialchars($stat['label'], ENT_QUOTES, 'UTF-8') ?></span>
                        <span class="flex size-7 items-center justify-center rounded-lg bg-[#f1f5fb] <?= $stat['color'] ?>" aria-hidden="true">↗</span>
                    </div>
                    <strong class="mt-4 block text-3xl font-bold tracking-tight text-[#172033]"><?= number_format((int) $stat['value']) ?></strong>
                    <span class="mt-2 block text-xs text-[#7a8699]"><?= htmlspecialchars($stat['note'], ENT_QUOTES, 'UTF-8') ?></span>
                </a>
            <?php endforeach; ?>
        </div>

        <section class="rounded-xl border border-[#dfe5ef] bg-white p-5 shadow-sm">
                <h2 class="text-base font-bold text-[#172033]">Quick actions</h2>
                <p class="mt-1 text-xs text-[#7a8699]">Open the area you need to manage.</p>
                <div class="mt-4 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                    <a href="<?= BASE_URL ?>/admin/users" class="rounded-lg border border-[#e1e7f0] bg-[#fafbfe] p-4 text-center text-sm font-semibold text-[#344054] hover:border-[#0b57d0] hover:text-[#0b57d0]">Manage users</a>
                    <a href="<?= BASE_URL ?>/admin/modules" class="rounded-lg border border-[#e1e7f0] bg-[#fafbfe] p-4 text-center text-sm font-semibold text-[#344054] hover:border-[#0b57d0] hover:text-[#0b57d0]">Add module</a>
                    <a href="<?= BASE_URL ?>/admin/posts" class="rounded-lg border border-[#e1e7f0] bg-[#fafbfe] p-4 text-center text-sm font-semibold text-[#344054] hover:border-[#0b57d0] hover:text-[#0b57d0]">Review posts</a>
                    <a href="<?= BASE_URL ?>/dashboard" class="rounded-lg border border-[#e1e7f0] bg-[#fafbfe] p-4 text-center text-sm font-semibold text-[#344054] hover:border-[#0b57d0] hover:text-[#0b57d0]">User dashboard</a>
                </div>
        </section>
    </div>
</section>
