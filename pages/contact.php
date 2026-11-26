<?php declare(strict_types=1);

partial('components/page-hero', [
    'crumbs'  => $page['crumbs'],
    'eyebrow' => '// contact',
    'title'   => 'Tell us what you have. We\'ll design what you need.',
    'lead'    => 'Use the assessment below for new deployments, migrations and takeovers. Just a quick question? Use the short message form further down, or write to ' . config('contact_email') . '.',
]);
?>
<section class="section section--request section--tight" id="request" aria-label="Deployment request">
  <div class="container-xl">
    <?php partial('components/request-form'); ?>
  </div>
</section>

<section class="section section--tight" id="message" aria-labelledby="message-title">
  <div class="container-xl">
    <div class="msg-layout">
      <div class="msg-layout__intro">
        <p class="eyebrow">// quick message</p>
        <h2 class="section-title section-title--sm" id="message-title">Just a question?</h2>
        <p class="msg-layout__lead">Not ready for a full assessment? Ask about pricing, an existing Graylog setup, partnerships or anything else. A real engineer answers.</p>
        <ul class="msg-layout__facts">
          <li><?= icon('clock') ?> Reply usually within one business day</li>
          <li><?= icon('mail') ?> <a href="mailto:<?= e(config('contact_email')) ?>"><?= e(config('contact_email')) ?></a></li>
          <li><?= icon('lock') ?> No newsletter, no automated sales sequence</li>
        </ul>
      </div>
      <?php partial('components/message-form'); ?>
    </div>
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
