# SEO & content architecture

## Information architecture
```
/                                   Home — all key themes, form (#request)
/services                           Hub
  /services/graylog-deployment      deployment, setup, docker, IaC
  /services/graylog-cluster         cluster, HA, architecture
  /services/graylog-managed-services managed service, operations, monitoring
  /services/graylog-migration       migration, upgrades, Elasticsearch → OpenSearch/Data Node
  /services/graylog-maintenance     maintenance, upgrades, troubleshooting, takeover
  /services/graylog-consulting      consulting, sizing, capacity, edition evaluation
/infrastructure                     Hub (+ OVHcloud, other VPS, own servers)
  /infrastructure/hetzner | aws | google-cloud | azure
/solutions                          Hub — 10 use cases, protocols (syslog, GELF, Beats, OTel)
  /solutions/kubernetes-logging
/pricing  /faq  /contact  /licensing  /blog  /privacy  /terms
```
Every page has unique content — no templated keyword pages. Add pages only when they carry real, distinct content.

## On-page implementation
- Unique `<title>` / meta description per route (`includes/routes.php`, data files).
- Canonical URLs, trailing-slash and `/index.php` redirects to one canonical form; www → apex and HTTP → HTTPS in `.htaccess`.
- Open Graph + Twitter card with a 1200×630 image (`assets/img/og-default.png`); per-page `og_image` supported.
- `robots` meta: `noindex` for 404 and for `/blog` until it has posts.
- JSON-LD `@graph`: Organization (every page), WebSite (home), BreadcrumbList (inner pages), Service (home, services, infra, solution), FAQPage (/faq, service pages, Kubernetes page), ItemList (/services), TechArticle (blog posts). **No Review/AggregateRating markup.**
- `sitemap.xml` generated from the route table: `php bin/build-sitemap.php`.
- Semantic headings: one `h1` per page; sections use `h2`/`h3`.

## Keyword themes → pages
| Theme | Primary page |
|---|---|
| graylog managed service / hosting / infrastructure | `/`, `/services/graylog-managed-services` |
| graylog deployment / setup / server / docker | `/services/graylog-deployment` |
| graylog cluster / HA | `/services/graylog-cluster` |
| graylog migration / upgrade | `/services/graylog-migration`, `/services/graylog-maintenance` |
| graylog consulting / sizing | `/services/graylog-consulting` |
| graylog hetzner / aws / google cloud / azure | `/infrastructure/*` |
| graylog kubernetes | `/solutions/kubernetes-logging` |
| graylog open / data node / opensearch / licensing | `/faq`, `/licensing` |

## Blog
File-based: copy `content/blog/_template.php` to `content/blog/<slug>.php`, set `draft => false`, run the sitemap build.
Planned topics are listed in `includes/blog.php → blog_planned_topics()` and shown on `/blog` until the first post exists.
Suggested first articles (highest intent): storage sizing, single node vs cluster, Data Node architecture, Graylog on Hetzner, migration guide.
Each article should link to the matching service/infrastructure page, and vice versa.

## Future extensions
- Case studies: add `content/case-studies/` with the same pattern as the blog (only real, approved customers).
- CRM: extend `dispatch_request()` with a webhook/API call.
- Customer portal: separate app/subdomain (e.g. `portal.managed-graylog.com`) — keep the marketing site static-ish.
- Analytics: privacy-friendly, self-hosted; update CSP and privacy policy.
