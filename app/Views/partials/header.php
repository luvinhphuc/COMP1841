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
 */
?>
<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Coursework Discussion</title>
        <link rel="shortcut icon" href="https://moodlecurrent.gre.ac.uk/pluginfile.php/1/core_admin/favicon/64x64/1778282230/v32.png">
        <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/app.css?v=<?= filemtime(ROOT_PATH . '/public/assets/css/app.css') ?>">
    </head>

    <body class="min-h-screen scrollbar-d-none bg-white font-sans text-[#222]">
        <header class="group sticky top-0 z-[80] border-b border-black bg-white data-[menu-open=true]:border-black/10"
                data-menu-open="false">
            <div class="flex min-h-20 items-center gap-12 px-5 sm:px-8 lg:gap-36 lg:px-32 xl:gap-48">
                <div class="flex shrink-0 items-center gap-3 sm:gap-4">
                    <button id="menu-btn" type="button"
                            class="group flex size-11 cursor-pointer items-center justify-center text-[#333] hover:text-black focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-black"
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

                    <a id="site-logo" href="<?= $isLoggedIn ? BASE_URL . '/dashboard' : (BASE_URL ?: '/') ?>" class="block w-[168px] shrink-0 sm:w-[190px]"
                       aria-label="University of Greenwich home">
                        <img src="<?= BASE_URL ?>/assets/images/shared/greenwich-logo.png" alt="University of Greenwich"
                             class="h-auto w-full object-contain">
                    </a>
                </div>

                <div class="ml-auto hidden shrink-0 items-center gap-3 transition-opacity duration-200 group-data-[menu-open=true]:pointer-events-none group-data-[menu-open=true]:opacity-0 sm:flex">
                    <?php if ($isLoggedIn): ?>
                        <div class="relative" data-user-menu>
                            <button type="button"
                                    class="flex size-12 items-center justify-center overflow-hidden rounded-full border border-[#c4c7c7] p-px focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-black"
                                    aria-label="Open user menu"
                                    aria-expanded="false"
                                    data-user-menu-button>
                                <?php if ($authAvatarUrl !== null): ?>
                                    <img src="<?= htmlspecialchars($authAvatarUrl, ENT_QUOTES, 'UTF-8') ?>" alt="User avatar"
                                         class="size-full rounded-full object-cover">
                                <?php else: ?>
                                    <span class="flex size-full items-center justify-center rounded-full bg-[#1E3A8A] text-sm font-semibold text-white"
                                          aria-hidden="true">
                                        <?= htmlspecialchars($authAvatarInitial, ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                <?php endif; ?>
                            </button>

                            <div class="invisible absolute right-0 top-[calc(100%+12px)] z-[90] w-64 rounded-lg border border-[#c4c7c7] bg-white p-2 opacity-0 shadow-[0_8px_8px_rgba(15,23,42,0.06)] transition data-[open=true]:visible data-[open=true]:opacity-100"
                                 data-user-menu-dropdown data-open="false">
                                <div class="border-b border-[#e6e8ec] px-3 py-3">
                                    <p class="text-sm font-semibold leading-5 text-[#191c1f]">
                                        <?= htmlspecialchars($authName, ENT_QUOTES, 'UTF-8') ?>
                                    </p>
                                    <?php if ($authUsername !== ''): ?>
                                        <p class="mt-0.5 text-xs leading-4 text-[#444748]">
                                            @<?= htmlspecialchars($authUsername, ENT_QUOTES, 'UTF-8') ?>
                                        </p>
                                    <?php endif; ?>
                                </div>
                                <a href="<?= BASE_URL ?>/dashboard"
                                   class="block rounded-md px-3 py-2 text-sm font-medium text-[#191c1f] transition hover:bg-[#f7f9fd]">
                                    Dashboard
                                </a>
                                <a href="<?= BASE_URL ?>/modules"
                                   class="block rounded-md px-3 py-2 text-sm font-medium text-[#191c1f] transition hover:bg-[#f7f9fd]">
                                    Modules
                                </a>
                                <a href="<?= BASE_URL ?>/discussions/create"
                                   class="block rounded-md px-3 py-2 text-sm font-medium text-[#191c1f] transition hover:bg-[#f7f9fd]">
                                    Ask Question
                                </a>
                                <a href="<?= BASE_URL ?>/preferences"
                                   class="block rounded-md px-3 py-2 text-sm font-medium text-[#191c1f] transition hover:bg-[#f7f9fd]">
                                    Preferences
                                </a>
                                <?php if ($isAdmin): ?>
                                    <a href="<?= BASE_URL ?>/admin"
                                       class="block rounded-md px-3 py-2 text-sm font-semibold text-[#315f90] transition hover:bg-[#f7f9fd]">
                                        Admin Area
                                    </a>
                                <?php endif; ?>
                                <form action="<?= BASE_URL ?>/logout" method="post">
                                    <input type="hidden" name="_csrf_token"
                                        value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                                    <button type="submit"
                                        class="block w-full rounded-md px-3 py-2 text-left text-sm font-medium text-[#ba1a1a] transition hover:bg-[#ba1a1a]/5">
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php else: ?>
                        <a href="<?= BASE_URL ?>/login"
                           class="inline-flex h-11 items-center justify-center rounded-lg border border-[#c4c7c7] px-4 text-sm font-semibold text-[#191c1f] transition hover:border-black focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-black">
                            Sign In
                        </a>
                        <a href="<?= BASE_URL ?>/register"
                           class="inline-flex h-11 items-center justify-center rounded-lg bg-black px-4 text-sm font-semibold text-white transition hover:bg-[#2a2d2f] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-black">
                            Sign Up
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </header>
