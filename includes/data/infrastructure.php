<?php
/**
 * Infrastructure providers. Entries with 'page' => true render at /infrastructure/{slug}.
 * No provider logos are used — only neutral glyphs — to avoid implying partnership.
 */
declare(strict_types=1);

return [

    'hetzner' => [
        'page'        => true,
        'name'        => 'Hetzner',
        'glyph'       => 'H',
        'tag'         => 'Cloud & dedicated',
        'summary'     => 'Cost-efficient cloud servers and dedicated hardware in EU and US locations. A strong fit for high-volume, storage-heavy logging.',
        'title'       => 'Graylog on Hetzner — Cloud & Dedicated Server Deployment',
        'description' => 'Deploy Graylog on Hetzner Cloud or Hetzner dedicated servers. Architecture, private networking, load balancing, storage and backups designed for log workloads.',
        'h1'          => 'Graylog on Hetzner',
        'lead'        => 'Logging is storage- and I/O-heavy. Hetzner\'s pricing on dedicated servers with large local NVMe or HDD capacity makes it one of the most cost-effective places to run a serious Graylog platform.',
        'intro'       => [
            'We deploy Graylog on Hetzner Cloud servers, on Hetzner dedicated servers, or on a mix of both — for example Graylog nodes on cloud instances with the search tier on dedicated machines with local NVMe. Cloud Networks and vSwitch can connect the two privately.',
            'Hetzner has data center locations in Germany and Finland, which is relevant for teams that want logs stored in the EU. Your data protection obligations are yours to assess; we make the location a deliberate part of the design.',
        ],
        'components'  => [
            ['Compute', 'Cloud servers for Graylog nodes and small clusters; dedicated servers for search nodes that need sustained disk throughput.'],
            ['Networking', 'Private Cloud Networks and vSwitch, Hetzner Cloud Firewalls plus host firewalls, no public exposure of MongoDB or search ports.'],
            ['Load balancing', 'Hetzner Cloud Load Balancers handle TCP and HTTP(S), not UDP. UDP syslog needs a different design — for example TCP syslog from sources, a relay tier, or floating IPs with keepalived.'],
            ['Storage', 'Local NVMe on dedicated servers for hot data, Cloud Volumes where flexibility matters more than throughput.'],
            ['Backups', 'Configuration and MongoDB backups to Storage Box or S3-compatible Object Storage; search snapshots where the backend supports the repository type.'],
            ['Automation', 'Terraform with the hcloud provider, Ansible for host configuration, reproducible rebuilds.'],
        ],
        'considerations' => [
            'Dedicated servers are provisioned with lead time and have no live migration — plan capacity and redundancy accordingly.',
            'Choose between cloud-only, dedicated-only and hybrid based on data volume and how quickly you need to scale.',
            'Design around the lack of UDP load balancing rather than discovering it during cut-over.',
        ],
    ],

    'aws' => [
        'page'        => true,
        'name'        => 'AWS',
        'glyph'       => 'A',
        'tag'         => 'Public cloud',
        'summary'     => 'Run Graylog inside your AWS accounts, next to the workloads it collects from, with VPC-private components and S3-based backups.',
        'title'       => 'Graylog on AWS — EC2, EBS, NLB & S3 Architecture',
        'description' => 'Deploy and operate Graylog in your AWS account: EC2 sizing, EBS gp3 storage, Network Load Balancer for syslog and GELF, S3 backups and Terraform-based delivery.',
        'h1'          => 'Graylog on AWS',
        'lead'        => 'If your workloads run on AWS, keeping log infrastructure in the same region avoids data transfer surprises and lets you use the IAM, VPC and backup tooling you already govern.',
        'intro'       => [
            'We deploy Graylog into your AWS account on EC2, typically across multiple Availability Zones within one region. Graylog nodes sit behind a Network Load Balancer, which supports both TCP and UDP listeners for syslog and GELF traffic.',
            'Managed search services are tempting, but compatibility with Graylog depends on the Graylog version and the service\'s supported API surface. We verify against current Graylog documentation before recommending one. In most cases, Graylog Data Node or self-managed OpenSearch on EC2 is more predictable.',
        ],
        'components'  => [
            ['Compute', 'EC2 instance families selected per role: memory for search nodes, balanced compute for Graylog nodes, burstable instances avoided for sustained ingest.'],
            ['Storage', 'EBS gp3 with IOPS and throughput provisioned independently of size — sized from real indexing load, not defaults.'],
            ['Networking', 'Private subnets, Security Groups per tier, VPC endpoints where they reduce NAT costs.'],
            ['Load balancing', 'Network Load Balancer for TCP/UDP inputs; Application Load Balancer or NLB with TLS for the web interface and API.'],
            ['Backups', 'Snapshots to S3 via the backend\'s S3 repository support where available, MongoDB backups to encrypted S3 buckets, lifecycle policies for retention.'],
            ['Automation', 'Terraform modules in your repository, Ansible or cloud-init for configuration, tags for cost allocation.'],
        ],
        'considerations' => [
            'Cross-AZ traffic is billed — replica placement and collector routing affect cost.',
            'Log volume from other regions or on-premises sources may incur data transfer charges.',
            'Instance and volume sizing should follow measured ingest, and be revisited as volumes grow.',
        ],
    ],

    'google-cloud' => [
        'page'        => true,
        'name'        => 'Google Cloud',
        'glyph'       => 'G',
        'tag'         => 'Public cloud',
        'summary'     => 'Graylog on Compute Engine with Persistent Disk or Hyperdisk, passthrough load balancing and Cloud Storage backups.',
        'title'       => 'Graylog on Google Cloud — Compute Engine Deployment',
        'description' => 'Deploy Graylog on Google Cloud: Compute Engine sizing, Persistent Disk and Hyperdisk storage, passthrough Network Load Balancer, Cloud Storage backups and Terraform.',
        'h1'          => 'Graylog on Google Cloud',
        'lead'        => 'For teams on Google Cloud, Graylog fits naturally on Compute Engine inside your VPC — with GKE and VM logs shipped to it privately.',
        'intro'       => [
            'We deploy Graylog on Compute Engine VMs across zones in a region, with a passthrough Network Load Balancer for TCP and UDP inputs and an HTTPS load balancer or internal access for the web interface.',
            'Running Graylog on VMs rather than inside GKE keeps stateful components simpler to operate. Collection from GKE clusters is handled by DaemonSet collectors shipping over private networking.',
        ],
        'components'  => [
            ['Compute', 'Machine types sized per role, with memory-heavy shapes for search nodes and no shared-core types for sustained ingest.'],
            ['Storage', 'Balanced or SSD Persistent Disk, or Hyperdisk where independent throughput tuning is needed.'],
            ['Networking', 'Private VPC subnets, firewall rules per tier using network tags or service accounts, Cloud NAT for egress.'],
            ['Load balancing', 'Internal or external passthrough Network Load Balancers for TCP/UDP inputs.'],
            ['Backups', 'Cloud Storage buckets for snapshots and MongoDB backups, with retention policies.'],
            ['Automation', 'Terraform with the Google provider, instance templates, Ansible for configuration.'],
        ],
        'considerations' => [
            'Persistent Disk performance scales with disk size and machine type — size for throughput, not just capacity.',
            'Inter-zone traffic has a cost; plan replica placement deliberately.',
            'Keep collection from GKE on private IPs to avoid egress and exposure.',
        ],
    ],

    'azure' => [
        'page'        => true,
        'name'        => 'Azure',
        'glyph'       => 'Az',
        'tag'         => 'Public cloud',
        'summary'     => 'Graylog on Azure VMs with Managed Disks, Azure Load Balancer and Blob Storage backups, integrated into your existing subscriptions.',
        'title'       => 'Graylog on Azure — VM, Managed Disk & Load Balancer Design',
        'description' => 'Deploy Graylog on Microsoft Azure: VM sizing, Premium SSD managed disks, Standard Load Balancer for syslog, Blob Storage backups and Terraform-based delivery.',
        'h1'          => 'Graylog on Azure',
        'lead'        => 'Many organisations standardised on Azure still need an independent log platform for Linux, network and application logs. We run Graylog inside your subscriptions and network policies.',
        'intro'       => [
            'We deploy Graylog on Azure Virtual Machines across Availability Zones where the region supports them, with Azure Load Balancer (Standard) handling TCP and UDP inputs.',
            'Identity integration, network security groups and Key Vault usage are designed to fit your existing Azure governance rather than working around it.',
        ],
        'components'  => [
            ['Compute', 'VM sizes chosen per role, with memory-optimised sizes for search nodes.'],
            ['Storage', 'Premium SSD or Premium SSD v2 managed disks, sized for indexing throughput.'],
            ['Networking', 'VNets with subnets per tier, Network Security Groups, private endpoints where appropriate.'],
            ['Load balancing', 'Azure Load Balancer (Standard) for TCP/UDP inputs, Application Gateway or internal access for the web UI.'],
            ['Backups', 'Blob Storage for snapshots where the backend supports it and for MongoDB backups, with lifecycle management.'],
            ['Automation', 'Terraform with the AzureRM provider, Ansible for configuration.'],
        ],
        'considerations' => [
            'Disk performance tiers and VM disk throughput limits both constrain indexing — check both.',
            'Zone-redundant designs increase cost; decide based on your actual availability requirement.',
            'Align tagging and resource groups with your cost management model.',
        ],
    ],

    'ovh' => [
        'page'    => false,
        'name'    => 'OVHcloud',
        'glyph'   => 'O',
        'tag'     => 'Cloud & bare metal',
        'summary' => 'Bare-metal and public cloud options in European and other regions, with private networking between servers.',
    ],

    'other' => [
        'page'    => false,
        'name'    => 'Other VPS & dedicated',
        'glyph'   => '+',
        'tag'     => 'Any Linux host',
        'summary' => 'Any provider that gives us modern Linux, predictable disks and private networking.',
    ],

    'customer' => [
        'page'    => false,
        'name'    => 'Your own servers',
        'glyph'   => '~',
        'tag'     => 'On-premises / colo',
        'summary' => 'Bare metal or virtualised infrastructure in your data center, including restricted networks.',
    ],
];
