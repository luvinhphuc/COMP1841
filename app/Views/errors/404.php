<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>404 - Page Not Found</title>

        <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/app.css">
    </head>
    <body>

        <main class="error-page flex flex-col items-center justify-center h-screen">
            <p class="error-code text-9xl text-justify">404</p>

            <h1 class="font-bold text-4xl my-[24px]">Page not found</h1>

            <p>
                The page you are looking for does not exist or has been moved.
            </p>

            <a href="<?= BASE_URL ?>"
               class="inline-block mt-6 px-4 py-2 rounded-2xl border border-blue-500 text-blue-500 transition duration-300 ease-in-out hover:-translate-y-1 hover:scale-105 hover:bg-blue-500 hover:text-white">
                Back to homepage
            </a>
        </main>

    </body>
</html>