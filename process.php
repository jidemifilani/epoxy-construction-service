<?php
/** PAGE 6 - OUR PROCESS */

require_once __DIR__ . '/includes/functions.php';

$pageTitle       = 'Epoxy Installation Process Nigeria | Diamond Grinding & Surface Prep';
$pageDescription = 'The exact 7-step process we use on every epoxy project. From site inspection to warranty handover - nothing skipped.';
$pageKeywords    = 'epoxy installation process, diamond grinding, concrete surface profile, epoxy surface preparation';

$bannerEyebrow = 'Our process';
$bannerTitle   = 'Why Our Floors Don&rsquo;t Peel: The Seven-Step Process.';
$bannerLede    = 'Most epoxy failures happen in step 3. We do step 3 &mdash; and the six around it &mdash; every time, without shortcuts.';
$trail         = ['Our Process' => null];
$bannerCtas    = button('Start With a Free Site Inspection', url('quote'), 'primary');

require __DIR__ . '/includes/header.php';
?>

<?php trust_bar(); ?>

<!-- ===== The seven steps ===== -->
<section class="sec">
  <div class="wrap" style="max-width:900px">
    <?php section_head('The seven steps', 'Every Project, Every Time.'); ?>

    <ol class="steps">
      <?php foreach (process_steps() as $index => $step): ?>
      <li class="steps__item reveal" id="step-<?= $index + 1 ?>">
        <span class="steps__num" aria-hidden="true"><?= str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) ?></span>
        <div class="steps__content">
          <h3>Step <?= $index + 1 ?> &mdash; <?= $step[0] ?></h3>
          <p><?= $step[1] ?></p>
          <?php if ($index === 2): ?>
          <p class="note" style="margin-top:.9rem">
            This is the step that determines whether your floor lasts 2 years or 20. If a quote you are
            comparing does not mention diamond grinding, ask what it does instead.
          </p>
          <?php endif; ?>
        </div>
      </li>
      <?php endforeach; ?>
    </ol>
  </div>
</section>

<!-- ===== Never skip / never do ===== -->
<section class="sec sec--paper">
  <div class="wrap split">
    <div class="panel reveal">
      <p class="eyebrow">What we never skip</p>
      <h2 style="font-size:1.5rem">Five Steps That Are Not Optional.</h2>
      <?php
      tick_list([
          'Diamond grinding',
          'Moisture testing',
          'Crack repair',
          'Written quotes',
          'Written warranties',
      ]);
      ?>
    </div>

    <div class="panel reveal" style="border-left-color:#c0392b;background:#fdf1ef">
      <p class="eyebrow" style="color:#c0392b">What we never do</p>
      <h2 style="font-size:1.5rem">Four Shortcuts We Will Not Take.</h2>
      <?php
      cross_list([
          'Acid-etch instead of grinding (fails under traffic)',
          'Spray-apply roller-grade coatings (manufacturers will not honour it)',
          'Sub-contract the installation to an unknown third party',
          'Quote verbally and bill differently',
      ]);
      ?>
    </div>
  </div>
</section>

<!-- ===== What this buys you ===== -->
<section class="sec sec--dark">
  <div class="banner__grid" aria-hidden="true"></div>
  <div class="wrap split" style="position:relative;z-index:1">
    <div>
      <?php section_head('What the process buys you', 'A Floor You Stop Thinking About.', '', 'left'); ?>
      <p>Prep is the only variable that reliably predicts lifespan. Everything in the seven steps above exists to make the bond between the concrete and the coating mechanical rather than hopeful.</p>
      <div class="cta-row">
        <?= button('Get My Free Quote', url('quote'), 'primary') ?>
        <?= button('See the Work', url('portfolio'), 'ghost') ?>
      </div>
    </div>
    <div class="figures reveal">
      <div class="figure"><span class="figure__num">70%</span><span class="figure__label">Of the job is prep</span></div>
      <div class="figure"><span class="figure__num">48h</span><span class="figure__label">Quote turnaround</span></div>
      <div class="figure"><span class="figure__num"><?= e(cfg('warranty_years')) ?></span><span class="figure__label">Year written warranty</span></div>
      <div class="figure"><span class="figure__num">95%+</span><span class="figure__label">Grinding dust contained</span></div>
    </div>
  </div>
</section>

<?php cta_band('Start With a Free Site Inspection.', 'We measure, test the slab and tell you what it actually needs &mdash; before any money changes hands.', 'Book My Site Inspection'); ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
