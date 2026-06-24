<?php

namespace App\Core;

Database::connect();
class Controller
{
    protected function view(string $viewPath, array $data = [])
    {
        if (!empty($data)) {
            extract($data);
        }

        $mainViewFile = ROOT_PATH . '/app/Views/' . $viewPath . '.php';

        if (!file_exists($mainViewFile)) {
            die('Lỗi: Không tìm thấy file View tại: ' . $mainViewFile);
        }

        require ROOT_PATH . '/app/Views/partials/header.php';
        require $mainViewFile;
        require ROOT_PATH . '/app/Views/partials/footer.php';
    }
}