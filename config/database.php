<?php

// Database connection settings are read once by App\Core\Database.
return [
    'host' => trim((string) ($_ENV['DB_HOST'] ?? 'localhost')),
    'port' => (int) ($_ENV['DB_PORT'] ?? 3306),
    'dbname' => trim((string) ($_ENV['DB_DATABASE'] ?? 'uog_discussion_db')),
    'username' => trim((string) ($_ENV['DB_USERNAME'] ?? 'root')),
    'password' => (string) ($_ENV['DB_PASSWORD'] ?? ''),
    'charset' => trim((string) ($_ENV['DB_CHARSET'] ?? 'utf8mb4')),
];
