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
}
