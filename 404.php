<?php
/**
 * Branded 404, wired up through ErrorDocument in .htaccess.
 *
 * article.php also includes this file directly for an unknown blog slug, so it
 * must not set a status code of its own -- the caller has already done that.
 */

require_once __DIR__ . '/includes/functions.php';

if (!headers_sent() && http_response_code() === 200) {
    http_response_code(404);
}

$pageTitle       = 'Page Not Found - ' . cfg('company_name');
$pageDescription = 'That page does not exist. Here is where to go instead.';
$noindex = true;   // private or transactional: never shown in search

require __DIR__ . '/includes/header.php';
?>

<section class="sec">
  <div class="wrap notfound">
    <p class="notfound__code reveal">404</p>
    <h1 class="reveal" style="font-size:clamp(1.7rem,3.4vw,2.6rem)">That Page Has Been Ground Off.</h1>
    <?= ornament() ?>
    <p class="reveal" style="max-width:52ch;margin-inline:auto">
      The link is wrong or the page has moved. The quickest routes back are below &mdash;
      or call <a href="<?= e(tel_link()) ?>"><?= e(cfg('phone')) ?></a> and we will point you at it.
    </p>
    <div class="cta-row" style="justify-content:center">
      <?= button('Back to Home', url(), 'primary') ?>
      <?= button('See the Portfolio', url('portfolio'), 'secondary') ?>
      <?= button('Get a Free Quote', url('quote'), 'ghost') ?>
    </div>
  </div>
</section>

<section class="sec sec--paper sec--tight">
  <div class="wrap">
    <?php section_head('Popular pages', 'Where Most People Were Heading.'); ?>
    <div class="grid grid--3">
      <?php
      feature_card('garage', 'Residential Epoxy', 'Garages, basements, patios and interiors.', 'Go there', url('services/residential'));
      feature_card('factory', 'Commercial &amp; Industrial', 'Warehouses, food, pharma and healthcare.', 'Go there', url('services/commercial'));
      feature_card('sparkle', 'Decorative &amp; Designer', 'Metallic, 3D, flake and marble-effect.', 'Go there', url('services/decorative'));
      ?>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
