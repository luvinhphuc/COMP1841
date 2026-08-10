<?php
/**
 * Reusable full-size image viewer with no external PHP variables.
 *
 * Triggers must provide data-image-viewer-trigger, data-image-viewer-src,
 * data-image-viewer-alt, and data-image-viewer-group.
 */
?>

<dialog
    class="m-auto h-screen w-screen max-h-none max-w-none overflow-hidden bg-transparent p-0 text-white backdrop:bg-ui-ink/90"
    aria-label="Image viewer" data-image-viewer>
    <div class="relative flex h-full w-full items-center justify-center p-4 sm:p-8" data-image-viewer-surface>
        <p class="sr-only" aria-live="polite" data-image-viewer-status></p>

        <img src="" alt="" class="max-h-full max-w-full object-contain" data-image-viewer-image>

        <button type="button"
            class="absolute right-4 top-4 inline-flex size-11 items-center justify-center rounded-full bg-ui-ink/75 text-white ring-1 ring-white/20 backdrop-blur transition hover:bg-ui-ink focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white sm:right-6 sm:top-6"
            aria-label="Close image viewer" data-image-viewer-close>
            <svg viewBox="0 0 20 20" class="size-5" fill="none" aria-hidden="true">
                <path d="m5 5 10 10M15 5 5 15" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
            </svg>
        </button>

        <button type="button"
            class="absolute left-4 inline-flex size-11 items-center justify-center rounded-full bg-ui-ink/75 text-white ring-1 ring-white/20 backdrop-blur transition hover:bg-ui-ink focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white disabled:cursor-not-allowed disabled:opacity-30 sm:left-6"
            aria-label="Previous image" data-image-viewer-previous>
            <svg viewBox="0 0 20 20" class="size-5" fill="none" aria-hidden="true">
                <path d="m12.5 4.5-5.5 5.5 5.5 5.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"
                    stroke-linejoin="round" />
            </svg>
        </button>

        <button type="button"
            class="absolute right-4 inline-flex size-11 items-center justify-center rounded-full bg-ui-ink/75 text-white ring-1 ring-white/20 backdrop-blur transition hover:bg-ui-ink focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white disabled:cursor-not-allowed disabled:opacity-30 sm:right-6"
            aria-label="Next image" data-image-viewer-next>
            <svg viewBox="0 0 20 20" class="size-5" fill="none" aria-hidden="true">
                <path d="m7.5 4.5 5.5 5.5-5.5 5.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"
                    stroke-linejoin="round" />
            </svg>
        </button>
    </div>
</dialog>