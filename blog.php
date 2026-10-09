<?php
/** PAGE 13 - BLOG / RESOURCES (SEO driver) */

require_once __DIR__ . '/includes/functions.php';

$pageTitle       = 'Epoxy Flooring Guides for Nigeria | Cost, Garage & Warehouse Advice';
$pageDescription = 'Guides, maintenance tips and project walk-throughs from ' . cfg('years') . '+ years of epoxy specialists.';
$pageKeywords    = 'epoxy flooring guides, epoxy blog, epoxy maintenance, epoxy cost guide';

$bannerEyebrow = 'Blog & resources';
$bannerTitle   = 'Guides From ' . cfg('years') . ' Years on Site.';
$bannerLede    = 'Twelve articles planned around the questions that actually get searched &mdash; pricing, comparisons, failure modes and maintenance. Each one has its outline and target keyword ready for drafting.';
$trail         = ['Blog' => null];

require __DIR__ . '/includes/header.php';

$all = articles();
$categories = array_values(array_unique(array_map(static fn (array $a): string => $a[2], $all)));
sort($categories);
?>

<section class="sec">
  <div class="wrap">

    <div>
      <div class="filters" data-filter-group data-filter-attr="data-group" data-filter-target=".post-card">
        <button class="filter is-active" type="button" data-value="all" aria-pressed="true">All</button>
        <?php foreach ($categories as $category): ?>
        <button class="filter" type="button" data-value="<?= e($category) ?>" aria-pressed="false"><?= e($category) ?></button>
        <?php endforeach; ?>
      </div>
      <p data-filter-status aria-live="polite" style="position:absolute;left:-9999px"></p>
    </div>

    <div class="grid grid--3">
      <?php foreach ($all as $index => $article):
          [$slug, $title, $category, $purpose] = $article;
      ?>
      <article class="card post-card reveal" data-group="<?= e($category) ?>">
        <p class="post-card__kicker"><?= e($category) ?> &middot; Article <?= $index + 1 ?></p>
        <h3><a href="<?= e(url('blog/' . $slug)) ?>"><?= e($title) ?></a></h3>
        <p class="post-card__note"><?= e($purpose) ?></p>
        <a class="link-arrow" href="<?= e(url('blog/' . $slug)) ?>">Read the outline<?= icon('arrow', 'link-arrow__icon') ?></a>
        <?= corner_frame() ?>
      </article>
      <?php endforeach; ?>
    </div>

    <p class="note reveal" style="margin-top:2rem">
      <strong>Content note for launch:</strong> these twelve are the launch plan from the website script, not finished posts.
      Each one opens a page carrying its target keyword, purpose and section outline &mdash; write the body into
      <code>includes/data.php</code> or move the articles to a database when the volume justifies it.
    </p>
  </div>
</section>

<section class="sec sec--paper sec--tight">
  <div class="wrap split">
    <div>
      <?php section_head('Why these twelve', 'Each One Targets a Different Intent.', '', 'left'); ?>
      <p>Price-intent, comparison, authority, bottom-funnel and industry-specific searches are all covered. Together they catch somebody at every stage &mdash; from &ldquo;what does this cost&rdquo; to &ldquo;which contractor do I call&rdquo;.</p>
      <div class="cta-row"><?= button('Get a Free Quote on Your Project', url('quote'), 'primary') ?></div>
    </div>
    <?php
    tick_list([
        '<strong>Price intent</strong> &mdash; cost guides with real ranges',
        '<strong>Comparison</strong> &mdash; epoxy against concrete, tile and paint',
        '<strong>Authority</strong> &mdash; why floors peel, what lifespan depends on',
        '<strong>Bottom-funnel</strong> &mdash; how to choose a contractor',
        '<strong>Industry</strong> &mdash; HACCP, warehouse downtime',
        '<strong>Maintenance</strong> &mdash; reduces post-install support load',
    ]);
    ?>
  </div>
</section>

<?php cta_band('Reading About It Is the Slow Way.', 'Book the free inspection and get the specific answer for your floor.'); ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
