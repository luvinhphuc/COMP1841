<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Helpers\FormatHelper;
use App\Helpers\PermissionHelper;
use App\Helpers\ViewHelper;
use App\Repositories\MediaRepository;
use App\Repositories\ModuleRepository;
use App\Repositories\PostRepository;
use App\Repositories\ReplyRepository;
use Throwable;

/**
 * Builds discussion listing and detail pages from repository records.
 */
class DiscussionController extends Controller
{
    private const DEFAULT_PAGE_LIMIT = 5;
    private const PAGE_LIMIT_OPTIONS = [5, 10, 20, 50];

    // Route actions --------------------------------------------------------
    public function index()
    {
        $filters = $this->requestFilters();
        $postRepository = new PostRepository();
        $moduleRepository = new ModuleRepository();
        $currentPage = max(1, ($filters['page'] ?? 1));
        $pageLimit = $filters['per_page'];
        $offset = ($currentPage - 1) * $pageLimit;
        $user = $this->currentUser();

        [$postRecords, $totalDiscussions] = $this->loadDiscussionResults(
            $postRepository,
            $filters,
            $offset,
            $pageLimit
        );

        $totalPages = max(1, (int)ceil($totalDiscussions / $pageLimit));

        if ($currentPage > $totalPages) {
            $currentPage = $totalPages;
            $filters['page'] = $currentPage;
            [$postRecords, $totalDiscussions] = $this->loadDiscussionResults(
                $postRepository,
                $filters,
                ($currentPage - 1) * $pageLimit,
                $pageLimit
            );
        }

        $sidebarData = $this->loadDiscussionSidebarData($postRepository, $moduleRepository);
        $modules = $this->loadFilterModules($moduleRepository);

        $this->view('discussions/index', [
            'pageTitle' => 'Discussions - ',
            'pageScripts' => ['discussion-filters.js'],
            'discussions' => array_map(
                fn(array $postRecord) => ViewHelper::formatDiscussionCard($postRecord),
                $postRecords
            ),
            'totalDiscussions' => $totalDiscussions,
            'filters' => $filters,
            'moduleChips' => ViewHelper::moduleChips($modules, $filters),
            'matchedModules' => ViewHelper::matchedModules($modules, $filters),
            'trendingModules' => ViewHelper::formatTrendingModules($sidebarData['trending_modules']),
            'popularDiscussions' => array_map(
                fn(array $postRecord) => ViewHelper::formatSidebarDiscussion($postRecord),
                $sidebarData['popular_post_records']
            ),
            'pagination' => $this->pagination($filters, $totalDiscussions, $currentPage),
            'userFullName' => $this->currentUser()['full_name'] ?? 'Student',
        ]);
    }

    public function show($identifier = '', $slug = '')
    {
        $identifier = trim((string)rawurldecode($identifier));
        $slug = trim((string)rawurldecode($slug));

        if ($identifier === '') {
            $this->notFound();
        }

        $postRepository = new PostRepository();

        if ($slug === '') {
            $postRecord = $this->findLegacyPostRecord($postRepository, $identifier);

            if ($postRecord === null) {
                $this->notFound();
            }

            $this->redirectToCanonicalDiscussion($postRecord);
        }

        $discussionId = ctype_digit($identifier) ? (int)$identifier : 0;

        if ($discussionId <= 0) {
            $this->notFound();
        }

        try {
            $postRecord = $postRepository->findDetailsById($discussionId);
        } catch (Throwable) {
            $postRecord = null;
        }

        if ($postRecord === null) {
            $this->notFound();
        }

        $canonicalSlug = trim((string)($postRecord['slug'] ?? ''));
        $canonicalSlug = $canonicalSlug !== '' ? $canonicalSlug : 'post';

        if ($slug !== $canonicalSlug) {
            $this->redirectToCanonicalDiscussion($postRecord);
        }

        $this->recordDiscussionViewOncePerSession($postRepository, $discussionId);

        try {
            $postMediaRecords = (new MediaRepository())->findByPostId($discussionId);
        } catch (Throwable) {
            $postMediaRecords = [];
        }

        try {
            $replyRecords = (new ReplyRepository())->findByPostId($discussionId);
        } catch (Throwable) {
            $replyRecords = [];
        }

        try {
            $replyMediaRecords = (new MediaRepository())->findReplyMediaByPostId($discussionId);
        } catch (Throwable) {
            $replyMediaRecords = [];
        }

        $replyFormState = $_SESSION['discussion_reply_state'] ?? [];
        $hasReplyFormState = (int)($replyFormState['post_id'] ?? 0) === $discussionId;

        $discussionEditFormState = $_SESSION['discussion_edit_state'] ?? [];
        $hasDiscussionEditFormState = (int)($discussionEditFormState['post_id'] ?? 0) === $discussionId;

        $replyEditFormState = $_SESSION['discussion_reply_edit_state'] ?? [];
        $hasReplyEditFormState = (int)($replyEditFormState['post_id'] ?? 0) === $discussionId;

        $requestedModalState = $_SESSION['discussion_modal_state'] ?? [];
        $hasRequestedModalState = (int)($requestedModalState['post_id'] ?? 0) === $discussionId;

        $replyErrors = $hasReplyFormState ? ($replyFormState['errors'] ?? []) : [];
        $replyOld = array_merge([
            'content' => '',
            'parent_reply_id' => '',
        ], $hasReplyFormState ? ($replyFormState['old'] ?? []) : []);

        $discussionEditErrors = $hasDiscussionEditFormState ? ($discussionEditFormState['errors'] ?? []) : [];
        $replyEditErrors = $hasReplyEditFormState ? ($replyEditFormState['errors'] ?? []) : [];
        $replyEditOld = $hasReplyEditFormState ? ($replyEditFormState['old'] ?? []) : [];
        $activeReplyEditId = $hasReplyEditFormState
            ? (int)($replyEditFormState['reply_id'] ?? 0)
            : 0;

        try {
            $availableModules = (new ModuleRepository())->findAll();
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
            $openModalId = (string)($requestedModalState['modal_id'] ?? '');
        }

        $replyMediaByReplyId = [];

        foreach ($replyMediaRecords as $replyMedia) {
            $replyId = (int)($replyMedia['reply_id'] ?? 0);

            if ($replyId > 0) {
                $replyMediaByReplyId[$replyId][] = $replyMedia;
            }
        }

        $discussion = $this->formatDiscussion($postRecord, $postMediaRecords);
        $formattedReplies = array_map(
            fn(array $replyRecord) => $this->formatReply(
                $replyRecord,
                $replyMediaByReplyId[(int)($replyRecord['id'] ?? 0)] ?? [],
                $postRecord
            ),
            $replyRecords
        );

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
            'title' => $discussion['title'],
            'module_id' => $discussion['module_id'],
            'content' => $discussion['content'],
        ], $hasDiscussionEditFormState ? ($discussionEditFormState['old'] ?? []) : []);

        $this->view('discussions/show', [
            'discussion' => $discussion,
            'replies' => $threadReplies,
            'acceptedReply' => $acceptedReply,
            'modalReplies' => $modalReplies,
            'replyErrors' => $replyErrors,
            'replyOld' => $replyOld,
            'isLoggedIn' => $this->currentUser() !== null,
            'modules' => $availableModules,
            'discussionEditErrors' => $discussionEditErrors,
            'discussionEditTitle' => (string)$discussionEditOld['title'],
            'discussionEditModuleId' => (string)$discussionEditOld['module_id'],
            'discussionEditContent' => (string)$discussionEditOld['content'],
            'replyEditErrors' => $replyEditErrors,
            'replyEditOld' => $replyEditOld,
            'activeReplyEditId' => $activeReplyEditId,
            'openModalId' => $openModalId,
            'hasImageViewer' => true,
            'pageScripts' => [
                'discussion-detail.js',
                'content-input.js',
                'attachment.js',
                'image-viewer.js',
                'modal.js',
            ],
        ]);

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

    // Listing and filters
    private function loadDiscussionResults(
        PostRepository $postRepository,
        array          $filters,
        int            $offset,
        int            $pageLimit
    )
    {
        try {
            return [
                $postRepository->findDiscussions($filters, $pageLimit, $offset),
                $postRepository->countDiscussions($filters),
            ];
        } catch (Throwable $exception) {
            error_log($exception->getMessage());

            return [[], 0];
        }
    }

    private function loadDiscussionSidebarData(
        PostRepository   $postRepository,
        ModuleRepository $moduleRepository
    )
    {
        try {
            $trendingModules = $moduleRepository->findTrending(3);
        } catch (Throwable $exception) {
            error_log($exception->getMessage());
            $trendingModules = [];
        }

        try {
            $popularPostRecords = $postRepository->findPopularDiscussions(3);
        } catch (Throwable $exception) {
            error_log($exception->getMessage());
            $popularPostRecords = [];
        }

        return [
            'trending_modules' => $trendingModules,
            'popular_post_records' => $popularPostRecords,
        ];
    }

    private function loadFilterModules(ModuleRepository $moduleRepository)
    {
        try {
            return $moduleRepository->findAll();
        } catch (Throwable $exception) {
            error_log($exception->getMessage());

            return [];
        }
    }

    private function requestFilters()
    {
        $status = trim((string)($_GET['status'] ?? ''));
        $sort = trim((string)($_GET['sort'] ?? ''));
        $moduleCode = trim((string)($_GET['module'] ?? ''));
        $pageLimit = filter_var(
            $_GET['per_page'] ?? self::DEFAULT_PAGE_LIMIT,
            FILTER_VALIDATE_INT
        );

        return [
            'q' => FormatHelper::shortText(trim((string)($_GET['q'] ?? '')), 100),
            'status' => in_array($status, ['open', 'solved'], true) ? $status : '',
            'module' => FormatHelper::shortText($moduleCode, 30),
            'sort' => $sort === 'popular' ? 'popular' : '',
            'page' => max(1, (int)($_GET['page'] ?? 1)),
            'per_page' => in_array($pageLimit, self::PAGE_LIMIT_OPTIONS, true)
                ? $pageLimit
                : self::DEFAULT_PAGE_LIMIT,
        ];
    }

    private function pagination(array $filters, int $totalDiscussions, int $currentPage)
    {
        $pageLimit = $filters['per_page'] ?? self::DEFAULT_PAGE_LIMIT;
        $totalPages = max(1, (int)ceil($totalDiscussions / $pageLimit));
        $currentPage = min(max(1, $currentPage), $totalPages);
        $firstPage = max(1, min($currentPage - 2, $totalPages - 4));
        $lastPage = min($totalPages, $firstPage + 4);
        $paginationPages = [];

        for ($paginationPage = $firstPage; $paginationPage <= $lastPage; $paginationPage++) {
            $paginationPages[] = [
                'number' => $paginationPage,
                'url' => FormatHelper::discussionUrl(
                    $filters,
                    ['page' => $paginationPage > 1 ? $paginationPage : null]
                ),
                'current' => $paginationPage === $currentPage,
            ];
        }

        $formQuery = $filters;
        unset($formQuery['page'], $formQuery['per_page']);
        $formQuery = array_filter(
            $formQuery,
            static fn($filterValue) => trim((string)$filterValue) !== ''
        );

        return [
            'current' => $currentPage,
            'total' => $totalPages,
            'total_items' => $totalDiscussions,
            'per_page' => $pageLimit,
            'per_page_options' => self::PAGE_LIMIT_OPTIONS,
            'pages' => $paginationPages,
            'has_previous' => $currentPage > 1,
            'has_next' => $currentPage < $totalPages,
            'previous_url' => FormatHelper::discussionUrl($filters, ['page' => $currentPage > 2 ? $currentPage - 1 : null]),
            'next_url' => FormatHelper::discussionUrl($filters, ['page' => min($totalPages, $currentPage + 1)]),
            'path' => BASE_URL . '/discussions',
            'query' => $formQuery,
        ];
    }

    // Formatting
    private function formatDiscussion(array $postRecord, array $mediaRecords = [])
    {
        $status = (string)($postRecord['status'] ?? 'open');
        $content = FormatHelper::textOr($postRecord['content'] ?? '', '');
        $moduleCode = FormatHelper::textOr($postRecord['module_code'] ?? '', 'MODULE');

        return array_merge([
            'id' => (int)($postRecord['id'] ?? 0),
            'title' => FormatHelper::textOr($postRecord['title'] ?? '', 'Untitled question'),
            'content' => $content,
            'content_segments' => $this->contentSegments($content),
            'module_code' => $moduleCode,
            'module_name' => FormatHelper::textOr($postRecord['module_name'] ?? '', 'Module discussion'),
            'module_url' => BASE_URL . '/discussions?module=' . rawurlencode($moduleCode),
            'module_id' => (int)($postRecord['module_id'] ?? 0),
            'attachments' => array_map(
                fn(array $mediaRecord) => $this->formatAttachment($mediaRecord),
                $mediaRecords
            ),
        ], $this->formatDiscussionMetadata($postRecord, $status), $this->formatDiscussionActions($postRecord));
    }

    private function formatDiscussionMetadata(array $postRecord, string $status)
    {
        return [
            'status' => $status === 'solved' ? 'Solved' : 'Open',
            'status_tone' => $status === 'solved' ? 'green' : 'neutral',
            'created_at_formatted' => FormatHelper::textOr(FormatHelper::relativeTime((string)($postRecord['created_at'] ?? '')), 'Recently'),
            'updated_at_formatted' => FormatHelper::textOr(FormatHelper::relativeTime((string)($postRecord['updated_at'] ?? '')), 'Recently'),
            'author_full_name' => $this->authorName($postRecord),
            'author_handle' => FormatHelper::authorHandle($postRecord),
            'author_initial' => FormatHelper::authorInitial($postRecord),
            'author_avatar_url' => FormatHelper::authorAvatarUrl($postRecord),
            'reply_count' => (int)($postRecord['reply_count'] ?? 0),
            'view_count' => (int)($postRecord['view_count'] ?? 0),
        ];
    }

    private function formatDiscussionActions(array $postRecord)
    {
        $discussionId = (int)($postRecord['id'] ?? 0);
        $canManageDiscussion = $this->canEditDiscussion($postRecord);

        return [
            'back_url' => BASE_URL . '/discussions',
            'slug' => (string)($postRecord['slug'] ?? $discussionId),
            'can_edit' => $canManageDiscussion,
            'can_delete' => $canManageDiscussion,
            'edit_url' => BASE_URL . '/discussions/edit/' . $discussionId,
            'delete_url' => BASE_URL . '/discussions/delete/' . $discussionId,
            'update_url' => BASE_URL . '/discussions/update/' . $discussionId,
            'destroy_url' => BASE_URL . '/discussions/destroy/' . $discussionId,
        ];
    }

    private function contentSegments(string $content)
    {
        $parts = preg_split('/(<pre\b[^>]*>.*?<\/pre>)/is', $content, -1, PREG_SPLIT_DELIM_CAPTURE);

        if ($parts === false) {
            return [['type' => 'text', 'content' => $content]];
        }

        $segments = [];

        foreach ($parts as $part) {
            if ($part === '') {
                continue;
            }


            if (preg_match('/^<pre\b[^>]*>(.*?)<\/pre>$/is', $part, $matches) === 1) {
                $codeContent = preg_replace('/^\R|\R$/', '', $matches[1]);
                $codeContent = $codeContent ?? $matches[1];
                $segments[] = [
                    'type' => 'code',
                    'language' => $this->detectCodeLanguage($codeContent),
                    'content' => $codeContent,
                ];
                continue;
            }

            $segments[] = ['type' => 'text', 'content' => $part];
        }

        return $segments;
    }

    private function detectCodeLanguage(string $code): string
    {
        $code = trim($code);

        if ($code === '') {
            return 'none';
        }

        if (preg_match('/<\?(?:php|=)/i', $code) === 1
            || preg_match('/\$[a-z_]\w*\s*(?:=|->)|\b(?:echo|namespace)\s+|->\w+\s*\(/i', $code) === 1) {
            return 'php';
        }

        if (preg_match(
            '/^\s*(?:SELECT\b[\s\S]*\bFROM\b|INSERT\s+INTO\b|UPDATE\b[\s\S]*\bSET\b|DELETE\s+FROM\b|CREATE\s+(?:TABLE|DATABASE)\b)/i',
            $code
        ) === 1) {
            return 'sql';
        }

        if (preg_match(
            '/^\s*(?:from\s+\S+\s+import\b|import\s+\S+|def\s+\w+\s*\(|class\s+\w+(?:\([^)]*\))?\s*:)|\b(?:print|len|range)\s*\(|\b(?:None|True|False)\b/m',
            $code
        ) === 1) {
            return 'python';
        }

        if (preg_match(
            '/\b(?:public|private|protected)\s+(?:static\s+)?(?:class|interface|enum|void|int|String|boolean)\b|System\.out\.(?:print|println)\s*\(|public\s+static\s+void\s+main\s*\(/',
            $code
        ) === 1) {
            return 'java';
        }

        if (($code[0] === '{' || $code[0] === '[')) {
            json_decode($code, true);

            if (json_last_error() === JSON_ERROR_NONE) {
                return 'json';
            }
        }

        if (preg_match('/<\/?[a-z][a-z0-9-]*(?:\s[^<>]*)?>/i', $code) === 1) {
            return 'markup';
        }

        if (preg_match(
            '/(?:^|\})\s*(?:[.#]?[a-z][\w-]*|\[[^\]]+\])(?:[^{}]*)\{\s*[-\w]+\s*:/im',
            $code
        ) === 1) {
            return 'css';
        }

        if (preg_match(
            '/\b(?:const|let|var)\s+[a-z_$]|\b(?:async\s+)?function\b|=>|\bconsole\.(?:log|error|warn)\s*\(|\b(?:document|window)\./i',
            $code
        ) === 1) {
            return 'javascript';
        }

        return 'none';
    }

    private function formatReply(array $replyRecord, array $mediaRecords = [], array $postRecord = [])
    {
        $role = trim((string)($replyRecord['role'] ?? 'student'));
        $replyId = (int)($replyRecord['id'] ?? 0);
        $isAccepted = (int)($replyRecord['is_accepted'] ?? 0) === 1;
        $postStatus = strtolower(trim((string)($postRecord['status'] ?? 'open')));
        $content = FormatHelper::textOr($replyRecord['content'] ?? '', 'No reply content is available.');
        $canMarkDiscussionAsSolved = PermissionHelper::canMarkDiscussionAsSolved(
            $this->currentUser(),
            $postRecord
        );

        return [
            'id' => $replyId,
            'post_id' => (int)($replyRecord['post_id'] ?? 0),
            'parent_reply_id' => (int)($replyRecord['parent_reply_id'] ?? 0),
            'user_id' => (int)($replyRecord['user_id'] ?? 0),
            'content' => $content,
            'content_segments' => $this->contentSegments($content),
            'author_full_name' => $this->authorName($replyRecord),
            'author_username' => trim((string)($replyRecord['username'] ?? '')),
            'author_handle' => FormatHelper::authorHandle($replyRecord),
            'parent_author_username' => trim((string)($replyRecord['parent_author_username'] ?? '')),
            'parent_author_full_name' => trim((string)($replyRecord['parent_author_name'] ?? '')),
            'author_initial' => FormatHelper::authorInitial($replyRecord),
            'author_avatar_url' => FormatHelper::authorAvatarUrl($replyRecord),
            'role' => $role !== '' ? ucfirst($role) : 'Student',
            'created_at_formatted' => FormatHelper::textOr(FormatHelper::relativeTime((string)($replyRecord['created_at'] ?? '')), 'Recently'),
            'is_accepted' => $isAccepted,
            'attachments' => array_map(
                fn(array $mediaRecord) => $this->formatAttachment($mediaRecord),
                $mediaRecords
            ),
            'can_edit' => $this->canEditReply($replyRecord),
            'can_delete' => $this->canDeleteReply($replyRecord),
            'can_mark_as_solved' => $canMarkDiscussionAsSolved && !$isAccepted,
            'can_unmark_as_solved' => $canMarkDiscussionAsSolved && $isAccepted && $postStatus === 'solved',
            'edit_url' => BASE_URL . '/discussions/reply-edit/' . $replyId,
            'delete_url' => BASE_URL . '/discussions/reply-delete/' . $replyId,
            'update_url' => BASE_URL . '/discussions/reply-update/' . $replyId,
            'destroy_url' => BASE_URL . '/discussions/reply-destroy/' . $replyId,
            'mark_as_solved_url' => BASE_URL . '/discussions/reply-mark-solved/' . $replyId,
            'unmark_as_solved_url' => BASE_URL . '/discussions/reply-unmark-solved/' . $replyId,
        ];
    }

    private function formatAttachment(array $mediaRecord)
    {
        $path = trim((string)($mediaRecord['path'] ?? ''));
        $type = trim((string)($mediaRecord['type'] ?? 'document'));

        return [
            'type' => in_array($type, ['image', 'video', 'document'], true) ? $type : 'document',
            'name' => FormatHelper::textOr($mediaRecord['original_name'] ?? '', basename($path)),
            'url' => FormatHelper::mediaUrl($path) ?? '',
            'mime_type' => trim((string)($mediaRecord['mime_type'] ?? '')),
            'size' => FormatHelper::formatFileSize((int)($mediaRecord['file_size'] ?? 0)),
        ];
    }

    private function findLegacyPostRecord(PostRepository $postRepository, string $id)
    {
        try {
            $postEntity = $postRepository->findBySlug($id);

            if ($postEntity !== null) {
                return $postEntity->toArray();
            }

            if (!ctype_digit($id)) {
                return null;
            }

            $postEntity = $postRepository->findById((int)$id);

            return $postEntity?->toArray();
        } catch (Throwable) {
            return null;
        }
    }

    private function redirectToCanonicalDiscussion(array $postRecord)
    {
        header('Location: ' . $this->discussionUrl($postRecord), true, 301);
        exit;
    }

    private function recordDiscussionViewOncePerSession(
        PostRepository $postRepository,
        int            $discussionId
    )
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
            $postRepository->recordView($discussionId);
            $_SESSION['viewed_posts'][] = $discussionId;
        } catch (Throwable) {
            return;
        }
    }

    // Authorization and shared utilities
    private function authorName(array $record)
    {
        return FormatHelper::textOr($record['full_name'] ?? '', 'Student');
    }

    private function canEditDiscussion(array $postRecord)
    {
        return PermissionHelper::canEditDiscussion($this->currentUser(), $postRecord);
    }

    private function canEditReply(array $reply)
    {
        return PermissionHelper::canEditReply($this->currentUser(), $reply);
    }

    private function canDeleteReply(array $reply)
    {
        return PermissionHelper::canDeleteReply($this->currentUser(), $reply);
    }

    private function discussionUrl(array $postRecord)
    {
        return FormatHelper::discussionDetailUrl($postRecord['id'] ?? 0, $postRecord['slug'] ?? '');
    }
}
