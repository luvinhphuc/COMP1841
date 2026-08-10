<?php
/**
 * Variables passed from App\Core\Controller::view()
 *
 * @var array<int, array{label: string, href: string}> $navbarModuleLinks
 * @var string $navbarActiveMenuKey
 * @var bool $isLoggedIn
 * @var bool $isAdmin
 * @var string $csrfToken
 */
?>
<div id="mega-menu"
     class="pointer-events-none invisible fixed inset-0 z-[70] overflow-y-auto bg-white text-ui-ink opacity-0 will-change-transform data-[open=true]:pointer-events-auto data-[open=true]:visible data-[open=true]:opacity-100"
     data-menu-overlay data-open="false" aria-hidden="true" aria-modal="true" aria-label="Main navigation" role="dialog"
     inert>
    <div class="relative flex min-h-full flex-col pt-20">
        <div
                class="grid flex-1 gap-10 px-5 py-10 sm:px-8 lg:grid-cols-[minmax(360px,520px)_minmax(0,1fr)] lg:gap-12 lg:px-12 lg:py-14 xl:gap-16 xl:px-20">
            <nav class="ml-0 flex flex-col justify-start gap-8 lg:ml-12" aria-label="Primary navigation">
                <button type="button"
                        class="group w-fit py-2 text-left font-serif text-[clamp(3.7rem,3.5vw,5.7rem)] leading-[1.04] text-ui-ink/45 transition duration-300 hover:text-ui-ink focus-visible:text-ui-ink data-[active=true]:text-ui-ink lg:py-1"
                        data-menu-primary-item data-menu-key="discussions"
                        data-active="<?= $navbarActiveMenuKey === 'discussions' ? 'true' : 'false' ?>"
                        data-menu-trigger="discussions" aria-controls="menu-panel-discussions" aria-expanded="false">
                    <span class="relative inline-block pb-1">
                        Discussions
                        <span
                                class="absolute inset-x-0 bottom-0 h-px origin-left scale-x-0 bg-brand-royal transition-transform duration-300 group-hover:scale-x-100 group-focus-visible:scale-x-100 group-data-[active=true]:scale-x-100"
                                aria-hidden="true"></span>
                    </span>
                </button>

                <button type="button"
                        class="group w-fit py-2 text-left font-serif text-[clamp(3.7rem,3.5vw,5.7rem)] leading-[1.04] text-ui-ink/45 transition duration-300 hover:text-ui-ink focus-visible:text-ui-ink data-[active=true]:text-ui-ink lg:py-1"
                        data-menu-primary-item data-menu-key="modules"
                        data-active="<?= $navbarActiveMenuKey === 'modules' ? 'true' : 'false' ?>"
                        data-menu-trigger="modules" aria-controls="menu-panel-modules" aria-expanded="false">
                    <span class="relative inline-block pb-1">
                        Modules
                        <span
                                class="absolute inset-x-0 bottom-0 h-px origin-left scale-x-0 bg-brand-royal transition-transform duration-300 group-hover:scale-x-100 group-focus-visible:scale-x-100 group-data-[active=true]:scale-x-100"
                                aria-hidden="true"></span>
                    </span>
                </button>

                <button type="button"
                        class="group w-fit py-2 text-left font-serif text-[clamp(3.7rem,3.5vw,5.7rem)] leading-[1.04] text-ui-ink/45 transition duration-300 hover:text-ui-ink focus-visible:text-ui-ink data-[active=true]:text-ui-ink lg:py-1"
                        data-menu-primary-item data-menu-key="resources" data-active="false"
                        data-menu-trigger="resources"
                        aria-controls="menu-panel-resources" aria-expanded="false">
                    <span class="relative inline-block pb-1">
                        Resources
                        <span
                                class="absolute inset-x-0 bottom-0 h-px origin-left scale-x-0 bg-brand-royal transition-transform duration-300 group-hover:scale-x-100 group-focus-visible:scale-x-100 group-data-[active=true]:scale-x-100"
                                aria-hidden="true"></span>
                    </span>
                </button>
                <a href="<?= BASE_URL ?>/contact"
                   class="group w-fit py-2 text-left font-serif text-[clamp(3.7rem,3.5vw,5.7rem)] leading-[1.04] text-ui-ink/45 transition duration-300 hover:text-ui-ink focus-visible:text-ui-ink data-[active=true]:text-ui-ink lg:py-1"
                   data-menu-primary-item data-menu-key="contact"
                   data-active="<?= $navbarActiveMenuKey === 'contact' ? 'true' : 'false' ?>"
                    <?= $navbarActiveMenuKey === 'contact' ? 'aria-current="page"' : '' ?>>
                    <span class="relative inline-block pb-1">
                        Contact
                        <span
                                class="absolute inset-x-0 bottom-0 h-px origin-left scale-x-0 bg-brand-royal transition-transform duration-300 group-hover:scale-x-100 group-focus-visible:scale-x-100 group-data-[active=true]:scale-x-100"
                                aria-hidden="true"></span>
                    </span>
                </a>
            </nav>

            <div class="flex items-start border-t border-ui-border pt-8 data-[empty=true]:invisible lg:border-l lg:border-t-0 lg:pl-12 lg:pt-0 xl:pl-16"
                 data-menu-panel-container data-empty="true">

                <section id="menu-panel-modules" class="w-full max-w-2xl data-[active=false]:hidden"
                         data-menu-panel="modules" data-active="false" aria-hidden="true">
                    <p class="ui-eyebrow">
                        Explore Modules
                    </p>
                    <h2 class="mt-4 font-serif text-4xl leading-tight sm:text-5xl">
                        Modules
                    </h2>
                    <p class="mt-4 max-w-xl text-base leading-7 text-ui-text sm:text-lg">
                        Browse questions, peer-reviewed notes, and academic discussions filtered by your current module.
                    </p>

                    <ul class="mt-8 border-t border-ui-border">
                        <?php foreach ($navbarModuleLinks as $navbarModuleLink): ?>
                            <li class="border-b border-ui-border">
                                <a href="<?= htmlspecialchars($navbarModuleLink['href'], ENT_QUOTES, 'UTF-8') ?>"
                                   class="group flex items-center justify-between gap-6 py-4 text-lg font-semibold transition hover:pl-2 hover:text-brand-royal focus-visible:pl-2 focus-visible:text-brand-royal sm:text-xl">
                                    <span><?= htmlspecialchars($navbarModuleLink['label'], ENT_QUOTES, 'UTF-8') ?></span>
                                    <svg viewBox="0 0 24 24"
                                         class="size-5 shrink-0 transition-transform group-hover:translate-x-1"
                                         fill="none"
                                         aria-hidden="true">
                                        <path d="M5 12h14M14 7l5 5-5 5" stroke="currentColor" stroke-width="1.8"
                                              stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </section>

                <section id="menu-panel-discussions" class="w-full max-w-2xl data-[active=false]:hidden"
                         data-menu-panel="discussions" data-active="false" aria-hidden="true">
                    <p class="ui-eyebrow">
                        Explore Discussions
                    </p>
                    <h2 class="mt-4 font-serif text-4xl leading-tight sm:text-5xl">
                        Discussions
                    </h2>
                    <p class="mt-4 max-w-xl text-base leading-7 text-ui-text sm:text-lg">
                        Ask questions, compare approaches, and follow the academic conversations that matter to your
                        coursework.
                    </p>

                    <ul class="mt-8 border-t border-ui-border">
                        <li class="border-b border-ui-border">
                            <a href="<?= BASE_URL ?>/discussions"
                               class="group flex items-center justify-between gap-6 py-4 text-lg font-semibold transition hover:pl-2 hover:text-brand-royal focus-visible:pl-2 focus-visible:text-brand-royal sm:text-xl">
                                <span>View all discussions</span>
                                <svg viewBox="0 0 24 24"
                                     class="size-5 shrink-0 transition-transform group-hover:translate-x-1" fill="none"
                                     aria-hidden="true">
                                    <path d="M5 12h14M14 7l5 5-5 5" stroke="currentColor" stroke-width="1.8"
                                          stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </a>
                        </li>
                        <li class="border-b border-ui-border">
                            <a href="<?= BASE_URL ?>/discussions?status=open"
                               class="group flex items-center justify-between gap-6 py-4 text-lg font-semibold transition hover:pl-2 hover:text-brand-royal focus-visible:pl-2 focus-visible:text-brand-royal sm:text-xl">
                                <span>Unsolved questions</span>
                                <svg viewBox="0 0 24 24"
                                     class="size-5 shrink-0 transition-transform group-hover:translate-x-1" fill="none"
                                     aria-hidden="true">
                                    <path d="M5 12h14M14 7l5 5-5 5" stroke="currentColor" stroke-width="1.8"
                                          stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </a>
                        </li>
                        <li class="border-b border-ui-border">
                            <a href="<?= BASE_URL ?>/discussions/create"
                               class="group flex items-center justify-between gap-6 py-4 text-lg font-semibold transition hover:pl-2 hover:text-brand-royal focus-visible:pl-2 focus-visible:text-brand-royal sm:text-xl">
                                <span>Create a question</span>
                                <svg viewBox="0 0 24 24"
                                     class="size-5 shrink-0 transition-transform group-hover:translate-x-1" fill="none"
                                     aria-hidden="true">
                                    <path d="M5 12h14M14 7l5 5-5 5" stroke="currentColor" stroke-width="1.8"
                                          stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </a>
                        </li>
                    </ul>
                </section>

                <section id="menu-panel-resources" class="w-full max-w-2xl data-[active=false]:hidden"
                         data-menu-panel="resources" data-active="false" aria-hidden="true">
                    <p class="ui-eyebrow">
                        Explore Resources
                    </p>
                    <h2 class="mt-4 font-serif text-4xl leading-tight sm:text-5xl">
                        Resources
                    </h2>
                    <p class="mt-4 max-w-xl text-base leading-7 text-ui-text sm:text-lg">
                        Find study materials, university tools, and useful links for your Greenwich learning workflow.
                    </p>

                    <ul class="mt-8 border-t border-ui-border">
                        <li class="border-b border-ui-border">
                            <a href="https://moodlecurrent.gre.ac.uk/" target="_blank" rel="noopener noreferrer"
                               class="group flex items-center justify-between gap-6 py-4 text-lg font-semibold transition hover:pl-2 hover:text-brand-royal focus-visible:pl-2 focus-visible:text-brand-royal sm:text-xl">
                                <span>Moodle</span>
                                <svg viewBox="0 0 24 24"
                                     class="size-5 shrink-0 transition-transform group-hover:translate-x-1" fill="none"
                                     aria-hidden="true">
                                    <path d="M5 12h14M14 7l5 5-5 5" stroke="currentColor" stroke-width="1.8"
                                          stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </a>
                        </li>
                        <li class="border-b border-ui-border">
                            <a href="https://portal.gre.ac.uk/" target="_blank" rel="noopener noreferrer"
                               class="group flex items-center justify-between gap-6 py-4 text-lg font-semibold transition hover:pl-2 hover:text-brand-royal focus-visible:pl-2 focus-visible:text-brand-royal sm:text-xl">
                                <span>Student Portal</span>
                                <svg viewBox="0 0 24 24"
                                     class="size-5 shrink-0 transition-transform group-hover:translate-x-1" fill="none"
                                     aria-hidden="true">
                                    <path d="M5 12h14M14 7l5 5-5 5" stroke="currentColor" stroke-width="1.8"
                                          stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </a>
                        </li>
                        <li class="border-b border-ui-border">
                            <a href="https://bannermenu.gre.ac.uk/" target="_blank" rel="noopener noreferrer"
                               class="group flex items-center justify-between gap-6 py-4 text-lg font-semibold transition hover:pl-2 hover:text-brand-royal focus-visible:pl-2 focus-visible:text-brand-royal sm:text-xl">
                                <span>Student Records</span>
                                <svg viewBox="0 0 24 24"
                                     class="size-5 shrink-0 transition-transform group-hover:translate-x-1" fill="none"
                                     aria-hidden="true">
                                    <path d="M5 12h14M14 7l5 5-5 5" stroke="currentColor" stroke-width="1.8"
                                          stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </a>
                        </li>
                    </ul>
                </section>
            </div>
        </div>

        <div class="border-t border-ui-border px-5 py-5 sm:px-8 lg:px-12 xl:px-20">
            <div class="flex flex-wrap items-center gap-3 sm:hidden">
                <?php if ($isLoggedIn): ?>
                    <a href="<?= BASE_URL ?>/profile" class="ui-button ui-button-secondary flex-1">Profile</a>
                    <?php if ($isAdmin): ?>
                        <a href="<?= BASE_URL ?>/admin" class="ui-button ui-button-secondary flex-1">Admin</a>
                    <?php endif; ?>
                    <form action="<?= BASE_URL ?>/logout" method="post" class="flex-1">
                        <input type="hidden" name="_csrf_token"
                               value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                        <button type="submit" class="ui-button ui-button-ghost w-full text-ui-danger">Sign out</button>
                    </form>
                <?php else: ?>
                    <a href="<?= BASE_URL ?>/login" class="ui-button ui-button-secondary flex-1">Sign in</a>
                    <a href="<?= BASE_URL ?>/register" class="ui-button ui-button-primary flex-1">Create account</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>