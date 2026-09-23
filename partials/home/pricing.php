<?php declare(strict_types=1); ?>
<section class="section section--pricing" id="pricing" aria-labelledby="pricing-title">
  <div class="container-xl">
    <div class="section-head section-head--split">
      <div>
        <p class="eyebrow" data-reveal>// pricing</p>
        <h2 class="section-title" id="pricing-title" data-reveal>Every environment is different.</h2>
      </div>
      <div>
        <p class="section-lead" data-reveal>
          A 10 GB/day single-node setup and a multi-terabyte HA cluster are different projects. Rather than publish numbers that fit nobody,
          we scope each environment and send a written quote.
        </p>
        <a class="btn-x btn-x--ghost" href="#request" data-reveal>Request a Quote <?= icon('arrow-right') ?></a>
      </div>
    </div>
    <?php partial('components/pricing-model'); ?>
  </div>
</section>
