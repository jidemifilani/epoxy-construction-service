<?php
/** PAGE 8 - FOR HOMEOWNERS (audience hub) */

require_once __DIR__ . '/includes/functions.php';

$pageTitle       = 'Epoxy Flooring for Homeowners - Garage, Basement, Patio & Interior';
$pageDescription = 'Everything homeowners need to know about epoxy flooring - pricing, process, maintenance and real project examples. Free quotes.';
$pageKeywords    = 'epoxy flooring for homeowners, garage epoxy cost, basement epoxy, DIY epoxy vs professional';

$bannerEyebrow = 'For homeowners';
$bannerTitle   = 'Everything You Wanted to Know Before Calling an Epoxy Company.';
$bannerLede    = 'No sales pressure. No jargon. Just the real answers &mdash; pricing, prep, maintenance and timelines &mdash; so you can make a confident decision about your home.';
$trail         = ['For Homeowners' => null];

require __DIR__ . '/includes/header.php';

$homeownerFaq = [
    ['Is it a DIY project?', 'The kits exist. The results do not last. Prep is the issue &mdash; a hire-shop grinder does not reach the surface profile the resin needs.'],
    ['How long until I can park on it?', '24 hours for foot traffic. 7 days for cars, or about 48 hours with a polyaspartic topcoat.'],
    ['Will it last if my floor has cracks?', 'Most minor cracks are handled in prep. We flag anything structural before quoting.'],
    ['How much mess is involved?', 'Grinding is dusty &mdash; we use HEPA-vacuumed grinders to contain 95%+.'],
    ['How do I maintain it?', 'Soft broom and a damp mop. That is it.'],
];
?>

<?php trust_bar(); ?>

<!-- ===== I'm thinking about epoxy for my... ===== -->
<section class="sec">
  <div class="wrap">
    <?php section_head('Where to start', '&ldquo;I&rsquo;m Thinking About Epoxy for My&hellip;&rdquo;'); ?>
    <div class="grid grid--4">
      <?php
      $spaces = [
          ['garage',   'Garage',              'Most popular. 2&ndash;4 day install. Resists everything.',        'services/residential#spaces'],
          ['basement', 'Basement',            'Moisture-tolerant. Turns storage into usable space.',             'services/residential#spaces'],
          ['patio',    'Patio / Pool Deck',   'UV-stable, anti-slip, barefoot-friendly.',                        'services/residential#spaces'],
          ['kitchen',  'Kitchen / Interior',  'Seamless, hygienic, high-end finish.',                            'services/residential#spaces'],
      ];
      foreach ($spaces as $space) {
          feature_card($space[0], $space[1], $space[2], 'See the detail', url($space[3]));
      }
      ?>
    </div>
  </div>
</section>

<!-- ===== What it costs, in brief ===== -->
<section class="sec sec--paper">
  <div class="wrap split">
    <div>
      <?php section_head('Budget', 'What People Actually Spend.', '', 'left'); ?>
      <p>A single garage usually lands between <strong><?= e(cfg('currency') . cfg('price_garage_1_low')) ?></strong> and <strong><?= e(cfg('currency') . cfg('price_garage_1_high')) ?></strong>; a double between <strong><?= e(cfg('currency') . cfg('price_garage_2_low')) ?></strong> and <strong><?= e(cfg('currency') . cfg('price_garage_2_high')) ?></strong>. Interior and decorative work is quoted per square metre.</p>
      <p>Where quotes differ wildly, it is nearly always prep. Ask every contractor to line-item it.</p>
      <div class="cta-row">
        <?= button('See Full Residential Pricing', url('services/residential') . '#costs', 'secondary') ?>
      </div>
    </div>
    <div class="figures reveal">
      <div class="figure"><span class="figure__num">2&ndash;4</span><span class="figure__label">Days on site</span></div>
      <div class="figure"><span class="figure__num">24h</span><span class="figure__label">To walk on it</span></div>
      <div class="figure"><span class="figure__num">7d</span><span class="figure__label">To park on it</span></div>
      <div class="figure"><span class="figure__num">15&ndash;20</span><span class="figure__label">Year lifespan</span></div>
    </div>
  </div>
</section>

<!-- ===== Honest homeowner FAQs ===== -->
<section class="sec">
  <div class="wrap" style="max-width:860px">
    <?php section_head('Honest answers', 'Homeowner FAQs.'); ?>
    <?php accordion($homeownerFaq); ?>
    <div class="cta-row" style="justify-content:center"><?= button('See All FAQs', url('faq'), 'ghost') ?></div>
  </div>
</section>

<?php faq_schema($homeownerFaq); ?>

<!-- ===== Three recent homes ===== -->
<section class="sec sec--paper">
  <div class="wrap">
    <?php section_head('Three recent homes', 'Residential Work, Start to Finish.'); ?>
    <div class="grid grid--3">
      <?php
      $residential = array_values(array_filter(projects(), static fn (array $p): bool => $p['tag'] === 'residential'));
      foreach (array_slice($residential, 0, 3) as $project) { project_card($project); }
      ?>
    </div>
    <div class="cta-row" style="justify-content:center"><?= button('View Full Portfolio', url('portfolio'), 'secondary') ?></div>
  </div>
</section>

<?php
echo '<section class="band"><div class="band__grid" aria-hidden="true"></div><div class="wrap band__inner">';
echo '<p class="eyebrow eyebrow--light reveal">Next step</p>';
echo '<h2 class="band__title reveal">Get a Number Before You Commit to Anything.</h2>';
echo ornament('light');
echo '<p class="band__lede reveal">Free on-site inspection, fixed-price written quote, no obligation.</p>';
echo '<div class="cta-row reveal">'
    . button('Get a Free Home Quote', url('quote'), 'primary')
    . button('WhatsApp a Photo of My Floor', whatsapp_link('Hello ' . cfg('company_name') . ', here is a photo of my floor - can you quote it?'), 'secondary', ['target' => '_blank', 'rel' => 'noopener'])
    . '</div>';
echo '</div></section>';
?>

<?php require __DIR__ . '/includes/footer.php'; ?>
