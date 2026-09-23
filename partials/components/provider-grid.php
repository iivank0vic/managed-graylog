<?php declare(strict_types=1); ?>
<div class="prov-grid">
  <?php foreach (data('infrastructure') as $slug => $p): ?>
    <?php $tag = !empty($p['page']) ? 'a' : 'div'; ?>
    <<?= $tag ?> class="prov<?= $tag === 'a' ? ' prov--link' : ' prov--compact' ?>"<?= $tag === 'a' ? ' href="' . e(url('/infrastructure/' . $slug)) . '"' : '' ?> data-reveal>
      <span class="glyph" aria-hidden="true"><?= e($p['glyph']) ?></span>
      <span class="prov__tag mono"><?= e($p['tag']) ?></span>
      <h3 class="prov__name"><?= e($p['name']) ?></h3>
      <p class="prov__text"><?= e($p['summary']) ?></p>
      <?php if ($tag === 'a'): ?>
        <span class="prov__more mono">graylog-on-<?= e($slug) ?> <?= icon('arrow-right') ?></span>
      <?php endif; ?>
    </<?= $tag ?>>
  <?php endforeach; ?>
</div>
