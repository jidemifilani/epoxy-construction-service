<?php
/**
 * Terms of service, including the warranty terms the About page links to.
 *
 * Not legal advice -- have a lawyer review it against your contracts and the
 * consumer law of your jurisdiction before publishing.
 */

require_once __DIR__ . '/includes/functions.php';

$pageTitle       = 'Terms of Service - ' . cfg('company_name');
$pageDescription = 'The terms on which ' . cfg('company_name') . ' quotes, installs and warrants epoxy flooring.';

$bannerEyebrow = 'Legal';
$bannerTitle   = 'Terms of Service';
$bannerLede    = 'Last updated ' . date('F Y') . '.';
$trail         = ['Terms of Service' => null];

require __DIR__ . '/includes/header.php';
?>

<section class="sec">
  <div class="wrap legal prose reveal">

    <p class="note"><strong>Before launch:</strong> these terms restate the commitments the website makes, so they are a
      faithful starting point &mdash; but they must be reconciled with your actual contract and reviewed by a lawyer
      familiar with <?= e(cfg('jurisdiction')) ?>.</p>

    <h2>Quotes</h2>
    <ul>
      <li>On-site inspections are free and carry no obligation.</li>
      <li>Quotes are issued in writing, line-itemed, within 48 hours of the inspection.</li>
      <li>A quoted price is held for 30 days from the date of issue.</li>
      <li>A quote is fixed for the scope it describes. Work discovered after the floor is opened up &mdash; a failed
        slab, undisclosed moisture, a structural crack &mdash; is quoted separately before it is carried out.</li>
    </ul>

    <h2>Payment</h2>
    <ul>
      <li>Residential: typically 50% deposit, 50% on completion.</li>
      <li>Commercial: milestone billing, agreed before work starts.</li>
      <li>No deposit is required to receive a quote.</li>
    </ul>

    <h2>Your responsibilities</h2>
    <ul>
      <li>Clear access to the area, or ask us to quote moving and returning contents.</li>
      <li>Power and water on site.</li>
      <li>Keeping the area free of traffic during cure. Cure times are given at handover in writing.</li>
    </ul>

    <h2 id="warranty">The <?= e(cfg('warranty_years')) ?>-year warranty</h2>
    <p>We warrant, in writing and as part of your contract, that for <?= e(cfg('warranty_years')) ?> years from handover
      your floor will not delaminate, bubble, peel or discolour under normal use. If it does, we return and repair it at
      no cost to you.</p>

    <h3>What normal use means</h3>
    <p>The use the floor was specified for, as recorded in your quote &mdash; domestic traffic, forklift traffic, wash-down,
      and so on. A system specified for a garage and then used as a chemical store is outside the warranty.</p>

    <h3>What the warranty does not cover</h3>
    <ul>
      <li>Structural movement of the slab beneath the coating.</li>
      <li>Impact damage, gouging, or dragging heavy equipment without protection.</li>
      <li>Chemical exposure beyond the specified resistance of the system installed.</li>
      <li>Damage from cleaning with abrasives or strong solvents, against the aftercare guide.</li>
      <li>Work altered, recoated or repaired by anyone else.</li>
    </ul>

    <h3>Making a claim</h3>
    <p>Email <a href="mailto:<?= e(cfg('email')) ?>"><?= e(cfg('email')) ?></a> with your project reference and photos.
      We inspect within 10 working days and, where the claim is covered, schedule the repair.</p>

    <h2>Cancellation</h2>
    <p>Cancel before materials are ordered and your deposit is refunded in full. After that, we deduct committed
      material and mobilisation costs and refund the balance.</p>

    <h2>Liability</h2>
    <p>We carry public liability insurance up to <?= e(cfg('insurance_cover')) ?> and workers' compensation cover for all
      crew. Certificates are available on request.</p>

    <h2>Governing law</h2>
    <p>These terms are governed by the laws of <?= e(cfg('jurisdiction')) ?>.</p>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
