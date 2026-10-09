<?php
/** PAGE 2 - ABOUT US */

require_once __DIR__ . '/includes/functions.php';

$pageTitle       = 'About ' . cfg('company_name') . ' - ' . cfg('years') . '+ Years of Epoxy Specialists in ' . cfg('city');
$pageDescription = 'Meet the team behind ' . num(cfg('projects_completed')) . '+ completed epoxy projects across '
    . cfg('service_area') . '. Specialists only. Written warranty. Fully licensed.';

$bannerEyebrow = 'About us';
$bannerTitle   = 'We Don&rsquo;t Do Flooring. We Do <em style="font-style:italic;color:var(--green-400)">Epoxy</em>.';
$bannerLede    = 'That one decision changes everything &mdash; what we train for, what we invest in, what we are accountable for, and how long your floor lasts.';
$trail         = ['About' => null];

require __DIR__ . '/includes/header.php';
?>

<?php trust_bar(); ?>

<!-- ===== Our story ===== -->
<section class="sec">
  <div class="wrap split split--wide">
    <div>
      <?php section_head('Our story', 'Why ' . e(cfg('company_name')) . ' Exists.', '', 'left'); ?>
      <p><?= e(cfg('company_name')) ?> started in <?= e(cfg('founding_year')) ?> after our founder, <?= e(cfg('founder_name')) ?>, watched too many clients pay twice &mdash; once for an epoxy floor that failed, and again for us to fix it.</p>
      <p>The pattern was always the same: wrong prep, wrong product for the environment, no written warranty, and a contractor who had moved on by the time the floor peeled.</p>
      <p>So we built the opposite company. One where <strong>surface prep is non-negotiable</strong>, where <strong>the warranty is on paper</strong>, and where <strong>the team quoting is the team installing</strong>.</p>
      <p><?= e(cfg('years')) ?> years and <?= e(num(cfg('projects_completed'))) ?>+ projects later, that approach is still the reason clients call us back &mdash; and refer us to their neighbours, their suppliers and their architects.</p>
    </div>
    <div class="reveal">
      <div class="figures">
        <div class="figure"><span class="figure__num"><?= e(cfg('founding_year')) ?></span><span class="figure__label">Founded</span></div>
        <div class="figure"><span class="figure__num"><?= e(num(cfg('projects_completed'))) ?>+</span><span class="figure__label">Projects</span></div>
        <div class="figure"><span class="figure__num"><?= e(cfg('warranty_years')) ?></span><span class="figure__label">Year Warranty</span></div>
        <div class="figure"><span class="figure__num">0</span><span class="figure__label">Subcontracted Crews</span></div>
      </div>
      <p class="pull" style="margin-top:1.6rem">&ldquo;Prep is 70% of the job. Everything else is finishing.&rdquo;</p>
    </div>
  </div>
</section>

<!-- ===== What we believe ===== -->
<section class="sec sec--paper">
  <div class="wrap">
    <?php section_head('What we believe', 'Four Non-Negotiables.'); ?>
    <div class="grid grid--2">
      <?php
      $beliefs = [
          ['Prep is 70% of the job.', 'We diamond-grind to the correct concrete surface profile every time. No acid-etching. No shortcuts.'],
          ['The right system for the environment.', 'A warehouse floor is not a kitchen floor is not a hospital floor. We spec, not guess.'],
          ['Warranties should be written.', 'If we cannot put it on paper, we will not say it in the quote.'],
          ['One point of contact, start to finish.', 'You get a project lead &mdash; not a call centre.'],
      ];
      foreach ($beliefs as $index => $belief) {
          reason_card($index + 1, $belief[0], $belief[1]);
      }
      ?>
    </div>
  </div>
</section>

<!-- ===== Credentials ===== -->
<section class="sec sec--dark">
  <div class="banner__grid" aria-hidden="true"></div>
  <div class="wrap split" style="position:relative;z-index:1">
    <div>
      <?php section_head('Credentials', 'Licensed. Insured. Certified.', '', 'left'); ?>
      <p>Everything below can be produced on request as a pre-qualification pack &mdash; the same pack our commercial and trade clients submit to their own compliance teams.</p>
      <div class="cta-row"><?= button('Request the Pre-Qualification Pack', url('for-businesses') . '#procurement', 'primary') ?></div>
    </div>
    <?php
    tick_list([
        'Fully licensed contractor in ' . e(cfg('jurisdiction')),
        'Public liability insurance &mdash; up to ' . e(cfg('insurance_cover')),
        'Workers&rsquo; compensation coverage for all crew',
        e(cfg('cert_1')) . ' certified installers',
        e(cfg('cert_2')) . ' member in good standing',
        'Trained on ' . e(cfg('manufacturer_1')) . ' and ' . e(cfg('manufacturer_2')) . ' systems',
    ]);
    ?>
  </div>
</section>

<!-- ===== The team ===== -->
<section class="sec">
  <div class="wrap">
    <?php section_head('The team', 'Four People You Will Actually Meet.',
        'The same four names appear on your quote, your site visit and your handover. No account manager you never see again.'); ?>
    <div class="grid grid--4">
      <?php
      $team = [
          ['crew', cfg('founder_name'), 'Founder &amp; Principal', 'Started the company after years of repairing other contractors&rsquo; work. Still walks every commercial site survey.'],
          ['clock', 'Operations Lead', 'Scheduling &amp; Crews', 'Owns the programme. Phases installs around your operation so the floor never costs you a shift.'],
          ['grind', 'Lead Installer', 'Prep &amp; Application', 'Runs the grinders and signs off every surface profile before a drop of primer goes down.'],
          ['doc', 'Project Manager', 'Your Single Contact', 'One number from quote to warranty handover, including the documentation pack.'],
      ];
      foreach ($team as $member) {
          echo '<article class="card feature-card reveal">';
          echo '<span class="feature-card__icon">' . icon($member[0], 'icon icon--lg') . '</span>';
          echo '<h3>' . $member[1] . '</h3>';
          echo '<p class="project-card__meta">' . $member[2] . '</p>';
          echo '<p>' . $member[3] . '</p>';
          echo corner_frame() . '</article>';
      }
      ?>
    </div>
    <p class="note reveal" style="margin-top:1.6rem">
      <strong>Content note for launch:</strong> replace these four cards with real photographs and two-line bios.
      The card markup already carries the right aspect ratio and heading levels.
    </p>
  </div>
</section>

<!-- ===== Our guarantee ===== -->
<section class="sec sec--paper-2">
  <div class="wrap">
    <div class="panel reveal">
      <p class="eyebrow">Our guarantee</p>
      <h2 style="font-size:clamp(1.5rem,3vw,2.1rem)">The <?= e(cfg('warranty_years')) ?>-Year <?= e(cfg('company_name')) ?> Guarantee.</h2>
      <p>If your floor delaminates, bubbles, peels or discolours under normal use within <?= e(cfg('warranty_years')) ?> years, we come back and fix it. No excuses. No invoices.</p>
      <p><strong>That is not marketing language &mdash; it is in your contract.</strong></p>
      <div class="cta-row"><?= button('See the Full Warranty Terms', url('terms') . '#warranty', 'secondary') ?></div>
    </div>
  </div>
</section>

<?php cta_band('Want to Work With a Specialist?', 'Free on-site inspection, anywhere in ' . e(cfg('service_area')) . '.', 'Book Your Free Site Inspection'); ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
