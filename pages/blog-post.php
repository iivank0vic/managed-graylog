<?php
/** @var string $slug */
declare(strict_types=1);

$post = blog_post($slug);
$path = '/blog/' . $slug;

$page['schema'][] = array_filter([
    '@type'            => 'TechArticle',
    '@id'              => abs_url($path) . '#article',
    'headline'         => $post['title'],
    'description'      => $post['description'],
    'datePublished'    => $post['date'],
    'dateModified'     => $post['updated'] ?: $post['date'],
    'author'           => ['@type' => 'Organization', 'name' => $post['author'] ?? config('site_name')],
    'publisher'        => ['@id' => org_id()],
    'mainEntityOfPage' => abs_url($path),
    'keywords'         => implode(', ', $post['tags']),
]);
?>
<article class="article">
  <?php partial('components/page-hero', [
      'crumbs'  => $page['crumbs'],
      'eyebrow' => '// ' . strtolower(implode(' · ', $post['tags'])),
      'title'   => $post['title'],
      'lead'    => $post['description'],
  ]); ?>
  <div class="container-xl">
    <p class="meta mono">
      Published <time datetime="<?= e($post['date']) ?>"><?= e($post['date']) ?></time>
      <?php if ($post['updated']): ?> · updated <time datetime="<?= e($post['updated']) ?>"><?= e($post['updated']) ?></time><?php endif; ?>
    </p>
    <div class="prose prose--wide prose--article">
      <?= $post['body'] /* trusted, author-controlled HTML from content/blog */ ?>
    </div>
  </div>
</article>

<?php partial('components/cta-band'); ?>
