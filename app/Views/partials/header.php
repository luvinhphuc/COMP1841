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

                    <a id="site-logo" href="<?= BASE_URL ?: '/' ?>" class="block w-[168px] shrink-0 sm:w-[190px]"
                       aria-label="University of Greenwich home">
                        <img src="<?= BASE_URL ?>/assets/images/shared/greenwich-logo.png" alt="University of Greenwich"
                             class="h-auto w-full object-contain">
                    </a>
                </div>

                <form
                        class="relative hidden h-[52px] min-w-0 flex-1 items-center rounded-full border border-[#d8dbe2] bg-[#f5f6fa] px-5 transition-opacity duration-200 group-data-[menu-open=true]:pointer-events-none group-data-[menu-open=true]:opacity-0 md:flex"
                        role="search" action="<?= BASE_URL ?>/discussions" method="get">
                    <label class="sr-only" for="site-search">Search coursework questions</label>
                    <svg viewBox="0 0 18 18" class="mr-4 size-5 shrink-0 text-[#6b7280]" fill="none" aria-hidden="true">
                        <circle cx="8" cy="8" r="5.75" stroke="currentColor" stroke-width="1.5"/>
                        <path d="m12.25 12.25 3 3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>
                    <input id="site-search" name="q" type="search" placeholder="Search coursework questions..."
                           class="min-w-0 flex-1 bg-transparent text-base text-[#5f6368] outline-none placeholder:text-[#5f6368]">
                </form>

                <div class="ml-auto hidden shrink-0 items-center gap-3 transition-opacity duration-200 group-data-[menu-open=true]:pointer-events-none group-data-[menu-open=true]:opacity-0 sm:flex">
                    <?php if ($isLoggedIn): ?>
                        <button type="button"
                                class="relative flex size-11 items-center justify-center rounded-full text-[#3f3f3f] hover:text-black focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-black"
                                aria-label="Notifications">
                            <svg viewBox="0 0 29 36" class="h-8 w-7" fill="none" aria-hidden="true">
                                <path d="M14.5 32.5a4 4 0 0 0 3.8-2.8h-7.6a4 4 0 0 0 3.8 2.8Z" fill="currentColor"/>
                                <path
                                        d="M5.5 25.5h18l-2.4-3.4V14a6.6 6.6 0 0 0-5-6.4V5.4a1.6 1.6 0 1 0-3.2 0v2.2A6.6 6.6 0 0 0 7.9 14v8.1l-2.4 3.4Z"
                                        stroke="currentColor" stroke-width="2.4" stroke-linejoin="round"/>
                            </svg>
                            <span class="absolute right-2 top-1.5 size-2 rounded-full bg-[#ba1a1a]"></span>
                        </button>

                        <div class="relative" data-user-menu>
                            <button type="button"
                                    class="flex size-12 items-center justify-center overflow-hidden rounded-full border border-[#c4c7c7] p-px focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-black"
                                    aria-label="Open user menu"
                                    aria-expanded="false"
                                    data-user-menu-button>
                                <img src="<?= htmlspecialchars($authAvatarUrl, ENT_QUOTES, 'UTF-8') ?>" alt="User avatar"
                                     class="size-full rounded-full object-cover">
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
                                <a href="<?= BASE_URL ?>/profile"
                                   class="block rounded-md px-3 py-2 text-sm font-medium text-[#191c1f] transition hover:bg-[#f7f9fd]">
                                    Profile
                                </a>
                                <a href="<?= BASE_URL ?>/modules"
                                   class="block rounded-md px-3 py-2 text-sm font-medium text-[#191c1f] transition hover:bg-[#f7f9fd]">
                                    My Modules
                                </a>
                                <a href="<?= BASE_URL ?>/logout"
                                   class="block rounded-md px-3 py-2 text-sm font-medium text-[#ba1a1a] transition hover:bg-[#ba1a1a]/5">
                                    Logout
                                </a>
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
