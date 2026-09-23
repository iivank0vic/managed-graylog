<?php
declare(strict_types=1);
$title = $title ?? 'You have logs. We build and operate the infrastructure that makes them useful.';
$text  = $text ?? 'Tell us about your environment. An engineer will reply with a first architecture outline.';
$href  = ($GLOBALS['CURRENT_PATH'] ?? '') === '/' ? '#request' : url('/contact') . '#request';
?>
<section class="cta-band" aria-label="Get started">
  <div class="container-xl">
    <div class="cta-band__inner" data-reveal>
      <div class="grid-bg grid-bg--soft" aria-hidden="true"></div>
      <p class="cta-band__prompt mono" aria-hidden="true">$ ./request-deployment-plan --env=production</p>
      <h2 class="cta-band__title"><?= e($title) ?></h2>
      <p class="cta-band__text"><?= e($text) ?></p>
      <div class="cta-band__actions">
        <a class="btn-x btn-x--primary btn-x--lg" href="<?= e($href) ?>">Design My Deployment <?= icon('arrow-right') ?></a>
        <a class="btn-x btn-x--ghost btn-x--lg" href="mailto:<?= e(config('contact_email')) ?>"><?= icon('mail') ?> <span class="d-none d-sm-inline"><?= e(config('contact_email')) ?></span><span class="d-sm-none">Email us</span></a>
      </div>
    </div>
  </div>
</section>
