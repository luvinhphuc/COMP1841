/** Renders removable file previews and revokes temporary object URLs after use. */
import {
    attachmentType,
    fileExtension,
    formatFileSize,
} from './attachment-validation.js';

export const createAttachmentPreview = ({
    preview,
    previewList,
    imageViewerGroup,
    onRemove,
}) => {
    const previewUrls = [];

    const clearUrls = () => {
        // Object URLs hold Blob references, so release them whenever previews are rebuilt.
        previewUrls.splice(0).forEach((previewUrl) => URL.revokeObjectURL(previewUrl));
    };

    const createDocumentPreview = (file) => {
        const documentPreview = document.createElement('div');
        documentPreview.className = 'flex size-full flex-col items-center justify-center gap-2 bg-ui-canvas px-3 text-center text-ui-muted';

        const documentIcon = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
        documentIcon.setAttribute('viewBox', '0 0 24 24');
        documentIcon.setAttribute('fill', 'none');
        documentIcon.setAttribute('aria-hidden', 'true');
        documentIcon.setAttribute('class', 'size-9');
        documentIcon.innerHTML = '<path d="M7 3.75h6.5L18 8.25v12H7v-16.5Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/><path d="M13.5 3.75v4.5H18M9.75 12h5.5M9.75 15.5h5.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>';

        const documentType = document.createElement('span');
        documentType.className = 'max-w-full truncate text-xs font-bold uppercase';
        documentType.textContent = fileExtension(file.name) || 'FILE';

        documentPreview.append(documentIcon, documentType);
        return documentPreview;
    };

    const render = (selectedFiles) => {
        clearUrls();

        if (!(previewList instanceof HTMLElement)) {
            return;
        }

        previewList.innerHTML = '';
        preview?.classList.toggle('hidden', selectedFiles.length === 0);

        selectedFiles.forEach((selectedFile, selectedFileIndex) => {
            const selectedAttachmentType = attachmentType(selectedFile);
            const previewTile = document.createElement('article');
            previewTile.className = 'group relative aspect-square min-w-0 overflow-hidden rounded-xl bg-ui-canvas ring-1 ring-ui-border';

            if (selectedAttachmentType === 'image') {
                const previewUrl = URL.createObjectURL(selectedFile);
                const imageButton = document.createElement('button');
                const image = document.createElement('img');

                previewUrls.push(previewUrl);
                imageButton.type = 'button';
                imageButton.className = 'size-full cursor-zoom-in focus-visible:outline-2 focus-visible:outline-offset-[-3px] focus-visible:outline-brand-blue';
                imageButton.setAttribute('aria-label', `View ${selectedFile.name} full size`);
                imageButton.dataset.imageViewerTrigger = '';
                imageButton.dataset.imageViewerGroup = imageViewerGroup;
                imageButton.dataset.imageViewerSrc = previewUrl;
                imageButton.dataset.imageViewerAlt = selectedFile.name;
                image.className = 'size-full object-cover transition duration-200 group-hover:scale-[1.02]';
                image.src = previewUrl;
                image.alt = '';
                imageButton.append(image);
                previewTile.append(imageButton);
            } else if (selectedAttachmentType === 'video') {
                const previewUrl = URL.createObjectURL(selectedFile);
                const video = document.createElement('video');

                previewUrls.push(previewUrl);
                video.className = 'size-full object-cover';
                video.src = previewUrl;
                video.muted = true;
                video.preload = 'metadata';
                video.setAttribute('aria-label', selectedFile.name);
                previewTile.append(video);
            } else {
                previewTile.append(createDocumentPreview(selectedFile));
            }

            const fileSizeBadge = document.createElement('span');
            fileSizeBadge.className = 'pointer-events-none absolute bottom-2 left-2 rounded-md bg-ui-ink/75 px-2 py-1 text-[11px] font-semibold leading-4 text-white backdrop-blur';
            fileSizeBadge.textContent = formatFileSize(selectedFile.size);

            const removeButton = document.createElement('button');
            removeButton.type = 'button';
            removeButton.className = 'absolute right-2 top-2 z-10 inline-flex size-8 items-center justify-center rounded-full bg-white/90 text-ui-danger shadow-sm ring-1 ring-ui-border transition hover:bg-white focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-ui-danger';
            removeButton.setAttribute('aria-label', `Remove ${selectedFile.name}`);
            removeButton.dataset.attachmentRemoveIndex = String(selectedFileIndex);
            removeButton.innerHTML = '<svg viewBox="0 0 20 20" class="size-4" fill="none" aria-hidden="true"><path d="m6 6 8 8M14 6l-8 8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>';

            previewTile.append(fileSizeBadge, removeButton);
            previewList.append(previewTile);
        });
    };

    previewList?.addEventListener('click', (event) => {
        const eventTarget = event.target;

        if (!(eventTarget instanceof Element)) {
            return;
        }

        const removeButton = eventTarget.closest('[data-attachment-remove-index]');

        if (!(removeButton instanceof HTMLButtonElement)) {
            return;
        }

        event.preventDefault();
        const selectedFileIndex = Number.parseInt(
            removeButton.dataset.attachmentRemoveIndex || '',
            10,
        );

        if (Number.isInteger(selectedFileIndex)) {
            onRemove(selectedFileIndex);
        }
    });

    return { render, clearUrls };
};
