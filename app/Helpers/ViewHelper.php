<?php

namespace App\Helpers;

/**
 * Maps repository records into stable, view-ready data structures.
 */
class ViewHelper
{
    public static function formatDiscussionCard(array $postRecord)
    {
        $status = (string)($postRecord['status'] ?? 'open');
        $title = FormatHelper::textOr($postRecord['title'] ?? '', 'Untitled question');

        return [
            'id' => (int)($postRecord['id'] ?? 0),
            'module_code' => FormatHelper::textOr($postRecord['module_code'] ?? '', 'MODULE'),
            'module_name' => FormatHelper::textOr($postRecord['module_name'] ?? '', 'Module discussion'),
            'status' => $status === 'solved' ? 'Solved' : 'Open',
            'status_tone' => $status === 'solved' ? 'green' : 'neutral',
            'created_at' => (string)($postRecord['created_at'] ?? ''),
            'title' => $title,
            'excerpt' => FormatHelper::textOr(
                self::excerpt((string)($postRecord['content'] ?? ''), 180),
                '
                '
            ),
            'author_full_name' => FormatHelper::textOr($postRecord['full_name'] ?? '', 'Student'),
            'author_handle' => FormatHelper::authorHandle($postRecord),
            'author_initial' => FormatHelper::authorInitial($postRecord),
            'author_avatar_url' => FormatHelper::authorAvatarUrl($postRecord),
            'reply_count' => (int)($postRecord['reply_count'] ?? 0),
            'view_count' => (int)($postRecord['view_count'] ?? 0),
            'attachment_preview_url' => FormatHelper::mediaUrl($postRecord['media_path'] ?? null),
            'attachment_type' => trim((string)($postRecord['media_type'] ?? '')),
            'attachment_preview_alt' => 'Preview for ' . $title,
            'url' => FormatHelper::discussionDetailUrl($postRecord['id'] ?? 0, $postRecord['slug'] ?? ''),
        ];
    }

    public static function formatSidebarDiscussion(array $postRecord)
    {
        return [
            'title' => FormatHelper::textOr($postRecord['title'] ?? '', 'Untitled question'),
            'reply_count' => (int)($postRecord['reply_count'] ?? 0),
            'url' => FormatHelper::discussionDetailUrl($postRecord['id'] ?? 0, $postRecord['slug'] ?? ''),
        ];
    }

    public static function moduleChips(array $modules, array $filters)
    {
        $chips = [];

        foreach ($modules as $module) {
            $code = trim((string)($module['code'] ?? ''));

            if ($code === '') {
                continue;
            }

            $chips[] = [
                'code' => $code,
                'name' => FormatHelper::textOr($module['name'] ?? '', $code),
                'url' => FormatHelper::discussionUrl($filters, ['module' => $code, 'page' => null]),
                'active' => strcasecmp((string)($filters['module'] ?? ''), $code) === 0,
            ];
        }

        return $chips;
    }

    public static function matchedModules(array $modules, array $filters)
    {
        $query = strtolower(trim((string)($filters['q'] ?? '')));

        if ($query === '') {
            return [];
        }

        $matches = [];

        foreach ($modules as $module) {
            $code = trim((string)($module['code'] ?? ''));
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
            $code = trim((string)($module['code'] ?? ''));
            $count = (int)($module['post_count'] ?? 0);

            if ($code === '') {
                continue;
            }

            $formatted[] = [
                'code' => $code,
                'name' => FormatHelper::textOr($module['name'] ?? '', $code),
                'discussion_count' => $count,
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
