<?php
/**
 * Blog post template. Copy to content/blog/your-slug.php (lowercase, hyphens).
 * Files starting with "_" are ignored. The slug becomes /blog/your-slug.
 *
 * 'body' is trusted author HTML (not user input). Use h2/h3, p, ul, pre><code, figure.
 * Run `php bin/build-sitemap.php` after publishing.
 */
declare(strict_types=1);

return [
    'draft'       => true,
    'title'       => 'How to design a production Graylog cluster',
    'description' => '120–160 character summary used for the meta description and article cards.',
    'date'        => '2026-01-01',   // ISO date, published
    'updated'     => null,           // ISO date when materially updated
    'author'      => 'managed-graylog engineering',
    'tags'        => ['Architecture', 'Clusters'],
    'reading_time'=> 8,              // minutes
    'body'        => <<<'HTML'
<p>Intro paragraph.</p>
<h2>Section heading</h2>
<p>Content.</p>
<pre><code>graylog:
  nodes: 2</code></pre>
HTML,
];
