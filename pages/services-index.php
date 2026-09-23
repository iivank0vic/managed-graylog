<?php
declare(strict_types=1);

$services = data('services');
$list = [];
foreach ($services as $slug => $s) {
    $list[] = ['@type' => 'ListItem', 'position' => count($list) + 1, 'url' => abs_url('/services/' . $slug), 'name' => $s['name']];
}
$page['schema'][] = ['@type' => 'ItemList', 'name' => 'Graylog services', 'itemListElement' => $list];

partial('components/page-hero', [
    'crumbs'  => $page['crumbs'],
    'eyebrow' => '// services',
    'title'   => 'Graylog services, from first deployment to long-term operations',
    'lead'    => 'Six ways we work with Graylog environments. Most engagements combine two or three — for example a migration followed by ongoing maintenance.',
    'ctas'    => true,
]);
?>
<section class="section section--tight">
  <div class="container-xl">
    <div class="svc-list">
      <?php foreach ($services as $slug => $s): ?>
        <a class="svc-row" href="<?= e(url('/services/' . $slug)) ?>" data-reveal>
          <span class="svc__icon"><?= icon($s['icon']) ?></span>
          <div class="svc-row__main">
            <h2 class="svc-row__title"><?= e($s['name']) ?></h2>
            <p class="svc-row__text"><?= e($s['summary']) ?></p>
          </div>
          <ul class="layer__tags mono">
            <?php foreach (array_slice(array_column($s['scope'], 0), 0, 3) as $t): ?><li><?= e(strtolower($t)) ?></li><?php endforeach; ?>
          </ul>
          <span class="svc-row__go" aria-hidden="true"><?= icon('arrow-right') ?></span>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section section--tight">
  <div class="container-xl">
    <div class="section-head">
      <p class="eyebrow">// engagement model</p>
      <h2 class="section-title section-title--sm">How engagements run</h2>
    </div>
    <?php partial('components/process'); ?>
  </div>
</section>

<?php partial('components/cta-band'); ?>
