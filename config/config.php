<?php

$environment = strtolower(trim((string) getenv('APP_ENV')));

define('APP_ENV', $environment === 'development' ? 'development' : 'production');

$scriptName = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));

define('BASE_URL', rtrim($scriptName, '/'));
