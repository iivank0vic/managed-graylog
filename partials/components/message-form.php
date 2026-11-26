<?php
/**
 * Quick message form (POST /contact/message). Works without JavaScript (POST + redirect),
 * enhanced with client-side validation and fetch submission (assets/js/main.js → [data-ajax-form]).
 */
declare(strict_types=1);

$opt    = data('form');
$old    = flash('msg_old') ?? [];
$errors = flash('msg_errors') ?? [];
$status = flash('msg_status');
$val    = static fn(string $k): string => is_string($old[$k] ?? null) ? $old[$k] : '';
$err    = static function (string $k) use ($errors): string {
    return '<p class="field__error" id="merr-' . e($k) . '"' . (isset($errors[$k]) ? '>' . e($errors[$k]) : ' hidden>') . '</p>';
};
$inv    = static fn(string $k): string => isset($errors[$k]) ? ' aria-invalid="true"' : '';
?>
<div class="msgbox" data-msgbox>
  <div class="msgbox__result" data-form-result-slot>
    <?php if ($status === 'sent'): ?>
      <div class="form-result form-result--ok" role="status" tabindex="-1" data-form-result>
        <?= icon('circle-check') ?>
        <div><strong>Message sent.</strong> We usually reply within one business day.</div>
      </div>
    <?php elseif ($errors): ?>
      <div class="form-result form-result--err" role="alert" tabindex="-1" data-form-result>
        <?= icon('circle-alert') ?>
        <div><strong>Please check the highlighted fields.</strong> <?= e($errors['_form'] ?? '') ?></div>
      </div>
    <?php endif; ?>
  </div>

  <form class="assess__form msgbox__form" action="<?= e(url('/contact/message')) ?>#message" method="post" novalidate
        data-ajax-form data-err-prefix="merr-" data-ok-title="Message sent.">
    <input type="hidden" name="_token" value="<?= e(csrf_token()) ?>">
    <input type="hidden" name="_ts" value="<?= e(form_timestamp_token()) ?>">
    <div class="hp" aria-hidden="true">
      <label for="m-website">Leave this field empty</label>
      <input type="text" id="m-website" name="website" tabindex="-1" autocomplete="off">
    </div>

    <div class="fs">
      <div class="fs__grid">
        <div class="field">
          <label for="m-name">Name <span class="req" aria-hidden="true">*</span></label>
          <input class="input" type="text" id="m-name" name="name" autocomplete="name" required maxlength="100" value="<?= e($val('name')) ?>" aria-describedby="merr-name"<?= $inv('name') ?>>
          <?= $err('name') ?>
        </div>
        <div class="field">
          <label for="m-email">Email <span class="req" aria-hidden="true">*</span></label>
          <input class="input" type="email" id="m-email" name="email" autocomplete="email" required maxlength="190" value="<?= e($val('email')) ?>" aria-describedby="merr-email"<?= $inv('email') ?>>
          <?= $err('email') ?>
        </div>
        <div class="field">
          <label for="m-company">Company <span class="hint">— optional</span></label>
          <input class="input" type="text" id="m-company" name="company" autocomplete="organization" maxlength="120" value="<?= e($val('company')) ?>" aria-describedby="merr-company"<?= $inv('company') ?>>
          <?= $err('company') ?>
        </div>
        <div class="field">
          <label for="m-topic">Topic <span class="req" aria-hidden="true">*</span></label>
          <select class="input" id="m-topic" name="topic" required aria-describedby="merr-topic"<?= $inv('topic') ?>>
            <option value="">Select…</option>
            <?php foreach ($opt['topic'] as $k => $label): ?>
              <option value="<?= e($k) ?>"<?= $val('topic') === (string) $k ? ' selected' : '' ?>><?= e($label) ?></option>
            <?php endforeach; ?>
          </select>
          <?= $err('topic') ?>
        </div>
        <div class="field field--full">
          <label for="m-message">Message <span class="req" aria-hidden="true">*</span></label>
          <textarea class="input" id="m-message" name="message" rows="5" required minlength="10" maxlength="4000" placeholder="How can we help?" aria-describedby="merr-message m-warning"<?= $inv('message') ?>><?= e($val('message')) ?></textarea>
          <?= $err('message') ?>
          <p class="field__warn" id="m-warning"><?= icon('key-round') ?> Please do not include passwords, API keys or other credentials.</p>
        </div>
      </div>

      <div class="field field--check">
        <input type="checkbox" id="m-consent" name="consent" value="1" required<?= $val('consent') === '1' ? ' checked' : '' ?> aria-describedby="merr-consent"<?= $inv('consent') ?>>
        <label for="m-consent">I agree that my details are processed to answer my message, as described in the <a href="<?= e(url('/privacy')) ?>">privacy policy</a>. <span class="req" aria-hidden="true">*</span></label>
        <?= $err('consent') ?>
      </div>
    </div>

    <div class="assess__submit">
      <button class="btn-x btn-x--primary" type="submit" data-submit>
        <span data-submit-label>Send message</span> <?= icon('arrow-right') ?>
      </button>
      <p class="assess__note">Or email <a href="mailto:<?= e(config('contact_email')) ?>"><?= e(config('contact_email')) ?></a></p>
    </div>
  </form>
</div>
