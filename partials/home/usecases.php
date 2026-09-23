<?php declare(strict_types=1); ?>
<section class="section section--usecases" id="use-cases" aria-labelledby="uc-title">
  <div class="container-xl">
    <div class="section-head section-head--split">
      <div>
        <p class="eyebrow" data-reveal>// use cases</p>
        <h2 class="section-title" id="uc-title" data-reveal>Built for teams that depend on their logs.</h2>
      </div>
      <p class="section-lead" data-reveal>
        Different workloads stress different parts of the platform: burst ingest, long retention, many sources, strict separation.
        The architecture follows the workload.
      </p>
    </div>
    <?php partial('components/usecase-grid'); ?>
  </div>
</section>
