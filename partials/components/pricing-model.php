<?php
declare(strict_types=1);

$factors = [
    ['server', 'Infrastructure size', 'Number and size of hosts across all tiers.'],
    ['activity', 'Log volume', 'Daily ingest and peak rates drive compute and disk.'],
    ['history', 'Retention', 'How long data is kept and searchable.'],
    ['network', 'Number of nodes', 'Graylog, search and MongoDB members.'],
    ['cloud', 'Cloud provider', 'Provider-specific networking, storage and automation.'],
    ['shield-check', 'HA requirements', 'Redundancy, failure domains and failover testing.'],
    ['wrench', 'Maintenance scope', 'Upgrades, patching and change volume.'],
    ['radar', 'Monitoring', 'Depth of platform monitoring and alert routing.'],
    ['archive', 'Backup', 'Backup scope, frequency and restore testing.'],
    ['life-buoy', 'Support level', 'Coverage hours and response expectations.'],
];

$models = [
    ['Deployment', 'Fixed-scope project', 'Design and build of a new environment, delivered as code with documentation.'],
    ['Migration', 'Fixed-scope project', 'Planned move to new infrastructure, versions or search backend, with cut-over and rollback.'],
    ['Managed Infrastructure', 'Monthly', 'We operate the platform: monitoring, patching, upgrades, capacity and backups.'],
    ['Maintenance', 'Monthly or hours-based', 'Health checks, upgrades and troubleshooting for environments you run yourselves.'],
    ['Consulting', 'Time-boxed', 'Architecture reviews, sizing and edition evaluation with a written outcome.'],
];
?>
<div class="pricing">
  <div class="pricing__factors" data-reveal>
    <h3 class="pricing__h">What pricing depends on</h3>
    <ul class="factor-list">
      <?php foreach ($factors as [$ic, $t, $d]): ?>
        <li><?= icon($ic) ?><span><strong><?= e($t) ?></strong><?= e($d) ?></span></li>
      <?php endforeach; ?>
    </ul>
  </div>
  <div class="pricing__models">
    <?php foreach ($models as [$t, $billing, $d]): ?>
      <div class="model" data-reveal>
        <div class="model__head">
          <h3 class="model__title"><?= e($t) ?></h3>
          <span class="model__billing mono"><?= e($billing) ?></span>
        </div>
        <p class="model__text"><?= e($d) ?></p>
      </div>
    <?php endforeach; ?>
    <p class="fineprint" data-reveal>Infrastructure costs are billed by your provider — ideally directly to your account. Commercial software licenses, if needed, are purchased from the vendor and are separate from our services.</p>
  </div>
</div>
