<?php
/**
 * Discussion search form with its feature-specific filters.
 *
 * @var array{
 *     search_value: string,
 *     per_page?: int,
 *     modules: array<int, array{code?: string}>,
 *     active_module?: string,
 *     active_status?: string,
 *     active_sort?: string
 * } $discussionSearchForm
 */

$discussionSearchFormData = is_array($discussionSearchForm ?? null) ? $discussionSearchForm : [];
$discussionSearchValue = (string)($discussionSearchFormData['search_value'] ?? '');
$discussionSearchPerPage = max(1, (int)($discussionSearchFormData['per_page'] ?? 5));
$discussionSearchModules = is_array($discussionSearchFormData['modules'] ?? null)
    ? $discussionSearchFormData['modules']
    : [];
$discussionSearchActiveModule = trim((string)($discussionSearchFormData['active_module'] ?? ''));
$discussionSearchActiveStatus = trim((string)($discussionSearchFormData['active_status'] ?? ''));
$discussionSearchActiveSort = trim((string)($discussionSearchFormData['active_sort'] ?? ''));
$discussionSearchActiveFilterCount = count(array_filter(
    [$discussionSearchActiveModule, $discussionSearchActiveStatus, $discussionSearchActiveSort],
    static fn(string $discussionSearchFilterValue): bool => $discussionSearchFilterValue !== ''
));
$discussionSearchHasFilters = $discussionSearchActiveFilterCount > 0;
?>

<form
        action="<?= BASE_URL ?>/discussions"
        method="get"
        role="search"
        class="flex w-full max-w-4xl min-w-0 flex-col gap-3 sm:flex-row"
        data-discussion-filter-form
        data-motion-reveal
>
    <div class="min-w-0 flex-1">
        <label for="discussion-search" class="sr-only">Search discussions</label>
        <div class="relative flex min-h-12 items-center rounded-lg border border-ui-border-strong bg-white px-4 transition duration-200 focus-within:border-brand-blue focus-within:ring-3 focus-within:ring-brand-blue/15">
            <svg viewBox="0 0 18 18" class="mr-3 size-5 shrink-0 text-ui-muted" fill="none" aria-hidden="true">
                <circle cx="8" cy="8" r="5.75" stroke="currentColor" stroke-width="1.5"/>
                <path d="m12.25 12.25 3 3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
            </svg>
            <input
                    id="discussion-search"
                    name="q"
                    type="search"
                    value="<?= htmlspecialchars($discussionSearchValue, ENT_QUOTES, 'UTF-8') ?>"
                    placeholder="Search discussions"
                    class="min-w-0 flex-1 bg-transparent pr-12 text-base text-ui-ink outline-none placeholder:text-ui-muted"
            >

            <details class="absolute right-2 top-1/2 z-20 -translate-y-1/2">
                <summary
                        class="ui-icon-button size-9 cursor-pointer list-none [&::-webkit-details-marker]:hidden <?= $discussionSearchHasFilters ? 'bg-ui-brand-soft text-brand-royal' : '' ?>"
                        aria-label="Filter discussions"
                        title="Filter discussions"
                >
                    <svg viewBox="0 0 18 18" class="size-4" fill="none" aria-hidden="true">
                        <path d="M3.75 5h10.5M5.75 9h6.5M7.75 13h2.5"
                              stroke="currentColor"
                              stroke-width="1.6"
                              stroke-linecap="round"/>
                    </svg>
                </summary>

                <?php if ($discussionSearchHasFilters): ?>
                    <span class="pointer-events-none absolute -right-1 -top-1 inline-flex size-4 items-center justify-center rounded-full bg-brand-royal text-[0.625rem] font-bold leading-none text-white">
                        <?= $discussionSearchActiveFilterCount ?>
                    </span>
                <?php endif; ?>

                <div class="ui-card absolute right-0 top-full mt-3 w-[min(88vw,360px)] p-4 shadow-ui-overlay">
                    <div class="grid gap-3">
                        <input type="hidden" name="per_page" value="<?= $discussionSearchPerPage ?>" data-default-value="5">

                        <div>
                            <label for="module-filter" class="ui-label">Module</label>
                            <select id="module-filter" name="module" class="ui-input h-12 bg-ui-canvas">
                                <option value="">All modules</option>
                                <?php foreach ($discussionSearchModules as $discussionSearchModule): ?>
                                    <?php $discussionSearchModuleCode = (string)($discussionSearchModule['code'] ?? ''); ?>
                                    <option value="<?= htmlspecialchars($discussionSearchModuleCode, ENT_QUOTES, 'UTF-8') ?>"
                                        <?= strcasecmp($discussionSearchActiveModule, $discussionSearchModuleCode) === 0 ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($discussionSearchModuleCode, ENT_QUOTES, 'UTF-8') ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div>
                            <label for="status-filter" class="ui-label">Status</label>
                            <select id="status-filter" name="status" class="ui-input h-12 bg-ui-canvas">
                                <option value="">All status</option>
                                <option value="open" <?= $discussionSearchActiveStatus === 'open' ? 'selected' : '' ?>>
                                    Open
                                </option>
                                <option value="solved" <?= $discussionSearchActiveStatus === 'solved' ? 'selected' : '' ?>>
                                    Solved
                                </option>
                            </select>
                        </div>

                        <div>
                            <label for="sort-filter" class="ui-label">Sort</label>
                            <select id="sort-filter" name="sort" class="ui-input h-12 bg-ui-canvas">
                                <option value="">Newest</option>
                                <option value="popular" <?= $discussionSearchActiveSort === 'popular' ? 'selected' : '' ?>>
                                    Popular
                                </option>
                            </select>
                        </div>

                        <div class="mt-4 flex justify-between">
                            <a href="<?= BASE_URL ?>/discussions"
                               class="ui-button ui-button-secondary min-h-12">Clear</a>
                            <button type="submit" class="ui-button ui-button-primary min-h-12">Apply</button>
                        </div>
                    </div>
                </div>
            </details>
        </div>
    </div>

    <button type="submit" class="ui-button ui-button-primary min-h-12 w-full px-5 sm:w-fit">
        Search
    </button>
</form>
