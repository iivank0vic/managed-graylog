<?php
/**
 * Deployment request handler (POST /contact).
 *
 * Layers of protection:
 *  - CSRF token (session-bound) and SameSite=Lax session cookie
 *  - Honeypot field + signed time-trap token (min fill time / max age)
 *  - Per-IP rate limiting (hashed IP, file-based, flock)
 *  - Strict server-side validation, whitelisted option values, length limits
 *  - Rejection of submissions that look like they contain credentials
 *  - Header-injection-safe mail (no user input in headers except a validated Reply-To)
 *  - All output escaped in templates; JSON responses for fetch clients
 *
 * Swap send_request_mail() for an SMTP/API transport (e.g. PHPMailer, Postmark, SES)
 * or add a CRM push in dispatch_request() without touching validation.
 */
declare(strict_types=1);

function handle_contact_submission(): void
{
    header('Cache-Control: no-store');
    $input = $_POST;

    // Silently accept obvious bots so they learn nothing.
    if (trim((string) ($input['website'] ?? '')) !== '') {
        respond_contact(true, [], 'sent');
    }

    if (!csrf_verify($input['_token'] ?? null)) {
        respond_contact(false, ['_form' => 'Your session expired. Please reload the page and submit again.'], null, $input, 419);
    }

    $age = form_timestamp_age($input['_ts'] ?? null);
    if ($age === null || $age > (int) config('form.max_age_seconds')) {
        respond_contact(false, ['_form' => 'The form expired. Please reload the page and submit again.'], null, $input, 400);
    }
    if ($age < (int) config('form.min_fill_seconds')) {
        respond_contact(true, [], 'sent'); // too fast for a human: treat as bot, pretend success
    }

    [$clean, $errors] = validate_request($input);
    if ($errors) {
        respond_contact(false, $errors, null, $input, 422);
    }

    // Rate limit only counts valid submissions, so users fixing typos are not locked out.
    if (!rate_limit_allow('contact', (int) config('form.rate_limit_max'), (int) config('form.rate_limit_window'))) {
        respond_contact(false, ['_form' => 'Too many requests from your network. Please try again later or email us directly.'], null, $input, 429);
    }

    if (!dispatch_request($clean)) {
        respond_contact(false, ['_form' => 'We could not send your request right now. Please email ' . config('contact_email') . ' instead.'], null, $input, 500);
    }

    // Rotate CSRF token after a successful submission.
    unset($_SESSION['csrf']);
    respond_contact(true, [], 'sent');
}

/** @return array{0: array<string,string>, 1: array<string,string>} */
function validate_request(array $in): array
{
    $opt = data('form');
    $errors = [];
    $str = static function (string $key, int $max) use ($in): string {
        $v = $in[$key] ?? '';
        if (!is_string($v)) {
            return '';
        }
        // Normalise: strip control chars (keep newlines/tabs), trim.
        $v = preg_replace('/[^\P{C}\n\t]/u', '', $v) ?? '';
        return mb_substr(trim($v), 0, $max);
    };

    $c = [
        'name'            => $str('name', 100),
        'company'         => $str('company', 120),
        'email'           => $str('email', 190),
        'environment'     => $str('environment', 40),
        'graylog_version' => $str('graylog_version', 40),
        'architecture'    => $str('architecture', 2000),
        'volume'          => $str('volume', 40),
        'servers'         => $str('servers', 40),
        'retention'       => $str('retention', 40),
        'infrastructure'  => $str('infrastructure', 40),
        'ha'              => $str('ha', 10),
        'monitoring'      => $str('monitoring', 10),
        'backup'          => $str('backup', 10),
        'notes'           => $str('notes', 4000),
    ];

    if (mb_strlen($c['name']) < 2) {
        $errors['name'] = 'Please enter your name.';
    } elseif (preg_match('/[\r\n<>]/', $c['name'])) {
        $errors['name'] = 'Please use letters only.';
    }
    if ($c['email'] === '' || !filter_var($c['email'], FILTER_VALIDATE_EMAIL) || preg_match('/[\r\n]/', $c['email'])) {
        $errors['email'] = 'Please enter a valid email address.';
    }
    if (preg_match('/[\r\n]/', $c['company'])) {
        $errors['company'] = 'Please use a single line.';
    }
    if ($c['graylog_version'] !== '' && !preg_match('/^[\w .,\/+-]{1,40}$/u', $c['graylog_version'])) {
        $errors['graylog_version'] = 'Please enter a version like 6.1, or "none".';
    }

    $choices = [
        'environment' => 'environment', 'volume' => 'volume', 'servers' => 'servers', 'retention' => 'retention',
        'infrastructure' => 'infrastructure', 'ha' => 'yesno', 'monitoring' => 'yesno', 'backup' => 'yesno',
    ];
    foreach ($choices as $field => $set) {
        if ($c[$field] !== '' && !array_key_exists($c[$field], $opt[$set])) {
            $errors[$field] = 'Please choose one of the listed options.';
        }
    }

    if (($in['consent'] ?? '') !== '1') {
        $errors['consent'] = 'Please confirm so we can process your request.';
    }

    foreach (['architecture', 'notes', 'company'] as $field) {
        if (looks_like_secret($c[$field])) {
            $errors[$field] = 'This looks like it may contain a password, key or token. Please remove it — we will agree a secure channel for access later.';
        }
    }

    // Crude link-spam check.
    if (preg_match_all('#https?://#i', $c['notes'] . ' ' . $c['architecture']) > 5) {
        $errors['notes'] = 'Please reduce the number of links.';
    }

    return [$c, $errors];
}

function looks_like_secret(string $text): bool
{
    if ($text === '') {
        return false;
    }
    $patterns = [
        '/-----BEGIN [A-Z ]*PRIVATE KEY-----/',
        '/\bAKIA[0-9A-Z]{16}\b/',                         // AWS access key id
        '/\b(?:ghp|gho|ghs|github_pat)_[A-Za-z0-9_]{20,}/', // GitHub tokens
        '/\bxox[baprs]-[A-Za-z0-9-]{10,}/',                 // Slack tokens
        '/\bAIza[0-9A-Za-z_\-]{35}\b/',                     // Google API key
        '/\b(?:password|passwd|pwd|secret|api[_-]?key|token)\s*[:=]\s*(?=\S*[\d!@#$%^&*])\S{8,}/i',
    ];
    foreach ($patterns as $p) {
        if (preg_match($p, $text)) {
            return true;
        }
    }
    return false;
}

/** File-based fixed-window rate limiter keyed by a salted hash of the client IP. */
function rate_limit_allow(string $bucket, int $max, int $window): bool
{
    $dir = rtrim((string) config('storage_path'), '/') . '/ratelimit';
    if (!is_dir($dir) && !@mkdir($dir, 0700, true) && !is_dir($dir)) {
        error_log('[managed-graylog] rate limit dir not writable; allowing request');
        return true;
    }
    $key  = hash_hmac('sha256', $bucket . '|' . client_ip(), app_secret());
    $file = $dir . '/' . substr($key, 0, 40) . '.json';
    $fh = @fopen($file, 'c+');
    if (!$fh) {
        return true;
    }
    flock($fh, LOCK_EX);
    $raw  = stream_get_contents($fh);
    $data = json_decode($raw ?: '[]', true) ?: [];
    $now  = time();
    $hits = array_values(array_filter($data['hits'] ?? [], static fn($t): bool => is_int($t) && $t > $now - $window));
    $allowed = count($hits) < $max;
    if ($allowed) {
        $hits[] = $now;
    }
    ftruncate($fh, 0);
    rewind($fh);
    fwrite($fh, json_encode(['hits' => $hits]));
    fflush($fh);
    flock($fh, LOCK_UN);
    fclose($fh);

    // Opportunistic cleanup of stale buckets (1% of requests).
    if (random_int(1, 100) === 1) {
        foreach (glob($dir . '/*.json') ?: [] as $f) {
            if (filemtime($f) < $now - 2 * $window) {
                @unlink($f);
            }
        }
    }
    return $allowed;
}

/** Deliver the request: store a copy (optional) and send email. Extend here for CRM/webhooks. */
function dispatch_request(array $c): bool
{
    $opt = data('form');
    $label = static fn(string $set, string $v): string => $v === '' ? '—' : ($opt[$set][$v] ?? $v);

    $record = [
        'id'              => bin2hex(random_bytes(8)),
        'received_at'     => gmdate('c'),
        'name'            => $c['name'],
        'company'         => $c['company'],
        'email'           => $c['email'],
        'environment'     => $label('environment', $c['environment']),
        'graylog_version' => $c['graylog_version'] ?: '—',
        'architecture'    => $c['architecture'] ?: '—',
        'volume'          => $label('volume', $c['volume']),
        'servers'         => $label('servers', $c['servers']),
        'retention'       => $label('retention', $c['retention']),
        'infrastructure'  => $label('infrastructure', $c['infrastructure']),
        'ha'              => $label('yesno', $c['ha']),
        'monitoring'      => $label('yesno', $c['monitoring']),
        'backup'          => $label('yesno', $c['backup']),
        'notes'           => $c['notes'] ?: '—',
    ];

    $stored = false;
    if (config('form.store_submissions')) {
        $dir = rtrim((string) config('storage_path'), '/') . '/submissions';
        if (is_dir($dir) || @mkdir($dir, 0700, true)) {
            $stored = @file_put_contents($dir . '/' . gmdate('Ymd-His') . '-' . $record['id'] . '.json', json_encode($record, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), LOCK_EX) !== false;
        }
    }

    if (config('mail.transport') === 'log') {
        return $stored;
    }
    $sent = send_request_mail($record);
    return $sent || $stored;
}

function send_request_mail(array $r): bool
{
    $to = (string) config('mail.to');
    $from = (string) config('mail.from');
    if (!filter_var($to, FILTER_VALIDATE_EMAIL) || !filter_var($from, FILTER_VALIDATE_EMAIL)) {
        error_log('[managed-graylog] mail.to / mail.from not configured');
        return false;
    }

    // Subject contains no raw user input beyond a sanitised, length-limited name.
    $safeName = preg_replace('/[^\p{L}\p{N} .\'-]/u', '', $r['name']) ?? '';
    $subject  = config('mail.subject_prefix') . 'Deployment request — ' . mb_substr($safeName, 0, 60);
    $encodedSubject = '=?UTF-8?B?' . base64_encode($subject) . '?=';

    $lines = [];
    foreach ($r as $k => $v) {
        if (in_array($k, ['architecture', 'notes'], true)) {
            continue;
        }
        $lines[] = str_pad(str_replace('_', ' ', $k) . ':', 18) . $v;
    }
    $body = "New deployment request via managed-graylog.com\n\n" . implode("\n", $lines)
        . "\n\n--- Current architecture ---\n" . $r['architecture']
        . "\n\n--- Additional requirements ---\n" . $r['notes'] . "\n";

    $headers = [
        'From: Managed Graylog <' . $from . '>',
        'Reply-To: ' . $r['email'], // validated with FILTER_VALIDATE_EMAIL and newline-free
        'MIME-Version: 1.0',
        'Content-Type: text/plain; charset=UTF-8',
        'Content-Transfer-Encoding: 8bit',
        'X-Mailer: managed-graylog-web',
    ];

    return @mail($to, $encodedSubject, $body, implode("\r\n", $headers), '-f' . $from);
}

/**
 * Send JSON (fetch) or redirect with flash data (no-JS). Never echoes raw input back unescaped:
 * templates escape everything they render.
 */
function respond_contact(bool $ok, array $errors, ?string $status, array $input = [], int $code = 200): never
{
    if (wants_json()) {
        http_response_code($ok ? 200 : $code);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'ok'      => $ok,
            'errors'  => $errors,
            'message' => $ok ? 'An engineer will review your details and reply by email.' : ($errors['_form'] ?? 'Please check the highlighted fields.'),
            'token'   => $ok ? csrf_token() : null,
        ]);
        exit;
    }

    if ($ok) {
        flash('form_status', $status);
    } else {
        $keep = array_intersect_key($input, array_flip(['name', 'company', 'email', 'environment', 'graylog_version', 'architecture', 'volume', 'servers', 'retention', 'infrastructure', 'ha', 'monitoring', 'backup', 'notes', 'consent']));
        flash('form_old', array_map(static fn($v) => is_string($v) ? mb_substr($v, 0, 4000) : '', $keep));
        flash('form_errors', $errors ?: ['_form' => 'Please check the form.']);
    }
    $back = (string) ($_SERVER['HTTP_REFERER'] ?? '');
    $target = (parse_url($back, PHP_URL_HOST) === ($_SERVER['HTTP_HOST'] ?? null) && parse_url($back, PHP_URL_PATH) === '/') ? '/#request' : '/contact#request';
    redirect($target);
}
