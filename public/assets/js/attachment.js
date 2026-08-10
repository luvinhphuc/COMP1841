/** Composes validation, preview, and drag-and-drop behaviours for attachment inputs. */
import {
    imageAccept,
    mergeAttachments,
    videoAccept,
} from './attachment-validation.js';
import { createAttachmentPreview } from './attachment-preview.js';
import { createAttachmentDropzone } from './attachment-dropzone.js';

let attachmentInstanceIndex = 0;

export const initAttachments = (root = document) => {
    // The initialisation flag prevents duplicate listeners when editors are opened repeatedly.
    const attachmentRoots = [
        ...(root instanceof Element && root.matches('[data-attachment]') ? [root] : []),
        ...Array.from(root.querySelectorAll('[data-attachment]')),
    ];

    attachmentRoots.forEach((attachmentRoot) => {
        if (attachmentRoot.dataset.attachmentInitialised === 'true') {
            return;
        }

        const attachmentInput = attachmentRoot.querySelector('[data-attachment-input]');

        if (!(attachmentInput instanceof HTMLInputElement)) {
            return;
        }

        attachmentRoot.dataset.attachmentInitialised = 'true';

        const attachmentButtons = Array.from(
            attachmentRoot.querySelectorAll('[data-attachment-action]'),
        );
        const attachmentSurface = attachmentRoot.querySelector('[data-attachment-surface]');
        const attachmentDropzone = attachmentRoot.querySelector('[data-attachment-dropzone]');
        const attachmentPreview = attachmentRoot.querySelector('[data-attachment-preview]');
        const attachmentPreviewList = attachmentRoot.querySelector('[data-attachment-preview-list]');
        const attachmentError = attachmentRoot.querySelector('[data-attachment-error]');
        const attachmentProfile = attachmentRoot.dataset.attachmentProfile || 'none';
        const defaultAccept = attachmentInput.accept;
        const imageViewerGroup = `attachment-preview-${attachmentInstanceIndex}`;
        const selectedFiles = [];

        attachmentInstanceIndex += 1;

        const setAttachmentError = (errorMessage) => {
            if (!(attachmentError instanceof HTMLElement)) {
                return;
            }

            attachmentError.textContent = errorMessage;
            attachmentError.classList.toggle('hidden', errorMessage === '');
            attachmentError.classList.toggle('block', errorMessage !== '');
            attachmentInput.setAttribute('aria-invalid', errorMessage === '' ? 'false' : 'true');
        };

        const synchroniseAttachmentInput = () => {
            // DataTransfer is the browser-supported way to replace a file input's immutable FileList.
            const dataTransfer = new DataTransfer();
            selectedFiles.forEach((selectedFile) => dataTransfer.items.add(selectedFile));
            attachmentInput.files = dataTransfer.files;
            attachmentInput.accept = defaultAccept;
        };

        const attachmentPreviewController = createAttachmentPreview({
            preview: attachmentPreview,
            previewList: attachmentPreviewList,
            imageViewerGroup,
            onRemove: (selectedFileIndex) => {
                if (
                    selectedFileIndex < 0
                    || selectedFileIndex >= selectedFiles.length
                ) {
                    return;
                }

                selectedFiles.splice(selectedFileIndex, 1);
                synchroniseAttachmentInput();
                attachmentPreviewController.render(selectedFiles);
                setAttachmentError('');
            },
        });

        const addAttachments = (incomingFiles) => {
            const result = mergeAttachments(
                selectedFiles,
                incomingFiles,
                attachmentProfile,
            );

            selectedFiles.splice(0, selectedFiles.length, ...result.files);
            synchroniseAttachmentInput();
            attachmentPreviewController.render(selectedFiles);
            setAttachmentError(result.errorMessage);
        };

        const attachmentDropzoneController = createAttachmentDropzone({
            surface: attachmentSurface,
            dropzone: attachmentDropzone,
            onFiles: addAttachments,
        });

        const chooseAttachments = (accept) => {
            attachmentInput.accept = accept;
            attachmentInput.click();
        };

        attachmentButtons.forEach((attachmentButton) => {
            attachmentButton.addEventListener('click', () => {
                const attachmentAction = attachmentButton.dataset.attachmentAction;

                if (attachmentAction === 'image') {
                    chooseAttachments(imageAccept);
                    return;
                }

                if (attachmentAction === 'video') {
                    chooseAttachments(videoAccept);
                    return;
                }

                if (attachmentAction === 'files') {
                    chooseAttachments(defaultAccept);
                }
            });
        });

        attachmentInput.addEventListener('change', () => {
            addAttachments(Array.from(attachmentInput.files ?? []));
        });

        attachmentRoot.closest('form')?.addEventListener('reset', () => {
            selectedFiles.splice(0);
            synchroniseAttachmentInput();
            attachmentPreviewController.render(selectedFiles);
            attachmentDropzoneController.reset();
            setAttachmentError('');
        });

        window.addEventListener('beforeunload', attachmentPreviewController.clearUrls);
    });
};

initAttachments();
