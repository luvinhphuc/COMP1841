<div id="mega-menu"
     class="pointer-events-none invisible fixed inset-0 z-[70] overflow-y-auto bg-white text-[#171717] opacity-0 will-change-transform data-[open=true]:pointer-events-auto data-[open=true]:visible data-[open=true]:opacity-100"
     data-menu-overlay data-open="false" aria-hidden="true" aria-modal="true" aria-label="Main navigation" role="dialog"
     inert>
    <div class="relative flex min-h-full flex-col pt-20">
        <div
                class="grid flex-1 gap-10 px-5 py-10 sm:px-8 lg:grid-cols-[minmax(360px,520px)_minmax(0,1fr)] lg:gap-12 lg:px-12 lg:py-14 xl:gap-16 xl:px-20">
            <nav class="flex flex-col justify-start ml-3 lg:ml-13" aria-label="Primary navigation">
                <a href="<?= BASE_URL ?: '/' ?>"
                   class="group w-fit py-2 text-left font-serif text-[clamp(2.7rem,8vw,5.7rem)] leading-[1.04] text-black/60 transition duration-300 hover:text-black focus-visible:text-black data-[active=true]:text-black lg:py-1"
                   data-menu-primary-item data-menu-key="home" data-active="true" aria-current="page">
                    <span class="relative inline-block pb-1">
                        Home
                        <span
                                class="absolute inset-x-0 bottom-0 h-px origin-left scale-x-0 bg-black transition-transform duration-300 group-hover:scale-x-100 group-focus-visible:scale-x-100 group-data-[active=true]:scale-x-100"
                                aria-hidden="true"></span>
                    </span>
                </a>

                <button type="button"
                        class="group w-fit py-2 text-left font-serif text-[clamp(2.7rem,8vw,5.7rem)] leading-[1.04] text-black/60 transition duration-300 hover:text-black focus-visible:text-black data-[active=true]:text-black lg:py-1"
                        data-menu-primary-item data-menu-key="about" data-active="false"
                        data-menu-trigger="about" aria-controls="menu-panel-about" aria-expanded="false">
                    <span class="relative inline-block pb-1">
                        About
                        <span
                                class="absolute inset-x-0 bottom-0 h-px origin-left scale-x-0 bg-black transition-transform duration-300 group-hover:scale-x-100 group-focus-visible:scale-x-100 group-data-[active=true]:scale-x-100"
                                aria-hidden="true"></span>
                    </span>
                </button>

                <button type="button"
                        class="group w-fit py-2 text-left font-serif text-[clamp(2.7rem,8vw,5.7rem)] leading-[1.04] text-black/60 transition duration-300 hover:text-black focus-visible:text-black data-[active=true]:text-black lg:py-1"
                        data-menu-primary-item data-menu-key="modules" data-active="false"
                        data-menu-trigger="modules" aria-controls="menu-panel-modules" aria-expanded="false">
                    <span class="relative inline-block pb-1">
                        Modules
                        <span
                                class="absolute inset-x-0 bottom-0 h-px origin-left scale-x-0 bg-black transition-transform duration-300 group-hover:scale-x-100 group-focus-visible:scale-x-100 group-data-[active=true]:scale-x-100"
                                aria-hidden="true"></span>
                    </span>
                </button>

                <button type="button"
                        class="group w-fit py-2 text-left font-serif text-[clamp(2.7rem,8vw,5.7rem)] leading-[1.04] text-black/60 transition duration-300 hover:text-black focus-visible:text-black data-[active=true]:text-black lg:py-1"
                        data-menu-primary-item data-menu-key="discussions" data-active="false"
                        data-menu-trigger="discussions" aria-controls="menu-panel-discussions" aria-expanded="false">
                    <span class="relative inline-block pb-1">
                        Discussions
                        <span
                                class="absolute inset-x-0 bottom-0 h-px origin-left scale-x-0 bg-black transition-transform duration-300 group-hover:scale-x-100 group-focus-visible:scale-x-100 group-data-[active=true]:scale-x-100"
                                aria-hidden="true"></span>
                    </span>
                </button>

                <button type="button"
                        class="group w-fit py-2 text-left font-serif text-[clamp(2.7rem,8vw,5.7rem)] leading-[1.04] text-black/60 transition duration-300 hover:text-black focus-visible:text-black data-[active=true]:text-black lg:py-1"
                        data-menu-primary-item data-menu-key="resources" data-active="false"
                        data-menu-trigger="resources" aria-controls="menu-panel-resources" aria-expanded="false">
                    <span class="relative inline-block pb-1">
                        Resources
                        <span
                                class="absolute inset-x-0 bottom-0 h-px origin-left scale-x-0 bg-black transition-transform duration-300 group-hover:scale-x-100 group-focus-visible:scale-x-100 group-data-[active=true]:scale-x-100"
                                aria-hidden="true"></span>
                    </span>
                </button>
            </nav>

            <div class="flex items-start border-t border-black/10 pt-8 data-[empty=true]:invisible lg:border-l lg:border-t-0 lg:pl-12 lg:pt-0 xl:pl-16"
                 data-menu-panel-container data-empty="true">
                <section id="menu-panel-about" class="w-full max-w-2xl data-[active=false]:hidden"
                         data-menu-panel="about" data-active="false" aria-hidden="true">
                    <p class="max-w-lg text-sm font-semibold uppercase tracking-[0.18em] text-black/45">
                        Explore About
                    </p>
                    <h2 class="mt-4 font-serif text-4xl leading-tight sm:text-5xl">
                        About
                    </h2>
                    <p class="mt-4 max-w-xl text-base leading-7 text-black/60 sm:text-lg">
                        Learn how the Greenwich discussion platform helps students share knowledge and support each
                        other.
                    </p>

                    <ul class="mt-8 border-t border-black/15">
                        <li class="border-b border-black/15">
                            <a href="<?= BASE_URL ?>/about"
                               class="group flex items-center justify-between gap-6 py-4 text-lg font-semibold transition hover:pl-2 hover:text-emerald-800 focus-visible:pl-2 focus-visible:text-emerald-800 sm:text-xl">
                                <span>About the platform</span>
                                <svg viewBox="0 0 24 24"
                                     class="size-5 shrink-0 transition-transform group-hover:translate-x-1" fill="none"
                                     aria-hidden="true">
                                    <path d="M5 12h14M14 7l5 5-5 5" stroke="currentColor" stroke-width="1.8"
                                          stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </a>
                        </li>
                        <li class="border-b border-black/15">
                            <a href="<?= BASE_URL ?>/guidelines"
                               class="group flex items-center justify-between gap-6 py-4 text-lg font-semibold transition hover:pl-2 hover:text-emerald-800 focus-visible:pl-2 focus-visible:text-emerald-800 sm:text-xl">
                                <span>Community guidelines</span>
                                <svg viewBox="0 0 24 24"
                                     class="size-5 shrink-0 transition-transform group-hover:translate-x-1" fill="none"
                                     aria-hidden="true">
                                    <path d="M5 12h14M14 7l5 5-5 5" stroke="currentColor" stroke-width="1.8"
                                          stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </a>
                        </li>
                        <li class="border-b border-black/15">
                            <a href="<?= BASE_URL ?>/contact"
                               class="group flex items-center justify-between gap-6 py-4 text-lg font-semibold transition hover:pl-2 hover:text-emerald-800 focus-visible:pl-2 focus-visible:text-emerald-800 sm:text-xl">
                                <span>Contact admin</span>
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

                <section id="menu-panel-modules" class="w-full max-w-2xl data-[active=false]:hidden"
                         data-menu-panel="modules" data-active="false" aria-hidden="true">
                    <p class="max-w-lg text-sm font-semibold uppercase tracking-[0.18em] text-black/45">
                        Explore Modules
                    </p>
                    <h2 class="mt-4 font-serif text-4xl leading-tight sm:text-5xl">
                        Modules
                    </h2>
                    <p class="mt-4 max-w-xl text-base leading-7 text-black/60 sm:text-lg">
                        Browse questions, peer-reviewed notes, and academic discussions filtered by your current module.
                    </p>

                    <ul class="mt-8 border-t border-black/15">
                        <?php foreach ($navbarModuleLinks as $moduleLink): ?>
                            <li class="border-b border-black/15">
                                <a href="<?= htmlspecialchars($moduleLink['href'], ENT_QUOTES, 'UTF-8') ?>"
                                   class="group flex items-center justify-between gap-6 py-4 text-lg font-semibold transition hover:pl-2 hover:text-emerald-800 focus-visible:pl-2 focus-visible:text-emerald-800 sm:text-xl">
                                    <span><?= htmlspecialchars($moduleLink['label'], ENT_QUOTES, 'UTF-8') ?></span>
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
                    <p class="max-w-lg text-sm font-semibold uppercase tracking-[0.18em] text-black/45">
                        Explore Discussions
                    </p>
                    <h2 class="mt-4 font-serif text-4xl leading-tight sm:text-5xl">
                        Discussions
                    </h2>
                    <p class="mt-4 max-w-xl text-base leading-7 text-black/60 sm:text-lg">
                        Ask questions, compare approaches, and follow the academic conversations that matter to your
                        coursework.
                    </p>

                    <ul class="mt-8 border-t border-black/15">
                        <li class="border-b border-black/15">
                            <a href="<?= BASE_URL ?>/discussions"
                               class="group flex items-center justify-between gap-6 py-4 text-lg font-semibold transition hover:pl-2 hover:text-emerald-800 focus-visible:pl-2 focus-visible:text-emerald-800 sm:text-xl">
                                <span>View all discussions</span>
                                <svg viewBox="0 0 24 24"
                                     class="size-5 shrink-0 transition-transform group-hover:translate-x-1" fill="none"
                                     aria-hidden="true">
                                    <path d="M5 12h14M14 7l5 5-5 5" stroke="currentColor" stroke-width="1.8"
                                          stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </a>
                        </li>
                        <li class="border-b border-black/15">
                            <a href="<?= BASE_URL ?>/discussions/unsolved"
                               class="group flex items-center justify-between gap-6 py-4 text-lg font-semibold transition hover:pl-2 hover:text-emerald-800 focus-visible:pl-2 focus-visible:text-emerald-800 sm:text-xl">
                                <span>Unsolved questions</span>
                                <svg viewBox="0 0 24 24"
                                     class="size-5 shrink-0 transition-transform group-hover:translate-x-1" fill="none"
                                     aria-hidden="true">
                                    <path d="M5 12h14M14 7l5 5-5 5" stroke="currentColor" stroke-width="1.8"
                                          stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </a>
                        </li>
                        <li class="border-b border-black/15">
                            <a href="<?= BASE_URL ?>/discussions/create"
                               class="group flex items-center justify-between gap-6 py-4 text-lg font-semibold transition hover:pl-2 hover:text-emerald-800 focus-visible:pl-2 focus-visible:text-emerald-800 sm:text-xl">
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
                    <p class="max-w-lg text-sm font-semibold uppercase tracking-[0.18em] text-black/45">
                        Explore Resources
                    </p>
                    <h2 class="mt-4 font-serif text-4xl leading-tight sm:text-5xl">
                        Resources
                    </h2>
                    <p class="mt-4 max-w-xl text-base leading-7 text-black/60 sm:text-lg">
                        Find study materials, university tools, and useful links for your Greenwich learning workflow.
                    </p>

                    <ul class="mt-8 border-t border-black/15">
                        <li class="border-b border-black/15">
                            <a href="https://moodlecurrent.gre.ac.uk/" target="_blank"
                               class="group flex items-center justify-between gap-6 py-4 text-lg font-semibold transition hover:pl-2 hover:text-emerald-800 focus-visible:pl-2 focus-visible:text-emerald-800 sm:text-xl">
                                <span>Moodle</span>
                                <svg viewBox="0 0 24 24"
                                     class="size-5 shrink-0 transition-transform group-hover:translate-x-1" fill="none"
                                     aria-hidden="true">
                                    <path d="M5 12h14M14 7l5 5-5 5" stroke="currentColor" stroke-width="1.8"
                                          stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </a>
                        </li>
                        <li class="border-b border-black/15">
                            <a href="https://portal.gre.ac.uk/" target="_blank"
                               class="group flex items-center justify-between gap-6 py-4 text-lg font-semibold transition hover:pl-2 hover:text-emerald-800 focus-visible:pl-2 focus-visible:text-emerald-800 sm:text-xl">
                                <span>Student Portal</span>
                                <svg viewBox="0 0 24 24"
                                     class="size-5 shrink-0 transition-transform group-hover:translate-x-1" fill="none"
                                     aria-hidden="true">
                                    <path d="M5 12h14M14 7l5 5-5 5" stroke="currentColor" stroke-width="1.8"
                                          stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </a>
                        </li>
                        <a href="https://bannermenu.gre.ac.uk/" target="_blank"
                           class="group flex items-center justify-between gap-6 py-4 text-lg font-semibold transition hover:pl-2 hover:text-emerald-800 focus-visible:pl-2 focus-visible:text-emerald-800 sm:text-xl">
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

        <div
                class="border-t border-black/10 px-5 py-5 text-xs uppercase tracking-[0.16em] text-black/45 sm:px-8 lg:px-12 xl:px-20">
        </div>
    </div>
</div>
