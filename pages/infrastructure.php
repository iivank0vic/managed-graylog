<?php
/** @var string $slug */
declare(strict_types=1);

$p = data('infrastructure')[$slug];
$path = '/infrastructure/' . $slug;
$page['schema'][] = schema_service('Graylog deployment on ' . $p['name'], $p['description'], $path, 'Graylog deployment and hosting infrastructure');

partial('components/page-hero', [
    'crumbs'  => $page['crumbs'],
    'eyebrow' => '// infrastructure / ' . $slug,
    'title'   => $p['h1'],
    'lead'    => $p['lead'],
    'ctas'    => true,
]);
?>
<section class="section section--tight">
  <div class="container-xl">
    <div class="prose-grid">
      <div class="prose">
        <?php foreach ($p['intro'] as $para): ?><p><?= e($para) ?></p><?php endforeach; ?>
      </div>
      <aside class="fit-card">
        <h2 class="fit-card__title">Design considerations</h2>
        <ul class="check-list check-list--warn">
          <?php foreach ($p['considerations'] as $c): ?><li><?= icon('triangle-alert') ?><span><?= e($c) ?></span></li><?php endforeach; ?>
        </ul>
      </aside>
    </div>
  </div>
</section>

<section class="section section--tight">
  <div class="container-xl">
    <div class="section-head">
      <p class="eyebrow">// reference build</p>
      <h2 class="section-title section-title--sm">How a Graylog environment on <?= e($p['name']) ?> is typically built</h2>
    </div>
    <div class="scope-grid">
      <?php foreach ($p['components'] as $i => [$t, $d]): ?>
        <div class="scope" data-reveal>
          <span class="scope__idx mono"><?= e(strtolower($t)) ?></span>
          <h3 class="scope__title"><?= e($t) ?></h3>
          <p class="scope__text"><?= e($d) ?></p>
        </div>
      <?php endforeach; ?>
    </div>
    <p class="fineprint"><?= e($p['name']) ?> is a trademark of its respective owner. We are an independent provider and not an official <?= e($p['name']) ?> partner. Provider capabilities change; we verify current features and pricing during design.</p>
  </div>
</section>

<section class="section section--tight">
  <div class="container-xl">
    <div class="section-head">
      <p class="eyebrow">// other environments</p>
      <h2 class="section-title section-title--sm">Comparing providers?</h2>
    </div>
    <?php partial('components/provider-grid'); ?>
  </div>
</section>

<?php partial('components/cta-band', ['title' => 'Planning Graylog on ' . $p['name'] . '?', 'text' => 'Send us your ingest volume and retention requirements and we will outline a topology and sizing for ' . $p['name'] . '.']); ?>
