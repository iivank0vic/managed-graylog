<?php
/**
 * Minimal file-based blog.
 *
 * Add an article by creating content/blog/{slug}.php that returns an array
 * (copy content/blog/_template.php). Files starting with "_" and entries with
 * 'draft' => true are ignored. No database required; can be swapped for a CMS later.
 */
declare(strict_types=1);

function blog_posts(): array
{
    static $posts = null;
    if ($posts !== null) {
        return $posts;
    }
    $posts = [];
    foreach (glob(APP_ROOT . '/content/blog/*.php') ?: [] as $file) {
        $base = basename($file, '.php');
        if (str_starts_with($base, '_') || !preg_match('/^[a-z0-9-]+$/', $base)) {
            continue;
        }
        $post = require $file;
        if (!is_array($post) || !empty($post['draft'])) {
            continue;
        }
        $post['slug'] = $base;
        $post += ['title' => $base, 'description' => '', 'date' => null, 'updated' => null, 'tags' => [], 'body' => ''];
        $posts[] = $post;
    }
    usort($posts, static fn(array $a, array $b): int => strcmp((string) $b['date'], (string) $a['date']));
    return $posts;
}

function blog_post(string $slug): ?array
{
    foreach (blog_posts() as $post) {
        if ($post['slug'] === $slug) {
            return $post;
        }
    }
    return null;
}

/** Article topics planned for the blog. Shown on /blog until real posts exist. */
function blog_planned_topics(): array
{
    return [
        ['How to design a production Graylog cluster', 'Architecture'],
        ['Graylog single node vs cluster: when to switch', 'Architecture'],
        ['How much storage does Graylog need?', 'Capacity'],
        ['Graylog retention planning with index sets', 'Capacity'],
        ['Graylog Data Node architecture explained', 'Search layer'],
        ['Graylog Open vs Enterprise: a technical comparison', 'Editions'],
        ['Running Graylog on Hetzner', 'Infrastructure'],
        ['Running Graylog on AWS', 'Infrastructure'],
        ['Running Graylog on Google Cloud', 'Infrastructure'],
        ['Graylog Docker deployment for production', 'Deployment'],
        ['Kubernetes logging with Graylog', 'Kubernetes'],
        ['How to migrate Graylog to new infrastructure', 'Migration'],
        ['How to monitor Graylog', 'Operations'],
        ['Securing Graylog with TLS end to end', 'Security'],
    ];
}
