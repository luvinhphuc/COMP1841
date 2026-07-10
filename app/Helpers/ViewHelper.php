<?php

namespace App\Helpers;

class ViewHelper
{
    public static function formatPostCard(array $post)
    {
        $status = (string) ($post['status'] ?? 'open');
        $title = FormatHelper::textOr($post['title'] ?? '', 'Untitled question');
        $createdAt = FormatHelper::textOr(
            FormatHelper::relativeTime((string) ($post['created_at'] ?? '')),
            'Recently'
        );

        return [
            'module' => FormatHelper::textOr($post['module_code'] ?? '', 'MODULE'),
            'module_name' => FormatHelper::textOr($post['module_name'] ?? '', 'Module discussion'),
            'status' => $status === 'solved' ? 'Solved' : 'Open',
            'status_tone' => $status === 'solved' ? 'green' : 'neutral',
            'created_at' => $createdAt,
            'time' => $createdAt,
            'title' => $title,
            'excerpt' => FormatHelper::textOr(
                self::excerpt((string) ($post['content'] ?? ''), 180),
                'No preview is available yet.'
            ),
            'author' => FormatHelper::textOr($post['full_name'] ?? $post['username'] ?? '', 'Student'),
            'author_handle' => FormatHelper::authorHandle($post),
            'avatar' => FormatHelper::authorInitial($post),
            'replies' => (int) ($post['reply_count'] ?? 0),
            'views' => FormatHelper::compactNumber((int) ($post['view_count'] ?? 0)),
            'image' => FormatHelper::mediaUrl($post['media_path'] ?? null),
            'media_type' => trim((string) ($post['media_type'] ?? '')),
            'preview_alt' => 'Preview for ' . $title,
            'url' => FormatHelper::discussionDetailUrl($post['slug'] ?? '', $post['id'] ?? ''),
        ];
    }

    public static function formatRecentView(array $post)
    {
        return [
            'title' => FormatHelper::textOr($post['title'] ?? '', 'Untitled question'),
            'module' => FormatHelper::textOr($post['module_code'] ?? '', 'MODULE'),
            'time' => FormatHelper::textOr(
                FormatHelper::relativeTime((string) ($post['viewed_at'] ?? '')),
                'Recently'
            ),
            'url' => FormatHelper::discussionDetailUrl($post['slug'] ?? '', $post['id'] ?? ''),
        ];
    }

    public static function formatSidebarDiscussion(array $post)
    {
        return [
            'title' => FormatHelper::textOr($post['title'] ?? '', 'Untitled question'),
            'replies' => (int) ($post['reply_count'] ?? 0),
            'url' => FormatHelper::discussionDetailUrl($post['slug'] ?? '', $post['id'] ?? ''),
        ];
    }

    public static function moduleChips(array $modules, array $filters)
    {
        $chips = [];

        foreach ($modules as $module) {
            $code = trim((string) ($module['code'] ?? ''));

            if ($code === '') {
                continue;
            }

            $chips[] = [
                'code' => $code,
                'name' => FormatHelper::textOr($module['name'] ?? '', $code),
                'url' => FormatHelper::discussionUrl($filters, ['module' => $code, 'page' => null]),
                'active' => strcasecmp((string) ($filters['module'] ?? ''), $code) === 0,
            ];
        }

        return $chips;
    }

    public static function matchedModules(array $modules, array $filters)
    {
        $query = strtolower(trim((string) ($filters['q'] ?? '')));

        if ($query === '') {
            return [];
        }

        $matches = [];

        foreach ($modules as $module) {
            $code = trim((string) ($module['code'] ?? ''));
            $name = FormatHelper::textOr($module['name'] ?? '', $code);

            if ($code === '') {
                continue;
            }

            if (!str_contains(strtolower($code), $query) && !str_contains(strtolower($name), $query)) {
                continue;
            }

            $matches[] = [
                'code' => $code,
                'name' => $name,
                'url' => FormatHelper::discussionUrl($filters, ['module' => $code, 'q' => null, 'page' => null]),
            ];
        }

        return $matches;
    }

    public static function formatTrendingModules(array $modules)
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
                'name' => FormatHelper::textOr($module['name'] ?? '', $code),
                'count' => $count . ' ' . ($count === 1 ? 'discussion' : 'discussions'),
                'url' => BASE_URL . '/discussions?module=' . rawurlencode($code),
            ];
        }

        return $formatted;
    }

    private static function excerpt(string $content, int $limit)
    {
        $content = trim(strip_tags($content));

        if (strlen($content) <= $limit) {
            return $content;
        }

        return rtrim(substr($content, 0, $limit - 3)) . '...';
    }
}
