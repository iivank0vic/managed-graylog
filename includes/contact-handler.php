<?php
/**
 * Contact form handlers.
 *
 *   POST /contact          deployment request (assessment form)  → handle_contact_submission()
 *   POST /contact/message  short general message                 → handle_message_submission()
 *
 * Layers of protection (shared by both forms, see contact_guard()):
 *  - CSRF token (session-bound) and SameSite=Lax session cookie
 *  - Honeypot field + signed time-trap token (min fill time / max age)
 *  - Per-IP rate limiting (hashed IP, file-based, flock), separate bucket per form
 *  - Strict server-side validation, whitelisted option values, length limits
 *  - Rejection of submissions that look like they contain credentials
 *  - Header-injection-safe mail (see includes/mailer.php)
 *  - All output escaped in templates; JSON responses for fetch clients
 *
 * Delivery goes through send_mail() (Mailgun API, PHP mail() or log — see config 'mail.transport').
 * Add a CRM/webhook push in dispatch_request() / dispatch_message() without touching validation.
 */
declare(strict_types=1);

require_once __DIR__ . '/mailer.php';

/** Per-form settings: flash keys, fields kept on error, redirect anchors, success message. */
function contact_form_meta(string $form): array
{
    return match ($form) {
        'message' => [
            'flash'   => 'msg',
            'keep'    => ['name', 'email', 'company', 'topic', 'message', 'consent'],
            'anchor'  => '#message',
            'ok'      => 'Thanks — your message is on its way. We usually reply within one business day.',
        ],
        default => [
            'flash'   => 'form',
            'keep'    => ['name', 'company', 'email', 'environment', 'graylog_version', 'architecture', 'volume', 'servers', 'retention', 'infrastructure', 'ha', 'monitoring', 'backup', 'notes', 'consent'],
            'anchor'  => '#request',
            'ok'      => 'An engineer will review your details and reply by email.',
        ],
    };
}

/**
 * Checks shared by every form: honeypot, CSRF, time trap. Ends the request on failure
 * (bots get a fake success so they learn nothing).
 */
function contact_guard(string $form, array $input): void
{
    header('Cache-Control: no-store');

    if (trim((string) ($input['website'] ?? '')) !== '') {
        respond_contact($form, true, [], 'sent');
    }
    if (!csrf_verify(is_string($input['_token'] ?? null) ? $input['_token'] : null)) {
        respond_contact($form, false, ['_form' => 'Your session expired. Please reload the page and submit again.'], null, $input, 419);
    }
    $age = form_timestamp_age(is_string($input['_ts'] ?? null) ? $input['_ts'] : null);
    if ($age === null || $age > (int) config('form.max_age_seconds')) {
        respond_contact($form, false, ['_form' => 'The form expired. Please reload the page and submit again.'], null, $input, 400);
    }
    if ($age < (int) config('form.min_fill_seconds')) {
        respond_contact($form, true, [], 'sent'); // too fast for a human: treat as bot, pretend success
    }
}

/** Rate limit (valid submissions only, so users fixing typos are not locked out), then deliver. */
function contact_finish(string $form, array $input, callable $dispatch, array $clean): never
{
    if (!rate_limit_allow($form, (int) config('form.rate_limit_max'), (int) config('form.rate_limit_window'))) {
        respond_contact($form, false, ['_form' => 'Too many requests from your network. Please try again later or email us directly.'], null, $input, 429);
    }
    if (!$dispatch($clean)) {
        respond_contact($form, false, ['_form' => 'We could not send your message right now. Please email ' . config('contact_email') . ' instead.'], null, $input, 500);
    }
    unset($_SESSION['csrf']); // rotate after a successful submission
    respond_contact($form, true, [], 'sent');
}

function handle_contact_submission(): void
{
    $input = $_POST;
    contact_guard('request', $input);
    [$clean, $errors] = validate_request($input);
    if ($errors) {
        respond_contact('request', false, $errors, null, $input, 422);
    }
    contact_finish('request', $input, 'dispatch_request', $clean);
}

function handle_message_submission(): void
{
    $input = $_POST;
    contact_guard('message', $input);
    [$clean, $errors] = validate_message($input);
    if ($errors) {
        respond_contact('message', false, $errors, null, $input, 422);
    }
    contact_finish('message', $input, 'dispatch_message', $clean);
}

/** Normalise a text input: strings only, strip control chars (keep newlines/tabs), trim, limit. */
function clean_input(array $in, string $key, int $max): string
{
    $v = $in[$key] ?? '';
    if (!is_string($v)) {
        return '';
    }
    $v = preg_replace('/[^\P{C}\n\t]/u', '', $v) ?? '';
    return mb_substr(trim($v), 0, $max);
}

/** Validation shared by both forms: name, email, company, consent. */
function validate_identity(array $c, array $in): array
{
    $errors = [];
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
    if (($in['consent'] ?? '') !== '1') {
        $errors['consent'] = 'Please confirm so we can process your request.';
    }
    return $errors;
}

/** @return array{0: array<string,string>, 1: array<string,string>} */
function validate_message(array $in): array
{
    $opt = data('form');
    $c = [
        'name'    => clean_input($in, 'name', 100),
        'email'   => clean_input($in, 'email', 190),
        'company' => clean_input($in, 'company', 120),
        'topic'   => clean_input($in, 'topic', 40),
        'message' => clean_input($in, 'message', 4000),
    ];
    $errors = validate_identity($c, $in);

    if ($c['topic'] === '' || !array_key_exists($c['topic'], $opt['topic'])) {
        $errors['topic'] = 'Please choose a topic.';
    }
    if (mb_strlen($c['message']) < 10) {
        $errors['message'] = 'Please write a short message (at least 10 characters).';
    } elseif (looks_like_secret($c['message'])) {
        $errors['message'] = 'This looks like it may contain a password, key or token. Please remove it — we will agree a secure channel for access later.';
    } elseif (preg_match_all('#https?://#i', $c['message']) > 3) {
        $errors['message'] = 'Please reduce the number of links.';
    }
    return [$c, $errors];
}

/** @return array{0: array<string,string>, 1: array<string,string>} */
function validate_request(array $in): array
{
    $opt = data('form');
    $str = static fn(string $key, int $max): string => clean_input($in, $key, $max);

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

    $errors = validate_identity($c, $in);
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

/** Write a JSON copy of a submission to storage/submissions when enabled. */
function store_submission(array $record): bool
{
    if (!config('form.store_submissions')) {
        return false;
    }
    $dir = rtrim((string) config('storage_path'), '/') . '/submissions';
    if (!is_dir($dir) && !@mkdir($dir, 0700, true) && !is_dir($dir)) {
        error_log('[managed-graylog] submissions dir not writable');
        return false;
    }
    $file = $dir . '/' . gmdate('Ymd-His') . '-' . $record['form'] . '-' . $record['id'] . '.json';
    return @file_put_contents($file, json_encode($record, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), LOCK_EX) !== false;
}

/** Name reduced to safe characters for use in a subject line. */
function subject_name(string $name): string
{
    return mb_substr(preg_replace('/[^\p{L}\p{N} .\'-]/u', '', $name) ?? '', 0, 60);
}

/**
 * Optional confirmation mail to the sender (config 'mail.autoreply').
 * Contains no user-supplied text except the sanitised first name, so it cannot be abused to relay content.
 */
function send_autoreply(string $email, string $name, string $what): void
{
    if (!config('mail.autoreply')) {
        return;
    }
    $first = strtok(subject_name($name), ' ') ?: 'there';
    $text = "Hi {$first},\n\n"
        . "thanks for contacting " . config('site_name') . ". We have received your {$what} and an engineer will reply personally, "
        . "usually within one business day.\n\n"
        . "Please do not send passwords, keys or other credentials by email — we will agree a secure channel if access is needed.\n\n"
        . "— " . config('site_name') . "\n" . config('base_url') . "\n\n"
        . "You are receiving this because this address was entered in the contact form on " . config('domain') . ". "
        . "If that was not you, you can ignore this message.\n";
    send_mail([
        'to'       => $email,
        'subject'  => 'We received your ' . $what . ' — ' . config('site_name'),
        'text'     => $text,
        'reply_to' => (string) config('contact_email'),
        'tag'      => 'autoreply',
    ]);
}

/** Deliver the deployment request: store a copy (optional) and send email. Extend here for CRM/webhooks. */
function dispatch_request(array $c): bool
{
    $opt = data('form');
    $label = static fn(string $set, string $v): string => $v === '' ? '—' : ($opt[$set][$v] ?? $v);

    $record = [
        'form'            => 'request',
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

    $stored = store_submission($record);

    $lines = [];
    foreach ($record as $k => $v) {
        if (in_array($k, ['form', 'architecture', 'notes'], true)) {
            continue;
        }
        $lines[] = str_pad(str_replace('_', ' ', $k) . ':', 18) . $v;
    }
    $body = 'New deployment request via ' . config('domain') . "\n\n" . implode("\n", $lines)
        . "\n\n--- Current architecture ---\n" . $record['architecture']
        . "\n\n--- Additional requirements ---\n" . $record['notes'] . "\n";

    $sent = send_mail([
        'to'       => (string) config('mail.to'),
        'subject'  => config('mail.subject_prefix') . 'Deployment request — ' . subject_name($c['name']),
        'text'     => $body,
        'reply_to' => $c['email'],
        'tag'      => 'deployment-request',
    ]);
    if ($sent) {
        send_autoreply($c['email'], $c['name'], 'deployment request');
    }
    return $sent || $stored;
}

/** Deliver a short general message. */
function dispatch_message(array $c): bool
{
    $topics = data('form')['topic'];
    $record = [
        'form'        => 'message',
        'id'          => bin2hex(random_bytes(8)),
        'received_at' => gmdate('c'),
        'name'        => $c['name'],
        'email'       => $c['email'],
        'company'     => $c['company'] ?: '—',
        'topic'       => $topics[$c['topic']] ?? $c['topic'],
        'message'     => $c['message'],
    ];

    $stored = store_submission($record);

    $body = 'New message via ' . config('domain') . "\n\n"
        . str_pad('name:', 14) . $record['name'] . "\n"
        . str_pad('email:', 14) . $record['email'] . "\n"
        . str_pad('company:', 14) . $record['company'] . "\n"
        . str_pad('topic:', 14) . $record['topic'] . "\n"
        . str_pad('received at:', 14) . $record['received_at'] . "\n"
        . str_pad('id:', 14) . $record['id'] . "\n\n"
        . "--- Message ---\n" . $record['message'] . "\n";

    $sent = send_mail([
        'to'       => (string) (config('mail.to_messages') ?: config('mail.to')),
        'subject'  => config('mail.subject_prefix') . $record['topic'] . ' — ' . subject_name($c['name']),
        'text'     => $body,
        'reply_to' => $c['email'],
        'tag'      => 'contact-message',
    ]);
    if ($sent) {
        send_autoreply($c['email'], $c['name'], 'message');
    }
    return $sent || $stored;
}

/**
 * Send JSON (fetch) or redirect with flash data (no-JS). Never echoes raw input back unescaped:
 * templates escape everything they render.
 */
function respond_contact(string $form, bool $ok, array $errors, ?string $status, array $input = [], int $code = 200): never
{
    $meta = contact_form_meta($form);

    if (wants_json()) {
        http_response_code($ok ? 200 : $code);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'ok'      => $ok,
            'errors'  => $errors,
            'message' => $ok ? $meta['ok'] : ($errors['_form'] ?? 'Please check the highlighted fields.'),
            'token'   => $ok ? csrf_token() : null,
        ]);
        exit;
    }

    $f = $meta['flash'];
    if ($ok) {
        flash($f . '_status', $status);
    } else {
        $keep = array_intersect_key($input, array_flip($meta['keep']));
        flash($f . '_old', array_map(static fn($v) => is_string($v) ? mb_substr($v, 0, 4000) : '', $keep));
        flash($f . '_errors', $errors ?: ['_form' => 'Please check the form.']);
    }
    $back = (string) ($_SERVER['HTTP_REFERER'] ?? '');
    $fromHome = parse_url($back, PHP_URL_HOST) === ($_SERVER['HTTP_HOST'] ?? null) && parse_url($back, PHP_URL_PATH) === '/';
    redirect(($fromHome && $form === 'request' ? '/' : '/contact') . $meta['anchor']);
}
