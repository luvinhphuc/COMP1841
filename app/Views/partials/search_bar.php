<?php
/**
 * Variables passed by a parent view before including this partial
 *
 * @var array $searchBar
 */
$searchBarId = trim((string) $searchBar['id']);
$searchBarName = trim((string) $searchBar['name']);
$searchBarValue = (string) $searchBar['value'];
$searchBarLabel = trim((string) $searchBar['label']);
$searchBarPlaceholder = trim((string) $searchBar['placeholder']);
$searchBarButtonLabel = trim((string) $searchBar['button_label']);
?>

<div class="min-w-0">
    <label for="<?= htmlspecialchars($searchBarId, ENT_QUOTES, 'UTF-8') ?>" class="sr-only">
        <?= htmlspecialchars($searchBarLabel, ENT_QUOTES, 'UTF-8') ?>
    </label>
    <div
        class="flex min-h-12 items-center rounded-lg bg-white px-4 ring-1 ring-[#d1d3d5] transition duration-200 focus-within:ring-2 focus-within:ring-black/15">
        <svg viewBox="0 0 18 18" class="mr-3 size-5 shrink-0 text-[#4B5563]" fill="none" aria-hidden="true">
            <circle cx="8" cy="8" r="5.75" stroke="currentColor" stroke-width="1.5" />
            <path d="m12.25 12.25 3 3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
        </svg>
        <input
            id="<?= htmlspecialchars($searchBarId, ENT_QUOTES, 'UTF-8') ?>"
            name="<?= htmlspecialchars($searchBarName, ENT_QUOTES, 'UTF-8') ?>"
            type="search"
            value="<?= htmlspecialchars($searchBarValue, ENT_QUOTES, 'UTF-8') ?>"
            placeholder="<?= htmlspecialchars($searchBarPlaceholder, ENT_QUOTES, 'UTF-8') ?>"
            class="min-w-0 flex-1 bg-transparent text-base text-[#111827] outline-none placeholder:text-[#4B5563]">
    </div>
</div>

<button
    type="submit"
    class="inline-flex min-h-12 w-full items-center justify-center rounded-lg bg-black px-5 text-sm font-semibold text-white transition duration-200 hover:bg-[#2a2d2f] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-black sm:w-fit">
    <?= htmlspecialchars($searchBarButtonLabel, ENT_QUOTES, 'UTF-8') ?>
</button>
