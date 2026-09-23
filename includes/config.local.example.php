<?php
/**
 * Copy to includes/config.local.php on the server (never commit the real file).
 * Only keys you set here override includes/config.php.
 */
declare(strict_types=1);

return [
    // 'env' => 'development',
    // 'base_url' => 'http://localhost:8080',

    // Generate with: php -r "echo bin2hex(random_bytes(32)), PHP_EOL;"
    'app_secret' => 'REPLACE_WITH_64_HEX_CHARS',

    'mail' => [
        'to'   => 'you@your-company.example',
        'from' => 'no-reply@managed-graylog.com',
    ],

    // Recommended: keep writable state outside the public web root.
    // 'storage_path' => '/home/USER/managed-graylog-storage',
];
