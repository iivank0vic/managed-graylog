<?php declare(strict_types=1);

$posts = blog_posts();
partial('components/page-hero', [
    'crumbs'  => $page['crumbs'],
    'eyebrow' => '// engineering notes',
    'title'   => 'Graylog engineering notes',
    'lead'    => 'Practical articles on designing, sizing, securing and operating Graylog infrastructure.',
]);
?>
<section class="section section--tight">
  <div class="container-xl">
    <?php if ($posts): ?>
      <div class="post-grid">
        <?php foreach ($posts as $post): ?>
          <article class="post-card" data-reveal>
            <p class="post-card__meta mono">
              <time datetime="<?= e($post['date']) ?>"><?= e($post['date']) ?></time>
              <?php if (!empty($post['reading_time'])): ?> · <?= (int) $post['reading_time'] ?> min<?php endif; ?>
            </p>
            <h2 class="post-card__title"><a href="<?= e(url('/blog/' . $post['slug'])) ?>"><?= e($post['title']) ?></a></h2>
            <p class="post-card__text"><?= e($post['description']) ?></p>
            <?php if ($post['tags']): ?>
              <ul class="layer__tags mono"><?php foreach ($post['tags'] as $t): ?><li><?= e(strtolower($t)) ?></li><?php endforeach; ?></ul>
            <?php endif; ?>
          </article>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <div class="notice mono">No articles published yet. Topics in preparation:</div>
      <ul class="topic-list">
        <?php foreach (blog_planned_topics() as [$t, $cat]): ?>
          <li><span class="mono"><?= e(strtolower($cat)) ?></span><?= e($t) ?></li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
  </div>
</section>

<?php partial('components/cta-band'); ?>
