<?php declare(strict_types=1);

partial('components/page-hero', [
    'crumbs'  => $page['crumbs'],
    'eyebrow' => '// contact',
    'title'   => 'Tell us what you have. We\'ll design what you need.',
    'lead'    => 'Use the assessment below for new deployments, migrations, takeovers or questions. Prefer email? Write to ' . config('contact_email') . '.',
]);
?>
<section class="section section--request section--tight" id="request" aria-label="Deployment request">
  <div class="container-xl">
    <?php partial('components/request-form'); ?>
  </div>
</section>

<section class="section section--tight">
  <div class="container-xl">
    <div class="section-head">
      <p class="eyebrow">// after you send it</p>
      <h2 class="section-title section-title--sm">What happens next</h2>
    </div>
    <?php partial('components/process'); ?>
  </div>
</section>
