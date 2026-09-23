<?php
/**
 * Application bootstrap: config, error handling, security headers.
 */
declare(strict_types=1);

define('APP_ROOT', dirname(__DIR__));

$GLOBALS['APP_CONFIG'] = require __DIR__ . '/config.php';

require __DIR__ . '/functions.php';
require __DIR__ . '/icons.php';
require __DIR__ . '/seo.php';

$isDev = config('env') === 'development';
error_reporting(E_ALL);
ini_set('display_errors', $isDev ? '1' : '0');
ini_set('log_errors', '1');

date_default_timezone_set('UTC');
mb_internal_encoding('UTF-8');

/*
 * Security headers are also set in .htaccess. They are sent from PHP as well so
 * the site stays protected on servers where .htaccess is ignored (nginx, php -S).
 * Keep both in sync. If you add analytics or third-party embeds, extend the CSP.
 */
if (!headers_sent()) {
    header_remove('X-Powered-By');
    header("Content-Security-Policy: default-src 'self'; script-src 'self'; style-src 'self'; img-src 'self' data:; font-src 'self'; connect-src 'self'; form-action 'self'; frame-ancestors 'none'; base-uri 'self'; object-src 'none'" . ($isDev ? '' : '; upgrade-insecure-requests'));
    header('X-Content-Type-Options: nosniff');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('X-Frame-Options: DENY');
    header('Permissions-Policy: camera=(), microphone=(), geolocation=(), payment=(), usb=(), interest-cohort=()');
    header('Cross-Origin-Opener-Policy: same-origin');
}
