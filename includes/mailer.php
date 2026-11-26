<?php
/**
 * Outgoing mail: one entry point (send_mail) with pluggable transports.
 *
 *   mailgun  Mailgun HTTP API (recommended; no local MTA needed)
 *   mail     PHP mail() via the host's MTA
 *   log      do not send, only write a line to the PHP error log (staging / local dev)
 *   auto     mailgun when an API key + domain are configured, otherwise mail()
 *
 * Mailgun credentials come from includes/config.local.php (git-ignored, never deployed by CI)
 * or from the environment variables MAILGUN_API_KEY, MAILGUN_DOMAIN, MAILGUN_REGION.
 *
 * Messages never put raw user input into headers: callers pass a validated reply_to
 * and a sanitised subject; everything is additionally stripped of CR/LF here.
 */
declare(strict_types=1);

/**
 * @param array{
 *   to: string, subject: string, text: string,
 *   reply_to?: ?string, from?: ?string, from_name?: ?string, tag?: ?string
 * } $msg
 */
function send_mail(array $msg): bool
{
    $from     = (string) ($msg['from'] ?? config('mail.from'));
    $fromName = (string) ($msg['from_name'] ?? config('mail.from_name', config('site_name')));
    $to       = (string) $msg['to'];
    $replyTo  = isset($msg['reply_to']) ? (string) $msg['reply_to'] : '';

    foreach (['to' => $to, 'from' => $from] as $label => $addr) {
        if (!filter_var($addr, FILTER_VALIDATE_EMAIL)) {
            error_log('[managed-graylog] mail: invalid or missing "' . $label . '" address');
            return false;
        }
    }
    if ($replyTo !== '' && !filter_var($replyTo, FILTER_VALIDATE_EMAIL)) {
        $replyTo = '';
    }

    $clean = static fn(string $v): string => trim(str_replace(["\r", "\n", "\0"], ' ', $v));
    $mail = [
        'from'      => $clean($from),
        'from_name' => preg_replace('/[^\p{L}\p{N} .\'&-]/u', '', $clean($fromName)) ?: 'Website',
        'to'        => $clean($to),
        'reply_to'  => $clean($replyTo),
        'subject'   => mb_substr($clean((string) $msg['subject']), 0, 200),
        'text'      => str_replace("\r\n", "\n", (string) $msg['text']),
        'tag'       => isset($msg['tag']) ? preg_replace('/[^a-z0-9_-]/i', '', (string) $msg['tag']) : '',
    ];

    return match (mail_transport()) {
        'mailgun' => mail_via_mailgun($mail),
        'mail'    => mail_via_php($mail),
        default   => mail_via_log($mail),
    };
}

/** Resolve the effective transport ('auto' picks Mailgun when it is configured). */
function mail_transport(): string
{
    $t = (string) config('mail.transport', 'auto');
    if ($t === 'auto') {
        return mailgun_settings() !== null ? 'mailgun' : 'mail';
    }
    return in_array($t, ['mailgun', 'mail', 'log'], true) ? $t : 'log';
}

/** @return array{api_key:string, domain:string, endpoint:string, timeout:int}|null */
function mailgun_settings(): ?array
{
    $env = static function (string $k): ?string {
        $v = getenv($k);
        return is_string($v) && $v !== '' ? $v : null;
    };
    $key    = (string) (config('mailgun.api_key') ?: $env('MAILGUN_API_KEY') ?: '');
    $domain = (string) (config('mailgun.domain') ?: $env('MAILGUN_DOMAIN') ?: '');
    $region = strtolower((string) (config('mailgun.region') ?: $env('MAILGUN_REGION') ?: 'eu'));

    if ($key === '' || $domain === '' || str_starts_with($key, 'REPLACE')) {
        return null;
    }
    if (!preg_match('/^[a-z0-9.-]+\.[a-z]{2,}$/i', $domain)) {
        error_log('[managed-graylog] mailgun.domain looks invalid');
        return null;
    }

    $base = (string) (config('mailgun.endpoint') ?: ($region === 'us' ? 'https://api.mailgun.net' : 'https://api.eu.mailgun.net'));
    return [
        'api_key'  => $key,
        'domain'   => $domain,
        'endpoint' => rtrim($base, '/') . '/v3/' . rawurlencode($domain) . '/messages',
        'timeout'  => max(3, (int) config('mailgun.timeout', 10)),
    ];
}

function mail_via_mailgun(array $m): bool
{
    $s = mailgun_settings();
    if ($s === null) {
        error_log('[managed-graylog] mail transport is "mailgun" but mailgun.api_key / mailgun.domain are not set');
        return false;
    }

    $fields = [
        'from'         => $m['from_name'] . ' <' . $m['from'] . '>',
        'to'           => $m['to'],
        'subject'      => $m['subject'],
        'text'         => $m['text'],
        'o:tracking'   => 'no', // no click/open tracking on transactional mail
    ];
    if ($m['reply_to'] !== '') {
        $fields['h:Reply-To'] = $m['reply_to'];
    }
    if ($m['tag'] !== '') {
        $fields['o:tag'] = $m['tag'];
    }
    $body = http_build_query($fields, '', '&', PHP_QUERY_RFC3986);

    if (function_exists('curl_init')) {
        $ch = curl_init($s['endpoint']);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $body,
            CURLOPT_USERPWD        => 'api:' . $s['api_key'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => $s['timeout'],
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_HTTPHEADER     => ['Accept: application/json'],
            CURLOPT_PROTOCOLS      => CURLPROTO_HTTPS | CURLPROTO_HTTP,
        ]);
        $resp = curl_exec($ch);
        $code = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        $err  = curl_error($ch);
        curl_close($ch);
    } else {
        $ctx = stream_context_create(['http' => [
            'method'        => 'POST',
            'header'        => "Content-Type: application/x-www-form-urlencoded\r\nAccept: application/json\r\n"
                             . 'Authorization: Basic ' . base64_encode('api:' . $s['api_key']) . "\r\n",
            'content'       => $body,
            'timeout'       => $s['timeout'],
            'ignore_errors' => true,
        ]]);
        $resp = @file_get_contents($s['endpoint'], false, $ctx);
        $code = 0;
        foreach ($http_response_header ?? [] as $h) {
            if (preg_match('#^HTTP/\S+\s+(\d{3})#', $h, $mm)) {
                $code = (int) $mm[1];
            }
        }
        $err = $resp === false ? 'stream request failed' : '';
    }

    if ($code >= 200 && $code < 300) {
        return true;
    }
    // Log status and Mailgun's message only — never the API key or the mail body.
    $detail = is_string($resp) ? (json_decode($resp, true)['message'] ?? mb_substr($resp, 0, 200)) : '';
    error_log(sprintf('[managed-graylog] mailgun send failed: HTTP %d %s %s', $code, $err, is_string($detail) ? $detail : ''));
    return false;
}

function mail_via_php(array $m): bool
{
    $headers = [
        'From: ' . $m['from_name'] . ' <' . $m['from'] . '>',
        'MIME-Version: 1.0',
        'Content-Type: text/plain; charset=UTF-8',
        'Content-Transfer-Encoding: 8bit',
        'X-Mailer: managed-graylog-web',
    ];
    if ($m['reply_to'] !== '') {
        $headers[] = 'Reply-To: ' . $m['reply_to'];
    }
    $subject = '=?UTF-8?B?' . base64_encode($m['subject']) . '?=';
    return @mail($m['to'], $subject, $m['text'], implode("\r\n", $headers), '-f' . $m['from']);
}

function mail_via_log(array $m): bool
{
    error_log(sprintf('[managed-graylog] mail (log transport) to=%s subject="%s" bytes=%d', $m['to'], $m['subject'], strlen($m['text'])));
    return true;
}
