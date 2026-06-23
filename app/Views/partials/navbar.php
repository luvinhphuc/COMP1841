<?php
$currentPath = trim(parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH), '/');
$basePath = trim(parse_url(BASE_URL, PHP_URL_PATH) ?? '', '/');

if ($basePath !== '' && strpos($currentPath, $basePath) === 0) {
    $currentPath = trim(substr($currentPath, strlen($basePath)), '/');
}

$url = static function (string $path = ''): string {
    if ($path === '') {
        return BASE_URL !== '' ? BASE_URL : '/';
    }

    return BASE_URL . '/' . ltrim($path, '/');
};

$primaryLinks = [
    ['label' => 'Home', 'href' => $url(), 'path' => ''],
    ['label' => 'About', 'href' => $url('about'), 'path' => 'about'],
    ['label' => 'Modules', 'href' => $url('modules'), 'path' => 'modules'],
    ['label' => 'Discussions', 'href' => $url('discussions'), 'path' => 'discussions'],
    ['label' => 'Resources', 'href' => $url('resources'), 'path' => 'resources'],
];

$moduleLinks = [
    ['label' => 'COMP1841: Systems Architecture', 'href' => $url('modules/comp1841')],
    ['label' => 'COMP1551: Software Engineering', 'href' => $url('modules/comp1551')],
    ['label' => 'COMP1786: Web Development', 'href' => $url('modules/comp1786')],
    ['label' => 'DESN2200: Digital Media Design', 'href' => $url('modules/desn2200')],
];
?>

<header class="sticky top-0 z-40 border-b border-black bg-white">
    <div class="flex min-h-[78px] items-center justify-between gap-4 px-4 py-3 sm:px-6 lg:px-10 xl:px-16">
        <div class="flex items-center gap-3 sm:gap-4">
            <button type="button"
                    class="flex h-10 w-10 items-center justify-center rounded-full border border-[#747878] text-[#333] transition hover:border-black hover:text-black lg:hidden"
                    data-mobile-menu-open
                    aria-label="Open navigation"
                    aria-controls="mobile-navigation"
                    aria-expanded="false">
                <svg viewBox="0 0 33 25" class="h-6 w-8" fill="none" aria-hidden="true">
                    <path d="M4.5 5.5h24M4.5 12.5h24M4.5 19.5h24"
                          stroke="currentColor"
                          stroke-width="3"
                          stroke-linecap="square"/>
                </svg>
            </button>

            <div class="relative hidden lg:block">
                <button type="button"
                        class="flex items-center gap-3 text-[#444748] transition hover:text-black"
                        data-mega-menu-toggle
                        aria-label="Open navigation menu"
                        aria-controls="desktop-mega-menu"
                        aria-expanded="false">
                    <span class="flex h-10 w-10 items-center justify-center rounded-full border border-[#747878]">
                        <svg viewBox="0 0 33 25" class="h-6 w-8" fill="none" aria-hidden="true">
                            <path d="M4.5 5.5h24M4.5 12.5h24M4.5 19.5h24"
                                  stroke="currentColor"
                                  stroke-width="3"
                                  stroke-linecap="square"/>
                        </svg>
                    </span>
                    <span class="text-[12px] font-medium uppercase leading-4 tracking-[1.2px] [font-family:'JetBrains_Mono',ui-monospace,monospace]">Menu</span>
                </button>
            </div>

            <a href="<?= $url() ?>" class="block w-[156px] shrink-0 sm:w-[190px]" aria-label="University of Greenwich home">
                <img
                    src="<?= BASE_URL ?>/assets/images/shared/greenwich-logo.png"
                    alt="University of Greenwich"
                    class="h-full object-contain"
                >
            </a>
        </div>

        <form action="<?= $url('search') ?>"
              method="get"
              class="relative hidden h-[52px] min-w-0 max-w-[620px] flex-1 items-center rounded-full border border-[#d8dbe2] bg-[#f5f6fa] px-5 md:flex"
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

        <div class="flex items-center justify-end gap-2 sm:gap-4">
            <button type="button"
                    class="relative flex h-11 w-11 items-center justify-center rounded-full text-[#3f3f3f] transition hover:bg-[#f5f6fa] hover:text-black"
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

            <div class="group relative">
                <button type="button"
                        class="flex size-12 items-center justify-center overflow-hidden rounded-full border border-[#c4c7c7] p-px transition hover:border-black"
                        aria-label="Open account menu">
                    <img
                        src="<?= BASE_URL ?>/assets/images/header/user-avatar.jpg"
                        alt="User avatar"
                        class="size-full rounded-full object-cover"
                    >
                </button>

                <div class="invisible absolute right-0 top-[calc(100%+12px)] w-48 translate-y-1 rounded-[8px] border border-[#e4e7ec] bg-white p-2 opacity-0 shadow-[0_18px_45px_rgba(15,23,42,0.12)] transition group-focus-within:visible group-focus-within:translate-y-0 group-focus-within:opacity-100 group-hover:visible group-hover:translate-y-0 group-hover:opacity-100">
                    <a href="<?= $url('profile') ?>" class="block rounded-[6px] px-3 py-2 text-sm font-medium text-[#444748] hover:bg-[#f5f6fa] hover:text-black">Profile</a>
                    <a href="<?= $url('settings') ?>" class="block rounded-[6px] px-3 py-2 text-sm font-medium text-[#444748] hover:bg-[#f5f6fa] hover:text-black">Settings</a>
                    <a href="<?= $url('logout') ?>" class="block rounded-[6px] px-3 py-2 text-sm font-medium text-[#444748] hover:bg-[#f5f6fa] hover:text-black">Sign out</a>
                </div>
            </div>
        </div>
    </div>

    <div id="desktop-mega-menu"
         class="fixed inset-0 z-50 hidden overflow-y-auto bg-white"
         data-mega-menu
         aria-hidden="true">
        <div class="flex min-h-screen flex-col">
            <div class="flex min-h-[78px] items-center justify-between px-16 py-5">
                <button type="button"
                        class="flex items-center gap-3 text-black"
                        data-mega-menu-close
                        aria-label="Close navigation">
                    <span class="flex size-10 items-center justify-center rounded-full border border-[#747878]">
                        <svg viewBox="0 0 20 20" class="size-4" fill="none" aria-hidden="true">
                            <path d="m5 5 10 10M15 5 5 15" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                        </svg>
                    </span>
                    <span class="text-[12px] font-medium uppercase leading-4 tracking-[1.2px] [font-family:'JetBrains_Mono',ui-monospace,monospace]">Close</span>
                </button>

                <a href="<?= $url() ?>" class="block w-[190px]" aria-label="University of Greenwich home">
                    <img src="<?= BASE_URL ?>/assets/images/shared/greenwich-logo.png"
                         alt="University of Greenwich"
                         class="h-full object-contain">
                </a>
            </div>

            <div class="grid flex-1 grid-cols-12 gap-6 px-16">
                <nav class="col-span-6 flex flex-col justify-center" aria-label="Primary navigation">
                    <?php foreach ($primaryLinks as $link): ?>
                        <?php $isActive = $currentPath === $link['path']; ?>
                        <a href="<?= $link['href'] ?>"
                           class="<?= $isActive ? 'text-black' : 'text-[#444748]' ?> w-fit border-black text-[clamp(3.25rem,5.8vw,5.25rem)] font-normal leading-[1.12] transition hover:text-black <?= $link['label'] === 'Home' ? 'border-b-2' : '' ?> [font-family:'Hanken_Grotesk',Inter,ui-sans-serif,sans-serif]">
                            <?= htmlspecialchars($link['label'], ENT_QUOTES, 'UTF-8') ?>
                        </a>
                    <?php endforeach; ?>
                </nav>

                <aside class="relative col-span-6 flex flex-col justify-center overflow-hidden border-l border-[rgba(196,199,199,0.3)] py-12 pl-16">
                    <div class="pointer-events-none absolute right-[-96px] top-1/2 size-[360px] -translate-y-1/2 rounded-full border-[32px] border-[#444748]/5"></div>

                    <div class="relative max-w-[430px] pb-9">
                        <a href="<?= $url('modules') ?>" class="group flex items-center gap-3 text-black">
                            <span class="text-[32px] font-semibold leading-10 tracking-[0px] [font-family:'Hanken_Grotesk',Inter,ui-sans-serif,sans-serif]">Modules</span>
                            <svg viewBox="0 0 20 20" class="size-5 transition group-hover:translate-x-1" fill="none" aria-hidden="true">
                                <path d="M4 10h11M11 5l5 5-5 5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </a>
                        <p class="mt-3 text-[18px] leading-7 text-[#444748]">
                            Browse questions, peer-reviewed notes, and academic discussions filtered by your current module.
                        </p>
                    </div>

                    <div class="relative max-w-[520px]">
                        <?php foreach ($moduleLinks as $module): ?>
                            <a href="<?= $module['href'] ?>"
                               class="flex items-center justify-between border-b border-[rgba(196,199,199,0.22)] py-4 text-[12px] font-medium leading-4 tracking-[0.6px] text-black transition hover:border-black hover:pl-2 [font-family:'Hanken_Grotesk',Inter,ui-sans-serif,sans-serif]">
                                <?= htmlspecialchars($module['label'], ENT_QUOTES, 'UTF-8') ?>
                                <svg viewBox="0 0 20 20" class="size-4" fill="none" aria-hidden="true">
                                    <path d="M4 10h11M11 5l5 5-5 5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </a>
                        <?php endforeach; ?>
                    </div>

                    <a href="<?= $url('modules') ?>"
                       class="relative mt-10 flex items-center gap-2 text-[12px] font-medium uppercase leading-4 tracking-[2.4px] text-black transition hover:gap-4 [font-family:'Hanken_Grotesk',Inter,ui-sans-serif,sans-serif]">
                        View all modules
                        <svg viewBox="0 0 20 20" class="h-[11px] w-[15px]" fill="none" aria-hidden="true">
                            <path d="M2 10h14M11 5l5 5-5 5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </a>
                </aside>
            </div>

            <div class="flex items-center justify-between border-t border-[rgba(196,199,199,0.22)] px-16 py-8">
                <nav class="flex items-center gap-12" aria-label="Utility navigation">
                    <a href="<?= $url('guidelines') ?>" class="text-[12px] font-medium uppercase leading-4 tracking-[1.2px] text-[#444748] hover:text-black [font-family:'JetBrains_Mono',ui-monospace,monospace]">Guidelines</a>
                    <a href="<?= $url('support') ?>" class="text-[12px] font-medium uppercase leading-4 tracking-[1.2px] text-[#444748] hover:text-black [font-family:'JetBrains_Mono',ui-monospace,monospace]">Support</a>
                    <a href="<?= $url('contact') ?>" class="text-[12px] font-medium uppercase leading-4 tracking-[1.2px] text-[#444748] hover:text-black [font-family:'JetBrains_Mono',ui-monospace,monospace]">Contact admin</a>
                </nav>

                <div class="flex items-center gap-4 text-[12px] font-medium leading-4 tracking-[0.6px] text-[#444748]/50 [font-family:'JetBrains_Mono',ui-monospace,monospace]">
                    <span>&copy; University of Greenwich</span>
                    <span class="size-1.5 rounded-full bg-[#444748]/20"></span>
                    <span>Current Session: 2023/24</span>
                </div>
            </div>
        </div>
    </div>
</header>

<div id="mobile-navigation"
     class="fixed inset-0 z-50 hidden overflow-y-auto bg-white"
     data-mobile-menu
     aria-hidden="true">
    <div class="flex min-h-screen flex-col">
        <div class="flex min-h-[78px] items-center justify-between px-4 py-5 sm:px-8">
            <button type="button"
                    class="flex items-center gap-3 text-black"
                    data-mobile-menu-close
                    aria-label="Close navigation">
                <span class="flex size-10 items-center justify-center rounded-full border border-[#747878]">
                    <svg viewBox="0 0 20 20" class="size-4" fill="none" aria-hidden="true">
                        <path d="m5 5 10 10M15 5 5 15" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                    </svg>
                </span>
                <span class="text-[12px] font-medium uppercase leading-4 tracking-[1.2px] [font-family:'JetBrains_Mono',ui-monospace,monospace]">Close</span>
            </button>

            <a href="<?= $url() ?>" class="block w-[156px] sm:w-[190px]" aria-label="University of Greenwich home">
                <img src="<?= BASE_URL ?>/assets/images/shared/greenwich-logo.png"
                     alt="University of Greenwich"
                     class="h-full object-contain">
            </a>
        </div>

        <div class="grid flex-1 gap-8 px-4 py-8 sm:px-8 md:grid-cols-2 md:px-12">
            <nav class="flex flex-col justify-center" aria-label="Mobile primary navigation">
                <?php foreach ($primaryLinks as $link): ?>
                    <?php $isActive = $currentPath === $link['path']; ?>
                    <a href="<?= $link['href'] ?>"
                       class="<?= $isActive ? 'text-black' : 'text-[#444748]' ?> w-fit border-black text-[clamp(3rem,14vw,5.25rem)] font-normal leading-[1.14] transition hover:text-black <?= $link['label'] === 'Home' ? 'border-b-2' : '' ?> [font-family:'Hanken_Grotesk',Inter,ui-sans-serif,sans-serif]">
                        <?= htmlspecialchars($link['label'], ENT_QUOTES, 'UTF-8') ?>
                    </a>
                <?php endforeach; ?>
            </nav>

            <aside class="border-t border-[rgba(196,199,199,0.3)] pt-8 md:border-l md:border-t-0 md:pl-10">
                <a href="<?= $url('modules') ?>" class="flex items-center gap-3 text-black">
                    <span class="text-[28px] font-semibold leading-9 [font-family:'Hanken_Grotesk',Inter,ui-sans-serif,sans-serif]">Modules</span>
                    <svg viewBox="0 0 20 20" class="size-5" fill="none" aria-hidden="true">
                        <path d="M4 10h11M11 5l5 5-5 5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </a>
                <p class="mt-3 max-w-[384px] text-[17px] leading-7 text-[#444748]">
                    Browse questions, peer-reviewed notes, and academic discussions filtered by your current module.
                </p>

                <div class="mt-7">
                    <?php foreach ($moduleLinks as $module): ?>
                        <a href="<?= $module['href'] ?>"
                           class="flex items-center justify-between border-b border-[rgba(196,199,199,0.22)] py-4 text-[12px] font-medium leading-4 tracking-[0.6px] text-black [font-family:'Hanken_Grotesk',Inter,ui-sans-serif,sans-serif]">
                            <?= htmlspecialchars($module['label'], ENT_QUOTES, 'UTF-8') ?>
                            <svg viewBox="0 0 20 20" class="size-4" fill="none" aria-hidden="true">
                                <path d="M4 10h11M11 5l5 5-5 5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </a>
                    <?php endforeach; ?>
                </div>
            </aside>
        </div>

        <div class="border-t border-[rgba(196,199,199,0.22)] px-4 py-8 sm:px-8 md:px-12">
            <nav class="flex flex-wrap gap-x-8 gap-y-4" aria-label="Mobile utility navigation">
                <a href="<?= $url('guidelines') ?>" class="text-[12px] font-medium uppercase leading-4 tracking-[1.2px] text-[#444748] [font-family:'JetBrains_Mono',ui-monospace,monospace]">Guidelines</a>
                <a href="<?= $url('support') ?>" class="text-[12px] font-medium uppercase leading-4 tracking-[1.2px] text-[#444748] [font-family:'JetBrains_Mono',ui-monospace,monospace]">Support</a>
                <a href="<?= $url('contact') ?>" class="text-[12px] font-medium uppercase leading-4 tracking-[1.2px] text-[#444748] [font-family:'JetBrains_Mono',ui-monospace,monospace]">Contact admin</a>
            </nav>
        </div>
    </div>
</div>

<script>
    (() => {
        const megaToggle = document.querySelector('[data-mega-menu-toggle]');
        const megaMenu = document.querySelector('[data-mega-menu]');
        const megaClose = document.querySelector('[data-mega-menu-close]');
        const mobileMenu = document.querySelector('[data-mobile-menu]');
        const mobileOpen = document.querySelector('[data-mobile-menu-open]');
        const mobileClose = document.querySelector('[data-mobile-menu-close]');

        const setMegaOpen = (isOpen) => {
            if (!megaToggle || !megaMenu) {
                return;
            }

            megaMenu.classList.toggle('hidden', !isOpen);
            megaMenu.setAttribute('aria-hidden', String(!isOpen));
            megaToggle.setAttribute('aria-expanded', String(isOpen));
            document.body.classList.toggle('overflow-hidden', isOpen);
        };

        const setMobileOpen = (isOpen) => {
            if (!mobileMenu || !mobileOpen) {
                return;
            }

            mobileMenu.classList.toggle('hidden', !isOpen);
            mobileMenu.setAttribute('aria-hidden', String(!isOpen));
            mobileOpen.setAttribute('aria-expanded', String(isOpen));
            document.body.classList.toggle('overflow-hidden', isOpen);
        };

        megaToggle?.addEventListener('click', () => {
            setMegaOpen(megaMenu.classList.contains('hidden'));
        });

        megaClose?.addEventListener('click', () => setMegaOpen(false));
        mobileOpen?.addEventListener('click', () => setMobileOpen(true));
        mobileClose?.addEventListener('click', () => setMobileOpen(false));

        document.addEventListener('click', (event) => {
            if (!megaMenu || !megaToggle || megaMenu.classList.contains('hidden')) {
                return;
            }

            if (!megaMenu.contains(event.target) && !megaToggle.contains(event.target)) {
                setMegaOpen(false);
            }
        });

        document.addEventListener('keydown', (event) => {
            if (event.key !== 'Escape') {
                return;
            }

            setMegaOpen(false);
            setMobileOpen(false);
        });
    })();
</script>
