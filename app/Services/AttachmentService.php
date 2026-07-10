<?php

namespace App\Services;

class AttachmentService
{
    private const IMAGE_MAX_SIZE = 10485760;
    private const VIDEO_MAX_SIZE = 52428800;
    private const DOCUMENT_MAX_SIZE = 10485760;

    public function validatedAttachment(?array $file)
    {
        if (!is_array($file) || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return ['has_file' => false, 'error' => ''];
        }

        $error = ($file['error'] ?? UPLOAD_ERR_OK);

        if ($error !== UPLOAD_ERR_OK) {
            return [
                'has_file' => true,
                'error' => $this->uploadErrorMessage($error),
            ];
        }

        $originalName = basename((string) ($file['name'] ?? 'attachment'));
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        $size = ($file['size'] ?? 0);
        $mimeType = $this->detectedMimeType(
            trim((string) ($file['tmp_name'] ?? '')),
            trim((string) ($file['type'] ?? ''))
        );
        $type = $this->attachmentType($extension, $mimeType);

        if ($type === '') {
            return [
                'has_file' => true,
                'error' => 'Upload an image, video, zip, document, or code file.',
            ];
        }

        $maxSize = $this->attachmentMaxSize($type);

        if ($size <= 0) {
            return [
                'has_file' => true,
                'error' => 'The attachment is empty. Please choose another file.',
            ];
        }

        if ($size > $maxSize) {
            return [
                'has_file' => true,
                'error' => 'Attachment is too large. Images can be 5 MB, videos 50 MB, and documents or code 10 MB.',
            ];
        }

        return [
            'has_file' => true,
            'error' => '',
            'tmp_name' => trim((string) ($file['tmp_name'] ?? '')),
            'original_name' => $originalName,
            'extension' => $extension,
            'mime_type' => $mimeType,
            'file_size' => $size,
            'type' => $type,
        ];
    }

    public function storeAttachment(array $attachment)
    {
        $type = trim((string) ($attachment['type'] ?? 'document'));
        $folder = $type === 'image' ? 'images' : ($type === 'video' ? 'videos' : 'documents');
        $uploadDir = ROOT_PATH . '/public/uploads/' . $folder;

        if (!is_dir($uploadDir) && !mkdir($uploadDir, 0775, true)) {
            return null;
        }

        $extension = $this->storageExtension(
            trim((string) ($attachment['extension'] ?? '')),
            $type
        );
        $fileName = date('YmdHis') . '-' . bin2hex(random_bytes(8)) . '.' . $extension;
        $targetPath = $uploadDir . '/' . $fileName;

        if (!move_uploaded_file(($attachment['tmp_name'] ?? ''), $targetPath)) {
            return null;
        }

        return [
            'type' => $type,
            'path' => 'uploads/' . $folder . '/' . $fileName,
            'original_name' => $attachment['original_name'] ?? $fileName,
            'mime_type' => $attachment['mime_type'] ?? null,
            'file_size' => ($attachment['file_size'] ?? 0),
        ];
    }

    public function removeStoredAttachment(array $attachment)
    {
        $path = trim((string) ($attachment['path'] ?? ''));

        if ($path === '') {
            return;
        }

        $absolutePath = ROOT_PATH . '/public/' . ltrim($path, '/');
        $uploadsRoot = realpath(ROOT_PATH . '/public/uploads');
        $storedPath = realpath($absolutePath);

        if ($uploadsRoot === false || $storedPath === false) {
            return;
        }

        if (str_starts_with($storedPath, $uploadsRoot) && is_file($storedPath)) {
            unlink($storedPath);
        }
    }

    private function attachmentType(string $extension, string $mimeType)
    {
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $videoExtensions = ['mp4', 'webm', 'mov'];
        $documentExtensions = [
            'zip',
            'txt',
            'php',
            'js',
            'css',
            'html',
            'htm',
            'json',
            'xml',
            'sql',
            'py',
            'java',
            'c',
            'cpp',
            'cs',
            'md',
            'pdf',
            'doc',
            'docx',
        ];

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

    private function storageExtension(string $extension, string $type)
    {
        $codeExtensions = ['php', 'js', 'css', 'html', 'htm', 'json', 'xml', 'sql', 'py', 'java', 'c', 'cpp', 'cs', 'md'];

        if ($type === 'document' && in_array($extension, $codeExtensions, true)) {
            return $extension . '.txt';
        }

        return $extension !== '' ? $extension : 'bin';
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

        return trim((string) $fallback) !== '' ? trim((string) $fallback) : 'application/octet-stream';
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

}
