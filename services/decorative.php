<?php
/** PAGE 5 - DECORATIVE & DESIGNER EPOXY */

require_once __DIR__ . '/../includes/functions.php';

$pageTitle       = 'Metallic Epoxy Floors Lagos, Nigeria | Decorative & 3D Epoxy';
$pageDescription = 'Statement decorative epoxy floors. Metallic, 3D, flake, marble-effect and custom-graphic systems installed by certified artisans. Portfolio online.';
$pageKeywords    = 'metallic epoxy floor Lagos, metallic epoxy Nigeria, 3D epoxy floor Nigeria, decorative epoxy Lagos, marble effect epoxy Nigeria, designer epoxy floors';

$bannerEyebrow = 'Decorative & Designer';
$bannerTitle   = 'Floors That Make the Room.';
$bannerLede    = 'When the floor is the design feature &mdash; not the backdrop &mdash; the install is a different craft. Our decorative systems are hand-finished by artisans trained specifically on metallic, 3D and multi-layer techniques.';
$trail         = ['Services' => 'services/', 'Decorative & Designer' => null];
$bannerCtas    = button('Book a Design Consultation', url('quote'), 'primary')
               . button('See the Decorative Portfolio', url('portfolio') . '#decorative', 'ghost');

require __DIR__ . '/../includes/header.php';
?>

<!-- ===== Seven signature finishes ===== -->
<section class="sec">
  <div class="wrap">
    <?php section_head('Decorative systems we offer', 'Seven Signature Finishes.'); ?>
    <div class="grid grid--3">
      <?php
      $finishes = [
          ['metallic', 'sparkle',   '&#10024; Metallic Epoxy',          'Liquid-metal pigments manipulated into one-of-a-kind flow patterns. Every floor is unique. Available in copper, silver, pearl, bronze and 20+ custom blends.'],
          ['3d',       'cube',      '&#129482; 3D Epoxy',               'A printed image layered beneath crystal-clear epoxy. Rivers, beaches, galaxies, brand graphics or custom artwork &mdash; up to any size.'],
          ['flake',    'flake',     '&#127806; Flake / Chip Systems',   'Decorative vinyl flakes broadcast into the base coat. Textured, anti-slip and incredibly durable. Hides minor slab imperfections.'],
          ['marble',   'marble',    '&#129704; Marble-Effect Epoxy',    'Hand-veined to mimic Carrara, Calacatta, Nero Marquina or any marble you specify. At a fraction of real stone cost.'],
          ['quartz',   'quartz',    '&#128142; Quartz &amp; Decorative Aggregate', 'Coloured quartz granules in a clear binder. Formal, durable and widely used in hotels and showrooms.'],
          ['logo',     'logofloor', '&#127912; Custom Graphic &amp; Logo Floors',  'Brand logos, wayfinding, courts and bespoke art embedded permanently under a protective topcoat.'],
          ['terrazzo', 'terrazzo',  '&#127963; Terrazzo-Style Epoxy',   'Modern reinterpretation of classic terrazzo &mdash; faster, lighter and more affordable than a traditional pour.'],
      ];
      foreach ($finishes as $finish) {
          echo '<article class="card swatch reveal">';
          echo '<div class="swatch__art">' . finish_swatch($finish[0]) . '</div>';
          echo '<div class="swatch__body"><h3>' . icon($finish[1]) . '<span>' . $finish[2] . '</span></h3>';
          echo '<p>' . $finish[3] . '</p></div>';
          echo corner_frame() . '</article>';
      }
      ?>
    </div>
  </div>
</section>

<!-- ===== Who this is for ===== -->
<section class="sec sec--paper">
  <div class="wrap split">
    <div>
      <?php section_head('Who this is for', 'Five Kinds of Brief.', '', 'left'); ?>
      <p class="pull">Portfolio-grade finishes, with the spec documentation to match.</p>
      <div class="cta-row"><?= button('Visit the Trade Hub', url('for-architects'), 'secondary') ?></div>
    </div>
    <?php
    tick_list([
        '<strong>Homeowners</strong> &mdash; statement entryways, living areas and media rooms.',
        '<strong>Hospitality</strong> &mdash; hotels, restaurants, caf&eacute;s, boutique retail.',
        '<strong>Showrooms</strong> &mdash; automotive, furniture, luxury retail.',
        '<strong>Event spaces</strong> &mdash; galleries, lounges, event centres.',
        '<strong>Architects &amp; interior designers</strong> &mdash; portfolio-grade finishes with spec documentation.',
    ]);
    ?>
  </div>
</section>

<!-- ===== Design consultation process ===== -->
<section class="sec sec--dark">
  <div class="banner__grid" aria-hidden="true"></div>
  <div class="wrap split" style="position:relative;z-index:1">
    <div>
      <?php section_head('Design consultation process', 'From Mood Board to Finished Floor.', '', 'left'); ?>
      <p>Decorative work is specified visually, not from a product code. Nothing goes down until you have signed off on a physical sample &mdash; and, if you want one, a test panel in the actual space.</p>
      <div class="cta-row"><?= button('Book My Design Consultation', url('quote'), 'primary') ?></div>
    </div>
    <?php
    step_list([
        ['Discovery call', 'Share your inspiration, space photos and timeline.'],
        ['Sample boards', 'Up to three physical samples delivered or shown on site.'],
        ['On-site mock-up', 'Optional, premium: a 1 m&sup2; test panel in your space.'],
        ['Final design sign-off', 'Colour, pattern direction and finish gloss level.'],
        ['Installation', 'Scheduled for the lowest-dust, lowest-disruption window.'],
    ], true);
    ?>
  </div>
</section>

<!-- ===== Finish and colour options ===== -->
<section class="sec">
  <div class="wrap">
    <?php section_head('Finish & colour options', 'The Library You Choose From.'); ?>
    <div class="grid grid--4">
      <?php
      $options = [
          ['Pigment library', '60+ metallic and solid tones', ['Copper', 'Silver', 'Pearl', 'Bronze', 'Graphite', 'Slate blue', 'Emerald']],
          ['Flake blends', '25+ pre-mixed and fully custom', ['Domino', 'Slate', 'Shoreline', 'Terracotta', 'Monochrome', 'Custom mix']],
          ['Gloss levels', 'Three standard sheens', ['Matte', 'Satin', 'High-gloss']],
          ['Topcoat options', 'Chosen for the environment', ['Standard', 'UV-stable', 'Anti-slip', 'Antimicrobial']],
      ];
      foreach ($options as $option) {
          echo '<article class="card reveal">';
          echo '<h3>' . e($option[0]) . '</h3>';
          echo '<p class="project-card__meta">' . e($option[1]) . '</p>';
          echo '<ul class="chips" style="margin-top:.9rem">';
          foreach ($option[2] as $chip) {
              echo '<li>' . e($chip) . '</li>';
          }
          echo '</ul>';
          echo corner_frame() . '</article>';
      }
      ?>
    </div>
    <p class="note reveal" style="margin-top:1.6rem">
      <strong>Content note for launch:</strong> this grid is the placeholder for the interactive colour swatch picker
      or the downloadable spec sheet. Real pigment and flake photography slots straight into the chip list.
    </p>
  </div>
</section>

<!-- ===== Decorative projects ===== -->
<section class="sec sec--paper">
  <div class="wrap">
    <?php section_head('Recent decorative work', 'Three Floors That Carried the Room.'); ?>
    <div class="grid grid--3">
      <?php
      $decorative = array_values(array_filter(projects(), static fn (array $p): bool => in_array($p['art'], ['metallic', 'showroom', 'entry'], true)));
      foreach ($decorative as $project) { project_card($project); }
      ?>
    </div>
  </div>
</section>

<?php
echo '<section class="band"><div class="band__grid" aria-hidden="true"></div><div class="wrap band__inner">';
echo '<p class="eyebrow eyebrow--light reveal">Next step</p>';
echo '<h2 class="band__title reveal">Start With Three Samples in Your Hand.</h2>';
echo ornament('light');
echo '<p class="band__lede reveal">Design consultation, physical sample boards and an optional mock-up panel before anything is committed.</p>';
echo '<div class="cta-row reveal">'
    . button('Book My Design Consultation', url('quote'), 'primary')
    . button('Request the Decorative Lookbook', url('contact') . '?topic=lookbook', 'secondary')
    . '</div>';
echo '</div></section>';
?>

<?php require __DIR__ . '/../includes/footer.php'; ?>
