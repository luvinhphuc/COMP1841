<?php

namespace App\Services;

use App\Helpers\FormatHelper;
use App\Models\Module;
use App\Models\UserModule;
use Throwable;

class NavigationService
{
    public function moduleLinks(int $limit = 3)
    {
        $authUser = $this->authUser();
        $isStudent = strtolower(trim((string) ($authUser['role'] ?? ''))) === 'student';
        $links = [];

        try {
            if ($isStudent) {
                $userId = filter_var($authUser['id'] ?? 0, FILTER_VALIDATE_INT);
                $modules = $userId !== false && $userId > 0
                    ? (new UserModule())->getModulesByUserId((int) $userId)
                    : [];
            } else {
                $modules = (new Module())->getAll();
            }
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

        $links[] = [
            'label' => $isStudent ? 'Manage my modules' : 'View all discussions',
            'href' => BASE_URL . ($isStudent ? '/preferences/modules' : '/discussions'),
        ];

        return $links;
    }

    public function authUser()
    {
        $authUser = $_SESSION['auth_user'] ?? null;

        return is_array($authUser) ? $authUser : null;
    }

    public function authDisplay(?array $authUser)
    {
        $authName = trim((string) ($authUser['full_name'] ?? $authUser['name'] ?? $authUser['username'] ?? 'Student'));
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
