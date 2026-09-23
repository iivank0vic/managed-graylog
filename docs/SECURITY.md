# Security notes

## Implemented
| Control | Where |
|---|---|
| CSRF token (session-bound, rotated after success), `SameSite=Lax`, `HttpOnly`, `Secure` cookie | `includes/functions.php`, `contact-handler.php` |
| Honeypot field + HMAC-signed time-trap (min 4 s, max 24 h) | form partial, handler |
| Rate limiting: 5 valid submissions / IP / hour, HMAC-hashed IPs, file lock | `rate_limit_allow()` |
| Server-side validation, whitelisted select values, length limits, control-char stripping | `validate_request()` |
| Rejects text that looks like credentials (private keys, AWS/GitHub/Slack/Google keys, `password=…`) | `looks_like_secret()` + same check client-side |
| No user input in mail headers except validated `Reply-To`; base64-encoded subject | `send_request_mail()` |
| Output escaping via `e()` everywhere; JSON-LD encoded with `JSON_HEX_TAG` | templates, `json_ld()` |
| Strict CSP (`default-src 'self'`, no inline scripts/styles), `nosniff`, `X-Frame-Options`, `Referrer-Policy`, `Permissions-Policy`, COOP | `.htaccess` + `bootstrap.php` |
| Private dirs blocked (`includes`, `storage`, `docs`, …), direct `.php` access denied except `index.php` | `.htaccess`, per-dir `.htaccess` |
| No secrets in repo: `app_secret` from `config.local.php` or auto-generated into `storage/.app_secret` | `app_secret()` |
| Self-hosted fonts, no third-party requests | `assets/fonts` |

## Recommendations
- Move `storage_path` outside the web root.
- Enable HSTS after confirming HTTPS everywhere; consider HSTS preload later.
- Use an authenticated SMTP/API mail transport (PHPMailer, Postmark, SES) instead of `mail()`.
- Define and automate retention for `storage/submissions` (e.g. cron deleting files older than N days) — or set `form.store_submissions` to `false`.
- Add Cloudflare Turnstile / hCaptcha only if spam appears (update CSP accordingly).
- Keep PHP patched; disable `expose_php`.
- Switch deployment from FTP to FTPS/SFTP.
- If you add analytics, prefer a privacy-friendly, self-hosted option and update CSP + privacy policy.
- Never ask for credentials through the website. Access is exchanged later over a channel the customer controls.
