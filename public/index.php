<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('ROOT_PATH', dirname(__DIR__));

require_once ROOT_PATH . '/config/config.php';

$displayErrors = APP_ENV === 'development' ? '1' : '0';

ini_set('display_errors', $displayErrors);
ini_set('display_startup_errors', $displayErrors);
ini_set('log_errors', '1');
error_reporting(E_ALL);

if (file_exists(ROOT_PATH . '/vendor/autoload.php')) {
    require_once ROOT_PATH . '/vendor/autoload.php';
} else {
    die('Hệ thống thiếu vendor/autoload.php. Hãy chạy composer install.');
}

use App\Core\App;

$app = new App();
