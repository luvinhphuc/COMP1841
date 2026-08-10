<?php

// Only known environment names are exposed to the rest of the application.
$environment = strtolower(trim((string) ($_ENV['APP_ENV'] ?? 'production')));

define('APP_ENV', $environment === 'development' ? 'development' : 'production');

$scriptName = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));

// Derive the mount path so the same build works at a domain root or in a subdirectory.
define('BASE_URL', rtrim($scriptName, '/'));
