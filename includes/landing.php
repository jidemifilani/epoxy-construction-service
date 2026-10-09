<?php
/**
 * Landing pages for paid advertising and for search queries that need a single,
 * focused answer. Each page targets one intent: one headline, one offer, one form.
 *
 * A page calls landing_render() with its content. The form records the campaign
 * the visitor came from (see includes/tracking.php) and the page slug, so every
 * enquiry shows which page and which advert produced it.
 */

declare(strict_types=1);

require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/leads.php';

/**
 * $p keys: slug, title, description, keywords, h1, lede, benefits[], steps[],
 *          faqs[[q,a]], form_title, space_options[], cta_label, noindex (optional),
 *          eyebrow, body (optional HTML shown after the benefits).
 */
function landing_render(array $p): void
{
    $slug = $p['slug'];

    // The form handler: validate, store the lead, then redirect to thank-you.
    [$old, $errors] = handle_submission('landing-' . $slug, [
        'name'       => 'required|max:120',
        'phone'      => 'required|phone|max:40',
        'email'      => 'email|max:160',
        'space_type' => 'required|max:60',
        'size'       => 'max:40',
        'message'    => 'max:1000',
    ]);

    $pageTitle       = $p['title'];
    $pageDescription = $p['description'];
    $pageKeywords    = $p['keywords'];
    $noindex         = !empty($p['noindex']);
    $seoType         = 'website';

    $bannerEyebrow = $p['eyebrow'];
    $bannerTitle   = $p['h1'];
    $bannerLede    = $p['lede'];
    $bannerCtas    = button('Get My Free Quote', '#quote', 'primary')
                   . button('WhatsApp a Photo', whatsapp_link('Hello, I would like a quote for epoxy flooring (' . $slug . ').'), 'ghost', ['target' => '_blank', 'rel' => 'noopener']);
    $trail         = [$p['trail'] => null];

    $schemaNodes = array_merge([schema_faq($p['faqs'])], $p['schema'] ?? []);
    $canonicalPath = $p['canonical'] ?? null;

    require __DIR__ . '/header.php';
    ?>

    <section class="sec">
      <div class="wrap split split--wide" style="align-items:start">
        <div>
          <?php section_head($p['eyebrow'], $p['section_title'], '', 'left'); ?>
          <?php if (!empty($p['body'])): ?>
            <div class="prose reveal"><?= $p['body'] ?></div>
          <?php endif; ?>
          <?php
          tick_list($p['benefits']);
          ?>
        </div>

        <div id="quote" class="form-card reveal" style="scroll-margin-top:110px">
          <h2 style="font-size:1.4rem"><?= e($p['form_title']) ?></h2>
          <p class="field__hint" style="margin-bottom:1rem">Free site inspection. Fixed-price written quote within 48 hours.</p>

          <?php if (!empty($errors['_form'])): ?>
          <p class="flash flash--error" role="alert"><?= e($errors['_form']) ?></p>
          <?php endif; ?>

          <form method="post" action="<?= e(url($slug)) ?>#quote" novalidate class="form">
            <?= csrf_field() ?>
            <div class="honeypot" aria-hidden="true">
              <label for="website">Website</label>
              <input type="text" id="website" name="website" tabindex="-1" autocomplete="off">
            </div>

            <div class="field">
              <label for="name">Your name <span class="req">*</span></label>
              <input type="text" id="name" name="name" required autocomplete="name" value="<?= e(old($old, 'name')) ?>">
              <?= field_error($errors, 'name') ?>
            </div>
            <div class="field">
              <label for="phone">Phone / WhatsApp <span class="req">*</span></label>
              <input type="tel" id="phone" name="phone" required autocomplete="tel" value="<?= e(old($old, 'phone')) ?>">
              <?= field_error($errors, 'phone') ?>
            </div>
            <div class="field">
              <label for="email">Email <span class="field__hint">(optional)</span></label>
              <input type="email" id="email" name="email" autocomplete="email" value="<?= e(old($old, 'email')) ?>">
              <?= field_error($errors, 'email') ?>
            </div>
            <div class="form__row form__row--2">
              <div class="field">
                <label for="space_type"><?= e($p['space_label'] ?? 'Type of space') ?> <span class="req">*</span></label>
                <select id="space_type" name="space_type" required>
                  <option value="">Choose one</option>
                  <?php foreach ($p['space_options'] as $option): ?>
                  <option value="<?= e($option) ?>"<?= was_chosen($old, 'space_type', $option) ? ' selected' : '' ?>><?= e($option) ?></option>
                  <?php endforeach; ?>
                </select>
                <?= field_error($errors, 'space_type') ?>
              </div>
              <div class="field">
                <label for="size">Approximate size (m&sup2;)</label>
                <input type="text" id="size" name="size" value="<?= e(old($old, 'size')) ?>">
              </div>
            </div>
            <div class="field">
              <label for="message">Anything we should know?</label>
              <textarea id="message" name="message"><?= e(old($old, 'message')) ?></textarea>
            </div>
            <button class="btn btn--primary btn--lg" type="submit" style="justify-content:center"><span><?= e($p['cta_label']) ?></span></button>
            <p class="field__hint">We reply within one business day. We never share your details.</p>
          </form>
        </div>
      </div>
    </section>

    <section class="sec sec--paper">
      <div class="wrap">
        <?php section_head('How it works', 'From Enquiry to Finished Floor.'); ?>
        <?php step_list($p['steps'], true); ?>
      </div>
    </section>

    <section class="sec">
      <div class="wrap" style="max-width:860px">
        <?php section_head('Questions', 'Answered Before You Ask.'); ?>
        <?php accordion($p['faqs']); ?>
      </div>
    </section>

    <?php cta_band('Ready to Price Your Floor?', 'Tell us the space and the size. We will come and measure, then send a fixed price in writing.', 'Get My Free Quote', '#quote'); ?>
    <?php
    require __DIR__ . '/footer.php';
}
