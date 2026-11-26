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

    // Mailgun (recommended). With mail.transport = 'auto' (default) Mailgun is used as soon as
    // api_key and domain are set here. Use a domain-scoped *Sending API key*, not the account key.
    'mailgun' => [
        'api_key' => 'REPLACE_WITH_MAILGUN_SENDING_KEY',
        'domain'  => 'mg.managed-graylog.com',
        'region'  => 'eu',   // 'eu' or 'us' — the region the domain was created in
    ],

    'mail' => [
        // 'transport' => 'mailgun',                    // force Mailgun ('log' for staging)
        // 'to'          => 'contact@managed-graylog.com',
        // 'to_messages' => 'hello@managed-graylog.com', // separate inbox for quick messages
        // 'from'        => 'no-reply@mg.managed-graylog.com',
        // 'autoreply'   => true,                       // confirmation mail to the sender
    ],

    // Recommended: keep writable state outside the public web root.
    // 'storage_path' => '/home/USER/managed-graylog-storage',
];
