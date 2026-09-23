<?php declare(strict_types=1);

partial('components/page-hero', [
    'crumbs'  => $page['crumbs'],
    'eyebrow' => '// legal',
    'title'   => 'Terms of use',
    'lead'    => 'Terms for using the managed-graylog.com website. Service engagements are governed by separate written agreements.',
]);
?>
<section class="section section--tight">
  <div class="container-xl">
    <div class="prose prose--wide">
      <div class="notice mono">DRAFT TEMPLATE — must be reviewed and completed by the site operator (and legal counsel where appropriate) before launch.</div>

      <h2>Information on this website</h2>
      <p>Content on this website is general technical information. It is not a binding offer, and it is not legal advice. Technical details about third-party software and cloud services reflect our understanding at the time of writing and may change.</p>

      <h2>Services</h2>
      <p>Services are provided only under a separate written agreement that defines scope, fees, responsibilities and liability.</p>

      <h2>Independence and trademarks</h2>
      <p>managed-graylog.com is an independent service provider and is not affiliated with, endorsed by or sponsored by Graylog, Inc. Graylog and other product and company names are trademarks of their respective owners and are used for identification only.</p>

      <h2>Third-party software</h2>
      <p>Software deployed as part of our services is subject to the respective vendor licenses. Customers are responsible for holding any required licenses. See <a href="<?= e(url('/licensing')) ?>">Software &amp; licensing</a>.</p>

      <h2>Liability</h2>
      <p>[PLACEHOLDER: liability limitations appropriate to your jurisdiction.]</p>

      <h2>Governing law</h2>
      <p>[PLACEHOLDER: governing law and jurisdiction.]</p>
    </div>
  </div>
</section>
