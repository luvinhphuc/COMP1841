<?php
/**
 * Variables passed from DashboardController::index()
 *
 * @var string $greetingName
 * @var array $homeModules
 * @var array $myQuestions
 * @var array $questionPagination
 * @var array $recentActivities
 * @var array $trendingModules
 */
?>

<section class="bg-[#f7f9fd] text-[#191c1f]">
    <div class="mx-auto flex w-full max-w-[1280px] flex-col gap-9 px-5 py-10 sm:px-8 lg:px-16 lg:py-14">
        <div class="grid items-end gap-6 lg:grid-cols-[minmax(0,1fr)_auto]" data-dashboard-reveal>
            <div>
                <h1 class="text-3xl font-semibold leading-10 text-black sm:text-[32px]">
                    <span data-greeting-time></span>, <?= htmlspecialchars($greetingName, ENT_QUOTES, 'UTF-8') ?>
                    <span aria-hidden="true">&#128075;</span>
                </h1>
                <p class="mt-2 text-lg leading-7 text-[#444748]">
                    What do you need help with today?
                </p>
            </div>

            <a href="<?= BASE_URL ?>/discussions/create"
                class="inline-flex h-12 w-fit items-center justify-center gap-2 rounded-lg bg-black px-6 text-sm font-medium tracking-[0.04em] text-white transition hover:bg-[#2a2d2f] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-black">
                <svg viewBox="0 0 20 20" class="size-4" fill="none" aria-hidden="true">
                    <path d="M10 4.25v11.5M4.25 10h11.5" stroke="currentColor" stroke-width="1.7"
                        stroke-linecap="round" />
                </svg>
                Ask Question
            </a>
        </div>

        <section class="flex flex-col gap-4" aria-labelledby="modules-heading" data-dashboard-reveal>
            <div class="flex flex-wrap items-center justify-between gap-4">
                <h2 id="modules-heading" class="flex items-center gap-2 text-xl font-semibold leading-7">
                    <svg viewBox="0 0 24 24" class="size-6 text-[#315f90]" fill="none" aria-hidden="true">
                        <path d="m12 4 8 4.2-8 4.2-8-4.2L12 4Z" stroke="currentColor" stroke-width="1.6"
                            stroke-linejoin="round" />
                        <path d="M6.5 11.2v4.2c0 1.4 2.5 3 5.5 3s5.5-1.6 5.5-3v-4.2" stroke="currentColor"
                            stroke-width="1.6" stroke-linecap="round" />
                    </svg>
                    Modules
                </h2>
                <a href="<?= BASE_URL ?>/modules"
                    class="group inline-flex items-center gap-2 text-xs font-bold leading-4 text-black transition hover:text-emerald-800">
                    View All Modules
                    <svg viewBox="0 0 20 20" class="size-4 transition group-hover:translate-x-1" fill="none"
                        aria-hidden="true">
                        <path d="M4 10h11M11 6l4 4-4 4" stroke="currentColor" stroke-width="1.7"
                            stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </a>
            </div>

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <?php if (!empty($homeModules)): ?>
                <?php foreach ($homeModules as $module): ?>
                <a href="<?= htmlspecialchars($module['url'], ENT_QUOTES, 'UTF-8') ?>"
                    class="group flex min-h-[126px] min-w-0 flex-col rounded-lg border border-[#c4c7c7] bg-white p-4 transition focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-black"
                    data-dashboard-card>
                    <div class="flex items-start justify-between gap-4">
                        <span
                            class="min-w-0 truncate rounded bg-[#d6e3ff] px-2 py-1 font-mono text-xs font-medium leading-4 tracking-[0.05em] text-[#001b3d]">
                            <?= htmlspecialchars($module['code'], ENT_QUOTES, 'UTF-8') ?>
                        </span>
                    </div>
                    <h3 class="mt-4 min-w-0 break-words text-base font-semibold leading-6 text-[#191c1f]"
                        dir="auto">
                        <?= htmlspecialchars($module['name'], ENT_QUOTES, 'UTF-8') ?>
                    </h3>
                    <p class="mt-2 flex min-w-0 items-center gap-1 text-sm leading-5 text-[#444748]">
                        <svg viewBox="0 0 18 18" class="size-4 shrink-0" fill="none" aria-hidden="true">
                            <path d="M4 5.5h10v6H7.4L4 14.2V5.5Z" stroke="currentColor" stroke-width="1.4"
                                stroke-linejoin="round" />
                        </svg>
                        <?= htmlspecialchars($module['discussion_count_label'], ENT_QUOTES, 'UTF-8') ?>
                    </p>
                </a>
                <?php endforeach; ?>
                <?php else: ?>
                <div
                    class="rounded-lg border border-dashed border-[#c4c7c7] bg-white p-5 text-sm leading-6 text-[#444748] sm:col-span-2 lg:col-span-4">
                    Modules will appear here when they are available.
                </div>
                <?php endif; ?>
            </div>
        </section>

        <div class="grid gap-10 lg:grid-cols-[minmax(0,2fr)_minmax(280px,1fr)]">
            <section class="flex flex-col gap-4" aria-labelledby="my-questions-heading" data-dashboard-reveal>
                <div class="flex items-center justify-between gap-4">
                    <h2 id="my-questions-heading" class="text-xl font-semibold leading-7 text-black">
                        My Questions
                    </h2>
                    <a href="<?= BASE_URL ?>/discussions"
                        class="group inline-flex items-center gap-2 text-xs font-bold leading-4 text-black transition hover:text-emerald-800">
                        View All Discussions
                        <svg viewBox="0 0 20 20" class="size-4 transition group-hover:translate-x-1" fill="none"
                            aria-hidden="true">
                            <path d="M4 10h11M11 6l4 4-4 4" stroke="currentColor" stroke-width="1.7"
                                stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </a>
                </div>

                <div class="flex flex-col gap-4">
                    <?php if (!empty($myQuestions)): ?>
                    <?php foreach ($myQuestions as $question): ?>
                    <?php
                    $postCard = $question;
                    $postCardAnimated = true;
                    require ROOT_PATH . '/app/Views/partials/post_card.php';
                    ?>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <div
                        class="rounded-xl border border-dashed border-[#c4c7c7] bg-white p-6 text-sm leading-6 text-[#444748]">
                        <p>You have not asked any questions yet.</p>
                        <a href="<?= BASE_URL ?>/discussions/create"
                            class="mt-4 inline-flex min-h-11 items-center justify-center rounded-lg bg-black px-5 text-sm font-semibold text-white transition hover:bg-[#2a2d2f] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-black">
                            Ask your first question
                        </a>
                    </div>
                    <?php endif; ?>
                </div>

                <?php if (($questionPagination['total'] ?? 1) > 1): ?>
                <nav class="flex items-center justify-between gap-4 border-t border-[#d1d3d5] pt-4"
                    aria-label="My Questions pagination">
                    <?php if (!empty($questionPagination['has_previous'])): ?>
                    <a href="<?= htmlspecialchars($questionPagination['previous_url'], ENT_QUOTES, 'UTF-8') ?>"
                        class="inline-flex h-10 items-center rounded-lg border border-[#c4c7c7] bg-white px-4 text-sm font-semibold text-[#191c1f] transition hover:border-black focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-black">
                        Previous
                    </a>
                    <?php else: ?>
                    <span class="inline-flex h-10 items-center rounded-lg border border-[#e6e8ec] bg-[#f5f6fa] px-4 text-sm font-semibold text-[#777]"
                        aria-disabled="true">
                        Previous
                    </span>
                    <?php endif; ?>

                    <span class="text-sm font-semibold text-[#444748]" aria-current="page">
                        Page <?= (int) ($questionPagination['current'] ?? 1) ?> of
                        <?= (int) ($questionPagination['total'] ?? 1) ?>
                    </span>

                    <?php if (!empty($questionPagination['has_next'])): ?>
                    <a href="<?= htmlspecialchars($questionPagination['next_url'], ENT_QUOTES, 'UTF-8') ?>"
                        class="inline-flex h-10 items-center rounded-lg border border-[#c4c7c7] bg-white px-4 text-sm font-semibold text-[#191c1f] transition hover:border-black focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-black">
                        Next
                    </a>
                    <?php else: ?>
                    <span class="inline-flex h-10 items-center rounded-lg border border-[#e6e8ec] bg-[#f5f6fa] px-4 text-sm font-semibold text-[#777]"
                        aria-disabled="true">
                        Next
                    </span>
                    <?php endif; ?>
                </nav>
                <?php endif; ?>
            </section>

            <aside class="flex flex-col gap-7 border-t border-[#d1d3d5] pt-7 lg:border-l lg:border-t-0 lg:pl-8 lg:pt-0"
                data-dashboard-reveal>
                <section aria-labelledby="recent-activity-heading">
                    <h2 id="recent-activity-heading" class="text-base font-bold uppercase leading-6 text-[#444748]">
                        Recent Activity
                    </h2>
                    <div class="mt-4 flex flex-col gap-4">
                        <?php if (!empty($recentActivities)): ?>
                        <?php foreach ($recentActivities as $activity): ?>
                        <div class="flex min-w-0 gap-3">
                            <span
                                class="mt-2 size-1.5 rounded-full <?= !empty($activity['active']) ? 'bg-black' : 'bg-[#c4c7c7]' ?>"></span>
                            <p class="min-w-0 text-sm font-bold leading-5 text-[#191c1f]" dir="auto">
                                <?= htmlspecialchars($activity['label'], ENT_QUOTES, 'UTF-8') ?>
                                <span class="block text-[10px] font-medium leading-4 tracking-[0.08em] text-[#444748]">
                                    <?= htmlspecialchars($activity['time'], ENT_QUOTES, 'UTF-8') ?>
                                </span>
                            </p>
                        </div>
                        <?php endforeach; ?>
                        <?php else: ?>
                        <p class="text-sm leading-6 text-[#444748]">
                            Recent posts and replies will appear here.
                        </p>
                        <?php endif; ?>
                    </div>
                </section>

                <section class="border-y border-[#d1d3d5] py-4" aria-labelledby="trending-modules-heading">
                    <h2 id="trending-modules-heading"
                        class="text-base font-bold uppercase leading-6 tracking-[0.1em] text-[#444748]">
                        Trending Modules
                    </h2>
                    <div class="mt-3 flex flex-col gap-2">
                        <?php if (!empty($trendingModules)): ?>
                        <?php foreach ($trendingModules as $module): ?>
                        <a href="<?= htmlspecialchars($module['url'], ENT_QUOTES, 'UTF-8') ?>"
                            class="group flex min-w-0 items-center justify-between gap-3 rounded-lg p-2 transition hover:bg-white focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-black">
                            <span
                                class="flex min-w-0 items-center gap-2 text-xs font-bold tracking-[0.05em] text-[#191c1f]">
                                <svg viewBox="0 0 16 16" class="size-4 shrink-0 text-[#f97316]" fill="currentColor"
                                    aria-hidden="true">
                                    <path
                                        d="M8.8 1.9c.4 2.2-.5 3.5-1.5 4.6-.9 1-1.8 1.9-1.5 3.4.2 1 1 1.7 2.1 1.7 1.4 0 2.3-1 2.3-2.4 0-.6-.2-1.2-.5-1.8 1.5.7 2.5 2.1 2.5 3.8 0 2.2-1.8 3.9-4.2 3.9s-4.2-1.7-4.2-4c0-2 1.1-3.2 2.2-4.4 1-1.1 2-2.2 2.8-4.8Z" />
                                </svg>
                                <span
                                    class="min-w-0 truncate"><?= htmlspecialchars($module['code'], ENT_QUOTES, 'UTF-8') ?></span>
                            </span>
                            <span class="shrink-0 text-[10px] font-medium tracking-[0.08em] text-[#444748]">
                                <?= htmlspecialchars($module['posts'], ENT_QUOTES, 'UTF-8') ?>
                            </span>
                        </a>
                        <?php endforeach; ?>
                        <?php else: ?>
                        <p class="text-sm leading-6 text-[#444748]">
                            Module activity will appear after students start posting.
                        </p>
                        <?php endif; ?>
                    </div>
                </section>
            </aside>
        </div>
    </div>
</section>
