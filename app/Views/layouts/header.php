<?php
/**
 * Variables passed from App\Core\Controller::view()
 *
 * @var bool $isLoggedIn
 * @var string $authName
 * @var string $authUsername
 * @var string|null $authAvatarUrl
 * @var string $authAvatarInitial
 * @var bool $isAdmin
 * @var string $pageTitle
 * @var string $authRole
 * @var string $csrfToken
 */
?>
<!doctype html>
<html lang="en">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?php
        $layoutHeaderDocumentTitle = preg_replace('/\s*-\s*$/', '', trim((string)($pageTitle ?? '')));
        $layoutHeaderDocumentTitle = $layoutHeaderDocumentTitle !== ''
            ? $layoutHeaderDocumentTitle . ' - Coursework Forum'
            : 'Coursework Forum';
        ?>
        <title><?= htmlspecialchars($layoutHeaderDocumentTitle, ENT_QUOTES, 'UTF-8') ?></title>
        <link rel="preconnect" href="https://moodlecurrent.gre.ac.uk">
        <link rel="shortcut icon"
              href="https://moodlecurrent.gre.ac.uk/pluginfile.php/1/core_admin/favicon/64x64/1778282230/v32.png">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link
                href="https://fonts.googleapis.com/css2?family=Google+Sans:ital,opsz,wght@0,17..18,400..700;1,17..18,400..700&family=Ledger&display=swap"
                rel="stylesheet">
        <link rel="stylesheet"
              href="<?= BASE_URL ?>/assets/css/app.css?v=<?= filemtime(ROOT_PATH . '/public/assets/css/app.css') ?>">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/prismjs@1.30.0/themes/prism-tomorrow.min.css">

    </head>

    <body class="min-h-screen scrollbar-d-none bg-ui-canvas text-ui-ink">
        <a href="#main-content"
           class="fixed top-3 left-3 z-[110] -translate-y-20 rounded-lg bg-brand-royal px-4 py-3 text-sm font-bold text-white transition focus:translate-y-0">
            Skip to content
        </a>
        <header
                class="group sticky top-0 z-[80] border-b border-ui-border bg-white/95 backdrop-blur data-[menu-open=true]:border-ui-border"
                data-menu-open="false">
            <div class="mx-auto flex min-h-20 w-full max-w-[100%-240px] items-center gap-8 px-5 sm:px-8 lg:px-16">
                <div class="flex shrink-0 items-center gap-3 sm:gap-4">
                    <button id="menu-btn" type="button" class="ui-icon-button cursor-pointer text-ui-ink"
                            aria-controls="mega-menu" aria-expanded="false" aria-label="Open navigation menu">
                        <svg viewBox="0 0 33 25" class="h-6 w-8" fill="none" aria-hidden="true" data-menu-icon>
                            <path data-menu-icon-line="top" d="M4.5 5.5h24" stroke="currentColor" stroke-width="3"
                                  stroke-linecap="square"/>
                            <path data-menu-icon-line="middle" d="M4.5 12.5h24" stroke="currentColor" stroke-width="3"
                                  stroke-linecap="square"/>
                            <path data-menu-icon-line="bottom" d="M4.5 19.5h24" stroke="currentColor" stroke-width="3"
                                  stroke-linecap="square"/>
                        </svg>
                    </button>

                    <a id="site-logo" href="<?= BASE_URL ?>/home" class="block w-[168px] shrink-0 sm:w-[190px]"
                       aria-label="University of Greenwich home">
                        <img src="<?= BASE_URL ?>/assets/images/shared/greenwich-logo.png" alt="University of Greenwich"
                             class="h-auto w-full object-contain">
                    </a>
                </div>

                <div
                        class="ml-auto hidden shrink-0 items-center gap-3 transition-opacity duration-200 group-data-[menu-open=true]:pointer-events-none group-data-[menu-open=true]:opacity-0 sm:flex">
                    <?php if ($isLoggedIn): ?>
                        <div class="relative" data-dropdown data-dropdown-close-on-select="true">
                            <button type="button"
                                    class="flex size-11 items-center justify-center overflow-hidden rounded-full border border-ui-border-strong p-px transition hover:border-brand-blue"
                                    aria-label="Open user menu" aria-expanded="false" data-dropdown-trigger>
                                <?php if ($authAvatarUrl !== null): ?>
                                    <img src="<?= htmlspecialchars($authAvatarUrl, ENT_QUOTES, 'UTF-8') ?>"
                                         alt="User avatar"
                                         class="size-full rounded-full object-cover">
                                <?php else: ?>
                                    <span
                                            class="flex size-full items-center justify-center rounded-full bg-brand-royal text-sm font-semibold text-white"
                                            aria-hidden="true">
                            <?= htmlspecialchars($authAvatarInitial, ENT_QUOTES, 'UTF-8') ?>
                        </span>
                                <?php endif; ?>
                            </button>

                            <div class="invisible absolute right-0 top-[calc(100%+12px)] z-[90] w-64 rounded-xl border border-ui-border bg-white p-2 opacity-0 shadow-ui-overlay transition data-[open=true]:visible data-[open=true]:opacity-100"
                                 data-dropdown-panel data-open="false">
                                <a href="<?= BASE_URL ?>/profile"
                                   class="flex items-center border-b border-ui-border px-3 py-3 transition hover:bg-ui-canvas">
                                    <div class="flex flex-col">
                                        <p class="text-sm font-semibold leading-5 text-ui-ink">
                                            <?= htmlspecialchars($authName, ENT_QUOTES, 'UTF-8') ?>
                                        </p>
                                        <?php if ($authUsername !== ''): ?>
                                            <p class="mt-0.5 text-xs leading-4 text-ui-muted">
                                                @<?= htmlspecialchars($authUsername, ENT_QUOTES, 'UTF-8') ?>
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                    <div class="ml-4">
                                        <?php
                                        $layoutHeaderRoleBadgeClass = match ($authRole) {
                                            'admin' => 'ui-badge-danger',
                                            'tutor' => 'ui-badge-tutor',
                                            default => 'ui-badge-brand',
                                        };
                                        ?>

                                        <span class="ui-badge <?= $layoutHeaderRoleBadgeClass ?>">
                                    <?= htmlspecialchars(ucfirst($authRole), ENT_QUOTES, 'UTF-8') ?>
                                </span>
                                    </div>

                                </a>
                                <a href="<?= BASE_URL ?>/modules"
                                   class="block rounded-lg px-3 py-2.5 text-sm font-semibold text-ui-ink transition hover:bg-ui-canvas hover:text-brand-royal">
                                    Modules
                                </a>
                                <a href="<?= BASE_URL ?>/discussions/create"
                                   class="block rounded-lg px-3 py-2.5 text-sm font-semibold text-ui-ink transition hover:bg-ui-canvas hover:text-brand-royal">
                                    Ask Question
                                </a>
                                <a href="<?= BASE_URL ?>/profile/preferences"
                                   class="block rounded-lg px-3 py-2.5 text-sm font-semibold text-ui-ink transition hover:bg-ui-canvas hover:text-brand-royal">
                                    Preferences
                                </a>
                                <?php if ($isAdmin): ?>
                                    <a href="<?= BASE_URL ?>/admin"
                                       class="block rounded-lg px-3 py-2.5 text-sm font-semibold text-brand-royal transition hover:bg-ui-canvas">
                                        Admin Panel
                                    </a>
                                <?php endif; ?>
                                <form action="<?= BASE_URL ?>/logout" method="post">
                                    <input type="hidden" name="_csrf_token"
                                           value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                                    <button type="submit"
                                            class="block w-full rounded-lg px-3 py-2.5 text-left text-sm font-semibold text-ui-danger transition hover:bg-ui-danger-soft">
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php else: ?>
                        <a href="<?= BASE_URL ?>/login" class="ui-button ui-button-secondary">
                            Sign In
                        </a>
                        <a href="<?= BASE_URL ?>/register" class="ui-button ui-button-primary">
                            Sign Up
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </header>
