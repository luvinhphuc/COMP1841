<?php

namespace App\Controllers\Admin;

use App\Controllers\AdminController;
use App\Repositories\ModuleRepository;
use App\Repositories\PostRepository;
use App\Services\DiscussionDeletionService;
use Throwable;

/**
 * Lists and moderates discussions across all account owners.
 */
class PostController extends AdminController
{
    // Administrative route actions ---------------------------------------
    public function index()
    {
        $this->requireAdmin();
        $filters = [
            'q' => trim((string)($_GET['q'] ?? '')),
            'status' => in_array($_GET['status'] ?? '', ['open', 'solved'], true) ? $_GET['status'] : '',
            'module' => trim((string)($_GET['module'] ?? '')),
            'sort' => 'latest',
        ];
        $page = $this->pageNumber();
        $pageLimit = $this->pageLimit();

        try {
            $postRepository = new PostRepository();
            $moduleRepository = new ModuleRepository();
            $total = (int)$postRepository->countDiscussions($filters);
            $pagination = $this->pagination('/admin/posts', $total, $page, $filters, $pageLimit);
            $postRecords = $postRepository->findDiscussions(
                $filters,
                $pageLimit,
                $pagination['offset']
            );
            $modules = $moduleRepository->findAll();
        } catch (Throwable) {
            $postRecords = [];
            $modules = [];
            $pagination = $this->pagination('/admin/posts', 0, 1, $filters, $pageLimit);
        }

        $this->view('admin/posts', [
            'adminSection' => 'posts',
            'pageTitle' => 'Manage Discussions',
            'pageScripts' => ['modal.js'],
            'discussions' => $postRecords,
            'modules' => $modules,
            'filters' => $filters,
            'pagination' => $pagination,
        ]);
    }

    public function delete($postId = 0)
    {
        $this->requireAdmin();
        $this->requirePost(BASE_URL . '/admin/posts');

        try {
            $deletionResult = (new DiscussionDeletionService())->delete((int)$postId);

            if (!$deletionResult['deleted']) {
                $this->adminError('/admin/posts', 'The discussion could not be deleted.');
            }

            if (!$deletionResult['cleanup_complete']) {
                $this->redirectWithToast(BASE_URL . '/admin/posts', [
                    'type' => 'warning',
                    'title' => 'Attachment cleanup incomplete',
                    'message' => 'The discussion was deleted, but some attachment files could not be removed.',
                ]);
            }
        } catch (Throwable) {
            $this->adminError('/admin/posts', 'The discussion could not be deleted.');
        }

        $this->adminSuccess('/admin/posts', 'Discussion deleted successfully.');
    }
}
