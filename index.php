<?php
/**
 * Front controller. All non-file requests are rewritten here by .htaccess.
 */
declare(strict_types=1);

require __DIR__ . '/includes/bootstrap.php';

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$path   = (string) parse_url((string) ($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH);
$path   = '/' . trim(rawurldecode($path), '/');

// Canonicalise: /index.php and trailing slashes redirect to the clean URL.
if ($path === '/index.php') {
    redirect('/', 301);
}
$requested = (string) parse_url((string) ($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH);
if ($requested !== '/' && str_ends_with($requested, '/') && ($method === 'GET' || $method === 'HEAD')) {
    $qs = (string) ($_SERVER['QUERY_STRING'] ?? '');
    redirect($path . ($qs !== '' ? '?' . $qs : ''), 301);
}

$GLOBALS['CURRENT_PATH'] = $path;
$routes = require __DIR__ . '/includes/routes.php';

// Form submissions.
if ($path === '/contact' && $method === 'POST') {
    require __DIR__ . '/includes/contact-handler.php';
    handle_contact_submission();
    exit;
}

if (!in_array($method, ['GET', 'HEAD'], true)) {
    http_response_code(405);
    header('Allow: GET, HEAD');
    exit;
}

$route = $routes[$path] ?? null;
if ($route === null) {
    http_response_code(404);
    $route = [
        'view'        => '404',
        'title'       => 'Page not found',
        'description' => 'The requested page does not exist.',
        'index'       => false,
    ];
}

$page = $route + ['path' => $path, 'crumbs' => [], 'params' => [], 'schema' => [], 'index' => true, 'type' => 'website'];

if (!empty($page['form'])) {
    start_session();
    header('Cache-Control: private, no-cache');
} else {
    header('Cache-Control: public, max-age=300');
}

// Render the view first so it can add schema nodes or adjust meta, then wrap it in the layout.
ob_start();
(static function (string $__view, array &$page): void {
    extract($page['params'], EXTR_SKIP);
    require __DIR__ . '/pages/' . $__view . '.php';
})($page['view'], $page);
$content = (string) ob_get_clean();

require __DIR__ . '/includes/header.php';
echo $content;
require __DIR__ . '/includes/footer.php';
