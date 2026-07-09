<?php

namespace App\Helpers;

class ViewHelper
{
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

}
