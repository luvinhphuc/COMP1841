<?php
/**
 * Variables passed from HomeController::index()
 *
 * No controller variables are required by this view.
 */
?>
<section class="relative w-full h-screen overflow-hidden">

    <!-- Video background full width -->
    <video autoplay muted playsinline
           class="absolute inset-0 w-full h-full object-cover">
        <source src="<?= BASE_URL ?>/assets/videos/home.mp4" type="video/mp4">
    </video>

    <!-- Lớp tối phủ lên video cho chữ dễ đọc -->
    <div class="absolute inset-0 bg-black/40"></div>

    <!-- Main content -->
    <div class="relative z-10 h-[calc(100vh-5rem)] flex items-center justify-center text-center text-white px-6">

        <div class="max-w-3xl">
            <h1 class="text-5xl md:text-7xl font-bold mb-6">
                Welcome
            </h1>

            <p class="text-lg md:text-xl mb-8">
                Ask questions, share knowledge, and connect with your learning community.
            </p>

            <div class="flex justify-center gap-4">
                <a href="<?= BASE_URL ?>/register"
                   class="px-8 py-3 rounded-lg bg-white text-black font-semibold hover:bg-gray-200 transition">
                    Get Started
                </a>

                <a href="<?= BASE_URL ?>/login"
                   class="px-8 py-3 rounded-lg border border-white text-white font-semibold hover:bg-white hover:text-black transition">
                    Login
                </a>
            </div>
        </div>

    </div>

</section>
