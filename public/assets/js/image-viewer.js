/** Presents grouped images in an accessible dialog with keyboard navigation. */
(() => {
    const imageViewer = document.querySelector('[data-image-viewer]');

    if (!(imageViewer instanceof HTMLDialogElement)) {
        return;
    }

    const imageViewerSurface = imageViewer.querySelector('[data-image-viewer-surface]');
    const imageViewerImage = imageViewer.querySelector('[data-image-viewer-image]');
    const imageViewerStatus = imageViewer.querySelector('[data-image-viewer-status]');
    const imageViewerClose = imageViewer.querySelector('[data-image-viewer-close]');
    const imageViewerPrevious = imageViewer.querySelector('[data-image-viewer-previous]');
    const imageViewerNext = imageViewer.querySelector('[data-image-viewer-next]');
    let imageViewerTriggers = [];
    let activeImageIndex = -1;
    let activeImageTrigger = null;

    if (!(imageViewerImage instanceof HTMLImageElement)) {
        return;
    }

    const updateImage = () => {
        const imageTrigger = imageViewerTriggers[activeImageIndex];

        if (!(imageTrigger instanceof HTMLElement)) {
            return;
        }

        imageViewerImage.src = imageTrigger.dataset.imageViewerSrc || '';
        imageViewerImage.alt = imageTrigger.dataset.imageViewerAlt || '';

        if (imageViewerStatus instanceof HTMLElement) {
            imageViewerStatus.textContent = `Image ${activeImageIndex + 1} of ${imageViewerTriggers.length}`;
        }

        if (imageViewerPrevious instanceof HTMLButtonElement) {
            imageViewerPrevious.disabled = activeImageIndex <= 0;
        }

        if (imageViewerNext instanceof HTMLButtonElement) {
            imageViewerNext.disabled = activeImageIndex >= imageViewerTriggers.length - 1;
        }
    };

    const moveImage = (direction) => {
        const nextImageIndex = activeImageIndex + direction;

        if (nextImageIndex < 0 || nextImageIndex >= imageViewerTriggers.length) {
            return;
        }

        activeImageIndex = nextImageIndex;
        updateImage();
    };

    const closeImageViewer = () => {
        if (imageViewer.open) {
            imageViewer.close();
        }
    };

    const openImageViewer = (imageTrigger) => {
        const imageViewerGroup = imageTrigger.dataset.imageViewerGroup || '';
        imageViewerTriggers = Array.from(document.querySelectorAll('[data-image-viewer-trigger]')).filter(
            (candidateTrigger) => candidateTrigger instanceof HTMLElement
                && candidateTrigger.dataset.imageViewerGroup === imageViewerGroup
                && (candidateTrigger.dataset.imageViewerSrc || '') !== ''
        );
        activeImageIndex = imageViewerTriggers.indexOf(imageTrigger);

        if (activeImageIndex < 0) {
            return;
        }

        activeImageTrigger = imageTrigger;
        updateImage();

        if (!imageViewer.open) {
            imageViewer.showModal();
        }

        imageViewerClose?.focus();
    };

    document.addEventListener('click', (event) => {
        const eventTarget = event.target;

        if (!(eventTarget instanceof Element)) {
            return;
        }

        const imageTrigger = eventTarget.closest('[data-image-viewer-trigger]');

        if (!(imageTrigger instanceof HTMLElement)) {
            return;
        }

        event.preventDefault();
        openImageViewer(imageTrigger);
    });

    imageViewerClose?.addEventListener('click', closeImageViewer);
    imageViewerPrevious?.addEventListener('click', () => moveImage(-1));
    imageViewerNext?.addEventListener('click', () => moveImage(1));

    imageViewer.addEventListener('click', (event) => {
        if (event.target === imageViewer || event.target === imageViewerSurface) {
            closeImageViewer();
        }
    });

    imageViewer.addEventListener('keydown', (event) => {
        if (event.key === 'ArrowLeft') {
            event.preventDefault();
            moveImage(-1);
        }

        if (event.key === 'ArrowRight') {
            event.preventDefault();
            moveImage(1);
        }
    });

    imageViewer.addEventListener('close', () => {
        imageViewerImage.src = '';
        imageViewerImage.alt = '';
        imageViewerTriggers = [];
        activeImageIndex = -1;

        if (activeImageTrigger instanceof HTMLElement && activeImageTrigger.isConnected) {
            activeImageTrigger.focus();
        }

        activeImageTrigger = null;
    });
})();
