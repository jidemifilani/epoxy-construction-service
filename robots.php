<?php
/**
 * robots.txt, served at /robots.txt by the rewrite in .htaccess.
 *
 * Public pages, photos and videos are open. Private and transactional pages,
 * the admin and the application internals are closed. Crawl-delay is not set:
 * Google ignores it, and Bing respects a modest value only if you need one.
 */

require_once __DIR__ . '/includes/functions.php';

header('Content-Type: text/plain; charset=utf-8');

$base = rtrim(base_path(), '/');
?>
User-agent: *
Allow: /
Allow: /assets/uploads/media/
Disallow: <?= $base ?>/admin/
Disallow: <?= $base ?>/cart
Disallow: <?= $base ?>/checkout
Disallow: <?= $base ?>/order-placed
Disallow: <?= $base ?>/thank-you
Disallow: <?= $base ?>/includes/
Disallow: <?= $base ?>/config/
Disallow: <?= $base ?>/database/
Disallow: <?= $base ?>/storage/

Sitemap: <?= absolute_url('sitemap.xml') ?>
