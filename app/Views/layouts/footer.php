<?php
/**
 * Variables passed from App\Core\Controller::view()
 *
 * @var array{type?: string, title?: string, message?: string}|null $flashToast
 * @var string $navbarScriptUrl
 * @var string $showPasswordScriptUrl
 * @var array<int, string> $pageScriptUrls
 * @var bool|null $hasImageViewer
 */
?>
</main>

<?php if (!empty($hasImageViewer)): ?>
<?php require ROOT_PATH . '/app/Views/components/image_viewer.php'; ?>
<?php endif; ?>

<?php if ($flashToast !== null): ?>
<?php
    $layoutFooterToastType = trim((string)($flashToast['type'] ?? 'info'));
    $layoutFooterToastType = $layoutFooterToastType !== '' ? $layoutFooterToastType : 'info';
    $layoutFooterToastTitle = trim((string)($flashToast['title'] ?? ''));
    $layoutFooterToastMessage = trim((string)($flashToast['message'] ?? ''));
    $layoutFooterToastIconClass = match ($layoutFooterToastType) {
        'success' => 'text-ui-success',
        'error' => 'text-ui-danger',
        'warning' => 'text-ui-warning',
        default => 'text-brand-royal',
    };
    ?>
<div class="fixed right-4 top-24 z-[100] w-[calc(100%-2rem)] max-w-xs rounded-xl border border-ui-border bg-white p-4 shadow-ui-overlay transition duration-200 ease-out motion-reduce:transition-none sm:right-6"
    role="alert" tabindex="-1" aria-labelledby="flash-toast-label" data-flash-toast>
    <div class="flex gap-x-3">
        <svg class="mt-0.5 size-4 shrink-0 <?= $layoutFooterToastIconClass ?>" viewBox="0 0 16 16" fill="currentColor"
            aria-hidden="true">
            <?php if ($layoutFooterToastType === 'success'): ?>
            <path
                d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0Zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05Z" />
            <?php elseif ($layoutFooterToastType === 'error'): ?>
            <path
                d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0ZM5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293 5.354 4.646Z" />
            <?php elseif ($layoutFooterToastType === 'warning'): ?>
            <path
                d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0ZM8 4a.905.905 0 0 0-.9.995l.35 3.507a.552.552 0 0 0 1.1 0l.35-3.507A.905.905 0 0 0 8 4Zm.002 6a1 1 0 1 0 0 2 1 1 0 0 0 0-2Z" />
            <?php else: ?>
            <path
                d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16Zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287ZM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2Z" />
            <?php endif; ?>
        </svg>

        <div class="min-w-0 grow">
            <?php if ($layoutFooterToastTitle !== ''): ?>
            <p id="flash-toast-label" class="text-sm font-semibold leading-5 text-ui-ink">
                <?= htmlspecialchars($layoutFooterToastTitle, ENT_QUOTES, 'UTF-8') ?>
            </p>
            <?php endif; ?>
            <p <?= $layoutFooterToastTitle === '' ? 'id="flash-toast-label"' : '' ?>
                class="<?= $layoutFooterToastTitle !== '' ? 'mt-1 ' : '' ?>text-sm leading-5 text-ui-text">
                <?= htmlspecialchars($layoutFooterToastMessage, ENT_QUOTES, 'UTF-8') ?>
            </p>
        </div>
    </div>
</div>
<?php endif; ?>

<footer class="border-t border-ui-border bg-white">
    <div
        class="mx-auto flex max-w-[1280px] flex-col gap-6 px-5 py-8 sm:px-8 lg:flex-row lg:items-center lg:justify-between lg:px-16">
        <div class="flex items-center gap-5">
            <img src="<?= BASE_URL ?>/assets/images/shared/greenwich-logo.png" alt="University of Greenwich"
                class="h-auto w-[168px] object-contain">
            <p class="hidden border-l border-ui-border pl-5 text-sm leading-6 text-ui-muted sm:block">
                Built by students, for students.
            </p>
        </div>

        <nav aria-label="Footer links">
            <ul class="flex flex-wrap items-center gap-x-6 gap-y-3 text-sm font-semibold text-ui-text">
                <li><a href="<?= BASE_URL ?>/privacy-policy"
                        class="transition hover:text-brand-royal">Privacy Policy</a></li>
                <li><a href="<?= BASE_URL ?>/contact" class="transition hover:text-brand-royal">Contact</a></li>
                <li><a href="https://moodlecurrent.gre.ac.uk/" target="_blank" rel="noopener noreferrer"
                        class="transition hover:text-brand-royal">Moodle</a></li>
                <li><a href="https://www.gre.ac.uk/" target="_blank" rel="noopener noreferrer"
                        class="transition hover:text-brand-royal">University website</a></li>
            </ul>
        </nav>
    </div>
</footer>
<script src="<?= BASE_URL ?>/assets/js/gsap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/prismjs@1.30.0/prism.min.js" data-manual></script>
<script src="https://cdn.jsdelivr.net/npm/prismjs@1.30.0/components/prism-markup-templating.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/prismjs@1.30.0/components/prism-php.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/prismjs@1.30.0/components/prism-sql.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/prismjs@1.30.0/components/prism-python.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/prismjs@1.30.0/components/prism-java.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/prismjs@1.30.0/components/prism-json.min.js"></script>
<script>
window.Prism?.highlightAll();
</script>
<?php if (!empty($showPasswordScriptUrl)): ?>
<script src="<?= htmlspecialchars($showPasswordScriptUrl, ENT_QUOTES, 'UTF-8') ?>"></script>
<?php endif; ?>
<?php if (!empty($navbarScriptUrl)): ?>
<script type="module" src="<?= htmlspecialchars($navbarScriptUrl, ENT_QUOTES, 'UTF-8') ?>"></script>
<?php endif; ?>
<?php foreach (($pageScriptUrls ?? []) as $layoutFooterPageScriptUrl): ?>
<script type="module" src="<?= htmlspecialchars($layoutFooterPageScriptUrl, ENT_QUOTES, 'UTF-8') ?>"></script>
<?php endforeach; ?>
<?php if ($flashToast !== null): ?>
<script>
(() => {
    const toast = document.querySelector("[data-flash-toast]");

    if (!toast) {
        return;
    }

    const closeToast = () => {
        toast.classList.add("translate-y-2", "opacity-0");
        window.setTimeout(() => toast.remove(), 220);
    };

    window.setTimeout(closeToast, 5200);
})();
</script>
<?php endif; ?>
</body>

</html>
