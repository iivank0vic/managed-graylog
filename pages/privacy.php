<?php declare(strict_types=1);

$controller = config('company.legal_name') ?: '[PLACEHOLDER: legal entity name and address]';
partial('components/page-hero', [
    'crumbs'  => $page['crumbs'],
    'eyebrow' => '// legal',
    'title'   => 'Privacy policy',
    'lead'    => 'How personal data submitted through this website is processed.',
]);
?>
<section class="section section--tight">
  <div class="container-xl">
    <div class="prose prose--wide">
      <div class="notice mono">DRAFT TEMPLATE — must be reviewed and completed by the site operator (and legal counsel where appropriate) before launch.</div>

      <h2>Controller</h2>
      <p><?= e($controller) ?><?php if ($a = config('company.address')): ?>, <?= e($a) ?><?php endif; ?>. Contact: <a href="mailto:<?= e(config('contact_email')) ?>"><?= e(config('contact_email')) ?></a>.</p>

      <h2>What we collect</h2>
      <p>When you submit the deployment request form, we process the details you enter: name, company, email address and the technical information you choose to provide about your environment. We ask you not to submit passwords, keys or other credentials.</p>
      <p>Our web server processes technical data required to deliver the site, such as IP address, requested URL, time and user agent, in server logs. The contact form uses a session cookie for security (CSRF protection). We do not use analytics or advertising cookies. <em>[Update this section if analytics are added.]</em></p>

      <h2>Purpose and legal basis</h2>
      <p>Form data is used to answer your request and to prepare a proposal (steps prior to entering into a contract, Art. 6(1)(b) GDPR) and, based on your consent, as indicated on the form. Server logs and rate-limiting data are processed on the basis of our legitimate interest in operating a secure website (Art. 6(1)(f) GDPR).</p>

      <h2>Retention</h2>
      <p>[PLACEHOLDER: define retention periods, e.g. requests that do not lead to a contract are deleted after N months; rate-limit data is kept for at most one hour in hashed form.]</p>

      <h2>Recipients</h2>
      <p>[PLACEHOLDER: hosting provider, email provider and any other processors, with their locations.]</p>

      <h2>Your rights</h2>
      <p>You have the right to access, rectification, erasure, restriction, data portability and objection, and to withdraw consent at any time. You may lodge a complaint with a supervisory authority.</p>
    </div>
  </div>
</section>
