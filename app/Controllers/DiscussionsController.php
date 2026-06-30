<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Models\Media;
use App\Models\Module;
use App\Models\Post;
use App\Models\Reply;
use Throwable;

class DiscussionsController extends Controller
{
    private const PAGE_LIMIT = 20;
    private const IMAGE_MAX_SIZE = 5242880;
    private const VIDEO_MAX_SIZE = 52428800;
    private const DOCUMENT_MAX_SIZE = 10485760;

    public function index()
    {
        $this->renderIndex();
    }

    public function unsolved()
    {
        $this->renderIndex(['status' => 'open']);
    }

    public function create()
    {
        if ($this->authUserId() === null) {
            header('Location: ' . BASE_URL . '/login');
            exit;
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
        ]);

        unset($_SESSION['discussion_create_errors'], $_SESSION['discussion_create_old']);
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/discussions/create');
            exit;
        }

        $userId = $this->authUserId();

        if ($userId === null) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        $data = [
            'title' => trim((string) ($_POST['title'] ?? '')),
            'module_id' => (int) ($_POST['module_id'] ?? 0),
            'content' => trim((string) ($_POST['content'] ?? '')),
            'user_id' => $userId,
            'status' => 'open',
        ];

        try {
            $attachment = $this->validatedAttachment($_FILES['attachment'] ?? null);
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
                $storedAttachment = $this->storeAttachment($attachment);

                if ($storedAttachment === null) {
                    $db->rollBack();
                    $this->redirectCreateWithErrors([
                        'attachment' => 'The attachment could not be saved. Please choose another file.',
                    ], $data);
                }

                $storedAttachment['post_id'] = $postId;

                if (!(new Media())->create($storedAttachment)) {
                    $this->removeStoredAttachment($storedAttachment);
                    $db->rollBack();
                    $this->redirectCreateWithErrors([
                        'attachment' => 'The attachment could not be saved. Please choose another file.',
                    ], $data);
                }
            }

            $db->commit();

            $post = $postModel->find($postId);
            $slug = trim((string) ($post['slug'] ?? ''));

            header('Location: ' . BASE_URL . '/discussions/' . rawurlencode($slug !== '' ? $slug : (string) $postId));
            exit;
        } catch (Throwable) {
            if (isset($db) && $db->inTransaction()) {
                $db->rollBack();
            }

            if (isset($storedAttachment) && is_array($storedAttachment)) {
                $this->removeStoredAttachment($storedAttachment);
            }

            $this->redirectCreateWithErrors([
                'general' => 'Unable to create this discussion right now. Please try again.',
            ], $data);
        }
    }

    public function show($slug = '')
    {
        $slug = trim(rawurldecode((string) $slug));

        if ($slug === '') {
            $this->notFound();
        }

        $postModel = new Post();
        $post = $this->findDiscussion($postModel, $slug);

        if ($post === null) {
            $this->notFound();
        }

        $userId = $this->authUserId();

        if ($userId !== null) {
            try {
                $postModel->recordView((int) ($post['id'] ?? 0), $userId);
            } catch (Throwable) {
                // View tracking is useful but should never block reading a discussion.
            }
        }

        $mediaItems = [];

        try {
            $mediaItems = (new Media())->getByPostId((int) ($post['id'] ?? 0));
        } catch (Throwable) {
            $mediaItems = [];
        }

        $postId = (int) ($post['id'] ?? 0);
        $replies = [];
        $relatedDiscussions = [];

        try {
            $replies = (new Reply())->getByPostId($postId);
        } catch (Throwable) {
            $replies = [];
        }

        try {
            $relatedDiscussions = $postModel->getPopularDiscussions(4);
        } catch (Throwable) {
            $relatedDiscussions = [];
        }

        $replyState = $_SESSION['discussion_reply_state'] ?? [];
        $hasReplyState = (int) ($replyState['post_id'] ?? 0) === $postId;
        $discussionEditState = $_SESSION['discussion_edit_state'] ?? [];
        $hasDiscussionEditState = (int) ($discussionEditState['post_id'] ?? 0) === $postId;
        $replyEditState = $_SESSION['discussion_reply_edit_state'] ?? [];
        $hasReplyEditState = (int) ($replyEditState['post_id'] ?? 0) === $postId;
        $modalState = $_SESSION['discussion_modal_state'] ?? [];
        $hasModalState = (int) ($modalState['post_id'] ?? 0) === $postId;
        $modules = [];

        try {
            $modules = (new Module())->getAll();
        } catch (Throwable) {
            $modules = [];
            $discussionEditState['errors']['general'] = $discussionEditState['errors']['general']
                ?? 'Modules could not be loaded. Please try again.';
        }

        $openModalId = '';

        if ($hasDiscussionEditState) {
            $openModalId = 'discussion-edit-modal';
        } elseif ($hasReplyEditState) {
            $openModalId = 'reply-edit-modal-' . (int) ($replyEditState['reply_id'] ?? 0);
        } elseif ($hasModalState) {
            $openModalId = (string) ($modalState['modal_id'] ?? '');
        }

        $this->view('discussions/show', [
            'discussion' => $this->formatDiscussionDetail($post, $mediaItems),
            'replies' => array_map(fn (array $reply): array => $this->formatReply($reply), $replies),
            'relatedDiscussions' => array_map(fn (array $relatedPost): array => $this->formatSidebarDiscussion($relatedPost), $relatedDiscussions),
            'replyErrors' => $hasReplyState ? ($replyState['errors'] ?? []) : [],
            'replyOld' => $hasReplyState ? ($replyState['old'] ?? []) : [],
            'modules' => $modules,
            'discussionEditErrors' => $hasDiscussionEditState ? ($discussionEditState['errors'] ?? []) : [],
            'discussionEditOld' => $hasDiscussionEditState ? ($discussionEditState['old'] ?? []) : [],
            'replyEditErrors' => $hasReplyEditState ? ($replyEditState['errors'] ?? []) : [],
            'replyEditOld' => $hasReplyEditState ? ($replyEditState['old'] ?? []) : [],
            'activeReplyEditId' => $hasReplyEditState ? (int) ($replyEditState['reply_id'] ?? 0) : 0,
            'openModalId' => $openModalId,
            'pageScripts' => ['discussion-detail.js'],
        ]);

        if ($hasReplyState) {
            unset($_SESSION['discussion_reply_state']);
        }

        if ($hasDiscussionEditState) {
            unset($_SESSION['discussion_edit_state']);
        }

        if ($hasReplyEditState) {
            unset($_SESSION['discussion_reply_edit_state']);
        }

        if ($hasModalState) {
            unset($_SESSION['discussion_modal_state']);
        }
    }

    public function reply()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/discussions');
            exit;
        }

        $userId = $this->authUserId();
        $postId = (int) ($_POST['post_id'] ?? 0);
        $slug = trim((string) ($_POST['slug'] ?? ''));
        $content = trim((string) ($_POST['content'] ?? ''));
        $redirectUrl = $this->replyRedirectUrl($slug, $postId);

        if ($userId === null) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        $errors = $this->validateReply($content);

        if ($postId <= 0) {
            $errors['general'] = 'This discussion could not be found. Please reopen it and try again.';
        }

        if (!empty($errors)) {
            $this->redirectReplyWithErrors($postId, $content, $errors, $redirectUrl);
        }

        try {
            $replyId = (new Reply())->create([
                'post_id' => $postId,
                'user_id' => $userId,
                'content' => $content,
            ]);

            if ($replyId <= 0) {
                $this->redirectReplyWithErrors($postId, $content, [
                    'general' => 'Unable to post your reply. Please try again.',
                ], $redirectUrl);
            }
        } catch (Throwable) {
            $this->redirectReplyWithErrors($postId, $content, [
                'general' => 'Unable to post your reply right now. Please try again.',
            ], $redirectUrl);
        }

        header('Location: ' . $redirectUrl);
        exit;
    }

    public function edit($id = 0)
    {
        $post = $this->findPostById((int) $id);

        if ($post === null) {
            $this->notFound();
        }

        if (!$this->canEditPost($post)) {
            $this->forbidden();
        }

        $this->redirectDiscussionEditWithErrors($post, [], []);
    }

    public function update($id = 0)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/discussions');
            exit;
        }

        $post = $this->findPostById((int) $id);

        if ($post === null) {
            $this->notFound();
        }

        if (!$this->canEditPost($post)) {
            $this->forbidden();
        }

        if (!$this->verifyCsrfToken($_POST['_csrf_token'] ?? null)) {
            $this->forbidden();
        }

        $data = [
            'title' => trim((string) ($_POST['title'] ?? '')),
            'module_id' => (int) ($_POST['module_id'] ?? 0),
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
        $slug = trim((string) ($updated['slug'] ?? $post['slug'] ?? $post['id'] ?? ''));

        header('Location: ' . BASE_URL . '/discussions/' . rawurlencode($slug));
        exit;
    }

    public function delete($id = 0)
    {
        $post = $this->findPostById((int) $id);

        if ($post === null) {
            $this->notFound();
        }

        if (!$this->canDeletePost($post)) {
            $this->forbidden();
        }

        $this->redirectModal((int) ($post['id'] ?? 0), 'discussion-delete-modal', $this->postUrl($post));
    }

    public function destroy($id = 0)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/discussions');
            exit;
        }

        $post = $this->findPostById((int) $id);

        if ($post === null) {
            $this->notFound();
        }

        if (!$this->canDeletePost($post) || !$this->verifyCsrfToken($_POST['_csrf_token'] ?? null)) {
            $this->forbidden();
        }

        try {
            (new Post())->delete((int) ($post['id'] ?? 0));
        } catch (Throwable) {
            header('Location: ' . BASE_URL . '/discussions/' . rawurlencode((string) ($post['slug'] ?? $post['id'] ?? '')));
            exit;
        }

        header('Location: ' . BASE_URL . '/discussions');
        exit;
    }

    public function replyEdit($id = 0)
    {
        $reply = $this->findReplyById((int) $id);

        if ($reply === null) {
            $this->notFound();
        }

        if (!$this->canEditReply($reply)) {
            $this->forbidden();
        }

        $this->redirectReplyEditWithErrors($reply, (string) ($reply['content'] ?? ''), []);
    }

    public function replyUpdate($id = 0)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/discussions');
            exit;
        }

        $reply = $this->findReplyById((int) $id);

        if ($reply === null) {
            $this->notFound();
        }

        if (!$this->canEditReply($reply) || !$this->verifyCsrfToken($_POST['_csrf_token'] ?? null)) {
            $this->forbidden();
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

    public function replyDelete($id = 0)
    {
        $reply = $this->findReplyById((int) $id);

        if ($reply === null) {
            $this->notFound();
        }

        if (!$this->canDeleteReply($reply)) {
            $this->forbidden();
        }

        $this->redirectModal(
            (int) ($reply['post_id'] ?? 0),
            'reply-delete-modal-' . (int) ($reply['id'] ?? 0),
            $this->postUrlFromReply($reply) . '#reply-' . (int) ($reply['id'] ?? 0)
        );
    }

    public function replyDestroy($id = 0)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/discussions');
            exit;
        }

        $reply = $this->findReplyById((int) $id);

        if ($reply === null) {
            $this->notFound();
        }

        if (!$this->canDeleteReply($reply) || !$this->verifyCsrfToken($_POST['_csrf_token'] ?? null)) {
            $this->forbidden();
        }

        try {
            (new Reply())->delete((int) ($reply['id'] ?? 0));
        } catch (Throwable) {
            header('Location: ' . $this->postUrlFromReply($reply));
            exit;
        }

        header('Location: ' . $this->postUrlFromReply($reply) . '#replies');
        exit;
    }

    private function renderIndex(array $forcedFilters = [])
    {
        $filters = array_merge($this->requestFilters(), $forcedFilters);
        $postModel = new Post();
        $moduleModel = new Module();
        $currentPage = max(1, (int) ($filters['page'] ?? 1));
        $offset = ($currentPage - 1) * self::PAGE_LIMIT;

        try {
            $posts = $postModel->getDiscussionList($filters, self::PAGE_LIMIT, $offset);
            $totalDiscussions = $postModel->getDiscussionCount($filters);
        } catch (Throwable) {
            $posts = [];
            $totalDiscussions = 0;
        }

        try {
            $trendingModules = $postModel->getTrendingModules(5);
        } catch (Throwable) {
            $trendingModules = [];
        }

        try {
            $recentViews = $postModel->getRecentViews($this->authUserId(), 4);
        } catch (Throwable) {
            $recentViews = [];
        }

        try {
            $popularDiscussions = $postModel->getPopularDiscussions(3);
        } catch (Throwable) {
            $popularDiscussions = [];
        }

        try {
            $modules = $moduleModel->getAll();
        } catch (Throwable) {
            $modules = [];
        }

        $this->view('discussions/index', [
            'discussions' => array_map(fn (array $post): array => $this->formatDiscussion($post), $posts),
            'totalDiscussions' => $totalDiscussions,
            'filters' => $filters,
            'statusFilters' => $this->statusFilters($filters),
            'moduleChips' => $this->moduleChips($modules, $filters),
            'matchedModules' => $this->matchedModules($modules, $filters),
            'trendingModules' => $this->formatTrendingModules($trendingModules),
            'recentViewedDiscussions' => array_map(fn (array $post): array => $this->formatRecentView($post), $recentViews),
            'popularDiscussions' => array_map(fn (array $post): array => $this->formatSidebarDiscussion($post), $popularDiscussions),
            'pagination' => $this->pagination($filters, $totalDiscussions, $currentPage),
        ]);
    }

    private function requestFilters(): array
    {
        $status = trim((string) ($_GET['status'] ?? ''));
        $sort = trim((string) ($_GET['sort'] ?? ''));
        $module = trim((string) ($_GET['module'] ?? ''));

        return [
            'q' => $this->shortText(trim((string) ($_GET['q'] ?? '')), 100),
            'status' => in_array($status, ['open', 'solved'], true) ? $status : '',
            'module' => $this->shortText($module, 30),
            'sort' => $sort === 'popular' ? 'popular' : '',
            'page' => max(1, (int) ($_GET['page'] ?? 1)),
        ];
    }

    private function statusFilters(array $filters): array
    {
        return [
            [
                'label' => 'All',
                'url' => $this->discussionUrl($filters, ['status' => null, 'sort' => null, 'page' => null]),
                'active' => ($filters['status'] ?? '') === '' && ($filters['sort'] ?? '') !== 'popular',
            ],
            [
                'label' => 'Open',
                'url' => $this->discussionUrl($filters, ['status' => 'open', 'sort' => null, 'page' => null]),
                'active' => ($filters['status'] ?? '') === 'open',
            ],
            [
                'label' => 'Solved',
                'url' => $this->discussionUrl($filters, ['status' => 'solved', 'sort' => null, 'page' => null]),
                'active' => ($filters['status'] ?? '') === 'solved',
            ],
            [
                'label' => 'Popular',
                'url' => $this->discussionUrl($filters, ['status' => null, 'sort' => 'popular', 'page' => null]),
                'active' => ($filters['sort'] ?? '') === 'popular',
            ],
        ];
    }

    private function moduleChips(array $modules, array $filters): array
    {
        $chips = [];

        foreach ($modules as $module) {
            $code = trim((string) ($module['code'] ?? ''));

            if ($code === '') {
                continue;
            }

            $chips[] = [
                'code' => $code,
                'name' => $this->textOr($module['name'] ?? '', $code),
                'url' => $this->discussionUrl($filters, ['module' => $code, 'page' => null]),
                'active' => strcasecmp((string) ($filters['module'] ?? ''), $code) === 0,
            ];
        }

        return $chips;
    }

    private function matchedModules(array $modules, array $filters): array
    {
        $query = strtolower(trim((string) ($filters['q'] ?? '')));

        if ($query === '') {
            return [];
        }

        $matches = [];

        foreach ($modules as $module) {
            $code = trim((string) ($module['code'] ?? ''));
            $name = $this->textOr($module['name'] ?? '', $code);

            if ($code === '') {
                continue;
            }

            if (!str_contains(strtolower($code), $query) && !str_contains(strtolower($name), $query)) {
                continue;
            }

            $matches[] = [
                'code' => $code,
                'name' => $name,
                'url' => $this->discussionUrl($filters, ['module' => $code, 'q' => null, 'page' => null]),
            ];
        }

        return $matches;
    }

    private function pagination(array $filters, int $totalDiscussions, int $currentPage)
    {
        $totalPages = max(1, (int) ceil($totalDiscussions / self::PAGE_LIMIT));

        return [
            'current' => min($currentPage, $totalPages),
            'total' => $totalPages,
            'has_previous' => $currentPage > 1,
            'has_next' => $currentPage < $totalPages,
            'previous_url' => $this->discussionUrl($filters, ['page' => $currentPage > 2 ? $currentPage - 1 : null]),
            'next_url' => $this->discussionUrl($filters, ['page' => $currentPage + 1]),
        ];
    }

    private function formatDiscussion(array $post): array
    {
        $status = (string) ($post['status'] ?? 'open');
        $title = $this->textOr($post['title'] ?? '', 'Untitled question');

        return [
            'module' => $this->textOr($post['module_code'] ?? '', 'MODULE'),
            'module_name' => $this->textOr($post['module_name'] ?? '', 'Module discussion'),
            'status' => $status === 'solved' ? 'Solved' : 'Open',
            'status_tone' => $status === 'solved' ? 'green' : 'neutral',
            'created_at' => $this->formatDate((string) ($post['created_at'] ?? '')),
            'title' => $title,
            'excerpt' => $this->textOr($this->excerpt((string) ($post['content'] ?? ''), 180), 'No preview is available yet.'),
            'author' => $this->authorName($post),
            'author_handle' => $this->authorHandle($post),
            'avatar' => $this->authorInitial($post),
            'replies' => (int) ($post['reply_count'] ?? 0),
            'views' => $this->compactNumber((int) ($post['view_count'] ?? 0)),
            'image' => $this->mediaUrl($post['media_path'] ?? null),
            'preview_alt' => 'Preview for ' . $title,
            'url' => BASE_URL . '/discussions/' . rawurlencode((string) ($post['slug'] ?? $post['id'] ?? '')),
        ];
    }

    private function formatTrendingModules(array $modules): array
    {
        $formatted = [];

        foreach ($modules as $module) {
            $code = trim((string) ($module['code'] ?? ''));
            $count = (int) ($module['post_count'] ?? 0);

            if ($code === '') {
                continue;
            }

            $formatted[] = [
                'code' => $code,
                'name' => $this->textOr($module['name'] ?? '', $code),
                'count' => $count . ' ' . ($count === 1 ? 'discussion' : 'discussions'),
                'url' => BASE_URL . '/discussions?module=' . rawurlencode($code),
            ];
        }

        return $formatted;
    }

    private function formatRecentView(array $post): array
    {
        return [
            'title' => $this->textOr($post['title'] ?? '', 'Untitled question'),
            'module' => $this->textOr($post['module_code'] ?? '', 'MODULE'),
            'time' => $this->textOr($this->relativeTime((string) ($post['viewed_at'] ?? '')), 'Recently'),
            'url' => BASE_URL . '/discussions/' . rawurlencode((string) ($post['slug'] ?? $post['id'] ?? '')),
        ];
    }

    private function formatSidebarDiscussion(array $post): array
    {
        return [
            'title' => $this->textOr($post['title'] ?? '', 'Untitled question'),
            'replies' => (int) ($post['reply_count'] ?? 0),
            'url' => BASE_URL . '/discussions/' . rawurlencode((string) ($post['slug'] ?? $post['id'] ?? '')),
        ];
    }

    private function formatDiscussionDetail(array $post, array $mediaItems = [])
    {
        $status = (string) ($post['status'] ?? 'open');
        $title = $this->textOr($post['title'] ?? '', 'Untitled question');
        $moduleCode = $this->textOr($post['module_code'] ?? '', 'MODULE');

        return [
            'id' => (int) ($post['id'] ?? 0),
            'title' => $title,
            'content' => $this->textOr($post['content'] ?? '', 'No content is available for this discussion.'),
            'module' => $moduleCode,
            'module_name' => $this->textOr($post['module_name'] ?? '', 'Module discussion'),
            'module_url' => BASE_URL . '/discussions?module=' . rawurlencode($moduleCode),
            'status' => $status === 'solved' ? 'Solved' : 'Open',
            'status_tone' => $status === 'solved' ? 'green' : 'neutral',
            'created_at' => $this->formatDate((string) ($post['created_at'] ?? '')),
            'updated_at' => $this->formatDate((string) ($post['updated_at'] ?? '')),
            'author' => $this->authorName($post),
            'author_handle' => $this->authorHandle($post),
            'avatar' => $this->authorInitial($post),
            'replies' => (int) ($post['reply_count'] ?? 0),
            'views' => $this->compactNumber((int) ($post['view_count'] ?? 0)),
            'back_url' => BASE_URL . '/discussions',
            'slug' => (string) ($post['slug'] ?? $post['id'] ?? ''),
            'module_id' => (int) ($post['module_id'] ?? 0),
            'attachments' => array_map(fn (array $media) => $this->formatAttachment($media), $mediaItems),
            'can_edit' => $this->canEditPost($post),
            'can_delete' => $this->canDeletePost($post),
            'edit_url' => BASE_URL . '/discussions/edit/' . (int) ($post['id'] ?? 0),
            'delete_url' => BASE_URL . '/discussions/delete/' . (int) ($post['id'] ?? 0),
            'update_url' => BASE_URL . '/discussions/update/' . (int) ($post['id'] ?? 0),
            'destroy_url' => BASE_URL . '/discussions/destroy/' . (int) ($post['id'] ?? 0),
        ];
    }

    private function formatReply(array $reply)
    {
        $role = trim((string) ($reply['role'] ?? 'student'));

        return [
            'id' => (int) ($reply['id'] ?? 0),
            'post_id' => (int) ($reply['post_id'] ?? 0),
            'user_id' => (int) ($reply['user_id'] ?? 0),
            'content' => $this->textOr($reply['content'] ?? '', 'No reply content is available.'),
            'author' => $this->authorName($reply),
            'author_handle' => $this->authorHandle($reply),
            'avatar' => $this->authorInitial($reply),
            'role' => $role !== '' ? ucfirst($role) : 'Student',
            'created_at' => $this->relativeTime((string) ($reply['created_at'] ?? '')),
            'is_accepted' => (int) ($reply['is_accepted'] ?? 0) === 1,
            'can_edit' => $this->canEditReply($reply),
            'can_delete' => $this->canDeleteReply($reply),
            'edit_url' => BASE_URL . '/discussions/reply-edit/' . (int) ($reply['id'] ?? 0),
            'delete_url' => BASE_URL . '/discussions/reply-delete/' . (int) ($reply['id'] ?? 0),
            'update_url' => BASE_URL . '/discussions/reply-update/' . (int) ($reply['id'] ?? 0),
            'destroy_url' => BASE_URL . '/discussions/reply-destroy/' . (int) ($reply['id'] ?? 0),
        ];
    }

    private function formatAttachment(array $media)
    {
        $path = trim((string) ($media['path'] ?? ''));
        $originalName = $this->textOr($media['original_name'] ?? '', basename($path));
        $type = trim((string) ($media['type'] ?? 'document'));

        return [
            'type' => in_array($type, ['image', 'video', 'document'], true) ? $type : 'document',
            'name' => $originalName,
            'url' => $this->mediaUrl($path),
            'mime_type' => trim((string) ($media['mime_type'] ?? '')),
            'size' => $this->formatFileSize((int) ($media['file_size'] ?? 0)),
        ];
    }

    private function validateDiscussionCreate(array $data, Module $moduleModel)
    {
        $errors = [];
        $title = trim((string) ($data['title'] ?? ''));
        $moduleId = (int) ($data['module_id'] ?? 0);

        if ($title === '') {
            $errors['title'] = 'Please enter a discussion title.';
        } elseif ($this->textLength($title) > 255) {
            $errors['title'] = 'Title must be 255 characters or fewer.';
        }

        if ($moduleId <= 0) {
            $errors['module_id'] = 'Please choose a module.';
        } else {
            try {
                if ($moduleModel->findById($moduleId) === null) {
                    $errors['module_id'] = 'Please choose an available module.';
                }
            } catch (Throwable) {
                $errors['module_id'] = 'Module could not be checked. Please try again.';
            }
        }

        return $errors;
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

    private function redirectCreateWithErrors($errors, array $old = [])
    {
        unset($old['user_id'], $old['status']);

        $_SESSION['discussion_create_errors'] = $errors;
        $_SESSION['discussion_create_old'] = $old;

        header('Location: ' . BASE_URL . '/discussions/create');
        exit;
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

    private function redirectDiscussionEditWithErrors(array $post, array $old, array $errors)
    {
        $_SESSION['discussion_edit_state'] = [
            'post_id' => (int) ($post['id'] ?? 0),
            'old' => $old,
            'errors' => $errors,
        ];

        header('Location: ' . $this->postUrl($post) . '#question-content-heading');
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
        $target = $slug !== '' ? $slug : (string) $postId;

        if ($target === '' || $target === '0') {
            return BASE_URL . '/discussions';
        }

        return BASE_URL . '/discussions/' . rawurlencode($target) . '#reply-editor';
    }

    private function validatedAttachment(?array $file)
    {
        if (!is_array($file) || (int) ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return ['has_file' => false, 'error' => ''];
        }

        $error = (int) ($file['error'] ?? UPLOAD_ERR_OK);

        if ($error !== UPLOAD_ERR_OK) {
            return [
                'has_file' => true,
                'error' => $this->uploadErrorMessage($error),
            ];
        }

        $originalName = basename((string) ($file['name'] ?? 'attachment'));
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        $size = (int) ($file['size'] ?? 0);
        $mimeType = $this->detectedMimeType((string) ($file['tmp_name'] ?? ''), (string) ($file['type'] ?? ''));
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
            'tmp_name' => (string) ($file['tmp_name'] ?? ''),
            'original_name' => $originalName,
            'extension' => $extension,
            'mime_type' => $mimeType,
            'file_size' => $size,
            'type' => $type,
        ];
    }

    private function storeAttachment(array $attachment)
    {
        $type = (string) ($attachment['type'] ?? 'document');
        $folder = $type === 'image' ? 'images' : ($type === 'video' ? 'videos' : 'documents');
        $uploadDir = ROOT_PATH . '/public/uploads/' . $folder;

        if (!is_dir($uploadDir) && !mkdir($uploadDir, 0775, true)) {
            return null;
        }

        $extension = $this->storageExtension(
            (string) ($attachment['extension'] ?? ''),
            $type
        );
        $fileName = date('YmdHis') . '-' . bin2hex(random_bytes(8)) . '.' . $extension;
        $targetPath = $uploadDir . '/' . $fileName;

        if (!move_uploaded_file((string) ($attachment['tmp_name'] ?? ''), $targetPath)) {
            return null;
        }

        return [
            'type' => $type,
            'path' => 'uploads/' . $folder . '/' . $fileName,
            'original_name' => $attachment['original_name'] ?? $fileName,
            'mime_type' => $attachment['mime_type'] ?? null,
            'file_size' => (int) ($attachment['file_size'] ?? 0),
        ];
    }

    private function removeStoredAttachment(array $attachment)
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

        return trim($fallback) !== '' ? trim($fallback) : 'application/octet-stream';
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

    private function findPostById(int $id): ?array
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

    private function findReplyById(int $id): ?array
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

    private function canEditPost(array $post): bool
    {
        $user = $this->authUser();

        if ($user === null) {
            return false;
        }

        return $this->isAdmin($user) || (int) ($post['user_id'] ?? 0) === (int) ($user['id'] ?? 0);
    }

    private function canDeletePost(array $post): bool
    {
        return $this->canEditPost($post);
    }

    private function canEditReply(array $reply): bool
    {
        $user = $this->authUser();

        if ($user === null) {
            return false;
        }

        return $this->isAdmin($user) || (int) ($reply['user_id'] ?? 0) === (int) ($user['id'] ?? 0);
    }

    private function canDeleteReply(array $reply): bool
    {
        $user = $this->authUser();

        if ($user === null) {
            return false;
        }

        $userId = (int) ($user['id'] ?? 0);

        return $this->isAdmin($user)
            || (int) ($reply['user_id'] ?? 0) === $userId
            || (int) ($reply['post_user_id'] ?? 0) === $userId;
    }

    private function isAdmin(array $user): bool
    {
        return strtolower(trim((string) ($user['role'] ?? ''))) === 'admin';
    }

    private function postUrlFromReply(array $reply): string
    {
        $target = trim((string) ($reply['post_slug'] ?? ''));

        if ($target === '') {
            $target = (string) ($reply['post_id'] ?? '');
        }

        return BASE_URL . '/discussions/' . rawurlencode($target);
    }

    private function postUrl(array $post)
    {
        $target = trim((string) ($post['slug'] ?? ''));

        if ($target === '') {
            $target = (string) ($post['id'] ?? '');
        }

        return BASE_URL . '/discussions/' . rawurlencode($target);
    }

    private function findDiscussion(Post $postModel, string $slug)
    {
        try {
            if (ctype_digit($slug)) {
                return $postModel->find((int) $slug);
            }

            return $postModel->findBySlug($slug);
        } catch (Throwable) {
            return null;
        }
    }

    private function notFound()
    {
        http_response_code(404);
        require ROOT_PATH . '/app/Views/errors/404.php';
        exit;
    }

    private function forbidden(): void
    {
        http_response_code(403);
        $this->view('errors/403');
        exit;
    }

    private function discussionUrl(array $filters, array $overrides = [])
    {
        $query = array_merge($filters, $overrides);

        foreach ($query as $key => $value) {
            if ($value === null || trim((string) $value) === '') {
                unset($query[$key]);
            }
        }

        $queryString = http_build_query($query);

        return BASE_URL . '/discussions' . ($queryString !== '' ? '?' . $queryString : '');
    }

    private function authUserId()
    {
        $authUser = $this->authUser();

        if ($authUser === null) {
            return null;
        }

        $userId = (int) ($authUser['id'] ?? 0);

        return $userId > 0 ? $userId : null;
    }

    private function authUser(): ?array
    {
        $authUser = $_SESSION['user'] ?? $_SESSION['auth_user'] ?? null;

        return is_array($authUser) ? $authUser : null;
    }

    private function authorName(array $post)
    {
        return $this->textOr($post['full_name'] ?? $post['username'] ?? '', 'Student');
    }

    private function authorHandle(array $post)
    {
        $username = trim((string) ($post['username'] ?? ''));

        return $username !== '' ? '@' . $username : '@student';
    }

    private function authorInitial(array $post)
    {
        $name = trim((string) ($post['full_name'] ?? $post['username'] ?? 'S'));

        return strtoupper($this->shortText($name, 2));
    }

    private function excerpt(string $content, int $limit)
    {
        $content = trim(strip_tags($content));

        if (strlen($content) <= $limit) {
            return $content;
        }

        return rtrim(substr($content, 0, $limit - 3)) . '...';
    }

    private function compactNumber(int $number)
    {
        if ($number >= 1000) {
            return rtrim(rtrim(number_format($number / 1000, 1), '0'), '.') . 'k';
        }

        return (string) $number;
    }

    private function formatFileSize(int $bytes)
    {
        if ($bytes <= 0) {
            return '';
        }

        if ($bytes >= 1048576) {
            return rtrim(rtrim(number_format($bytes / 1048576, 1), '0'), '.') . ' MB';
        }

        return max(1, (int) ceil($bytes / 1024)) . ' KB';
    }

    private function mediaUrl(mixed $path)
    {
        $path = trim((string) $path);

        if ($path === '') {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        return BASE_URL . '/' . ltrim($path, '/');
    }

    private function formatDate(string $dateTime)
    {
        $timestamp = strtotime($dateTime);

        return $timestamp !== false ? date('M j, Y', $timestamp) : 'Recently';
    }

    private function relativeTime(string $dateTime)
    {
        $timestamp = strtotime($dateTime);

        if ($timestamp === false) {
            return '';
        }

        $seconds = max(0, time() - $timestamp);

        if ($seconds < 60) {
            return 'Just now';
        }

        if ($seconds < 3600) {
            $minutes = (int) floor($seconds / 60);
            return $minutes . ' min' . ($minutes === 1 ? '' : 's') . ' ago';
        }

        if ($seconds < 86400) {
            $hours = (int) floor($seconds / 3600);
            return $hours . 'h ago';
        }

        if ($seconds < 172800) {
            return 'Yesterday';
        }

        $days = (int) floor($seconds / 86400);

        return $days . ' days ago';
    }

    private function textOr(mixed $value, string $default)
    {
        $text = trim((string) $value);

        return $text !== '' ? $text : $default;
    }

    private function shortText(string $value, int $length)
    {
        if (function_exists('mb_substr')) {
            return mb_substr($value, 0, $length);
        }

        return substr($value, 0, $length);
    }

    private function textLength(string $value)
    {
        if (function_exists('mb_strlen')) {
            return mb_strlen($value);
        }

        return strlen($value);
    }
}
