/** Mirrors server upload constraints to provide faster, non-authoritative feedback. */
export const imageAccept = 'image/jpeg,image/png,image/gif,image/webp';
export const videoAccept = 'video/mp4,video/webm,video/quicktime';

const imageMaxSize = 10 * 1024 * 1024;
const videoMaxSize = 50 * 1024 * 1024;
const documentMaxSize = 10 * 1024 * 1024;
const attachmentMaxCount = 5;
const attachmentTotalMaxSize = 100 * 1024 * 1024;
const imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
const videoExtensions = ['mp4', 'webm', 'mov'];
const documentExtensions = [
    'zip', 'txt', 'php', 'js', 'css', 'html', 'htm', 'json', 'xml', 'sql', 'py', 'java',
    'c', 'cpp', 'cs', 'md', 'pdf', 'doc', 'docx',
];

export const formatFileSize = (fileSize) => {
    if (fileSize < 1024) {
        return `${fileSize} B`;
    }

    if (fileSize < 1024 * 1024) {
        return `${(fileSize / 1024).toFixed(1)} KB`;
    }

    return `${(fileSize / (1024 * 1024)).toFixed(1)} MB`;
};

export const fileExtension = (fileName) => {
    const extensionSeparatorIndex = fileName.lastIndexOf('.');
    return extensionSeparatorIndex >= 0
        ? fileName.slice(extensionSeparatorIndex + 1).toLowerCase()
        : '';
};

export const attachmentType = (file) => {
    const extension = fileExtension(file.name);

    if (imageExtensions.includes(extension) && file.type.startsWith('image/')) {
        return 'image';
    }

    if (videoExtensions.includes(extension) && file.type.startsWith('video/')) {
        return 'video';
    }

    if (documentExtensions.includes(extension)) {
        return 'document';
    }

    return '';
};

const attachmentIdentity = (file) =>
    [file.name, file.size, file.lastModified, file.type].join('::');

const validationMessage = (file, attachmentProfile) => {
    const selectedAttachmentType = attachmentType(file);

    if (selectedAttachmentType === '') {
        return 'Choose a supported image, video, document, archive, or code file.';
    }

    if (attachmentProfile === 'reply' && selectedAttachmentType !== 'image') {
        return 'Comments only support JPEG, PNG, GIF, or WebP images.';
    }

    const attachmentMaxSize = selectedAttachmentType === 'video'
        ? videoMaxSize
        : selectedAttachmentType === 'image'
            ? imageMaxSize
            : documentMaxSize;

    if (file.size <= 0) {
        return `${file.name} is empty.`;
    }

    if (file.size > attachmentMaxSize) {
        return `${file.name} is larger than the allowed ${formatFileSize(attachmentMaxSize)}.`;
    }

    return '';
};

export const mergeAttachments = (selectedFiles, incomingFiles, attachmentProfile) => {
    const files = [...selectedFiles];
    const selectedIdentities = new Set(files.map(attachmentIdentity));
    let errorMessage = '';

    for (const incomingFile of incomingFiles) {
        const incomingIdentity = attachmentIdentity(incomingFile);

        if (selectedIdentities.has(incomingIdentity)) {
            errorMessage ||= `${incomingFile.name} is already selected.`;
            continue;
        }

        if (files.length >= attachmentMaxCount) {
            errorMessage ||= 'You can upload up to 5 files at a time.';
            break;
        }

        const incomingFileError = validationMessage(incomingFile, attachmentProfile);

        if (incomingFileError !== '') {
            errorMessage ||= incomingFileError;
            continue;
        }

        const nextTotalFileSize = files.reduce(
            (totalSize, selectedFile) => totalSize + selectedFile.size,
            0,
        ) + incomingFile.size;

        if (nextTotalFileSize > attachmentTotalMaxSize) {
            errorMessage ||= 'The combined attachment size must be 100 MB or smaller.';
            continue;
        }

        files.push(incomingFile);
        selectedIdentities.add(incomingIdentity);
    }

    return { files, errorMessage };
};
