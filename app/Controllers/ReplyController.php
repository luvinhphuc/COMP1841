<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Helpers\FormatHelper;
use App\Helpers\PermissionHelper;
use App\Repositories\MediaRepository;
use App\Repositories\ReplyRepository;
use App\Services\AttachmentService;
use Throwable;

/**
 * Handles nested reply mutations and accepted-solution transitions.
 */
class ReplyController extends Controller
{
    // Route actions --------------------------------------------------------
    public function store()
    {
        $this->requirePost(BASE_URL . '/discussions');

        $discussionId = filter_var($_POST['post_id'] ?? 0, FILTER_VALIDATE_INT);
        $parentReplyId = filter_var($_POST['parent_reply_id'] ?? 0, FILTER_VALIDATE_INT);
        $discussionId = $discussionId === false ? 0 : $discussionId;
        $parentReplyId = $parentReplyId === false ? 0 : $parentReplyId;
        $slug = trim((string) ($_POST['slug'] ?? ''));
        $redirectUrl = $this->replyRedirectUrl($slug, $discussionId);

        $authUserId = $this->currentUserId();
        $content = trim((string) ($_POST['content'] ?? ''));

        if ($authUserId === null) {
            $this->redirectTo(BASE_URL . '/login');
        }

        $attachmentService = new AttachmentService();
        $replyErrors = $this->validateReply($content);

        try {
            $attachmentValidationData = $attachmentService->validatedAttachments(
                $_FILES['attachments'] ?? null,
                true
            );
            $attachments = $attachmentValidationData['attachments'];
        } catch (Throwable) {
            $attachmentValidationData = [
                'attachments' => [],
                'error' => 'The image could not be checked. Please choose another image.',
            ];
            $attachments = [];
        }

        if ($attachmentValidationData['error'] !== '') {
            $replyErrors['attachments'] = $attachmentValidationData['error'];
        }

        if ($discussionId <= 0) {
            $replyErrors['general'] = 'This discussion could not be found. Please reopen it and try again.';
        }

        if (!empty($replyErrors)) {
            $this->redirectReplyWithErrors($discussionId, $content, $replyErrors, $redirectUrl);
        }

        try {
            $db = Database::connect();
            $storedAttachments = [];
            $mediaRepository = new MediaRepository();

            // Keep reply creation and attachment metadata atomic; stored files are cleaned on failure.
            $db->beginTransaction();

            $replyId = (new ReplyRepository())->create([
                'post_id' => $discussionId,
                'parent_reply_id' => $parentReplyId > 0 ? $parentReplyId : null,
                'user_id' => $authUserId,
                'content' => $content,
            ]);

            if ($replyId <= 0) {
                $db->rollBack();
                $this->redirectReplyWithErrors($discussionId, $content, [
                    'general' => 'Unable to post your reply. Please try again.',
                ], $redirectUrl);
            }

            foreach ($attachments as $attachment) {
                $storedAttachment = $attachmentService->storeAttachment($attachment);

                if ($storedAttachment === null) {
                    $db->rollBack();
                    $attachmentService->removeStoredAttachments($storedAttachments);
                    $this->redirectReplyWithErrors($discussionId, $content, [
                        'attachments' => 'The images could not be saved. Please choose them again.',
                    ], $redirectUrl);
                }

                $storedAttachment['reply_id'] = $replyId;
                $storedAttachments[] = $storedAttachment;

                if (!$mediaRepository->create($storedAttachment)) {
                    $db->rollBack();
                    $attachmentService->removeStoredAttachments($storedAttachments);
                    $this->redirectReplyWithErrors($discussionId, $content, [
                        'attachments' => 'The images could not be saved. Please choose them again.',
                    ], $redirectUrl);
                }
            }

            $db->commit();
        } catch (Throwable) {
            if (isset($db) && $db->inTransaction()) {
                $db->rollBack();
            }

            if (isset($storedAttachments) && is_array($storedAttachments)) {
                $attachmentService->removeStoredAttachments($storedAttachments);
            }

            $this->redirectReplyWithErrors($discussionId, $content, [
                'general' => 'Unable to post your reply right now. Please try again.',
            ], $redirectUrl);
        }

        $this->redirectTo($redirectUrl);
    }

    public function edit($id = 0)
    {
        $reply = $this->findReplyById($id);

        if ($reply === null) {
            $this->notFound();
        }

        if (!$this->canEditReply($reply)) {
            $this->forbidden($this->discussionUrlFromReply($reply));
        }

        $this->redirectReplyEditWithErrors($reply, (string) ($reply['content'] ?? ''), []);
    }

    public function update($id = 0)
    {
        $this->requirePost(BASE_URL . '/discussions');

        $reply = $this->findReplyById($id);

        if ($reply === null) {
            $this->notFound();
        }

        if (!$this->canEditReply($reply)) {
            $this->forbidden($this->discussionUrlFromReply($reply));
        }

        $content = trim((string) ($_POST['content'] ?? ''));
        $errors = $this->validateReply($content);

        if (!empty($errors)) {
            $this->redirectReplyEditWithErrors($reply, $content, $errors);
        }

        try {
            if (!(new ReplyRepository())->update((int) ($reply['id'] ?? 0), $content)) {
                $this->redirectReplyEditWithErrors($reply, $content, [
                    'general' => 'Unable to update this reply. Please try again.',
                ]);
            }
        } catch (Throwable) {
            $this->redirectReplyEditWithErrors($reply, $content, [
                'general' => 'Unable to update this reply right now. Please try again.',
            ]);
        }

        $this->redirectTo($this->discussionUrlFromReply($reply) . '#reply-' . (int) ($reply['id'] ?? 0));
    }

    public function delete($id = 0)
    {
        $reply = $this->findReplyById($id);

        if ($reply === null) {
            $this->notFound();
        }

        if (!$this->canDeleteReply($reply)) {
            $this->forbidden($this->discussionUrlFromReply($reply));
        }

        $this->redirectModal(
            (int) ($reply['post_id'] ?? 0),
            'reply-delete-modal-' . (int) ($reply['id'] ?? 0),
            $this->discussionUrlFromReply($reply) . '#reply-' . (int) ($reply['id'] ?? 0)
        );
    }

    public function destroy($id = 0)
    {
        $this->requirePost(BASE_URL . '/discussions');

        $reply = $this->findReplyById($id);

        if ($reply === null) {
            $this->notFound();
        }

        if (!$this->canDeleteReply($reply)) {
            $this->forbidden($this->discussionUrlFromReply($reply));
        }

        try {
            $replyId = (int) ($reply['id'] ?? 0);
            $mediaRecords = (new MediaRepository())->findByReplyId($replyId);

            if ((new ReplyRepository())->delete($replyId)) {
                (new AttachmentService())->removeStoredAttachments($mediaRecords);
            }
        } catch (Throwable) {
            $this->redirectTo($this->discussionUrlFromReply($reply));
        }

        $this->redirectTo($this->discussionUrlFromReply($reply) . '#replies');
    }

    // Solution actions
    public function markAsSolved($id = 0)
    {
        $this->requirePost(BASE_URL . '/discussions');

        $reply = $this->findReplyById($id);

        if ($reply === null) {
            $this->notFound();
        }

        if (!$this->canMarkDiscussionAsSolved($reply)) {
            $this->forbidden($this->discussionUrlFromReply($reply));
        }

        $discussionUrl = $this->discussionUrlFromReply($reply);

        if ((int) ($reply['is_accepted'] ?? 0) === 1
            && strtolower(trim((string) ($reply['post_status'] ?? ''))) === 'solved') {
            $this->redirectWithToast($discussionUrl . '#reply-' . (int) $reply['id'], [
                'type' => 'info',
                'title' => 'Already solved',
                'message' => 'This comment is already marked as the solved answer.',
            ]);
        }

        try {
            if (!(new ReplyRepository())->markAsSolved((int) ($reply['id'] ?? 0))) {
                throw new \RuntimeException('The reply could not be marked as solved.');
            }
        } catch (Throwable) {
            $this->redirectWithToast($discussionUrl . '#reply-' . (int) $reply['id'], [
                'type' => 'error',
                'title' => 'Unable to mark as solved',
                'message' => 'Please try again.',
            ]);
        }

        $this->redirectWithToast($discussionUrl . '#reply-' . (int) $reply['id'], [
            'type' => 'success',
            'title' => 'Comment marked as solved',
            'message' => 'This comment is now the solved answer for the discussion.',
        ]);
    }

    public function unmarkAsSolved($id = 0)
    {
        $this->requirePost(BASE_URL . '/discussions');

        $reply = $this->findReplyById($id);

        if ($reply === null) {
            $this->notFound();
        }

        if (!$this->canMarkDiscussionAsSolved($reply)) {
            $this->forbidden($this->discussionUrlFromReply($reply));
        }

        $discussionUrl = $this->discussionUrlFromReply($reply);

        if ((int) ($reply['is_accepted'] ?? 0) !== 1) {
            $this->redirectWithToast($discussionUrl . '#reply-' . (int) $reply['id'], [
                'type' => 'info',
                'title' => 'Already open',
                'message' => 'This comment is not currently marked as the solved answer.',
            ]);
        }

        try {
            if (!(new ReplyRepository())->unmarkAsSolved((int) ($reply['id'] ?? 0))) {
                throw new \RuntimeException('The solved reply could not be removed.');
            }
        } catch (Throwable) {
            $this->redirectWithToast($discussionUrl . '#reply-' . (int) $reply['id'], [
                'type' => 'error',
                'title' => 'Unable to reopen discussion',
                'message' => 'Please try again.',
            ]);
        }

        $this->redirectWithToast($discussionUrl . '#reply-' . (int) $reply['id'], [
            'type' => 'success',
            'title' => 'Discussion reopened',
            'message' => 'The solved answer was removed and the discussion is open again.',
        ]);
    }

    // Validation
    private function validateReply(string $content)
    {
        $errors = [];

        if ($content === '') {
            $errors['content'] = 'Please write a reply before posting.';
        } elseif (FormatHelper::textLength($content) > 5000) {
            $errors['content'] = 'Reply must be 5000 characters or fewer.';
        }

        return $errors;
    }

    // Redirects and state
    private function redirectReplyWithErrors(int $discussionId, string $content, array $errors, string $redirectUrl)
    {
        $_SESSION['discussion_reply_state'] = [
            'post_id' => $discussionId,
            'old' => ['content' => $content],
            'errors' => $errors,
        ];

        $this->redirectTo($redirectUrl);
    }

    private function redirectReplyEditWithErrors(array $reply, string $content, array $errors)
    {
        $_SESSION['discussion_reply_edit_state'] = [
            'post_id' => (int) ($reply['post_id'] ?? 0),
            'reply_id' => (int) ($reply['id'] ?? 0),
            'old' => ['content' => $content],
            'errors' => $errors,
        ];

        $this->redirectTo($this->discussionUrlFromReply($reply) . '#reply-' . (int) ($reply['id'] ?? 0));
    }

    private function redirectModal(int $discussionId, string $modalId, string $redirectUrl)
    {
        $_SESSION['discussion_modal_state'] = [
            'post_id' => $discussionId,
            'modal_id' => $modalId,
        ];

        $this->redirectTo($redirectUrl);
    }

    private function replyRedirectUrl(string $slug, int $discussionId)
    {
        if ($slug === '' && $discussionId <= 0) {
            return BASE_URL . '/discussions';
        }

        return FormatHelper::discussionDetailUrl($discussionId, $slug) . '#reply-editor';
    }

    // Lookups and authorization
    private function findReplyById(int $id)
    {
        if ($id <= 0) {
            return null;
        }

        try {
            return (new ReplyRepository())->findDetailsById($id);
        } catch (Throwable) {
            return null;
        }
    }

    private function canEditReply(array $reply)
    {
        return PermissionHelper::canEditReply($this->currentUser(), $reply);
    }

    private function canDeleteReply(array $reply)
    {
        return PermissionHelper::canDeleteReply($this->currentUser(), $reply);
    }

    private function canMarkDiscussionAsSolved(array $reply)
    {
        return PermissionHelper::canMarkDiscussionAsSolved($this->currentUser(), [
            'user_id' => $reply['post_user_id'] ?? 0,
        ]);
    }

    private function discussionUrlFromReply(array $reply)
    {
        return FormatHelper::discussionDetailUrl(
            $reply['post_id'] ?? 0,
            $reply['post_slug'] ?? ''
        );
    }
}
