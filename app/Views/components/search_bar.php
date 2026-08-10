<?php
/**
 * Variables passed by a parent view before including this partial
 *
 * @var array{
 *     id: string,
 *     name: string,
 *     value: string,
 *     label: string,
 *     placeholder: string,
 *     button_label: string
 * } $searchBar
 */
$searchBarId = trim((string)$searchBar['id']);
$searchBarName = trim((string)$searchBar['name']);
$searchBarValue = (string)$searchBar['value'];
$searchBarLabel = trim((string)$searchBar['label']);
$searchBarPlaceholder = trim((string)$searchBar['placeholder']);
$searchBarButtonLabel = trim((string)$searchBar['button_label']);
$searchBarInputClass = 'min-w-0 flex-1 bg-transparent text-base text-ui-ink outline-none placeholder:text-ui-muted';
?>

<div class="flex-1 min-w-0">
    <label for="<?= htmlspecialchars($searchBarId, ENT_QUOTES, 'UTF-8') ?>" class="sr-only">
        <?= htmlspecialchars($searchBarLabel, ENT_QUOTES, 'UTF-8') ?>
    </label>
    <div
        class="relative flex min-h-12 items-center rounded-lg border border-ui-border-strong bg-white px-4 transition duration-200 focus-within:border-brand-blue focus-within:ring-3 focus-within:ring-brand-blue/15">
        <svg viewBox="0 0 18 18" class="mr-3 size-5 shrink-0 text-ui-muted" fill="none" aria-hidden="true">
            <circle cx="8" cy="8" r="5.75" stroke="currentColor" stroke-width="1.5" />
            <path d="m12.25 12.25 3 3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
        </svg>
        <input id="<?= htmlspecialchars($searchBarId, ENT_QUOTES, 'UTF-8') ?>"
            name="<?= htmlspecialchars($searchBarName, ENT_QUOTES, 'UTF-8') ?>" type="search"
            value="<?= htmlspecialchars($searchBarValue, ENT_QUOTES, 'UTF-8') ?>"
            placeholder="<?= htmlspecialchars($searchBarPlaceholder, ENT_QUOTES, 'UTF-8') ?>"
            class="<?= htmlspecialchars($searchBarInputClass, ENT_QUOTES, 'UTF-8') ?>">
    </div>
</div>

<button type="submit" class="ui-button ui-button-primary min-h-12 w-full px-5 sm:w-fit">
    <?= htmlspecialchars($searchBarButtonLabel, ENT_QUOTES, 'UTF-8') ?>
</button>