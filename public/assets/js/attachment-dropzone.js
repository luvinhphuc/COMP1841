/** Converts an attachment surface into a keyboard-accessible drag-and-drop target. */
export const createAttachmentDropzone = ({ surface, dropzone, onFiles }) => {
    if (!(surface instanceof HTMLElement) || !(dropzone instanceof HTMLElement)) {
        return { reset: () => {} };
    }

    const defaultBorderClass = dropzone.classList.contains('border-ui-danger')
        ? 'border-ui-danger'
        : 'border-ui-border-strong';
    let dragDepth = 0;

    const setDragState = (isDragging) => {
        dropzone.classList.toggle(defaultBorderClass, !isDragging);
        dropzone.classList.toggle('border-brand-blue', isDragging);
        dropzone.classList.toggle('bg-ui-canvas', isDragging);
    };

    const show = () => {
        dropzone.classList.remove('hidden');
        dropzone.classList.add('flex');
    };

    const hide = () => {
        dragDepth = 0;
        dropzone.classList.add('hidden');
        dropzone.classList.remove('flex');
    };

    const reset = () => {
        setDragState(false);
        hide();
    };

    const isFileDrag = (event) =>
        Array.from(event.dataTransfer?.types ?? []).includes('Files');

    surface.addEventListener('dragenter', (event) => {
        if (!isFileDrag(event)) {
            return;
        }

        event.preventDefault();
        dragDepth += 1;
        show();
        setDragState(true);
    });

    surface.addEventListener('dragover', (event) => {
        if (!isFileDrag(event)) {
            return;
        }

        event.preventDefault();
        show();
        setDragState(true);
    });

    surface.addEventListener('dragleave', (event) => {
        if (!isFileDrag(event)) {
            return;
        }

        event.preventDefault();
        dragDepth -= 1;

        if (dragDepth <= 0) {
            reset();
        }
    });

    dropzone.addEventListener('drop', (event) => {
        if (!isFileDrag(event)) {
            return;
        }

        event.preventDefault();
        const files = Array.from(event.dataTransfer?.files ?? []);
        reset();
        onFiles(files);
    });

    return { reset };
};
