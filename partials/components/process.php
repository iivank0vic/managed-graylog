<?php
declare(strict_types=1);

$steps = [
    ['Tell us what you have', 'Current setup, log sources, volumes, pain points. Rough numbers are fine.', 'intake'],
    ['We review your workload', 'Ingest rate, message size, retention, search patterns and availability needs.', 'assess'],
    ['We design the architecture', 'Topology, sizing, provider, security model — written down with the reasoning.', 'design'],
    ['We deploy the infrastructure', 'Provisioned as code with Terraform and Ansible, reviewed with your team.', 'provision'],
    ['We configure Graylog', 'Inputs, collectors, streams, pipelines, index sets, dashboards and alerts.', 'configure'],
    ['We secure and monitor it', 'TLS, access control, firewalls, platform monitoring and backup verification.', 'harden'],
    ['We maintain it', 'Patching, upgrades, capacity planning and troubleshooting — or a clean handover.', 'operate'],
];
?>
<ol class="process">
  <?php foreach ($steps as $i => [$title, $text, $cmd]): ?>
    <li class="process__step" data-reveal>
      <div class="process__node" aria-hidden="true"><span><?= $i + 1 ?></span></div>
      <p class="process__label mono">step <?= sprintf('%02d', $i + 1) ?> · <?= e($cmd) ?></p>
      <h3 class="process__title"><?= e($title) ?></h3>
      <p class="process__text"><?= e($text) ?></p>
    </li>
  <?php endforeach; ?>
</ol>
