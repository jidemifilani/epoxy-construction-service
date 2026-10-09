<?php
/**
 * Post-submit confirmation. The script asks for expected response time, a
 * WhatsApp quick-contact button and links to the portfolio / blog.
 *
 * robots.txt disallows this page, as the script specifies.
 */

require_once __DIR__ . '/includes/functions.php';

start_session_if_needed();
$kind = $_SESSION['lead_kind'] ?? '';
$name = $_SESSION['lead_name'] ?? '';
unset($_SESSION['lead_kind'], $_SESSION['lead_name']);

$heading = $name !== ''
    ? 'Thank you, ' . e(explode(' ', $name)[0]) . '. We have it.'
    : 'Thank you. We have it.';

$pageTitle       = 'Thank You - ' . cfg('company_name');
$pageDescription = 'Your enquiry has been received. We respond within one business day.';
$noindex = true;   // private or transactional: never shown in search

$bannerEyebrow = 'Enquiry received';
$bannerTitle   = $heading;
$bannerLede    = match (true) {
    $kind === 'quote'       => 'A project coordinator will call within two hours during business hours to confirm the details, then book your free on-site inspection.',
    $kind === 'application' => 'Thank you for applying. We review every application and will contact you if your experience matches what the role needs.',
    default                 => 'We respond to every enquiry within one business day &mdash; usually within a few hours.',
};

require __DIR__ . '/includes/header.php';
?>

<section class="sec">
  <div class="wrap">
    <?php section_head('What happens next', 'Your Timeline From Here.'); ?>

    <div class="grid grid--4">
      <?php
      $steps = match (true) {
          $kind === 'quote' => [
              ['phone',    'Within 2 hours',           'A project coordinator confirms the details with you.'],
              ['calendar', 'Within 48 hours',          'Your free on-site inspection is scheduled.'],
              ['doc',      'Within 48 hours of visit', 'Your line-itemed written quote arrives by email.'],
              ['shield',   '30 days',                  'Your quote stays locked at that price.'],
          ],
          $kind === 'application' => [
              ['doc',    'Received',      'Your application and CV are saved and visible to the hiring team.'],
              ['clock',  'Reviewed',      'We check your experience against what the role needs.'],
              ['chat',   'Shortlisting',  'Shortlisted applicants are contacted by phone or WhatsApp.'],
              ['shield', 'Decision',      'We let every shortlisted applicant know the outcome either way.'],
          ],
          default => [
              ['phone',    'Within one business day',  'Someone from the team replies &mdash; usually within a few hours.'],
              ['chat',     'Faster if you need it',    'WhatsApp is the quickest route, especially with photos.'],
              ['calendar', 'Free inspection',          'Booked at a time that suits you, with no obligation.'],
              ['doc',      'Written quote',            'Fixed price, line-itemed, within 48 hours of the visit.'],
          ],
      };
      foreach ($steps as $index => $step) {
          echo '<article class="card reason-card reveal">';
          echo '<span class="reason-card__num">' . str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) . '</span>';
          echo '<span class="feature-card__icon">' . icon($step[0], 'icon icon--lg') . '</span>';
          echo '<h3>' . $step[1] . '</h3><p>' . $step[2] . '</p>';
          echo corner_frame() . '</article>';
      }
      ?>
    </div>

    <div class="cta-row" style="justify-content:center;margin-top:2.4rem">
      <?= button('WhatsApp Us Now', whatsapp_link('Hello ' . cfg('company_name') . ', I just submitted an enquiry on your website.'), 'primary', ['target' => '_blank', 'rel' => 'noopener']) ?>
      <?= button('Call ' . cfg('phone'), tel_link(), 'secondary') ?>
    </div>
  </div>
</section>

<section class="sec sec--paper">
  <div class="wrap">
    <?php section_head('While you wait', 'Two Things Worth a Look.'); ?>
    <div class="grid grid--2">
      <?php
      if ($kind === 'application') {
          feature_card('crew', 'About the Company',
              'Who we are and what we believe about doing the job properly, from prep to warranty.',
              'Read about us', url('about'));
          feature_card('doc', 'Other Open Roles',
              'See the other positions open right now, in case one suits someone you know.',
              'View all roles', url('careers'));
      } else {
          feature_card('star', 'The Portfolio',
              'Completed projects with the system, finish and timeline for each one. Find something close to your space and mention it when we call.',
              'View the portfolio', url('portfolio'));
          feature_card('doc', 'Guides &amp; Resources',
              'Pricing guides, honest comparisons and the nine questions worth asking any epoxy contractor &mdash; including us.',
              'Read the guides', url('blog'));
      }
      ?>
    </div>
  </div>
</section>

<?= tracking_conversion('lead') ?>
<?php require __DIR__ . '/includes/footer.php'; ?>
