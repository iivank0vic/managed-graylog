<?php
/**
 * Core helpers: config access, escaping, URLs, assets, CSRF, rendering.
 */
declare(strict_types=1);

/** Read a config value using dot notation, e.g. config('mail.to'). */
function config(string $key, mixed $default = null): mixed
{
    $value = $GLOBALS['APP_CONFIG'] ?? [];
    foreach (explode('.', $key) as $segment) {
        if (!is_array($value) || !array_key_exists($segment, $value)) {
            return $default;
        }
        $value = $value[$segment];
    }
    return $value;
}

/** HTML-escape for text and attribute contexts. */
function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5, 'UTF-8');
}

/** Root-relative URL for an internal path. */
function url(string $path = '/'): string
{
    return '/' . ltrim($path, '/');
}

/** Absolute URL (canonical, Open Graph, sitemap, schema). */
function abs_url(string $path = '/'): string
{
    $path = '/' . ltrim($path, '/');
    return rtrim((string) config('base_url'), '/') . ($path === '/' ? '/' : $path);
}

/** Asset URL with cache-busting version derived from file mtime. */
function asset(string $path): string
{
    $path = ltrim($path, '/');
    $file = APP_ROOT . '/assets/' . $path;
    $v = is_file($file) ? (string) filemtime($file) : '1';
    return '/assets/' . $path . '?v=' . $v;
}

/** True if the given route path is the current one (or a parent section of it). */
function is_active(string $path, bool $section = false): bool
{
    $current = $GLOBALS['CURRENT_PATH'] ?? '/';
    if ($section && $path !== '/') {
        return $current === $path || str_starts_with($current, rtrim($path, '/') . '/');
    }
    return $current === $path;
}

/** Render a partial file with an isolated variable scope. */
function partial(string $name, array $vars = []): void
{
    $file = APP_ROOT . '/partials/' . $name . '.php';
    if (!is_file($file)) {
        throw new RuntimeException('Partial not found: ' . $name);
    }
    (static function (string $__file, array $__vars): void {
        extract($__vars, EXTR_SKIP);
        require $__file;
    })($file, $vars);
}

/** Load a data file from includes/data (cached per request). */
function data(string $name): array
{
    static $cache = [];
    if (!isset($cache[$name])) {
        $cache[$name] = require APP_ROOT . '/includes/data/' . $name . '.php';
    }
    return $cache[$name];
}

/* --------------------------------------------------------------------------
 * Secrets, sessions, CSRF
 * ----------------------------------------------------------------------- */

/** App secret from config, or generated once into storage (never committed). */
function app_secret(): string
{
    $secret = config('app_secret');
    if (is_string($secret) && strlen($secret) >= 32 && !str_starts_with($secret, 'REPLACE')) {
        return $secret;
    }
    $file = rtrim((string) config('storage_path'), '/') . '/.app_secret';
    if (is_file($file)) {
        $stored = trim((string) file_get_contents($file));
        if (strlen($stored) >= 32) {
            return $stored;
        }
    }
    $generated = bin2hex(random_bytes(32));
    if (@file_put_contents($file, $generated, LOCK_EX) === false) {
        error_log('[managed-graylog] Could not persist app secret; set app_secret in config.local.php');
    } else {
        @chmod($file, 0600);
    }
    return $generated;
}

function start_session(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }
    $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
    session_name('mg_sid');
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'secure'   => $secure,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    session_start();
}

function csrf_token(): string
{
    start_session();
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_verify(?string $token): bool
{
    start_session();
    return is_string($token) && !empty($_SESSION['csrf']) && hash_equals($_SESSION['csrf'], $token);
}

/** Signed timestamp used as a time-trap against instant bot submissions. */
function form_timestamp_token(): string
{
    $ts = (string) time();
    return $ts . '.' . hash_hmac('sha256', 'form-ts|' . $ts, app_secret());
}

/** Returns seconds since the token was issued, or null if the token is invalid. */
function form_timestamp_age(?string $token): ?int
{
    if (!is_string($token) || !preg_match('/^(\d{9,11})\.([a-f0-9]{64})$/', $token, $m)) {
        return null;
    }
    $expected = hash_hmac('sha256', 'form-ts|' . $m[1], app_secret());
    if (!hash_equals($expected, $m[2])) {
        return null;
    }
    return time() - (int) $m[1];
}

/** Session flash storage (used for no-JS form submissions). */
function flash(string $key, mixed $value = null): mixed
{
    start_session();
    if (func_num_args() === 2) {
        $_SESSION['_flash'][$key] = $value;
        return null;
    }
    $v = $_SESSION['_flash'][$key] ?? null;
    unset($_SESSION['_flash'][$key]);
    return $v;
}

/* --------------------------------------------------------------------------
 * Misc
 * ----------------------------------------------------------------------- */

function json_ld(array $data): string
{
    return '<script type="application/ld+json">'
        . json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP)
        . '</script>';
}

function client_ip(): string
{
    // Only REMOTE_ADDR is trusted. If you run behind a reverse proxy/CDN,
    // configure the web server (e.g. mod_remoteip) to set REMOTE_ADDR correctly.
    return (string) ($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0');
}

function wants_json(): bool
{
    return str_contains((string) ($_SERVER['HTTP_ACCEPT'] ?? ''), 'application/json');
}

function redirect(string $to, int $status = 303): never
{
    header('Location: ' . $to, true, $status);
    exit;
}
