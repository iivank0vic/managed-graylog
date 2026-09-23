<?php
declare(strict_types=1);

$items = [
    ['rocket', 'Graylog Deployment', 'From a single production server to multi-node clusters.', '/services/graylog-deployment', 'feature'],
    ['network', 'Graylog Cluster Architecture', 'Resilient architectures for growing log volumes.', '/services/graylog-cluster', 'feature'],
    ['arrow-right-left', 'Migration', 'Move existing Graylog environments with minimal disruption.', '/services/graylog-migration', ''],
    ['gauge', 'Performance Tuning', 'Optimise ingestion, indexing, storage and search performance.', '/services/graylog-consulting', ''],
    ['wrench', 'Maintenance', 'Updates, health checks, configuration changes and troubleshooting.', '/services/graylog-maintenance', ''],
    ['lock', 'Security Hardening', 'TLS, access control, firewalling, secrets and secure architecture.', '/services/graylog-managed-services', ''],
    ['history', 'Backup & Disaster Recovery', 'Backup and recovery designed around your retention requirements.', '/services/graylog-managed-services', ''],
    ['cloud', 'Cloud Deployment', 'AWS, Google Cloud, Hetzner, Azure, OVHcloud and your own infrastructure.', '/infrastructure', ''],
    ['file-code', 'Infrastructure as Code', 'Terraform, Ansible and reproducible deployments.', '/services/graylog-deployment', ''],
    ['activity', 'Monitoring', 'Health of infrastructure, storage, ingestion and critical services.', '/services/graylog-managed-services', ''],
];
?>
<section class="section section--services" id="services" aria-labelledby="services-title">
  <div class="container-xl">
    <div class="section-head">
      <p class="eyebrow" data-reveal>// services</p>
      <h2 class="section-title" id="services-title" data-reveal>From a single server to a multi-node logging platform.</h2>
      <p class="section-lead" data-reveal>You choose the infrastructure. We engineer the platform — and keep it running if you want us to.</p>
    </div>

    <div class="svc-grid">
      <?php foreach ($items as $i => [$ic, $title, $text, $href, $mod]): ?>
        <a class="svc<?= $mod ? ' svc--' . e($mod) : '' ?>" href="<?= e($href) ?>" data-reveal>
          <span class="svc__idx mono"><?= sprintf('%02d', $i + 1) ?></span>
          <span class="svc__icon"><?= icon($ic) ?></span>
          <h3 class="svc__title"><?= e($title) ?></h3>
          <p class="svc__text"><?= e($text) ?></p>
          <span class="svc__go" aria-hidden="true"><?= icon('arrow-up-right') ?></span>
          <?php if ($mod === 'feature'): ?>
            <span class="svc__trace mono" aria-hidden="true"><?= $i === 0 ? '$ terraform apply && ansible-playbook site.yml' : 'nodes: 2 · search: 3 · rs0: 3' ?></span>
          <?php endif; ?>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>
