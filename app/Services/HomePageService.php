<?php

namespace App\Services;

use App\Models\Module;
use App\Models\Post;
use Throwable;

class HomePageService
{
    public function getViewData(): array
    {
        $newestQuestions = $this->newestQuestions();

        return [
            'homeModules' => $this->homeModules(),
            'newestQuestions' => $newestQuestions,
            'recentActivities' => $this->recentActivities($newestQuestions),
            'trendingModules' => $this->trendingModules(),
            'recentPostViews' => $this->recentPostViews(),
        ];
    }

    private function homeModules(): array
    {
        try {
            $modules = (new Module())->getAll();
        } catch (Throwable) {
            return [];
        }

        $modules = array_slice($modules, 0, 4);

        return array_map(static function (array $module): array {
            return [
                'code' => trim((string) ($module['code'] ?? '')),
                'name' => trim((string) ($module['name'] ?? '')),
                'discussion_count' => null,
                'active' => false,
            ];
        }, $modules);
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

        return array_map(static function (array $module): array {
            $postCount = (int) ($module['post_count'] ?? 0);

            return [
                'code' => trim((string) ($module['code'] ?? '')),
                'posts' => $postCount . ' ' . ($postCount === 1 ? 'post' : 'posts'),
            ];
        }, $modules);
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
                'label' => $this->authorHandle($post) . ' viewed ' . trim((string) ($post['title'] ?? 'Untitled post')),
                'time' => $this->relativeTime((string) ($post['viewed_at'] ?? '')),
                'active' => $index === 0,
            ];
        }, $posts, array_keys($posts));
    }

    private function formatQuestion(array $post): array
    {
        $status = (string) ($post['status'] ?? 'open');

        return [
            'module' => trim((string) ($post['module_code'] ?? '')),
            'status' => $status === 'solved' ? 'SOLVED' : 'OPEN',
            'status_tone' => $status === 'solved' ? 'green' : 'neutral',
            'time' => $this->relativeTime((string) ($post['created_at'] ?? '')),
            'title' => trim((string) ($post['title'] ?? 'Untitled post')),
            'excerpt' => $this->excerpt((string) ($post['content'] ?? '')),
            'author' => $this->authorHandle($post),
            'avatar' => $this->authorInitial($post),
            'replies' => (string) ((int) ($post['reply_count'] ?? 0)),
            'views' => $this->compactNumber((int) ($post['view_count'] ?? 0)),
            'image' => $this->mediaUrl($post['media_path'] ?? null),
            'url' => BASE_URL . '/discussions/' . rawurlencode((string) ($post['slug'] ?? $post['id'] ?? '')),
        ];
    }

    private function authorHandle(array $post): string
    {
        $username = trim((string) ($post['username'] ?? ''));

        return $username !== '' ? '@' . $username : '@student';
    }

    private function authorInitial(array $post): string
    {
        $name = trim((string) ($post['full_name'] ?? $post['username'] ?? 'S'));

        return strtoupper(substr($name, 0, 1));
    }

    private function excerpt(string $content, int $limit = 140): string
    {
        $content = trim(strip_tags($content));

        if (strlen($content) <= $limit) {
            return $content;
        }

        return rtrim(substr($content, 0, $limit - 3)) . '...';
    }

    private function compactNumber(int $number): string
    {
        if ($number >= 1000) {
            return rtrim(rtrim(number_format($number / 1000, 1), '0'), '.') . 'k';
        }

        return (string) $number;
    }

    private function mediaUrl(mixed $path): ?string
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

    private function relativeTime(string $dateTime): string
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
