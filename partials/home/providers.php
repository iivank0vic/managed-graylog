<?php declare(strict_types=1); ?>
<section class="section section--providers" id="infrastructure" aria-labelledby="providers-title">
  <div class="container-xl">
    <div class="section-head section-head--split">
      <div>
        <p class="eyebrow" data-reveal>// infrastructure</p>
        <h2 class="section-title" id="providers-title" data-reveal>Run it where it makes sense.</h2>
      </div>
      <p class="section-lead" data-reveal>
        We can deploy into your existing infrastructure or provision a new environment around your requirements.
        The provider is a design decision: cost per TB, data location, network paths and what your team already operates.
      </p>
    </div>

    <?php partial('components/provider-grid'); ?>

    <p class="fineprint" data-reveal>Provider names are used to describe where we can deploy. We are not an official partner of any provider listed.</p>
  </div>
</section>
