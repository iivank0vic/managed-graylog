# Licensing-sensitive wording & claims policy

_Last reviewed: 23 September 2026. Not legal advice — have counsel review before launch._

## 1. Research summary (what the copy is based on)

| Topic | Current situation (as researched) | Source |
|---|---|---|
| Graylog Open | Free, "source-available under SSPL", no ingest cap per vendor pricing page | graylog.org/pricing |
| Graylog commercial editions | Graylog Enterprise and Graylog Security; licensed by daily volume or consumption units per vendor | graylog.org/pricing |
| Graylog SSPL history | Graylog moved to SSPL with v4.0 (2021) | graylog.org/post/graylog-v4-0-licensing-sspl |
| Current Graylog | 7.x (7.1 released 2026). 7.0 requires Java 21 and MongoDB ≥ 7.0 | go2docs.graylog.org – Upgrade to Graylog 7.0 |
| Search backend | Elasticsearch and OpenSearch 1.x **deprecated in 7.0, removal planned for 8.0**; Graylog Data Node or self-managed OpenSearch preferred | same |
| OpenSearch | Apache License 2.0; governed by the OpenSearch Software Foundation (Linux Foundation) | opensearch.org |
| Elasticsearch | Source available under a choice of **ELv2, SSPL or AGPLv3** (AGPL option added 2024); default distribution ELv2 | elastic.co/pricing/faq/licensing |
| MongoDB Community Server | SSPL | mongodb.com |

## 2. Why the site is worded the way it is

- **Independent provider.** The business sells engineering (design, deployment, operations). The site never implies partnership, reselling, certification or endorsement.
- **"Managed" ≠ "SaaS".** SSPL §13 attaches obligations to *offering the program as a service*. The site therefore describes **dedicated, per-customer environments, preferably in the customer's own account or servers**, and explicitly says there is **no shared multi-tenant Graylog service**. Whether *your* concrete operating model triggers §13 is a legal question — get counsel's opinion, especially before hosting environments in *your* accounts and billing for them.
- **Graylog Open is not "licensing solved".** The site says so explicitly (FAQ + /licensing).
- **Commercial features** are routed to the vendor: "customers requiring commercial Graylog functionality obtain the appropriate license from the vendor".
- **Elasticsearch** is not promised anywhere for new builds; copy uses "Graylog Data Node / OpenSearch".
- **No logos.** Providers are shown as neutral monograms (H, A, G…). No Graylog logo or visual identity is used; the brand mark is original.
- **Footer + /terms + /licensing** carry the non-affiliation and trademark notices.

## 3. Claims that must NOT be made without verification

Do not add any of these unless you have written proof and, where relevant, legal sign-off:

1. "Official Graylog partner / reseller / MSP", "Graylog certified", "Graylog-approved".
2. "Official Graylog Managed Service", "Graylog Cloud", "Graylog Hosting by Graylog" or any name implying the vendor runs it.
3. "Graylog as a Service", "Graylog SaaS", or a shared/multi-tenant hosted Graylog offering — until counsel has reviewed SSPL implications.
4. "Graylog is completely free as a managed service" / "no licensing restrictions".
5. "All Graylog Enterprise/Security features included/free".
6. "We can provide/resell Graylog Enterprise licenses" (unless you have a reseller agreement).
7. Partnership/certification with Hetzner, AWS, Google Cloud, Azure, OVHcloud (e.g. "AWS Partner", "Google Cloud Partner").
8. Compliance claims: "GDPR compliant platform", "ISO 27001 certified", "SOC 2", "HIPAA", "PCI DSS" — for you *or* for customer environments.
9. Uptime/SLA numbers ("99.99% uptime"), response-time guarantees, "24/7 support" — unless contractually backed.
10. Customer counts, logos, testimonials, case studies, reviews, awards, years in business, number of engineers, GB/day managed.
11. "Zero data loss" / "never lose a log".
12. Performance numbers ("handles 1 TB/day on 3 nodes") presented as general facts.
13. Using Elasticsearch as a default search backend for new Graylog deployments.
14. Specific pricing (the site intentionally has none).
15. Legal advice of any kind about licensing.

## 4. Things to re-verify periodically (every ~6 months)

- Graylog editions, names and pricing model (graylog.org/pricing).
- Supported search backends / MongoDB versions per Graylog release.
- Whether Graylog's OpenTelemetry input and other features referenced remain in Graylog Open.
- Elastic/OpenSearch/MongoDB license changes.
- Provider facts used in copy (e.g. Hetzner Cloud Load Balancer has no UDP; AWS NLB / GCP passthrough NLB / Azure LB support UDP).

## 5. Placeholders to fill before launch

- `includes/config.php`: `company.legal_name`, `address`, `vat_id`, `country`, `contact_email`, `mail.to`.
- `/privacy` and `/terms`: marked DRAFT; complete with counsel. EU/HR businesses typically also need a legal notice (company details) — the footer prints them automatically once set in config.
