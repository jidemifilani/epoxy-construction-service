<?php
/** Services overview - the optional parent page from the script's site map. */

require_once __DIR__ . '/../includes/functions.php';

$pageTitle       = 'Epoxy Flooring Services Lagos | Residential, Commercial, Decorative';
$pageDescription = 'Three service lines, one standard of prep: residential epoxy, commercial and industrial systems, and decorative designer finishes across ' . cfg('service_area') . '.';
$pageKeywords    = 'epoxy flooring services, epoxy contractor ' . cfg('city') . ', industrial epoxy, decorative epoxy';

$bannerEyebrow = 'Services';
$bannerTitle   = 'One Material. Three Service Lines.';
$bannerLede    = 'The same disciplined prep underneath all three. What changes is the system we spec on top of it &mdash; and that choice is driven entirely by how the space gets used.';
$trail         = ['Services' => null];

require __DIR__ . '/../includes/header.php';
?>

<section class="sec">
  <div class="wrap">
    <div class="grid grid--3">
      <?php
      $lines = [
          ['garage', 'Residential Epoxy', 'Garages, basements, patios, pool decks, kitchens and interior living spaces. Industrial formulas adapted for the look and feel of a home.',
              ['15-20 year floor', 'Seamless, no grout lines', 'Colour, flake and metallic options'], 'services/residential'],
          ['factory', 'Commercial &amp; Industrial', 'Warehouses, factories, food and pharma, healthcare, automotive and retail. Rated for the exact punishment your facility delivers.',
              ['Fast-cure, 24-hour return to service', 'Line marking under the topcoat', 'Documentation for ISO and HACCP audits'], 'services/commercial'],
          ['sparkle', 'Decorative &amp; Designer', 'Metallic, 3D, flake, marble-effect, quartz, custom graphics and terrazzo-style. Hand-finished by artisans trained on each technique.',
              ['60+ pigments, 25+ flake blends', 'Physical sample boards', 'Optional on-site mock-up panel'], 'services/decorative'],
      ];
      foreach ($lines as $line) {
          echo '<article class="card feature-card reveal">';
          echo '<span class="feature-card__icon">' . icon($line[0], 'icon icon--lg') . '</span>';
          echo '<h3>' . $line[1] . '</h3><p>' . $line[2] . '</p>';
          echo '<ul class="ticks" style="margin:.4rem 0 1.2rem">';
          foreach ($line[3] as $point) {
              echo '<li>' . icon('check', 'ticks__icon') . '<span>' . $point . '</span></li>';
          }
          echo '</ul>';
          echo '<a class="link-arrow" href="' . e(url($line[4])) . '">Explore this service' . icon('arrow', 'link-arrow__icon') . '</a>';
          echo corner_frame() . '</article>';
      }
      ?>
    </div>
  </div>
</section>

<section class="sec sec--paper sec--tight">
  <div class="wrap split">
    <div>
      <?php section_head('Not sure which applies', 'Tell Us How the Space Gets Used.', '', 'left'); ?>
      <p>You do not need to know the system name. Describe the traffic, the chemicals, the hygiene standard and the look you want &mdash; specifying the right build is our job, and we do it on site before quoting.</p>
      <div class="cta-row">
        <?= button('Get a Free Quote', url('quote'), 'primary') ?>
        <?= button('See Our Process', url('process'), 'ghost') ?>
      </div>
    </div>
    <?php
    tick_list([
        'Diamond-ground prep on every system, without exception',
        'Fixed-price written quote within 48 hours of the site visit',
        e(cfg('warranty_years')) . '-year written warranty on adhesion, delamination and finish',
        'In-house crews &mdash; the team quoting is the team installing',
    ]);
    ?>
  </div>
</section>

<?php cta_band('Which Floor Are We Quoting?', 'Free on-site inspection. Fixed-price quote within 48 hours.'); ?>

<?php require __DIR__ . '/../includes/footer.php'; ?>
