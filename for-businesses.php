<?php
/** PAGE 9 - FOR BUSINESSES & FACILITY MANAGERS */

require_once __DIR__ . '/includes/functions.php';

$pageTitle       = 'Commercial Epoxy Flooring Partner - Warehouses, Food, Pharma, Healthcare';
$pageDescription = 'The commercial epoxy partner facility managers call back. Fast-cure, documented, warranty-backed. Specialists in food, pharma, logistics and manufacturing.';
$pageKeywords    = 'commercial epoxy partner, facility flooring, warehouse recoat, food plant flooring, clean room epoxy';

$bannerEyebrow = 'For businesses & facility managers';
$bannerTitle   = 'The Floor Shouldn&rsquo;t Be a Line Item You Worry About.';
$bannerLede    = 'Downtime, hygiene audits, chemical spills, forklift damage &mdash; your floor sits under all of it. We design systems around your operation, not around what we have in stock.';
$trail         = ['For Businesses' => null];
$bannerCtas    = button('Book a Facility Walkthrough', url('quote'), 'primary')
               . button('Email the Commercial Team', 'mailto:' . cfg('commercial_email'), 'ghost');

require __DIR__ . '/includes/header.php';
?>

<?php trust_bar(); ?>

<!-- ===== What facility managers get ===== -->
<section class="sec">
  <div class="wrap split">
    <div>
      <?php section_head('What you get from us', 'Six Things That Protect Your Operation.', '', 'left'); ?>
      <p>Night and weekend shifts carry <strong>no premium</strong> above <?= e(num(cfg('night_shift_threshold'))) ?> m&sup2;, because the point of a phased install is that it costs you nothing in production.</p>
      <div class="cta-row"><?= button('Request a Site Survey', url('quote'), 'primary') ?></div>
    </div>
    <?php
    tick_list([
        '<strong>Phased installs</strong> that protect operations',
        '<strong>Night and weekend shifts</strong> at no premium above ' . num(cfg('night_shift_threshold')) . ' m&sup2;',
        '<strong>Documented application record</strong> for ISO, HACCP and GMP audits',
        '<strong>PDS, MSDS and warranty</strong> delivered in a single project folder',
        '<strong>Single point of contact</strong> &mdash; one project manager, start to finish',
        '<strong>Fast-cure systems</strong> that return the floor to service in 24 hours or less',
    ]);
    ?>
  </div>
</section>

<!-- ===== Common commercial projects ===== -->
<section class="sec sec--paper">
  <div class="wrap">
    <?php section_head('Common commercial projects', 'Six Briefs We See Most Weeks.'); ?>
    <div class="grid grid--3">
      <?php
      $briefs = [
          ['warehouse', 'Warehouse recoat during a planned shutdown', 'Scoped to finish inside the window you already have, bay by bay if needed.'],
          ['factory',   'New-build facility floor',                    'Specified from the drawings, installed in sequence with the other trades.'],
          ['food',      'Wash-down zone replacement in a food plant',  'Coved skirtings, antimicrobial topcoat, phased around production.'],
          ['retail',    'Showroom refresh ahead of a launch',          'Night shifts and a fixed handover date you can plan the opening around.'],
          ['pharma',    'Clean room or lab installation',              'ISO-class finishes and ESD options where the process requires them.'],
          ['auto',      'Loading bay repair and recoat',               'Epoxy mortar where the slab has taken impact damage.'],
      ];
      foreach ($briefs as $brief) {
          feature_card($brief[0], $brief[1], $brief[2]);
      }
      ?>
    </div>
  </div>
</section>

<!-- ===== Procurement & documentation ===== -->
<section class="sec sec--dark" id="procurement">
  <div class="banner__grid" aria-hidden="true"></div>
  <div class="wrap split" style="position:relative;z-index:1">
    <div>
      <?php section_head('Procurement & documentation', 'We Work the Way Your Process Works.', '', 'left'); ?>
      <p>Contact our commercial team for spec sheets, insurance certificates or a full pre-qualification pack. Most requests turn round the same working day.</p>
      <div class="cta-row">
        <?= button('Request Vendor Pre-Qualification Pack', url('contact') . '?topic=prequal', 'primary') ?>
      </div>
    </div>
    <?php
    tick_list([
        'Tender documents',
        'RFQs and RFPs',
        'Pre-qualified vendor lists',
        'Insurance and compliance submissions',
        'Blanket agreements for multi-site contracts',
    ]);
    ?>
  </div>
</section>

<!-- ===== Reviews from businesses ===== -->
<section class="sec">
  <div class="wrap">
    <?php section_head('From facility managers', 'What Operations Teams Say.'); ?>
    <div class="grid grid--2">
      <?php
      $business = array_values(array_filter(testimonials(), static fn (array $r): bool => $r['group'] === 'businesses'));
      foreach ($business as $review) { review_card($review); }
      ?>
    </div>
    <div class="cta-row" style="justify-content:center">
      <?= button('See Commercial Systems', url('services/commercial'), 'secondary') ?>
      <?= button('Read All Reviews', url('testimonials'), 'ghost') ?>
    </div>
  </div>
</section>

<?php
echo '<section class="band"><div class="band__grid" aria-hidden="true"></div><div class="wrap band__inner">';
echo '<p class="eyebrow eyebrow--light reveal">Next step</p>';
echo '<h2 class="band__title reveal">Let Us Scope It Around Your Shifts.</h2>';
echo ornament('light');
echo '<p class="band__lede reveal">Site survey, moisture test, phased programme and a documented handover pack.</p>';
echo '<div class="cta-row reveal">'
    . button('Book a Facility Walkthrough', url('quote'), 'primary')
    . button('Email the Commercial Team', 'mailto:' . cfg('commercial_email'), 'secondary')
    . '</div>';
echo '</div></section>';
?>

<?php require __DIR__ . '/includes/footer.php'; ?>
