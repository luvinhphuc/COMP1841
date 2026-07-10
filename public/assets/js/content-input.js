(() => {
    const inputs = Array.from(document.querySelectorAll('[data-content-input]'));
    const imageAccept = 'image/jpeg,image/png,image/gif,image/webp';
    const videoAccept = 'video/mp4,video/webm,video/quicktime';

    if (inputs.length === 0) {
        return;
    }

    const formatFileSize = (size) => {
        if (size < 1024) {
            return `${size} B`;
        }

        if (size < 1024 * 1024) {
            return `${(size / 1024).toFixed(1)} KB`;
        }

        return `${(size / (1024 * 1024)).toFixed(1)} MB`;
    };

    inputs.forEach((input) => {
        const contentInput = input.querySelector('[data-content-field]');
        const toolbarButtons = Array.from(input.querySelectorAll('[data-content-action]'));
        const fileInput = input.querySelector('[data-attachment-input]');
        const attachmentSurface = input.querySelector('[data-attachment-surface]');
        const dropzone = input.querySelector('[data-attachment-dropzone]');
        const preview = input.querySelector('[data-attachment-preview]');
        const previewMedia = input.querySelector('[data-attachment-preview-media]');
        const previewName = input.querySelector('[data-attachment-preview-name]');
        const previewMeta = input.querySelector('[data-attachment-preview-meta]');
        const removeButton = input.querySelector('[data-attachment-remove]');
        const defaultAccept = input.dataset.defaultAccept || '';
        const imageOnly = defaultAccept === imageAccept;
        let previewUrl = '';
        let dragDepth = 0;

        const insertCodeBlock = () => {
            if (!(contentInput instanceof HTMLTextAreaElement)) {
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
            if (!(fileInput instanceof HTMLInputElement)) {
                return;
            }

            fileInput.accept = accept;
            fileInput.click();
        };

        toolbarButtons.forEach((button) => {
            button.addEventListener('click', () => {
                const action = button.dataset.contentAction;

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

        if (!(fileInput instanceof HTMLInputElement)) {
            return;
        }

        const clearPreviewUrl = () => {
            if (previewUrl !== '') {
                URL.revokeObjectURL(previewUrl);
                previewUrl = '';
            }
        };

        const hideDropzone = () => {
            dragDepth = 0;
            dropzone?.classList.add('hidden');
            dropzone?.classList.remove('flex');
        };

        const resetPreview = () => {
            clearPreviewUrl();
            fileInput.accept = defaultAccept;

            if (previewMedia) {
                previewMedia.innerHTML = '';
            }

            if (previewName) {
                previewName.textContent = '';
            }

            if (previewMeta) {
                previewMeta.textContent = '';
            }

            preview?.classList.add('hidden');
            hideDropzone();
        };

        const renderPreviewMedia = (file) => {
            if (!previewMedia) {
                return;
            }

            previewMedia.innerHTML = '';

            if (file.type.startsWith('image/')) {
                previewUrl = URL.createObjectURL(file);
                const image = document.createElement('img');
                image.className = 'h-full max-h-52 w-full object-contain';
                image.alt = file.name;
                image.src = previewUrl;
                previewMedia.append(image);
                return;
            }

            if (file.type.startsWith('video/')) {
                previewUrl = URL.createObjectURL(file);
                const video = document.createElement('video');
                video.className = 'h-full max-h-52 w-full object-contain';
                video.controls = true;
                video.preload = 'metadata';
                video.src = previewUrl;
                previewMedia.append(video);
                return;
            }

            const fileLabel = document.createElement('span');
            fileLabel.className = 'flex min-h-40 items-center justify-center bg-[#eef2f6] px-4 text-sm font-bold text-[#27313a]';
            fileLabel.textContent = 'File attachment';
            previewMedia.append(fileLabel);
        };

        const renderPreview = () => {
            const file = fileInput.files?.[0];
            hideDropzone();

            if (!file) {
                resetPreview();
                return;
            }

            clearPreviewUrl();
            renderPreviewMedia(file);

            if (previewName) {
                previewName.textContent = file.name;
            }

            if (previewMeta) {
                previewMeta.textContent = `${file.type || 'File'} | ${formatFileSize(file.size)}`;
            }

            preview?.classList.remove('hidden');
        };

        const showDropzone = () => {
            dropzone?.classList.remove('hidden');
            dropzone?.classList.add('flex');
        };

        const isFileDrag = (event) => Array.from(event.dataTransfer?.types ?? []).includes('Files');
        const acceptsFile = (file) => !imageOnly || file.type.startsWith('image/');

        fileInput.addEventListener('change', renderPreview);

        removeButton?.addEventListener('click', (event) => {
            event.preventDefault();
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

        dropzone.addEventListener('drop', (event) => {
            event.preventDefault();
            setDragState(false);
            hideDropzone();

            const file = event.dataTransfer?.files?.[0];

            if (!file || !acceptsFile(file)) {
                return;
            }

            const transfer = new DataTransfer();
            transfer.items.add(file);
            fileInput.files = transfer.files;
            renderPreview();
        });

        window.addEventListener('beforeunload', clearPreviewUrl);
    });
})();
