<?php
/**
 * Privacy policy.
 *
 * This describes what this website actually does with a submission, which is
 * the only honest basis for the page. It is not legal advice -- have it
 * reviewed against the data-protection law of your jurisdiction before launch.
 */

require_once __DIR__ . '/includes/functions.php';

$pageTitle       = 'Privacy Policy - ' . cfg('company_name');
$pageDescription = 'How ' . cfg('company_name') . ' collects, uses and stores the information you send through this website.';

$bannerEyebrow = 'Legal';
$bannerTitle   = 'Privacy Policy';
$bannerLede    = 'Last updated ' . date('F Y') . '.';
$trail         = ['Privacy Policy' => null];

require __DIR__ . '/includes/header.php';
?>

<section class="sec">
  <div class="wrap legal prose reveal">

    <p class="note"><strong>Before launch:</strong> have this reviewed against the data-protection law that applies to
      <?= e(cfg('jurisdiction')) ?>. The sections below describe what the site genuinely does, so a reviewer has
      accurate facts to work from rather than boilerplate.</p>

    <h2>Who we are</h2>
    <p><?= e(cfg('company_name')) ?>, <?= e(cfg('office_address')) ?>. You can reach us at
      <a href="mailto:<?= e(cfg('email')) ?>"><?= e(cfg('email')) ?></a> or <?= e(cfg('phone')) ?>.</p>

    <h2>What we collect</h2>
    <p>Only what you type into one of our forms: your name, email address, phone or WhatsApp number, and the details
      you give us about your project &mdash; the type of space, its approximate size, its current floor, the finish you
      are interested in, your location and your preferred timeline.</p>
    <p>We do not ask for payment details anywhere on this website.</p>

    <h2>Why we collect it</h2>
    <p>To respond to your enquiry, prepare a quote, and arrange a site inspection. That is the only purpose.</p>

    <h2>How it is stored</h2>
    <p>Submissions are written to a file on the server hosting this website, and &mdash; where email notification is
      enabled &mdash; sent to our own inbox. They are not sold, rented or shared with third parties for marketing.</p>

    <h2>How long we keep it</h2>
    <p>Enquiries are kept for as long as needed to quote and deliver the work, and for the duration of any warranty
      arising from it. You can ask us to delete your details at any time.</p>

    <h2>Cookies and advertising</h2>
    <p>This website uses a session cookie, which keeps your cart and form submissions linked to you and protects the
      forms against cross-site request forgery. It is removed when you close your browser.</p>
    <p>When you arrive from an advert, the link carries campaign details (for example the advert source and a
      click identifier). We keep these in your session so that an enquiry or order can be traced back to the
      advert that produced it. We do not use them to identify you outside this website.</p>
    <p>When the site's analytics and advertising accounts are connected, Google Analytics, Google Ads and the Meta
      Pixel may set their own cookies to measure visits, show our adverts, and record enquiries and orders. These
      are only loaded once those accounts are configured by us. You can control them through your browser settings
      or the platforms' own privacy tools.</p>

    <h2>Your rights</h2>
    <ul>
      <li>Ask what we hold about you.</li>
      <li>Ask us to correct it.</li>
      <li>Ask us to delete it.</li>
      <li>Ask us to stop contacting you.</li>
    </ul>
    <p>Email <a href="mailto:<?= e(cfg('email')) ?>"><?= e(cfg('email')) ?></a> and we will action it.</p>

    <h2>Changes to this policy</h2>
    <p>If this policy changes we will update the date at the top of this page.</p>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
