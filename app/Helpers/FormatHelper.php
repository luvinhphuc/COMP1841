<?php

namespace App\Helpers;

class FormatHelper
{
    public static function textOr(mixed $value, string $default)
    {
        $text = trim((string) $value);

        return $text !== '' ? $text : $default;
    }

    public static function shortText(string $value, int $length)
    {
        if (function_exists('mb_substr')) {
            return mb_substr($value, 0, $length);
        }

        return substr($value, 0, $length);
    }

    public static function textLength(string $value)
    {
        if (function_exists('mb_strlen')) {
            return mb_strlen($value);
        }

        return strlen($value);
    }

    public static function compactNumber(int $number)
    {
        if ($number >= 1000) {
            return rtrim(rtrim(number_format($number / 1000, 1), '0'), '.') . 'k';
        }

        return $number;
    }

    public static function mediaUrl(mixed $path)
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

    public static function discussionUrl(array $filters, array $overrides = [])
    {
        $query = array_merge($filters, $overrides);

        foreach ($query as $key => $value) {
            if ($value === null || trim((string) $value) === '') {
                unset($query[$key]);
            }
        }

        $queryString = http_build_query($query);

        return BASE_URL . '/discussions' . ($queryString !== '' ? '?' . $queryString : '');
    }

    public static function discussionDetailUrl(mixed $slug, mixed $fallbackId = '')
    {
        $target = trim((string) $slug);

        if ($target === '') {
            $target = trim((string) $fallbackId);
        }

        return BASE_URL . '/discussions/' . rawurlencode($target);
    }

    public static function authorHandle(array $user)
    {
        $username = trim((string) ($user['username'] ?? ''));

        return $username !== '' ? '@' . $username : '@student';
    }

    public static function authorInitial(array $user)
    {
        $name = trim((string) ($user['full_name'] ?? $user['username'] ?? 'S'));

        return strtoupper(self::shortText($name, 2));
    }

    public static function relativeTime(string $dateTime)
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
            $minutes = floor($seconds / 60);
            return $minutes . ' min' . ($minutes === 1 ? '' : 's') . ' ago';
        }

        if ($seconds < 86400) {
            $hours = floor($seconds / 3600);
            return $hours . 'h ago';
        }

        if ($seconds < 172800) {
            return 'Yesterday';
        }

        $days = floor($seconds / 86400);
        if ($days > 3) {
            return date('d M Y', $timestamp);
        }
        return $days . ' days ago';
    }

    public static function formatFileSize(int $bytes)
    {
        if ($bytes <= 0) {
            return '';
        }

        if ($bytes >= 1048576) {
            return rtrim(rtrim(number_format($bytes / 1048576, 1), '0'), '.') . ' MB';
        }

        return max(1, ceil($bytes / 1024)) . ' KB';
    }
}
