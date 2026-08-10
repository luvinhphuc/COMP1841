<?php

use App\Helpers\FormatHelper;

/**
 * Variables passed from DiscussionController::index()
 *
 * @var array $discussions
 * @var array $filters
 * @var array $moduleChips
 * @var array $matchedModules
 * @var array $trendingModules
 * @var array $popularDiscussions
 * @var array $pagination
 * @var int $totalDiscussions
 * @var string $userFullName
 */
$discussionFeed = $discussions;
$searchValue = (string)($filters['q'] ?? '');
$activeModule = (string)($filters['module'] ?? '');
$activeSort = (string)($filters['sort'] ?? '');
$activeStatus = (string)($filters['status'] ?? '');
$hasSearch = trim($searchValue) !== '';
$discussionHeading = $activeSort === 'popular' ? 'Popular Discussions' : 'Newest Discussions';
$discussionCountLabel = $totalDiscussions === 1 ? '1 discussion' : $totalDiscussions . ' discussions';
$visibleCount = count($discussionFeed);
?>
<div class="flex h-80 w-full flex-col items-center justify-center bg-ui-canvas bg-cover bg-center bg-no-repeat px-4"
    role="img" aria-label="Coursework Forum pattern"
    style="background-image: url('<?= BASE_URL ?>/assets/images/1429687babc4fc3b76681856fbd652a5cd7cf56a.png');"
    data-motion-reveal>
    <h1 id="typing-text" class="pb-6 font-ui-ink font-serif text-4xl" data-motion-reveal>
        Welcome, <?= htmlspecialchars($userFullName) ?>!
    </h1>
    <?php
    $discussionSearchForm = [
        'search_value' => $searchValue,
        'per_page' => (int)($filters['per_page'] ?? 5),
        'modules' => $moduleChips,
        'active_module' => $activeModule,
        'active_status' => $activeStatus,
        'active_sort' => $activeSort,
    ];
    require ROOT_PATH . '/app/Views/discussions/partials/search_form.php';
    unset($discussionSearchForm);
    ?>
</div>
<section class="ui-page">
    <div class="ui-container flex flex-col gap-8">
        <section class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_auto] lg:items-end" aria-labelledby="discussions-title"
            data-motion-intro>
            <div class="max-w-3xl">
                <h2 id="discussions-title" class="ui-page-title">
                    <?= htmlspecialchars($discussionHeading, ENT_QUOTES, 'UTF-8') ?>
                </h2>
            </div>

            <a href="<?= BASE_URL ?>/discussions/create" class="ui-button ui-button-primary min-h-12 w-fit px-5">
                <svg viewBox="0 0 20 20" class="size-4 shrink-0" fill="none" aria-hidden="true">
                    <path d="M10 4.25v11.5M4.25 10h11.5" stroke="currentColor" stroke-width="1.7"
                        stroke-linecap="round" />
                </svg>
                Start discussion
            </a>
        </section>

        <div class="grid gap-8 lg:grid-cols-[minmax(0,1fr)_320px]">
            <section class="min-w-0" aria-labelledby="discussion-feed-heading">
                <div class="mb-4 flex flex-wrap items-end justify-between gap-3">
                    <div>
                        <h2 id="discussion-feed-heading" class="text-xl font-semibold leading-7 text-ui-ink">
                            <?= htmlspecialchars($discussionCountLabel, ENT_QUOTES, 'UTF-8') ?>
                        </h2>
                        <p class="mt-1 text-sm leading-6 text-ui-text">
                            <?= $activeSort === 'popular' ? 'Sorted by reply activity and views.' : 'Sorted by newest coursework activity.' ?>
                        </p>
                    </div>
                    <p class="text-sm font-medium text-ui-text">
                        Showing <?= htmlspecialchars($visibleCount, ENT_QUOTES, 'UTF-8') ?> of
                        <?= htmlspecialchars($totalDiscussions, ENT_QUOTES, 'UTF-8') ?>
                    </p>
                </div>

                <?php if (!empty($discussionFeed)): ?>
                <div class="grid gap-4" data-motion-list>
                    <?php foreach ($discussionFeed as $discussionCard): ?>
                    <?php
                            $discussionCardAnimated = false;
                            require ROOT_PATH . '/app/Views/discussions/partials/post_card.php';
                            ?>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <div class="rounded-xl border border-dashed border-ui-border-strong bg-white p-6 sm:p-8">
                    <?php if (!empty($matchedModules)): ?>
                    <h3 class="text-xl font-semibold leading-7 text-ui-ink">Module found</h3>
                    <p class="mt-2 max-w-2xl text-sm leading-6 text-ui-text">
                        No discussions match this search yet, but these modules match your query.
                    </p>
                    <div class="mt-5 grid gap-3">
                        <?php foreach ($matchedModules as $module): ?>
                        <a href="<?= htmlspecialchars(($module['url'] ?? '#'), ENT_QUOTES, 'UTF-8') ?>"
                            class="flex min-w-0 flex-wrap items-center justify-between gap-3 rounded-lg border border-ui-border bg-ui-canvas px-4 py-3 transition duration-200 hover:border-brand-blue hover:bg-white">
                            <span class="min-w-0">
                                <span class="block font-mono text-xs font-semibold text-brand-navy">
                                    <?= htmlspecialchars(($module['code'] ?? 'MODULE'), ENT_QUOTES, 'UTF-8') ?>
                                </span>
                                <span class="mt-1 block text-sm leading-5 text-ui-text" dir="auto">
                                    <?= htmlspecialchars(($module['name'] ?? 'Module'), ENT_QUOTES, 'UTF-8') ?>
                                </span>
                            </span>
                            <span class="shrink-0 text-sm font-semibold text-ui-ink">View discussions</span>
                        </a>
                        <?php endforeach; ?>
                    </div>
                    <?php else: ?>
                    <h3 class="text-xl font-semibold leading-7 text-ui-ink">No discussions yet</h3>
                    <p class="mt-2 max-w-2xl text-sm leading-6 text-ui-text">
                        Start the first coursework question for your module. Clear titles, a short explanation,
                        and any
                        relevant error message help classmates reply faster.
                    </p>
                    <?php endif; ?>
                    <a href="<?= BASE_URL ?>/discussions/create" class="ui-button ui-button-primary mt-5">
                        Start discussion
                    </a>
                </div>
                <?php endif; ?>

                <div class="ui-card mt-6 overflow-hidden" data-motion-reveal>
                    <?php
                    $paginationConfig = ['item_label' => 'Discussions', 'data' => $pagination];
                    require ROOT_PATH . '/app/Views/components/pagination.php';
                    unset($paginationConfig);
                    ?>
                </div>
            </section>

            <aside class="flex flex-col ui-card p-5 border-ui-border lg:sticky lg:top-28 lg:self-start lg:border-l"
                data-motion-reveal aria-label="Discussion sidebar">
                <section class="border-b border-ui-border py-5 lg:pt-0" aria-labelledby="forum-stats-heading">
                    <h2 id="forum-stats-heading" class="text-base font-semibold text-ui-ink">Forum statistics
                    </h2>
                    <dl class="mt-4 grid gap-3">
                        <div class="flex items-center justify-between gap-3">
                            <dt class="text-sm text-ui-text">Discussions</dt>
                            <dd class="font-semibold text-ui-ink">
                                <?= htmlspecialchars($totalDiscussions, ENT_QUOTES, 'UTF-8') ?></dd>
                        </div>
                        <div class="flex items-center justify-between gap-3">
                            <dt class="text-sm text-ui-text">Tracked modules</dt>
                            <dd class="font-semibold text-ui-ink">
                                <?= htmlspecialchars(count($moduleChips), ENT_QUOTES, 'UTF-8') ?></dd>
                        </div>
                        <div class="flex items-center justify-between gap-3">
                            <dt class="text-sm text-ui-text">Popular threads</dt>
                            <dd class="font-semibold text-ui-ink">
                                <?= htmlspecialchars(count($popularDiscussions), ENT_QUOTES, 'UTF-8') ?></dd>
                        </div>
                    </dl>
                </section>

                <section class="border-b border-ui-border py-5" aria-labelledby="trending-modules-heading">
                    <h2 id="trending-modules-heading" class="text-base font-semibold text-ui-ink">Trending modules
                    </h2>
                    <div class="mt-4 grid gap-2">
                        <?php if (!empty($trendingModules)): ?>
                        <?php foreach ($trendingModules as $module): ?>
                        <a href="<?= htmlspecialchars(($module['url'] ?? '#'), ENT_QUOTES, 'UTF-8') ?>"
                            class="flex min-w-0 items-center justify-between gap-3 rounded-lg px-3 py-2 transition duration-200 hover:bg-white">
                            <span class="min-w-0 font-mono text-xs font-semibold text-brand-navy">
                                🔥 <?= htmlspecialchars(($module['code'] ?? 'MODULE'), ENT_QUOTES, 'UTF-8') ?>
                            </span>
                            <span class="shrink-0 text-xs font-medium text-ui-text">
                                <?= (int)$module['discussion_count'] ?>
                                <?= (int)$module['discussion_count'] === 1 ? 'discussion' : 'discussions' ?>
                            </span>
                        </a>
                        <?php endforeach; ?>
                        <?php else: ?>
                        <p class="text-sm leading-6 text-ui-text">Module activity will appear after students start
                            discussions.</p>
                        <?php endif; ?>
                    </div>
                </section>
            </aside>
        </div>
    </div>
</section>
