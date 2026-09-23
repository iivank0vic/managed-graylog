<?php declare(strict_types=1); ?>
<section class="hero" aria-labelledby="hero-title">
  <div class="hero__bg" aria-hidden="true">
    <div class="grid-bg"></div>
    <div class="hero__glow hero__glow--a"></div>
    <div class="hero__glow hero__glow--b"></div>
  </div>

  <div class="container-xl hero__grid">
    <div class="hero__copy">
      <p class="status-pill" data-reveal>
        <span class="status-pill__dot" aria-hidden="true"></span>
        Independent Graylog engineering <span class="status-pill__sep" aria-hidden="true">/</span> deploy · operate · migrate
      </p>

      <h1 class="hero__title" id="hero-title" data-reveal>
        <span class="line">Your Logs.</span>
        <span class="line">Your Infrastructure.</span>
        <span class="line grad-text">Fully Managed.</span>
      </h1>

      <p class="hero__lead" data-reveal>
        Production-grade Graylog infrastructure deployed, secured and maintained on the cloud or servers you choose.
        Built around your ingestion rate, retention and availability requirements.
      </p>

      <div class="hero__ctas" data-reveal>
        <a class="btn-x btn-x--primary btn-x--lg" href="#request">Design My Deployment <?= icon('arrow-right') ?></a>
        <a class="btn-x btn-x--ghost btn-x--lg" href="<?= e(url('/contact')) ?>">Talk to an Engineer</a>
      </div>

      <div class="stack-strip" data-reveal>
        <p class="stack-strip__label mono">Stack we work with</p>
        <ul class="stack-strip__list">
          <?php foreach (['Graylog', 'Linux', 'Docker', 'Terraform', 'Ansible', 'AWS', 'Google Cloud', 'Hetzner'] as $t): ?>
            <li><?= e($t) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    </div>

    <div class="hero__visual" data-reveal>
      <?php partial('components/control-plane'); ?>
    </div>
  </div>
</section>
