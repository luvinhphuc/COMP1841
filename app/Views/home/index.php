<?php
/** Public landing page. */
?>
<section class="relative isolate min-h-[calc(100vh-80px)] overflow-hidden bg-brand-navy text-white" data-landing-hero>
    <video autoplay muted loop playsinline preload="metadata" data-landing-video aria-hidden="true"
        class="absolute inset-0 size-full object-cover motion-reduce:hidden">
        <source src="<?= BASE_URL ?>/assets/videos/home.mp4" type="video/mp4">
    </video>

    <div class="absolute inset-0 bg-[linear-gradient(90deg,rgba(0,0,51,0.94)_0%,rgba(0,0,51,0.75)_48%,rgba(0,0,51,0.34)_100%)]"
        data-landing-overlay></div>
    <div class="absolute inset-0 bg-[linear-gradient(0deg,rgba(0,0,20,0.48)_0%,transparent_48%)]" data-landing-overlay>
    </div>

    <div
        class="relative mx-auto flex min-h-[calc(100vh-80px)] max-w-[1440px] items-end px-5 py-12 sm:px-8 sm:py-16 lg:items-center lg:px-16 lg:py-20">
        <div class="max-w-3xl" data-landing-content>
            <p class="text-xs font-bold uppercase tracking-[0.18em] text-white/75">University of Greenwich coursework
                forum</p>
            <h1 class="mt-5 font-serif text-[clamp(3.25rem,8vw,7.5rem)] leading-[0.92] tracking-[-0.035em]">
                Coursework questions, answered together.
            </h1>
            <p class="mt-7 max-w-2xl text-base leading-7 text-white/80 sm:text-xl sm:leading-8">
                Ask clearly, compare approaches, and learn from students across your modules in one focused academic
                space.
            </p>

            <div class="mt-9 flex flex-col gap-3 sm:flex-row">
                <a href="<?= BASE_URL ?>/register"
                    class="ui-button min-h-12 bg-white px-6 text-brand-navy hover:bg-white/90">
                    Create your account
                </a>
                <a href="<?= BASE_URL ?>/login"
                    class="ui-button min-h-12 border border-white/45 bg-white/10 px-6 text-white backdrop-blur hover:border-white hover:bg-white/20">
                    Sign in
                </a>
            </div>

            <div
                class="mt-10 flex flex-wrap gap-x-6 gap-y-3 border-t border-white/20 pt-5 text-sm font-semibold text-white/70">
            </div>
        </div>
    </div>
</section>