<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Module;
use App\Models\Post;
use Throwable;

class HomeController extends Controller
{
    public function index()
    {
        $authUser = $this->authUser();
        $newestQuestions = $this->newestQuestions();

        $data = [
            'homeModules' => $this->homeModules(),
            'newestQuestions' => $newestQuestions,
            'recentActivities' => $this->recentActivities($newestQuestions),
            'trendingModules' => $this->trendingModules(),
            'recentPostViews' => $this->recentPostViews(),
            'authUser' => $authUser,
            'greetingName' => $this->greetingName($authUser),
            'pageScripts' => ['home.js'],
        ];

        $this->view('home/index', $data);
    }

    private function authUser(): ?array
    {
        $authUser = $_SESSION['user'] ?? $_SESSION['auth_user'] ?? null;

        return is_array($authUser) ? $authUser : null;
    }

    private function greetingName(?array $authUser): string
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

    private function homeModules(): array
    {
        try {
            $modules = (new Module())->getAll();
        } catch (Throwable) {
            return [];
        }

        $modules = array_slice($modules, 0, 4);

        $modules = array_map(function (array $module): array {
            $code = trim((string) ($module['code'] ?? ''));

            if ($code === '') {
                return [];
            }

            return [
                'url' => $this->moduleUrl($code),
                'code' => $code,
                'name' => $this->textOr($module['name'] ?? '', 'Untitled module'),
                'discussion_count' => null,
                'discussion_count_label' => 'No discussions yet',
                'active' => false,
            ];
        }, $modules);

        return array_values(array_filter($modules));
    }

    private function newestQuestions(): array
    {
        try {
            $posts = (new Post())->getLatest(3);
        } catch (Throwable) {
            return [];
        }

        return array_map(fn (array $post): array => $this->formatQuestion($post), $posts);
    }

    private function recentActivities(array $questions): array
    {
        return array_map(static function (array $question, int $index): array {
            return [
                'label' => $question['author'] . ' posted ' . $question['title'],
                'time' => $question['time'],
                'active' => $index === 0,
            ];
        }, $questions, array_keys($questions));
    }

    private function trendingModules(): array
    {
        try {
            $modules = (new Post())->getTrendingModules(3);
        } catch (Throwable) {
            return [];
        }

        $modules = array_map(function (array $module): array {
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

    private function recentPostViews(): array
    {
        try {
            $posts = (new Post())->getRecentViews(null, 2);
        } catch (Throwable) {
            return [];
        }

        return array_map(function (array $post, int $index): array {
            return [
                'label' => $this->authorHandle($post) . ' viewed ' . $this->textOr($post['title'] ?? '', 'Untitled post'),
                'time' => $this->textOr($this->relativeTime((string) ($post['viewed_at'] ?? '')), 'Recently'),
                'active' => $index === 0,
            ];
        }, $posts, array_keys($posts));
    }

    private function formatQuestion(array $post): array
    {
        $status = (string) ($post['status'] ?? 'open');

        return [
            'module' => $this->textOr($post['module_code'] ?? '', 'MODULE'),
            'status' => $status === 'solved' ? 'SOLVED' : 'OPEN',
            'status_tone' => $status === 'solved' ? 'green' : 'neutral',
            'time' => $this->textOr($this->relativeTime((string) ($post['created_at'] ?? '')), 'Recently'),
            'title' => $this->textOr($post['title'] ?? '', 'Untitled question'),
            'excerpt' => $this->textOr($this->excerpt((string) ($post['content'] ?? '')), 'No preview is available yet.'),
            'author' => $this->authorHandle($post),
            'avatar' => $this->authorInitial($post),
            'replies' => (string) ((int) ($post['reply_count'] ?? 0)),
            'views' => $this->compactNumber((int) ($post['view_count'] ?? 0)),
            'image' => $this->mediaUrl($post['media_path'] ?? null),
            'preview_alt' => 'Preview for ' . $this->textOr($post['title'] ?? '', 'Untitled question'),
            'url' => BASE_URL . '/discussions/' . rawurlencode((string) ($post['slug'] ?? $post['id'] ?? '')),
        ];
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

    private function excerpt(string $content, int $limit = 140)
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

    private function moduleUrl(string $code)
    {
        return BASE_URL . '/modules/' . rawurlencode(strtolower($code));
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
}
