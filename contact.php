<?php
/** PAGE 14 - CONTACT */

require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/leads.php';

[$old, $errors] = handle_submission('contact', [
    'name'          => 'required|max:120',
    'email'         => 'required|email|max:160',
    'phone'         => 'required|phone|max:40',
    'enquiring_as'  => 'required|in:Homeowner,Business,Architect / Contractor',
    'project_type'  => 'max:60',
    'floor_area'    => 'max:40',
    'timeline'      => 'max:60',
    'message'       => 'max:2000',
]);

// A CTA elsewhere on the site can pre-set the subject, e.g. ?topic=prequal
$topics = [
    'lookbook'     => 'I would like the decorative lookbook.',
    'prequal'      => 'Please send the vendor pre-qualification pack.',
    'samples'      => 'I would like physical sample boards for a project.',
    'case-studies' => 'Please send the commercial case study PDFs.',
];
$topic = isset($_GET['topic']) && is_string($_GET['topic']) ? ($topics[$_GET['topic']] ?? '') : '';

$pageTitle       = 'Contact Epoxy Flooring Company Lagos, Nigeria | ' . cfg('company_name');
$pageDescription = 'Call, WhatsApp or email ' . cfg('company_name') . ' for a free on-site inspection anywhere in '
    . cfg('service_area') . '. Response within one business day.';

$bannerEyebrow = 'Contact';
$bannerTitle   = 'Let&rsquo;s Talk About Your Floor.';
$bannerLede    = 'Pick whichever way suits you. We respond to every enquiry within one business day &mdash; usually within a few hours.';
$trail         = ['Contact' => null];

require __DIR__ . '/includes/header.php';
?>

<!-- ===== Contact methods ===== -->
<section class="sec">
  <div class="wrap">
    <?php section_head('Contact methods', 'Four Ways to Reach Us.'); ?>
    <div class="grid grid--4">
      <?php
      echo '<article class="card feature-card contact-tile reveal">';
      echo '<span class="feature-card__icon">' . icon('phone', 'icon icon--lg') . '</span>';
      echo '<h3>Call Us</h3>';
      echo '<a href="' . e(tel_link()) . '">' . e(cfg('phone')) . '</a>';
      echo '<p class="contact-tile__note">' . e(cfg('hours_short')) . '</p>';
      echo corner_frame() . '</article>';

      echo '<article class="card feature-card contact-tile reveal">';
      echo '<span class="feature-card__icon">' . icon('chat', 'icon icon--lg') . '</span>';
      echo '<h3>WhatsApp</h3>';
      echo '<a href="' . e(whatsapp_link()) . '" target="_blank" rel="noopener">' . e(cfg('whatsapp')) . '</a>';
      echo '<p class="contact-tile__note">Fastest for photos and site videos</p>';
      echo corner_frame() . '</article>';

      echo '<article class="card feature-card contact-tile reveal">';
      echo '<span class="feature-card__icon">' . icon('mail', 'icon icon--lg') . '</span>';
      echo '<h3>Email</h3>';
      echo '<a href="mailto:' . e(cfg('email')) . '">' . email_html(cfg('email')) . '</a>';
      echo '<a href="mailto:' . e(cfg('commercial_email')) . '">' . email_html(cfg('commercial_email')) . '</a>';
      echo '<a href="mailto:' . e(cfg('trade_email')) . '">' . email_html(cfg('trade_email')) . '</a>';
      echo '<p class="contact-tile__note">General &middot; Commercial &middot; Trade</p>';
      echo corner_frame() . '</article>';

      echo '<article class="card feature-card contact-tile reveal">';
      echo '<span class="feature-card__icon">' . icon('pin', 'icon icon--lg') . '</span>';
      echo '<h3>Visit</h3>';
      echo '<p>' . e(cfg('office_address')) . '</p>';
      echo '<p class="contact-tile__note">By appointment &mdash; we are usually on site.</p>';
      echo corner_frame() . '</article>';
      ?>
    </div>
  </div>
</section>

<!-- ===== Contact form + service area ===== -->
<section class="sec sec--paper">
  <div class="wrap split split--wide" style="align-items:start">

    <form class="form-card form reveal" method="post" action="<?= e(url('contact')) ?>#form" id="form" novalidate>
      <h2 style="font-size:1.5rem">Send Us the Details</h2>
      <p style="font-size:.95rem;margin-bottom:.4rem">Fields marked <span class="req" style="color:var(--green-600)">*</span> are required. Everything else helps us quote faster.</p>

      <?= csrf_field() ?>
      <div class="honeypot" aria-hidden="true">
        <label for="website">Website</label>
        <input type="text" id="website" name="website" tabindex="-1" autocomplete="off">
      </div>

      <?php if (!empty($errors['_form'])): ?>
      <p class="flash flash--error" role="alert"><?= e($errors['_form']) ?></p>
      <?php endif; ?>

      <div class="field">
        <label for="name">Name <span class="req">*</span></label>
        <input type="text" id="name" name="name" required autocomplete="name"
               value="<?= e(old($old, 'name')) ?>" <?= isset($errors['name']) ? 'aria-invalid="true"' : '' ?>>
        <?= field_error($errors, 'name') ?>
      </div>

      <div class="form__row form__row--2">
        <div class="field">
          <label for="email">Email <span class="req">*</span></label>
          <input type="email" id="email" name="email" required autocomplete="email"
                 value="<?= e(old($old, 'email')) ?>" <?= isset($errors['email']) ? 'aria-invalid="true"' : '' ?>>
          <?= field_error($errors, 'email') ?>
        </div>
        <div class="field">
          <label for="phone">Phone / WhatsApp <span class="req">*</span></label>
          <input type="tel" id="phone" name="phone" required autocomplete="tel"
                 value="<?= e(old($old, 'phone')) ?>" <?= isset($errors['phone']) ? 'aria-invalid="true"' : '' ?>>
          <?= field_error($errors, 'phone') ?>
        </div>
      </div>

      <fieldset class="fieldset">
        <legend>I&rsquo;m enquiring as <span class="req">*</span></legend>
        <div class="radios">
          <?php
          foreach (['Homeowner', 'Business', 'Architect / Contractor'] as $index => $role) {
              $checked = was_chosen($old, 'enquiring_as', $role) || ($old === [] && $index === 0);
              echo '<label><input type="radio" name="enquiring_as" value="' . e($role) . '"'
                  . ($checked ? ' checked' : '') . ' required><span>' . e($role) . '</span></label>';
          }
          ?>
        </div>
        <?= field_error($errors, 'enquiring_as') ?>
      </fieldset>

      <div class="form__row form__row--2">
        <div class="field">
          <label for="project_type">Project type</label>
          <select id="project_type" name="project_type">
            <?php
            foreach (['', 'Garage', 'Basement', 'Patio / Pool deck', 'Kitchen / Interior', 'Warehouse', 'Showroom', 'Decorative', 'Other'] as $type) {
                $label = $type === '' ? 'Select a project type' : $type;
                echo '<option value="' . e($type) . '"' . (was_chosen($old, 'project_type', $type) ? ' selected' : '') . '>' . e($label) . '</option>';
            }
            ?>
          </select>
        </div>
        <div class="field">
          <label for="floor_area">Approximate floor area (m&sup2;)</label>
          <input type="text" id="floor_area" name="floor_area" placeholder="A rough number is fine"
                 value="<?= e(old($old, 'floor_area')) ?>">
        </div>
      </div>

      <div class="field">
        <label for="timeline">When would you like it done?</label>
        <select id="timeline" name="timeline">
          <?php
          foreach (['', 'This month', 'Next 1-3 months', '3-6 months', 'Just researching'] as $when) {
              $label = $when === '' ? 'Select a timeline' : $when;
              echo '<option value="' . e($when) . '"' . (was_chosen($old, 'timeline', $when) ? ' selected' : '') . '>' . e($label) . '</option>';
          }
          ?>
        </select>
      </div>

      <div class="field">
        <label for="message">Tell us about the project</label>
        <textarea id="message" name="message" placeholder="Condition of the floor, what it is used for, the look you want&hellip;"><?= e(old($old, 'message', $topic)) ?></textarea>
        <span class="field__hint">Photos help a lot &mdash; WhatsApp them to <?= e(cfg('whatsapp')) ?> and mention your name here.</span>
        <?= field_error($errors, 'message') ?>
      </div>

      <div class="form__actions">
        <button class="btn btn--primary btn--lg" type="submit"><span>Submit Enquiry</span></button>
      </div>
      <p class="field__hint">We never share your details. See our <a href="<?= e(url('privacy')) ?>">privacy policy</a>.</p>
    </form>

    <div class="rail">
      <div class="rail__card reveal">
        <h3>Service area</h3>
        <?php
        // Read the list into a variable first: end() takes a reference, so it
        // cannot be handed the return value of cfg() directly.
        $areas = (array) cfg('service_cities', []);
        $lastArea = array_pop($areas);
        ?>
        <p style="font-size:.93rem">We serve
          <?= $areas !== [] ? e(implode(', ', $areas)) . ' and ' : '' ?><?= e((string) $lastArea) ?>,
          plus surrounding areas within <?= e(cfg('service_radius')) ?> km. For projects outside our standard
          radius we quote travel on a per-project basis.</p>
        <div class="map" role="img" aria-label="Service area map centred on <?= e(cfg('city')) ?>">
          <span class="map__pin"><?= icon('pin') ?></span>
          <p class="map__note"><strong>Map placeholder.</strong> Embed a Google Map pinned to
            <?= e(cfg('office_address')) ?> with a <?= e(cfg('service_radius')) ?> km radius overlay.</p>
        </div>
      </div>

      <div class="rail__card reveal">
        <h3>Business hours</h3>
        <ul class="foot__contact" style="margin:0">
          <li><?= icon('clock') ?><span><?= e(cfg('hours_weekday')) ?></span></li>
          <li><?= icon('clock') ?><span><?= e(cfg('hours_saturday')) ?></span></li>
          <li><?= icon('chat') ?><span><?= e(cfg('hours_sunday')) ?></span></li>
        </ul>
      </div>

      <div class="rail__card reveal">
        <h3>Prefer a fixed quote?</h3>
        <p style="font-size:.93rem">The quote page asks the four questions we need to price a floor properly.</p>
        <div class="cta-row" style="margin-top:1rem"><?= button('Request a Free Quote', url('quote'), 'primary') ?></div>
      </div>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
