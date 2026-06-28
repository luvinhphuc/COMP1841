<?php

namespace App\Services;

use App\Models\Module;
use Throwable;

class NavigationService
{
    public function moduleLinks(int $limit = 3): array
    {
        $links = [
            ['label' => 'View all modules', 'href' => BASE_URL . '/modules'],
        ];

        try {
            $modules = (new Module())->getAll();
        } catch (Throwable) {
            return $links;
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
                'href' => BASE_URL . '/modules/' . rawurlencode(strtolower($moduleCode)),
            ];

            $shownModules++;

            if ($shownModules >= $limit) {
                break;
            }
        }

        return $links;
    }

    public function authUser(): ?array
    {
        $authUser = $_SESSION['user'] ?? $_SESSION['auth_user'] ?? null;

        return is_array($authUser) ? $authUser : null;
    }

    public function authDisplay(?array $authUser): array
    {
        $authName = trim((string) ($authUser['full_name'] ?? $authUser['name'] ?? $authUser['username'] ?? 'Student'));
        $authUsername = trim((string) ($authUser['username'] ?? ''));
        $authAvatar = trim((string) ($authUser['avatar'] ?? ''));

        return [
            'isLoggedIn' => is_array($authUser),
            'authName' => $authName !== '' ? $authName : 'Student',
            'authUsername' => $authUsername,
            'authAvatarUrl' => $authAvatar !== ''
                ? BASE_URL . '/' . ltrim($authAvatar, '/')
                : BASE_URL . '/assets/images/header/user-avatar.jpg',
        ];
    }
}
