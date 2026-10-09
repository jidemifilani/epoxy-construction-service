<?php
/** PAGE 11 - FAQ */

require_once __DIR__ . '/includes/functions.php';

$pageTitle       = 'Epoxy Flooring FAQ Nigeria | Cost, Durability & Maintenance';
$pageDescription = 'Common questions about epoxy flooring answered honestly. Pricing, installation time, maintenance, DIY vs. professional, and more.';
$pageKeywords    = 'epoxy flooring faq, epoxy cost, epoxy durability, epoxy maintenance, epoxy vs polyaspartic';

$bannerEyebrow = 'FAQ';
$bannerTitle   = 'Honest Answers, Including the Unhelpful Ones.';
$bannerLede    = 'Fifteen questions across pricing, durability, maintenance, timelines and the technical detail. Where the honest answer is &ldquo;it depends&rdquo;, we say what it depends on.';
$trail         = ['FAQ' => null];

require __DIR__ . '/includes/header.php';

$groups = faq_groups();
?>

<section class="sec">
  <div class="wrap split split--wide" style="align-items:start">
    <div style="max-width:760px">
      <?php accordion($groups, true); ?>
    </div>

    <div class="rail">
      <div class="rail__card reveal">
        <h3>Jump to a section</h3>
        <ul class="foot__links" style="margin:0">
          <?php foreach (array_keys($groups) as $name): ?>
          <li><a href="#<?= e(slug($name)) ?>"><?= $name ?></a></li>
          <?php endforeach; ?>
        </ul>
      </div>

      <div class="rail__card reveal">
        <h3>Still have questions?</h3>
        <p style="font-size:.93rem">Our technical team answers specification questions directly &mdash; no sales handover.</p>
        <div class="cta-row" style="flex-direction:column;align-items:stretch;margin-top:1rem">
          <?= button('WhatsApp Our Technical Team', whatsapp_link('Hello ' . cfg('company_name') . ', I have a technical question about epoxy flooring.'), 'primary', ['target' => '_blank', 'rel' => 'noopener']) ?>
          <?= button('Email a Question', 'mailto:' . cfg('email'), 'ghost') ?>
        </div>
      </div>

      <div class="rail__card reveal">
        <h3>Related reading</h3>
        <ul class="foot__links" style="margin:0">
          <li><a href="<?= e(url('process')) ?>">The seven-step process</a></li>
          <li><a href="<?= e(url('services/residential')) ?>#costs">Residential pricing table</a></li>
          <li><a href="<?= e(url('services/commercial')) ?>">Commercial system comparison</a></li>
          <li><a href="<?= e(url('blog')) ?>">Guides &amp; resources</a></li>
        </ul>
      </div>
    </div>
  </div>
</section>

<?php faq_schema(flatten_faq($groups)); ?>

<?php cta_band('Question Answered? Let&rsquo;s Price the Floor.', 'Free on-site inspection and a fixed-price written quote within 48 hours.'); ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
