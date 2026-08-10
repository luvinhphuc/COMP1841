<?php

namespace App\Services;

use App\Helpers\FormatHelper;
use App\Repositories\ModuleRepository;
use Throwable;

/**
 * Produces navigation state without coupling layout views to repositories.
 */
class NavigationService
{
    public function activeMenuKey(string $route)
    {
        $route = trim($route, '/');

        if ($route === 'modules' || str_starts_with($route, 'modules/')) {
            return 'modules';
        }

        if ($route === 'discussions' || str_starts_with($route, 'discussions/')) {
            return 'discussions';
        }

        if ($route === 'contact' || str_starts_with($route, 'contact/')) {
            return 'contact';
        }

        return 'home';
    }

    public function moduleLinks(int $limit = 3)
    {
        $links = [[
            'label' => 'View all modules',
            'href' => BASE_URL . '/modules',
        ]];

        try {
            $modules = (new ModuleRepository())->findAll();
        } catch (Throwable) {
            $modules = [];
        }

        $shownModules = 0;

        foreach ($modules as $module) {
            $moduleCode = trim((string) ($module['code'] ?? ''));
            $moduleName = trim((string) ($module['name'] ?? ''));

            if ($moduleCode === '') {
                continue;
            }

            $links[] = [
                'label' => $moduleName !== '' ? $moduleCode . ': ' . $moduleName : $moduleCode,
                'href' => BASE_URL . '/discussions?module=' . rawurlencode($moduleCode),
            ];

            $shownModules++;

            if ($shownModules >= $limit) {
                break;
            }
        }

        return $links;
    }

    public function authDisplay(?array $authUser)
    {
        $authName = trim((string) ($authUser['full_name'] ?? ''));
        $authUsername = trim((string) ($authUser['username'] ?? ''));

        return [
            'isLoggedIn' => is_array($authUser),
            'authName' => $authName !== '' ? $authName : 'Student',
            'authUsername' => $authUsername,
            'authAvatarUrl' => FormatHelper::authorAvatarUrl($authUser ?? []),
            'authAvatarInitial' => FormatHelper::authorInitial($authUser ?? []),
        ];
    }
}
