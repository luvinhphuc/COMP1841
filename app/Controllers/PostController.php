<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Helpers\FormatHelper;
use App\Helpers\PermissionHelper;
use App\Repositories\MediaRepository;
use App\Repositories\ModuleRepository;
use App\Repositories\PostRepository;
use App\Services\AttachmentService;
use App\Services\DiscussionDeletionService;
use Throwable;

/**
 * Handles creation, editing, and owner-authorized deletion of discussions.
 */
class PostController extends Controller
{
    public function create()
    {
        if ($this->currentUserId() === null) {
            $this->redirectTo(BASE_URL . '/login');
        }

        $discussionErrors = $_SESSION['discussion_create_errors'] ?? [];
        $discussionOld = $_SESSION['discussion_create_old'] ?? [];

        try {
            $modules = (new ModuleRepository())->findAll();
        } catch (Throwable) {
            $modules = [];
            $discussionErrors['general'] = $discussionErrors['general']
                ?? 'Modules could not be loaded. Please try again.';
        }

        $this->view('discussions/create', [
            'modules' => $modules,
            'errors' => $discussionErrors,
            'old' => $discussionOld,
            'formAction' => BASE_URL . '/discussions/store',
            'formTitle' => 'Create discussion',
            'pageTitle' => 'Create discussion',
            'submitLabel' => 'Post',
            'cancelUrl' => BASE_URL . '/discussions',
            'hasImageViewer' => true,
            'pageScripts' => [
                'content-input.js',
                'attachment.js',
                'image-viewer.js',
            ],
        ]);

        unset($_SESSION['discussion_create_errors'], $_SESSION['discussion_create_old']);
    }

    public function store()
    {
        $this->requirePost(BASE_URL . '/discussions/create');

        $authUserId = $this->currentUserId();

        if ($authUserId === null) {
            $this->redirectTo(BASE_URL . '/login');
        }

        $attachmentService = new AttachmentService();
        $moduleId = filter_var($_POST['module_id'] ?? 0, FILTER_VALIDATE_INT);
        $postData = [
            'title' => trim((string)($_POST['title'] ?? '')),
            'module_id' => $moduleId === false ? 0 : $moduleId,
            'content' => trim((string)($_POST['content'] ?? '')),
            'user_id' => $authUserId,
        ];

        try {
            $attachmentValidationData = $attachmentService->validatedAttachments($_FILES['attachments'] ?? null);
            $attachments = $attachmentValidationData['attachments'];
            $moduleRepository = new ModuleRepository();
            $postRepository = new PostRepository();
            $mediaRepository = new MediaRepository();
            $postErrors = $this->validatePostData($postData, $moduleRepository, false);

            if ($attachmentValidationData['error'] !== '') {
                $postErrors['attachments'] = $attachmentValidationData['error'];
            }

            if (!empty($postErrors)) {
                $this->redirectCreateWithErrors($postErrors, $postData);
            }

            $database = Database::connect();
            $storedAttachments = [];

            // Treat the discussion row and all attachment metadata as one database unit.
            $database->beginTransaction();

            $postId = $postRepository->create($postData);

            if ($postId <= 0) {
                $database->rollBack();
                $this->redirectCreateWithErrors([
                    'general' => 'Unable to create this discussion. Please check the details and try again.',
                ], $postData);
            }

            foreach ($attachments as $attachment) {
                $storedAttachment = $attachmentService->storeAttachment($attachment);

                if ($storedAttachment === null) {
                    $database->rollBack();
                    $attachmentService->removeStoredAttachments($storedAttachments);
                    $this->redirectCreateWithErrors([
                        'attachments' => 'The attachments could not be saved. Please choose the files again.',
                    ], $postData);
                }

                $storedAttachment['post_id'] = $postId;
                $storedAttachments[] = $storedAttachment;

                if (!$mediaRepository->create($storedAttachment)) {
                    $database->rollBack();
                    $attachmentService->removeStoredAttachments($storedAttachments);
                    $this->redirectCreateWithErrors([
                        'attachments' => 'The attachments could not be saved. Please choose the files again.',
                    ], $postData);
                }
            }

            $database->commit();

            $postEntity = $postRepository->findById($postId);
            $slug = trim((string)($postEntity->slug ?? ''));

            $this->redirectTo(FormatHelper::discussionDetailUrl($postId, $slug));
        } catch (Throwable) {
            if (isset($database) && $database->inTransaction()) {
                $database->rollBack();
            }

            if (isset($storedAttachments) && is_array($storedAttachments)) {
                $attachmentService->removeStoredAttachments($storedAttachments);
            }

            $this->redirectCreateWithErrors([
                'general' => 'Unable to create this discussion right now. Please try again.',
            ], $postData);
        }
    }

    public function edit($postId = 0)
    {
        $postRecord = $this->loadPostRecordById((int)$postId);

        if ($postRecord === null) {
            $this->notFound();
        }

        if (!PermissionHelper::canEditDiscussion($this->currentUser(), $postRecord)) {
            $this->forbidden($this->discussionUrl($postRecord));
        }

        $this->redirectEditWithErrors($postRecord, [], []);
    }

    public function update($postId = 0)
    {
        $this->requirePost(BASE_URL . '/discussions');

        $postRecord = $this->loadPostRecordById((int)$postId);

        if ($postRecord === null) {
            $this->notFound();
        }

        if (!PermissionHelper::canEditDiscussion($this->currentUser(), $postRecord)) {
            $this->forbidden($this->discussionUrl($postRecord));
        }

        $moduleId = filter_var($_POST['module_id'] ?? 0, FILTER_VALIDATE_INT);
        $postData = [
            'title' => trim((string)($_POST['title'] ?? '')),
            'module_id' => $moduleId === false ? 0 : $moduleId,
            'content' => trim((string)($_POST['content'] ?? '')),
        ];

        try {
            $moduleRepository = new ModuleRepository();
            $postErrors = $this->validatePostData($postData, $moduleRepository);

            if (!empty($postErrors)) {
                $this->redirectEditWithErrors($postRecord, $postData, $postErrors);
            }

            if (!(new PostRepository())->update((int)($postRecord['id'] ?? 0), $postData)) {
                $this->redirectEditWithErrors($postRecord, $postData, [
                    'general' => 'Unable to update this discussion. Please try again.',
                ]);
            }
        } catch (Throwable) {
            $this->redirectEditWithErrors($postRecord, $postData, [
                'general' => 'Unable to update this discussion right now. Please try again.',
            ]);
        }

        $updatedPostRecord = $this->loadPostRecordById((int)($postRecord['id'] ?? 0)) ?? $postRecord;
        $updatedPostId = (int)($updatedPostRecord['id'] ?? $postRecord['id'] ?? 0);
        $slug = trim((string)($updatedPostRecord['slug'] ?? $postRecord['slug'] ?? ''));

        $this->redirectTo(FormatHelper::discussionDetailUrl($updatedPostId, $slug));
    }

    public function delete($postId = 0)
    {
        $postRecord = $this->loadPostRecordById((int)$postId);

        if ($postRecord === null) {
            $this->notFound();
        }

        if (!PermissionHelper::canEditDiscussion($this->currentUser(), $postRecord)) {
            $this->forbidden($this->discussionUrl($postRecord));
        }

        $this->redirectModal(
            (int)($postRecord['id'] ?? 0),
            'discussion-delete-modal',
            $this->discussionUrl($postRecord)
        );
    }

    public function destroy($postId = 0)
    {
        $this->requirePost(BASE_URL . '/discussions');

        $postRecord = $this->loadPostRecordById((int)$postId);

        if ($postRecord === null) {
            $this->notFound();
        }

        if (!PermissionHelper::canEditDiscussion($this->currentUser(), $postRecord)) {
            $this->forbidden($this->discussionUrl($postRecord));
        }

        try {
            $postId = (int)($postRecord['id'] ?? 0);
            $deletionResult = (new DiscussionDeletionService())->delete($postId);

            if (!$deletionResult['deleted']) {
                $this->redirectWithToast($this->discussionUrl($postRecord), [
                    'type' => 'error',
                    'title' => 'Unable to delete discussion',
                    'message' => 'The discussion could not be deleted. Please try again.',
                ]);
            }

            if (!$deletionResult['cleanup_complete']) {
                $this->redirectWithToast(BASE_URL . '/discussions', [
                    'type' => 'warning',
                    'title' => 'Attachment cleanup incomplete',
                    'message' => 'The discussion was deleted, but some attachment files could not be removed.',
                ]);
            }
        } catch (Throwable) {
            $this->redirectTo(FormatHelper::discussionDetailUrl(
                $postRecord['id'] ?? 0,
                $postRecord['slug'] ?? ''
            ));
        }

        $this->redirectTo(BASE_URL . '/discussions');
    }

    private function validatePostData(
        array $postData,
        ModuleRepository $moduleRepository,
        bool $contentRequired = true
    ): array
    {
        $postErrors = [];
        $title = trim((string)($postData['title'] ?? ''));
        $content = trim((string)($postData['content'] ?? ''));
        $moduleId = (int)($postData['module_id'] ?? 0);

        if ($title === '') {
            $postErrors['title'] = 'Please enter a discussion title.';
        } elseif (FormatHelper::textLength($title) > 255) {
            $postErrors['title'] = 'Title must be 255 characters or fewer.';
        }

        if ($contentRequired && $content === '') {
            $postErrors['content'] = 'Please enter a discussion body.';
        } elseif ($content !== '' && FormatHelper::textLength($content) > 5000) {
            $postErrors['content'] = 'Discussion body must be 5000 characters or fewer.';
        }

        if ($moduleId <= 0) {
            $postErrors['module_id'] = 'Please choose a module.';
        } else {
            try {
                if (!$moduleRepository->existsById($moduleId)) {
                    $postErrors['module_id'] = 'Please choose an available module.';
                }
            } catch (Throwable) {
                $postErrors['module_id'] = 'Module could not be checked. Please try again.';
            }
        }

        return $postErrors;
    }

    private function redirectCreateWithErrors(array $postErrors, array $postData = [])
    {
        unset($postData['user_id']);

        $_SESSION['discussion_create_errors'] = $postErrors;
        $_SESSION['discussion_create_old'] = $postData;

        $this->redirectTo(BASE_URL . '/discussions/create');
    }

    private function redirectEditWithErrors(array $postRecord, array $postData, array $postErrors)
    {
        $_SESSION['discussion_edit_state'] = [
            'post_id' => (int)($postRecord['id'] ?? 0),
            'old' => $postData,
            'errors' => $postErrors,
        ];

        $this->redirectTo($this->discussionUrl($postRecord) . '#question-content-heading');
    }

    private function redirectModal(int $postId, string $modalId, string $redirectUrl)
    {
        $_SESSION['discussion_modal_state'] = [
            'post_id' => $postId,
            'modal_id' => $modalId,
        ];

        $this->redirectTo($redirectUrl);
    }

    private function loadPostRecordById(int $postId): ?array
    {
        if ($postId <= 0) {
            return null;
        }

        try {
            $postEntity = (new PostRepository())->findById($postId);

            return $postEntity?->toArray();
        } catch (Throwable) {
            return null;
        }
    }

    private function discussionUrl(array $postRecord): string
    {
        return FormatHelper::discussionDetailUrl($postRecord['id'] ?? 0, $postRecord['slug'] ?? '');
    }
}
