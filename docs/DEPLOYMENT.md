# Deployment

## Requirements
- PHP **8.1+** (tested on 8.4) with `mbstring`, `json` (default), `session`.
- Apache with `mod_rewrite` and `mod_headers` (optional: `mod_deflate`/`mod_brotli`, `mod_expires`).
  Nginx works too — see the snippet below.
- A working mail setup (`mail()` via local MTA) **or** switch to an SMTP/API transport (see `includes/contact-handler.php → send_request_mail()`).
- HTTPS certificate.

The repository root **is** the web root.

## 1. GitHub Actions (existing FTP workflow)
`.github/workflows/managed-graraylog.yml` deploys on every push using FTP-Deploy-Action. The workflow now excludes build sources,
docs and local state. Secrets `FTP_HOSTNAME`, `FTP_USERNAME`, `FTP_PASSWD` stay in GitHub → Settings → Secrets.

Recommended: switch the server/protocol to **FTPS** (`protocol: ftps`) or SFTP-based deploy if your host supports it.

## 2. First-time server setup
1. Create `includes/config.local.php` **on the server** (never commit it) from `includes/config.local.example.php`:
   - `app_secret`: `php -r "echo bin2hex(random_bytes(32)), PHP_EOL;"`
   - `mail.to`, `mail.from` (a mailbox on your domain with SPF/DKIM/DMARC set up)
   - Optionally `storage_path` → a directory **outside** the web root.
2. Make `storage/` (or your `storage_path`) writable by PHP: `chmod 700`, owned by the PHP user.
3. Fill the placeholders listed in `docs/LICENSING-AND-CLAIMS.md §5`.
4. Enable HSTS in `.htaccess` once HTTPS is confirmed on all hostnames.
5. Submit `https://managed-graylog.com/sitemap.xml` in Google Search Console and Bing Webmaster Tools.

## 3. Local development
```bash
npm install            # bootstrap (scss), sass, lucide-static (icons) — dev only
npm run build:css      # src/scss → assets/css/site.min.css
npm run serve          # php -S 127.0.0.1:8080 bin/router.php
```
Create `includes/config.local.php` with `'env' => 'development', 'base_url' => 'http://127.0.0.1:8080', 'mail' => ['transport' => 'log']`.

After adding pages or blog posts: `php bin/build-sitemap.php` (commit the regenerated `sitemap.xml`).

## 4. Nginx equivalent
```nginx
root /var/www/managed-graylog;
index index.php;
location ~ ^/(includes|partials|pages|content|storage|docs|src|bin|node_modules)/ { deny all; }
location ~ /\. { deny all; }
location ~* \.(css|js|woff2|svg|png|ico)$ { expires 1y; add_header Cache-Control "public, immutable"; }
location / { try_files $uri /index.php$is_args$args; }
location = /index.php { include fastcgi_params; fastcgi_param SCRIPT_FILENAME $document_root/index.php; fastcgi_pass unix:/run/php/php8.3-fpm.sock; }
location ~ \.php$ { deny all; }
gzip on; gzip_types text/css application/javascript image/svg+xml application/xml;
```
Security headers are also emitted by PHP (`includes/bootstrap.php`), so they apply on nginx as well.

## 5. Behind Cloudflare / a reverse proxy
Rate limiting uses `REMOTE_ADDR`. Configure `mod_remoteip` (Apache) or `real_ip_header` (nginx) so it contains the real client IP.
Also consider Cloudflare Turnstile if spam gets through (add the widget script to the CSP).

## 6. Post-deploy checklist
- [ ] `/includes/config.php`, `/storage/`, `/docs/` return 403
- [ ] Form submission arrives by email; `storage/submissions` retention defined
- [ ] https://securityheaders.com and Lighthouse run
- [ ] Rich Results Test on `/`, `/faq`, a service page
- [ ] Placeholders replaced, privacy/terms reviewed
