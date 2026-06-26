<?php

$dbName = 'uog_discussion_db';

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

$command = '"' . $mysqldump . '" -u root ' . $dbName . ' > "' . $output . '"';

system($command);

echo "Database exported to: $output\n";