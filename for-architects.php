<?php
/** PAGE 10 - FOR ARCHITECTS, BUILDERS & CONTRACTORS (Trade Hub) */

require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/leads.php';

// The trade partnership enquiry posts back to this page.
[$old, $errors] = handle_submission('trade', [
    'firm_name'    => 'required|max:120',
    'contact_name' => 'required|max:120',
    'email'        => 'required|email|max:160',
    'phone'        => 'phone|max:40',
    'project_type' => 'required|max:120',
    'location'     => 'required|max:160',
    'start_date'   => 'max:60',
    'floor_area'   => 'max:40',
    'finish'       => 'max:120',
    'engagement'   => 'max:1500',
]);

$pageTitle       = 'Epoxy Flooring Partner for Architects & Contractors in ' . cfg('city');
$pageDescription = 'Spec sheets, physical samples, installer documentation and partnership pricing for architects, builders and main contractors.';
$pageKeywords    = 'epoxy spec sheets, architect flooring partner, contractor epoxy, trade epoxy pricing';

$bannerEyebrow = 'For architects, builders & contractors';
$bannerTitle   = 'The Trade Partner Behind the Finishes You&rsquo;ve Been Specifying.';
$bannerLede    = 'We work directly with architects, interior designers, main contractors and project managers across ' . e(cfg('service_area')) . '. Specs, samples, documentation and reliable site delivery.';
$trail         = ['For Architects' => null];
$bannerCtas    = button('Submit a Partnership Enquiry', '#enquiry', 'primary')
               . button('Email the Trade Desk', 'mailto:' . cfg('trade_email'), 'ghost');

require __DIR__ . '/includes/header.php';
?>

<!-- ===== What trade clients get ===== -->
<section class="sec">
  <div class="wrap split">
    <div>
      <?php section_head('What trade clients get', 'Six Things You Can Hold Us To.', '', 'left'); ?>
      <p class="pull">Programme-aligned installation that respects your critical path.</p>
      <div class="cta-row"><?= button('Request Sample Boards', url('contact') . '?topic=samples', 'secondary') ?></div>
    </div>
    <?php
    tick_list([
        '<strong>Full spec sheets</strong> for every system we install',
        '<strong>Physical sample boards</strong> delivered or dropped to your office',
        '<strong>CAD-friendly detail drawings</strong> for cove skirtings and transitions',
        '<strong>Programme-aligned installation</strong> that respects your critical path',
        '<strong>Compliance documentation</strong> for insurance and client handover',
        '<strong>Preferential partnership rates</strong> on repeat project volume',
    ]);
    ?>
  </div>
</section>

<!-- ===== How we work with trade ===== -->
<section class="sec sec--dark">
  <div class="banner__grid" aria-hidden="true"></div>
  <div class="wrap split" style="position:relative;z-index:1">
    <div>
      <?php section_head('How we work with trade', 'Four Points of Contact.', '', 'left'); ?>
      <p>The earlier we are in the conversation, the fewer surprises reach your snag list. We would rather flag a moisture problem at design stage than discover it the week the floor is due.</p>
      <div class="cta-row"><?= button('Submit a Partnership Enquiry', '#enquiry', 'primary') ?></div>
    </div>
    <?php
    step_list([
        ['Early engagement', 'We join design meetings before the floor spec is finalised.'],
        ['Spec validation', 'We check your proposed system against actual site conditions.'],
        ['Programme integration', 'We sequence around other trades to avoid damage and rework.'],
        ['Site handover', 'We document, we clean, we close out.'],
    ], true);
    ?>
  </div>
</section>

<!-- ===== Partnership enquiry form ===== -->
<section class="sec sec--paper" id="enquiry">
  <div class="wrap">
    <?php section_head('Partnership enquiry', 'Tell Us About the Project.',
        'Everything below except the project detail is optional. We reply within one business day.'); ?>

    <div class="split split--wide" style="align-items:start">
      <form class="form-card form reveal" method="post" action="<?= e(url('for-architects')) ?>#enquiry" novalidate>
        <?= csrf_field() ?>
        <div class="honeypot" aria-hidden="true">
          <label for="website">Website</label>
          <input type="text" id="website" name="website" tabindex="-1" autocomplete="off">
        </div>

        <?php if (!empty($errors['_form'])): ?>
        <p class="flash flash--error" role="alert"><?= e($errors['_form']) ?></p>
        <?php endif; ?>

        <div class="form__row form__row--2">
          <div class="field">
            <label for="firm_name">Firm name <span class="req">*</span></label>
            <input type="text" id="firm_name" name="firm_name" required value="<?= e(old($old, 'firm_name')) ?>"
                   <?= isset($errors['firm_name']) ? 'aria-invalid="true"' : '' ?>>
            <?= field_error($errors, 'firm_name') ?>
          </div>
          <div class="field">
            <label for="contact_name">Your name <span class="req">*</span></label>
            <input type="text" id="contact_name" name="contact_name" required value="<?= e(old($old, 'contact_name')) ?>"
                   <?= isset($errors['contact_name']) ? 'aria-invalid="true"' : '' ?>>
            <?= field_error($errors, 'contact_name') ?>
          </div>
        </div>

        <div class="form__row form__row--2">
          <div class="field">
            <label for="email">Email <span class="req">*</span></label>
            <input type="email" id="email" name="email" required value="<?= e(old($old, 'email')) ?>"
                   <?= isset($errors['email']) ? 'aria-invalid="true"' : '' ?>>
            <?= field_error($errors, 'email') ?>
          </div>
          <div class="field">
            <label for="phone">Phone / WhatsApp</label>
            <input type="tel" id="phone" name="phone" value="<?= e(old($old, 'phone')) ?>"
                   <?= isset($errors['phone']) ? 'aria-invalid="true"' : '' ?>>
            <?= field_error($errors, 'phone') ?>
          </div>
        </div>

        <div class="form__row form__row--2">
          <div class="field">
            <label for="project_type">Project type <span class="req">*</span></label>
            <select id="project_type" name="project_type" required>
              <?php
              $types = ['', 'Residential development', 'Commercial fit-out', 'Industrial / warehouse', 'Hospitality', 'Healthcare', 'Retail / showroom', 'Education', 'Other'];
              foreach ($types as $type) {
                  $label = $type === '' ? 'Select a project type' : $type;
                  echo '<option value="' . e($type) . '"' . (was_chosen($old, 'project_type', $type) ? ' selected' : '') . '>' . e($label) . '</option>';
              }
              ?>
            </select>
            <?= field_error($errors, 'project_type') ?>
          </div>
          <div class="field">
            <label for="location">Project location <span class="req">*</span></label>
            <input type="text" id="location" name="location" required placeholder="City or area"
                   value="<?= e(old($old, 'location')) ?>" <?= isset($errors['location']) ? 'aria-invalid="true"' : '' ?>>
            <?= field_error($errors, 'location') ?>
          </div>
        </div>

        <div class="form__row form__row--2">
          <div class="field">
            <label for="start_date">Estimated start date</label>
            <input type="text" id="start_date" name="start_date" placeholder="e.g. Q1 next year"
                   value="<?= e(old($old, 'start_date')) ?>">
            <?= field_error($errors, 'start_date') ?>
          </div>
          <div class="field">
            <label for="floor_area">Floor area (m&sup2;)</label>
            <input type="text" id="floor_area" name="floor_area" placeholder="Approximate is fine"
                   value="<?= e(old($old, 'floor_area')) ?>">
            <?= field_error($errors, 'floor_area') ?>
          </div>
        </div>

        <div class="field">
          <label for="finish">Finish of interest</label>
          <input type="text" id="finish" name="finish" placeholder="Metallic, flake, self-levelling, undecided&hellip;"
                 value="<?= e(old($old, 'finish')) ?>">
          <?= field_error($errors, 'finish') ?>
        </div>

        <div class="field">
          <label for="engagement">How would you like us to engage?</label>
          <textarea id="engagement" name="engagement" placeholder="Spec review, sample boards, a design meeting, a tender response&hellip;"><?= e(old($old, 'engagement')) ?></textarea>
          <span class="field__hint">If this is a tender, tell us the submission date and we will work backwards from it.</span>
          <?= field_error($errors, 'engagement') ?>
        </div>

        <div class="form__actions">
          <button class="btn btn--primary btn--lg" type="submit"><span>Submit Trade Partnership Enquiry</span></button>
        </div>
      </form>

      <div class="rail">
        <div class="rail__card reveal">
          <h3>Trade desk</h3>
          <ul class="foot__contact" style="margin:0">
            <li><?= icon('mail') ?><a href="mailto:<?= e(cfg('trade_email')) ?>"><?= email_html(cfg('trade_email')) ?></a></li>
            <li><?= icon('phone') ?><a href="<?= e(tel_link()) ?>"><?= e(cfg('phone')) ?></a></li>
            <li><?= icon('clock') ?><span><?= e(cfg('hours_short')) ?></span></li>
          </ul>
        </div>
        <div class="rail__card reveal">
          <h3>What we can send today</h3>
          <?php
          tick_list([
              'System spec sheets (PDF)',
              'Cove skirting and transition details',
              'Insurance and licence certificates',
              'Pre-qualification pack',
              'Physical sample boards',
          ]);
          ?>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ===== Reviews from trade ===== -->
<section class="sec">
  <div class="wrap">
    <?php section_head('From the trade', 'What Architects and PMs Say.'); ?>
    <div class="grid grid--2">
      <?php
      $trade = array_values(array_filter(testimonials(), static fn (array $r): bool => $r['group'] === 'trade'));
      foreach ($trade as $review) { review_card($review); }
      ?>
    </div>
  </div>
</section>

<?php cta_band('Specifying a Floor This Quarter?', 'Send us the drawings and we will validate the spec against site conditions before it is locked.', 'Submit a Partnership Enquiry', '#enquiry'); ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
