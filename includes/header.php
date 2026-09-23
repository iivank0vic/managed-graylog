<?php
/**
 * Document head + site navigation.
 * Expects $page (route definition merged with runtime values).
 */
declare(strict_types=1);

$title       = page_title($page);
$description = (string) ($page['description'] ?? '');
$canonical   = abs_url($page['path']);
$ogImage     = abs_url($page['og_image'] ?? '/assets/img/og-default.png');
$robots      = !empty($page['index']) ? 'index, follow, max-image-preview:large' : 'noindex, follow';
$isHome      = $page['path'] === '/';
$ctaHref     = $isHome ? '#request' : url('/contact') . '#request';

$schema   = [schema_organization()];
if ($isHome) {
    $schema[] = schema_website();
}
$schema[] = schema_breadcrumbs($page['crumbs']);
foreach ($page['schema'] as $node) {
    $schema[] = $node;
}

$nav = [
    ['Services', '/services', true],
    ['Solutions', '/solutions', true],
    ['Infrastructure', '/infrastructure', true],
    ['How It Works', '/#how-it-works', false],
    ['Pricing', '/pricing', false],
    ['FAQ', '/faq', false],
    ['Contact', '/contact', false],
];
$services = data('services');
$infra    = array_filter(data('infrastructure'), static fn(array $p): bool => !empty($p['page']));
?><!doctype html>
<html lang="<?= e(config('locale')) ?>" class="no-js">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($title) ?></title>
<meta name="description" content="<?= e($description) ?>">
<meta name="robots" content="<?= e($robots) ?>">
<link rel="canonical" href="<?= e($canonical) ?>">
<meta name="theme-color" content="<?= e(config('theme_color')) ?>">
<meta name="color-scheme" content="dark">

<link rel="preload" href="/assets/fonts/inter-latin-wght-normal.woff2" as="font" type="font/woff2" crossorigin>
<link rel="stylesheet" href="<?= e(asset('css/site.min.css')) ?>">
<script src="<?= e(asset('js/main.js')) ?>" defer></script>

<link rel="icon" href="/assets/img/favicon.svg" type="image/svg+xml">
<link rel="icon" href="/favicon.ico" sizes="32x32">
<link rel="apple-touch-icon" href="/assets/img/apple-touch-icon.png">
<link rel="manifest" href="/site.webmanifest">

<meta property="og:type" content="<?= e($page['type'] === 'article' ? 'article' : 'website') ?>">
<meta property="og:site_name" content="<?= e(config('site_name')) ?>">
<meta property="og:title" content="<?= e($page['og_title'] ?? $title) ?>">
<meta property="og:description" content="<?= e($description) ?>">
<meta property="og:url" content="<?= e($canonical) ?>">
<meta property="og:image" content="<?= e($ogImage) ?>">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:image:alt" content="Managed Graylog — Graylog infrastructure, deployed and operated">
<meta property="og:locale" content="en_US">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?= e($page['og_title'] ?? $title) ?>">
<meta name="twitter:description" content="<?= e($description) ?>">
<meta name="twitter:image" content="<?= e($ogImage) ?>">
<?= schema_graph($schema) ?>

</head>
<body class="<?= $isHome ? 'is-home' : 'is-inner' ?>">
<a class="skip-link" href="#main">Skip to content</a>

<header class="site-header" data-header>
  <div class="container-xl site-header__inner">
    <a class="brand" href="<?= e(url('/')) ?>" aria-label="Managed Graylog home">
      <?= brand_mark() ?>
      <span class="brand__word">Managed <span class="brand__accent">Graylog</span></span>
    </a>

    <nav class="primary-nav" aria-label="Primary">
      <ul class="primary-nav__list">
        <?php foreach ($nav as [$label, $href, $section]): ?>
          <?php $active = $section ? is_active($href, true) : is_active($href); ?>
          <li class="primary-nav__item<?= in_array($label, ['Services', 'Infrastructure'], true) ? ' has-menu' : '' ?>">
            <a class="primary-nav__link<?= $active ? ' is-active' : '' ?>" href="<?= e($href) ?>"<?= $active ? ' aria-current="page"' : '' ?>><?= e($label) ?></a>
            <?php if ($label === 'Services'): ?>
              <div class="mega">
                <div class="mega__grid">
                  <?php foreach ($services as $slug => $s): ?>
                    <a class="mega__item" href="<?= e(url('/services/' . $slug)) ?>">
                      <span class="mega__icon"><?= icon($s['icon']) ?></span>
                      <span><strong><?= e($s['name']) ?></strong><small><?= e($s['summary']) ?></small></span>
                    </a>
                  <?php endforeach; ?>
                </div>
                <a class="mega__footer" href="<?= e(url('/services')) ?>">All services <?= icon('arrow-right') ?></a>
              </div>
            <?php elseif ($label === 'Infrastructure'): ?>
              <div class="mega mega--narrow">
                <div class="mega__grid mega__grid--1">
                  <?php foreach ($infra as $slug => $p): ?>
                    <a class="mega__item" href="<?= e(url('/infrastructure/' . $slug)) ?>">
                      <span class="glyph glyph--sm" aria-hidden="true"><?= e($p['glyph']) ?></span>
                      <span><strong>Graylog on <?= e($p['name']) ?></strong><small><?= e($p['tag']) ?></small></span>
                    </a>
                  <?php endforeach; ?>
                </div>
                <a class="mega__footer" href="<?= e(url('/infrastructure')) ?>">All environments <?= icon('arrow-right') ?></a>
              </div>
            <?php endif; ?>
          </li>
        <?php endforeach; ?>
      </ul>
    </nav>

    <div class="site-header__actions">
      <a class="btn-x btn-x--primary btn-x--sm d-none d-lg-inline-flex" href="<?= e($ctaHref) ?>">Design My Graylog</a>
      <button class="menu-toggle d-lg-none" type="button" aria-expanded="false" aria-controls="mobile-menu" data-menu-toggle>
        <span class="visually-hidden">Open menu</span>
        <span class="menu-toggle__bars" aria-hidden="true"><span></span><span></span></span>
      </button>
    </div>
  </div>
</header>

<div class="mobile-menu" id="mobile-menu" data-mobile-menu hidden>
  <nav class="mobile-menu__nav" aria-label="Mobile">
    <ul>
      <?php foreach ($nav as $i => [$label, $href]): ?>
        <li><a href="<?= e($href) ?>"><span class="mobile-menu__idx">0<?= $i + 1 ?></span><?= e($label) ?></a></li>
      <?php endforeach; ?>
    </ul>
    <div class="mobile-menu__sub">
      <p class="eyebrow">Services</p>
      <?php foreach ($services as $slug => $s): ?>
        <a href="<?= e(url('/services/' . $slug)) ?>"><?= e($s['name']) ?></a>
      <?php endforeach; ?>
    </div>
    <a class="btn-x btn-x--primary w-100" href="<?= e($ctaHref) ?>">Design My Graylog</a>
  </nav>
</div>

<main id="main" tabindex="-1">
