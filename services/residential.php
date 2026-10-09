<?php
/** PAGE 3 - RESIDENTIAL EPOXY SERVICES */

require_once __DIR__ . '/../includes/functions.php';

$pageTitle       = 'Garage Epoxy Floor Lagos, Nigeria | Residential Epoxy Flooring';
$pageDescription = 'Premium residential epoxy flooring in ' . cfg('city') . '. Garage floors, basements, pool decks, patios and interior living spaces. Free quotes. ' . cfg('warranty_years') . '-year warranty.';
$pageKeywords    = 'garage epoxy floor Lagos, residential epoxy flooring Nigeria, garage floor coating Lagos, basement epoxy Lagos, patio epoxy Nigeria, home epoxy floors';

$bannerEyebrow = 'Residential';
$bannerTitle   = 'Residential Epoxy Floors Built to Outlast the House.';
$bannerLede    = 'Your garage, basement, patio or interior deserves better than paint-on coatings that peel in 18 months. Our residential systems are the same industrial-grade formulas we install in warehouses &mdash; adapted for the look and feel of a home.';
$trail         = ['Services' => 'services/', 'Residential' => null];

$bannerCtas = button('Get My Free Home Quote', url('quote'), 'primary');

require __DIR__ . '/../includes/header.php';

$residentialFaq = [
    ['Can I park on it right away?', 'Walk on it in 24 hours, park in 7 days. A polyaspartic topcoat brings that forward to roughly 48 hours.'],
    ['Will it crack if my slab cracks?', 'Minor shrinkage cracks are handled during prep. Structural movement is a separate issue &mdash; we flag it before quoting.'],
    ['Can you work around furniture or cars?', 'We move it, we work, we return it. All in the quote.'],
    ['Does it fade in sunlight?', 'Our UV-stable topcoats are designed for sun exposure. Standard epoxies yellow &mdash; we do not use those outdoors.'],
];
?>

<?php trust_bar(); ?>

<!-- ===== Where we install ===== -->
<section class="sec" id="spaces">
  <div class="wrap">
    <?php section_head('Where we install at home', 'Six Spaces We Transform.'); ?>
    <div class="grid grid--3">
      <?php
      $spaces = [
          ['garage',   '&#128663; Garage Floors',          'The most popular residential install. Resists oil, brake fluid, hot tyres and dropped tools. Choose matte, gloss, flake or metallic.'],
          ['basement', '&#127968; Basements',               'Moisture-resistant, mould-resistant and warmer underfoot than bare concrete. Finally makes the basement feel like a real room.'],
          ['patio',    '&#9728; Patios &amp; Pool Decks',   'UV-stable topcoats with anti-slip aggregates. Comfortable barefoot, built for weather.'],
          ['kitchen',  '&#127859; Kitchens &amp; Pantries', 'Seamless &mdash; no grout lines to trap grease or bacteria. Food-safe and easy to clean.'],
          ['living',   '&#128715; Interior Living Spaces',  'Yes, really. Our decorative systems rival polished concrete and natural stone &mdash; at a lower install cost.'],
          ['workshop', '&#129520; Workshops &amp; Hobby Rooms', 'Chemical-resistant for the hobbyist with solvents, oils or paints in play.'],
      ];
      foreach ($spaces as $space) {
          feature_card($space[0], $space[1], $space[2]);
      }
      ?>
    </div>
  </div>
</section>

<!-- ===== Why homeowners choose epoxy ===== -->
<section class="sec sec--paper">
  <div class="wrap split">
    <div>
      <?php section_head('Why homeowners choose epoxy', 'What You Are Actually Buying.', '', 'left'); ?>
      <p class="pull">A 15&ndash;20 year floor instead of a 3-year coating.</p>
    </div>
    <?php
    tick_list([
        '<strong>Seamless surface</strong> &mdash; no tile grout, no plank seams, no dirt traps.',
        '<strong>Easier cleaning</strong> &mdash; mop, done.',
        '<strong>Higher resale value</strong> &mdash; buyers notice finished garages and basements.',
        '<strong>Design freedom</strong> &mdash; colour, flake, metallic, gloss level, logos.',
        '<strong>Scratch and stain resistance</strong> &mdash; dropped tools, pet nails, red wine. Shrugs it off.',
    ]);
    ?>
  </div>
</section>

<!-- ===== What it costs ===== -->
<section class="sec" id="costs">
  <div class="wrap">
    <?php section_head('What it costs', 'Realistic Residential Pricing.', 'Every home is different, but as a reference:'); ?>

    <div class="table-wrap reveal">
      <table>
        <caption>Indicative ranges. Final price depends on floor condition, prep requirements and system choice.</caption>
        <thead><tr><th scope="col">Space Type</th><th scope="col">Typical Range</th></tr></thead>
        <tbody>
          <tr><td><strong>Garage (standard 1-car)</strong></td><td><?= e(cfg('currency') . cfg('price_garage_1_low')) ?> &ndash; <?= e(cfg('currency') . cfg('price_garage_1_high')) ?></td></tr>
          <tr><td><strong>Garage (2-car)</strong></td><td><?= e(cfg('currency') . cfg('price_garage_2_low')) ?> &ndash; <?= e(cfg('currency') . cfg('price_garage_2_high')) ?></td></tr>
          <tr><td><strong>Basement</strong></td><td><?= e(cfg('currency') . cfg('rate_basement')) ?> / m&sup2;</td></tr>
          <tr><td><strong>Patio / Pool Deck</strong></td><td><?= e(cfg('currency') . cfg('rate_patio')) ?> / m&sup2;</td></tr>
          <tr><td><strong>Metallic Decorative</strong></td><td><?= e(cfg('currency') . cfg('rate_metallic')) ?> / m&sup2;</td></tr>
        </tbody>
      </table>
    </div>

    <p class="note reveal" style="margin-top:1.4rem">
      You get a <strong>fixed-price written quote</strong> before work starts &mdash; no surprises.
    </p>
    <div class="cta-row" style="justify-content:center"><?= button('Get My Home Quote', url('quote'), 'primary') ?></div>
  </div>
</section>

<!-- ===== Residential process ===== -->
<section class="sec sec--dark">
  <div class="banner__grid" aria-hidden="true"></div>
  <div class="wrap split" style="position:relative;z-index:1">
    <div>
      <?php section_head('Our residential process', 'Four Days, Four Steps.', 'The timeline below is a typical single garage. Larger or multi-room jobs are scoped the same way, just longer.', 'left'); ?>
      <div class="cta-row"><?= button('See the Full Seven-Step Process', url('process'), 'primary') ?></div>
    </div>
    <?php
    step_list([
        ['Day 0 &mdash; Free site inspection', 'And a written quote within 48 hours.'],
        ['Day 1 &mdash; Diamond grinding', 'Crack repair and primer.'],
        ['Day 2 &mdash; Base coat', 'Plus the decorative layer: flake, metallic or solid colour.'],
        ['Day 3 &mdash; Clear topcoat', '24-hour foot-traffic cure.'],
        ['Day 7 &mdash; Full vehicle-ready cure', 'Walkthrough and warranty handover.'],
    ], true);
    ?>
  </div>
</section>

<!-- ===== Residential FAQ ===== -->
<section class="sec sec--paper">
  <div class="wrap" style="max-width:860px">
    <?php section_head('Residential FAQ', 'The Four Questions Every Homeowner Asks.'); ?>
    <?php accordion($residentialFaq); ?>
    <div class="cta-row" style="justify-content:center"><?= button('See All FAQs', url('faq'), 'ghost') ?></div>
  </div>
</section>

<?php faq_schema($residentialFaq); ?>

<?php
$waLink = whatsapp_link('Hello ' . cfg('company_name') . ', here is a photo of my floor - can you quote it?');
echo '<section class="band"><div class="band__grid" aria-hidden="true"></div><div class="wrap band__inner">';
echo '<p class="eyebrow eyebrow--light reveal">Next step</p>';
echo '<h2 class="band__title reveal">Send Us the Floor. We Will Send You the Number.</h2>';
echo ornament('light');
echo '<p class="band__lede reveal">A photo on WhatsApp is enough to start. The written quote follows the site visit.</p>';
echo '<div class="cta-row reveal">'
    . button('Request My Free Home Quote', url('quote'), 'primary')
    . button('WhatsApp a Photo of My Floor', $waLink, 'secondary', ['target' => '_blank', 'rel' => 'noopener'])
    . '</div>';
echo '</div></section>';
?>

<?php require __DIR__ . '/../includes/footer.php'; ?>
