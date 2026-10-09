<?php
/** CAREERS - open roles. Each one links to /careers/<slug> for the full posting and the apply form. */

require_once __DIR__ . '/includes/functions.php';

$jobs = open_jobs();

$pageTitle       = 'Careers at ' . cfg('company_name') . ' | Jobs in Epoxy Flooring, Lagos';
$pageDescription = 'Open roles at ' . cfg('company_name') . ': site supervisor, epoxy floor installers, and specialist flooring and procurement roles in Lagos. Apply online with your CV.';
$pageKeywords    = 'epoxy flooring jobs Lagos, flooring installer jobs Nigeria, site supervisor job Lagos, careers ' . cfg('company_name');

$bannerEyebrow = 'Careers';
$bannerTitle   = 'Build Floors That Outlast the Building.';
$bannerLede    = 'We are specialists, not generalists, and that starts with who we hire. Below are the roles open right now.';
$trail         = ['Careers' => null];

require __DIR__ . '/includes/header.php';
?>

<section class="sec">
  <div class="wrap">
    <?php section_head('Working with us', 'What the Job Actually Involves.',
        'Every project runs on the same discipline: correct preparation, a written process, and a crew that does the job once, properly. These roles are part of that crew, on site across ' . e(cfg('service_area')) . '.'); ?>

    <?php
    tick_list([
        'Trained on our own systems and process, not left to work it out on site',
        'PPE and safety equipment provided for every role that needs it',
        'Work across residential, commercial and decorative projects, not just one kind of floor',
        'An in-house crew &mdash; we do not subcontract the work our name goes on',
    ], 'ticks--2col');
    ?>
  </div>
</section>

<section class="sec sec--paper">
  <div class="wrap">
    <?php section_head('Open roles', $jobs === [] ? 'Nothing Open Right Now.' : count($jobs) . ' Position' . (count($jobs) === 1 ? '' : 's') . ' Open.'); ?>

    <?php if ($jobs === []): ?>
      <div class="card reveal" style="max-width:640px;margin:0 auto;text-align:center">
        <h3>No open roles at the moment</h3>
        <p>Check back soon, or send a speculative application with your CV to <a href="mailto:<?= e(cfg('email')) ?>"><?= e(cfg('email')) ?></a>.</p>
      </div>
    <?php else: ?>
    <div class="grid grid--3">
      <?php foreach ($jobs as $job): ?>
      <article class="card feature-card reveal">
        <span class="feature-card__icon"><?= icon('crew', 'icon icon--lg') ?></span>
        <h3><a href="<?= e(url('careers/' . $job['slug'])) ?>"><?= e($job['title']) ?></a></h3>
        <p class="project-card__meta"><?= e($job['department']) ?> &middot; <?= e($job['employment_type']) ?></p>
        <p><?= e($job['summary']) ?></p>
        <a class="link-arrow" href="<?= e(url('careers/' . $job['slug'])) ?>">View and apply<?= icon('arrow', 'link-arrow__icon') ?></a>
        <?= corner_frame() ?>
      </article>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
</section>

<?php cta_band('Know Someone Who Would Be Good at This?', 'Share this page, or email a CV even if nothing above fits yet.', 'Email Your CV', 'mailto:' . cfg('email')); ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
