<?php
/**
 * Deployment request form definition — shared by the form partial and the server-side handler.
 * Select/radio values are whitelisted server-side against these keys.
 */
declare(strict_types=1);

return [
    'environment' => [
        'new'           => 'Nothing yet — new deployment',
        'single'        => 'Single Graylog server',
        'cluster'       => 'Graylog cluster',
        'other_stack'   => 'Another logging stack (ELK, Loki, files…)',
        'unsure'        => 'Not sure',
    ],
    'volume' => [
        'lt5'     => '< 5 GB/day',
        '5_20'    => '5 – 20 GB/day',
        '20_100'  => '20 – 100 GB/day',
        '100_500' => '100 – 500 GB/day',
        'gt500'   => '500+ GB/day',
        'unknown' => 'Unknown — help us estimate',
    ],
    'servers' => [
        '1_10'     => '1 – 10',
        '10_50'    => '10 – 50',
        '50_250'   => '50 – 250',
        '250_1000' => '250 – 1,000',
        'gt1000'   => '1,000+',
    ],
    'infrastructure' => [
        'hetzner_cloud'     => 'Hetzner Cloud',
        'hetzner_dedicated' => 'Hetzner dedicated',
        'aws'               => 'AWS',
        'gcp'               => 'Google Cloud',
        'azure'             => 'Azure',
        'ovh'               => 'OVHcloud',
        'other_vps'         => 'Other VPS / dedicated',
        'own'               => 'Our own servers',
        'recommend'         => 'Recommend something',
    ],
    'retention' => [
        '7d'      => '7 days',
        '30d'     => '30 days',
        '90d'     => '90 days',
        '180d'    => '180 days',
        '1y'      => '1 year',
        'gt1y'    => 'More than 1 year',
        'unsure'  => 'Not sure yet',
    ],
    'yesno' => [
        'yes'    => 'Yes',
        'no'     => 'No',
        'unsure' => 'Unsure',
    ],
];
