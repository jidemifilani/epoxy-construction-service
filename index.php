<?php
/** PAGE 1 - HOME */

require_once __DIR__ . '/includes/functions.php';

$pageTitle       = 'Epoxy Flooring Lagos, Nigeria | ' . cfg('company_name');
$pageDescription = 'Transform your floors with industrial-grade epoxy from ' . cfg('company_name') . '. '
    . cfg('years') . '+ years, ' . num(cfg('projects_completed')) . '+ projects, ' . cfg('warranty_years')
    . '-year warranty. Free quotes across ' . cfg('service_area') . '. Call ' . cfg('phone') . '.';
$pageKeywords    = 'epoxy flooring Lagos, epoxy flooring Nigeria, epoxy contractor Lagos, garage epoxy Lagos, commercial epoxy flooring Nigeria, metallic epoxy floor Lagos, epoxy floor cost Nigeria';

require __DIR__ . '/includes/header.php';
?>

<!-- ===== Hero ===== -->
<section class="hero">
  <div class="hero__art"><?= hero_artwork() ?></div>
  <div class="wrap">
    <div class="hero__inner">
      <p class="hero__eyebrow reveal">Epoxy specialists in <?= e(cfg('city')) ?></p>
      <h1 class="hero__title reveal">Floors That <em>Outlast</em> the Building Around Them.</h1>
      <p class="hero__lede reveal">
        Industrial-grade epoxy coatings for homes, businesses and design-driven spaces across
        <?= e(cfg('service_area')) ?>. Backed by a written <strong><?= e(cfg('warranty_years')) ?>-year warranty</strong>.
      </p>
      <div class="cta-row reveal">
        <?= button('Get My Free On-Site Quote', url('quote'), 'primary') ?>
        <?= button('See Our Work', url('portfolio'), 'ghost') ?>
      </div>
      <ul class="hero__cues reveal">
        <li><?= icon('grind') ?> Diamond-ground prep on every job</li>
        <li><?= icon('price') ?> Fixed-price written quotes</li>
        <li><?= icon('crew') ?> In-house crews, never subcontracted</li>
      </ul>
    </div>
  </div>
</section>

<!-- ===== Section 1 - Trust bar ===== -->
<?php trust_bar(); ?>

<!-- ===== Section 2 - What we do ===== -->
<section class="sec">
  <div class="wrap">
    <?php section_head(
        'What we do',
        'One Material. Three Ways We Make It Work for You.',
        'Epoxy is only as good as the hands applying it. We are specialists &mdash; not a general contractor dabbling in floors. Every project goes through the same disciplined process, whether we are coating a 60 m&sup2; garage or a 6,000 m&sup2; warehouse.'
    ); ?>

    <div class="grid grid--3">
      <?php
      feature_card('garage', '&#127968; Residential Epoxy',
          'Garage floors, basements, patios, pool decks, kitchens and interior living spaces. Scratch-resistant, chemical-proof and seamless.',
          'Explore Residential', url('services/residential'));
      feature_card('factory', '&#127981; Commercial &amp; Industrial',
          'Warehouses, factories, showrooms, hospitals and food-grade facilities. Engineered for traffic, chemicals, hygiene and uptime.',
          'Explore Commercial', url('services/commercial'));
      feature_card('sparkle', '&#127912; Decorative &amp; Designer',
          'Metallic, 3D, flake, marble-effect and custom graphic floors for architects, designers and homeowners who want a statement.',
          'Explore Decorative', url('services/decorative'));
      ?>
    </div>
  </div>
</section>

<!-- ===== Section 3 - Why us ===== -->
<section class="sec sec--dark">
  <div class="banner__grid" aria-hidden="true"></div>
  <div class="wrap" style="position:relative;z-index:1">
    <?php section_head('Why ' . cfg('company_name'), 'Six Reasons Clients Keep Calling Us Back.'); ?>

    <div class="grid grid--3">
      <?php
      $reasons = [
          ['Specialists, Not Generalists.', 'Epoxy is the only thing we do. ' . cfg('years') . ' years of it.'],
          ['Written ' . cfg('warranty_years') . '-Year Warranty.', 'On adhesion, delamination and finish &mdash; in writing, before you sign.'],
          ['Zero-Shortcut Prep.', 'We grind to the correct profile every time. Peeling floors come from skipped prep &mdash; never from us.'],
          ['Transparent, Fixed-Price Quotes.', 'No surprise invoices. You see the full cost before we lift a tool.'],
          ['Minimal Downtime.', 'Fast-cure systems available for businesses that cannot afford to close.'],
          ['In-House Crews.', 'We do not subcontract. The team quoting the job is the team doing the job.'],
      ];
      foreach ($reasons as $index => $reason) {
          reason_card($index + 1, $reason[0], $reason[1]);
      }
      ?>
    </div>
  </div>
</section>

<!-- ===== Section 4 - Audience router ===== -->
<section class="sec sec--paper">
  <div class="wrap">
    <?php section_head('Who we serve', 'Which One Sounds Like You?'); ?>

    <div class="grid grid--3">
      <?php
      $routes = [
          ['A', 'I am a Homeowner', 'I want my garage, basement or patio to look premium and last 20 years.', 'Visit the Homeowner Hub', 'for-homeowners'],
          ['B', 'I Run a Business or Facility', 'I need a floor that handles traffic, chemicals or hygiene standards &mdash; with the shortest possible shutdown.', 'Visit the Business Hub', 'for-businesses'],
          ['C', 'I am an Architect, Builder or Contractor', 'I need spec sheets, samples, timelines and a reliable partner on tender projects.', 'Visit the Trade Hub', 'for-architects'],
      ];
      foreach ($routes as $route) {
          echo '<article class="card router-card reveal">';
          echo '<p class="router-card__label">Card ' . e($route[0]) . '</p>';
          echo '<h3>' . $route[1] . '</h3>';
          echo '<p class="router-card__quote">&ldquo;' . $route[2] . '&rdquo;</p>';
          echo '<a class="link-arrow" href="' . e(url($route[4])) . '">' . e($route[3]) . icon('arrow', 'link-arrow__icon') . '</a>';
          echo corner_frame() . '</article>';
      }
      ?>
    </div>
  </div>
</section>

<!-- ===== Section 5 - Process snapshot ===== -->
<section class="sec">
  <div class="wrap split split--wide">
    <div>
      <?php section_head('Our process', 'How a ' . e(cfg('company_name')) . ' Project Runs.', '', 'left'); ?>
      <p>Five stages, every time, on a 30 m&sup2; garage and a 6,000 m&sup2; warehouse alike. The full seven-step method is documented in detail &mdash; including the two steps most contractors skip.</p>
      <div class="cta-row"><?= button('See the Full Process', url('process'), 'secondary') ?></div>
    </div>
    <?php
    step_list([
        ['Free Site Inspection', 'We measure, test the slab and discuss your goals.'],
        ['Fixed-Price Quote', 'Line-itemed, with your chosen system and finish.'],
        ['Diamond-Grinding Prep', 'The step 90% of failed epoxy jobs skip.'],
        ['Application', 'Primer, base, decorative layer and sealed topcoat.'],
        ['Cure &amp; Handover', 'Walkthrough, aftercare guide and your written warranty.'],
    ], true);
    ?>
  </div>
</section>

<!-- ===== Section 6 - Featured projects ===== -->
<section class="sec sec--paper">
  <div class="wrap">
    <?php section_head('Recent work', 'Projects We Would Show You In Person.'); ?>
    <?php home_slideshow(projects()); ?>
    <div class="cta-row" style="justify-content:center"><?= button('View Full Portfolio', url('portfolio'), 'secondary') ?></div>
  </div>
</section>

<!-- ===== Section 7 - Testimonials ===== -->
<section class="sec">
  <div class="wrap">
    <?php section_head('What clients say', 'Reviews from Real Projects.'); ?>
    <div class="grid grid--3">
      <?php foreach (array_slice(testimonials(), 0, 3) as $review) { review_card($review); } ?>
    </div>
    <div class="cta-row" style="justify-content:center"><?= button('Read More Reviews', url('testimonials'), 'ghost') ?></div>
  </div>
</section>

<!-- ===== Section 8 - FAQ snippet ===== -->
<section class="sec sec--paper-2">
  <div class="wrap split">
    <div>
      <?php section_head('Common questions', 'Answers Before You Ask.', '', 'left'); ?>
      <p>The four questions that come up on nearly every site visit. Another fifteen are answered on the FAQ page &mdash; pricing, durability, maintenance, timelines and the technical detail.</p>
      <div class="cta-row"><?= button('See All FAQs', url('faq'), 'secondary') ?></div>
    </div>
    <div><?php accordion(faq_snippet()); ?></div>
  </div>
</section>

<?php faq_schema(faq_snippet()); ?>

<!-- ===== Section 9 - Final CTA ===== -->
<?php cta_band(
    'Ready for a Floor That Finally Matches the Rest of Your Space?',
    'Free, no-pressure on-site inspection. Fixed-price quote within 48 hours.'
); ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
