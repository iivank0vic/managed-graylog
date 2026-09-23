<?php
/**
 * SEO helpers: meta tags and Schema.org JSON-LD builders.
 * No Review/AggregateRating markup — there are no genuine reviews to mark up.
 */
declare(strict_types=1);

function page_title(array $page): string
{
    if (!empty($page['title_full'])) {
        return $page['title_full'];
    }
    return ($page['title'] ?? config('site_name')) . ' | ' . config('site_name');
}

function org_id(): string
{
    return abs_url('/') . '#organization';
}

function schema_organization(): array
{
    $org = [
        '@type'       => 'Organization',
        '@id'         => org_id(),
        'name'        => config('site_name'),
        'url'         => abs_url('/'),
        'logo'        => abs_url('/assets/img/logo-512.png'),
        'description' => 'Independent provider of Graylog deployment, managed infrastructure and consulting services.',
        'email'       => config('contact_email'),
        'knowsAbout'  => ['Graylog', 'Centralized logging', 'Log management', 'OpenSearch', 'MongoDB', 'Linux', 'Terraform', 'Ansible', 'Kubernetes logging', 'OpenTelemetry', 'Syslog', 'GELF'],
    ];
    if ($legal = config('company.legal_name')) {
        $org['legalName'] = $legal;
    }
    if ($addr = config('company.address')) {
        $org['address'] = ['@type' => 'PostalAddress', 'streetAddress' => $addr, 'addressCountry' => config('company.country')];
    }
    if ($vat = config('company.vat_id')) {
        $org['vatID'] = $vat;
    }
    if ($same = config('same_as')) {
        $org['sameAs'] = array_values($same);
    }
    return $org;
}

function schema_website(): array
{
    return [
        '@type'     => 'WebSite',
        '@id'       => abs_url('/') . '#website',
        'url'       => abs_url('/'),
        'name'      => config('site_name'),
        'publisher' => ['@id' => org_id()],
        'inLanguage'=> config('locale'),
    ];
}

function schema_breadcrumbs(array $crumbs): ?array
{
    if (!$crumbs) {
        return null;
    }
    $items = [['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => abs_url('/')]];
    $pos = 2;
    foreach ($crumbs as $label => $path) {
        $items[] = ['@type' => 'ListItem', 'position' => $pos++, 'name' => $label, 'item' => abs_url($path)];
    }
    return ['@type' => 'BreadcrumbList', 'itemListElement' => $items];
}

function schema_service(string $name, string $description, string $path, ?string $type = null): array
{
    return [
        '@type'       => 'Service',
        '@id'         => abs_url($path) . '#service',
        'name'        => $name,
        'serviceType' => $type ?? $name,
        'description' => $description,
        'url'         => abs_url($path),
        'provider'    => ['@id' => org_id()],
    ];
}

/** @param array<int, array{0:string,1:string}> $faqs */
function schema_faq(array $faqs): ?array
{
    if (!$faqs) {
        return null;
    }
    return [
        '@type'      => 'FAQPage',
        'mainEntity' => array_map(static fn(array $f): array => [
            '@type'          => 'Question',
            'name'           => $f[0],
            'acceptedAnswer' => ['@type' => 'Answer', 'text' => $f[1]],
        ], $faqs),
    ];
}

/** Wrap nodes into a single @graph document. */
function schema_graph(array $nodes): string
{
    $nodes = array_values(array_filter($nodes));
    return json_ld(['@context' => 'https://schema.org', '@graph' => $nodes]);
}
