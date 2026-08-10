<?php

// SMTP settings remain environment-driven so credentials never need to live in source.
return [
    'host' => trim((string) ($_ENV['MAIL_HOST'] ?? 'smtp.gmail.com')),
    'port' => (int) ($_ENV['MAIL_PORT'] ?? 587),
    'encryption' => strtolower(trim((string) ($_ENV['MAIL_ENCRYPTION'] ?? 'tls'))),
    'timeout' => (int) ($_ENV['MAIL_TIMEOUT'] ?? 10),
    'username' => trim((string) ($_ENV['MAIL_USERNAME'] ?? '')),
    'password' => (string) ($_ENV['MAIL_PASSWORD'] ?? ''),
    'from_email' => trim((string) ($_ENV['MAIL_FROM_ADDRESS'] ?? '')),
    'from_name' => trim((string) ($_ENV['MAIL_FROM_NAME'] ?? 'Coursework Forum')),
    'admin_email' => trim((string) ($_ENV['ADMIN_EMAIL'] ?? '')),
    'admin_name' => trim((string) ($_ENV['ADMIN_NAME'] ?? 'Administrator')),
];
