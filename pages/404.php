<?php declare(strict_types=1); ?>
<section class="page-hero page-hero--404">
  <div class="page-hero__bg" aria-hidden="true"><div class="grid-bg"></div></div>
  <div class="container-xl">
    <p class="eyebrow mono">HTTP 404</p>
    <h1 class="page-hero__title">No messages match this route.</h1>
    <pre class="term mono" aria-hidden="true"><code>$ curl -I <?= e(abs_url($page['path'])) ?>

HTTP/2 404
x-hint: try one of the links below</code></pre>
    <div class="hero__ctas">
      <a class="btn-x btn-x--primary" href="/">Back to home</a>
      <a class="btn-x btn-x--ghost" href="<?= e(url('/services')) ?>">Services</a>
      <a class="btn-x btn-x--ghost" href="<?= e(url('/contact')) ?>">Contact</a>
    </div>
  </div>
</section>
