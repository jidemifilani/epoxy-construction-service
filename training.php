<?php
/** TRAINING - Epoxy installation and jewellery-making workshops across Nigeria */

require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/leads.php';

[$old, $errors] = handle_submission('training', [
    'name'     => 'required|max:120',
    'phone'    => 'required|phone|max:40',
    'email'    => 'required|email|max:160',
    'program'  => 'required|in:Epoxy Flooring Installation Masterclass,Epoxy Resin Jewellery Making Workshop',
    'location' => 'required|max:160',
    'date_pref'=> 'max:60',
]);

$pageTitle       = 'Epoxy Training Nigeria | Installation Masterclass & Jewellery Workshop';
$pageDescription = 'Professional epoxy training across Nigeria. Learn epoxy floor installation (3-day masterclass for contractors) or resin jewellery making (1-day intensive for artisans). Register your interest today.';
$pageKeywords    = 'epoxy floor installation training Nigeria, epoxy resin jewellery workshop Nigeria, epoxy contractor training Lagos, resin jewellery making class Nigeria';

$bannerEyebrow = 'Training programmes';
$bannerTitle   = 'Learn Epoxy. Work Anywhere in Nigeria.';
$bannerLede    = 'Two hands-on programmes: a 3-day floor installation masterclass for tradespeople and builders, and a 1-day resin jewellery intensive for artisans and hobbyists. Both are taught by our own certified installation team.';
$trail         = ['Training' => null];

$schemaOrg = json_encode([
    '@context' => 'https://schema.org',
    '@graph'   => [
        [
            '@type'          => 'Course',
            'name'           => 'Epoxy Flooring Installation Masterclass',
            'description'    => 'A 3-day hands-on masterclass covering substrate preparation, mixing ratios, metallic, self-levelling and industrial epoxy systems, and project costing. Taught by certified installers.',
            'provider'       => ['@type' => 'Organization', 'name' => cfg('company_name'), 'url' => cfg('site_url') ?? absolute_url('')],
            'courseMode'     => 'onsite',
            'duration'       => 'P3D',
            'availableLanguage' => 'en',
            'inLanguage'     => 'en',
            'offers'         => ['@type' => 'Offer', 'availability' => 'https://schema.org/InStock', 'category' => 'vocational training'],
            'hasCourseInstance' => [
                '@type'      => 'CourseInstance',
                'courseMode' => 'onsite',
                'location'   => ['@type' => 'Place', 'address' => ['@type' => 'PostalAddress', 'addressCountry' => 'NG']],
            ],
        ],
        [
            '@type'          => 'Course',
            'name'           => 'Epoxy Resin Jewellery Making Workshop',
            'description'    => 'A 1-day intensive covering resin casting, pigment use, moulding, inclusions and finishing. Leave with 5+ finished pieces and the skills to start selling.',
            'provider'       => ['@type' => 'Organization', 'name' => cfg('company_name'), 'url' => cfg('site_url') ?? absolute_url('')],
            'courseMode'     => 'onsite',
            'duration'       => 'P1D',
            'availableLanguage' => 'en',
            'inLanguage'     => 'en',
            'offers'         => ['@type' => 'Offer', 'availability' => 'https://schema.org/InStock', 'category' => 'craft workshop'],
            'hasCourseInstance' => [
                '@type'      => 'CourseInstance',
                'courseMode' => 'onsite',
                'location'   => ['@type' => 'Place', 'address' => ['@type' => 'PostalAddress', 'addressCountry' => 'NG']],
            ],
        ],
    ],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

require __DIR__ . '/includes/header.php';
?>
<script type="application/ld+json"><?= $schemaOrg ?></script>

<!-- ===== Why train with us ===== -->
<section class="sec">
  <div class="wrap">
    <?php section_head('Why train with us', 'Taught by the People Who Install for a Living.',
        'Our trainers are the same certified team that lays epoxy on residential, commercial and industrial sites across ' . e(cfg('service_area')) . '. You are learning from practitioners, not theorists.'); ?>

    <?php
    tick_list([
        'Certified instructors with active installation projects across Nigeria',
        'Small cohorts — maximum 8 per session — so you get hands-on time, not just a demonstration',
        'All materials and safety equipment included; nothing extra to buy on the day',
        'Certificate of completion on every programme, recognised by contractors and procurement teams',
        'Post-training support: WhatsApp access to your instructor for 30 days after the class',
    ], 'ticks--2col');
    ?>
  </div>
</section>

<!-- ===== Programme 1: Flooring Installation ===== -->
<section class="sec sec--paper" id="installation">
  <div class="wrap split split--wide" style="align-items:start">
    <div>
      <?php section_head('Programme 1', 'Epoxy Flooring Installation Masterclass.', '', 'left'); ?>
      <p><strong>Duration:</strong> 3 days (24 contact hours) &middot; <strong>Format:</strong> Classroom + live floor installation</p>
      <p>Designed for contractors, builders, site supervisors, and property managers who want to add a high-demand trade skill to their offering. Venues rotate across Lagos, Abuja, Port Harcourt, and other major cities — register your interest and we match you to the next intake near you.</p>

      <h3 class="training__module-head">What you will cover</h3>
      <ul class="training__modules">
        <li><strong>Day 1 &mdash; Substrates &amp; preparation</strong><br>Concrete assessment, moisture testing, diamond grinding, shot blasting, crack repair, and priming. The 80 % of the job that determines whether the coating fails or lasts 20 years.</li>
        <li><strong>Day 2 &mdash; Systems &amp; application</strong><br>Self-levelling epoxy, metallic &amp; decorative, solvent-free industrial, anti-slip aggregates. Mixing ratios, pot life, ambient conditions, colour blending.</li>
        <li><strong>Day 3 &mdash; Topcoats, business &amp; live install</strong><br>Polyurethane and epoxy topcoats, project costing, quoting, client communication. Afternoon: full live install on a real substrate — you coat it.</li>
      </ul>

      <div class="training__tags">
        <span>Contractors</span>
        <span>Builders</span>
        <span>Site supervisors</span>
        <span>Property managers</span>
        <span>Renovation firms</span>
      </div>
    </div>
    <div class="training__info-card reveal">
      <dl class="training__dl">
        <dt><?= icon('clock') ?>Duration</dt><dd>3 days</dd>
        <dt><?= icon('crew') ?>Cohort size</dt><dd>Max 8 participants</dd>
        <dt><?= icon('pin') ?>Locations</dt><dd>Lagos, Abuja, Port Harcourt + other cities by demand</dd>
        <dt><?= icon('shield') ?>Includes</dt><dd>All materials, PPE, certificate &amp; 30-day WhatsApp support</dd>
      </dl>
      <a class="btn btn--primary" href="#register">Register interest<?= icon('arrow', 'btn__icon') ?></a>
    </div>
  </div>
</section>

<!-- ===== Programme 2: Jewellery Workshop ===== -->
<section class="sec" id="jewellery">
  <div class="wrap split split--wide split--rev" style="align-items:start">
    <div>
      <?php section_head('Programme 2', 'Epoxy Resin Jewellery Making Workshop.', '', 'left'); ?>
      <p><strong>Duration:</strong> 1 day (8 hours) &middot; <strong>Format:</strong> Studio-style hands-on workshop</p>
      <p>For artisans, hobbyists, and anyone who wants to start a creative side income. Resin jewellery is one of the fastest-growing craft categories in Nigeria, and this workshop gets you making sellable pieces on day one. No prior art experience needed.</p>

      <h3 class="training__module-head">What you will make &amp; learn</h3>
      <ul class="training__modules">
        <li><strong>Morning &mdash; Foundations</strong><br>Resin chemistry (plain language), safety and ventilation, measuring and mixing ratios, working with pigments and dyes, preventing bubbles, mould types and release agents.</li>
        <li><strong>Afternoon &mdash; Casting &amp; finishing</strong><br>Inclusions (flowers, foil, glitter), layering techniques, pendants, earrings and bangles. UV resin for rapid prototyping. Sanding, buffing and setting hardware. Each participant leaves with at least 5 finished pieces.</li>
      </ul>

      <div class="training__tags">
        <span>Artisans</span>
        <span>Hobbyists</span>
        <span>Craft sellers</span>
        <span>Market vendors</span>
        <span>Students</span>
      </div>
    </div>
    <div class="training__info-card reveal">
      <dl class="training__dl">
        <dt><?= icon('clock') ?>Duration</dt><dd>1 day (8 hours)</dd>
        <dt><?= icon('crew') ?>Cohort size</dt><dd>Max 8 participants</dd>
        <dt><?= icon('pin') ?>Locations</dt><dd>Lagos + other cities by demand</dd>
        <dt><?= icon('shield') ?>Includes</dt><dd>All resins, pigments, moulds, hardware, packaging &amp; certificate</dd>
      </dl>
      <a class="btn btn--primary" href="#register">Register interest<?= icon('arrow', 'btn__icon') ?></a>
    </div>
  </div>
</section>

<!-- ===== Registration form ===== -->
<section class="sec sec--paper" id="register">
  <div class="wrap split split--wide" style="align-items:start">

    <div>
      <?php section_head('Registration', 'Secure Your Place.', 'Cohorts are small by design. Drop your details below and we will confirm the next intake date in your area and send pricing.', 'left'); ?>

      <div class="trust-points">
        <p><?= icon('check') ?> <strong>No payment now</strong> — registration is an expression of interest only</p>
        <p><?= icon('check') ?> We confirm dates and pricing by WhatsApp within 24 hours</p>
        <p><?= icon('check') ?> Cohorts run once your city has enough confirmed interest</p>
      </div>
    </div>

    <form class="form-card form reveal" method="post" action="<?= e(url('training')) ?>#register" novalidate>
      <?= csrf_field() ?>
      <div class="honeypot" aria-hidden="true">
        <label for="website">Website</label>
        <input type="text" id="website" name="website" tabindex="-1" autocomplete="off">
      </div>

      <?php if (!empty($errors['_form'])): ?>
      <p class="flash flash--error" role="alert"><?= e($errors['_form']) ?></p>
      <?php endif; ?>
      <?php if (!empty($errors) && empty($errors['_form'])): ?>
      <p class="flash flash--error" role="alert">Please fix the highlighted fields below.</p>
      <?php endif; ?>

      <div class="field <?= isset($errors['name']) ? 'field--error' : '' ?>">
        <label for="t-name">Full name *</label>
        <input type="text" id="t-name" name="name" value="<?= e(old($old, 'name')) ?>" autocomplete="name" required>
        <?= field_error($errors, 'name') ?>
      </div>

      <div class="field <?= isset($errors['phone']) ? 'field--error' : '' ?>">
        <label for="t-phone">Phone / WhatsApp *</label>
        <input type="tel" id="t-phone" name="phone" value="<?= e(old($old, 'phone')) ?>" autocomplete="tel" required>
        <?= field_error($errors, 'phone') ?>
      </div>

      <div class="field <?= isset($errors['email']) ? 'field--error' : '' ?>">
        <label for="t-email">Email address *</label>
        <input type="email" id="t-email" name="email" value="<?= e(old($old, 'email')) ?>" autocomplete="email" required>
        <?= field_error($errors, 'email') ?>
      </div>

      <div class="field <?= isset($errors['program']) ? 'field--error' : '' ?>">
        <label for="t-program">Programme of interest *</label>
        <select id="t-program" name="program" required>
          <option value="">— Select a programme —</option>
          <option value="Epoxy Flooring Installation Masterclass"<?= was_chosen($old, 'program', 'Epoxy Flooring Installation Masterclass') ? ' selected' : '' ?>>Epoxy Flooring Installation Masterclass (3 days)</option>
          <option value="Epoxy Resin Jewellery Making Workshop"<?= was_chosen($old, 'program', 'Epoxy Resin Jewellery Making Workshop') ? ' selected' : '' ?>>Epoxy Resin Jewellery Making Workshop (1 day)</option>
        </select>
        <?= field_error($errors, 'program') ?>
      </div>

      <div class="field <?= isset($errors['location']) ? 'field--error' : '' ?>">
        <label for="t-location">Your city / state *</label>
        <input type="text" id="t-location" name="location" value="<?= e(old($old, 'location')) ?>" placeholder="e.g. Lagos, Abuja, Port Harcourt, Enugu…" required>
        <?= field_error($errors, 'location') ?>
      </div>

      <div class="field <?= isset($errors['date_pref']) ? 'field--error' : '' ?>">
        <label for="t-date">Preferred month (optional)</label>
        <input type="text" id="t-date" name="date_pref" value="<?= e(old($old, 'date_pref')) ?>" placeholder="e.g. January, Q1 2025, ASAP…">
        <?= field_error($errors, 'date_pref') ?>
      </div>

      <button class="btn btn--primary btn--full" type="submit">
        <span>Register my interest</span><?= icon('arrow', 'btn__icon') ?>
      </button>
      <p class="form-note">We will confirm your intake date by WhatsApp within 24 hours.</p>
    </form>

  </div>
</section>

<!-- ===== FAQ teaser ===== -->
<section class="sec">
  <div class="wrap" style="max-width:720px;margin:0 auto;text-align:center">
    <?php section_head('Questions?', 'Reach Us Directly.', ''); ?>
    <p>WhatsApp <a href="<?= e(whatsapp_link('Hello, I have a question about the epoxy training programme.')) ?>" target="_blank" rel="noopener"><?= e(cfg('whatsapp')) ?></a> or call <a href="<?= e(tel_link()) ?>"><?= e(cfg('phone')) ?></a>. We are available <?= e(cfg('hours_short')) ?>.</p>
    <a class="btn btn--ghost" href="<?= e(url('faq')) ?>">Read the FAQ<?= icon('arrow', 'btn__icon') ?></a>
  </div>
</section>

<style>
.training__module-head{font-size:1rem;font-weight:700;margin:1.5rem 0 .5rem;color:var(--green-700,#166534)}
.training__modules{list-style:none;padding:0;margin:0 0 1.5rem;display:flex;flex-direction:column;gap:1rem}
.training__modules li{padding:.75rem 1rem;background:var(--surface-2,#f8fafc);border-left:3px solid var(--green-500,#22c55e);border-radius:0 .375rem .375rem 0}
.training__info-card{background:var(--surface-1,#fff);border:1px solid var(--border,#e2e8f0);border-radius:.75rem;padding:1.5rem;display:flex;flex-direction:column;gap:1rem}
.training__dl{display:grid;grid-template-columns:auto 1fr;gap:.5rem .75rem;margin:0}
.training__dl dt{display:flex;align-items:center;gap:.35rem;font-weight:600;font-size:.875rem;white-space:nowrap}
.training__dl dd{margin:0;font-size:.875rem}
.training__tags{display:flex;flex-wrap:wrap;gap:.5rem;margin-top:1rem}
.training__tags span{font-size:.8rem;padding:.25rem .65rem;background:var(--green-100,#dcfce7);color:var(--green-800,#166534);border-radius:999px;font-weight:500}
.trust-points{display:flex;flex-direction:column;gap:.75rem;margin-top:1.5rem}
.trust-points p{display:flex;align-items:center;gap:.5rem;margin:0}
.form-note{font-size:.8rem;color:var(--muted,#64748b);text-align:center;margin:.5rem 0 0}
</style>

<?php require __DIR__ . '/includes/footer.php'; ?>
