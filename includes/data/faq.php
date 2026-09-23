<?php
/**
 * FAQ content. Rendered on /faq (grouped, with FAQPage schema) and partially on the homepage.
 * Keep answers factual. Licensing answers must not read as legal advice.
 * 'home' => true marks entries shown on the homepage.
 */
declare(strict_types=1);

return [
    'General' => [
        ['What is Graylog?', 'Graylog is a centralized log management platform developed by Graylog, Inc. It receives logs from servers, applications, containers and network devices, processes them into structured fields, stores them in a search backend and lets you search, dashboard and alert on them. A Graylog deployment consists of the Graylog server, MongoDB for configuration and metadata, and a search backend — Graylog Data Node or OpenSearch in current versions.', true],
        ['What is Graylog Open?', 'Graylog Open is the free edition of Graylog. According to the vendor, it is source-available under the Server Side Public License (SSPL) and has no ingest volume cap. It covers core log collection, processing, search, dashboards and alerting. Commercial editions — currently Graylog Enterprise and Graylog Security — add further functionality under a paid license.', true],
        ['Are you affiliated with Graylog, Inc.?', 'No. managed-graylog.com is an independent infrastructure and consulting provider. We are not an official Graylog partner, reseller or managed service, and nothing on this site is endorsed by Graylog, Inc. Graylog is a trademark of its respective owner.', true],
        ['Is this a Graylog SaaS?', 'No. We design, deploy and operate dedicated Graylog environments for individual customers — preferably inside the customer\'s own cloud account or data center. We do not run a shared, multi-tenant Graylog service.', false],
    ],

    'Deployment & infrastructure' => [
        ['Can you deploy Graylog on Hetzner?', 'Yes — on Hetzner Cloud servers, Hetzner dedicated servers or a hybrid of both. Dedicated servers with local NVMe are often a cost-effective choice for the search tier. One design detail: Hetzner Cloud Load Balancers do not forward UDP, so UDP syslog needs a different ingestion design.', true],
        ['Can you deploy Graylog on AWS?', 'Yes. We deploy into your AWS account on EC2 with EBS storage, Network Load Balancers for TCP and UDP inputs, private subnets and S3-based backups, delivered as Terraform.', false],
        ['Can you deploy Graylog on Google Cloud?', 'Yes. We deploy on Compute Engine with Persistent Disk or Hyperdisk, passthrough Network Load Balancers for inputs and Cloud Storage for backups.', false],
        ['Can you work with my existing infrastructure?', 'Yes. We regularly deploy onto customer-owned servers, private clouds and existing cloud accounts, within your network and security policies. We need modern Linux, adequate disks and a clear network path from your log sources.', false],
        ['Can you build a high-availability Graylog cluster?', 'Yes. A typical HA design uses two or more Graylog nodes behind a load balancer, a search cluster of at least three nodes with replicas, and a three-member MongoDB replica set — spread across failure domains and tested by failing components before go-live.', true],
        ['Do you support Docker?', 'Yes. Graylog, MongoDB and the search backend all have container images. Docker Compose is a good fit for smaller or self-contained environments; for larger deployments we often prefer native packages on dedicated hosts for simpler operations.', false],
        ['Do you support Kubernetes?', 'Yes, in two ways: collecting logs from Kubernetes clusters (the most common need), and running Graylog on Kubernetes when there is a clear operational reason. For the latter, stateful components need careful storage and disruption planning.', false],
        ['Can you deploy OpenSearch?', 'Yes. We deploy and operate self-managed OpenSearch clusters as the search backend for Graylog, in versions supported by your Graylog release. OpenSearch is licensed under the Apache License 2.0 and is developed under the OpenSearch Software Foundation.', false],
        ['Can you deploy Graylog Data Node?', 'Yes. Graylog Data Node is the vendor\'s component for running and managing the OpenSearch-based search layer, including certificate handling and version management. Graylog\'s documentation lists it as a preferred option for current versions. Whether Data Node or self-managed OpenSearch is the better fit depends on how much control you need over the search cluster.', true],
        ['Do you still deploy Elasticsearch for Graylog?', 'Not for new deployments. Graylog 7.0 deprecated Elasticsearch as a search backend, with removal planned for Graylog 8.0. For existing environments on Elasticsearch we plan a migration to Graylog Data Node or OpenSearch.', false],
        ['How much storage does Graylog need?', 'It depends on daily ingest, retention, replica count and how your messages index. A starting model is: daily volume × retention days × (1 + replicas) × an index overhead factor measured from your data, plus headroom for disk watermarks and growth. We build this with your real numbers rather than rules of thumb.', false],
    ],

    'Operations' => [
        ['Can you manage an existing Graylog cluster?', 'Yes. Takeovers start with read-only access and a written health check covering versions, disks, indices, journals, search cluster health, MongoDB, certificates, backups and access. Then we fix what is urgent and move the environment into ongoing maintenance if you want that.', true],
        ['Can you migrate my Graylog server?', 'Yes — to new hardware, a different provider, a newer Graylog version, or from Elasticsearch to OpenSearch or Data Node. Migrations are planned with a cut-over and rollback procedure, and where possible logs are dual-shipped during the transition.', true],
        ['Can you configure TLS?', 'Yes. We configure TLS for the web interface and API, for inputs that support it (syslog over TLS, GELF TCP, Beats, OpenTelemetry), and between Graylog, the search backend and MongoDB. Certificates can come from your internal CA or from Let\'s Encrypt, with renewal automated and monitored.', false],
        ['Can you configure backups?', 'Yes. We back up MongoDB, Graylog configuration and infrastructure code, and design search-layer backups or snapshots to object storage around your retention requirements. Backups are only considered working once a restore has been tested.', false],
        ['Can you monitor Graylog?', 'Yes. We monitor the platform itself: node health, journal utilisation, processing and output rates, search cluster status, disk usage, MongoDB replication and certificate expiry. Graylog can expose metrics in Prometheus format, which fits into most existing monitoring stacks.', false],
        ['Can you maintain the server after deployment?', 'Yes. Ongoing maintenance covers OS patching, Graylog and dependency upgrades, capacity management, backup verification and troubleshooting. Scope and response expectations are agreed per environment.', true],
        ['What access do you need, and how do we share credentials?', 'Access is agreed after the initial conversation and shared through a secure channel you control — never through the public contact form. We prefer named, individual accounts that you can audit and revoke.', false],
    ],

    'Licensing' => [
        ['Do I need a Graylog commercial license?', 'It depends on the features you need and how you use the software. Graylog Open covers core log management. If your requirements include functionality from Graylog Enterprise or Graylog Security, you obtain a license from the vendor. We can map your technical requirements to editions, but we do not provide legal advice — license terms are defined by the vendor.', true],
        ['What is the difference between Graylog Open and Enterprise?', 'Graylog Open is free and source-available under the SSPL. Graylog Enterprise is a commercial edition that adds features aimed at larger organisations and is licensed by data volume according to the vendor\'s current pricing model. The exact feature split changes between releases, so we always check the vendor\'s current comparison before recommending one.', false],
        ['Does using Graylog Open mean there are no licensing considerations?', 'No. Graylog Open, MongoDB Community Server and parts of the Elasticsearch ecosystem are distributed under licenses such as the SSPL that carry specific conditions, especially around offering software as a service. How these apply depends on your deployment and use. Please review the license texts and, where needed, consult legal counsel.', false],
    ],
];
