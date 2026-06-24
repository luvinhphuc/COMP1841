<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>COMP1841</title>
        <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/app.css">
        <script src="<?= BASE_URL ?>/assets/js/gsap.min.js" defer></script>
        <script src="<?= BASE_URL ?>/assets/js/navbar.js?v=<?= filemtime(ROOT_PATH . '/public/assets/js/navbar.js') ?>" defer></script>
    </head>

    <body class="min-h-screen scrollbar-d-none bg-white font-sans text-[#222]">
        <?php require ROOT_PATH . '/app/Views/partials/navbar.php'; ?>

        <main>
