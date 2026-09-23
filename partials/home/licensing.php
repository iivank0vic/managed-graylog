<?php declare(strict_types=1); ?>
<section class="section section--licensing" id="licensing" aria-labelledby="lic-title">
  <div class="container-xl">
    <div class="lic" data-reveal>
      <div class="lic__icon"><?= icon('scale') ?></div>
      <div class="lic__body">
        <p class="eyebrow">// software &amp; licensing</p>
        <h2 class="lic__title" id="lic-title">We provide engineering. Software licensing follows the vendor.</h2>
        <p>
          Software licensing depends on the Graylog edition and deployment model you choose. Graylog Open is free and source-available under the SSPL;
          Graylog Enterprise and Graylog Security are commercial editions licensed by Graylog, Inc. If you need commercial Graylog functionality,
          you obtain the appropriate license from the vendor and we deploy and operate it for you.
        </p>
        <p>
          Need Graylog Enterprise or Security features? We can help you evaluate licensing and deployment options from a technical point of view.
          This is not legal advice — license terms are defined by each vendor.
        </p>
      </div>
      <a class="link-arrow lic__link" href="<?= e(url('/licensing')) ?>">Read our licensing notes <?= icon('arrow-right') ?></a>
    </div>
  </div>
</section>
