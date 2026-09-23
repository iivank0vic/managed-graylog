<?php
declare(strict_types=1);

$problems = [
    ['crit', 'topology.single_node', 'Single-server deployments', 'Graylog, search and MongoDB on one VM. When it goes down, so does your visibility.'],
    ['warn', 'disk.watermark', 'Disk growth', 'Indices grow faster than planned until watermarks block writes.'],
    ['warn', 'retention.default', 'Poor retention planning', 'Retention set by default, not by requirement — too short for audits, too long for disks.'],
    ['crit', 'input.not_running', 'Failed inputs', 'An input stops after a restart or certificate change. Nobody notices for days.'],
    ['warn', 'collector.silent', 'Broken collectors', 'Agents silently stop shipping after OS or configuration changes.'],
    ['crit', 'search.heap_pressure', 'Overloaded search nodes', 'Oversized shards and heap pressure. Searches time out exactly when you need them.'],
    ['warn', 'mongodb.unmaintained', 'MongoDB issues', 'No replica set, no authentication, several versions behind.'],
    ['crit', 'cluster.status_red', 'Cluster failures', 'A node drops, the cluster goes red, recovery becomes trial and error.'],
    ['warn', 'version.eol', 'Upgrades', 'Stuck on an end-of-life version because the upgrade path looks risky.'],
    ['warn', 'tls.cert_expiry', 'TLS certificates', 'An expired certificate breaks inputs and the web UI at the worst possible time.'],
    ['warn', 'backup.untested', 'Backups', 'Backups exist. Nobody has ever restored one.'],
    ['info', 'alerting.none', 'Alerting', 'Nothing alerts when the alerting platform itself is unhealthy.'],
    ['warn', 'journal.backlog', 'High ingestion volume', 'Traffic spikes fill the journal and processing falls behind.'],
    ['info', 'migration.pending', 'Infrastructure migrations', 'Moving a stateful platform to new infrastructure without losing logs.'],
];
?>
<section class="section section--problems" id="problems" aria-labelledby="problems-title">
  <div class="container-xl">
    <div class="section-head section-head--split">
      <div>
        <p class="eyebrow" data-reveal>// the problem</p>
        <h2 class="section-title" id="problems-title" data-reveal>Running Graylog shouldn't become another infrastructure problem.</h2>
      </div>
      <p class="section-lead" data-reveal>
        Graylog is straightforward to install and easy to outgrow. Most of the environments we see did not fail because of Graylog —
        they failed because nobody designed the infrastructure around it.
      </p>
    </div>

    <div class="alert-feed" data-reveal>
      <div class="alert-feed__head mono">
        <span>events / platform-health</span>
        <span class="alert-feed__legend"><i class="sev sev--crit"></i>crit <i class="sev sev--warn"></i>warn <i class="sev sev--info"></i>info</span>
      </div>
      <ul class="alert-feed__list">
        <?php foreach ($problems as [$sev, $code, $title, $text]): ?>
          <li class="alert-row alert-row--<?= e($sev) ?>">
            <span class="alert-row__sev mono"><?= e(strtoupper($sev)) ?></span>
            <span class="alert-row__code mono"><?= e($code) ?></span>
            <span class="alert-row__body"><strong><?= e($title) ?></strong> <?= e($text) ?></span>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>

    <div class="resolve" data-reveal>
      <span class="resolve__icon"><?= icon('circle-check') ?></span>
      <p class="resolve__text">We build the platform so you don't have to.</p>
      <a class="link-arrow" href="#services">See what we do <?= icon('arrow-right') ?></a>
    </div>
  </div>
</section>
