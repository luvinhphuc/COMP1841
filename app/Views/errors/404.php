<?php
/**
 * Variables passed from App\Core\Controller::notFound()
 *
 * No controller variables are required by this view.
 */
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>404 - Page Not Found</title>

    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/app.css">
</head>

<body class="bg-white text-ui-ink">
    <main id="main-content" class="flex min-h-screen items-center justify-center px-5 py-12">
        <section class="w-full max-w-2xl p-8 text-center sm:p-12" aria-labelledby="error-title" data-motion-intro>
            <div class="mx-auto w-full max-w-[300px]" aria-hidden="true" data-motion-reveal>
                <?php include ROOT_PATH . '/public/assets/svg/404.svg'; ?>
            </div>
            <h1 id="error-title" class="ui-page-title">Page not found</h1>

            <p class="ui-page-description mx-auto">
                The page you are looking for does not exist or may have been moved.
            </p>

            <a href="<?= BASE_URL ?: '/' ?>" class="ui-button ui-button-primary mt-7">
                Back to homepage
            </a>
        </section>
    </main>

    <script src="<?= BASE_URL ?>/assets/js/gsap.min.js"></script>
    <script
        src="<?= BASE_URL ?>/assets/js/site-animations.js?v=<?= filemtime(ROOT_PATH . '/public/assets/js/site-animations.js') ?>">
    </script>

</body>

</html>