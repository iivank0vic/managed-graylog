<?php
declare(strict_types=1);

$items = [];
foreach (data('faq') as $group) {
    foreach ($group as $f) {
        if (!empty($f[2])) {
            $items[] = $f;
        }
    }
}
?>
<section class="section section--faq" id="faq" aria-labelledby="faq-title">
  <div class="container-xl">
    <div class="faq-layout">
      <div class="faq-layout__head">
        <p class="eyebrow" data-reveal>// faq</p>
        <h2 class="section-title" id="faq-title" data-reveal>Questions engineers ask us.</h2>
        <p class="section-lead" data-reveal>Straight answers about deployment, operations, OpenSearch, Data Node and licensing.</p>
        <a class="link-arrow" href="<?= e(url('/faq')) ?>" data-reveal>All questions <?= icon('arrow-right') ?></a>
      </div>
      <?php partial('components/faq-list', ['items' => $items, 'idPrefix' => 'home-faq']); ?>
    </div>
  </div>
</section>
