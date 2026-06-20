<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('ROOT_PATH', dirname(__DIR__));

// Bật lỗi trước khi require các file khác
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

if (file_exists(ROOT_PATH . '/vendor/autoload.php')) {
    require_once ROOT_PATH . '/vendor/autoload.php';
} else {
    die('Hệ thống thiếu vendor/autoload.php. Hãy chạy composer install.');
}

require_once ROOT_PATH . '/config/config.php';

use App\Core\App;

$app = new App();