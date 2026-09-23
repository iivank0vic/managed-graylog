<?php
// Router for PHP's built-in dev server: `php -S 127.0.0.1:8080 bin/router.php`
// Mirrors the .htaccess rules: static files are served directly, private dirs are denied.
$root = dirname(__DIR__);
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if (preg_match('#^/(includes|partials|pages|content|storage|docs|src|bin|node_modules)(/|$)#', $path) || preg_match('#/\.#', $path)) {
    http_response_code(403);
    exit('Forbidden');
}
$file = realpath($root . $path);
if ($path !== '/' && $file && str_starts_with($file, $root) && is_file($file) && !str_ends_with($file, '.php')) {
    return false;
}
chdir($root);
require $root . '/index.php';
