<?php declare(strict_types=1); ?>
<ul class="uc-grid">
  <?php foreach (data('solutions')['use_cases'] as $uc): ?>
    <?php [$id, $title, $ic, $text] = $uc; $href = $uc[4] ?? null; ?>
    <li class="uc" id="uc-<?= e($id) ?>" data-reveal>
      <span class="uc__icon"><?= icon($ic) ?></span>
      <h3 class="uc__title"><?php if ($href): ?><a href="<?= e($href) ?>"><?= e($title) ?></a><?php else: ?><?= e($title) ?><?php endif; ?></h3>
      <p class="uc__text"><?= e($text) ?></p>
    </li>
  <?php endforeach; ?>
</ul>
