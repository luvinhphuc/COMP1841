<?php

namespace App\Services;

/**
 * Validates uploads and manages their safe storage beneath the public upload root.
 */
class AttachmentService
{
    private const AVATAR_MAX_SIZE = 2097152;
    private const IMAGE_MAX_SIZE = 10485760;
    private const VIDEO_MAX_SIZE = 52428800;
    private const DOCUMENT_MAX_SIZE = 10485760;
    private const ATTACHMENT_MAX_COUNT = 5;
    private const ATTACHMENT_TOTAL_MAX_SIZE = 104857600;

    public function validatedAttachments(?array $uploadedFiles, bool $isImageOnly = false): array
    {
        $uploadedFileRecords = $this->normaliseUploadedFiles($uploadedFiles);

        if ($uploadedFileRecords === []) {
            return ['attachments' => [], 'error' => ''];
        }

        if (count($uploadedFileRecords) > self::ATTACHMENT_MAX_COUNT) {
            return ['attachments' => [], 'error' => 'You can upload up to 5 files at a time.'];
        }

        $attachments = [];
        $totalFileSize = 0;

        foreach ($uploadedFileRecords as $uploadedFileRecord) {
            $attachmentValidationData = $this->validateAttachment($uploadedFileRecord, $isImageOnly);

            if ($attachmentValidationData['error'] !== '') {
                return ['attachments' => [], 'error' => $attachmentValidationData['error']];
            }

            $attachment = $attachmentValidationData['attachment'];
            $totalFileSize += (int) ($attachment['file_size'] ?? 0);
            $attachments[] = $attachment;
        }

        if ($totalFileSize > self::ATTACHMENT_TOTAL_MAX_SIZE) {
            return ['attachments' => [], 'error' => 'The combined attachment size must be 100 MB or smaller.'];
        }

        return ['attachments' => $attachments, 'error' => ''];
    }

    private function normaliseUploadedFiles(?array $uploadedFiles): array
    {
        if (!is_array($uploadedFiles) || !is_array($uploadedFiles['name'] ?? null)) {
            return [];
        }

        $uploadedFileRecords = [];

        foreach ($uploadedFiles['name'] as $fileIndex => $fileName) {
            $uploadError = (int) ($uploadedFiles['error'][$fileIndex] ?? UPLOAD_ERR_NO_FILE);

            if ($uploadError === UPLOAD_ERR_NO_FILE) {
                continue;
            }

            $uploadedFileRecords[] = [
                'name' => $fileName,
                'type' => $uploadedFiles['type'][$fileIndex] ?? '',
                'tmp_name' => $uploadedFiles['tmp_name'][$fileIndex] ?? '',
                'error' => $uploadError,
                'size' => $uploadedFiles['size'][$fileIndex] ?? 0,
            ];
        }

        return $uploadedFileRecords;
    }

    private function validateAttachment(array $uploadedFileRecord, bool $isImageOnly): array
    {
        $uploadError = (int) ($uploadedFileRecord['error'] ?? UPLOAD_ERR_OK);

        if ($uploadError !== UPLOAD_ERR_OK) {
            return ['attachment' => [], 'error' => $this->uploadErrorMessage($uploadError)];
        }

        $originalName = basename((string) ($uploadedFileRecord['name'] ?? 'attachment'));
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        $fileSize = (int) ($uploadedFileRecord['size'] ?? 0);
        $temporaryPath = trim((string) ($uploadedFileRecord['tmp_name'] ?? ''));
        // Trust server-side content inspection over the browser-supplied MIME value.
        $mimeType = $this->detectedMimeType(
            $temporaryPath,
            trim((string) ($uploadedFileRecord['type'] ?? ''))
        );
        $type = $this->attachmentType($extension, $mimeType);

        if ($type === '') {
            return ['attachment' => [], 'error' => 'Upload an image, video, zip, document, or code file.'];
        }

        if ($isImageOnly && $type !== 'image') {
            return ['attachment' => [], 'error' => 'Comments only support JPEG, PNG, GIF, or WebP images.'];
        }

        $maxSize = $this->attachmentMaxSize($type);

        if ($temporaryPath === '' || !is_uploaded_file($temporaryPath)) {
            return ['attachment' => [], 'error' => 'The attachment could not be checked. Please choose another file.'];
        }

        if ($fileSize <= 0) {
            return ['attachment' => [], 'error' => 'The attachment is empty. Please choose another file.'];
        }

        if ($fileSize > $maxSize) {
            return ['attachment' => [], 'error' => 'Attachment is too large. Images can be 10 MB, videos 50 MB, and documents or code 10 MB.'];
        }

        return [
            'attachment' => [
                'tmp_name' => $temporaryPath,
                'original_name' => $originalName,
                'extension' => $extension,
                'mime_type' => $mimeType,
                'file_size' => $fileSize,
                'type' => $type,
            ],
            'error' => '',
        ];
    }

    private function uploadErrorMessage(int $error)
    {
        if (in_array($error, [UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE], true)) {
            return 'The attachment is larger than the server allows.';
        }

        if ($error === UPLOAD_ERR_PARTIAL) {
            return 'The attachment only uploaded partially. Please try again.';
        }

        return 'The attachment could not be uploaded. Please choose another file.';
    }

    private function detectedMimeType(string $tmpName, string $fallback)
    {
        if ($tmpName !== '' && is_file($tmpName) && function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);

            if ($finfo !== false) {
                $mimeType = finfo_file($finfo, $tmpName);
                finfo_close($finfo);

                if (is_string($mimeType) && $mimeType !== '') {
                    return $mimeType;
                }
            }
        }

        return trim((string)$fallback) !== '' ? trim((string)$fallback) : 'application/octet-stream';
    }

    private function attachmentType(string $extension, string $mimeType)
    {
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $videoExtensions = ['mp4', 'webm', 'mov'];
        $documentExtensions = ['zip', 'txt', 'php', 'js', 'css', 'html', 'htm', 'json', 'xml', 'sql', 'py', 'java', 'c', 'cpp', 'cs', 'md', 'pdf', 'doc', 'docx',];

        if (in_array($extension, $imageExtensions, true) && str_starts_with($mimeType, 'image/')) {
            return 'image';
        }

        if (in_array($extension, $videoExtensions, true) && str_starts_with($mimeType, 'video/')) {
            return 'video';
        }

        if (in_array($extension, $documentExtensions, true)) {
            return 'document';
        }

        return '';
    }

    private function attachmentMaxSize(string $type)
    {
        if ($type === 'image') {
            return self::IMAGE_MAX_SIZE;
        }

        if ($type === 'video') {
            return self::VIDEO_MAX_SIZE;
        }

        return self::DOCUMENT_MAX_SIZE;
    }

    public function storeAttachment(array $attachment)
    {
        $type = trim((string)($attachment['type'] ?? 'document'));
        $folder = $type === 'image' ? 'images' : ($type === 'video' ? 'videos' : 'documents');
        $uploadDir = ROOT_PATH . '/public/uploads/' . $folder;

        if (!is_dir($uploadDir) && !mkdir($uploadDir, 0775, true)) {
            return null;
        }

        $extension = $this->storageExtension(trim((string)($attachment['extension'] ?? '')), $type);
        $fileName = date('YmdHis') . '-' . bin2hex(random_bytes(8)) . '.' . $extension;
        $targetPath = $uploadDir . '/' . $fileName;

        if (!move_uploaded_file(($attachment['tmp_name'] ?? ''), $targetPath)) {
            return null;
        }

        return ['type' => $type, 'path' => 'uploads/' . $folder . '/' . $fileName, 'original_name' => $attachment['original_name'] ?? $fileName, 'mime_type' => $attachment['mime_type'] ?? null, 'file_size' => ($attachment['file_size'] ?? 0),];
    }

    private function storageExtension(string $extension, string $type)
    {
        $codeExtensions = ['php', 'js', 'css', 'html', 'htm', 'json', 'xml', 'sql', 'py', 'java', 'c', 'cpp', 'cs', 'md'];

        // Appending .txt prevents uploaded source files from being executed by the web server.
        if ($type === 'document' && in_array($extension, $codeExtensions, true)) {
            return $extension . '.txt';
        }

        return $extension !== '' ? $extension : 'bin';
    }

    public function validatedAvatar(?array $file)
    {
        if (!is_array($file) || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return ['has_file' => false, 'error' => 'Please choose an avatar image.'];
        }

        $error = (int)($file['error'] ?? UPLOAD_ERR_OK);

        if ($error !== UPLOAD_ERR_OK) {
            return ['has_file' => true, 'error' => $this->uploadErrorMessage($error),];
        }

        $originalName = basename((string)($file['name'] ?? 'avatar'));
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        $tmpName = trim((string)($file['tmp_name'] ?? ''));
        $fileSize = $tmpName !== '' && is_file($tmpName) ? filesize($tmpName) : false;
        $size = is_int($fileSize) ? $fileSize : 0;
        $mimeType = $this->detectedMimeType($tmpName, '');
        $allowedMimeTypes = ['jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png', 'gif' => 'image/gif', 'webp' => 'image/webp',];

        if (!isset($allowedMimeTypes[$extension]) || $allowedMimeTypes[$extension] !== $mimeType) {
            return ['has_file' => true, 'error' => 'Upload a JPG, JPEG, PNG, GIF, or WebP image.',];
        }

        if ($tmpName === '' || !is_uploaded_file($tmpName) || @getimagesize($tmpName) === false) {
            return ['has_file' => true, 'error' => 'The selected file is not a valid image.',];
        }

        if ($size <= 0) {
            return ['has_file' => true, 'error' => 'The avatar image is empty. Please choose another file.',];
        }

        if ($size > self::AVATAR_MAX_SIZE) {
            return ['has_file' => true, 'error' => 'The avatar image must be 2 MB or smaller.',];
        }

        return ['has_file' => true, 'error' => '', 'tmp_name' => $tmpName, 'extension' => $extension === 'jpeg' ? 'jpg' : $extension,];
    }

    public function storeAvatar(array $avatar)
    {
        $uploadDir = ROOT_PATH . '/public/uploads/avatars';

        if (!is_dir($uploadDir) && !mkdir($uploadDir, 0775, true)) {
            return null;
        }

        $extension = trim((string)($avatar['extension'] ?? ''));

        if (!in_array($extension, ['jpg', 'png', 'gif', 'webp'], true)) {
            return null;
        }

        $fileName = date('YmdHis') . '-' . bin2hex(random_bytes(8)) . '.' . $extension;
        $targetPath = $uploadDir . '/' . $fileName;

        if (!move_uploaded_file((string)($avatar['tmp_name'] ?? ''), $targetPath)) {
            return null;
        }

        return 'uploads/avatars/' . $fileName;
    }

    public function removeStoredAttachment(array $attachment): bool
    {
        $path = trim((string)($attachment['path'] ?? ''));

        if ($path === '') {
            return false;
        }

        $absolutePath = ROOT_PATH . '/public/' . ltrim($path, '/');
        $uploadsRoot = realpath(ROOT_PATH . '/public/uploads');
        $storedPath = realpath($absolutePath);

        if ($uploadsRoot === false) {
            return false;
        }

        if ($storedPath === false) {
            return !file_exists($absolutePath) && !is_link($absolutePath);
        }

        // Canonical paths ensure deletion can never escape the managed uploads directory.
        $uploadsPrefix = rtrim($uploadsRoot, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        if (!str_starts_with($storedPath, $uploadsPrefix) || !is_file($storedPath)) {
            return false;
        }

        return @unlink($storedPath);
    }

    public function removeStoredAttachments(array $attachments): array
    {
        $failedAttachments = [];

        foreach ($attachments as $attachment) {
            if (!is_array($attachment) || !$this->removeStoredAttachment($attachment)) {
                $failedAttachments[] = is_array($attachment) ? $attachment : [];
            }
        }

        return $failedAttachments;
    }

}
