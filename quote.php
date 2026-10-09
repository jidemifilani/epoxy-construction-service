<?php
/** PAGE 15 - REQUEST A FREE QUOTE (lead-gen landing page) */

require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/leads.php';

[$old, $errors] = handle_submission('quote', [
    // Step 1 - about you
    'name'          => 'required|max:120',
    'phone'         => 'required|phone|max:40',
    'email'         => 'required|email|max:160',
    'you_are'       => 'required|in:Homeowner,Business,Architect,Contractor',
    // Step 2 - about the space
    'space_type'    => 'required|max:60',
    'size'          => 'max:40',
    'current_floor' => 'max:60',
    'known_issues'  => 'max:600',
    // Step 3 - about the finish
    'finish'        => 'max:60',
    'priority'      => 'max:60',
    // Step 4 - timing and location
    'location'      => 'required|max:160',
    'timeline'      => 'max:60',
]);

$pageTitle       = 'Free Epoxy Flooring Quote Lagos, Nigeria | No Obligation';
$pageDescription = 'Get a fixed-price written quote within 48 hours. On-site inspection free. No obligation. Serving all ' . cfg('service_area') . '.';
$pageKeywords    = 'free epoxy flooring quote Lagos, epoxy flooring quote Nigeria, epoxy floor installation Lagos, epoxy estimate Nigeria';

$bannerEyebrow = 'Free quote';
$bannerTitle   = 'Get a Fixed-Price Written Quote. In 48 Hours. No Obligation.';
$bannerLede    = 'Fill the form below or WhatsApp a photo of your floor. We will inspect, measure and send a line-itemed quote &mdash; in writing &mdash; with your system choice, warranty terms and start date.';
$trail         = ['Request a Quote' => null];

require __DIR__ . '/includes/header.php';
?>

<!-- ===== What happens after you submit ===== -->
<section class="sec sec--tight">
  <div class="wrap">
    <?php section_head('What happens after you submit', 'Four Steps, Then a Number.'); ?>
    <div class="grid grid--4">
      <?php
      $after = [
          ['clock',    'Within 2 hours',            'A project coordinator reaches out to confirm details.'],
          ['calendar', 'Within 48 hours',           'On-site inspection scheduled.'],
          ['doc',      'Within 48 hours of visit',  'Your written quote lands in your inbox.'],
          ['shield',   'Decision window',           'Your quote is locked for 30 days.'],
      ];
      foreach ($after as $index => $step) {
          echo '<article class="card reason-card reveal">';
          echo '<span class="reason-card__num">' . str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) . '</span>';
          echo '<span class="feature-card__icon">' . icon($step[0], 'icon icon--lg') . '</span>';
          echo '<h3>' . e($step[1]) . '</h3><p>' . e($step[2]) . '</p>';
          echo corner_frame() . '</article>';
      }
      ?>
    </div>
  </div>
</section>

<!-- ===== Multi-step quote form ===== -->
<section class="sec sec--paper">
  <div class="wrap split split--wide" style="align-items:start">

    <form class="form-card form reveal" method="post" action="<?= e(url('quote')) ?>#form" id="form" data-multistep novalidate>
      <?= csrf_field() ?>
      <div class="honeypot" aria-hidden="true">
        <label for="website">Website</label>
        <input type="text" id="website" name="website" tabindex="-1" autocomplete="off">
      </div>

      <?php if (!empty($errors['_form'])): ?>
      <p class="flash flash--error" role="alert"><?= e($errors['_form']) ?></p>
      <?php endif; ?>

      <?php if (!empty($errors) && empty($errors['_form'])): ?>
      <p class="flash flash--error" role="alert">Some details need a second look &mdash; they are marked below.</p>
      <?php endif; ?>

      <ol class="steps-nav" aria-hidden="true">
        <li class="steps-nav__item is-current">1. You</li>
        <li class="steps-nav__item">2. The space</li>
        <li class="steps-nav__item">3. The finish</li>
        <li class="steps-nav__item">4. Timing</li>
      </ol>

      <!-- Step 1 -->
      <fieldset class="fstep is-current">
        <legend style="font-family:var(--serif);font-size:1.3rem;color:var(--ink-900);margin-bottom:.6rem">Step 1 &mdash; About You</legend>

        <div class="field">
          <label for="name">Name <span class="req">*</span></label>
          <input type="text" id="name" name="name" required autocomplete="name"
                 value="<?= e(old($old, 'name')) ?>" <?= isset($errors['name']) ? 'aria-invalid="true"' : '' ?>>
          <?= field_error($errors, 'name') ?>
        </div>

        <div class="form__row form__row--2">
          <div class="field">
            <label for="phone">Phone / WhatsApp <span class="req">*</span></label>
            <input type="tel" id="phone" name="phone" required autocomplete="tel"
                   value="<?= e(old($old, 'phone')) ?>" <?= isset($errors['phone']) ? 'aria-invalid="true"' : '' ?>>
            <?= field_error($errors, 'phone') ?>
          </div>
          <div class="field">
            <label for="email">Email <span class="req">*</span></label>
            <input type="email" id="email" name="email" required autocomplete="email"
                   value="<?= e(old($old, 'email')) ?>" <?= isset($errors['email']) ? 'aria-invalid="true"' : '' ?>>
            <?= field_error($errors, 'email') ?>
          </div>
        </div>

        <fieldset class="fieldset">
          <legend>You are <span class="req">*</span></legend>
          <div class="radios">
            <?php
            foreach (['Homeowner', 'Business', 'Architect', 'Contractor'] as $index => $who) {
                $checked = was_chosen($old, 'you_are', $who) || ($old === [] && $index === 0);
                echo '<label><input type="radio" name="you_are" value="' . e($who) . '"'
                    . ($checked ? ' checked' : '') . ' required><span>' . e($who) . '</span></label>';
            }
            ?>
          </div>
          <?= field_error($errors, 'you_are') ?>
        </fieldset>
      </fieldset>

      <!-- Step 2 -->
      <fieldset class="fstep">
        <legend style="font-family:var(--serif);font-size:1.3rem;color:var(--ink-900);margin-bottom:.6rem">Step 2 &mdash; About the Space</legend>

        <div class="form__row form__row--2">
          <div class="field">
            <label for="space_type">Space type <span class="req">*</span></label>
            <select id="space_type" name="space_type" required>
              <?php
              foreach (['', 'Garage', 'Basement', 'Patio / Pool deck', 'Kitchen / Interior', 'Warehouse', 'Factory', 'Showroom', 'Commercial kitchen', 'Other'] as $space) {
                  $label = $space === '' ? 'Select a space type' : $space;
                  echo '<option value="' . e($space) . '"' . (was_chosen($old, 'space_type', $space) ? ' selected' : '') . '>' . e($label) . '</option>';
              }
              ?>
            </select>
            <?= field_error($errors, 'space_type') ?>
          </div>
          <div class="field">
            <label for="size">Approximate size (m&sup2;)</label>
            <input type="text" id="size" name="size" placeholder="Or the room dimensions"
                   value="<?= e(old($old, 'size')) ?>">
            <?= field_error($errors, 'size') ?>
          </div>
        </div>

        <div class="field">
          <label for="current_floor">Current floor material</label>
          <select id="current_floor" name="current_floor">
            <?php
            foreach (['', 'Bare concrete', 'Tile', 'Previous epoxy', 'Paint', 'Screed', 'Other'] as $floor) {
                $label = $floor === '' ? 'Select the existing surface' : $floor;
                echo '<option value="' . e($floor) . '"' . (was_chosen($old, 'current_floor', $floor) ? ' selected' : '') . '>' . e($label) . '</option>';
            }
            ?>
          </select>
        </div>

        <div class="field">
          <label for="known_issues">Any known cracks or moisture issues?</label>
          <textarea id="known_issues" name="known_issues" style="min-height:96px"
                    placeholder="Damp patches, hairline cracks, a slab that has moved&hellip; or just say not sure."><?= e(old($old, 'known_issues')) ?></textarea>
          <span class="field__hint">Not sure is a perfectly good answer &mdash; we test the slab on site.</span>
          <?= field_error($errors, 'known_issues') ?>
        </div>
      </fieldset>

      <!-- Step 3 -->
      <fieldset class="fstep">
        <legend style="font-family:var(--serif);font-size:1.3rem;color:var(--ink-900);margin-bottom:.6rem">Step 3 &mdash; About the Finish</legend>

        <fieldset class="fieldset">
          <legend>Finish of interest</legend>
          <div class="radios">
            <?php
            foreach (['Solid colour', 'Flake', 'Metallic', '3D', 'Undecided'] as $index => $finish) {
                $checked = was_chosen($old, 'finish', $finish) || ($old === [] && $finish === 'Undecided');
                echo '<label><input type="radio" name="finish" value="' . e($finish) . '"'
                    . ($checked ? ' checked' : '') . '><span>' . e($finish) . '</span></label>';
            }
            ?>
          </div>
        </fieldset>

        <fieldset class="fieldset">
          <legend>Your priority</legend>
          <div class="radios">
            <?php
            foreach (['Durability', 'Aesthetics', 'Fastest install', 'Lowest price'] as $priority) {
                $checked = was_chosen($old, 'priority', $priority);
                echo '<label><input type="radio" name="priority" value="' . e($priority) . '"'
                    . ($checked ? ' checked' : '') . '><span>' . e($priority) . '</span></label>';
            }
            ?>
          </div>
          <span class="field__hint">Knowing which of these matters most changes what we recommend.</span>
        </fieldset>
      </fieldset>

      <!-- Step 4 -->
      <fieldset class="fstep">
        <legend style="font-family:var(--serif);font-size:1.3rem;color:var(--ink-900);margin-bottom:.6rem">Step 4 &mdash; Timing &amp; Location</legend>

        <div class="field">
          <label for="location">Project location <span class="req">*</span></label>
          <input type="text" id="location" name="location" required placeholder="City or area"
                 value="<?= e(old($old, 'location')) ?>" <?= isset($errors['location']) ? 'aria-invalid="true"' : '' ?>>
          <?= field_error($errors, 'location') ?>
        </div>

        <div class="field">
          <label for="timeline">Preferred timeline</label>
          <select id="timeline" name="timeline">
            <?php
            foreach (['', 'This month', 'Next 1-3 months', '3-6 months', 'Just researching'] as $when) {
                $label = $when === '' ? 'Select a timeline' : $when;
                echo '<option value="' . e($when) . '"' . (was_chosen($old, 'timeline', $when) ? ' selected' : '') . '>' . e($label) . '</option>';
            }
            ?>
          </select>
          <?= field_error($errors, 'timeline') ?>
        </div>

        <p class="field__hint">No deposit is required to receive a quote, and your quote stays locked for 30 days.</p>
      </fieldset>

      <div class="form__nav">
        <button class="btn btn--ghost" type="button" data-step-back><span>Back</span></button>
        <button class="btn btn--secondary" type="button" data-step-next><span>Next</span></button>
        <button class="btn btn--primary btn--lg" type="submit" data-step-submit hidden><span>Submit &amp; Lock My Quote</span></button>
      </div>

      <noscript>
        <div class="form__actions">
          <button class="btn btn--primary btn--lg" type="submit"><span>Submit &amp; Lock My Quote</span></button>
        </div>
      </noscript>

      <p class="field__hint">We never share your details. See our <a href="<?= e(url('privacy')) ?>">privacy policy</a>.</p>
    </form>

    <!-- Trust signals (right rail) -->
    <div class="rail">
      <div class="rail__card reveal">
        <h3>Why this is safe to send</h3>
        <?php
        tick_list([
            e(cfg('warranty_years')) . '-year written warranty',
            'Fully licensed &amp; insured',
            e(cfg('years')) . '+ years in epoxy',
            e(num(cfg('projects_completed'))) . '+ completed projects',
            'No deposit required to receive a quote',
            'Quote locked for 30 days',
        ]);
        ?>
      </div>

      <div class="rail__card reveal">
        <h3>Rather not fill a form?</h3>
        <p style="font-size:.93rem">Send a photo of the floor. It is usually enough for us to tell you the likely system and range before we visit.</p>
        <div class="cta-row" style="flex-direction:column;align-items:stretch;margin-top:1rem">
          <?= button('WhatsApp a Photo', whatsapp_link('Hello ' . cfg('company_name') . ', here is a photo of my floor - can you quote it?'), 'primary', ['target' => '_blank', 'rel' => 'noopener']) ?>
          <?= button('Call ' . cfg('phone'), tel_link(), 'ghost') ?>
        </div>
      </div>

      <div class="rail__card reveal">
        <h3>What we will ask on site</h3>
        <p style="font-size:.93rem">Slab moisture, existing coatings, crack pattern, traffic type and how soon you need the space back. Twenty minutes, no obligation.</p>
        <a class="link-arrow" href="<?= e(url('process')) ?>">See the full process<?= icon('arrow', 'link-arrow__icon') ?></a>
      </div>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
