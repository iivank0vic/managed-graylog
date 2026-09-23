<?php
/**
 * Site configuration (non-secret values only).
 *
 * Secrets and environment-specific overrides belong in includes/config.local.php,
 * which is git-ignored. See includes/config.local.example.php.
 *
 * Values marked PLACEHOLDER must be verified/replaced before launch.
 */
declare(strict_types=1);

$config = [
    'env'         => 'production',          // 'production' | 'development'
    'site_name'   => 'Managed Graylog',
    'domain'      => 'managed-graylog.com',
    'base_url'    => 'https://managed-graylog.com', // no trailing slash
    'locale'      => 'en',
    'theme_color' => '#07090d',

    // Public contact address shown on the site and used in schema.org data.
    'contact_email' => 'contact@managed-graylog.com',

    // Legal entity details. PLACEHOLDER — leave null until real values exist.
    // When null, the site omits them instead of inventing anything.
    'company' => [
        'legal_name' => null,   // e.g. 'Example d.o.o.'
        'address'    => null,   // e.g. 'Street 1, 10000 Zagreb, Croatia'
        'vat_id'     => null,   // e.g. 'HR00000000000'
        'country'    => null,   // ISO 3166-1 alpha-2, e.g. 'HR'
    ],

    // Optional public profiles for Organization schema "sameAs". Only add real, owned profiles.
    'same_as' => [],

    'mail' => [
        // 'mail'  = PHP mail() (requires a working MTA on the host)
        // 'log'   = do not send, only store (useful for staging)
        'transport'      => 'mail',
        'to'             => 'contact@managed-graylog.com',  // inbox that receives form requests
        'from'           => 'no-reply@managed-graylog.com', // sender address; domain needs SPF/DKIM/DMARC
        'subject_prefix' => '[Managed Graylog] ',
    ],

    'form' => [
        'min_fill_seconds'  => 4,     // submissions faster than this are treated as bots
        'max_age_seconds'   => 86400, // form token lifetime
        'rate_limit_max'    => 5,     // submissions per IP per window
        'rate_limit_window' => 3600,  // seconds
        'store_submissions' => true,  // JSON copy in storage/submissions (see privacy policy)
    ],

    // Writable directory for rate-limit state, submissions and the generated app secret.
    // Prefer a path OUTSIDE the web root in production (override in config.local.php).
    'storage_path' => dirname(__DIR__) . '/storage',

    // Secret used for HMAC signing (form tokens, IP hashing). Leave null here.
    // Set it in config.local.php, or it is generated once into storage/.app_secret.
    'app_secret' => null,
];

$local = __DIR__ . '/config.local.php';
if (is_file($local)) {
    $overrides = require $local;
    if (is_array($overrides)) {
        $config = array_replace_recursive($config, $overrides);
    }
}

return $config;
