<?php
/**
 * Site footer. Expects $page.
 */
declare(strict_types=1);

$legalName = config('company.legal_name');
$address   = config('company.address');
$vat       = config('company.vat_id');
?>
</main>

<footer class="site-footer">
  <div class="container-xl">
    <div class="site-footer__top">
      <div class="site-footer__brand">
        <a class="brand" href="<?= e(url('/')) ?>" aria-label="Managed Graylog home">
          <?= brand_mark() ?>
          <span class="brand__word">Managed <span class="brand__accent">Graylog</span></span>
        </a>
        <p class="site-footer__tagline">Graylog infrastructure, designed, deployed and operated on the servers and clouds you choose.</p>
        <a class="site-footer__mail mono" href="mailto:<?= e(config('contact_email')) ?>"><?= e(config('contact_email')) ?></a>
      </div>

      <nav class="site-footer__cols" aria-label="Footer">
        <div>
          <p class="site-footer__h">Services</p>
          <ul>
            <?php foreach (data('services') as $slug => $s): ?>
              <li><a href="<?= e(url('/services/' . $slug)) ?>"><?= e($s['name']) ?></a></li>
            <?php endforeach; ?>
          </ul>
        </div>
        <div>
          <p class="site-footer__h">Infrastructure</p>
          <ul>
            <?php foreach (data('infrastructure') as $slug => $p): if (empty($p['page'])) continue; ?>
              <li><a href="<?= e(url('/infrastructure/' . $slug)) ?>">Graylog on <?= e($p['name']) ?></a></li>
            <?php endforeach; ?>
            <li><a href="<?= e(url('/infrastructure')) ?>">All environments</a></li>
          </ul>
        </div>
        <div>
          <p class="site-footer__h">Company</p>
          <ul>
            <li><a href="<?= e(url('/solutions')) ?>">Solutions</a></li>
            <li><a href="<?= e(url('/pricing')) ?>">Pricing</a></li>
            <li><a href="<?= e(url('/faq')) ?>">FAQ</a></li>
            <li><a href="<?= e(url('/blog')) ?>">Blog</a></li>
            <li><a href="<?= e(url('/contact')) ?>">Contact</a></li>
          </ul>
        </div>
        <div>
          <p class="site-footer__h">Legal</p>
          <ul>
            <li><a href="<?= e(url('/licensing')) ?>">Licensing</a></li>
            <li><a href="<?= e(url('/privacy')) ?>">Privacy</a></li>
            <li><a href="<?= e(url('/terms')) ?>">Terms</a></li>
          </ul>
        </div>
      </nav>
    </div>

    <div class="site-footer__legal">
      <p>
        <strong>managed-graylog.com</strong> is an independent service provider and is not affiliated with, endorsed by or sponsored by Graylog, Inc.
        Graylog is a trademark of its respective owner. All other product names, logos and brands mentioned are property of their respective owners and are used for identification only.
        Software licensing is subject to the applicable vendor license.
      </p>
      <p class="site-footer__meta mono">
        <span>&copy; <?= date('Y') ?> <?= e($legalName ?: 'managed-graylog.com') ?></span>
        <?php if ($address): ?><span><?= e($address) ?></span><?php endif; ?>
        <?php if ($vat): ?><span>VAT <?= e($vat) ?></span><?php endif; ?>
      </p>
    </div>
  </div>
</footer>
</body>
</html>
