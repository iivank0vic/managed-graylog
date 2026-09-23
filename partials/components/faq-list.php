<?php
/**
 * FAQ list using native <details>/<summary> (keyboard accessible, works without JS).
 * @var array<int, array{0:string,1:string}> $items
 */
declare(strict_types=1);
$idPrefix = $idPrefix ?? 'faq';
?>
<div class="faq">
  <?php foreach ($items as $i => $f): ?>
    <details class="faq__item" id="<?= e($idPrefix . '-' . ($i + 1)) ?>" data-reveal>
      <summary class="faq__q">
        <span><?= e($f[0]) ?></span>
        <span class="faq__icon" aria-hidden="true"><?= icon('plus') ?></span>
      </summary>
      <div class="faq__a"><p><?= e($f[1]) ?></p></div>
    </details>
  <?php endforeach; ?>
</div>
