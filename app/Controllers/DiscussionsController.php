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
            'discussions' => array_map(fn (array $post) => $this->formatDiscussion($post), $posts),
            'totalDiscussions' => $totalDiscussions,
            'filters' => $filters,
            'statusFilters' => $this->statusFilters($filters),
            'moduleChips' => ViewHelper::moduleChips($modules, $filters),
            'matchedModules' => ViewHelper::matchedModules($modules, $filters),
            'trendingModules' => ViewHelper::formatTrendingModules($trendingModules),
            'recentViewedDiscussions' => array_map(fn (array $post) => $this->formatRecentView($post), $recentViews),
            'popularDiscussions' => array_map(fn (array $post) => $this->formatSidebarDiscussion($post), $popularDiscussions),
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
        $post = $this->findDiscussion($postModel, $slug);

        if ($post === null) {
            $this->notFound();
        }

        $postId = (int) ($post['id'] ?? 0);
        $this->recordViewOncePerSession($postModel, $postId);

        try {
            $mediaItems = (new Media())->getByPostId($postId);
        } catch (Throwable) {
            $mediaItems = [];
        }

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

        $replyErrors = $hasReplyState ? ($replyState['errors'] ?? []) : [];
        $replyOld = array_merge([
            'content' => '',
            'parent_reply_id' => '',
        ], $hasReplyState ? ($replyState['old'] ?? []) : []);

        $discussionEditErrors = $hasDiscussionEditState ? ($discussionEditState['errors'] ?? []) : [];
        $replyEditErrors = $hasReplyEditState ? ($replyEditState['errors'] ?? []) : [];
        $replyEditOld = $hasReplyEditState ? ($replyEditState['old'] ?? []) : [];
        $activeReplyEditId = $hasReplyEditState ? (int) ($replyEditState['reply_id'] ?? 0) : 0;

        try {
            $modules = (new Module())->getAll();
        } catch (Throwable) {
            $modules = [];

            if ($hasDiscussionEditState) {
                $discussionEditErrors['general'] = $discussionEditErrors['general']
                    ?? 'Modules could not be loaded. Please try again.';
            }
        }

        $openModalId = '';

        if ($hasDiscussionEditState) {
            $openModalId = 'discussion-edit-modal';
        } elseif ($hasReplyEditState) {
            $openModalId = 'reply-edit-modal-' . $activeReplyEditId;
        } elseif ($hasModalState) {
            $openModalId = (string) ($modalState['modal_id'] ?? '');
        }

        $discussion = $this->formatDiscussionDetail($post, $mediaItems);
        $formattedReplies = array_map(fn (array $reply) => $this->formatReply($reply), $replies);

        $acceptedReply = null;
        $threadReplies = [];
        $modalReplies = [];

        foreach ($formattedReplies as $reply) {
            $modalReplies[] = $reply;

            if (!empty($reply['is_accepted']) && $acceptedReply === null) {
                $acceptedReply = $reply;
                continue;
            }

            $threadReplies[] = $reply;
        }

        $discussionEditOld = array_merge([
            'title' => $discussion['title'] ?? '',
            'module_id' => $discussion['module_id'] ?? '',
            'content' => $discussion['content'] ?? '',
        ], $hasDiscussionEditState ? ($discussionEditState['old'] ?? []) : []);

        $discussionEditTitle = (string) ($discussionEditOld['title'] ?? '');
        $discussionEditModuleId = (string) ($discussionEditOld['module_id'] ?? '');
        $discussionEditContent = (string) ($discussionEditOld['content'] ?? '');

        $this->view('discussions/show', [
            'discussion' => $discussion,

            // Main reply list after moving accepted reply out.
            'replies' => $threadReplies,

            // Reply data for accepted block and modals.
            'acceptedReply' => $acceptedReply,
            'modalReplies' => $modalReplies,

            'relatedDiscussions' => array_map(
                fn (array $relatedPost) => $this->formatSidebarDiscussion($relatedPost),
                $relatedDiscussions
            ),

            'replyErrors' => $replyErrors,
            'replyOld' => $replyOld,

            'statusTone' => $discussion['status_tone'] ?? 'neutral',
            'replyCount' => (int) ($discussion['replies'] ?? count($formattedReplies)),
            'attachments' => $discussion['attachments'] ?? [],
            'isLoggedIn' => $this->currentUser() !== null,

            'modules' => $modules,

            'discussionEditErrors' => $discussionEditErrors,
            'discussionEditOld' => $discussionEditOld,
            'discussionEditTitle' => $discussionEditTitle,
            'discussionEditModuleId' => $discussionEditModuleId,
            'discussionEditContent' => $discussionEditContent,

            'replyEditErrors' => $replyEditErrors,
            'replyEditOld' => $replyEditOld,
            'activeReplyEditId' => $activeReplyEditId,

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
    private function formatDiscussion(array $post)
    {
        $status = (string) ($post['status'] ?? 'open');
        $title = FormatHelper::textOr($post['title'] ?? '', 'Untitled question');

        return [
            'module' => FormatHelper::textOr($post['module_code'] ?? '', 'MODULE'),
            'module_name' => FormatHelper::textOr($post['module_name'] ?? '', 'Module discussion'),
            'status' => $status === 'solved' ? 'Solved' : 'Open',
            'status_tone' => $status === 'solved' ? 'green' : 'neutral',
            'created_at' => FormatHelper::textOr(FormatHelper::relativeTime((string) ($post['created_at'] ?? '')), 'Recently'),
            'title' => $title,
            'excerpt' => FormatHelper::textOr($this->excerpt((string) ($post['content'] ?? ''), 180), 'No preview is available yet.'),
            'author' => $this->authorName($post),
            'author_handle' => FormatHelper::authorHandle($post),
            'avatar' => FormatHelper::authorInitial($post),
            'replies' => (int) ($post['reply_count'] ?? 0),
            'views' => FormatHelper::compactNumber((int) ($post['view_count'] ?? 0)),
            'image' => FormatHelper::mediaUrl($post['media_path'] ?? null),
            'media_type' => trim((string) ($post['media_type'] ?? '')),
            'preview_alt' => 'Preview for ' . $title,
            'url' => BASE_URL . '/discussions/' . rawurlencode((string) ($post['slug'] ?? $post['id'] ?? '')),
        ];
    }

    private function formatRecentView(array $post)
    {
        return [
            'title' => FormatHelper::textOr($post['title'] ?? '', 'Untitled question'),
            'module' => FormatHelper::textOr($post['module_code'] ?? '', 'MODULE'),
            'time' => FormatHelper::textOr(FormatHelper::relativeTime((string) ($post['viewed_at'] ?? '')), 'Recently'),
            'url' => BASE_URL . '/discussions/' . rawurlencode((string) ($post['slug'] ?? $post['id'] ?? '')),
        ];
    }

    private function formatSidebarDiscussion(array $post)
    {
        return [
            'title' => FormatHelper::textOr($post['title'] ?? '', 'Untitled question'),
            'replies' => (int) ($post['reply_count'] ?? 0),
            'url' => BASE_URL . '/discussions/' . rawurlencode((string) ($post['slug'] ?? $post['id'] ?? '')),
        ];
    }

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

    private function formatReply(array $reply)
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
            'url' => FormatHelper::mediaUrl($path),
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

    private function excerpt(string $content, int $limit)
    {
        $content = trim(strip_tags($content));

        if (strlen($content) <= $limit) {
            return $content;
        }

        return rtrim(substr($content, 0, $limit - 3)) . '...';
    }

    private function recordViewOncePerSession(Post $postModel, int $postId)
    {
        if ($postId <= 0) {
            return;
        }

        if (!isset($_SESSION['viewed_posts']) || !is_array($_SESSION['viewed_posts'])) {
            $_SESSION['viewed_posts'] = [];
        }

        $_SESSION['viewed_posts'] = array_values(array_unique(array_map(
            'intval',
            $_SESSION['viewed_posts']
        )));

        if (in_array($postId, $_SESSION['viewed_posts'], true)) {
            return;
        }

        try {
            $postModel->recordView($postId, $this->currentUserId());
            $_SESSION['viewed_posts'][] = $postId;
        } catch (Throwable) {
            return;
        }
    }
}
