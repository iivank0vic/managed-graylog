<?php
/**
 * Route table: clean URL => page definition.
 * Also used by bin/build-sitemap.php, so every indexable page must be listed here.
 *
 * Keys per route:
 *   view         file in /pages (without .php)
 *   title        <title> (site name is appended automatically unless 'title_full' is set)
 *   description  meta description (aim for 120–160 characters)
 *   crumbs       breadcrumb trail [label => path] excluding Home
 *   params       extra variables passed to the view
 *   index        false => noindex, excluded from sitemap
 *   priority     sitemap priority hint
 */
declare(strict_types=1);

$routes = [
    '/' => [
        'view'        => 'home',
        'title_full'  => 'Managed Graylog — Graylog Infrastructure, Deployment & Consulting',
        'description' => 'Production-grade Graylog infrastructure deployed, secured and maintained on Hetzner, AWS, Google Cloud, Azure or your own servers. Independent engineers, not a template.',
        'priority'    => '1.0',
        'form'        => true,
    ],
    '/services' => [
        'view'        => 'services-index',
        'title'       => 'Graylog Services — Deployment, Clusters, Migration & Maintenance',
        'description' => 'Graylog deployment, cluster architecture, migration, maintenance, managed infrastructure and consulting — engineered around your ingest rate, retention and availability needs.',
        'crumbs'      => ['Services' => '/services'],
        'priority'    => '0.9',
    ],
    '/infrastructure' => [
        'view'        => 'infrastructure-index',
        'title'       => 'Graylog Hosting Infrastructure — Hetzner, AWS, Google Cloud, Azure',
        'description' => 'Run Graylog where it makes sense: Hetzner cloud and dedicated servers, AWS, Google Cloud, Azure, OVHcloud or your own hardware. Provider-specific architecture, not copy-paste.',
        'crumbs'      => ['Infrastructure' => '/infrastructure'],
        'priority'    => '0.8',
    ],
    '/solutions' => [
        'view'        => 'solutions-index',
        'title'       => 'Centralized Logging Solutions with Graylog',
        'description' => 'Graylog logging architectures for SaaS, e-commerce, hosting, Kubernetes, Linux fleets, security monitoring, enterprise IT and MSPs.',
        'crumbs'      => ['Solutions' => '/solutions'],
        'priority'    => '0.8',
    ],
    '/pricing' => [
        'view'        => 'pricing',
        'title'       => 'Pricing & Quotes for Graylog Deployment and Management',
        'description' => 'How pricing works for Graylog deployment, migration, managed infrastructure, maintenance and consulting. Every environment is scoped individually — request a quote.',
        'crumbs'      => ['Pricing' => '/pricing'],
        'priority'    => '0.7',
    ],
    '/faq' => [
        'view'        => 'faq',
        'title'       => 'Graylog FAQ — Deployment, Clusters, OpenSearch, Data Node & Licensing',
        'description' => 'Answers about Graylog Open, Graylog Data Node, OpenSearch, HA clusters, deployment on Hetzner, AWS and Google Cloud, backups, TLS, monitoring and licensing.',
        'crumbs'      => ['FAQ' => '/faq'],
        'priority'    => '0.7',
    ],
    '/contact' => [
        'view'        => 'contact',
        'title'       => 'Request a Graylog Deployment Plan',
        'description' => 'Tell us about your environment, log volume and retention needs. An engineer reviews it and replies with a proposed architecture and next steps.',
        'crumbs'      => ['Contact' => '/contact'],
        'priority'    => '0.8',
        'form'        => true,
    ],
    '/licensing' => [
        'view'        => 'licensing',
        'title'       => 'Software & Licensing — Graylog, OpenSearch, MongoDB',
        'description' => 'How software licensing relates to our services: Graylog Open (SSPL), Graylog Enterprise and Security, OpenSearch (Apache 2.0), MongoDB and Elasticsearch. Not legal advice.',
        'crumbs'      => ['Licensing' => '/licensing'],
        'priority'    => '0.5',
    ],
    '/blog' => [
        'view'        => 'blog-index',
        'title'       => 'Graylog Engineering Notes & Guides',
        'description' => 'Technical articles on Graylog architecture, sizing, retention, OpenSearch and Data Node, migrations, security and operations.',
        'crumbs'      => ['Blog' => '/blog'],
        'priority'    => '0.6',
    ],
    '/privacy' => [
        'view'        => 'privacy',
        'title'       => 'Privacy Policy',
        'description' => 'How managed-graylog.com processes personal data submitted through this website.',
        'crumbs'      => ['Privacy' => '/privacy'],
        'priority'    => '0.2',
    ],
    '/terms' => [
        'view'        => 'terms',
        'title'       => 'Terms of Use',
        'description' => 'Terms of use for the managed-graylog.com website.',
        'crumbs'      => ['Terms' => '/terms'],
        'priority'    => '0.2',
    ],
];

foreach (data('services') as $slug => $s) {
    $routes['/services/' . $slug] = [
        'view'        => 'service',
        'title'       => $s['title'],
        'description' => $s['description'],
        'crumbs'      => ['Services' => '/services', $s['name'] => '/services/' . $slug],
        'params'      => ['slug' => $slug],
        'priority'    => '0.8',
    ];
}

foreach (data('infrastructure') as $slug => $p) {
    if (empty($p['page'])) {
        continue;
    }
    $routes['/infrastructure/' . $slug] = [
        'view'        => 'infrastructure',
        'title'       => $p['title'],
        'description' => $p['description'],
        'crumbs'      => ['Infrastructure' => '/infrastructure', $p['name'] => '/infrastructure/' . $slug],
        'params'      => ['slug' => $slug],
        'priority'    => '0.7',
    ];
}

foreach (data('solutions')['pages'] as $slug => $p) {
    $routes['/solutions/' . $slug] = [
        'view'        => 'solution',
        'title'       => $p['title'],
        'description' => $p['description'],
        'crumbs'      => ['Solutions' => '/solutions', $p['name'] => '/solutions/' . $slug],
        'params'      => ['slug' => $slug],
        'priority'    => '0.7',
    ];
}

require_once __DIR__ . '/blog.php';
foreach (blog_posts() as $post) {
    $routes['/blog/' . $post['slug']] = [
        'view'        => 'blog-post',
        'title'       => $post['title'],
        'description' => $post['description'],
        'crumbs'      => ['Blog' => '/blog', $post['title'] => '/blog/' . $post['slug']],
        'params'      => ['slug' => $post['slug']],
        'priority'    => '0.6',
        'type'        => 'article',
    ];
}

// Keep the blog index out of search results until it has real articles.
$routes['/blog']['index'] = blog_posts() !== [];

return $routes;
