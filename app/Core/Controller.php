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
