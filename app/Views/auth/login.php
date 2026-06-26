<?php
$errors = $errors ?? [];
$old = $old ?? [];
$success = $success ?? null;
?>

<section class="bg-[#f7f9fd] px-5 py-10 text-[#191c1f] sm:px-8 lg:py-14">
    <div class="mx-auto grid w-full max-w-[1040px] gap-8 lg:grid-cols-[minmax(0,0.9fr)_minmax(380px,0.85fr)] lg:items-start">
        <div class="hidden border-l-4 border-black pl-6 lg:block" data-home-reveal>
            <p class="text-sm font-bold uppercase tracking-[0.16em] text-[#444748]">
                Greenwich Discussions
            </p>
            <h1 class="mt-4 text-4xl font-semibold leading-tight text-black">
                Welcome back
            </h1>
            <p class="mt-4 max-w-md text-base leading-7 text-[#444748]">
                Sign in to follow module discussions, ask coursework questions, and continue learning with your class.
            </p>
        </div>

        <div class="rounded-xl border border-[#c4c7c7] bg-white p-5 shadow-[0_18px_38px_rgba(25,28,31,0.06)] sm:p-8" data-home-reveal>
            <div class="mb-6">
                <p class="text-sm font-bold uppercase tracking-[0.12em] text-[#444748]">
                    Login
                </p>
                <h2 class="mt-2 text-3xl font-semibold leading-10 text-black">
                    Sign in to your account
                </h2>
            </div>

            <?php if (!empty($success)): ?>
                <div class="mb-5 rounded-lg border border-[#137333]/30 bg-[#137333]/5 p-4 text-sm text-[#0b5c26]">
                    <?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($errors['general'])): ?>
                <div class="mb-5 rounded-lg border border-[#ba1a1a]/30 bg-[#ba1a1a]/5 p-4 text-sm text-[#8f1111]">
                    <?= htmlspecialchars($errors['general'], ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php endif; ?>

            <form action="<?= BASE_URL ?>/login/authenticate" method="post" class="grid gap-5" novalidate>
                <div>
                    <label for="email" class="mb-2 block text-sm font-semibold text-[#191c1f]">Email</label>
                    <input id="email"
                           name="email"
                           type="email"
                           value="<?= htmlspecialchars($old['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                           autocomplete="email"
                           required
                           class="h-12 w-full rounded-lg border border-[#c4c7c7] bg-white px-4 text-base outline-none transition focus:border-black focus:ring-2 focus:ring-black/10">
                    <p class="mt-2 text-sm text-[#ba1a1a]">
                        <?= htmlspecialchars($errors['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                    </p>
                </div>

                <div>
                    <label for="password" class="mb-2 block text-sm font-semibold text-[#191c1f]">Password</label>
                    <input id="password"
                           name="password"
                           type="password"
                           autocomplete="current-password"
                           required
                           class="h-12 w-full rounded-lg border border-[#c4c7c7] bg-white px-4 text-base outline-none transition focus:border-black focus:ring-2 focus:ring-black/10">
                    <p class="mt-2 text-sm text-[#ba1a1a]">
                        <?= htmlspecialchars($errors['password'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                    </p>
                </div>

                <button type="submit"
                        class="mt-2 inline-flex h-12 items-center justify-center rounded-lg bg-black px-6 text-sm font-semibold tracking-[0.04em] text-white transition hover:bg-[#2a2d2f] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-black">
                    Sign In
                </button>

                <p class="text-sm leading-6 text-[#444748]">
                    Need an account?
                    <a href="<?= BASE_URL ?>/register" class="font-semibold text-black underline-offset-4 hover:underline">
                        Register with your @gre.ac.uk email.
                    </a>
                </p>
            </form>
        </div>
    </div>
</section>
