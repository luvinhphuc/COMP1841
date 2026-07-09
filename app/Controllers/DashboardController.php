<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Helpers\FormatHelper;
use App\Models\Module;
use App\Models\Post;
use Throwable;

class DashboardController extends Controller
{
    public function index()
    {
        $authUser = $this->currentUser();
        $userId = $this->currentUserId();

        if (!is_array($authUser) || $userId === null) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        $myQuestions = $this->myQuestions($userId);

        $this->view('dashboard/index', [
            'homeModules' => $this->homeModules(),
            'myQuestions' => $myQuestions,
            'recentActivities' => $this->recentActivities($myQuestions),
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

    private function homeModules()
    {
        try {
            $modules = (new Module())->getAll();
        } catch (Throwable) {
            return [];
        }

        $modules = array_slice($modules, 0, 4);

        $modules = array_map(function (array $module){
            $code = trim((string) ($module['code'] ?? ''));

            if ($code === '') {
                return [];
            }

            return [
                'url' => $this->moduleUrl($code),
                'code' => $code,
                'name' => FormatHelper::textOr($module['name'] ?? '', 'Untitled module'),
                'discussion_count' => null,
                'discussion_count_label' => 'View discussions',
                'active' => false,
            ];
        }, $modules);

        return array_values(array_filter($modules));
    }

    private function myQuestions(int $userId)
    {
        try {
            $posts = (new Post())->getByUserId($userId, 10);
        } catch (Throwable) {
            return [];
        }

        return array_map(fn (array $post) => $this->formatQuestion($post), $posts);
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

    private function formatQuestion(array $post)
    {
        $status = ($post['status'] ?? 'open');
        $title = FormatHelper::textOr($post['title'] ?? '', 'Untitled question');
        $createdAt = FormatHelper::textOr(FormatHelper::relativeTime((string) ($post['created_at'] ?? '')), 'Recently');
        $replyCount = filter_var($post['reply_count'] ?? 0, FILTER_VALIDATE_INT);

        return [
            'module' => FormatHelper::textOr($post['module_code'] ?? '', 'MODULE'),
            'module_name' => FormatHelper::textOr($post['module_name'] ?? '', 'Module discussion'),
            'status' => $status === 'solved' ? 'Solved' : 'Open',
            'status_tone' => $status === 'solved' ? 'green' : 'neutral',
            'created_at' => $createdAt,
            'time' => $createdAt,
            'title' => $title,
            'excerpt' => FormatHelper::textOr($this->excerpt(($post['content'] ?? ''), 180), 'No preview is available yet.'),
            'author' => FormatHelper::textOr($post['full_name'] ?? $post['username'] ?? '', 'Student'),
            'author_handle' => FormatHelper::authorHandle($post),
            'avatar' => FormatHelper::authorInitial($post),
            'replies' => $replyCount === false ? 0 : $replyCount,
            'views' => FormatHelper::compactNumber((int) ($post['view_count'] ?? 0)),
            'image' => FormatHelper::mediaUrl($post['media_path'] ?? null),
            'media_type' => trim((string) ($post['media_type'] ?? '')),
            'preview_alt' => 'Preview for ' . $title,
            'url' => BASE_URL . '/discussions/' . rawurlencode((string) ($post['slug'] ?? $post['id'] ?? '')),
        ];
    }

    private function excerpt(string $content, int $limit = 140)
    {
        $content = trim(strip_tags($content));

        if (strlen($content) <= $limit) {
            return $content;
        }

        return rtrim(substr($content, 0, $limit - 3)) . '...';
    }

    private function moduleUrl(string $code)
    {
        return BASE_URL . '/discussions?module=' . rawurlencode($code);
    }

}
