<?php $greetingName = $greetingName ?? 'Student'; ?>

<section class="bg-[#f7f9fd] text-[#191c1f]">
    <div class="mx-auto flex w-full max-w-[1280px] flex-col gap-9 px-5 py-10 sm:px-8 lg:px-16 lg:py-14">
        <div class="grid items-end gap-6 lg:grid-cols-[minmax(0,1fr)_auto]" data-home-reveal>
            <div>
                <h1 class="text-3xl font-semibold leading-10 text-black sm:text-[32px]">
                    <span data-greeting-time></span>, <?= htmlspecialchars($greetingName, ENT_QUOTES, 'UTF-8') ?>
                    <span aria-hidden="true">&#128075;</span>
                </h1>
                <p class="mt-2 text-lg leading-7 text-[#444748]">
                    What do you need help with today?
                </p>
            </div>

            <a href="<?= BASE_URL ?>/questions/create"
                class="inline-flex h-12 w-fit items-center justify-center gap-2 rounded-lg bg-black px-6 text-sm font-medium tracking-[0.04em] text-white transition hover:bg-[#2a2d2f] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-black">
                <svg viewBox="0 0 20 20" class="size-4" fill="none" aria-hidden="true">
                    <path d="M10 4.25v11.5M4.25 10h11.5" stroke="currentColor" stroke-width="1.7"
                        stroke-linecap="round" />
                </svg>
                Ask Question
            </a>
        </div>

        <section class="flex flex-col gap-4" aria-labelledby="my-modules-heading" data-home-reveal>
            <div class="flex items-center justify-between gap-4">
                <h2 id="my-modules-heading" class="flex items-center gap-2 text-xl font-semibold leading-7">
                    <svg viewBox="0 0 24 24" class="size-6 text-[#315f90]" fill="none" aria-hidden="true">
                        <path d="m12 4 8 4.2-8 4.2-8-4.2L12 4Z" stroke="currentColor" stroke-width="1.6"
                            stroke-linejoin="round" />
                        <path d="M6.5 11.2v4.2c0 1.4 2.5 3 5.5 3s5.5-1.6 5.5-3v-4.2" stroke="currentColor"
                            stroke-width="1.6" stroke-linecap="round" />
                    </svg>
                    My Modules
                </h2>
                <a href="<?= BASE_URL ?>/modules"
                    class="group inline-flex items-center gap-2 text-xs font-bold leading-4 text-black transition hover:text-emerald-800">
                    View All Modules
                    <svg viewBox="0 0 20 20" class="size-4 transition group-hover:translate-x-1" fill="none"
                        aria-hidden="true">
                        <path d="M4 10h11M11 6l4 4-4 4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                </a>
            </div>

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <?php if (!empty($homeModules)): ?>
                <?php foreach ($homeModules as $module): ?>
                <a href="<?= htmlspecialchars($module['url'], ENT_QUOTES, 'UTF-8') ?>"
                    class="group flex min-h-[126px] min-w-0 flex-col rounded-lg border border-[#c4c7c7] bg-white p-4 transition focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-black"
                    data-home-card>
                    <div class="flex items-start justify-between gap-4">
                        <span
                            class="min-w-0 rounded bg-[#d6e3ff] px-2 py-1 font-mono text-xs font-medium leading-4 tracking-[0.05em] text-[#001b3d] [overflow-wrap:anywhere]">
                            <?= htmlspecialchars($module['code'], ENT_QUOTES, 'UTF-8') ?>
                        </span>
                        <?php if (!empty($module['active'])): ?>
                        <span class="mt-1 size-2 rounded-full bg-black" aria-label="Active module"></span>
                        <?php endif; ?>
                    </div>
                    <h3 class="mt-4 min-w-0 text-base font-semibold leading-6 text-[#191c1f] [overflow-wrap:anywhere]"
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
                    Your enrolled modules will appear here when they are available.
                </div>
                <?php endif; ?>
            </div>
        </section>

        <div class="grid gap-10 lg:grid-cols-[minmax(0,2fr)_minmax(280px,1fr)]">
            <section class="flex flex-col gap-4" aria-labelledby="newest-questions-heading" data-home-reveal>
                <div class="flex items-center justify-between gap-4">
                    <h2 id="newest-questions-heading" class="text-xl font-semibold leading-7 text-black">
                        Newest Questions
                    </h2>
                    <a href="<?= BASE_URL ?>/discussions"
                        class="group inline-flex items-center gap-2 text-xs font-bold leading-4 text-black transition hover:text-emerald-800">
                        Load More Questions
                        <svg viewBox="0 0 20 20" class="size-4 transition group-hover:translate-x-1" fill="none"
                            aria-hidden="true">
                            <path d="M4 10h11M11 6l4 4-4 4" stroke="currentColor" stroke-width="1.7"
                                stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </a>
                </div>

                <div class="flex flex-col gap-4">
                    <?php if (!empty($newestQuestions)): ?>
                    <?php foreach ($newestQuestions as $question): ?>
                    <article class="min-w-0 rounded-xl border border-[#c4c7c7] bg-white p-4 transition sm:p-6"
                        data-home-card>
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex min-w-0 flex-wrap gap-2">
                                <span
                                    class="max-w-full rounded bg-[#b6d0ff] px-2 py-0.5 text-[10px] font-medium leading-4 tracking-[0.08em] text-[#3f5881] [overflow-wrap:anywhere]">
                                    <?= htmlspecialchars($question['module'], ENT_QUOTES, 'UTF-8') ?>
                                </span>
                                <span
                                    class="inline-flex items-center gap-1 rounded px-2 py-0.5 text-[10px] font-medium leading-4 tracking-[0.08em] <?= ($question['status_tone'] ?? '') === 'green' ? 'bg-[#dcfce7] text-[#166534]' : 'bg-[#e6e8ec] text-[#444748]' ?>">
                                    <?php if (($question['status_tone'] ?? '') === 'green'): ?>
                                    <svg viewBox="0 0 16 16" class="size-3 shrink-0" fill="none" aria-hidden="true">
                                        <path d="m4 8.2 2.4 2.4L12 5" stroke="currentColor" stroke-width="1.7"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <?php endif; ?>
                                    <?= htmlspecialchars($question['status'], ENT_QUOTES, 'UTF-8') ?>
                                </span>
                            </div>
                            <span
                                class="shrink-0 text-right text-[10px] font-medium leading-4 tracking-[0.08em] text-[#444748]">
                                <?= htmlspecialchars($question['time'], ENT_QUOTES, 'UTF-8') ?>
                            </span>
                        </div>

                        <div class="mt-3 grid min-w-0 gap-4 sm:grid-cols-[minmax(0,1fr)_auto]">
                            <div class="min-w-0">
                                <h3 class="max-w-2xl text-lg font-medium leading-7 text-black [overflow-wrap:anywhere]"
                                    dir="auto">
                                    <a href="<?= htmlspecialchars($question['url'], ENT_QUOTES, 'UTF-8') ?>"
                                        class="rounded-sm transition hover:text-emerald-800 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-black">
                                        <?= htmlspecialchars($question['title'], ENT_QUOTES, 'UTF-8') ?>
                                    </a>
                                </h3>
                                <p class="mt-2 max-w-2xl text-base leading-6 text-[#444748] [overflow-wrap:anywhere]"
                                    dir="auto">
                                    <?= htmlspecialchars($question['excerpt'], ENT_QUOTES, 'UTF-8') ?>
                                </p>
                            </div>

                            <?php if (!empty($question['image'])): ?>
                            <img src="<?= htmlspecialchars($question['image'], ENT_QUOTES, 'UTF-8') ?>"
                                alt="<?= htmlspecialchars($question['preview_alt'], ENT_QUOTES, 'UTF-8') ?>"
                                loading="lazy" decoding="async"
                                class="h-[104px] w-full rounded-lg object-cover sm:w-[164px] sm:h-[128px]">
                            <?php endif; ?>
                        </div>

                        <div class="mt-5 flex flex-wrap items-center justify-between gap-4">
                            <div class="flex min-w-0 items-center gap-3">
                                <span
                                    class="flex size-6 shrink-0 items-center justify-center rounded-full bg-black text-[10px] text-white"
                                    aria-hidden="true">
                                    <?= htmlspecialchars($question['avatar'], ENT_QUOTES, 'UTF-8') ?>
                                </span>
                                <span
                                    class="min-w-0 text-xs font-medium leading-4 tracking-[0.05em] text-[#444748] [overflow-wrap:anywhere]"
                                    dir="auto">
                                    by <?= htmlspecialchars($question['author'], ENT_QUOTES, 'UTF-8') ?>
                                </span>
                            </div>

                            <div class="flex items-center gap-4 text-[10px] font-medium leading-4 tracking-[0.08em] text-[#444748]"
                                aria-label="Question engagement">
                                <span class="inline-flex items-center gap-1">
                                    <svg viewBox="0 0 18 18" class="size-4 shrink-0" fill="none" aria-hidden="true">
                                        <path d="M4 5.5h10v6H7.4L4 14.2V5.5Z" stroke="currentColor" stroke-width="1.4"
                                            stroke-linejoin="round" />
                                    </svg>
                                    <?= htmlspecialchars($question['replies'], ENT_QUOTES, 'UTF-8') ?>
                                </span>
                                <span class="inline-flex items-center gap-1">
                                    <svg viewBox="0 0 20 20" class="size-4 shrink-0" fill="none" aria-hidden="true">
                                        <path d="M2.5 10s2.7-4.5 7.5-4.5 7.5 4.5 7.5 4.5-2.7 4.5-7.5 4.5S2.5 10 2.5 10Z"
                                            stroke="currentColor" stroke-width="1.5" />
                                        <circle cx="10" cy="10" r="1.8" stroke="currentColor" stroke-width="1.5" />
                                    </svg>
                                    <?= htmlspecialchars($question['views'], ENT_QUOTES, 'UTF-8') ?>
                                </span>
                            </div>
                        </div>
                    </article>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <div
                        class="rounded-xl border border-dashed border-[#c4c7c7] bg-white p-6 text-sm leading-6 text-[#444748]">
                        No questions have been posted yet. Start a module discussion when you are ready.
                    </div>
                    <?php endif; ?>
                </div>
            </section>

            <aside class="flex flex-col gap-7 border-t border-[#d1d3d5] pt-7 lg:border-l lg:border-t-0 lg:pl-8 lg:pt-0"
                data-home-reveal>
                <section aria-labelledby="recent-activity-heading">
                    <h2 id="recent-activity-heading"
                        class="text-base font-bold uppercase leading-6 tracking-[0.1em] text-[#444748]">
                        Recent Activity
                    </h2>
                    <div class="mt-4 flex flex-col gap-4">
                        <?php if (!empty($recentActivities)): ?>
                        <?php foreach ($recentActivities as $activity): ?>
                        <div class="flex min-w-0 gap-3">
                            <span
                                class="mt-2 size-1.5 rounded-full <?= !empty($activity['active']) ? 'bg-black' : 'bg-[#c4c7c7]' ?>"></span>
                            <p class="min-w-0 text-sm font-bold leading-5 text-[#191c1f] [overflow-wrap:anywhere]"
                                dir="auto">
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
                                    class="min-w-0 [overflow-wrap:anywhere]"><?= htmlspecialchars($module['code'], ENT_QUOTES, 'UTF-8') ?></span>
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

                <section aria-labelledby="recent-post-view-heading">
                    <h2 id="recent-post-view-heading"
                        class="text-base font-bold uppercase leading-6 tracking-[0.1em] text-[#444748]">
                        Recent Post View
                    </h2>
                    <div class="mt-4 flex flex-col gap-4">
                        <?php if (!empty($recentPostViews)): ?>
                        <?php foreach ($recentPostViews as $activity): ?>
                        <div class="flex min-w-0 gap-3">
                            <span
                                class="mt-2 size-1.5 rounded-full <?= !empty($activity['active']) ? 'bg-black' : 'bg-[#c4c7c7]' ?>"></span>
                            <p class="min-w-0 text-sm font-bold leading-5 text-[#191c1f] [overflow-wrap:anywhere]"
                                dir="auto">
                                <?= htmlspecialchars($activity['label'], ENT_QUOTES, 'UTF-8') ?>
                                <span class="block text-[10px] font-medium leading-4 tracking-[0.08em] text-[#444748]">
                                    <?= htmlspecialchars($activity['time'], ENT_QUOTES, 'UTF-8') ?>
                                </span>
                            </p>
                        </div>
                        <?php endforeach; ?>
                        <?php else: ?>
                        <p class="text-sm leading-6 text-[#444748]">
                            Recently viewed discussions will appear here.
                        </p>
                        <?php endif; ?>
                    </div>
                </section>
            </aside>
        </div>
    </div>
</section>
