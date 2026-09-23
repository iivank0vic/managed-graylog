<?php
/**
 * Generates sitemap.xml from the route table. Run after adding pages or blog posts:
 *   php bin/build-sitemap.php
 */
declare(strict_types=1);

$_SERVER['REQUEST_URI'] = '/';
define('APP_ROOT', dirname(__DIR__));
$GLOBALS['APP_CONFIG'] = require APP_ROOT . '/includes/config.php';
require APP_ROOT . '/includes/functions.php';

$routes = require APP_ROOT . '/includes/routes.php';

$xml = new XMLWriter();
$xml->openMemory();
$xml->setIndent(true);
$xml->startDocument('1.0', 'UTF-8');
$xml->startElement('urlset');
$xml->writeAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');

$today = gmdate('Y-m-d');
foreach ($routes as $path => $route) {
    if (array_key_exists('index', $route) && $route['index'] === false) {
        continue;
    }
    $xml->startElement('url');
    $xml->writeElement('loc', abs_url($path));
    $xml->writeElement('lastmod', $today);
    $xml->writeElement('priority', $route['priority'] ?? '0.5');
    $xml->endElement();
}
$xml->endElement();
$xml->endDocument();

file_put_contents(APP_ROOT . '/sitemap.xml', $xml->outputMemory());
echo "sitemap.xml written (" . count($routes) . " routes considered)\n";
