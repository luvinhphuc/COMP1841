<?php

// Front-controller bootstrap: initialise shared state before dispatching the request.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('ROOT_PATH', dirname(__DIR__));

if (file_exists(ROOT_PATH . '/vendor/autoload.php')) {
    require_once ROOT_PATH . '/vendor/autoload.php';
} else {
    die('The system is missing vendor/autoload.php. Please run composer install in terminal.');
}

// Environment values are optional locally; configuration files provide safe defaults.
Dotenv\Dotenv::createImmutable(ROOT_PATH)->safeLoad();

require_once ROOT_PATH . '/config/config.php';

$displayErrors = APP_ENV === 'development' ? '1' : '0';

ini_set('display_errors', $displayErrors);
ini_set('display_startup_errors', $displayErrors);
ini_set('log_errors', '1');
error_reporting(E_ALL);

use App\Core\App;

// Constructing the router resolves and executes the matching controller action.
$app = new App();
