<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Helpers\FormatHelper;
use App\Helpers\ViewHelper;
use App\Helpers\PermissionHelper;
use App\Models\Media;
use App\Models\Module;
use App\Models\Post;
use App\Models\Reply;
use Throwable;

class DiscussionsController extends Controller
{
    private const PAGE_LIMIT = 20;

    public function index()
    {
        $filters = $this->requestFilters();
        $postModel = new Post();
        $moduleModel = new Module();
        $currentPage = max(1, ($filters['page'] ?? 1));
        $offset = ($currentPage - 1) * self::PAGE_LIMIT;

        try {
            $posts = $postModel->getDiscussionList($filters, self::PAGE_LIMIT, $offset);
            $totalDiscussions = $postModel->getDiscussionCount($filters);
        } catch (Throwable) {
            $posts = [];
            $totalDiscussions = 0;
        }

        try {
            $trendingModules = $moduleModel->getTrending(3);
        } catch (Throwable) {
            $trendingModules = [];
        }

        try {
            $recentViews = $postModel->getRecentViews($this->currentUserId(), 4);
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
            'discussions' => array_map(fn (array $post) => ViewHelper::formatPostCard($post), $posts),
            'totalDiscussions' => $totalDiscussions,
            'filters' => $filters,
            'statusFilters' => $this->statusFilters($filters),
            'moduleChips' => ViewHelper::moduleChips($modules, $filters),
            'matchedModules' => ViewHelper::matchedModules($modules, $filters),
            'trendingModules' => ViewHelper::formatTrendingModules($trendingModules),
            'recentViewedDiscussions' => array_map(
                fn (array $post) => ViewHelper::formatRecentView($post),
                $recentViews
            ),
            'popularDiscussions' => array_map(
                fn (array $post) => ViewHelper::formatSidebarDiscussion($post),
                $popularDiscussions
            ),
            'pagination' => $this->pagination($filters, $totalDiscussions, $currentPage),
        ]);
    }

    public function show($slug = '')
    {
        $slug = trim((string) rawurldecode($slug));

        if ($slug === '') {
            $this->notFound();
        }

        $postModel = new Post();
        $discussionRecord = $this->findDiscussion($postModel, $slug);

        if ($discussionRecord === null) {
            $this->notFound();
        }

        $discussionId = (int) ($discussionRecord['id'] ?? 0);
        $this->recordDiscussionViewOncePerSession($postModel, $discussionId);

        // Optional page sections fall back independently when one query fails.
        try {
            $discussionMediaItems = (new Media())->getByPostId($discussionId);
        } catch (Throwable) {
            $discussionMediaItems = [];
        }

        try {
            $replyRecords = (new Reply())->getByPostId($discussionId);
        } catch (Throwable) {
            $replyRecords = [];
        }

        try {
            $replyMediaRecords = (new Media())->getReplyMediaByPostId($discussionId);
        } catch (Throwable) {
            $replyMediaRecords = [];
        }

        try {
            $relatedDiscussionRecords = $postModel->getPopularDiscussions(4);
        } catch (Throwable) {
            $relatedDiscussionRecords = [];
        }

        // Flash form state must belong to this discussion before it is restored.
        $replyFormState = $_SESSION['discussion_reply_state'] ?? [];
        $hasReplyFormState = (int) ($replyFormState['post_id'] ?? 0) === $discussionId;

        $discussionEditFormState = $_SESSION['discussion_edit_state'] ?? [];
        $hasDiscussionEditFormState = (int) ($discussionEditFormState['post_id'] ?? 0) === $discussionId;

        $replyEditFormState = $_SESSION['discussion_reply_edit_state'] ?? [];
        $hasReplyEditFormState = (int) ($replyEditFormState['post_id'] ?? 0) === $discussionId;

        $requestedModalState = $_SESSION['discussion_modal_state'] ?? [];
        $hasRequestedModalState = (int) ($requestedModalState['post_id'] ?? 0) === $discussionId;

        $replyErrors = $hasReplyFormState ? ($replyFormState['errors'] ?? []) : [];
        $replyOld = array_merge([
            'content' => '',
            'parent_reply_id' => '',
        ], $hasReplyFormState ? ($replyFormState['old'] ?? []) : []);

        $discussionEditErrors = $hasDiscussionEditFormState
            ? ($discussionEditFormState['errors'] ?? [])
            : [];
        $replyEditErrors = $hasReplyEditFormState ? ($replyEditFormState['errors'] ?? []) : [];
        $replyEditOld = $hasReplyEditFormState ? ($replyEditFormState['old'] ?? []) : [];
        $activeReplyEditId = $hasReplyEditFormState
            ? (int) ($replyEditFormState['reply_id'] ?? 0)
            : 0;

        try {
            $availableModules = (new Module())->getAll();
        } catch (Throwable) {
            $availableModules = [];

            if ($hasDiscussionEditFormState) {
                $discussionEditErrors['general'] = $discussionEditErrors['general']
                    ?? 'Modules could not be loaded. Please try again.';
            }
        }

        $openModalId = '';

        if ($hasDiscussionEditFormState) {
            $openModalId = 'discussion-edit-modal';
        } elseif ($hasReplyEditFormState) {
            $openModalId = 'reply-edit-modal-' . $activeReplyEditId;
        } elseif ($hasRequestedModalState) {
            $openModalId = (string) ($requestedModalState['modal_id'] ?? '');
        }

        $replyMediaByReplyId = [];

        foreach ($replyMediaRecords as $replyMedia) {
            $replyId = (int) ($replyMedia['reply_id'] ?? 0);

            if ($replyId > 0) {
                $replyMediaByReplyId[$replyId][] = $replyMedia;
            }
        }

        // Database records are normalized once before the template receives them.
        $discussionView = $this->formatDiscussionDetail($discussionRecord, $discussionMediaItems);
        $replyViews = array_map(
            fn (array $reply) => $this->formatReply(
                $reply,
                $replyMediaByReplyId[(int) ($reply['id'] ?? 0)] ?? []
            ),
            $replyRecords
        );

        $acceptedReply = null;
        $threadReplies = [];
        $modalReplies = [];

        foreach ($replyViews as $reply) {
            $modalReplies[] = $reply;

            if (!empty($reply['is_accepted']) && $acceptedReply === null) {
                $acceptedReply = $reply;
                continue;
            }

            $threadReplies[] = $reply;
        }

        $discussionEditOld = array_merge([
            'title' => $discussionView['title'],
            'module_id' => $discussionView['module_id'],
            'content' => $discussionView['content'],
        ], $hasDiscussionEditFormState ? ($discussionEditFormState['old'] ?? []) : []);

        $discussionEditTitle = (string) $discussionEditOld['title'];
        $discussionEditModuleId = (string) $discussionEditOld['module_id'];
        $discussionEditContent = (string) $discussionEditOld['content'];

        $this->view('discussions/show', [
            'discussion' => $discussionView,
            'replies' => $threadReplies,
            'acceptedReply' => $acceptedReply,
            'modalReplies' => $modalReplies,
            'relatedDiscussions' => array_map(
                fn (array $relatedPost) => ViewHelper::formatSidebarDiscussion($relatedPost),
                $relatedDiscussionRecords
            ),
            'replyErrors' => $replyErrors,
            'replyOld' => $replyOld,
            'isLoggedIn' => $this->currentUser() !== null,
            'modules' => $availableModules,
            'discussionEditErrors' => $discussionEditErrors,
            'discussionEditTitle' => $discussionEditTitle,
            'discussionEditModuleId' => $discussionEditModuleId,
            'discussionEditContent' => $discussionEditContent,
            'replyEditErrors' => $replyEditErrors,
            'replyEditOld' => $replyEditOld,
            'activeReplyEditId' => $activeReplyEditId,
            'openModalId' => $openModalId,
            'pageScripts' => ['discussion-detail.js', 'content-input.js'],
        ]);

        // Consume only the flash state restored on this request.
        if ($hasReplyFormState) {
            unset($_SESSION['discussion_reply_state']);
        }

        if ($hasDiscussionEditFormState) {
            unset($_SESSION['discussion_edit_state']);
        }

        if ($hasReplyEditFormState) {
            unset($_SESSION['discussion_reply_edit_state']);
        }

        if ($hasRequestedModalState) {
            unset($_SESSION['discussion_modal_state']);
        }
    }

    // Request filters and pagination
    private function requestFilters()
    {
        $status = trim((string) ($_GET['status'] ?? ''));
        $sort = trim((string) ($_GET['sort'] ?? ''));
        $module = trim((string) ($_GET['module'] ?? ''));

        return [
            'q' => FormatHelper::shortText(trim((string) ($_GET['q'] ?? '')), 100),
            'status' => in_array($status, ['open', 'solved'], true) ? $status : '',
            'module' => FormatHelper::shortText($module, 30),
            'sort' => $sort === 'popular' ? 'popular' : '',
            'page' => max(1, (int) ($_GET['page'] ?? 1)),
        ];
    }

    private function statusFilters(array $filters)
    {
        return [
            [
                'label' => 'All',
                'url' => FormatHelper::discussionUrl($filters, ['status' => null, 'sort' => null, 'page' => null]),
                'active' => ($filters['status'] ?? '') === '' && ($filters['sort'] ?? '') !== 'popular',
            ],
            [
                'label' => 'Open',
                'url' => FormatHelper::discussionUrl($filters, ['status' => 'open', 'sort' => null, 'page' => null]),
                'active' => ($filters['status'] ?? '') === 'open',
            ],
            [
                'label' => 'Solved',
                'url' => FormatHelper::discussionUrl($filters, ['status' => 'solved', 'sort' => null, 'page' => null]),
                'active' => ($filters['status'] ?? '') === 'solved',
            ],
            [
                'label' => 'Popular',
                'url' => FormatHelper::discussionUrl($filters, ['status' => null, 'sort' => 'popular', 'page' => null]),
                'active' => ($filters['sort'] ?? '') === 'popular',
            ],
        ];
    }

    private function pagination(array $filters, int $totalDiscussions, int $currentPage)
    {
        $totalPages = max(1, ceil($totalDiscussions / self::PAGE_LIMIT));

        return [
            'current' => min($currentPage, $totalPages),
            'total' => $totalPages,
            'has_previous' => $currentPage > 1,
            'has_next' => $currentPage < $totalPages,
            'previous_url' => FormatHelper::discussionUrl($filters, ['page' => $currentPage > 2 ? $currentPage - 1 : null]),
            'next_url' => FormatHelper::discussionUrl($filters, ['page' => $currentPage + 1]),
        ];
    }

    // View data formatters
    private function formatDiscussionDetail(array $post, array $mediaItems = [])
    {
        $status = (string) ($post['status'] ?? 'open');
        $title = FormatHelper::textOr($post['title'] ?? '', 'Untitled question');
        $moduleCode = FormatHelper::textOr($post['module_code'] ?? '', 'MODULE');
        $content = FormatHelper::textOr($post['content'] ?? '', 'No content is available for this discussion.');

        return [
            'id' => (int) ($post['id'] ?? 0),
            'title' => $title,
            'content' => $content,
            'content_segments' => $this->contentSegments($content),
            'module' => $moduleCode,
            'module_name' => FormatHelper::textOr($post['module_name'] ?? '', 'Module discussion'),
            'module_url' => BASE_URL . '/discussions?module=' . rawurlencode($moduleCode),
            'status' => $status === 'solved' ? 'Solved' : 'Open',
            'status_tone' => $status === 'solved' ? 'green' : 'neutral',
            'created_at' => FormatHelper::textOr(FormatHelper::relativeTime((string) ($post['created_at'] ?? '')), 'Recently'),
            'updated_at' => FormatHelper::textOr(FormatHelper::relativeTime((string) ($post['updated_at'] ?? '')), 'Recently'),
            'author' => $this->authorName($post),
            'author_handle' => FormatHelper::authorHandle($post),
            'avatar' => FormatHelper::authorInitial($post),
            'replies' => (int) ($post['reply_count'] ?? 0),
            'views' => FormatHelper::compactNumber((int) ($post['view_count'] ?? 0)),
            'back_url' => BASE_URL . '/discussions',
            'slug' => (string) ($post['slug'] ?? $post['id'] ?? ''),
            'module_id' => (int) ($post['module_id'] ?? 0),
            'attachments' => array_map(fn (array $media) => $this->formatAttachment($media), $mediaItems),
            'can_edit' => $this->canEditPost($post),
            'can_delete' => $this->canEditPost($post),
            'edit_url' => BASE_URL . '/discussions/edit/' . (int) ($post['id'] ?? 0),
            'delete_url' => BASE_URL . '/discussions/delete/' . (int) ($post['id'] ?? 0),
            'update_url' => BASE_URL . '/discussions/update/' . (int) ($post['id'] ?? 0),
            'destroy_url' => BASE_URL . '/discussions/destroy/' . (int) ($post['id'] ?? 0),
        ];
    }

    private function contentSegments(string $content)
    {
        $parts = preg_split('/(<pre\b[^>]*>.*?<\/pre>)/is', $content, -1, PREG_SPLIT_DELIM_CAPTURE);

        if ($parts === false) {
            return [
                [
                    'type' => 'text',
                    'content' => $content,
                ],
            ];
        }

        $segments = [];

        foreach ($parts as $part) {
            if ($part === '') {
                continue;
            }

            if (preg_match('/^<pre\b[^>]*>(.*?)<\/pre>$/is', $part, $matches) === 1) {
                $codeContent = preg_replace('/^\R|\R$/', '', $matches[1]);

                $segments[] = [
                    'type' => 'code',
                    'content' => $codeContent ?? $matches[1],
                ];
                continue;
            }

            $segments[] = [
                'type' => 'text',
                'content' => $part,
            ];
        }

        return $segments;
    }

    private function formatReply(array $reply, array $mediaItems = [])
    {
        $role = trim((string) ($reply['role'] ?? 'student'));

        return [
            'id' => (int) ($reply['id'] ?? 0),
            'post_id' => (int) ($reply['post_id'] ?? 0),
            'parent_reply_id' => (int) ($reply['parent_reply_id'] ?? 0),
            'user_id' => (int) ($reply['user_id'] ?? 0),
            'content' => FormatHelper::textOr($reply['content'] ?? '', 'No reply content is available.'),
            'author' => $this->authorName($reply),
            'author_username' => trim((string) ($reply['username'] ?? '')),
            'author_handle' => FormatHelper::authorHandle($reply),
            'parent_author_username' => trim((string) ($reply['parent_author_username'] ?? '')),
            'parent_author_name' => trim((string) ($reply['parent_author_name'] ?? '')),
            'avatar' => FormatHelper::authorInitial($reply),
            'role' => $role !== '' ? ucfirst($role) : 'Student',
            'created_at' => FormatHelper::textOr(FormatHelper::relativeTime((string) ($reply['created_at'] ?? '')), 'Recently'),
            'is_accepted' => (int) ($reply['is_accepted'] ?? 0) === 1,
            'attachments' => array_map(fn (array $media) => $this->formatAttachment($media), $mediaItems),
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
        $originalName = FormatHelper::textOr($media['original_name'] ?? '', basename($path));
        $type = trim((string) ($media['type'] ?? 'document'));

        return [
            'type' => in_array($type, ['image', 'video', 'document'], true) ? $type : 'document',
            'name' => $originalName,
            'url' => FormatHelper::mediaUrl($path) ?? '',
            'mime_type' => trim((string) ($media['mime_type'] ?? '')),
            'size' => FormatHelper::formatFileSize((int) ($media['file_size'] ?? 0)),
        ];
    }

    private function canEditPost(array $post)
    {
        return PermissionHelper::canEditPost($this->currentUser(), $post);
    }

    private function canEditReply(array $reply)
    {
        return PermissionHelper::canEditReply($this->currentUser(), $reply);
    }

    private function canDeleteReply(array $reply)
    {
        return PermissionHelper::canDeleteReply($this->currentUser(), $reply);
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

    private function authorName(array $post)
    {
        return FormatHelper::textOr($post['full_name'] ?? $post['username'] ?? '', 'Student');
    }

    private function recordDiscussionViewOncePerSession(Post $postModel, int $discussionId)
    {
        if ($discussionId <= 0) {
            return;
        }

        if (!isset($_SESSION['viewed_posts']) || !is_array($_SESSION['viewed_posts'])) {
            $_SESSION['viewed_posts'] = [];
        }

        $_SESSION['viewed_posts'] = array_values(array_unique(array_map(
            'intval',
            $_SESSION['viewed_posts']
        )));

        if (in_array($discussionId, $_SESSION['viewed_posts'], true)) {
            return;
        }

        try {
            $postModel->recordView($discussionId, $this->currentUserId());
            $_SESSION['viewed_posts'][] = $discussionId;
        } catch (Throwable) {
            return;
        }
    }
}
