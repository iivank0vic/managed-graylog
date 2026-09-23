<?php
/** @var string $slug */
declare(strict_types=1);

$all = data('services');
$s   = $all[$slug];
$path = '/services/' . $slug;

$page['schema'][] = schema_service($s['name'], $s['description'], $path);
$page['schema'][] = schema_faq($s['faqs']);

partial('components/page-hero', [
    'crumbs'   => $page['crumbs'],
    'eyebrow'  => '// ' . strtolower($s['eyebrow']),
    'title'    => $s['h1'],
    'lead'     => $s['lead'],
    'spec'     => $s['spec'],
    'specName' => $slug . '.yaml',
    'ctas'     => true,
]);
?>
<section class="section section--tight">
  <div class="container-xl">
    <div class="prose-grid">
      <div class="prose">
        <?php foreach ($s['intro'] as $p): ?><p><?= e($p) ?></p><?php endforeach; ?>
      </div>
      <aside class="fit-card" aria-labelledby="fit-title">
        <h2 class="fit-card__title" id="fit-title">A good fit when</h2>
        <ul class="check-list">
          <?php foreach ($s['fit'] as $f): ?><li><?= icon('check') ?><span><?= e($f) ?></span></li><?php endforeach; ?>
        </ul>
      </aside>
    </div>
  </div>
</section>

<section class="section section--tight">
  <div class="container-xl">
    <div class="section-head">
      <p class="eyebrow">// scope</p>
      <h2 class="section-title section-title--sm"><?= e($s['scope_title']) ?></h2>
    </div>
    <div class="scope-grid">
      <?php foreach ($s['scope'] as $i => [$t, $d]): ?>
        <div class="scope" data-reveal>
          <span class="scope__idx mono"><?= sprintf('%02d', $i + 1) ?></span>
          <h3 class="scope__title"><?= e($t) ?></h3>
          <p class="scope__text"><?= e($d) ?></p>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section section--tight">
  <div class="container-xl">
    <div class="split">
      <div>
        <p class="eyebrow">// deliverables</p>
        <h2 class="section-title section-title--sm">What you receive</h2>
        <ul class="deliv-list">
          <?php foreach ($s['deliverables'] as $d): ?><li><?= icon('file-text') ?><span><?= e($d) ?></span></li><?php endforeach; ?>
        </ul>
      </div>
      <div>
        <p class="eyebrow">// faq</p>
        <h2 class="section-title section-title--sm">Common questions</h2>
        <?php partial('components/faq-list', ['items' => $s['faqs'], 'idPrefix' => $slug]); ?>
      </div>
    </div>
  </div>
</section>

<section class="section section--tight">
  <div class="container-xl">
    <p class="eyebrow">// related services</p>
    <div class="related">
      <?php foreach ($s['related'] as $r): $rs = $all[$r]; ?>
        <a class="related__item" href="<?= e(url('/services/' . $r)) ?>">
          <span class="svc__icon"><?= icon($rs['icon']) ?></span>
          <span><strong><?= e($rs['name']) ?></strong><small><?= e($rs['summary']) ?></small></span>
          <?= icon('arrow-right') ?>
        </a>
      <?php endforeach; ?>
    </div>
    <p class="fineprint">Independent service provider — not affiliated with Graylog, Inc. Software licensing is subject to the applicable vendor license. <a href="<?= e(url('/licensing')) ?>">Licensing notes</a>.</p>
  </div>
</section>

<?php partial('components/cta-band'); ?>
