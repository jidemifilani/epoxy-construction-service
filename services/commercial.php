<?php
/** PAGE 4 - COMMERCIAL & INDUSTRIAL EPOXY */

require_once __DIR__ . '/../includes/functions.php';

$pageTitle       = 'Commercial Epoxy Flooring Nigeria | Warehouse & Factory Floors in Lagos';
$pageDescription = 'Industrial-grade epoxy flooring for warehouses, factories, showrooms, food and pharma, and healthcare facilities. Fast-cure options. ' . cfg('warranty_years') . '-year warranty.';
$pageKeywords    = 'commercial epoxy flooring Nigeria, industrial epoxy Lagos, warehouse epoxy Lagos, factory floor coating Nigeria, food-grade epoxy Nigeria';

$bannerEyebrow = 'Commercial & Industrial';
$bannerTitle   = 'Epoxy Floors Engineered for Downtime You Can&rsquo;t Afford.';
$bannerLede    = 'Forklifts. Chemicals. Hot wash-downs. Hygiene audits. Round-the-clock traffic. Your floor is infrastructure &mdash; and it has to perform like it. We spec and install systems rated for the exact punishment your facility delivers.';
$trail         = ['Services' => 'services/', 'Commercial & Industrial' => null];
$bannerCtas    = button('Request a Commercial Site Visit', url('quote'), 'primary')
               . button('Talk to the Spec Team', 'mailto:' . cfg('commercial_email'), 'ghost');

require __DIR__ . '/../includes/header.php';
?>

<?php trust_bar(); ?>

<!-- ===== Industries ===== -->
<section class="sec">
  <div class="wrap">
    <?php section_head('Industries we serve', 'Specialists Across Eight Sectors.'); ?>
    <div class="grid grid--4">
      <?php
      $sectors = [
          ['warehouse',   'Warehouses &amp; Logistics',          'Forklift-rated, line-marking integrated, dust-free.'],
          ['factory',     'Manufacturing &amp; Heavy Industry',  'Chemical, abrasion and thermal shock resistance.'],
          ['food',        'Food &amp; Beverage',                 'HACCP-aligned, seamless, antimicrobial options.'],
          ['pharma',      'Pharmaceutical &amp; Clean Rooms',    'ISO-class finishes, electrostatic dissipative (ESD) options.'],
          ['health',      'Healthcare &amp; Laboratories',       'Hygienic coved skirtings, infection-control finishes.'],
          ['auto',        'Automotive &amp; Workshops',          'Oil, fuel and brake-fluid resistant.'],
          ['retail',      'Showrooms &amp; Retail',              'High-gloss decorative with industrial durability underneath.'],
          ['hospitality', 'Hospitality &amp; Commercial Kitchens','Slip-resistant, grease-proof, food-safe.'],
      ];
      foreach ($sectors as $sector) {
          feature_card($sector[0], $sector[1], $sector[2]);
      }
      ?>
    </div>
  </div>
</section>

<!-- ===== Why facility managers choose us ===== -->
<section class="sec sec--dark">
  <div class="banner__grid" aria-hidden="true"></div>
  <div class="wrap" style="position:relative;z-index:1">
    <?php section_head('Why facility managers choose us', 'Six Operational Wins.'); ?>
    <div class="grid grid--3">
      <?php
      $wins = [
          ['Downtime-minimised installs.', 'Phased scheduling, night shifts, or fast-cure (polyaspartic) systems that return the floor to service in 24 hours.'],
          ['Resistance matched to your process.', 'Chemical and abrasion ratings set against what you actually run &mdash; not a generic spec.'],
          ['Line-marking &amp; safety zoning.', 'Installed under the topcoat so it never wears off.'],
          ['Antimicrobial, easy-sanitise finishes.', 'For food, pharma and healthcare environments.'],
          ['Reduced equipment wear.', 'Seamless, level surfaces are kinder to forklifts and pallet trucks.'],
          ['Documentation included.', 'Product data sheets, warranty, MSDS and application record for audits.'],
      ];
      foreach ($wins as $index => $win) {
          reason_card($index + 1, $win[0], $win[1]);
      }
      ?>
    </div>
  </div>
</section>

<!-- ===== Systems table ===== -->
<section class="sec">
  <div class="wrap">
    <?php section_head('Systems we install', 'Match the System to the Problem.',
        'Six builds cover almost every commercial brief. The right one is chosen on site, after the slab moisture test and a walk through your process.'); ?>

    <div class="table-wrap reveal">
      <table>
        <thead>
          <tr>
            <th scope="col">System</th>
            <th scope="col">Best For</th>
            <th scope="col">Thickness</th>
            <th scope="col">Cure Time</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach (systems_table() as $row): ?>
          <tr>
            <td><strong><?= e($row[0]) ?></strong></td>
            <td><?= e($row[1]) ?></td>
            <td><?= e($row[2]) ?></td>
            <td><?= e($row[3]) ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</section>

<!-- ===== Commercial process ===== -->
<section class="sec sec--paper">
  <div class="wrap split split--wide">
    <div>
      <?php section_head('Our commercial process', 'How We Keep Your Facility Running.', '', 'left'); ?>
      <p>Night and weekend shifts carry <strong>no premium</strong> on contracts above <?= e(num(cfg('night_shift_threshold'))) ?> m&sup2;.</p>
      <div class="cta-row">
        <?= button('Book a Facility Walkthrough', url('quote'), 'primary') ?>
      </div>
    </div>
    <?php
    step_list([
        ['Site survey', 'Slab moisture test, surface profile check and chemistry mapping of your process.'],
        ['Phased scope', 'Bay-by-bay or wing-by-wing scheduling to protect operations.'],
        ['Night / weekend shifts', 'Available at no premium on contracts above ' . num(cfg('night_shift_threshold')) . ' m&sup2;.'],
        ['Dust-contained grinding', 'HEPA vacuum systems. Zero cross-contamination with product zones.'],
        ['Documented handover', 'PDS, MSDS, warranty and application record for ISO / HACCP audits.'],
    ], true);
    ?>
  </div>
</section>

<!-- ===== Case studies ===== -->
<section class="sec">
  <div class="wrap">
    <?php section_head('Case studies', 'Three Facilities, Three Constraints.',
        'Each one scoped around the thing the client could not give up: production hours, audit compliance, or a launch date.'); ?>

    <div class="grid grid--3">
      <?php
      $cases = [
          ['Food Processing Plant', cfg('city'),
           'Replace failing tile grout in the wash-down zone without halting production.',
           '4mm epoxy mortar with coved skirtings, antimicrobial topcoat.',
           '36 hours, phased over two weekends.',
           'Passed HACCP audit on first inspection. 5 years, zero callbacks.'],
          ['Regional Distribution Hub', cfg('city'),
           'Coat 3,200 m&sup2; of warehouse floor with racking in place and trucks arriving daily.',
           'Solvent-free industrial epoxy with embedded line marking.',
           'Two weekends, bay-by-bay.',
           'No missed dispatch windows. Line marking still crisp at year three.'],
          ['Automotive Flagship Showroom', cfg('city'),
           'Deliver a display-grade floor with an embedded brand logo before a fixed launch date.',
           'Self-levelling epoxy with 3D graphic layer and high-gloss topcoat.',
           'Five night shifts.',
           'Handed over 48 hours ahead of launch.'],
      ];
      foreach ($cases as $case) {
          echo '<article class="card reveal">';
          echo '<h3>' . e($case[0]) . '</h3>';
          echo '<p class="project-card__meta">' . e($case[1]) . '</p>';
          echo '<dl class="spec" style="margin-top:.8rem">';
          foreach (['Challenge' => $case[2], 'System' => $case[3], 'Downtime' => $case[4], 'Result' => $case[5]] as $label => $value) {
              echo '<div class="spec__row"><dt>' . e($label) . '</dt><dd>' . $value . '</dd></div>';
          }
          echo '</dl>';
          echo corner_frame() . '</article>';
      }
      ?>
    </div>
    <p class="note reveal" style="margin-top:1.6rem">
      <strong>Content note for launch:</strong> swap these three for named client case studies once approvals are in.
      Keep the challenge / system / downtime / result structure &mdash; it is what facility managers scan for.
    </p>
  </div>
</section>

<?php
echo '<section class="band"><div class="band__grid" aria-hidden="true"></div><div class="wrap band__inner">';
echo '<p class="eyebrow eyebrow--light reveal">Next step</p>';
echo '<h2 class="band__title reveal">Let Us Walk the Floor Before We Quote It.</h2>';
echo ornament('light');
echo '<p class="band__lede reveal">Site survey, moisture test and a phased scope written around your production schedule.</p>';
echo '<div class="cta-row reveal">'
    . button('Book a Facility Walkthrough', url('quote'), 'primary')
    . button('Email Our Spec Team', 'mailto:' . cfg('commercial_email'), 'secondary')
    . '</div>';
echo '</div></section>';
?>

<?php require __DIR__ . '/../includes/footer.php'; ?>
