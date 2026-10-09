<?php
/** PAGE 12 - TESTIMONIALS & REVIEWS */

require_once __DIR__ . '/includes/functions.php';

$pageTitle       = cfg('company_name') . ' Reviews - Real Clients, Real Projects';
$pageDescription = 'See what homeowners, businesses and architects say about working with ' . cfg('company_name') . '.';
$pageKeywords    = cfg('company_name') . ' reviews, epoxy flooring reviews ' . cfg('city');

$bannerEyebrow = 'Testimonials & reviews';
$bannerTitle   = num(cfg('review_count')) . '+ Reviews. Three Audience Groups. One Common Thread.';
$bannerLede    = 'We come back when they call. Filter by who left the review &mdash; homeowners, facility managers, or the architects and contractors who specify us.';
$trail         = ['Testimonials' => null];

require __DIR__ . '/includes/header.php';

$reviews = testimonials();

?>

<?php trust_bar(); ?>

<section class="sec">
  <div class="wrap">

    <div>
      <div class="filters" data-filter-group data-filter-attr="data-group" data-filter-target=".review-card">
        <?php
        $filters = [
            'all'        => 'All Reviews',
            'homeowners' => 'Homeowners',
            'businesses' => 'Businesses',
            'trade'      => 'Trade &amp; Architects',
        ];
        foreach ($filters as $value => $label) {
            $active = $value === 'all';
            echo '<button class="filter' . ($active ? ' is-active' : '') . '" type="button"'
                . ' data-value="' . e($value) . '" aria-pressed="' . ($active ? 'true' : 'false') . '">'
                . $label . '</button>';
        }
        ?>
      </div>
      <p data-filter-status aria-live="polite" style="position:absolute;left:-9999px"></p>
    </div>

    <div class="grid grid--3">
      <?php foreach ($reviews as $review) { review_card($review); } ?>
    </div>

    <p class="note reveal" style="margin-top:2rem">
      <strong>Content note for launch:</strong> replace these with real reviews before publishing, and add a
      <em>Video Reviews</em> filter once the first clips are recorded. Review names and roles live in
      <code>includes/data.php</code>; the <code>group</code> field is what the filter reads.
    </p>
  </div>
</section>

<section class="sec sec--paper sec--tight">
  <div class="wrap split">
    <div>
      <?php section_head('Review card template', 'What Every Review Shows.', '', 'left'); ?>
      <p>Each card carries the quote, the name, the role or location, a five-star rating and the project detail &mdash; type, size and system. The project line is what makes a review useful rather than decorative.</p>
      <div class="cta-row">
        <?= button('Read Google Reviews', '#', 'secondary') ?>
        <?= button('Request Case Study PDFs', url('contact') . '?topic=case-studies', 'ghost') ?>
      </div>
      <p class="field__hint" style="margin-top:.8rem">Point the first button at your Google Business Profile once it is claimed.</p>
    </div>
    <div class="card reveal">
      <h3>Fields</h3>
      <dl class="spec" style="margin-top:.8rem">
        <?php foreach ([
            'Quote'   => 'In the client&rsquo;s own words',
            'Name'    => 'First name and initial is enough',
            'Role'    => 'Or location, for homeowners',
            'Rating'  => 'Five stars',
            'Project' => 'Type, size, system installed',
            'Group'   => 'Homeowner / business / trade',
        ] as $label => $value) {
            echo '<div class="spec__row"><dt>' . $label . '</dt><dd>' . $value . '</dd></div>';
        }
        ?>
      </dl>
    </div>
  </div>
</section>


<?php cta_band('Want to Be the Next One?', 'Free on-site inspection and a fixed-price written quote within 48 hours.'); ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
