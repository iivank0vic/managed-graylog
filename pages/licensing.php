<?php declare(strict_types=1);

partial('components/page-hero', [
    'crumbs'  => $page['crumbs'],
    'eyebrow' => '// software & licensing',
    'title'   => 'Software & licensing',
    'lead'    => 'We provide engineering and operations. The software we deploy is licensed by its vendors. This page explains how that fits together — it is general information, not legal advice.',
]);

$editions = [
    ['Graylog Open', 'Free', 'Source-available under the Server Side Public License (SSPL), per the vendor. Core log collection, processing, search, dashboards and alerting.'],
    ['Graylog Enterprise', 'Commercial', 'Paid edition from Graylog, Inc. with additional features for larger organisations. Licensed according to the vendor\'s terms and pricing model.'],
    ['Graylog Security', 'Commercial', 'Paid edition from Graylog, Inc. focused on security analytics and SIEM use cases. Licensed according to the vendor\'s terms.'],
];
?>
<section class="section section--tight">
  <div class="container-xl">
    <div class="prose prose--wide">
      <p class="meta mono">Last reviewed: September 2026 · Vendor terms change — always check the current license texts.</p>

      <h2>What we sell — and what we don't</h2>
      <p>
        Our fees cover engineering work: design, deployment, migration, maintenance and operation of infrastructure. Software licenses are separate.
        When you need commercial software, you obtain the appropriate license from the vendor (or its authorised channels), and the license is between you and the vendor.
      </p>
      <p>
        managed-graylog.com is an independent provider. We are not affiliated with, endorsed by or a partner of Graylog, Inc., and this website is not an official Graylog offering.
      </p>

      <h2>Graylog editions</h2>
      <p>Software licensing depends on the Graylog edition and the deployment model you choose. At the time of review, the vendor offers:</p>
    </div>

    <div class="table-wrap">
      <table class="table-x">
        <caption class="visually-hidden">Graylog editions</caption>
        <thead><tr><th scope="col">Edition</th><th scope="col">Type</th><th scope="col">Notes</th></tr></thead>
        <tbody>
          <?php foreach ($editions as [$n, $t, $d]): ?>
            <tr><th scope="row"><?= e($n) ?></th><td><span class="pill mono"><?= e($t) ?></span></td><td><?= e($d) ?></td></tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <div class="prose prose--wide">
      <p>For current features, editions and pricing, see the vendor's own pages, for example <a href="https://graylog.org/pricing/" rel="noopener" target="_blank">graylog.org/pricing</a>.</p>

      <h2>Using Graylog Open is not the same as "no licensing questions"</h2>
      <p>
        Graylog Open is free to use, but the SSPL is not a permissive license. It includes conditions — notably around making the software available to third parties as a service —
        whose application depends on how the software is used and by whom. The same license family also applies to MongoDB Community Server, which Graylog requires.
      </p>
      <p>
        Our standard model is to deploy <strong>dedicated environments for individual customers</strong>, preferably in the customer's own cloud account or on the customer's servers.
        We do not operate a shared, multi-tenant Graylog service. If your plans involve offering Graylog-based functionality to your own customers, review the relevant license
        terms with your legal counsel and, where appropriate, with the vendor.
      </p>

      <h2>Commercial features</h2>
      <p>
        Need Graylog Enterprise or Security features? We can help you evaluate licensing and deployment options from a technical point of view: which of your requirements
        are covered by Graylog Open, which depend on a commercial edition, and how each option affects the architecture. Customers who require commercial Graylog functionality
        obtain the appropriate license from the vendor; we then deploy and operate it.
      </p>

      <h2>Search backend: Data Node, OpenSearch and Elasticsearch</h2>
      <ul>
        <li><strong>Graylog Data Node</strong> is Graylog's component for running the OpenSearch-based search layer. It is distributed by Graylog; its terms follow the license it ships under.</li>
        <li><strong>OpenSearch</strong> is licensed under the Apache License 2.0 and is developed under the OpenSearch Software Foundation, a Linux Foundation project.</li>
        <li><strong>Elasticsearch</strong> source code is currently offered by Elastic under a choice of the Elastic License 2.0, the SSPL, or the AGPLv3; the default distribution is under the Elastic License 2.0. Graylog 7.0 deprecated Elasticsearch as a search backend, with removal planned for Graylog 8.0. We do not use Elasticsearch for new Graylog deployments.</li>
      </ul>

      <h2>Other components</h2>
      <p>
        Operating systems, collectors (such as Fluent Bit, the OpenTelemetry Collector or Beats), automation tools and cloud services each come with their own licenses and terms of service.
        We document which components an environment uses so that you can review them.
      </p>

      <h2>Trademarks</h2>
      <p>
        Graylog is a trademark of its respective owner. OpenSearch, Elasticsearch, MongoDB, and the names of cloud providers are trademarks of their respective owners.
        They are used on this site only to describe the technologies we work with. We do not use vendor logos, and nothing here implies endorsement.
      </p>

      <h2>Not legal advice</h2>
      <p>
        This page summarises publicly available information to help you ask the right questions. It is not legal advice. For decisions that depend on license interpretation,
        consult qualified legal counsel and the license texts themselves.
      </p>
    </div>
  </div>
</section>

<?php partial('components/cta-band', ['title' => 'Evaluating Graylog editions?', 'text' => 'We can map your technical requirements against Graylog Open and the commercial editions, and show what each option means for your architecture.']); ?>
