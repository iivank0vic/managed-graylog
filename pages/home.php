<?php
declare(strict_types=1);

$page['schema'][] = schema_service(
    'Managed Graylog infrastructure',
    'Design, deployment, migration, maintenance and operation of dedicated Graylog environments on cloud or customer infrastructure.',
    '/',
    'Log management infrastructure services'
);

foreach (['hero', 'problems', 'services', 'layers', 'providers', 'architecture', 'usecases', 'process', 'request', 'pricing', 'licensing', 'faq'] as $section) {
    partial('home/' . $section);
}
partial('components/cta-band');
