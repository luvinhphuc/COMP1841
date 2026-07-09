(() => {
    const contentInput = document.querySelector('#content');
    const toolbarButtons = Array.from(document.querySelectorAll('[data-composer-action]'));
    const fileInput = document.querySelector('#attachment-file');
    const fileName = document.querySelector('[data-attachment-file-name]');
    const attachmentSurface = document.querySelector('[data-attachment-surface]');
    const dropzone = document.querySelector('[data-attachment-dropzone]');
    const emptyState = document.querySelector('[data-attachment-empty]');
    const preview = document.querySelector('[data-attachment-preview]');
    const previewMedia = document.querySelector('[data-attachment-preview-media]');
    const previewName = document.querySelector('[data-attachment-preview-name]');
    const previewMeta = document.querySelector('[data-attachment-preview-meta]');
    const removeButton = document.querySelector('[data-attachment-remove]');
    const mediaAccept = 'image/jpeg,image/png,image/gif,image/webp,video/mp4,video/webm,video/quicktime';
    const imageAccept = 'image/jpeg,image/png,image/gif,image/webp';
    const videoAccept = 'video/mp4,video/webm,video/quicktime';
    let previewUrl = '';
    let dragDepth = 0;

    const insertCodeBlock = () => {
        if (!contentInput) {
            return;
        }

        const start = contentInput.selectionStart;
        const end = contentInput.selectionEnd;
        const selectedText = contentInput.value.slice(start, end);
        const codeText = selectedText !== '' ? selectedText : 'code';
        const block = `<pre>\n${codeText}\n</pre>`;

        contentInput.setRangeText(block, start, end, 'end');
        contentInput.focus();

        if (selectedText === '') {
            const cursorStart = start + '<pre>\n'.length;
            const cursorEnd = cursorStart + codeText.length;

            contentInput.setSelectionRange(cursorStart, cursorEnd);
        }
    };

    const chooseAttachment = (accept) => {
        if (!fileInput) {
            return;
        }

        fileInput.accept = accept;
        fileInput.click();
    };

    toolbarButtons.forEach((button) => {
        button.addEventListener('click', () => {
            const action = button.dataset.composerAction;

            if (action === 'image') {
                chooseAttachment(imageAccept);
                return;
            }

            if (action === 'video') {
                chooseAttachment(videoAccept);
                return;
            }

            if (action === 'code') {
                insertCodeBlock();
            }
        });
    });

    if (!fileInput || !fileName) {
        return;
    }

    const defaultHelpText = 'JPEG, PNG, GIF, WebP, MP4, WebM, or MOV';

    const formatFileSize = (size) => {
        if (size < 1024) {
            return `${size} B`;
        }

        if (size < 1024 * 1024) {
            return `${(size / 1024).toFixed(1)} KB`;
        }

        return `${(size / (1024 * 1024)).toFixed(1)} MB`;
    };

    const clearPreviewUrl = () => {
        if (previewUrl) {
            URL.revokeObjectURL(previewUrl);
            previewUrl = '';
        }
    };

    const resetPreview = () => {
        clearPreviewUrl();
        fileInput.accept = mediaAccept;
        fileName.textContent = defaultHelpText;

        if (previewMedia) {
            previewMedia.innerHTML = '';
        }

        if (previewName) {
            previewName.textContent = '';
        }

        if (previewMeta) {
            previewMeta.textContent = '';
        }

        emptyState?.classList.remove('hidden');
        preview?.classList.add('hidden');
    };

    const showDropzone = () => {
        if (!dropzone) {
            return;
        }

        dropzone.classList.remove('hidden');
        dropzone.classList.add('flex');
    };

    const hideDropzone = () => {
        if (!dropzone) {
            return;
        }

        dragDepth = 0;
        dropzone.classList.add('hidden');
        dropzone.classList.remove('flex');
    };

    const isFileDrag = (event) => Array.from(event.dataTransfer?.types ?? []).includes('Files');

    const renderPreview = () => {
        const files = fileInput.files;
        hideDropzone();

        if (!files || files.length === 0) {
            resetPreview();
            return;
        }

        const file = files[0];
        clearPreviewUrl();
        previewUrl = URL.createObjectURL(file);
        fileName.textContent = file.name;

        if (previewName) {
            previewName.textContent = file.name;
        }

        if (previewMeta) {
            previewMeta.textContent = `${file.type || 'Media file'} | ${formatFileSize(file.size)}`;
        }

        if (previewMedia) {
            previewMedia.innerHTML = '';

            if (file.type.startsWith('video/')) {
                const video = document.createElement('video');
                video.className = 'h-full max-h-52 w-full object-contain';
                video.controls = true;
                video.preload = 'metadata';
                video.src = previewUrl;
                previewMedia.append(video);
            } else {
                const image = document.createElement('img');
                image.className = 'h-full max-h-52 w-full object-contain';
                image.alt = file.name;
                image.src = previewUrl;
                previewMedia.append(image);
            }
        }

        emptyState?.classList.add('hidden');
        preview?.classList.remove('hidden');
    };

    dropzone?.addEventListener('click', () => {
        fileInput.accept = mediaAccept;
        hideDropzone();
    });

    fileInput.addEventListener('change', renderPreview);

    removeButton?.addEventListener('click', (event) => {
        event.preventDefault();
        event.stopPropagation();
        fileInput.value = '';
        resetPreview();
    });

    if (!dropzone || !attachmentSurface) {
        return;
    }

    const defaultBorderClass = dropzone.classList.contains('border-[#ba1a1a]')
        ? 'border-[#ba1a1a]'
        : 'border-[#c4c7c7]';

    const setDragState = (isDragging) => {
        dropzone.classList.toggle(defaultBorderClass, !isDragging);
        dropzone.classList.toggle('border-[#315f90]', isDragging);
        dropzone.classList.toggle('bg-[#f7f9fd]', isDragging);
    };

    attachmentSurface.addEventListener('dragenter', (event) => {
        if (!isFileDrag(event)) {
            return;
        }

        event.preventDefault();
        dragDepth++;
        showDropzone();
        setDragState(true);
    });

    attachmentSurface.addEventListener('dragover', (event) => {
        if (!isFileDrag(event)) {
            return;
        }

        event.preventDefault();
        showDropzone();
        setDragState(true);
    });

    attachmentSurface.addEventListener('dragleave', (event) => {
        if (!isFileDrag(event)) {
            return;
        }

        event.preventDefault();
        dragDepth--;

        if (dragDepth <= 0) {
            setDragState(false);
            hideDropzone();
        }
    });

    dropzone.addEventListener('dragover', (event) => {
        event.preventDefault();
        setDragState(true);
    });

    ['dragend', 'drop'].forEach((eventName) => {
        document.addEventListener(eventName, () => {
            setDragState(false);
            hideDropzone();
        });
    });

    ['dragenter', 'dragover'].forEach((eventName) => {
        dropzone.addEventListener(eventName, (event) => {
            event.preventDefault();
            setDragState(true);
        });
    });

    dropzone.addEventListener('drop', (event) => {
        event.preventDefault();
        setDragState(false);
        hideDropzone();

        const file = event.dataTransfer?.files?.[0];

        if (!file || (!file.type.startsWith('image/') && !file.type.startsWith('video/'))) {
            return;
        }

        const transfer = new DataTransfer();
        transfer.items.add(file);
        fileInput.files = transfer.files;
        renderPreview();
    });
})();
