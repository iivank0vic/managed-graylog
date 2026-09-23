<?php
/**
 * Deployment assessment form. Works without JavaScript (POST + redirect),
 * enhanced with client-side validation, live spec preview and fetch submission.
 */
declare(strict_types=1);

$opt    = data('form');
$old    = flash('form_old') ?? [];
$errors = flash('form_errors') ?? [];
$status = flash('form_status');
$val    = static fn(string $k): string => is_string($old[$k] ?? null) ? $old[$k] : '';
$err    = static function (string $k) use ($errors): string {
    return isset($errors[$k]) ? '<p class="field__error" id="err-' . e($k) . '">' . e($errors[$k]) . '</p>' : '<p class="field__error" id="err-' . e($k) . '" hidden></p>';
};
$inv    = static fn(string $k): string => isset($errors[$k]) ? ' aria-invalid="true"' : '';
$select = static function (string $name, array $options, string $placeholder) use ($val, $inv): string {
    $html = '<select class="input" id="f-' . e($name) . '" name="' . e($name) . '" aria-describedby="err-' . e($name) . '"' . $inv($name) . '>';
    $html .= '<option value="">' . e($placeholder) . '</option>';
    foreach ($options as $k => $label) {
        $html .= '<option value="' . e($k) . '"' . ($val($name) === (string) $k ? ' selected' : '') . '>' . e($label) . '</option>';
    }
    return $html . '</select>';
};
?>
<div class="assess" data-assess>
  <?php if ($status === 'sent'): ?>
    <div class="form-result form-result--ok" role="status" tabindex="-1" data-form-result>
      <?= icon('circle-check') ?>
      <div><strong>Request received.</strong> An engineer will review your environment details and reply by email.</div>
    </div>
  <?php elseif ($errors): ?>
    <div class="form-result form-result--err" role="alert" tabindex="-1" data-form-result>
      <?= icon('circle-alert') ?>
      <div><strong>Please check the highlighted fields.</strong> <?= e($errors['_form'] ?? '') ?></div>
    </div>
  <?php endif; ?>

  <form class="assess__form" action="<?= e(url('/contact')) ?>#request" method="post" novalidate data-request-form>
    <input type="hidden" name="_token" value="<?= e(csrf_token()) ?>">
    <input type="hidden" name="_ts" value="<?= e(form_timestamp_token()) ?>">
    <div class="hp" aria-hidden="true">
      <label for="f-website">Leave this field empty</label>
      <input type="text" id="f-website" name="website" tabindex="-1" autocomplete="off">
    </div>

    <fieldset class="fs">
      <legend class="fs__legend"><span class="mono">01</span> Contact</legend>
      <div class="fs__grid">
        <div class="field">
          <label for="f-name">Name <span class="req" aria-hidden="true">*</span></label>
          <input class="input" type="text" id="f-name" name="name" autocomplete="name" required maxlength="100" value="<?= e($val('name')) ?>" aria-describedby="err-name"<?= $inv('name') ?>>
          <?= $err('name') ?>
        </div>
        <div class="field">
          <label for="f-company">Company</label>
          <input class="input" type="text" id="f-company" name="company" autocomplete="organization" maxlength="120" value="<?= e($val('company')) ?>" aria-describedby="err-company"<?= $inv('company') ?>>
          <?= $err('company') ?>
        </div>
        <div class="field field--full">
          <label for="f-email">Work email <span class="req" aria-hidden="true">*</span></label>
          <input class="input" type="email" id="f-email" name="email" autocomplete="email" required maxlength="190" value="<?= e($val('email')) ?>" aria-describedby="err-email"<?= $inv('email') ?>>
          <?= $err('email') ?>
        </div>
      </div>
    </fieldset>

    <fieldset class="fs">
      <legend class="fs__legend"><span class="mono">02</span> Current environment</legend>
      <div class="fs__grid">
        <div class="field">
          <label for="f-environment">What do you run today?</label>
          <?= $select('environment', $opt['environment'], 'Select…') ?>
          <?= $err('environment') ?>
        </div>
        <div class="field">
          <label for="f-graylog_version">Current Graylog version</label>
          <input class="input mono" type="text" id="f-graylog_version" name="graylog_version" maxlength="40" placeholder="e.g. 6.1, or none" value="<?= e($val('graylog_version')) ?>" aria-describedby="err-graylog_version"<?= $inv('graylog_version') ?>>
          <?= $err('graylog_version') ?>
        </div>
        <div class="field field--full">
          <label for="f-architecture">Current architecture <span class="hint">— nodes, search backend, where it runs</span></label>
          <textarea class="input" id="f-architecture" name="architecture" rows="3" maxlength="2000" placeholder="e.g. 1 VM with Graylog + OpenSearch + MongoDB on 500 GB disk, running at 80%" aria-describedby="err-architecture"<?= $inv('architecture') ?>><?= e($val('architecture')) ?></textarea>
          <?= $err('architecture') ?>
        </div>
      </div>
    </fieldset>

    <fieldset class="fs">
      <legend class="fs__legend"><span class="mono">03</span> Workload</legend>
      <div class="fs__grid">
        <div class="field">
          <label for="f-volume">Expected log volume</label>
          <?= $select('volume', $opt['volume'], 'Select…') ?>
          <?= $err('volume') ?>
        </div>
        <div class="field">
          <label for="f-servers">Number of servers / log sources</label>
          <?= $select('servers', $opt['servers'], 'Select…') ?>
          <?= $err('servers') ?>
        </div>
        <div class="field">
          <label for="f-retention">Retention requirement</label>
          <?= $select('retention', $opt['retention'], 'Select…') ?>
          <?= $err('retention') ?>
        </div>
      </div>
    </fieldset>

    <fieldset class="fs">
      <legend class="fs__legend"><span class="mono">04</span> Infrastructure &amp; requirements</legend>
      <div class="field">
        <p class="field__label" id="lbl-infra">Preferred infrastructure</p>
        <div class="chips" role="radiogroup" aria-labelledby="lbl-infra">
          <?php foreach ($opt['infrastructure'] as $k => $label): ?>
            <label class="chip">
              <input type="radio" name="infrastructure" value="<?= e($k) ?>"<?= $val('infrastructure') === $k ? ' checked' : '' ?>>
              <span><?= e($label) ?></span>
            </label>
          <?php endforeach; ?>
        </div>
        <?= $err('infrastructure') ?>
      </div>

      <div class="toggles">
        <?php foreach (['ha' => 'High availability required?', 'monitoring' => 'Monitoring required?', 'backup' => 'Backup required?'] as $name => $label): ?>
          <div class="toggle-row">
            <p class="toggle-row__label" id="lbl-<?= e($name) ?>"><?= e($label) ?></p>
            <div class="seg" role="radiogroup" aria-labelledby="lbl-<?= e($name) ?>">
              <?php foreach ($opt['yesno'] as $k => $lab): ?>
                <label class="seg__opt">
                  <input type="radio" name="<?= e($name) ?>" value="<?= e($k) ?>"<?= $val($name) === $k ? ' checked' : '' ?>>
                  <span><?= e($lab) ?></span>
                </label>
              <?php endforeach; ?>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </fieldset>

    <fieldset class="fs">
      <legend class="fs__legend"><span class="mono">05</span> Anything else</legend>
      <div class="field">
        <label for="f-notes">Additional requirements</label>
        <textarea class="input" id="f-notes" name="notes" rows="4" maxlength="4000" placeholder="Timelines, compliance context, integrations, what is currently broken…" aria-describedby="err-notes notes-warning"<?= $inv('notes') ?>><?= e($val('notes')) ?></textarea>
        <?= $err('notes') ?>
        <p class="field__warn" id="notes-warning"><?= icon('key-round') ?> Please do not include passwords, API keys, private keys or cloud credentials. We will agree a secure way to share access later.</p>
      </div>

      <div class="field field--check">
        <input type="checkbox" id="f-consent" name="consent" value="1" required<?= $val('consent') === '1' ? ' checked' : '' ?> aria-describedby="err-consent"<?= $inv('consent') ?>>
        <label for="f-consent">I agree that the details above are processed to answer my request, as described in the <a href="<?= e(url('/privacy')) ?>">privacy policy</a>. <span class="req" aria-hidden="true">*</span></label>
        <?= $err('consent') ?>
      </div>
    </fieldset>

    <div class="assess__submit">
      <button class="btn-x btn-x--primary btn-x--lg" type="submit" data-submit>
        <span data-submit-label>Get My Deployment Plan</span> <?= icon('arrow-right') ?>
      </button>
      <p class="assess__note">An engineer reads every request. No automated sales sequence.</p>
    </div>
  </form>

  <aside class="assess__aside" aria-label="Request summary">
    <div class="spec">
      <div class="spec__chrome mono"><span class="cp__dots" aria-hidden="true"><i></i><i></i><i></i></span> deployment-request.yaml</div>
      <pre class="spec__body mono" data-spec-preview aria-hidden="true"><code><span class="k">request</span>:
  <span class="k">environment</span>: <span class="v">~</span>
  <span class="k">volume</span>: <span class="v">~</span>
  <span class="k">retention</span>: <span class="v">~</span>
  <span class="k">infrastructure</span>: <span class="v">~</span>
  <span class="k">ha</span>: <span class="v">~</span></code></pre>
    </div>
    <div class="aside-card">
      <h3 class="aside-card__title">What happens next</h3>
      <ol class="aside-steps">
        <li>An engineer reviews your details.</li>
        <li>We reply with questions or a first architecture outline.</li>
        <li>A short call to confirm scope, then a written proposal.</li>
      </ol>
    </div>
    <div class="aside-card aside-card--muted">
      <p><?= icon('info') ?> Rough numbers are fine. If you don't know your log volume yet, that is part of what we help you work out.</p>
    </div>
  </aside>
</div>
