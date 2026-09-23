<?php
declare(strict_types=1);

$layers = [
    ['Infrastructure', 'Compute, network and disks sized from ingest rate and retention — not from a default VM template.', ['vCPU / RAM', 'NVMe / IOPS', 'private networks']],
    ['Operating System', 'Hardened Linux, kernel and filesystem settings for search workloads, time sync, log rotation.', ['Debian / Ubuntu / RHEL', 'sysctl', 'chrony']],
    ['Graylog', 'Nodes, inputs, streams, pipelines, index sets and alerting — configured for how your team investigates.', ['inputs', 'pipelines', 'index sets']],
    ['Data / Search', 'Graylog Data Node or OpenSearch with shard, replica and heap strategy matched to your data.', ['data node', 'opensearch', 'shards']],
    ['Database', 'MongoDB replica sets with authentication, TLS and a supported version path.', ['mongodb', 'rs0', 'auth']],
    ['Storage', 'Hot data on fast disks, planned headroom below watermarks, growth modelled ahead of time.', ['watermarks', 'capacity model']],
    ['Security', 'TLS on every hop that supports it, firewalls per tier, least-privilege roles, secrets outside repos.', ['tls', 'firewall', 'rbac']],
    ['Monitoring', 'The platform watched as closely as the systems it collects from.', ['journal', 'throughput', 'cert expiry']],
    ['Backup', 'MongoDB, configuration and search snapshots — with restores actually tested.', ['snapshots', 'restore drills']],
    ['Automation', 'Terraform and Ansible so every environment can be rebuilt, reviewed and changed safely.', ['terraform', 'ansible', 'git']],
];
?>
<section class="section section--layers" id="engineering" aria-labelledby="layers-title">
  <div class="container-xl">
    <div class="layers">
      <div class="layers__intro">
        <p class="eyebrow" data-reveal>// engineering, not installation</p>
        <h2 class="section-title" id="layers-title" data-reveal>We don't just install Graylog.</h2>
        <p class="layers__sub grad-text" data-reveal>We design the infrastructure around it.</p>
        <p class="section-lead" data-reveal>
          A reliable logging platform is ten layers deep. Graylog is one of them.
          Each layer has its own failure modes, and each one is part of what we deliver and maintain.
        </p>
        <div class="stack-meter" aria-hidden="true" data-stack-meter>
          <?php foreach ($layers as $i => $l): ?><span></span><?php endforeach; ?>
        </div>
      </div>

      <ol class="layers__list">
        <?php foreach ($layers as $i => [$name, $text, $tags]): ?>
          <li class="layer" data-layer-row data-reveal>
            <span class="layer__num mono"><?= sprintf('%02d', $i + 1) ?></span>
            <div class="layer__main">
              <h3 class="layer__name"><?= e($name) ?></h3>
              <p class="layer__text"><?= e($text) ?></p>
            </div>
            <ul class="layer__tags mono" aria-label="Examples">
              <?php foreach ($tags as $t): ?><li><?= e($t) ?></li><?php endforeach; ?>
            </ul>
          </li>
        <?php endforeach; ?>
      </ol>
    </div>
  </div>
</section>
