<?php
/** GALLERY - photos and videos of finished floors, from the media library. */

require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/seo.php';

$photos = media_list('image');
$videos = media_list('video');

$pageTitle       = 'Epoxy Floor Photos and Videos, Lagos Nigeria | ' . cfg('company_name');
$pageDescription = 'Photos and videos of epoxy floors we have installed across Lagos and the South-West: garages, warehouses, showrooms and metallic and decorative finishes.';
$pageKeywords    = 'epoxy floor photos Lagos, epoxy floor videos Nigeria, garage epoxy photos, warehouse epoxy Nigeria, metallic epoxy photos';

$bannerEyebrow = 'Gallery';
$bannerTitle   = 'Our Floors, in Photos and Film.';
$bannerLede    = 'Real installs across Lagos and the South-West. Each photo and film shows the finished floor; the project pages show the system, size and timeline.';
$trail         = ['Gallery' => null];

$schemaNodes = [
    schema_breadcrumbs([['Home', ''], ['Gallery', 'gallery']]),
];
foreach (array_slice($photos, 0, 30) as $photo) {
    if (!empty($photo['file_path'])) {
        $schemaNodes[] = schema_image($photo);
    }
}
foreach ($videos as $video) {
    $schemaNodes[] = schema_video($video + ['video_embed_url' => media_embed_url((string) $video['video_url'])]);
}

require __DIR__ . '/includes/header.php';
?>

<section class="sec">
  <div class="wrap">
    <?php section_head('Photos', 'Finished Floors.'); ?>

    <?php if ($photos === []): ?>
      <p class="admin-empty" style="text-align:center">Photos are being added. Check back soon, or ask about a recent install.</p>
    <?php else: ?>
    <div class="gallery-grid">
      <?php foreach ($photos as $photo): ?>
      <figure class="gallery-item reveal">
        <?= media_img($photo, '(min-width: 1000px) 33vw, (min-width: 600px) 50vw, 100vw') ?>
        <?php if ($photo['caption'] !== ''): ?><figcaption><?= e($photo['caption']) ?></figcaption><?php endif; ?>
      </figure>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
</section>

<section class="sec sec--paper">
  <div class="wrap">
    <?php section_head('Videos', 'Films of the Work.', 'Walk-throughs of finished floors, and the stages of a typical install.'); ?>

    <?php if ($videos === []): ?>
      <p class="admin-empty" style="text-align:center">Videos are being added. Check back soon.</p>
    <?php else: ?>
    <div class="grid grid--2">
      <?php foreach ($videos as $video): ?>
      <figure class="card video-card reveal">
        <?= media_video($video) ?>
        <figcaption>
          <h3><?= e($video['title']) ?></h3>
          <?php if ($video['caption'] !== ''): ?><p><?= e($video['caption']) ?></p><?php endif; ?>
        </figcaption>
      </figure>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
</section>

<?php cta_band('Want a Floor Like This in Your Space?', 'Send us a photo of your floor. We will tell you the system, the range and the next step.', 'Request a Free Quote'); ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
