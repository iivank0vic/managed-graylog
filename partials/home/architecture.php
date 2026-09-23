<?php
declare(strict_types=1);

// [id, label, sub, x, y, w, h, panel]
$nodes = [
    ['apps', 'APPLICATIONS', 'your workloads', 200, 20, 200, 44, 'apps'],
    ['linux', 'Linux', 'journald · syslog', 40, 110, 150, 44, 'linux'],
    ['docker', 'Docker', 'gelf driver', 225, 110, 150, 44, 'docker'],
    ['k8s', 'Kubernetes', 'daemonsets', 410, 110, 150, 44, 'k8s'],
    ['collect', 'LOG COLLECTION', 'agents · buffering · retries', 150, 200, 300, 44, 'collect'],
    ['lb', 'LOAD BALANCER / INPUTS', 'tcp · udp · tls', 150, 290, 300, 44, 'lb'],
    ['gl1', 'NODE 01', 'graylog', 120, 416, 160, 52, 'cluster'],
    ['gl2', 'NODE 02', 'graylog', 320, 416, 160, 52, 'cluster'],
    ['data', 'DATA LAYER', 'data node · opensearch', 40, 536, 260, 44, 'data'],
    ['mongo', 'MONGODB', 'metadata · rs0', 340, 536, 220, 44, 'mongo'],
    ['storage', 'STORAGE / BACKUPS', 'snapshots · object storage', 150, 626, 300, 44, 'storage'],
];

// [from, to, path]
$edges = [
    ['apps', 'linux', 'M300 64 C300 90 115 84 115 110'],
    ['apps', 'docker', 'M300 64 V110'],
    ['apps', 'k8s', 'M300 64 C300 90 485 84 485 110'],
    ['linux', 'collect', 'M115 154 C115 180 260 174 260 200'],
    ['docker', 'collect', 'M300 154 V200'],
    ['k8s', 'collect', 'M485 154 C485 180 340 174 340 200'],
    ['collect', 'lb', 'M300 244 V290'],
    ['lb', 'gl1', 'M300 334 C300 372 200 380 200 416'],
    ['lb', 'gl2', 'M300 334 C300 372 400 380 400 416'],
    ['gl1', 'data', 'M200 468 C200 505 170 505 170 536'],
    ['gl2', 'data', 'M400 468 C400 508 250 500 250 536'],
    ['gl1', 'mongo', 'M200 468 C200 508 400 500 400 536'],
    ['gl2', 'mongo', 'M400 468 C400 505 450 505 450 536'],
    ['data', 'storage', 'M170 580 C170 606 260 600 260 626'],
    ['mongo', 'storage', 'M450 580 C450 606 340 600 340 626'],
];

$panels = [
    'apps' => ['Applications', 'Where logs originate', [
        'Application logs — structured JSON wherever the code allows it',
        'Web server access and error logs',
        'Audit, authentication and security events',
    ], ['json', 'stdout', 'files', 'events']],
    'linux' => ['Linux hosts', 'System and service logs', [
        'journald and rsyslog / syslog-ng forwarding over TCP or TLS',
        'auditd and authentication logs for security use cases',
        'Collectors managed centrally so configuration does not drift',
    ], ['journald', 'rsyslog', 'auditd', 'filebeat']],
    'docker' => ['Docker hosts', 'Container logs without losing context', [
        'GELF logging driver for direct shipping, or a collector reading container log files',
        'Container name, image and labels preserved as fields',
        'Buffering so a Graylog restart does not drop container output',
    ], ['gelf', 'labels', 'compose']],
    'k8s' => ['Kubernetes', 'Cluster-wide collection', [
        'DaemonSet collectors: Fluent Bit, OpenTelemetry Collector or Filebeat',
        'Namespace, pod, container and label metadata on every message',
        'Multiline stack traces joined at the source',
    ], ['daemonset', 'otel', 'fluent bit']],
    'collect' => ['Log collection', 'The part that decides whether logs arrive', [
        'Protocol choice: syslog over TCP/TLS, GELF, Beats or OTLP — UDP only where loss is acceptable',
        'Disk or memory buffers and retries on every agent',
        'Parsing done once, in the right place',
    ], ['syslog', 'gelf', 'beats', 'otlp']],
    'lb' => ['Load balancer & inputs', 'One entry point, many nodes', [
        'TCP and UDP load balancing appropriate to your provider',
        'Health checks against Graylog\'s load-balancer status endpoint',
        'TLS termination or passthrough, one input per protocol and port',
    ], ['tcp/udp', 'tls', 'health checks']],
    'cluster' => ['Graylog cluster', 'Processing, routing and search API', [
        'Processing pipelines parse, enrich and normalise messages',
        'Streams route messages to index sets with their own retention',
        'On-disk journal buffers messages if the search layer slows down',
        'Nodes scale horizontally behind the load balancer',
    ], ['pipelines', 'streams', 'journal', 'alerts']],
    'data' => ['Data layer', 'Where your logs are indexed and searched', [
        'Graylog Data Node or self-managed OpenSearch in supported versions',
        'Shard size, replica count and heap tuned to your data',
        'Index rotation and retention per index set, disk watermarks respected',
    ], ['data node', 'opensearch', 'replicas']],
    'mongo' => ['MongoDB', 'Configuration and metadata', [
        'Stores users, inputs, streams, dashboards and alert definitions — not log messages',
        'Three-member replica set with authentication for HA',
        'Version kept within what your Graylog release requires',
    ], ['replica set', 'auth', 'tls']],
    'storage' => ['Storage & backups', 'Recoverable by design', [
        'Search snapshots to object storage where the backend supports it',
        'MongoDB backups and configuration kept in version control',
        'Restores tested on a schedule, not assumed',
    ], ['snapshots', 's3-compatible', 'restore drills']],
];
?>
<section class="section section--arch" id="architecture" aria-labelledby="arch-title">
  <div class="container-xl">
    <div class="section-head">
      <p class="eyebrow" data-reveal>// reference architecture</p>
      <h2 class="section-title" id="arch-title" data-reveal>Every layer, accounted for.</h2>
      <p class="section-lead" data-reveal>A typical highly available Graylog platform. Hover or select a component to see what we design and configure at that layer.</p>
    </div>

    <div class="arch" data-arch>
      <div class="arch__diagram" data-reveal>
        <svg class="arch__svg" viewBox="0 0 600 690" role="group" aria-label="Interactive architecture diagram">
          <defs>
            <radialGradient id="ad-pkt">
              <stop offset="0" stop-color="#e0fbff"/>
              <stop offset=".45" stop-color="#38e1ff"/>
              <stop offset="1" stop-color="#38e1ff" stop-opacity="0"/>
            </radialGradient>
          </defs>

          <rect class="ad-frame" x="90" y="380" width="420" height="110" rx="12"/>
          <text class="ad-frame__label" x="92" y="372">GRAYLOG CLUSTER</text>

          <g class="ad-edges" fill="none">
            <?php foreach ($edges as $i => [$a, $b, $d]): ?>
              <path id="ad-e<?= $i ?>" class="ad-edge" data-a="<?= e($a) ?>" data-b="<?= e($b) ?>" d="<?= e($d) ?>"/>
            <?php endforeach; ?>
          </g>

          <g class="ad-pkts" aria-hidden="true">
            <?php foreach ([0, 4, 6, 7, 8, 9, 12, 13] as $k => $i): ?>
              <circle r="3.5" fill="url(#ad-pkt)">
                <animateMotion dur="<?= 1.4 + ($k % 3) * 0.3 ?>s" begin="-<?= $k * 0.35 ?>s" repeatCount="indefinite"><mpath href="#ad-e<?= $i ?>"/></animateMotion>
              </circle>
            <?php endforeach; ?>
          </g>

          <?php foreach ($nodes as [$id, $label, $sub, $x, $y, $w, $h, $panel]): ?>
            <g class="ad-node" data-node="<?= e($id) ?>" data-panel-target="<?= e($panel) ?>" tabindex="0" role="button"
               aria-controls="arch-inspector" aria-label="<?= e(ucwords(strtolower($label))) ?> — show details">
              <rect x="<?= $x ?>" y="<?= $y ?>" width="<?= $w ?>" height="<?= $h ?>" rx="8"/>
              <text class="ad-node__label" x="<?= $x + $w / 2 ?>" y="<?= $y + ($h > 44 ? 23 : 19) ?>" text-anchor="middle"><?= e($label) ?></text>
              <text class="ad-node__sub" x="<?= $x + $w / 2 ?>" y="<?= $y + ($h > 44 ? 40 : 34) ?>" text-anchor="middle"><?= e($sub) ?></text>
            </g>
          <?php endforeach; ?>
        </svg>
      </div>

      <aside class="arch__inspector" id="arch-inspector" aria-live="polite" data-reveal>
        <div class="inspector__chrome mono"><span>inspect</span><span data-inspector-id>cluster</span></div>
        <?php foreach ($panels as $key => [$title, $role, $points, $tags]): ?>
          <div class="inspector__panel" data-panel="<?= e($key) ?>"<?= $key === 'cluster' ? '' : ' hidden' ?>>
            <p class="inspector__role mono"><?= e($role) ?></p>
            <h3 class="inspector__title"><?= e($title) ?></h3>
            <ul class="inspector__points">
              <?php foreach ($points as $pt): ?><li><?= icon('check') ?><span><?= e($pt) ?></span></li><?php endforeach; ?>
            </ul>
            <ul class="inspector__tags mono">
              <?php foreach ($tags as $t): ?><li><?= e($t) ?></li><?php endforeach; ?>
            </ul>
          </div>
        <?php endforeach; ?>
        <p class="inspector__hint mono">tip: use Tab / Enter to move through components</p>
      </aside>
    </div>
  </div>
</section>
