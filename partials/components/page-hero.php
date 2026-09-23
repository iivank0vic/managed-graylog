<?php
/**
 * Inner page header with breadcrumbs.
 * @var array  $crumbs  [label => path]
 * @var string $eyebrow
 * @var string $title
 * @var string $lead
 * @var ?array $spec    optional key => value block rendered as a config panel
 * @var ?string $specName
 */
declare(strict_types=1);
$spec     = $spec ?? null;
$specName = $specName ?? 'spec.yaml';
?>
<section class="page-hero<?= $spec ? ' page-hero--spec' : '' ?>">
  <div class="page-hero__bg" aria-hidden="true"><div class="grid-bg"></div><div class="hero__glow hero__glow--a"></div></div>
  <div class="container-xl">
    <nav class="crumbs mono" aria-label="Breadcrumb">
      <ol>
        <li><a href="/">home</a></li>
        <?php $n = count($crumbs); $i = 0; foreach ($crumbs as $label => $path): $i++; ?>
          <li><?php if ($i === $n): ?><span aria-current="page"><?= e(strtolower($label)) ?></span><?php else: ?><a href="<?= e($path) ?>"><?= e(strtolower($label)) ?></a><?php endif; ?></li>
        <?php endforeach; ?>
      </ol>
    </nav>
    <div class="page-hero__grid">
      <div>
        <p class="eyebrow"><?= e($eyebrow) ?></p>
        <h1 class="page-hero__title"><?= e($title) ?></h1>
        <p class="page-hero__lead"><?= e($lead) ?></p>
        <?php if (!empty($ctas)): ?>
          <div class="hero__ctas">
            <a class="btn-x btn-x--primary" href="<?= e(url('/contact')) ?>#request">Design My Deployment <?= icon('arrow-right') ?></a>
            <a class="btn-x btn-x--ghost" href="<?= e(url('/contact')) ?>">Talk to an Engineer</a>
          </div>
        <?php endif; ?>
      </div>
      <?php if ($spec): ?>
        <div class="spec spec--hero" aria-label="Summary">
          <div class="spec__chrome mono"><span class="cp__dots" aria-hidden="true"><i></i><i></i><i></i></span> <?= e($specName) ?></div>
          <dl class="spec__dl mono">
            <?php foreach ($spec as $k => $v): ?>
              <div><dt><?= e($k) ?>:</dt><dd><?= e($v) ?></dd></div>
            <?php endforeach; ?>
          </dl>
        </div>
      <?php endif; ?>
    </div>
  </div>
</section>
