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

<header class="sticky top-0 z-40 border-b border-black/10 bg-white">
    <div class="flex min-h-20 items-center justify-between px-5 sm:px-8 lg:px-12">
        <a href="<?= $url() ?>" class="block w-[168px] sm:w-[205px]" aria-label="University of Greenwich home">
            <img
                src="<?= BASE_URL ?>/assets/images/shared/greenwich-logo.png"
                alt="University of Greenwich"
                class="h-auto w-full object-contain"
            >
        </a>

        <button
            type="button"
            class="group flex min-h-12 items-center gap-3 px-1 text-sm font-semibold uppercase tracking-[0.14em] text-[#171717] focus-visible:outline-none"
            data-menu-open
            aria-controls="site-menu"
            aria-expanded="false"
        >
            <span>Menu</span>
            <span class="flex size-11 items-center justify-center rounded-full border border-black/25 transition group-hover:border-black group-focus-visible:border-black">
                <svg viewBox="0 0 24 24" class="size-5" fill="none" aria-hidden="true">
                    <path d="M3 7h18M3 12h18M3 17h18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                </svg>
            </span>
        </button>
    </div>
</header>

<div
    id="site-menu"
    class="pointer-events-none invisible fixed inset-0 z-50 overflow-y-auto bg-[#0b0b0b] text-white opacity-0 will-change-transform data-[open=true]:pointer-events-auto"
    data-menu-overlay
    data-open="false"
    aria-hidden="true"
    aria-modal="true"
    aria-label="Main navigation"
    role="dialog"
    inert
>
    <div class="pointer-events-none absolute inset-0 overflow-hidden" aria-hidden="true">
        <div class="absolute -right-40 top-24 size-[36rem] rounded-full bg-emerald-950/35 blur-[120px]"></div>
        <div class="absolute bottom-0 left-1/3 size-96 rounded-full bg-slate-800/30 blur-[110px]"></div>
    </div>

    <div class="relative flex min-h-full flex-col">
        <div class="flex min-h-20 items-center justify-between border-b border-white/10 px-5 sm:px-8 lg:px-12">
            <a href="<?= $url() ?>" class="block w-[168px] sm:w-[205px]" aria-label="University of Greenwich home">
                <img
                    src="<?= BASE_URL ?>/assets/images/shared/greenwich-logo.png"
                    alt="University of Greenwich"
                    class="h-auto w-full brightness-0 invert"
                >
            </a>

            <button
                type="button"
                class="group flex min-h-12 items-center gap-3 px-1 text-sm font-semibold uppercase tracking-[0.14em] focus-visible:outline-none"
                data-menu-close
                aria-label="Close navigation menu"
            >
                <span>Close</span>
                <span class="flex size-11 items-center justify-center rounded-full border border-white/35 transition group-hover:border-white group-focus-visible:border-white">
                    <svg viewBox="0 0 24 24" class="size-5" fill="none" aria-hidden="true">
                        <path d="M5 5l14 14M19 5 5 19" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                    </svg>
                </span>
            </button>
        </div>

        <div class="grid flex-1 gap-10 px-5 py-10 sm:px-8 lg:grid-cols-[minmax(340px,0.9fr)_minmax(360px,1.1fr)] lg:gap-20 lg:px-12 lg:py-14 xl:gap-28 xl:px-20">
            <nav class="flex flex-col justify-center" aria-label="Primary navigation">
                <a
                    href="<?= $url() ?>"
                    class="group w-fit py-2 text-left font-serif text-[clamp(2.7rem,8vw,5.7rem)] leading-[1.04] text-white/45 transition duration-300 hover:text-white focus-visible:text-white data-[active=true]:text-white lg:py-1"
                    data-menu-primary-item
                    data-menu-key="home"
                    data-active="<?= $isHomeActive ? 'true' : 'false' ?>"
                    aria-current="<?= $isHomeActive ? 'page' : 'false' ?>"
                >
                    <span class="relative inline-block pb-1">
                        Home
                        <span class="absolute inset-x-0 bottom-0 h-px origin-left scale-x-0 bg-white transition-transform duration-300 group-hover:scale-x-100 group-focus-visible:scale-x-100 group-data-[active=true]:scale-x-100" aria-hidden="true"></span>
                    </span>
                </a>

                <?php foreach ($navigation as $key => $menu): ?>
                    <?php $isActive = $key === $activeMenuKey; ?>
                    <button
                        type="button"
                        class="group w-fit py-2 text-left font-serif text-[clamp(2.7rem,8vw,5.7rem)] leading-[1.04] text-white/45 transition duration-300 hover:text-white focus-visible:text-white data-[active=true]:text-white lg:py-1"
                        data-menu-primary-item
                        data-menu-key="<?= htmlspecialchars($key, ENT_QUOTES, 'UTF-8') ?>"
                        data-menu-trigger="<?= htmlspecialchars($key, ENT_QUOTES, 'UTF-8') ?>"
                        data-active="<?= $isActive ? 'true' : 'false' ?>"
                        aria-controls="menu-panel-<?= htmlspecialchars($key, ENT_QUOTES, 'UTF-8') ?>"
                        aria-expanded="false"
                    >
                        <span class="relative inline-block pb-1">
                            <?= htmlspecialchars($menu['label'], ENT_QUOTES, 'UTF-8') ?>
                            <span class="absolute inset-x-0 bottom-0 h-px origin-left scale-x-0 bg-white transition-transform duration-300 group-hover:scale-x-100 group-focus-visible:scale-x-100 group-data-[active=true]:scale-x-100" aria-hidden="true"></span>
                        </span>
                    </button>
                <?php endforeach; ?>
            </nav>

            <div
                class="flex items-center border-t border-white/15 pt-8 data-[empty=true]:invisible lg:border-l lg:border-t-0 lg:pl-16 lg:pt-0 xl:pl-24"
                data-menu-panel-container
                data-empty="true"
            >
                <?php foreach ($navigation as $key => $menu): ?>
                    <section
                        id="menu-panel-<?= htmlspecialchars($key, ENT_QUOTES, 'UTF-8') ?>"
                        class="w-full max-w-2xl data-[active=false]:hidden"
                        data-menu-panel="<?= htmlspecialchars($key, ENT_QUOTES, 'UTF-8') ?>"
                        data-active="false"
                        aria-hidden="true"
                    >
                        <p class="max-w-lg text-sm font-semibold uppercase tracking-[0.18em] text-white/50">
                            Explore <?= htmlspecialchars($menu['label'], ENT_QUOTES, 'UTF-8') ?>
                        </p>
                        <h2 class="mt-4 font-serif text-4xl leading-tight sm:text-5xl">
                            <?= htmlspecialchars($menu['label'], ENT_QUOTES, 'UTF-8') ?>
                        </h2>
                        <p class="mt-4 max-w-xl text-base leading-7 text-white/65 sm:text-lg">
                            <?= htmlspecialchars($menu['description'], ENT_QUOTES, 'UTF-8') ?>
                        </p>

                        <ul class="mt-8 border-t border-white/20">
                            <?php foreach ($menu['children'] as $child): ?>
                                <li class="border-b border-white/20">
                                    <a
                                        href="<?= htmlspecialchars($child['href'], ENT_QUOTES, 'UTF-8') ?>"
                                        class="group flex items-center justify-between gap-6 py-4 text-lg font-semibold transition hover:pl-2 hover:text-emerald-300 focus-visible:pl-2 focus-visible:text-emerald-300 sm:text-xl"
                                    >
                                        <span><?= htmlspecialchars($child['label'], ENT_QUOTES, 'UTF-8') ?></span>
                                        <svg viewBox="0 0 24 24" class="size-5 shrink-0 transition-transform group-hover:translate-x-1" fill="none" aria-hidden="true">
                                            <path d="M5 12h14M14 7l5 5-5 5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </section>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="border-t border-white/10 px-5 py-5 text-xs uppercase tracking-[0.16em] text-white/45 sm:px-8 lg:px-12 xl:px-20">
            University of Greenwich discussion platform
        </div>
    </div>
</div>
