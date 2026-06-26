<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>404 - Page Not Found</title>

        <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/app.css">
        <link rel="preconnect" href="https://unpkg.com" crossorigin>
        <link rel="preconnect" href="https://lottie.host" crossorigin>

        <link rel="modulepreload"
              href="https://unpkg.com/@lottiefiles/dotlottie-wc@0.9.14/dist/dotlottie-wc.js">

        <link rel="preload"
              href="https://lottie.host/8991c8a3-ab6f-4a57-8245-d9d41c47a959/7SZzrJAVRh.lottie"
              as="fetch"
              type="application/octet-stream"
              crossorigin>

        <script src="https://unpkg.com/@lottiefiles/dotlottie-wc@0.9.14/dist/dotlottie-wc.js"
                type="module"></script>
    </head>
    <body>

        <main class="error-page flex flex-col items-center justify-center h-screen">
            <div class="error-code text-9xl text-justify">
                <dotlottie-wc
                        src="https://lottie.host/8991c8a3-ab6f-4a57-8245-d9d41c47a959/7SZzrJAVRh.lottie"
                        style="width: 500px;height: 200px"
                        autoplay
                        loop
                ></dotlottie-wc>

            </div>
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