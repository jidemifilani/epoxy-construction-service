<?php
/**
 * XML sitemap, served at /sitemap.xml by the rewrite in .htaccess.
 *
 * Built from the live database, so a new article, photo or video appears here
 * as soon as it is published. Includes image and video entries, so photos and
 * films can be found in image and video search.
 *
 * Never listed: cart, checkout, order confirmation, thank-you, admin, and any
 * page the SEO admin has marked noindex, or any unpublished article.
 */

require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/seo.php';
require_once __DIR__ . '/includes/media.php';
require_once __DIR__ . '/includes/locations.php';

header('Content-Type: application/xml; charset=utf-8');

/** path => [changefreq, priority] */
$pages = [
    ''                                  => ['weekly',  '1.0'],
    'shop'                              => ['weekly',  '0.9'],
    'quote'                             => ['monthly', '0.9'],
    'services/residential'              => ['monthly', '0.9'],
    'services/commercial'               => ['monthly', '0.9'],
    'services/decorative'               => ['monthly', '0.9'],
    'services/'                         => ['monthly', '0.8'],
    'garage-epoxy-floor-lagos'          => ['monthly', '0.9'],
    'epoxy-flooring-lagos'              => ['monthly', '0.9'],
    'commercial-epoxy-flooring-nigeria' => ['monthly', '0.9'],
    'epoxy-floor-cost-nigeria'          => ['monthly', '0.9'],
    'buy-epoxy-resin-nigeria'           => ['monthly', '0.9'],
    'portfolio'                         => ['weekly',  '0.8'],
    'gallery'                           => ['weekly',  '0.8'],
    'process'                           => ['monthly', '0.8'],
    'about'                             => ['monthly', '0.7'],
    'for-homeowners'                    => ['monthly', '0.7'],
    'for-businesses'                    => ['monthly', '0.7'],
    'for-architects'                    => ['monthly', '0.7'],
    'faq'                               => ['monthly', '0.7'],
    'testimonials'                      => ['monthly', '0.6'],
    'blog'                              => ['weekly',  '0.7'],
    'contact'                           => ['monthly', '0.8'],
    'privacy'                           => ['yearly',  '0.2'],
    'terms'                             => ['yearly',  '0.2'],
];

// Pages the SEO admin has hidden are left out.
$hidden = [];
try {
    foreach (db_rows('SELECT path FROM seo_pages WHERE noindex = 1') as $row) {
        $hidden[trim((string) $row['path'], '/')] = true;
    }
} catch (Throwable $e) {
    // No SEO table yet: nothing is hidden.
}
foreach (array_keys($hidden) as $path) {
    unset($pages[$path]);
}

// Location pages, from the same list the areas hub shows.
foreach (location_all() as $location) {
    $pages['epoxy-flooring/' . $location['slug']] = ['monthly', $location['coverage'] === 'standard' ? '0.8' : '0.6'];
}
$pages['areas'] = ['monthly', '0.7'];
$pages['careers']  = ['weekly', '0.7'];
$pages['training'] = ['monthly', '0.7'];
try {
    foreach (db_rows("SELECT slug FROM jobs WHERE published = 1 AND status = 'open'") as $job) {
        $pages['careers/' . $job['slug']] = ['weekly', '0.7'];
    }
} catch (Throwable $e) {
    // No jobs table yet.
}

// Published articles only. Articles without text are noindex and stay out.
$articleEntries = [];
foreach (articles() as $article) {
    if (trim((string) $article[5]) !== '') {
        $articleEntries['blog/' . $article[0]] = ['monthly', '0.6'];
    }
}

$photos = media_list('image');
$videos = media_list('video');
$today  = date('Y-m-d');

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1"
        xmlns:video="http://www.google.com/schemas/sitemap-video/1.1">
<?php foreach (array_merge($pages, $articleEntries) as $path => [$freq, $priority]): ?>
  <url>
    <loc><?= e(absolute_url($path)) ?></loc>
    <lastmod><?= $today ?></lastmod>
    <changefreq><?= $freq ?></changefreq>
    <priority><?= $priority ?></priority>
<?php if ($path === 'gallery'): ?>
<?php foreach ($photos as $photo): if (empty($photo['file_path'])) { continue; } ?>
    <image:image>
      <image:loc><?= e(absolute_url((string) $photo['file_path'])) ?></image:loc>
      <image:title><?= e($photo['title']) ?></image:title>
      <image:caption><?= e($photo['caption'] !== '' ? $photo['caption'] : $photo['alt_text']) ?></image:caption>
    </image:image>
<?php endforeach; ?>
<?php foreach ($videos as $video): ?>
    <video:video>
      <video:thumbnail_loc><?= e(absolute_url((string) ($video['thumbnail'] ?? 'assets/img/social-card.png'))) ?></video:thumbnail_loc>
      <video:title><?= e($video['title']) ?></video:title>
      <video:description><?= e($video['caption'] !== '' ? $video['caption'] : $video['title']) ?></video:description>
<?php if (!empty($video['video_url'])): ?>
      <video:player_loc><?= e((string) media_embed_url((string) $video['video_url'])) ?></video:player_loc>
<?php else: ?>
      <video:content_loc><?= e(absolute_url((string) $video['file_path'])) ?></video:content_loc>
<?php endif; ?>
      <video:publication_date><?= e(date('c', strtotime((string) $video['created_at']))) ?></video:publication_date>
    </video:video>
<?php endforeach; ?>
<?php endif; ?>
  </url>
<?php endforeach; ?>
</urlset>
