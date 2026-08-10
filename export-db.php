<?php

// CLI utility for exporting the configured application database into a portable SQL file.
define('ROOT_PATH', __DIR__);

require_once ROOT_PATH . '/vendor/autoload.php';

Dotenv\Dotenv::createImmutable(ROOT_PATH)->safeLoad();

$databaseConfig = require ROOT_PATH . '/config/database.php';
$dbName = (string) $databaseConfig['dbname'];

$databaseDir = __DIR__ . '/database';
$output = $databaseDir . '/' . $dbName . '.sql';

if (!is_dir($databaseDir)) {
    mkdir($databaseDir, 0777, true);
}

$possibleDumpPaths = [
    'C:\xampp\mysql\bin\mysqldump.exe',
    '/Applications/XAMPP/xamppfiles/bin/mysqldump',
];

$mysqldump = null;

foreach ($possibleDumpPaths as $path) {
    if (file_exists($path)) {
        $mysqldump = $path;
        break;
    }
}

if (!$mysqldump) {
    die("mysqldump not found\n");
}

// Escape every shell argument because credentials and installation paths may contain spaces.
$commandParts = [
    escapeshellarg($mysqldump),
    '--host=' . escapeshellarg((string) $databaseConfig['host']),
    '--port=' . max(1, (int) $databaseConfig['port']),
    '--user=' . escapeshellarg((string) $databaseConfig['username']),
];

$databasePassword = (string) $databaseConfig['password'];

if ($databasePassword !== '') {
    $commandParts[] = '--password=' . escapeshellarg($databasePassword);
}

$commandParts[] = escapeshellarg($dbName);
$command = implode(' ', $commandParts) . ' > ' . escapeshellarg($output);

system($command);

echo "Database exported to: $output\n";
