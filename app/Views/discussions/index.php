<?php
use App\Helpers\FormatHelper;
/**
 * Variables passed from DiscussionsController::index()
 *
 * @var array $discussions
 * @var array $filters
 * @var array $statusFilters
 * @var array $moduleChips
 * @var array $matchedModules
 * @var array $trendingModules
 * @var array $recentViewedDiscussions
 * @var array $popularDiscussions
 * @var array $pagination
 * @var int $totalDiscussions
 */
$feedPosts = $discussions;
$searchValue = (string) ($filters['q'] ?? '');
$activeModule = (string) ($filters['module'] ?? '');
$activeSort = (string) ($filters['sort'] ?? '');
$activeStatus = (string) ($filters['status'] ?? '');
$hasSearch = trim($searchValue) !== '';
$hasPanelFilter = $activeModule !== '' || $activeSort !== '' || $activeStatus !== '';
$activeFilters = [];

if ($hasSearch) {
    $activeFilters[] = [
        'label' => 'Search: ' . $searchValue,
        'remove_url' => FormatHelper::discussionUrl($filters, ['q' => null, 'page' => null]),
    ];
}

if ($activeModule !== '') {
    $activeFilters[] = [
        'label' => 'Module: ' . $activeModule,
        'remove_url' => FormatHelper::discussionUrl($filters, ['module' => null, 'page' => null]),
    ];
}

if ($activeStatus !== '') {
    $activeFilters[] = [
        'label' => 'Status: ' . ucfirst($activeStatus),
        'remove_url' => FormatHelper::discussionUrl($filters, ['status' => null, 'page' => null]),
    ];
}

if ($activeSort === 'popular') {
    $activeFilters[] = [
        'label' => 'Sort: Popular',
        'remove_url' => FormatHelper::discussionUrl($filters, ['sort' => null, 'page' => null]),
    ];
}

$activeFilterCount = count($activeFilters);
$hasActiveFilter = $activeFilterCount > 0;
$discussionCountLabel = $totalDiscussions === 1 ? '1 discussion' : $totalDiscussions . ' discussions';
$visibleCount = count($feedPosts);
?>

<section class="box-border min-h-screen bg-[#f7f9fd] px-4 py-8 font-sans text-[#191c1f] sm:px-6 lg:px-10 lg:py-10">
    <div class="mx-auto flex max-w-[1280px] flex-col gap-8">
        <section class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_auto] lg:items-end" aria-labelledby="discussions-title">
            <div class="max-w-3xl">
                <p class="text-sm font-semibold text-[#315f90]">Coursework community</p>
                <h1 id="discussions-title" class="mt-3 text-3xl font-semibold leading-tight text-[#0F172A] sm:text-4xl">
                    All Discussions
                </h1>
                <p class="mt-4 max-w-[68ch] text-base leading-7 text-[#444748]">
                    Browse coursework discussions from every module, find relevant answers, and follow the questions
                    your classmates are working through.
                </p>
            </div>

            <a href="<?= BASE_URL ?>/discussions/create"
                class="inline-flex min-h-12 w-fit items-center justify-center gap-2 rounded-lg bg-black px-5 text-sm font-semibold text-white transition duration-200 hover:bg-[#2a2d2f] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-black">
                <svg viewBox="0 0 20 20" class="size-4 shrink-0" fill="none" aria-hidden="true">
                    <path d="M10 4.25v11.5M4.25 10h11.5" stroke="currentColor" stroke-width="1.7"
                        stroke-linecap="round" />
                </svg>
                Start discussion
            </a>
        </section>

        <!--SEARCH AND FILTER -->
        <section class="border-y border-[#e6e8ec] py-4" aria-label="Search and filter discussions">
            <form action="<?= BASE_URL ?>/discussions" method="get" role="search"
                class="grid gap-3 sm:grid-cols-[minmax(0,1fr)_auto_auto] sm:items-start">
                <?php
                $searchBar = [
                    'id' => 'discussion-search',
                    'name' => 'q',
                    'value' => $searchValue,
                    'label' => 'Search discussions',
                    'placeholder' => 'Search discussions',
                    'button_label' => 'Search',
                ];
                require ROOT_PATH . '/app/Views/partials/search_bar.php';
                unset($searchBar);
                ?>
                <details class="relative w-full sm:w-fit sm:justify-self-end">
                    <summary
                        class="inline-flex h-12 w-full cursor-pointer list-none items-center justify-center gap-2 rounded-lg px-4 text-sm font-medium ring-1 transition duration-200 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#2563EB] sm:w-auto [&::-webkit-details-marker]:hidden <?= $hasPanelFilter ? 'bg-[rgb(37,99,235)] text-white ring-[rgb(37,99,235)] hover:bg-[rgb(37,99,235)]' : 'bg-white text-[#2563EB] ring-[#60A5FA] hover:bg-[#EFF6FF]' ?>">
                        <svg viewBox="0 0 18 18" class="size-4 shrink-0" fill="none" aria-hidden="true">
                            <path d="M3.75 5h10.5M5.75 9h6.5M7.75 13h2.5" stroke="currentColor" stroke-width="1.6"
                                stroke-linecap="round" />
                        </svg>
                        Filter
                        <?php if ($hasPanelFilter): ?>
                        <span
                            class="rounded-sm bg-white/15 px-1.5 py-0.5 text-xs"><?= htmlspecialchars(($activeFilterCount - ($hasSearch ? 1 : 0)), ENT_QUOTES, 'UTF-8') ?></span>
                        <?php endif; ?>
                    </summary>

                    <div
                        class="absolute left-0 z-20 mt-3 w-[min(88vw,360px)] rounded-lg bg-white p-4 ring-1 ring-[#d1d3d5] shadow-[0_18px_38px_rgba(25,28,31,0.12)] sm:right-0 sm:left-auto">
                        <div class="grid gap-3">
                            <div>
                                <label for="module-filter"
                                    class="mb-2 block text-sm font-semibold text-[#111827]">Module</label>
                                <select id="module-filter" name="module"
                                    class="h-12 w-full rounded-lg border-0 bg-[#f7f9fd] px-4 text-sm font-medium text-[#111827] ring-1 ring-[#d1d3d5] transition duration-200 focus:outline-none focus:ring-2 focus:ring-black/15">
                                    <option value="">All modules</option>
                                    <?php foreach ($moduleChips as $module): ?>
                                    <?php $moduleCode = ($module['code'] ?? ''); ?>
                                    <option value="<?= htmlspecialchars($moduleCode, ENT_QUOTES, 'UTF-8') ?>"
                                        <?= strcasecmp($activeModule, $moduleCode) === 0 ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($moduleCode, ENT_QUOTES, 'UTF-8') ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div>
                                <label for="status-filter"
                                    class="mb-2 block text-sm font-semibold text-[#111827]">Status</label>
                                <select id="status-filter" name="status"
                                    class="h-12 w-full rounded-lg border-0 bg-[#f7f9fd] px-4 text-sm font-medium text-[#111827] ring-1 ring-[#d1d3d5] transition duration-200 focus:outline-none focus:ring-2 focus:ring-black/15">
                                    <option value="">All status</option>
                                    <option value="open" <?= $activeStatus === 'open' ? 'selected' : '' ?>>Open</option>
                                    <option value="solved" <?= $activeStatus === 'solved' ? 'selected' : '' ?>>Solved
                                    </option>
                                </select>
                            </div>

                            <div>
                                <label for="sort-filter"
                                    class="mb-2 block text-sm font-semibold text-[#111827]">Sort</label>
                                <select id="sort-filter" name="sort"
                                    class="h-12 w-full rounded-lg border-0 bg-[#f7f9fd] px-4 text-sm font-medium text-[#111827] ring-1 ring-[#d1d3d5] transition duration-200 focus:outline-none focus:ring-2 focus:ring-black/15">
                                    <option value="">Newest</option>
                                    <option value="popular" <?= $activeSort === 'popular' ? 'selected' : '' ?>>Popular
                                    </option>
                                </select>
                            </div>

                            <button type="submit"
                                class="inline-flex min-h-12 items-center justify-center rounded-lg bg-black px-5 text-sm font-semibold text-white transition duration-200 hover:bg-[#2a2d2f] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-black">
                                Apply
                            </button>
                        </div>
                    </div>
                </details>
            </form>

            <?php if ($hasActiveFilter): ?>
            <div class="mt-3 flex flex-wrap items-center gap-2 text-sm" aria-label="Active discussion filters">
                <span class="font-medium text-[#444748]">Active filters</span>
                <?php foreach ($activeFilters as $filter): ?>
                <span
                    class="inline-flex max-w-full items-center gap-1.5 rounded-sm bg-white py-1 pr-1 pl-2.5 text-xs font-semibold text-[#191c1f] ring-1 ring-[#d1d3d5]">
                    <span class="truncate"><?= htmlspecialchars($filter['label'], ENT_QUOTES, 'UTF-8') ?></span>
                    <a href="<?= htmlspecialchars($filter['remove_url'], ENT_QUOTES, 'UTF-8') ?>"
                        class="inline-flex size-4 shrink-0 items-center justify-center rounded-sm text-[#444748] transition duration-200 hover:bg-[#eef2f7] hover:text-black focus-visible:outline-2 focus-visible:outline-offset-1 focus-visible:outline-black"
                        aria-label="Remove <?= htmlspecialchars($filter['label'], ENT_QUOTES, 'UTF-8') ?>">
                        <span aria-hidden="true">&times;</span>
                    </a>
                </span>
                <?php endforeach; ?>
                <a href="<?= BASE_URL ?>/discussions"
                    class="inline-flex min-h-8 items-center rounded-md px-2.5 text-xs font-semibold text-[#191c1f] transition duration-200 hover:bg-white hover:text-emerald-800 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-black">
                    Clear
                </a>
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
                    <p class="text-sm font-medium text-[#444748]">
                        Showing <?= htmlspecialchars($visibleCount, ENT_QUOTES, 'UTF-8') ?> of
                        <?= htmlspecialchars($totalDiscussions, ENT_QUOTES, 'UTF-8') ?>
                    </p>
                </div>

                <?php if (!empty($feedPosts)): ?>
                <div class="grid gap-4">
                    <?php foreach ($feedPosts as $post): ?>
                    <?php
                            $postCard = $post;
                            $postCardAnimated = false;
                            require ROOT_PATH . '/app/Views/partials/post_card.php';
                            ?>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <div class="rounded-xl border border-dashed border-[#CBD5E1] bg-white p-6 sm:p-8">
                    <?php if (!empty($matchedModules)): ?>
                    <h3 class="text-xl font-semibold leading-7 text-[#0F172A]">Module found</h3>
                    <p class="mt-2 max-w-2xl text-sm leading-6 text-[#4B5563]">
                        No discussions match this search yet, but these modules match your query.
                    </p>
                    <div class="mt-5 grid gap-3">
                        <?php foreach ($matchedModules as $module): ?>
                        <a href="<?= htmlspecialchars(($module['url'] ?? '#'), ENT_QUOTES, 'UTF-8') ?>"
                            class="flex min-w-0 flex-wrap items-center justify-between gap-3 rounded-lg border border-[#d1d3d5] bg-[#F7F8FB] px-4 py-3 transition duration-200 hover:border-black hover:bg-white focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-black">
                            <span class="min-w-0">
                                <span class="block font-mono text-xs font-semibold text-[#001b3d]">
                                    <?= htmlspecialchars(($module['code'] ?? 'MODULE'), ENT_QUOTES, 'UTF-8') ?>
                                </span>
                                <span class="mt-1 block text-sm leading-5 text-[#4B5563]" dir="auto">
                                    <?= htmlspecialchars(($module['name'] ?? 'Module'), ENT_QUOTES, 'UTF-8') ?>
                                </span>
                            </span>
                            <span class="shrink-0 text-sm font-semibold text-[#191c1f]">View discussions</span>
                        </a>
                        <?php endforeach; ?>
                    </div>
                    <?php else: ?>
                    <h3 class="text-xl font-semibold leading-7 text-[#0F172A]">No discussions yet</h3>
                    <p class="mt-2 max-w-2xl text-sm leading-6 text-[#4B5563]">
                        Start the first coursework question for your module. Clear titles, a short explanation, and any
                        relevant error message help classmates reply faster.
                    </p>
                    <?php endif; ?>
                    <a href="<?= BASE_URL ?>/discussions/create"
                        class="mt-5 inline-flex min-h-11 items-center justify-center rounded-lg bg-black px-5 text-sm font-semibold text-white transition duration-200 hover:bg-[#2a2d2f] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-black">
                        Start discussion
                    </a>
                </div>
                <?php endif; ?>

                <?php if (($pagination['total'] ?? 1) > 1): ?>
                <nav class="mt-6 flex flex-wrap items-center justify-between gap-3 border-y border-[#e6e8ec] py-3"
                    aria-label="Pagination">
                    <a href="<?= htmlspecialchars(($pagination['previous_url'] ?? '#'), ENT_QUOTES, 'UTF-8') ?>"
                        class="inline-flex h-10 items-center rounded-lg border px-4 text-sm font-semibold transition duration-200 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-black <?= !empty($pagination['has_previous']) ? 'border-[#c4c7c7] bg-white text-[#191c1f] hover:border-black' : 'pointer-events-none border-[#e6e8ec] bg-[#f5f6fa] text-[#444748]' ?>"
                        <?= empty($pagination['has_previous']) ? 'aria-disabled="true"' : '' ?>>
                        Previous
                    </a>
                    <span class="text-sm font-medium text-[#4B5563]" aria-current="page">
                        Page <?= htmlspecialchars(($pagination['current'] ?? 1), ENT_QUOTES, 'UTF-8') ?> of
                        <?= htmlspecialchars(($pagination['total'] ?? 1), ENT_QUOTES, 'UTF-8') ?>
                    </span>
                    <a href="<?= htmlspecialchars(($pagination['next_url'] ?? '#'), ENT_QUOTES, 'UTF-8') ?>"
                        class="inline-flex h-10 items-center rounded-lg border px-4 text-sm font-semibold transition duration-200 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-black <?= !empty($pagination['has_next']) ? 'border-[#c4c7c7] bg-white text-[#191c1f] hover:border-black' : 'pointer-events-none border-[#e6e8ec] bg-[#f5f6fa] text-[#444748]' ?>"
                        <?= empty($pagination['has_next']) ? 'aria-disabled="true"' : '' ?>>
                        Next
                    </a>
                </nav>
                <?php endif; ?>
            </section>

            <aside class="flex flex-col border-t border-[#e6e8ec] pt-2 lg:border-t-0 lg:border-l lg:pl-8 lg:pt-0"
                aria-label="Discussion sidebar">
                <section class="border-b border-[#e6e8ec] py-5 lg:pt-0" aria-labelledby="community-stats-heading">
                    <h2 id="community-stats-heading" class="text-base font-semibold text-[#0F172A]">Community statistics
                    </h2>
                    <dl class="mt-4 grid gap-3">
                        <div class="flex items-center justify-between gap-3">
                            <dt class="text-sm text-[#4B5563]">Discussions</dt>
                            <dd class="font-semibold text-[#111827]">
                                <?= htmlspecialchars($totalDiscussions, ENT_QUOTES, 'UTF-8') ?></dd>
                        </div>
                        <div class="flex items-center justify-between gap-3">
                            <dt class="text-sm text-[#4B5563]">Tracked modules</dt>
                            <dd class="font-semibold text-[#111827]">
                                <?= htmlspecialchars(count($moduleChips), ENT_QUOTES, 'UTF-8') ?></dd>
                        </div>
                        <div class="flex items-center justify-between gap-3">
                            <dt class="text-sm text-[#4B5563]">Popular threads</dt>
                            <dd class="font-semibold text-[#111827]">
                                <?= htmlspecialchars(count($popularDiscussions), ENT_QUOTES, 'UTF-8') ?></dd>
                        </div>
                    </dl>
                </section>

                <section class="border-b border-[#e6e8ec] py-5" aria-labelledby="trending-modules-heading">
                    <h2 id="trending-modules-heading" class="text-base font-semibold text-[#0F172A]">Trending modules
                    </h2>
                    <div class="mt-4 grid gap-2">
                        <?php if (!empty($trendingModules)): ?>
                        <?php foreach ($trendingModules as $module): ?>
                        <a href="<?= htmlspecialchars(($module['url'] ?? '#'), ENT_QUOTES, 'UTF-8') ?>"
                            class="flex min-w-0 items-center justify-between gap-3 rounded-lg px-3 py-2 transition duration-200 hover:bg-white focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-black">
                            <span class="min-w-0 font-mono text-xs font-semibold text-[#001b3d]">
                                <?= htmlspecialchars(($module['code'] ?? 'MODULE'), ENT_QUOTES, 'UTF-8') ?>
                            </span>
                            <span class="shrink-0 text-xs font-medium text-[#4B5563]">
                                <?= htmlspecialchars(($module['count'] ?? $module['posts'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
                            </span>
                        </a>
                        <?php endforeach; ?>
                        <?php else: ?>
                        <p class="text-sm leading-6 text-[#4B5563]">Module activity will appear after students start
                            discussions.</p>
                        <?php endif; ?>
                    </div>
                </section>

                <section class="border-b border-[#e6e8ec] py-5" aria-labelledby="popular-discussions-heading">
                    <h2 id="popular-discussions-heading" class="text-base font-semibold text-[#0F172A]">Popular
                        discussions</h2>
                    <div class="mt-4 grid gap-4">
                        <?php if (!empty($popularDiscussions)): ?>
                        <?php foreach ($popularDiscussions as $discussion): ?>
                        <a href="<?= htmlspecialchars(($discussion['url'] ?? '#'), ENT_QUOTES, 'UTF-8') ?>"
                            class="block rounded-md text-[#111827] transition duration-200 hover:text-emerald-800 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-black">
                            <span class="block break-words text-sm font-semibold leading-5" dir="auto">
                                <?= htmlspecialchars(($discussion['title'] ?? 'Untitled discussion'), ENT_QUOTES, 'UTF-8') ?>
                            </span>
                            <span class="mt-1 block text-xs font-medium text-[#4B5563]">
                                <?= htmlspecialchars(($discussion['replies'] ?? 0), ENT_QUOTES, 'UTF-8') ?>
                                <?= (($discussion['replies'] ?? 0)) === 1 ? 'reply' : 'replies' ?>
                            </span>
                        </a>
                        <?php endforeach; ?>
                        <?php else: ?>
                        <p class="text-sm leading-6 text-[#4B5563]">Popular questions will appear when replies are
                            added.</p>
                        <?php endif; ?>
                    </div>
                </section>

                <section class="pt-6" aria-labelledby="recently-active-heading">
                    <h2 id="recently-active-heading" class="text-base font-semibold text-[#0F172A]">Recently active</h2>
                    <div class="mt-4 grid gap-4">
                        <?php if (!empty($recentViewedDiscussions)): ?>
                        <?php foreach ($recentViewedDiscussions as $discussion): ?>
                        <a href="<?= htmlspecialchars(($discussion['url'] ?? '#'), ENT_QUOTES, 'UTF-8') ?>"
                            class="block rounded-md text-[#111827] transition duration-200 hover:text-emerald-800 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-black">
                            <span class="block break-words text-sm font-semibold leading-5" dir="auto">
                                <?= htmlspecialchars(($discussion['title'] ?? 'Untitled discussion'), ENT_QUOTES, 'UTF-8') ?>
                            </span>
                            <span class="mt-1 block text-xs font-medium text-[#4B5563]">
                                <?= htmlspecialchars(($discussion['module'] ?? 'MODULE'), ENT_QUOTES, 'UTF-8') ?>
                                &middot;
                                <?= htmlspecialchars(($discussion['time'] ?? 'Recently'), ENT_QUOTES, 'UTF-8') ?>
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
