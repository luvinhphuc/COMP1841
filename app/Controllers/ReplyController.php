<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Helpers\PermissionHelper;
use App\Models\Media;
use App\Models\Reply;
use App\Services\AttachmentService;
use Throwable;

class ReplyController extends Controller
{
    public function store()
    {
        $this->requirePost(BASE_URL . '/discussions');

        $postId = filter_var($_POST['post_id'] ?? 0, FILTER_VALIDATE_INT);
        $parentReplyId = filter_var($_POST['parent_reply_id'] ?? 0, FILTER_VALIDATE_INT);
        $postId = $postId === false ? 0 : $postId;
        $parentReplyId = $parentReplyId === false ? 0 : $parentReplyId;
        $slug = trim((string) ($_POST['slug'] ?? ''));
        $redirectUrl = $this->replyRedirectUrl($slug, $postId);

        if (!$this->verifyCsrfToken($_POST['_csrf_token'] ?? null)) {
            $this->forbidden($redirectUrl);
        }

        $userId = $this->currentUserId();
        $content = trim((string) ($_POST['content'] ?? ''));

        if ($userId === null) {
            $this->redirectTo(BASE_URL . '/login');
        }

        $attachmentService = new AttachmentService();
        $errors = $this->validateReply($content);

        try {
            $attachment = $attachmentService->validatedAttachment($_FILES['attachment'] ?? null);
        } catch (Throwable) {
            $attachment = [
                'has_file' => true,
                'error' => 'The image could not be checked. Please choose another image.',
            ];
        }

        if (($attachment['error'] ?? '') !== '') {
            $errors['attachment'] = $attachment['error'];
        } elseif (!empty($attachment['has_file']) && ($attachment['type'] ?? '') !== 'image') {
            $errors['attachment'] = 'Comments only support JPEG, PNG, GIF, or WebP images.';
        }

        if ($postId <= 0) {
            $errors['general'] = 'This discussion could not be found. Please reopen it and try again.';
        }

        if (!empty($errors)) {
            $this->redirectReplyWithErrors($postId, $content, $errors, $redirectUrl);
        }

        try {
            $db = Database::connect();
            $storedAttachment = null;

            $db->beginTransaction();

            $replyId = (new Reply())->create([
                'post_id' => $postId,
                'parent_reply_id' => $parentReplyId > 0 ? $parentReplyId : null,
                'user_id' => $userId,
                'content' => $content,
            ]);

            if ($replyId <= 0) {
                $db->rollBack();
                $this->redirectReplyWithErrors($postId, $content, [
                    'general' => 'Unable to post your reply. Please try again.',
                ], $redirectUrl);
            }

            if (!empty($attachment['has_file'])) {
                $storedAttachment = $attachmentService->storeAttachment($attachment);

                if ($storedAttachment === null) {
                    $db->rollBack();
                    $this->redirectReplyWithErrors($postId, $content, [
                        'attachment' => 'The image could not be saved. Please choose another image.',
                    ], $redirectUrl);
                }

                $storedAttachment['reply_id'] = $replyId;

                if (!(new Media())->create($storedAttachment)) {
                    $attachmentService->removeStoredAttachment($storedAttachment);
                    $db->rollBack();
                    $this->redirectReplyWithErrors($postId, $content, [
                        'attachment' => 'The image could not be saved. Please choose another image.',
                    ], $redirectUrl);
                }
            }

            $db->commit();
        } catch (Throwable) {
            if (isset($db) && $db->inTransaction()) {
                $db->rollBack();
            }

            if (isset($storedAttachment) && is_array($storedAttachment)) {
                $attachmentService->removeStoredAttachment($storedAttachment);
            }

            $this->redirectReplyWithErrors($postId, $content, [
                'general' => 'Unable to post your reply right now. Please try again.',
            ], $redirectUrl);
        }

        header('Location: ' . $redirectUrl);
        exit;
    }

    public function edit($id = 0)
    {
        $reply = $this->findReplyById($id);

        if ($reply === null) {
            $this->notFound();
        }

        if (!$this->canEditReply($reply)) {
            $this->forbidden($this->postUrlFromReply($reply));
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

        if (!$this->canEditReply($reply) || !$this->verifyCsrfToken($_POST['_csrf_token'] ?? null)) {
            $this->forbidden($this->postUrlFromReply($reply));
        }

        $content = trim((string) ($_POST['content'] ?? ''));
        $errors = $this->validateReply($content);

        if (!empty($errors)) {
            $this->redirectReplyEditWithErrors($reply, $content, $errors);
        }

        try {
            if (!(new Reply())->update((int) ($reply['id'] ?? 0), $content)) {
                $this->redirectReplyEditWithErrors($reply, $content, [
                    'general' => 'Unable to update this reply. Please try again.',
                ]);
            }
        } catch (Throwable) {
            $this->redirectReplyEditWithErrors($reply, $content, [
                'general' => 'Unable to update this reply right now. Please try again.',
            ]);
        }

        header('Location: ' . $this->postUrlFromReply($reply) . '#reply-' . (int) ($reply['id'] ?? 0));
        exit;
    }

    public function delete($id = 0)
    {
        $reply = $this->findReplyById($id);

        if ($reply === null) {
            $this->notFound();
        }

        if (!$this->canDeleteReply($reply)) {
            $this->forbidden($this->postUrlFromReply($reply));
        }

        $this->redirectModal(
            (int) ($reply['post_id'] ?? 0),
            'reply-delete-modal-' . (int) ($reply['id'] ?? 0),
            $this->postUrlFromReply($reply) . '#reply-' . (int) ($reply['id'] ?? 0)
        );
    }

    public function destroy($id = 0)
    {
        $this->requirePost(BASE_URL . '/discussions');

        $reply = $this->findReplyById($id);

        if ($reply === null) {
            $this->notFound();
        }

        if (!$this->canDeleteReply($reply) || !$this->verifyCsrfToken($_POST['_csrf_token'] ?? null)) {
            $this->forbidden($this->postUrlFromReply($reply));
        }

        try {
            $replyId = (int) ($reply['id'] ?? 0);
            $attachments = (new Media())->getByReplyId($replyId);

            if ((new Reply())->delete($replyId)) {
                $attachmentService = new AttachmentService();

                foreach ($attachments as $attachment) {
                    $attachmentService->removeStoredAttachment($attachment);
                }
            }
        } catch (Throwable) {
            header('Location: ' . $this->postUrlFromReply($reply));
            exit;
        }

        header('Location: ' . $this->postUrlFromReply($reply) . '#replies');
        exit;
    }

    private function validateReply(string $content)
    {
        $errors = [];

        if ($content === '') {
            $errors['content'] = 'Please write a reply before posting.';
        } elseif ($this->textLength($content) > 5000) {
            $errors['content'] = 'Reply must be 5000 characters or fewer.';
        }

        return $errors;
    }

    private function redirectReplyWithErrors(int $postId, string $content, array $errors, string $redirectUrl)
    {
        $_SESSION['discussion_reply_state'] = [
            'post_id' => $postId,
            'old' => ['content' => $content],
            'errors' => $errors,
        ];

        header('Location: ' . $redirectUrl);
        exit;
    }

    private function redirectReplyEditWithErrors(array $reply, string $content, array $errors)
    {
        $_SESSION['discussion_reply_edit_state'] = [
            'post_id' => (int) ($reply['post_id'] ?? 0),
            'reply_id' => (int) ($reply['id'] ?? 0),
            'old' => ['content' => $content],
            'errors' => $errors,
        ];

        header('Location: ' . $this->postUrlFromReply($reply) . '#reply-' . (int) ($reply['id'] ?? 0));
        exit;
    }

    private function redirectModal(int $postId, string $modalId, string $redirectUrl)
    {
        $_SESSION['discussion_modal_state'] = [
            'post_id' => $postId,
            'modal_id' => $modalId,
        ];

        header('Location: ' . $redirectUrl);
        exit;
    }

    private function replyRedirectUrl(string $slug, int $postId)
    {
        if ($slug === '' && $postId <= 0) {
            return BASE_URL . '/discussions';
        }

        $target = $slug !== '' ? $slug : $postId;

        return BASE_URL . '/discussions/' . rawurlencode($target) . '#reply-editor';
    }

    private function findReplyById(int $id)
    {
        if ($id <= 0) {
            return null;
        }

        try {
            return (new Reply())->find($id);
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

    private function postUrlFromReply(array $reply)
    {
        $target = trim((string) ($reply['post_slug'] ?? ''));

        if ($target === '') {
            $target = (string) ($reply['post_id'] ?? '');
        }

        return BASE_URL . '/discussions/' . rawurlencode($target);
    }

    private function forbidden(string $redirectUrl = '')
    {
        $this->redirectWithToast($redirectUrl !== '' ? $redirectUrl : BASE_URL . '/discussions', [
            'type' => 'error',
            'title' => 'Permission denied',
            'message' => 'Only owners and admins can make this change.',
        ]);
    }

    private function textLength(string $value)
    {
        if (function_exists('mb_strlen')) {
            return mb_strlen($value);
        }

        return strlen($value);
    }
}
