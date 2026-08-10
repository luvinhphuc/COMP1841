<?php

namespace App\Services;

use App\Repositories\MediaRepository;
use App\Repositories\PostRepository;

/**
 * Coordinates database deletion with best-effort cleanup of stored attachments.
 */
class DiscussionDeletionService
{
    private MediaRepository $mediaRepository;
    private PostRepository $postRepository;
    private AttachmentService $attachmentService;

    public function __construct(
        ?MediaRepository $mediaRepository = null,
        ?PostRepository $postRepository = null,
        ?AttachmentService $attachmentService = null
    ) {
        $this->mediaRepository = $mediaRepository ?? new MediaRepository();
        $this->postRepository = $postRepository ?? new PostRepository();
        $this->attachmentService = $attachmentService ?? new AttachmentService();
    }

    public function delete(int $postId): array
    {
        if ($postId <= 0) {
            return [
                'deleted' => false,
                'cleanup_complete' => true,
            ];
        }

        // Read paths before cascading database deletes remove their metadata.
        $mediaRecords = $this->mediaRepository->findAllByPostId($postId);

        if (!$this->postRepository->delete($postId)) {
            return [
                'deleted' => false,
                'cleanup_complete' => true,
            ];
        }

        // File cleanup is best-effort: the database result remains authoritative.
        $failedAttachments = $this->attachmentService->removeStoredAttachments($mediaRecords);

        foreach ($failedAttachments as $failedAttachment) {
            error_log(sprintf(
                'Discussion attachment cleanup failed for post %d: %s',
                $postId,
                trim((string)($failedAttachment['path'] ?? 'unknown path'))
            ));
        }

        return [
            'deleted' => true,
            'cleanup_complete' => $failedAttachments === [],
        ];
    }
}
