<?php

namespace App\Core;

use App\Services\NavigationService;

class Controller
{
    protected function view(string $viewPath, array $data = [])
    {
        $navigation = new NavigationService();

        if (!array_key_exists('navbarModuleLinks', $data)) {
            $data['navbarModuleLinks'] = $navigation->moduleLinks();
        }

        if (!array_key_exists('authUser', $data)) {
            $data['authUser'] = $navigation->authUser();
        }

        $data = array_merge($data, $navigation->authDisplay($data['authUser']));

        if (!array_key_exists('pageScriptUrls', $data)) {
            $data['pageScriptUrls'] = $this->pageScriptUrls($data['pageScripts'] ?? []);
        }

        $data['navbarScriptUrl'] = $this->assetScriptUrl('navbar.js');
        $data['csrfToken'] = $this->csrfToken();
        $data['flashToast'] = $this->flashToast();

        if (!empty($data)) {
            extract($data);
        }

        $mainViewFile = ROOT_PATH . '/app/Views/' . $viewPath . '.php';

        if (!file_exists($mainViewFile)) {
            die('Lỗi: Không tìm thấy file View tại: ' . $mainViewFile);
        }

        require ROOT_PATH . '/app/Views/partials/header.php';
        require ROOT_PATH . '/app/Views/partials/navbar.php';
        echo '<main>';
        require $mainViewFile;
        require ROOT_PATH . '/app/Views/partials/footer.php';
    }

    protected function csrfToken(): string
    {
        if (empty($_SESSION['_csrf_token']) || !is_string($_SESSION['_csrf_token'])) {
            $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['_csrf_token'];
    }

    protected function verifyCsrfToken(?string $token): bool
    {
        $sessionToken = $_SESSION['_csrf_token'] ?? '';

        return is_string($sessionToken)
            && is_string($token)
            && $sessionToken !== ''
            && hash_equals($sessionToken, $token);
    }

    private function flashToast()
    {
        $toast = $_SESSION['flash_toast'] ?? null;
        unset($_SESSION['flash_toast']);

        if (!is_array($toast)) {
            return null;
        }

        $message = trim((string) ($toast['message'] ?? ''));

        if ($message === '') {
            return null;
        }

        return [
            'type' => trim((string) ($toast['type'] ?? 'info')),
            'title' => trim((string) ($toast['title'] ?? '')),
            'message' => $message,
        ];
    }

    private function pageScriptUrls(array $pageScripts): array
    {
        $scriptUrls = [];

        foreach ($pageScripts as $pageScript) {
            $scriptUrl = $this->assetScriptUrl((string) $pageScript);

            if ($scriptUrl !== '') {
                $scriptUrls[] = $scriptUrl;
            }
        }

        return $scriptUrls;
    }

    private function assetScriptUrl(string $script): string
    {
        $scriptName = basename($script);
        $scriptPath = ROOT_PATH . '/public/assets/js/' . $scriptName;

        if (!is_file($scriptPath)) {
            return '';
        }

        return BASE_URL . '/assets/js/' . rawurlencode($scriptName) . '?v=' . filemtime($scriptPath);
    }
}
