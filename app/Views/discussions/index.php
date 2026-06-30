<?php
$feedPosts = $posts ?? $discussions ?? [];
$filters = $filters ?? [];
$moduleChips = $moduleChips ?? [];
$matchedModules = $matchedModules ?? [];
$trendingModules = $trendingModules ?? [];
$recentViewedDiscussions = $recentViewedDiscussions ?? [];
$popularDiscussions = $popularDiscussions ?? [];
$pagination = $pagination ?? ['current' => 1, 'total' => 1, 'has_previous' => false, 'has_next' => false];
$totalDiscussions = (int) ($totalDiscussions ?? count($feedPosts));
$searchValue = (string) ($filters['q'] ?? '');
$activeModule = (string) ($filters['module'] ?? '');
$activeSort = (string) ($filters['sort'] ?? '');
$activeStatus = (string) ($filters['status'] ?? '');
$discussionCountLabel = $totalDiscussions === 1 ? '1 discussion' : $totalDiscussions . ' discussions';
$visibleCount = count($feedPosts);
?>

<section class="box-border min-h-screen bg-[#F7F8FB] px-4 py-8 [font-family:Inter,ui-sans-serif,system-ui,sans-serif] text-[#111827] sm:px-6 lg:px-10 lg:py-10">
    <div class="mx-auto flex max-w-[1280px] flex-col gap-8">
        <section class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_auto] lg:items-end" aria-labelledby="discussions-title">
            <div class="max-w-3xl">
                <p class="text-sm font-semibold text-[#1E3A8A]">Coursework community</p>
                <h1 id="discussions-title" class="mt-3 text-4xl font-semibold leading-tight tracking-[-0.01em] text-[#0F172A] sm:text-5xl">
                    All Discussions
                </h1>
                <p class="mt-4 max-w-2xl text-base leading-7 text-[#4B5563]">
                    Browse coursework discussions from every module, find relevant answers, and follow the questions your classmates are working through.
                </p>
            </div>

            <a
                href="<?= BASE_URL ?>/discussions/create"
                class="inline-flex min-h-12 w-fit items-center justify-center gap-2 rounded-2xl bg-[#1E3A8A] px-5 text-sm font-semibold text-white transition duration-200 hover:bg-[#172E70] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#1E3A8A]"
            >
                <svg viewBox="0 0 20 20" class="size-4 shrink-0" fill="none" aria-hidden="true">
                    <path d="M10 4.25v11.5M4.25 10h11.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" />
                </svg>
                Start discussion
            </a>
        </section>

        <section class="rounded-[20px] bg-white p-4 ring-1 ring-[#E5E7EB] sm:p-5" aria-label="Search and filter discussions">
            <form action="<?= BASE_URL ?>/discussions" method="get" role="search" class="grid gap-3 sm:grid-cols-[minmax(0,1fr)_auto] sm:items-start">
                <div class="min-w-0">
                    <label for="discussion-search" class="sr-only">Search discussions</label>
                    <div class="flex min-h-12 items-center rounded-2xl bg-[#F7F8FB] px-4 ring-1 ring-[#E5E7EB] transition duration-200 focus-within:ring-2 focus-within:ring-[#2563EB]/30">
                        <svg viewBox="0 0 18 18" class="mr-3 size-5 shrink-0 text-[#4B5563]" fill="none" aria-hidden="true">
                            <circle cx="8" cy="8" r="5.75" stroke="currentColor" stroke-width="1.5" />
                            <path d="m12.25 12.25 3 3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                        </svg>
                        <input
                            id="discussion-search"
                            name="q"
                            type="search"
                            value="<?= htmlspecialchars($searchValue, ENT_QUOTES, 'UTF-8') ?>"
                            placeholder="Search discussions"
                            class="min-w-0 flex-1 bg-transparent text-base text-[#111827] outline-none placeholder:text-[#4B5563]"
                        >
                    </div>
                </div>

                <details class="relative w-fit sm:justify-self-end">
                    <summary
                        class="inline-flex h-12 cursor-pointer list-none items-center justify-center gap-2 rounded-full bg-white px-4 text-sm font-medium text-[#2563EB] ring-1 ring-[#60A5FA] transition duration-200 hover:bg-[#EFF6FF] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#2563EB] [&::-webkit-details-marker]:hidden"
                    >
                        <svg viewBox="0 0 18 18" class="size-4 shrink-0" fill="none" aria-hidden="true">
                            <path d="M3.75 5h10.5M5.75 9h6.5M7.75 13h2.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" />
                        </svg>
                        Filter
                    </summary>

                    <div class="absolute left-0 z-20 mt-3 w-[min(88vw,360px)] rounded-2xl bg-white p-4 ring-1 ring-[#E5E7EB] shadow-[0_8px_8px_rgba(15,23,42,0.06)] sm:right-0 sm:left-auto">
                        <div class="grid gap-3">
                            <div>
                                <label for="module-filter" class="mb-2 block text-sm font-semibold text-[#111827]">Module</label>
                                <select
                                    id="module-filter"
                                    name="module"
                                    class="h-12 w-full rounded-2xl border-0 bg-[#F7F8FB] px-4 text-sm font-medium text-[#111827] ring-1 ring-[#E5E7EB] transition duration-200 focus:outline-none focus:ring-2 focus:ring-[#2563EB]/30"
                                >
                                    <option value="">All modules</option>
                                    <?php foreach ($moduleChips as $module): ?>
                                        <?php $moduleCode = (string) ($module['code'] ?? ''); ?>
                                        <option value="<?= htmlspecialchars($moduleCode, ENT_QUOTES, 'UTF-8') ?>" <?= strcasecmp($activeModule, $moduleCode) === 0 ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($moduleCode, ENT_QUOTES, 'UTF-8') ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div>
                                <label for="status-filter" class="mb-2 block text-sm font-semibold text-[#111827]">Status</label>
                                <select
                                    id="status-filter"
                                    name="status"
                                    class="h-12 w-full rounded-2xl border-0 bg-[#F7F8FB] px-4 text-sm font-medium text-[#111827] ring-1 ring-[#E5E7EB] transition duration-200 focus:outline-none focus:ring-2 focus:ring-[#2563EB]/30"
                                >
                                    <option value="">All status</option>
                                    <option value="open" <?= $activeStatus === 'open' ? 'selected' : '' ?>>Open</option>
                                    <option value="solved" <?= $activeStatus === 'solved' ? 'selected' : '' ?>>Solved</option>
                                </select>
                            </div>

                            <div>
                                <label for="sort-filter" class="mb-2 block text-sm font-semibold text-[#111827]">Sort</label>
                                <select
                                    id="sort-filter"
                                    name="sort"
                                    class="h-12 w-full rounded-2xl border-0 bg-[#F7F8FB] px-4 text-sm font-medium text-[#111827] ring-1 ring-[#E5E7EB] transition duration-200 focus:outline-none focus:ring-2 focus:ring-[#2563EB]/30"
                                >
                                    <option value="">Newest</option>
                                    <option value="popular" <?= $activeSort === 'popular' ? 'selected' : '' ?>>Popular</option>
                                </select>
                            </div>

                            <button
                                type="submit"
                                class="inline-flex min-h-12 items-center justify-center rounded-2xl bg-[#1E3A8A] px-5 text-sm font-semibold text-white transition duration-200 hover:bg-[#172E70] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#1E3A8A]"
                            >
                                Apply
                            </button>
                        </div>
                    </div>
                </details>
            </form>

            <?php if (!empty($moduleChips)): ?>
                <div class="mt-4 flex gap-2 overflow-x-auto pb-1" aria-label="Quick module filters">
                    <a
                        href="<?= BASE_URL ?>/discussions"
                        class="inline-flex h-9 shrink-0 items-center rounded-full px-3 text-xs font-semibold transition duration-200 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#1E3A8A] <?= $activeModule === '' ? 'bg-[#1E3A8A] text-white' : 'bg-[#EEF2FF] text-[#1E3A8A] hover:bg-[#DBEAFE]' ?>"
                    >
                        All
                    </a>
                    <?php foreach ($moduleChips as $module): ?>
                        <a
                            href="<?= htmlspecialchars((string) ($module['url'] ?? '#'), ENT_QUOTES, 'UTF-8') ?>"
                            class="inline-flex h-9 max-w-[180px] shrink-0 items-center rounded-full px-3 font-mono text-xs font-semibold transition duration-200 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#1E3A8A] <?= !empty($module['active']) ? 'bg-[#1E3A8A] text-white' : 'bg-[#EEF2FF] text-[#1E3A8A] hover:bg-[#DBEAFE]' ?>"
                            title="<?= htmlspecialchars((string) ($module['name'] ?? $module['code'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                            <?= !empty($module['active']) ? 'aria-current="page"' : '' ?>
                        >
                            <span class="truncate">
                                <?= htmlspecialchars((string) ($module['code'] ?? 'MODULE'), ENT_QUOTES, 'UTF-8') ?>
                            </span>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>

        <div class="grid gap-8 lg:grid-cols-[minmax(0,1fr)_320px]">
            <section class="min-w-0" aria-labelledby="discussion-feed-heading">
                <div class="mb-4 flex flex-wrap items-end justify-between gap-3">
                    <div>
                        <h2 id="discussion-feed-heading" class="text-xl font-semibold leading-7 text-[#0F172A]">
                            <?= htmlspecialchars($discussionCountLabel, ENT_QUOTES, 'UTF-8') ?>
                        </h2>
                        <p class="mt-1 text-sm leading-6 text-[#4B5563]">
                            <?= $activeSort === 'popular' ? 'Sorted by reply activity and views.' : 'Sorted by newest coursework activity.' ?>
                        </p>
                    </div>
                    <p class="text-sm font-medium text-[#4B5563]">
                        Showing <?= htmlspecialchars((string) $visibleCount, ENT_QUOTES, 'UTF-8') ?> on this page
                    </p>
                </div>

                <?php if (!empty($feedPosts)): ?>
                    <div class="flex flex-col gap-4">
                        <?php foreach ($feedPosts as $post): ?>
                            <?php
                            $replyCount = (int) ($post['replies'] ?? $post['reply_count'] ?? 0);
                            $status = (string) ($post['status'] ?? 'Open');
                            $statusTone = (string) ($post['status_tone'] ?? (strtolower($status) === 'solved' ? 'green' : 'neutral'));
                            $postUrl = (string) ($post['url'] ?? '#');
                            $postTitle = (string) ($post['title'] ?? 'Untitled discussion');
                            $postImage = (string) ($post['image'] ?? '');
                            ?>
                            <article class="group rounded-[20px] bg-white p-5 ring-1 ring-[#E5E7EB] transition duration-200 hover:-translate-y-0.5 hover:ring-[#BFDBFE] hover:shadow-[0_8px_8px_rgba(15,23,42,0.06)] focus-within:ring-2 focus-within:ring-[#2563EB]/30 sm:p-6">
                                <div class="flex flex-col gap-5">
                                    <div class="flex flex-wrap items-start justify-between gap-3">
                                        <div class="flex min-w-0 flex-wrap gap-2">
                                            <span class="inline-flex max-w-full items-center rounded-full bg-[#EEF2FF] px-3 py-1 font-mono text-xs font-semibold text-[#1E3A8A]">
                                                <span class="truncate"><?= htmlspecialchars((string) ($post['module'] ?? 'MODULE'), ENT_QUOTES, 'UTF-8') ?></span>
                                            </span>
                                            <span class="inline-flex items-center gap-1 rounded-full px-3 py-1 text-xs font-semibold <?= $statusTone === 'green' ? 'bg-[#DCFCE7] text-[#166534]' : 'bg-[#F3F4F6] text-[#374151]' ?>">
                                                <?php if ($statusTone === 'green'): ?>
                                                    <svg viewBox="0 0 16 16" class="size-3.5 shrink-0" fill="none" aria-hidden="true">
                                                        <path d="m4 8.2 2.4 2.4L12 5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" />
                                                    </svg>
                                                <?php endif; ?>
                                                <?= htmlspecialchars($status, ENT_QUOTES, 'UTF-8') ?>
                                            </span>
                                        </div>

                                        <time class="text-sm font-medium text-[#6B7280]">
                                            <?= htmlspecialchars((string) ($post['created_at'] ?? $post['time'] ?? 'Recently'), ENT_QUOTES, 'UTF-8') ?>
                                        </time>
                                    </div>

                                    <div class="grid gap-4 sm:grid-cols-[minmax(0,1fr)_auto]">
                                        <div class="min-w-0">
                                            <?php if (!empty($post['module_name'])): ?>
                                                <p class="text-sm font-medium leading-6 text-[#4B5563]" dir="auto">
                                                    <?= htmlspecialchars((string) $post['module_name'], ENT_QUOTES, 'UTF-8') ?>
                                                </p>
                                            <?php endif; ?>
                                            <h3 class="mt-1 text-xl font-semibold leading-7 text-[#0F172A] [overflow-wrap:anywhere]" dir="auto">
                                                <a
                                                    href="<?= htmlspecialchars($postUrl, ENT_QUOTES, 'UTF-8') ?>"
                                                    class="rounded-sm transition duration-200 hover:text-[#1E3A8A] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#1E3A8A]"
                                                >
                                                    <?= htmlspecialchars($postTitle, ENT_QUOTES, 'UTF-8') ?>
                                                </a>
                                            </h3>
                                            <p class="mt-2 line-clamp-2 max-w-3xl text-base leading-7 text-[#4B5563] [overflow-wrap:anywhere]" dir="auto">
                                                <?= htmlspecialchars((string) ($post['excerpt'] ?? 'No preview is available yet.'), ENT_QUOTES, 'UTF-8') ?>
                                            </p>
                                        </div>

                                        <?php if ($postImage !== ''): ?>
                                            <a
                                                href="<?= htmlspecialchars($postUrl, ENT_QUOTES, 'UTF-8') ?>"
                                                class="flex h-[104px] w-full items-center justify-center gap-2 rounded-2xl bg-[#F7F8FB] text-xs font-semibold text-[#4B5563] ring-1 ring-[#E5E7EB] transition duration-200 hover:ring-[#2563EB] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#1E3A8A] sm:w-[156px]"
                                                aria-label="Open discussion with attached media: <?= htmlspecialchars($postTitle, ENT_QUOTES, 'UTF-8') ?>"
                                            >
                                                <svg viewBox="0 0 18 18" class="size-4 shrink-0" fill="none" aria-hidden="true">
                                                    <rect x="3" y="4" width="12" height="10" rx="1.6" stroke="currentColor" stroke-width="1.4" />
                                                    <path d="m4.5 12 3-3 2.2 2.2 1.2-1.2 2.6 2" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" />
                                                </svg>
                                                Media
                                            </a>
                                        <?php endif; ?>
                                    </div>

                                    <div class="flex flex-wrap items-center justify-between gap-4 border-t border-[#E5E7EB] pt-4">
                                        <div class="flex min-w-0 items-center gap-3">
                                            <span class="flex size-9 shrink-0 items-center justify-center rounded-full bg-[#1E3A8A] text-xs font-semibold text-white" aria-hidden="true">
                                                <?= htmlspecialchars((string) ($post['avatar'] ?? 'S'), ENT_QUOTES, 'UTF-8') ?>
                                            </span>
                                            <p class="min-w-0 text-sm leading-6 text-[#4B5563] [overflow-wrap:anywhere]" dir="auto">
                                                <span class="font-semibold text-[#111827]">
                                                    <?= htmlspecialchars((string) ($post['author'] ?? 'Student'), ENT_QUOTES, 'UTF-8') ?>
                                                </span>
                                                <?php if (!empty($post['author_handle'])): ?>
                                                    <span class="ml-1"><?= htmlspecialchars((string) $post['author_handle'], ENT_QUOTES, 'UTF-8') ?></span>
                                                <?php endif; ?>
                                            </p>
                                        </div>

                                        <div class="flex items-center gap-4 text-sm font-medium text-[#4B5563]" aria-label="Discussion engagement">
                                            <span class="inline-flex items-center gap-1.5">
                                                <svg viewBox="0 0 18 18" class="size-4 shrink-0" fill="none" aria-hidden="true">
                                                    <path d="M4 5.5h10v6H7.4L4 14.2V5.5Z" stroke="currentColor" stroke-width="1.4" stroke-linejoin="round" />
                                                </svg>
                                                <?= htmlspecialchars((string) $replyCount, ENT_QUOTES, 'UTF-8') ?>
                                            </span>
                                            <span class="inline-flex items-center gap-1.5">
                                                <svg viewBox="0 0 20 20" class="size-4 shrink-0" fill="none" aria-hidden="true">
                                                    <path d="M2.5 10s2.7-4.5 7.5-4.5 7.5 4.5 7.5 4.5-2.7 4.5-7.5 4.5S2.5 10 2.5 10Z" stroke="currentColor" stroke-width="1.5" />
                                                    <circle cx="10" cy="10" r="1.8" stroke="currentColor" stroke-width="1.5" />
                                                </svg>
                                                <?= htmlspecialchars((string) ($post['views'] ?? $post['view_count'] ?? '0'), ENT_QUOTES, 'UTF-8') ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="rounded-[20px] border border-dashed border-[#CBD5E1] bg-white p-6 sm:p-8">
                        <?php if (!empty($matchedModules)): ?>
                            <h3 class="text-xl font-semibold leading-7 text-[#0F172A]">Module found</h3>
                            <p class="mt-2 max-w-2xl text-sm leading-6 text-[#4B5563]">
                                No discussions match this search yet, but these modules match your query.
                            </p>
                            <div class="mt-5 grid gap-3">
                                <?php foreach ($matchedModules as $module): ?>
                                    <a
                                        href="<?= htmlspecialchars((string) ($module['url'] ?? '#'), ENT_QUOTES, 'UTF-8') ?>"
                                        class="flex min-w-0 flex-wrap items-center justify-between gap-3 rounded-2xl bg-[#F7F8FB] px-4 py-3 ring-1 ring-[#E5E7EB] transition duration-200 hover:ring-[#2563EB] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#1E3A8A]"
                                    >
                                        <span class="min-w-0">
                                            <span class="block font-mono text-xs font-semibold text-[#1E3A8A]">
                                                <?= htmlspecialchars((string) ($module['code'] ?? 'MODULE'), ENT_QUOTES, 'UTF-8') ?>
                                            </span>
                                            <span class="mt-1 block text-sm leading-5 text-[#4B5563]" dir="auto">
                                                <?= htmlspecialchars((string) ($module['name'] ?? 'Module'), ENT_QUOTES, 'UTF-8') ?>
                                            </span>
                                        </span>
                                        <span class="shrink-0 text-sm font-semibold text-[#1E3A8A]">View discussions</span>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <h3 class="text-xl font-semibold leading-7 text-[#0F172A]">No discussions yet</h3>
                            <p class="mt-2 max-w-2xl text-sm leading-6 text-[#4B5563]">
                                Start the first coursework question for your module. Clear titles, a short explanation, and any relevant error message help classmates reply faster.
                            </p>
                        <?php endif; ?>
                        <a
                            href="<?= BASE_URL ?>/discussions/create"
                            class="mt-5 inline-flex min-h-11 items-center justify-center rounded-2xl bg-[#1E3A8A] px-5 text-sm font-semibold text-white transition duration-200 hover:bg-[#172E70] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#1E3A8A]"
                        >
                            Start discussion
                        </a>
                    </div>
                <?php endif; ?>

                <?php if (($pagination['total'] ?? 1) > 1): ?>
                    <nav class="mt-6 flex flex-wrap items-center justify-between gap-3 rounded-[20px] bg-white p-3 ring-1 ring-[#E5E7EB]" aria-label="Discussion pagination">
                        <a
                            href="<?= htmlspecialchars((string) ($pagination['previous_url'] ?? '#'), ENT_QUOTES, 'UTF-8') ?>"
                            class="inline-flex h-10 items-center rounded-2xl px-4 text-sm font-semibold transition duration-200 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#1E3A8A] <?= !empty($pagination['has_previous']) ? 'bg-[#EEF2FF] text-[#1E3A8A] hover:bg-[#DBEAFE]' : 'pointer-events-none bg-[#F3F4F6] text-[#9CA3AF]' ?>"
                            <?= empty($pagination['has_previous']) ? 'aria-disabled="true"' : '' ?>
                        >
                            Previous
                        </a>
                        <span class="text-sm font-medium text-[#4B5563]">
                            Page <?= htmlspecialchars((string) ($pagination['current'] ?? 1), ENT_QUOTES, 'UTF-8') ?> of <?= htmlspecialchars((string) ($pagination['total'] ?? 1), ENT_QUOTES, 'UTF-8') ?>
                        </span>
                        <a
                            href="<?= htmlspecialchars((string) ($pagination['next_url'] ?? '#'), ENT_QUOTES, 'UTF-8') ?>"
                            class="inline-flex h-10 items-center rounded-2xl px-4 text-sm font-semibold transition duration-200 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#1E3A8A] <?= !empty($pagination['has_next']) ? 'bg-[#EEF2FF] text-[#1E3A8A] hover:bg-[#DBEAFE]' : 'pointer-events-none bg-[#F3F4F6] text-[#9CA3AF]' ?>"
                            <?= empty($pagination['has_next']) ? 'aria-disabled="true"' : '' ?>
                        >
                            Next
                        </a>
                    </nav>
                <?php endif; ?>
            </section>

            <aside class="hidden flex-col gap-4 lg:flex" aria-label="Discussion sidebar">
                <section class="rounded-[20px] bg-white p-5 ring-1 ring-[#E5E7EB]" aria-labelledby="community-stats-heading">
                    <h2 id="community-stats-heading" class="text-base font-semibold text-[#0F172A]">Community statistics</h2>
                    <dl class="mt-4 grid gap-3">
                        <div class="flex items-center justify-between gap-3">
                            <dt class="text-sm text-[#4B5563]">Discussions</dt>
                            <dd class="font-semibold text-[#111827]"><?= htmlspecialchars((string) $totalDiscussions, ENT_QUOTES, 'UTF-8') ?></dd>
                        </div>
                        <div class="flex items-center justify-between gap-3">
                            <dt class="text-sm text-[#4B5563]">Tracked modules</dt>
                            <dd class="font-semibold text-[#111827]"><?= htmlspecialchars((string) count($moduleChips), ENT_QUOTES, 'UTF-8') ?></dd>
                        </div>
                        <div class="flex items-center justify-between gap-3">
                            <dt class="text-sm text-[#4B5563]">Popular threads</dt>
                            <dd class="font-semibold text-[#111827]"><?= htmlspecialchars((string) count($popularDiscussions), ENT_QUOTES, 'UTF-8') ?></dd>
                        </div>
                    </dl>
                </section>

                <section class="rounded-[20px] bg-white p-5 ring-1 ring-[#E5E7EB]" aria-labelledby="trending-modules-heading">
                    <h2 id="trending-modules-heading" class="text-base font-semibold text-[#0F172A]">Trending modules</h2>
                    <div class="mt-4 grid gap-2">
                        <?php if (!empty($trendingModules)): ?>
                            <?php foreach ($trendingModules as $module): ?>
                                <a
                                    href="<?= htmlspecialchars((string) ($module['url'] ?? '#'), ENT_QUOTES, 'UTF-8') ?>"
                                    class="flex min-w-0 items-center justify-between gap-3 rounded-2xl px-3 py-2 transition duration-200 hover:bg-[#F7F8FB] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#1E3A8A]"
                                >
                                    <span class="min-w-0 font-mono text-xs font-semibold text-[#1E3A8A]">
                                        <?= htmlspecialchars((string) ($module['code'] ?? 'MODULE'), ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                    <span class="shrink-0 text-xs font-medium text-[#4B5563]">
                                        <?= htmlspecialchars((string) ($module['count'] ?? $module['posts'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                </a>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-sm leading-6 text-[#4B5563]">Module activity will appear after students start discussions.</p>
                        <?php endif; ?>
                    </div>
                </section>

                <section class="rounded-[20px] bg-white p-5 ring-1 ring-[#E5E7EB]" aria-labelledby="popular-discussions-heading">
                    <h2 id="popular-discussions-heading" class="text-base font-semibold text-[#0F172A]">Popular discussions</h2>
                    <div class="mt-4 grid gap-4">
                        <?php if (!empty($popularDiscussions)): ?>
                            <?php foreach ($popularDiscussions as $discussion): ?>
                                <a
                                    href="<?= htmlspecialchars((string) ($discussion['url'] ?? '#'), ENT_QUOTES, 'UTF-8') ?>"
                                    class="block rounded-2xl transition duration-200 hover:text-[#1E3A8A] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#1E3A8A]"
                                >
                                    <span class="block text-sm font-semibold leading-5 text-[#111827] [overflow-wrap:anywhere]" dir="auto">
                                        <?= htmlspecialchars((string) ($discussion['title'] ?? 'Untitled discussion'), ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                    <span class="mt-1 block text-xs font-medium text-[#4B5563]">
                                        <?= htmlspecialchars((string) ($discussion['replies'] ?? 0), ENT_QUOTES, 'UTF-8') ?>
                                        <?= ((int) ($discussion['replies'] ?? 0)) === 1 ? 'reply' : 'replies' ?>
                                    </span>
                                </a>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-sm leading-6 text-[#4B5563]">Popular questions will appear when replies are added.</p>
                        <?php endif; ?>
                    </div>
                </section>

                <section class="rounded-[20px] bg-white p-5 ring-1 ring-[#E5E7EB]" aria-labelledby="recently-active-heading">
                    <h2 id="recently-active-heading" class="text-base font-semibold text-[#0F172A]">Recently active</h2>
                    <div class="mt-4 grid gap-4">
                        <?php if (!empty($recentViewedDiscussions)): ?>
                            <?php foreach ($recentViewedDiscussions as $discussion): ?>
                                <a
                                    href="<?= htmlspecialchars((string) ($discussion['url'] ?? '#'), ENT_QUOTES, 'UTF-8') ?>"
                                    class="block rounded-2xl transition duration-200 hover:text-[#1E3A8A] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#1E3A8A]"
                                >
                                    <span class="block text-sm font-semibold leading-5 text-[#111827] [overflow-wrap:anywhere]" dir="auto">
                                        <?= htmlspecialchars((string) ($discussion['title'] ?? 'Untitled discussion'), ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                    <span class="mt-1 block text-xs font-medium text-[#4B5563]">
                                        <?= htmlspecialchars((string) ($discussion['module'] ?? 'MODULE'), ENT_QUOTES, 'UTF-8') ?> &middot; <?= htmlspecialchars((string) ($discussion['time'] ?? 'Recently'), ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                </a>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-sm leading-6 text-[#4B5563]">Discussions you open will appear here.</p>
                        <?php endif; ?>
                    </div>
                </section>
            </aside>
        </div>
    </div>
</section>
