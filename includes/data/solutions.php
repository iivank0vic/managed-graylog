<?php
/**
 * Use cases (cards on home and /solutions) and dedicated solution pages.
 * No compliance or certification claims — only technical capabilities.
 */
declare(strict_types=1);

return [
    'use_cases' => [
        ['saas', 'SaaS companies', 'layers', 'Application, API and infrastructure logs in one place, with streams per service and retention per data class.'],
        ['ecommerce', 'E-commerce', 'shopping-cart', 'Checkout errors, payment gateway responses and traffic spikes visible while they are happening — not after the weekend.'],
        ['hosting', 'Hosting providers', 'server', 'High-volume syslog from hundreds of hosts, with isolation between customer environments designed in from the start.'],
        ['gaming', 'Game servers', 'gamepad-2', 'Bursty ingest from game and matchmaking servers, with fast search when players report problems.'],
        ['kubernetes', 'Kubernetes environments', 'boxes', 'Container logs with pod, namespace and node metadata, collected by DaemonSets and routed by stream.', '/solutions/kubernetes-logging'],
        ['linux', 'Linux infrastructure', 'terminal', 'journald, syslog and auditd from fleets of Linux servers, parsed into fields you can actually search.'],
        ['webapps', 'Web applications', 'globe', 'Nginx, Apache and application logs correlated by request, with alerting on error rates.'],
        ['security', 'Security monitoring', 'shield', 'Authentication, firewall and audit logs retained and searchable for investigations. SIEM features depend on the Graylog edition.'],
        ['enterprise', 'Enterprise IT', 'building-2', 'Windows event logs, network devices and business applications under one access-controlled platform.'],
        ['msp', 'MSPs & IT providers', 'users', 'Per-client environments or carefully separated streams, operated consistently across your customer base.'],
    ],

    'pages' => [
        'kubernetes-logging' => [
            'name'        => 'Kubernetes Logging with Graylog',
            'title'       => 'Kubernetes Logging with Graylog — Architecture & Deployment',
            'description' => 'Centralized Kubernetes logging with Graylog: DaemonSet collectors, OpenTelemetry, GELF and Beats inputs, metadata enrichment, multiline handling and retention per namespace.',
            'eyebrow'     => 'Solution / Kubernetes logging',
            'h1'          => 'Kubernetes logging with Graylog',
            'lead'        => 'Containers are short-lived. Their logs should not be. We build the collection pipeline from your clusters into Graylog so every log line arrives with the context needed to find it later.',
            'sections'    => [
                [
                    'title' => 'Collection inside the cluster',
                    'body'  => [
                        'Container runtimes write stdout and stderr to files on each node. A collector running as a DaemonSet tails those files, adds Kubernetes metadata from the API server — namespace, pod, container, labels — and ships events to Graylog.',
                        'Common choices are Fluent Bit (GELF output), the OpenTelemetry Collector (OTLP to Graylog\'s OpenTelemetry input in recent versions) or Filebeat (Beats input). We pick based on what else runs in your clusters: if you already operate an OpenTelemetry pipeline for traces and metrics, logs should usually follow the same path.',
                    ],
                ],
                [
                    'title' => 'Where Graylog itself should run',
                    'body'  => [
                        'Graylog can run inside Kubernetes, but its stateful dependencies — the search layer and MongoDB — need careful storage and disruption handling. For many teams it is simpler and more resilient to run Graylog on dedicated VMs or servers outside the clusters it observes.',
                        'That separation has another benefit: when a cluster is having a bad day, the platform you use to investigate it is not affected by the same problem.',
                    ],
                ],
                [
                    'title' => 'Making container logs searchable',
                    'body'  => [
                        'Structured JSON logs are parsed into fields; unstructured logs are handled with processing pipeline rules. Multiline stack traces are joined at the collector, not after the fact. Noisy namespaces can be routed to index sets with shorter retention so they do not crowd out what matters.',
                    ],
                ],
            ],
            'pipeline' => [
                ['node', 'Container runtime', '/var/log/containers/*.log'],
                ['daemonset', 'Collector DaemonSet', 'Fluent Bit · OTel Collector · Filebeat'],
                ['enrich', 'Metadata enrichment', 'namespace · pod · container · labels'],
                ['transport', 'Transport', 'GELF · OTLP · Beats over TLS'],
                ['graylog', 'Graylog inputs', 'load-balanced, journaled'],
                ['route', 'Streams & index sets', 'retention per namespace or team'],
            ],
            'faqs' => [
                ['Should Graylog run inside Kubernetes?', 'It can, but for most teams we recommend running the Graylog platform on dedicated VMs or servers and collecting from Kubernetes with DaemonSets. It separates the observer from the observed and keeps stateful components simpler.'],
                ['Which collector should we use?', 'Fluent Bit is lightweight and widely used. The OpenTelemetry Collector makes sense if you are standardising on OpenTelemetry. Filebeat fits environments already using Beats. All three can feed Graylog.'],
                ['Can we separate logs per team or namespace?', 'Yes. Streams route messages by namespace, label or any field, and index sets give each stream its own retention. Access can then be scoped per stream.'],
            ],
        ],
    ],
];
