<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Helpers\PermissionHelper;
use App\Repositories\ModuleRepository;
use App\Repositories\PostRepository;
use App\Repositories\UserRepository;
use Throwable;

/**
 * Supplies authorization, pagination, and feedback helpers shared by admin controllers.
 */
class AdminController extends Controller
{
    protected const DEFAULT_PAGE_LIMIT = 5;
    protected const PAGE_LIMIT_OPTIONS = [5, 10, 20, 50];

    public function index()
    {
        $this->requireAdmin();

        try {
            $userCount = (new UserRepository())->countAll();
            $moduleCount = (new ModuleRepository())->countAll();
            $discussionCounts = (new PostRepository())->countByStatus();
        } catch (Throwable) {
            $userCount = 0;
            $moduleCount = 0;
            $discussionCounts = ['total' => 0, 'open' => 0, 'solved' => 0];
        }

        $this->view('admin/index', [
            'adminSection' => 'overview',
            'pageTitle' => 'Admin Overview',
            'userCount' => $userCount,
            'moduleCount' => $moduleCount,
            'discussionCounts' => $discussionCounts,
        ]);
    }

    protected function requireAdmin()
    {
        $authUser = $this->currentUser();

        if ($authUser === null) {
            $this->redirectTo(BASE_URL . '/login');
        }

        if (!PermissionHelper::isAdmin($authUser)) {
            $this->redirectWithToast(BASE_URL . '/discussions', [
                'type' => 'error',
                'title' => 'Permission denied',
                'message' => 'Only administrators can access the admin area.',
            ]);
        }

        return $authUser;
    }

    protected function pageNumber(): int
    {
        $page = filter_var($_GET['page'] ?? 1, FILTER_VALIDATE_INT);

        return $page !== false && $page > 0 ? $page : 1;
    }

    protected function pageLimit(): int
    {
        $pageLimit = filter_var($_GET['per_page'] ?? self::DEFAULT_PAGE_LIMIT, FILTER_VALIDATE_INT);

        return in_array($pageLimit, self::PAGE_LIMIT_OPTIONS, true)
            ? $pageLimit
            : self::DEFAULT_PAGE_LIMIT;
    }

    protected function pagination(
        string $path,
        int $totalItems,
        int $currentPage,
        array $query = [],
        int $pageLimit = self::DEFAULT_PAGE_LIMIT
    ): array
    {
        $totalPages = max(1, (int)ceil($totalItems / $pageLimit));
        $currentPage = min(max(1, $currentPage), $totalPages);
        $pageWindowSize = 5;
        $firstPage = max(1, min($currentPage - 2, $totalPages - $pageWindowSize + 1));
        $lastPage = min($totalPages, $firstPage + $pageWindowSize - 1);
        $paginationQuery = array_merge($query, ['per_page' => $pageLimit]);
        $pages = [];

        for ($page = $firstPage; $page <= $lastPage; $page++) {
            $pages[] = [
                'number' => $page,
                'url' => $this->pageUrl($path, $paginationQuery, $page),
                'current' => $page === $currentPage,
            ];
        }

        return [
            'current' => $currentPage,
            'total' => $totalPages,
            'total_items' => $totalItems,
            'per_page' => $pageLimit,
            'per_page_options' => self::PAGE_LIMIT_OPTIONS,
            'offset' => ($currentPage - 1) * $pageLimit,
            'pages' => $pages,
            'has_previous' => $currentPage > 1,
            'has_next' => $currentPage < $totalPages,
            'previous_url' => $this->pageUrl($path, $paginationQuery, max(1, $currentPage - 1)),
            'next_url' => $this->pageUrl($path, $paginationQuery, min($totalPages, $currentPage + 1)),
            'path' => BASE_URL . $path,
            'query' => array_filter(
                $query,
                static fn($value) => trim((string)$value) !== ''
            ),
        ];
    }

    protected function pageUrl(string $path, array $query, int $page): string
    {
        $query = array_filter($query, static fn($value) => trim((string)$value) !== '');
        $query['page'] = $page;

        return BASE_URL . $path . '?' . http_build_query($query);
    }

    protected function adminSuccess(string $path, string $message)
    {
        $this->redirectWithToast(BASE_URL . $path, [
            'type' => 'success',
            'title' => 'Saved',
            'message' => $message,
        ]);
    }

    protected function adminError(string $path, string $message)
    {
        $this->redirectWithToast(BASE_URL . $path, [
            'type' => 'error',
            'title' => 'Unable to save',
            'message' => $message,
        ]);
    }
}
