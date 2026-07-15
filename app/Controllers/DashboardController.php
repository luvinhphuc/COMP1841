<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Helpers\FormatHelper;
use App\Helpers\ViewHelper;
use App\Models\Module;
use App\Models\Post;
use App\Models\UserModule;
use Throwable;

class DashboardController extends Controller
{
    private const MODULE_LIMIT = 4;
    private const QUESTION_LIMIT = 5;
    private const MODULE_DISCUSSION_LIMIT = 5;

    public function index()
    {
        $authUser = $this->currentUser();
        $userId = $this->currentUserId();

        if (!is_array($authUser) || $userId === null) {
            $this->redirectTo(BASE_URL . '/login');
        }

        $isStudent = $this->isStudent($authUser);
        $selectedModules = [];

        if ($isStudent) {
            try {
                $selectedModules = (new UserModule())->getModulesByUserId($userId);

                if (empty($selectedModules)) {
                    $this->redirectTo(BASE_URL . '/onboarding/modules');
                }
            } catch (Throwable) {
                $this->redirectWithToast(BASE_URL . '/onboarding/modules', [
                    'type' => 'error',
                    'title' => 'Modules unavailable',
                    'message' => 'Your modules could not be checked right now. Please try again.',
                ]);
            }
        }

        [$myQuestions, $questionPagination] = $this->paginatedQuestions($userId);
        $recentQuestions = $this->recentQuestions($userId);
        $formattedModules = $this->formatModules($selectedModules);

        $this->view('dashboard/index', [
            'selectedModulePreview' => array_slice($formattedModules, 0, self::MODULE_LIMIT),
            'selectedModuleCount' => count($formattedModules),
            'selectedModuleDiscussions' => $isStudent ? $this->moduleDiscussions($userId) : [],
            'showMyModules' => $isStudent,
            'myQuestions' => $myQuestions,
            'questionPagination' => $questionPagination,
            'recentActivities' => $this->recentActivities($recentQuestions),
            'trendingModules' => $this->trendingModules(),
            'authUser' => $authUser,
            'greetingName' => $this->greetingName($authUser),
            'pageScripts' => ['home.js'],
        ]);
    }

    private function greetingName($authUser)
    {
        $firstName = trim((string) ($authUser['first_name'] ?? ''));

        if ($firstName !== '') {
            return $firstName;
        }

        $fullName = trim((string) ($authUser['full_name'] ?? $authUser['name'] ?? ''));

        if ($fullName !== '') {
            $nameParts = preg_split('/\s+/', $fullName, 2);
            $name = trim((string) ($nameParts[0] ?? ''));

            if ($name !== '') {
                return $name;
            }
        }

        $username = trim((string) ($authUser['username'] ?? ''));

        return $username !== '' ? $username : 'Student';
    }

    private function formatModules(array $modules)
    {
        $modules = array_map(function (array $module) {
            $code = trim((string) ($module['code'] ?? ''));
            $discussionCount = (int) ($module['discussion_count'] ?? 0);

            if ($code === '') {
                return [];
            }

            return [
                'url' => $this->moduleUrl($code),
                'code' => $code,
                'name' => FormatHelper::textOr($module['name'] ?? '', 'Untitled module'),
                'discussion_count_label' => $discussionCount . ' '
                    . ($discussionCount === 1 ? 'discussion' : 'discussions'),
            ];
        }, $modules);

        return array_values(array_filter($modules));
    }

    private function moduleDiscussions(int $userId)
    {
        try {
            $posts = (new Post())->getLatestForUserModules($userId, self::MODULE_DISCUSSION_LIMIT);
        } catch (Throwable) {
            return [];
        }

        return array_map(fn (array $post) => ViewHelper::formatPostCard($post), $posts);
    }

    private function paginatedQuestions(int $userId): array
    {
        $requestedPage = max(1, (int) ($_GET['page'] ?? 1));

        try {
            $postModel = new Post();
            $totalQuestions = $postModel->countByUserId($userId);
            $totalPages = max(1, (int) ceil($totalQuestions / self::QUESTION_LIMIT));
            $currentPage = min($requestedPage, $totalPages);
            $offset = ($currentPage - 1) * self::QUESTION_LIMIT;
            $posts = $postModel->getByUserId($userId, self::QUESTION_LIMIT, $offset);
        } catch (Throwable) {
            $posts = [];
            $currentPage = 1;
            $totalPages = 1;
        }

        return [
            array_map(fn (array $post) => ViewHelper::formatPostCard($post), $posts),
            [
                'current' => $currentPage,
                'total' => $totalPages,
                'has_previous' => $currentPage > 1,
                'has_next' => $currentPage < $totalPages,
                'previous_url' => $this->dashboardPageUrl($currentPage - 1),
                'next_url' => $this->dashboardPageUrl($currentPage + 1),
            ],
        ];
    }

    private function recentQuestions(int $userId): array
    {
        try {
            $posts = (new Post())->getByUserId($userId, 3);
        } catch (Throwable) {
            return [];
        }

        return array_map(fn (array $post) => ViewHelper::formatPostCard($post), $posts);
    }

    private function recentActivities(array $questions)
    {
        $questions = array_slice($questions, 0, 3);

        return array_map(static function (array $question, int $index) {
            return [
                'label' => 'You posted ' . $question['title'],
                'time' => $question['time'],
                'active' => $index === 0,
            ];
        }, $questions, array_keys($questions));
    }

    private function trendingModules()
    {
        try {
            $modules = (new Module())->getTrending(3);
        } catch (Throwable) {
            return [];
        }

        $modules = array_map(function (array $module) {
            $code = trim((string) ($module['code'] ?? ''));
            $postCount = (int) ($module['post_count'] ?? 0);

            if ($code === '') {
                return [];
            }

            return [
                'url' => $this->moduleUrl($code),
                'code' => $code,
                'posts' => $postCount . ' ' . ($postCount === 1 ? 'post' : 'posts'),
            ];
        }, $modules);

        return array_values(array_filter($modules));
    }

    private function moduleUrl(string $code)
    {
        return BASE_URL . '/discussions?module=' . rawurlencode($code);
    }

    private function dashboardPageUrl(int $page)
    {
        $url = BASE_URL . '/dashboard';

        if ($page > 1) {
            $url .= '?page=' . $page;
        }

        return $url . '#my-questions-heading';
    }

    private function isStudent(array $user)
    {
        return strtolower(trim((string) ($user['role'] ?? ''))) === 'student';
    }
}
