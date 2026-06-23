<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>COMP1841</title>
        <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/app.css">
    </head>

    <body class="min-h-screen bg-white font-sans text-[#222]">
        <header class="flex items-center gap-32 border-b border-black bg-white px-10 py-3">
            <!-- Left -->
            <div class="flex shrink-0 items-center gap-4">
                <button type="button"
                        class="flex h-10 w-10 items-center justify-center text-[#333] hover:text-black"
                        aria-label="Open navigation">
                    <svg viewBox="0 0 33 25" class="h-6 w-8" fill="none" aria-hidden="true">
                        <path d="M4.5 5.5h24M4.5 12.5h24M4.5 19.5h24"
                              stroke="currentColor"
                              stroke-width="3"
                              stroke-linecap="square"/>
                    </svg>
                </button>

                <a href="<?= BASE_URL ?>" class="block w-[190px] shrink-0" aria-label="University of Greenwich home">
                    <img
                            src="<?= BASE_URL ?>/assets/images/shared/greenwich-logo.png"
                            alt="University of Greenwich"
                            class="h-full object-contain"
                    >
                </a>
            </div>

            <!-- Search -->
            <form class="relative flex h-[52px] min-w-0 flex-1 items-center rounded-full border border-[#d8dbe2] bg-[#f5f6fa] px-5"
                  role="search">
                <label class="sr-only" for="site-search">Search coursework questions</label>

                <svg viewBox="0 0 18 18"
                     class="mr-4 h-5 w-5 shrink-0 text-[#6b7280]"
                     fill="none"
                     aria-hidden="true">
                    <circle cx="8" cy="8" r="5.75" stroke="currentColor" stroke-width="1.5"/>
                    <path d="m12.25 12.25 3 3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                </svg>

                <input
                        id="site-search"
                        name="q"
                        type="search"
                        placeholder="Search coursework questions..."
                        class="min-w-0 flex-1 bg-transparent text-[16px] text-[#5f6368] outline-none placeholder:text-[#5f6368]"
                >
            </form>

            <!-- Right -->
            <div class="flex shrink-0 items-center justify-end gap-4">
                <button type="button"
                        class="relative flex h-11 w-11 items-center justify-center rounded-full text-[#3f3f3f] hover:text-black"
                        aria-label="Notifications">
                    <svg viewBox="0 0 29 36" class="h-8 w-7" fill="none" aria-hidden="true">
                        <path d="M14.5 32.5a4 4 0 0 0 3.8-2.8h-7.6a4 4 0 0 0 3.8 2.8Z" fill="currentColor"/>
                        <path d="M5.5 25.5h18l-2.4-3.4V14a6.6 6.6 0 0 0-5-6.4V5.4a1.6 1.6 0 1 0-3.2 0v2.2A6.6 6.6 0 0 0 7.9 14v8.1l-2.4 3.4Z"
                              stroke="currentColor"
                              stroke-width="2.4"
                              stroke-linejoin="round"/>
                    </svg>
                    <span class="absolute right-2 top-1.5 size-2 rounded-full bg-[#ba1a1a]"></span>
                </button>

                <a href="#"
                   class="flex size-12 items-center justify-center overflow-hidden rounded-full border border-[#c4c7c7] p-px"
                   aria-label="User profile">
                    <img
                            src="<?= BASE_URL ?>/assets/images/header/user-avatar.jpeg"
                            alt="User avatar"
                            class="size-full rounded-full object-cover"
                    >
                </a>
            </div>
        </header>

        <main>