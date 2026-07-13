<?php
/**
 * Variables passed from App\Core\Controller::view()
 *
 * @var array|null $flashToast
 * @var string $navbarScriptUrl
 * @var string $showPasswordScriptUrl
 * @var array $pageScriptUrls
 */
?>
</main>

<?php if ($flashToast !== null): ?>
    <?php
        $toastType = trim((string) ($flashToast['type'] ?? 'info'));
        $toastType = $toastType !== '' ? $toastType : 'info';
        $toastTitle = trim((string) ($flashToast['title'] ?? ''));
        $toastMessage = trim((string) ($flashToast['message'] ?? ''));
        $isErrorToast = $toastType === 'error';
    ?>
    <div
        class="fixed right-4 top-24 z-[100] w-[calc(100%-2rem)] max-w-sm rounded-lg border bg-white p-4 text-[#191c1f] shadow-[0_18px_38px_rgba(25,28,31,0.12)] transition duration-200 ease-out motion-reduce:transition-none sm:right-6"
        role="<?= $isErrorToast ? 'alert' : 'status' ?>"
        aria-live="<?= $isErrorToast ? 'assertive' : 'polite' ?>"
        data-flash-toast
    >
        <div class="flex items-start gap-3">
            <span class="mt-0.5 flex size-8 shrink-0 items-center justify-center rounded-lg <?= $isErrorToast ? 'bg-[#ba1a1a]/10 text-[#ba1a1a]' : 'bg-[#d6e3ff] text-[#001b3d]' ?>" aria-hidden="true">
                <?php if ($isErrorToast): ?>
                    <svg viewBox="0 0 20 20" class="size-4" fill="none">
                        <path d="M10 6v4.5M10 14h.01M17 10a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                <?php else: ?>
                    <svg viewBox="0 0 20 20" class="size-4" fill="none">
                        <path d="m5 10.5 3 3 7-7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                <?php endif; ?>
            </span>

            <div class="min-w-0 flex-1">
                <?php if ($toastTitle !== ''): ?>
                    <p class="text-sm font-semibold leading-5">
                        <?= htmlspecialchars($toastTitle, ENT_QUOTES, 'UTF-8') ?>
                    </p>
                <?php endif; ?>
                <p class="<?= $toastTitle !== '' ? 'mt-1 ' : '' ?>text-sm leading-5 text-[#444748]">
                    <?= htmlspecialchars($toastMessage, ENT_QUOTES, 'UTF-8') ?>
                </p>
            </div>

            <button
                type="button"
                class="-mr-1 -mt-1 flex size-8 shrink-0 items-center justify-center rounded-md text-[#444748] transition hover:bg-[#f7f9fd] hover:text-[#191c1f] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-black"
                aria-label="Dismiss message"
                data-flash-toast-dismiss
            >
                <svg viewBox="0 0 20 20" class="size-4" fill="none" aria-hidden="true">
                    <path d="m6 6 8 8M14 6l-8 8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                </svg>
            </button>
        </div>
    </div>
<?php endif; ?>

<footer class="flex w-full flex-col items-center gap-4 border-t border-[#222] pt-[25px] pb-6">
    <div class="flex flex-col items-start gap-1">
        <img
            src="<?= BASE_URL ?>/assets/images/shared/greenwich-logo.png"
            alt="University of Greenwich"
            class="h-[74px] w-[202px] object-contain"
        >
        <p class="w-full text-center text-sm leading-5 text-[#6b6b6b]">
            Built by students, for students.
        </p>
    </div>

    <nav class="pt-4" aria-label="Footer links">
        <ul class="flex items-center gap-6 text-[#6b6b6b]">
            <li>
                <a href="#" class="block size-[16.667px] transition-colors hover:text-[#222]" aria-label="Website">
                    <svg viewBox="0 0 20 20" class="size-full" fill="none" aria-hidden="true">
                        <circle cx="10" cy="10" r="8" stroke="currentColor" stroke-width="1.6"/>
                        <path d="M2.5 10h15M10 2.5c2.2 2.25 3.3 4.75 3.3 7.5s-1.1 5.25-3.3 7.5M10 2.5C7.8 4.75 6.7 7.25 6.7 10s1.1 5.25 3.3 7.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                    </svg>
                </a>
            </li>
            <li>
                <a href="#" class="block h-[13.333px] w-[16.667px] transition-colors hover:text-[#222]" aria-label="Email">
                    <svg viewBox="0 0 20 16" class="size-full" fill="none" aria-hidden="true">
                        <rect x="1.5" y="1.5" width="17" height="13" rx="1.4" stroke="currentColor" stroke-width="1.6"/>
                        <path d="M2.4 3.1 10 8.7l7.6-5.6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </a>
            </li>
            <li>
                <a href="#" class="block h-[16.667px] w-[13.333px] transition-colors hover:text-[#222]" aria-label="Location">
                    <svg viewBox="0 0 16 20" class="size-full" fill="none" aria-hidden="true">
                        <path d="M8 18.1S2.5 12.5 2.5 7.8a5.5 5.5 0 1 1 11 0C13.5 12.5 8 18.1 8 18.1Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>
                        <circle cx="8" cy="7.8" r="1.8" stroke="currentColor" stroke-width="1.6"/>
                    </svg>
                </a>
            </li>
        </ul>
    </nav>
</footer>
<script src="<?= BASE_URL ?>/assets/js/gsap.min.js"></script>
<?php if (!empty($showPasswordScriptUrl)): ?>
    <script src="<?= htmlspecialchars($showPasswordScriptUrl, ENT_QUOTES, 'UTF-8') ?>"></script>
<?php endif; ?>
<?php if (!empty($navbarScriptUrl)): ?>
    <script src="<?= htmlspecialchars($navbarScriptUrl, ENT_QUOTES, 'UTF-8') ?>"></script>
<?php endif; ?>
<?php foreach (($pageScriptUrls ?? []) as $pageScriptUrl): ?>
    <script src="<?= htmlspecialchars($pageScriptUrl, ENT_QUOTES, 'UTF-8') ?>"></script>
<?php endforeach; ?>
<?php if ($flashToast !== null): ?>
    <script>
        (() => {
            const toast = document.querySelector("[data-flash-toast]");
            const dismiss = document.querySelector("[data-flash-toast-dismiss]");

            if (!toast) {
                return;
            }

            const closeToast = () => {
                toast.classList.add("translate-y-2", "opacity-0");
                window.setTimeout(() => toast.remove(), 220);
            };

            dismiss?.addEventListener("click", closeToast);
            window.setTimeout(closeToast, 5200);
        })();
    </script>
<?php endif; ?>
</body>
</html>
