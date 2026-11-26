# Deployment

## Requirements
- PHP **8.1+** (tested on 8.4) with `mbstring`, `json` (default), `session`.
- Apache with `mod_rewrite` and `mod_headers` (optional: `mod_deflate`/`mod_brotli`, `mod_expires`).
  Nginx works too — see the snippet below.
- A Mailgun account (recommended, see [Mailgun](#mailgun)) **or** a working local MTA for PHP `mail()`. PHP `curl` extension recommended (falls back to streams).
- HTTPS certificate.

The repository root **is** the web root.

## 1. GitHub Actions (existing FTP workflow)
`.github/workflows/managed-graraylog.yml` deploys on every push to **`main`** (or manually via *Run workflow*) using FTP-Deploy-Action.
Feature branches and pull requests do not deploy. The workflow now excludes build sources,
docs and local state. Secrets `FTP_HOSTNAME`, `FTP_USERNAME`, `FTP_PASSWD` stay in GitHub → Settings → Secrets.

Recommended: switch the server/protocol to **FTPS** (`protocol: ftps`) or SFTP-based deploy if your host supports it.

## 2. First-time server setup
1. Create `includes/config.local.php` **on the server** (never commit it) from `includes/config.local.example.php`:
   - `app_secret`: `php -r "echo bin2hex(random_bytes(32)), PHP_EOL;"`
   - `mailgun.api_key`, `mailgun.domain`, `mailgun.region` (see [Mailgun](#mailgun))
   - optionally `mail.to`, `mail.to_messages`, `mail.from`, `mail.autoreply`
   - Optionally `storage_path` → a directory **outside** the web root.
2. Make `storage/` (or your `storage_path`) writable by PHP: `chmod 700`, owned by the PHP user.
3. Fill the placeholders listed in `docs/LICENSING-AND-CLAIMS.md §5`.
4. Enable HSTS in `.htaccess` once HTTPS is confirmed on all hostnames.
5. Submit `https://managed-graylog.com/sitemap.xml` in Google Search Console and Bing Webmaster Tools.

## Mailgun
Both forms (`/contact` deployment request and `/contact/message` quick message) send through `includes/mailer.php`.
With `mail.transport = 'auto'` (default) Mailgun is used as soon as an API key and domain are configured; otherwise PHP `mail()`.

1. **Add a sending domain** in Mailgun → *Sending → Domains*, e.g. `mg.managed-graylog.com` (a subdomain keeps
   Mailgun's DNS separate from your normal mailbox). Pick **EU** region if you want data kept in the EU — the region must match `mailgun.region`.
2. **DNS** at your DNS provider — copy the exact values Mailgun shows:
   - `TXT mg.managed-graylog.com` → `v=spf1 include:mailgun.org ~all`
   - `TXT <selector>._domainkey.mg.managed-graylog.com` → DKIM key
   - `CNAME email.mg.managed-graylog.com` → tracking (optional; the site disables tracking per message)
   - `MX mg.managed-graylog.com` → `mxa.eu.mailgun.org` / `mxb.eu.mailgun.org` (only needed for bounces/receiving)
   - Root domain DMARC if not present: `TXT _dmarc.managed-graylog.com` → `v=DMARC1; p=none; rua=mailto:dmarc@managed-graylog.com` (tighten to `quarantine` later)
   Wait until Mailgun shows the domain as **Verified**.
3. **Create a Sending API key** for that domain (*Domain settings → Sending API keys*). Do not use the account-wide private key.
4. **Configure the server** — in `includes/config.local.php` (never committed, not deployed by CI):
   ```php
   'mailgun' => [
       'api_key' => 'xxxxxxxxxxxxxxxxxxxxxxxx-xxxxxxxx-xxxxxxxx',
       'domain'  => 'mg.managed-graylog.com',
       'region'  => 'eu',
   ],
   'mail' => [
       'from'        => 'no-reply@managed-graylog.com', // any address on managed-graylog.com aligns with DMARC (relaxed)
       'to'          => 'contact@managed-graylog.com',
       // 'to_messages' => 'hello@managed-graylog.com',
       // 'autoreply'   => true,
   ],
   ```
   Alternatively set env vars `MAILGUN_API_KEY`, `MAILGUN_DOMAIN`, `MAILGUN_REGION` (e.g. `SetEnv` in the vhost, or PHP-FPM `env[...]`).
5. **Test**: submit both forms on `/contact`. Check Mailgun → *Logs*. Failures are written to the PHP error log as
   `[managed-graylog] mailgun send failed: HTTP <code> <message>` (the key and message body are never logged).
   If Mailgun is down and `form.store_submissions` is on, the visitor still gets a success message and the JSON copy stays in `storage/submissions`.

Messages are tagged in Mailgun (`deployment-request`, `contact-message`, `autoreply`) and sent with tracking disabled.
`Reply-To` is the visitor's validated address, so replying from your inbox goes straight to them.

## 3. Local development
```bash
npm install            # bootstrap (scss), sass, lucide-static (icons) — dev only
npm run build:css      # src/scss → assets/css/site.min.css
npm run serve          # php -S 127.0.0.1:8080 bin/router.php
```
Create `includes/config.local.php` with `'env' => 'development', 'base_url' => 'http://127.0.0.1:8080', 'mail' => ['transport' => 'log']`
(or real Mailgun credentials to test delivery).

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
- [ ] Both forms (deployment request + quick message) arrive by email via Mailgun; `storage/submissions` retention defined
- [ ] https://securityheaders.com and Lighthouse run
- [ ] Rich Results Test on `/`, `/faq`, a service page
- [ ] Placeholders replaced, privacy/terms reviewed
