<?php
/** PAGE 7 - PORTFOLIO / GALLERY */

require_once __DIR__ . '/includes/functions.php';

$pageTitle       = 'Epoxy Flooring Projects in Lagos, Nigeria | Portfolio';
$pageDescription = 'Browse real completed epoxy flooring projects across ' . cfg('service_area') . '. Residential, commercial and decorative installs with system and timeline detail.';
$pageKeywords    = 'epoxy flooring portfolio, epoxy projects ' . cfg('city') . ', metallic epoxy gallery';

$bannerEyebrow = 'Portfolio';
$bannerTitle   = num(cfg('projects_completed')) . '+ Projects. One Standard.';
$bannerLede    = 'Filter by the kind of space you have. Every card lists the system, the finish and how long it took, because those are the three things that actually transfer to your project.';
$trail         = ['Portfolio' => null];

require __DIR__ . '/includes/header.php';
?>

<section class="sec">
  <div class="wrap">

    <div>
      <div class="filters" data-filter-group data-filter-attr="data-tag" data-filter-target=".project-card">
        <?php
        $filters = ['all' => 'All', 'residential' => 'Residential', 'commercial' => 'Commercial', 'decorative' => 'Decorative'];
        foreach ($filters as $value => $label) {
            $active = $value === 'all';
            echo '<button class="filter' . ($active ? ' is-active' : '') . '" type="button"'
                . ' data-value="' . e($value) . '" aria-pressed="' . ($active ? 'true' : 'false') . '">'
                . e($label) . '</button>';
        }
        ?>
      </div>
      <p class="sr-status" data-filter-status aria-live="polite" style="position:absolute;left:-9999px"></p>
    </div>

    <div class="grid grid--3">
      <?php foreach (projects() as $project) { project_card($project); } ?>
    </div>

    <p class="note reveal" style="margin-top:2rem">
      <strong>Content note for launch:</strong> each card is built for a before image, an after image and a detail shot.
      Add them to <code>assets/img/projects/&lt;slug&gt;/</code> and swap <code>project_art()</code> in
      <code>includes/components.php</code> for an <code>&lt;img&gt;</code> &mdash; the aspect ratio and hover zoom already match.
      Alt text should name the finish and the location, e.g. &ldquo;Metallic epoxy garage floor, <?= e(cfg('city')) ?> residence&rdquo;.
    </p>
  </div>
</section>

<!-- ===== Project card template, documented for whoever adds real entries ===== -->
<section class="sec sec--paper sec--tight">
  <div class="wrap split">
    <div>
      <?php section_head('Project card template', 'What Each Entry Records.', '', 'left'); ?>
      <p>Every project is logged against the same six fields in <code>includes/data.php</code>. Keeping the shape identical is what lets the filter, the schema and the related-project blocks all read from one list.</p>
      <div class="cta-row"><?= button('Discuss a Similar Project', url('quote'), 'primary') ?></div>
    </div>
    <div class="card reveal">
      <h3>Fields</h3>
      <dl class="spec" style="margin-top:.8rem">
        <?php
        foreach ([
            'Project name' => 'and location',
            'Space'        => 'Garage / warehouse / showroom / etc.',
            'Size'         => 'In m&sup2;',
            'System'       => 'The exact build installed',
            'Finish'       => 'Metallic blue, grey flake, and so on',
            'Timeline'     => 'Days or shifts on site',
        ] as $label => $value) {
            echo '<div class="spec__row"><dt>' . $label . '</dt><dd>' . $value . '</dd></div>';
        }
        ?>
      </dl>
    </div>
  </div>
</section>

<?php cta_band('See Something Close to Your Space?', 'Tell us which project it was and we will quote the same build on your floor.', 'Discuss a Similar Project'); ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
