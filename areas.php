<?php
/** AREAS - where we quote epoxy flooring, grouped by region. Links to each location page. */

require_once __DIR__ . '/includes/landing.php';
require_once __DIR__ . '/includes/locations.php';

$pageTitle       = 'Epoxy Flooring Areas in Nigeria | Lagos, Ibadan, Abuja and More';
$pageDescription = 'Where we quote epoxy flooring in Nigeria: Lagos and Lekki, Ogun, Ibadan, Ibeju-Lekki and other cities and states. See which places are standard coverage and which are quoted per project.';
$pageKeywords    = 'epoxy flooring Nigeria, epoxy flooring Lagos, epoxy flooring Abuja, epoxy flooring Ibadan, epoxy flooring Port Harcourt, epoxy flooring Kano, epoxy flooring Ogun';

$bannerEyebrow = 'Areas we cover';
$bannerTitle   = 'Epoxy Flooring Across Nigeria.';
$bannerLede    = 'Lagos and the South-West are our standard service area. Other states are quoted project by project, with travel and site logistics included in the price.';
$trail         = ['Areas we cover' => null];

require __DIR__ . '/includes/header.php';

$grouped = [];
foreach (location_all() as $location) {
    $grouped[$location['region']][] = $location;
}
?>

<section class="sec">
  <div class="wrap">
    <?php section_head('Lagos and the South-West', 'Our Standard Service Area.',
        'Projects in these places are part of our standard service area. Use the page for your place to send the space and size.'); ?>
    <div class="grid grid--3">
      <?php
      echo '<article class="card feature-card reveal">';
      echo '<span class="feature-card__icon">' . icon('pin', 'icon icon--lg') . '</span>';
      echo '<h3><a href="' . e(url('epoxy-flooring-lagos')) . '">Lagos</a></h3>';
      echo '<p>Lagos, Lekki, Festac, Amuwo-Odofin and Ibeju-Lekki.</p>';
      echo '<a class="link-arrow" href="' . e(url('epoxy-flooring-lagos')) . '">Epoxy flooring in Lagos' . icon('arrow', 'link-arrow__icon') . '</a>';
      echo corner_frame() . '</article>';
      ?>
    </div>
  </div>
</section>

<?php foreach (LOCATION_REGIONS as $region => $heading):
    if (empty($grouped[$region])) { continue; }
    $standard = $region === 'Lagos' || $region === 'South-West';
?>
<section class="sec <?= $standard ? 'sec--paper' : '' ?>">
  <div class="wrap">
    <?php section_head($standard ? 'Standard service area' : 'Quoted per project', $heading,
        $standard ? '' : 'Projects here are quoted individually. The quote includes travel and site logistics.'); ?>
    <div class="grid grid--3">
      <?php foreach ($grouped[$region] as $location): ?>
      <article class="card feature-card reveal">
        <span class="feature-card__icon"><?= icon('pin', 'icon icon--lg') ?></span>
        <h3><a href="<?= e(url('epoxy-flooring/' . $location['slug'])) ?>"><?= e($location['name']) ?></a></h3>
        <p><?= e($location['coverage'] === 'standard' ? 'Standard service area' : 'Quoted per project') ?></p>
        <a class="link-arrow" href="<?= e(url('epoxy-flooring/' . $location['slug'])) ?>">Epoxy flooring in <?= e($location['name']) ?><?= icon('arrow', 'link-arrow__icon') ?></a>
        <?= corner_frame() ?>
      </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endforeach; ?>

<?php cta_band('Not Sure Whether We Cover Your Place?', 'Send us the location and the space. We will tell you straight away whether we can quote it, and what it would involve.', 'Ask About My Area'); ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
