# managed-graylog.com

Marketing website for an **independent** Graylog deployment, managed-infrastructure and consulting provider.
PHP 8.1+ · Bootstrap 5.3 (custom Sass subset) · vanilla JS · no framework, no database.

## Structure
```
index.php                  Front controller (routing, layout)
.htaccess                  Rewrites, security headers, caching, compression, private-dir blocking
robots.txt · sitemap.xml · site.webmanifest · favicon.ico
includes/
  bootstrap.php            Config, error handling, security headers
  config.php               Non-secret config (+ optional config.local.php, git-ignored)
  functions.php            Escaping, URLs, assets, CSRF, sessions, flash
  routes.php               Route table (also feeds the sitemap)
  seo.php                  Meta + Schema.org builders
  header.php · footer.php  Layout
  icons.php                Inline SVG icons (Lucide, ISC) + brand mark
  contact-handler.php      Form processing (validation, anti-spam, mail)
  blog.php                 File-based blog
  data/                    Content: services, infrastructure, solutions, faq, form options
pages/                     One view per page type (home, service, infrastructure, …)
partials/home/             Homepage sections
partials/components/       Reusable blocks (control-plane visual, request form, FAQ, CTA…)
content/blog/              Blog posts (_template.php)
assets/css/site.min.css    Built from src/scss
assets/js/main.js          All interactions
assets/fonts · assets/img
storage/                   Writable: rate-limit state, submissions, generated secret (not deployed)
src/scss/                  Sass sources
bin/                       Dev tools: dev-server router, sitemap + icon builders
docs/                      Deployment, security, SEO, licensing/claims policy
```

## Quick start
```bash
npm install && npm run build:css
cp includes/config.local.example.php includes/config.local.php   # edit it
npm run serve   # http://127.0.0.1:8080
```

## Docs
- [Deployment](docs/DEPLOYMENT.md)
- [Security](docs/SECURITY.md)
- [SEO & content](docs/SEO-AND-CONTENT.md)
- [Licensing-sensitive wording & claims NOT to make](docs/LICENSING-AND-CLAIMS.md)

## Before launch
Replace placeholders (company details, contact mailbox), complete `/privacy` and `/terms`, and have the licensing wording reviewed.

Graylog is a trademark of its respective owner. This project is not affiliated with Graylog, Inc.
Third-party: Bootstrap (MIT), Lucide icons (ISC), Inter & JetBrains Mono fonts (SIL OFL 1.1).
