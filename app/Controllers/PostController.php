<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Helpers\FormatHelper;
use App\Helpers\PermissionHelper;
use App\Models\Media;
use App\Models\Module;
use App\Models\Post;
use App\Services\AttachmentService;
use Throwable;

class PostController extends Controller
{
    public function create()
    {
        if ($this->currentUserId() === null) {
            $this->redirectTo(BASE_URL . '/login');
        }

        $errors = $_SESSION['discussion_create_errors'] ?? [];
        $old = $_SESSION['discussion_create_old'] ?? [];

        try {
            $modules = (new Module())->getAll();
        } catch (Throwable) {
            $modules = [];
            $errors['general'] = $errors['general'] ?? 'Modules could not be loaded. Please try again.';
        }

        $this->view('discussions/create', [
            'modules' => $modules,
            'errors' => $errors,
            'old' => $old,
            'formAction' => BASE_URL . '/discussions/store',
            'formTitle' => 'Create post',
            'submitLabel' => 'Post',
            'cancelUrl' => BASE_URL . '/discussions',
            'showAttachmentField' => true,
            'pageScripts' => ['content-input.js'],
        ]);

        unset($_SESSION['discussion_create_errors'], $_SESSION['discussion_create_old']);
    }

    public function store()
    {
        $this->requirePost(BASE_URL . '/discussions/create');

        if (!$this->verifyCsrfToken($_POST['_csrf_token'] ?? null)) {
            $this->forbidden();
        }

        $userId = $this->currentUserId();

        if ($userId === null) {
            $this->redirectTo(BASE_URL . '/login');
        }

        $attachmentService = new AttachmentService();
        $moduleId = filter_var($_POST['module_id'] ?? 0, FILTER_VALIDATE_INT);
        $data = [
            'title' => trim((string) ($_POST['title'] ?? '')),
            'module_id' => $moduleId === false ? 0 : $moduleId,
            'content' => trim((string) ($_POST['content'] ?? '')),
            'user_id' => $userId,
            'status' => 'open',
        ];

        try {
            $attachment = $attachmentService->validatedAttachment($_FILES['attachment'] ?? null);
            $moduleModel = new Module();
            $postModel = new Post();
            $errors = $this->validateDiscussionCreate($data, $moduleModel);

            if (($attachment['error'] ?? '') !== '') {
                $errors['attachment'] = $attachment['error'];
            }

            if (!empty($errors)) {
                $this->redirectCreateWithErrors($errors, $data);
            }

            $db = Database::connect();
            $storedAttachment = null;

            $db->beginTransaction();

            $postId = $postModel->create($data);

            if ($postId <= 0) {
                $db->rollBack();
                $this->redirectCreateWithErrors([
                    'general' => 'Unable to create this discussion. Please check the details and try again.',
                ], $data);
            }

            if (!empty($attachment['has_file'])) {
                $storedAttachment = $attachmentService->storeAttachment($attachment);

                if ($storedAttachment === null) {
                    $db->rollBack();
                    $this->redirectCreateWithErrors([
                        'attachment' => 'The attachment could not be saved. Please choose another file.',
                    ], $data);
                }

                $storedAttachment['post_id'] = $postId;

                if (!(new Media())->create($storedAttachment)) {
                    $attachmentService->removeStoredAttachment($storedAttachment);
                    $db->rollBack();
                    $this->redirectCreateWithErrors([
                        'attachment' => 'The attachment could not be saved. Please choose another file.',
                    ], $data);
                }
            }

            $db->commit();

            $post = $postModel->find($postId);
            $slug = trim((string) ($post['slug'] ?? ''));

            $this->redirectTo(FormatHelper::discussionDetailUrl($postId, $slug));
        } catch (Throwable) {
            if (isset($db) && $db->inTransaction()) {
                $db->rollBack();
            }

            if (isset($storedAttachment) && is_array($storedAttachment)) {
                $attachmentService->removeStoredAttachment($storedAttachment);
            }

            $this->redirectCreateWithErrors([
                'general' => 'Unable to create this discussion right now. Please try again.',
            ], $data);
        }
    }

    public function edit($id = 0)
    {
        $post = $this->findPostById($id);

        if ($post === null) {
            $this->notFound();
        }

        if (!$this->canEditPost($post)) {
            $this->forbidden($this->postUrl($post));
        }

        $this->redirectDiscussionEditWithErrors($post, [], []);
    }

    public function update($id = 0)
    {
        $this->requirePost(BASE_URL . '/discussions');

        $post = $this->findPostById($id);

        if ($post === null) {
            $this->notFound();
        }

        if (!$this->canEditPost($post)) {
            $this->forbidden($this->postUrl($post));
        }

        if (!$this->verifyCsrfToken($_POST['_csrf_token'] ?? null)) {
            $this->forbidden($this->postUrl($post));
        }

        $moduleId = filter_var($_POST['module_id'] ?? 0, FILTER_VALIDATE_INT);
        $data = [
            'title' => trim((string) ($_POST['title'] ?? '')),
            'module_id' => $moduleId === false ? 0 : $moduleId,
            'content' => trim((string) ($_POST['content'] ?? '')),
        ];

        try {
            $moduleModel = new Module();
            $errors = $this->validateDiscussionCreate($data, $moduleModel);

            if (!empty($errors)) {
                $this->redirectDiscussionEditWithErrors($post, $data, $errors);
            }

            if (!(new Post())->update((int) ($post['id'] ?? 0), $data)) {
                $this->redirectDiscussionEditWithErrors($post, $data, [
                    'general' => 'Unable to update this discussion. Please try again.',
                ]);
            }
        } catch (Throwable) {
            $this->redirectDiscussionEditWithErrors($post, $data, [
                'general' => 'Unable to update this discussion right now. Please try again.',
            ]);
        }

        $updated = $this->findPostById((int) ($post['id'] ?? 0)) ?? $post;
        $updatedId = (int) ($updated['id'] ?? $post['id'] ?? 0);
        $slug = trim((string) ($updated['slug'] ?? $post['slug'] ?? ''));

        $this->redirectTo(FormatHelper::discussionDetailUrl($updatedId, $slug));
    }

    public function delete($id = 0)
    {
        $post = $this->findPostById($id);

        if ($post === null) {
            $this->notFound();
        }

        if (!$this->canDeletePost($post)) {
            $this->forbidden($this->postUrl($post));
        }

        $this->redirectModal((int) ($post['id'] ?? 0), 'discussion-delete-modal', $this->postUrl($post));
    }

    public function destroy($id = 0)
    {
        $this->requirePost(BASE_URL . '/discussions');

        $post = $this->findPostById($id);

        if ($post === null) {
            $this->notFound();
        }

        if (!$this->canDeletePost($post) || !$this->verifyCsrfToken($_POST['_csrf_token'] ?? null)) {
            $this->forbidden($this->postUrl($post));
        }

        try {
            (new Post())->delete((int) ($post['id'] ?? 0));
        } catch (Throwable) {
            $this->redirectTo(FormatHelper::discussionDetailUrl(
                $post['id'] ?? 0,
                $post['slug'] ?? ''
            ));
        }

        $this->redirectTo(BASE_URL . '/discussions');
    }

    private function validateDiscussionCreate(array $data, Module $moduleModel)
    {
        $errors = [];
        $title = trim((string) ($data['title'] ?? ''));
        $moduleId = (int) ($data['module_id'] ?? 0);

        if ($title === '') {
            $errors['title'] = 'Please enter a discussion title.';
        } elseif (FormatHelper::textLength($title) > 255) {
            $errors['title'] = 'Title must be 255 characters or fewer.';
        }

        if ($moduleId <= 0) {
            $errors['module_id'] = 'Please choose a module.';
        } else {
            try {
                if (!$moduleModel->exists($moduleId)) {
                    $errors['module_id'] = 'Please choose an available module.';
                }
            } catch (Throwable) {
                $errors['module_id'] = 'Module could not be checked. Please try again.';
            }
        }

        return $errors;
    }

    private function redirectCreateWithErrors($errors, array $old = [])
    {
        unset($old['user_id'], $old['status']);

        $_SESSION['discussion_create_errors'] = $errors;
        $_SESSION['discussion_create_old'] = $old;

        $this->redirectTo(BASE_URL . '/discussions/create');
    }

    private function redirectDiscussionEditWithErrors(array $post, array $old, array $errors)
    {
        $_SESSION['discussion_edit_state'] = [
            'post_id' => (int) ($post['id'] ?? 0),
            'old' => $old,
            'errors' => $errors,
        ];

        $this->redirectTo($this->postUrl($post) . '#question-content-heading');
    }

    private function redirectModal(int $postId, string $modalId, string $redirectUrl)
    {
        $_SESSION['discussion_modal_state'] = [
            'post_id' => $postId,
            'modal_id' => $modalId,
        ];

        $this->redirectTo($redirectUrl);
    }

    private function findPostById(int $id)
    {
        if ($id <= 0) {
            return null;
        }

        try {
            return (new Post())->find($id);
        } catch (Throwable) {
            return null;
        }
    }

    private function canEditPost(array $post)
    {
        return PermissionHelper::canEditPost($this->currentUser(), $post);
    }

    private function canDeletePost(array $post)
    {
        return $this->canEditPost($post);
    }

    private function postUrl(array $post)
    {
        return FormatHelper::discussionDetailUrl($post['id'] ?? 0, $post['slug'] ?? '');
    }
}
