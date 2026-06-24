<?php
$url = static function (string $path = ''): string {
    if ($path === '') {
        return BASE_URL !== '' ? BASE_URL : '/';
    }

    return BASE_URL . '/' . ltrim($path, '/');
};

$routePath = trim((string) ($_GET['url'] ?? ''), '/');
$currentPath = $routePath !== ''
    ? $routePath
    : trim(parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH), '/');
$basePath = trim(parse_url(BASE_URL, PHP_URL_PATH) ?? '', '/');

if ($basePath !== '' && strpos($currentPath, $basePath) === 0) {
    $currentPath = trim(substr($currentPath, strlen($basePath)), '/');
}

$currentSection = explode('/', $currentPath)[0] ?? '';
$activeMenuKey = in_array($currentSection, ['discussions', 'modules', 'resources', 'support'], true)
    ? $currentSection
    : 'home';
$isHomeActive = $activeMenuKey === 'home';

$menus = [
    'home' => ['label' => 'Home', 'href' => $url()],
    'discussions' => [
        'label' => 'Discussions',
        'description' => 'Ask questions, compare approaches, and learn with the community.',
        'children' => [
            ['label' => 'View all discussions', 'href' => $url('discussions')],
            ['label' => 'Unsolved questions', 'href' => $url('discussions/unsolved')],
            ['label' => 'Solved questions', 'href' => $url('discussions/solved')],
            ['label' => 'Create a question', 'href' => $url('discussions/create')],
        ],
    ],
    'resources' => [
        'label' => 'Resources',
        'description' => 'Find the university tools and study materials you use most.',
        'children' => [
            ['label' => 'View resources', 'href' => $url('resources')],
            ['label' => 'Moodle', 'href' => $url('resources/moodle')],
            ['label' => 'Student Portal', 'href' => $url('resources/student-portal')],
        ],
    ],
    'support' => [
        'label' => 'Support',
        'description' => 'Get practical help with the platform and common questions.',
        'children' => [
            ['label' => 'Contact administrator', 'href' => $url('support/contact')],
            ['label' => 'FAQ', 'href' => $url('support/faq')],
        ],
    ],
];

$moduleChildren = [
    ['label' => 'View all modules', 'href' => $url('modules')],
];

foreach ($modules ?? [] as $module) {
    $moduleCode = trim((string) ($module['code'] ?? ''));

    if ($moduleCode === '') {
        continue;
    }

    $moduleChildren[] = [
        'label' => $moduleCode,
        'href' => $url('modules/' . rawurlencode(strtolower($moduleCode))),
    ];
}

$navigation = [
    'discussions' => $menus['discussions'],
    'modules' => [
        'label' => 'Modules',
        'description' => 'Browse discussions and learning materials by module.',
        'children' => $moduleChildren,
    ],
    'resources' => $menus['resources'],
    'support' => $menus['support'],
];
?>

<header class="group sticky top-0 z-[80] border-b border-black bg-white data-[menu-open=true]:border-black/10"
    data-menu-open="false">
    <div class="flex min-h-20 items-center gap-12 px-5 sm:px-8 lg:gap-36 lg:px-32 xl:gap-48">
        <div class="flex shrink-0 items-center gap-3 sm:gap-4">
            <button id="menu-btn" type="button"
                class="group flex size-11 items-center justify-center text-[#333] hover:text-black focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-black"
                aria-controls="mega-menu" aria-expanded="false" aria-label="Open navigation menu">
                <svg viewBox="0 0 33 25" class="h-6 w-8" fill="none" aria-hidden="true">
                    <path data-menu-icon-open d="M4.5 5.5h24M4.5 12.5h24M4.5 19.5h24" stroke="currentColor"
                        stroke-width="3" stroke-linecap="square" />
                    <path data-menu-icon-close class="hidden" d="M7 5l19 15M26 5 7 20" stroke="currentColor"
                        stroke-width="2.5" stroke-linecap="square" />
                </svg>
            </button>

            <a id="site-logo" href="<?= $url() ?>" class="block w-[168px] shrink-0 sm:w-[190px]"
                aria-label="University of Greenwich home">
                <img src="<?= BASE_URL ?>/assets/images/shared/greenwich-logo.png" alt="University of Greenwich"
                    class="h-auto w-full object-contain">
            </a>
        </div>

        <form
            class="relative hidden h-[52px] min-w-0 flex-1 items-center rounded-full border border-[#d8dbe2] bg-[#f5f6fa] px-5 transition-opacity duration-200 group-data-[menu-open=true]:pointer-events-none group-data-[menu-open=true]:opacity-0 md:flex"
            role="search" action="<?= $url('discussions') ?>" method="get">
            <label class="sr-only" for="site-search">Search coursework questions</label>
            <svg viewBox="0 0 18 18" class="mr-4 size-5 shrink-0 text-[#6b7280]" fill="none" aria-hidden="true">
                <circle cx="8" cy="8" r="5.75" stroke="currentColor" stroke-width="1.5" />
                <path d="m12.25 12.25 3 3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
            </svg>
            <input id="site-search" name="q" type="search" placeholder="Search coursework questions..."
                class="min-w-0 flex-1 bg-transparent text-base text-[#5f6368] outline-none placeholder:text-[#5f6368]">
        </form>

        <div
            class="ml-auto hidden shrink-0 items-center gap-3 transition-opacity duration-200 group-data-[menu-open=true]:pointer-events-none group-data-[menu-open=true]:opacity-0 sm:flex">
            <button type="button"
                class="relative flex size-11 items-center justify-center rounded-full text-[#3f3f3f] hover:text-black focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-black"
                aria-label="Notifications">
                <svg viewBox="0 0 29 36" class="h-8 w-7" fill="none" aria-hidden="true">
                    <path d="M14.5 32.5a4 4 0 0 0 3.8-2.8h-7.6a4 4 0 0 0 3.8 2.8Z" fill="currentColor" />
                    <path
                        d="M5.5 25.5h18l-2.4-3.4V14a6.6 6.6 0 0 0-5-6.4V5.4a1.6 1.6 0 1 0-3.2 0v2.2A6.6 6.6 0 0 0 7.9 14v8.1l-2.4 3.4Z"
                        stroke="currentColor" stroke-width="2.4" stroke-linejoin="round" />
                </svg>
                <span class="absolute right-2 top-1.5 size-2 rounded-full bg-[#ba1a1a]"></span>
            </button>
            <a href="#"
                class="flex size-12 items-center justify-center overflow-hidden rounded-full border border-[#c4c7c7] p-px"
                aria-label="User profile">
                <img src="<?= BASE_URL ?>/assets/images/header/user-avatar.jpeg" alt="User avatar"
                    class="size-full rounded-full object-cover">
            </a>
        </div>
    </div>
</header>

<div id="mega-menu"
    class="pointer-events-none invisible fixed inset-0 z-[70] overflow-y-auto bg-white text-[#171717] opacity-0 will-change-transform data-[open=true]:pointer-events-auto data-[open=true]:visible data-[open=true]:opacity-100"
    data-menu-overlay data-open="false" aria-hidden="true" aria-modal="true" aria-label="Main navigation" role="dialog"
    inert>
    <div class="relative flex min-h-full flex-col pt-20">
        <div
            class="grid flex-1 gap-10 px-5 py-10 sm:px-8 lg:grid-cols-[minmax(360px,520px)_minmax(0,1fr)] lg:gap-12 lg:px-12 lg:py-14 xl:gap-16 xl:px-20">
            <nav class="flex flex-col justify-start" aria-label="Primary navigation">
                <a href="<?= $url() ?>"
                    class="group w-fit py-2 text-left font-serif text-[clamp(2.7rem,8vw,5.7rem)] leading-[1.04] text-black/60 transition duration-300 hover:text-black focus-visible:text-black data-[active=true]:text-black lg:py-1"
                    data-menu-primary-item data-menu-key="home" data-active="<?= $isHomeActive ? 'true' : 'false' ?>"
                    aria-current="<?= $isHomeActive ? 'page' : 'false' ?>">
                    <span class="relative inline-block pb-1">
                        Home
                        <span
                            class="absolute inset-x-0 bottom-0 h-px origin-left scale-x-0 bg-black transition-transform duration-300 group-hover:scale-x-100 group-focus-visible:scale-x-100 group-data-[active=true]:scale-x-100"
                            aria-hidden="true"></span>
                    </span>
                </a>

                <?php foreach ($navigation as $key => $menu): ?>
                <?php $isActive = $key === $activeMenuKey; ?>
                <button type="button"
                    class="group w-fit py-2 text-left font-serif text-[clamp(2.7rem,8vw,5.7rem)] leading-[1.04] text-black/60 transition duration-300 hover:text-black focus-visible:text-black data-[active=true]:text-black lg:py-1"
                    data-menu-primary-item data-menu-key="<?= htmlspecialchars($key, ENT_QUOTES, 'UTF-8') ?>"
                    data-menu-trigger="<?= htmlspecialchars($key, ENT_QUOTES, 'UTF-8') ?>"
                    data-active="<?= $isActive ? 'true' : 'false' ?>"
                    aria-controls="menu-panel-<?= htmlspecialchars($key, ENT_QUOTES, 'UTF-8') ?>" aria-expanded="false">
                    <span class="relative inline-block pb-1">
                        <?= htmlspecialchars($menu['label'], ENT_QUOTES, 'UTF-8') ?>
                        <span
                            class="absolute inset-x-0 bottom-0 h-px origin-left scale-x-0 bg-black transition-transform duration-300 group-hover:scale-x-100 group-focus-visible:scale-x-100 group-data-[active=true]:scale-x-100"
                            aria-hidden="true"></span>
                    </span>
                </button>
                <?php endforeach; ?>
            </nav>

            <div class="flex items-start border-t border-black/10 pt-8 data-[empty=true]:invisible lg:border-l lg:border-t-0 lg:pl-12 lg:pt-0 xl:pl-16"
                data-menu-panel-container data-empty="true">
                <?php foreach ($navigation as $key => $menu): ?>
                <section id="menu-panel-<?= htmlspecialchars($key, ENT_QUOTES, 'UTF-8') ?>"
                    class="w-full max-w-2xl data-[active=false]:hidden"
                    data-menu-panel="<?= htmlspecialchars($key, ENT_QUOTES, 'UTF-8') ?>" data-active="false"
                    aria-hidden="true">
                    <p class="max-w-lg text-sm font-semibold uppercase tracking-[0.18em] text-black/45">
                        Explore <?= htmlspecialchars($menu['label'], ENT_QUOTES, 'UTF-8') ?>
                    </p>
                    <h2 class="mt-4 font-serif text-4xl leading-tight sm:text-5xl">
                        <?= htmlspecialchars($menu['label'], ENT_QUOTES, 'UTF-8') ?>
                    </h2>
                    <p class="mt-4 max-w-xl text-base leading-7 text-black/60 sm:text-lg">
                        <?= htmlspecialchars($menu['description'], ENT_QUOTES, 'UTF-8') ?>
                    </p>

                    <ul class="mt-8 border-t border-black/15">
                        <?php foreach ($menu['children'] as $child): ?>
                        <li class="border-b border-black/15">
                            <a href="<?= htmlspecialchars($child['href'], ENT_QUOTES, 'UTF-8') ?>"
                                class="group flex items-center justify-between gap-6 py-4 text-lg font-semibold transition hover:pl-2 hover:text-emerald-800 focus-visible:pl-2 focus-visible:text-emerald-800 sm:text-xl">
                                <span><?= htmlspecialchars($child['label'], ENT_QUOTES, 'UTF-8') ?></span>
                                <svg viewBox="0 0 24 24"
                                    class="size-5 shrink-0 transition-transform group-hover:translate-x-1" fill="none"
                                    aria-hidden="true">
                                    <path d="M5 12h14M14 7l5 5-5 5" stroke="currentColor" stroke-width="1.8"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </section>
                <?php endforeach; ?>
            </div>
        </div>

        <div
            class="border-t border-black/10 px-5 py-5 text-xs uppercase tracking-[0.16em] text-black/45 sm:px-8 lg:px-12 xl:px-20">
            University of Greenwich discussion platform
        </div>
    </div>
</div>
