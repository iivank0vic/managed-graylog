<?php
/** @var string $slug */
declare(strict_types=1);

$p = data('solutions')['pages'][$slug];
$page['schema'][] = schema_service($p['name'], $p['description'], '/solutions/' . $slug, 'Centralized logging architecture');
$page['schema'][] = schema_faq($p['faqs']);

partial('components/page-hero', [
    'crumbs'  => $page['crumbs'],
    'eyebrow' => '// ' . strtolower($p['eyebrow']),
    'title'   => $p['h1'],
    'lead'    => $p['lead'],
    'ctas'    => true,
]);
?>
<section class="section section--tight">
  <div class="container-xl">
    <div class="section-head">
      <p class="eyebrow">// pipeline</p>
      <h2 class="section-title section-title--sm">From container stdout to a searchable stream</h2>
    </div>
    <ol class="pipeline">
      <?php foreach ($p['pipeline'] as $i => [$id, $t, $d]): ?>
        <li class="pipeline__step" data-reveal>
          <span class="pipeline__idx mono"><?= sprintf('%02d', $i + 1) ?></span>
          <strong><?= e($t) ?></strong>
          <span class="mono"><?= e($d) ?></span>
        </li>
      <?php endforeach; ?>
    </ol>
  </div>
</section>

<section class="section section--tight">
  <div class="container-xl">
    <div class="prose prose--wide">
      <?php foreach ($p['sections'] as $sec): ?>
        <h2><?= e($sec['title']) ?></h2>
        <?php foreach ($sec['body'] as $para): ?><p><?= e($para) ?></p><?php endforeach; ?>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section section--tight">
  <div class="container-xl">
    <div class="faq-layout">
      <div class="faq-layout__head">
        <p class="eyebrow">// faq</p>
        <h2 class="section-title section-title--sm">Kubernetes logging questions</h2>
      </div>
      <?php partial('components/faq-list', ['items' => $p['faqs'], 'idPrefix' => $slug]); ?>
    </div>
  </div>
</section>

<?php partial('components/cta-band'); ?>
