<?php declare(strict_types=1);

partial('components/page-hero', [
    'crumbs'  => $page['crumbs'],
    'eyebrow' => '// infrastructure',
    'title'   => 'Run Graylog where it makes sense',
    'lead'    => 'Cloud, dedicated, hybrid or your own data center. We deploy into your existing infrastructure or provision a new environment around your requirements.',
    'ctas'    => true,
]);

$criteria = [
    ['Cost per stored TB', 'Logging is storage-heavy. Dedicated servers with local disks can change the economics significantly compared to network block storage.'],
    ['Proximity to sources', 'Shipping logs across regions or providers adds latency and, on most clouds, data transfer charges.'],
    ['Data location', 'Where logs are stored can matter for your data protection obligations. We make the location an explicit design choice.'],
    ['Operational fit', 'The best provider is often the one your team already operates, with IAM, networking and billing in place.'],
    ['Scaling pattern', 'Cloud instances scale in minutes; dedicated hardware scales in days. Growth rate influences which fits.'],
    ['Network features', 'Load balancer protocol support (UDP!), private networking and firewalling differ between providers.'],
];
?>
<section class="section section--tight">
  <div class="container-xl">
    <?php partial('components/provider-grid'); ?>
    <p class="fineprint">Provider names are used to describe where we can deploy. We are not an official partner of any provider listed.</p>
  </div>
</section>

<section class="section section--tight">
  <div class="container-xl">
    <div class="section-head">
      <p class="eyebrow">// choosing</p>
      <h2 class="section-title section-title--sm">How we help choose the environment</h2>
    </div>
    <div class="scope-grid">
      <?php foreach ($criteria as $i => [$t, $d]): ?>
        <div class="scope" data-reveal>
          <span class="scope__idx mono"><?= sprintf('%02d', $i + 1) ?></span>
          <h3 class="scope__title"><?= e($t) ?></h3>
          <p class="scope__text"><?= e($d) ?></p>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section section--tight">
  <div class="container-xl">
    <div class="split">
      <div class="prose">
        <p class="eyebrow">// deployment models</p>
        <h2 class="section-title section-title--sm">Cloud, bare metal or hybrid</h2>
        <p><strong>Cloud</strong> deployments suit teams that need to scale quickly or keep logs close to cloud workloads. Everything is provisioned with Terraform in your account.</p>
        <p><strong>Bare-metal</strong> deployments on dedicated servers suit high, steady volumes where local NVMe and predictable cost per TB matter most.</p>
        <p><strong>Hybrid</strong> designs combine both — for example Graylog nodes in the cloud and the search tier on dedicated hardware, connected over private networking.</p>
        <p><strong>Customer-owned</strong> infrastructure works too, including restricted networks, as long as we have a sanctioned way to access and operate it.</p>
      </div>
      <div class="spec">
        <div class="spec__chrome mono"><span class="cp__dots" aria-hidden="true"><i></i><i></i><i></i></span> placement.tf</div>
        <pre class="spec__body mono"><code><span class="c"># illustrative example — not a real configuration</span>
<span class="k">module</span> <span class="v">"graylog"</span> {
  <span class="k">graylog_nodes</span>  = <span class="v">2</span>
  <span class="k">search_nodes</span>   = <span class="v">3</span>
  <span class="k">mongodb_rs</span>     = <span class="v">3</span>
  <span class="k">placement</span>      = <span class="v">"hybrid"</span>
  <span class="k">search_tier</span>    = <span class="v">"dedicated-nvme"</span>
  <span class="k">private_net</span>    = <span class="v">true</span>
}</code></pre>
      </div>
    </div>
  </div>
</section>

<?php partial('components/cta-band'); ?>
