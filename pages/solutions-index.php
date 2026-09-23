<?php declare(strict_types=1);

partial('components/page-hero', [
    'crumbs'  => $page['crumbs'],
    'eyebrow' => '// solutions',
    'title'   => 'Centralized logging for the way you actually run infrastructure',
    'lead'    => 'Logs are only useful when they arrive reliably, carry the right context and stay searchable for as long as you need them. Here is how that looks for different kinds of teams.',
    'ctas'    => true,
]);

$protocols = [
    ['Syslog', 'RFC 3164 / 5424 over UDP, TCP or TLS — network devices, Linux hosts, appliances.'],
    ['GELF', 'Graylog Extended Log Format over UDP, TCP or HTTP — structured messages from apps and Docker.'],
    ['Beats', 'Filebeat and Winlogbeat shipping files and Windows event logs.'],
    ['OpenTelemetry', 'OTLP logs from OpenTelemetry SDKs and Collectors via Graylog\'s OpenTelemetry input in recent versions.'],
];
?>
<section class="section section--tight">
  <div class="container-xl">
    <?php partial('components/usecase-grid'); ?>
    <p class="fineprint">We describe technical capabilities only. We do not claim certifications or compliance attestations; compliance depends on your organisation and controls.</p>
  </div>
</section>

<section class="section section--tight">
  <div class="container-xl">
    <div class="section-head">
      <p class="eyebrow">// ingestion</p>
      <h2 class="section-title section-title--sm">Protocols and integrations we design around</h2>
    </div>
    <div class="scope-grid scope-grid--4">
      <?php foreach ($protocols as [$t, $d]): ?>
        <div class="scope" data-reveal>
          <span class="scope__idx mono"><?= e(strtolower($t)) ?></span>
          <h3 class="scope__title"><?= e($t) ?></h3>
          <p class="scope__text"><?= e($d) ?></p>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section section--tight">
  <div class="container-xl">
    <a class="feature-link" href="<?= e(url('/solutions/kubernetes-logging')) ?>">
      <span class="svc__icon"><?= icon('boxes') ?></span>
      <span><small class="mono">solution guide</small><strong>Kubernetes logging with Graylog</strong>DaemonSet collectors, metadata enrichment, multiline handling and where Graylog itself should run.</span>
      <?= icon('arrow-right') ?>
    </a>
  </div>
</section>

<?php partial('components/cta-band'); ?>
