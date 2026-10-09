<?php
/**
 * Shared document head, utility bar, sticky nav and inner-page banner.
 *
 * A page sets any of these before including this file.
 *
 * PLAIN TEXT -- escaped on output, so write "Decorative & Designer", never
 * "Decorative &amp; Designer", or the entity is escaped a second time and the
 * visitor reads the "&amp;":
 *   $pageTitle        string  browser title, without the company name
 *   $pageDescription  string  meta description
 *   $pageKeywords     string  comma-separated target keywords from the script
 *   $bannerEyebrow    string  small label above the banner H1
 *   $trail            array   ['Services' => 'services/', 'Residential' => null]
 *   $bodyClass        string  extra class on <body>
 *
 * MARKUP -- echoed as-is, so entities and inline tags are expected here:
 *   $bannerTitle      string  when set, renders the dark page banner (H1)
 *   $bannerLede       string  supporting paragraph under the banner H1
 *   $bannerCtas       string  pre-rendered buttons shown inside the banner
 */

declare(strict_types=1);

// functions.php pulls in data.php, artwork.php and components.php itself.
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/shop.php';   // cart_count() for the header badge
require_once __DIR__ . '/seo.php';
require_once __DIR__ . '/tracking.php';

start_session_if_needed();
tracking_capture();   // remember which advert brought this visitor, if any

$pageTitle       = $pageTitle       ?? '';
$pageDescription = $pageDescription ?? 'Industrial-grade epoxy flooring for homes, businesses and design-driven spaces across ' . cfg('service_area') . '.';
$pageKeywords    = $pageKeywords    ?? '';
$bannerTitle     = $bannerTitle     ?? '';
$bannerEyebrow   = $bannerEyebrow   ?? '';
$bannerLede      = $bannerLede      ?? '';
$bannerCtas      = $bannerCtas      ?? '';
$trail           = $trail           ?? [];
$bodyClass       = $bodyClass       ?? '';

$company   = cfg('company_name');
$flashes   = take_flashes();

// Search and social metadata. An SEO-admin override for this page wins over
// what the page sets; a page can also ask for noindex with $noindex = true.
$seoOverride = seo_override(current_path());
$seoTitle    = !empty($seoOverride['title'])       ? $seoOverride['title']       : ($pageTitle !== '' ? $pageTitle : $company . ' | ' . cfg('tagline') . ' in ' . cfg('city') . ', Nigeria');
$seoDesc     = !empty($seoOverride['description']) ? $seoOverride['description'] : $pageDescription;
$seoKeywords = !empty($seoOverride['keywords'])    ? $seoOverride['keywords']    : $pageKeywords;
$seoNoindex  = !empty($seoOverride['noindex']) || !empty($noindex);
$seoType     = $seoType ?? 'website';
$seoImage    = $seoImage ?? null;
$schemaNodes = $schemaNodes ?? [];   // extra JSON-LD nodes a page adds

$fullTitle   = $seoTitle;
$pageTitle   = $seoTitle;
$pageDescription = $seoDesc;
$pageKeywords    = $seoKeywords;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<!-- Marks the document as scripted. The reveal and multi-step styles are
     scoped to html.js, so if the script never loads nothing stays hidden. -->
<script>document.documentElement.classList.add('js');</script>
<title><?= e($fullTitle) ?></title>
<?= seo_head_tags([
    'title'       => $fullTitle,
    'description' => $pageDescription,
    'keywords'    => $pageKeywords,
    'noindex'     => $seoNoindex,
    'canonical'   => $canonicalPath ?? null,
    'type'        => $seoType,
    'image'       => $seoImage,
]) ?>
<?= tracking_head() ?>
<meta name="theme-color" content="#0b2545">

<link rel="icon" href="<?= e(asset('img/favicon.svg')) ?>" type="image/svg+xml">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400;0,9..144,600;0,9..144,700;1,9..144,400&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= e(asset('css/main.css')) ?>?v=3">
<link rel="stylesheet" href="<?= e(asset('css/shop.css')) ?>?v=3">
<link rel="stylesheet" href="<?= e(asset('css/slides.css')) ?>?v=3">

<?= jsonld(schema_local_business(), schema_website(), ...$schemaNodes) ?>
</head>
<body class="<?= e($bodyClass) ?>">

<a class="skip" href="#main">Skip to content</a>

<!-- Utility bar: the script's phone + hours strip above the nav. -->
<div class="util">
  <div class="wrap util__inner">
    <p class="util__note"><?= icon('shield', 'util__icon') ?> <span>Licensed &middot; Insured &middot; <?= e(cfg('warranty_years')) ?>-Year Written Warranty</span></p>
    <p class="util__contact">
      <a href="<?= e(tel_link()) ?>"><?= icon('phone', 'util__icon') ?> <?= e(cfg('phone')) ?></a>
      <span class="util__sep" aria-hidden="true"></span>
      <span class="util__hours"><?= icon('clock', 'util__icon') ?> <?= e(cfg('hours_short')) ?></span>
    </p>
  </div>
</div>

<header class="head" id="siteHead">
  <div class="wrap head__inner">
    <a class="brand" href="<?= e(url()) ?>">
      <?= brand_mark() ?>
      <span class="brand__text">
        <strong><?= e($company) ?></strong>
        <small><?= e(cfg('tagline')) ?></small>
      </span>
    </a>

    <nav class="nav" id="primaryNav" aria-label="Primary">
      <ul class="nav__list">
        <?php foreach (nav_items() as $item):
            $hasChildren = !empty($item['children']);
            $active = is_current($item['path'])
                || ($hasChildren && isset($item['match']) && is_section($item['match']))
                || ($hasChildren && !isset($item['match']) && is_section($item['path']));
        ?>
        <li class="nav__item<?= $hasChildren ? ' has-sub' : '' ?>">
          <a class="nav__link<?= $active ? ' is-active' : '' ?>" href="<?= e(url($item['path'])) ?>"
             <?= $hasChildren ? 'aria-expanded="false"' : '' ?>>
            <span><?= e($item['label']) ?></span>
            <?php if ($hasChildren): ?><i class="nav__caret" aria-hidden="true"></i><?php endif; ?>
          </a>
          <?php if ($hasChildren): ?>
          <ul class="sub">
            <?php foreach ($item['children'] as $child): ?>
            <li><a href="<?= e(url($child['path'])) ?>"<?= is_current($child['path']) ? ' class="is-active"' : '' ?>><?= e($child['label']) ?></a></li>
            <?php endforeach; ?>
          </ul>
          <?php endif; ?>
        </li>
        <?php endforeach; ?>
      </ul>
    </nav>

    <div class="head__actions">
      <a class="head__cart" href="<?= e(url('cart')) ?>" aria-label="Cart, <?= (int) cart_count() ?> packs">
        <?= icon('cube') ?><span class="head__cart-count"><?= cart_count() > 0 ? (int) cart_count() : '' ?></span>
      </a>
      <a class="head__phone" href="<?= e(tel_link()) ?>" aria-label="Call <?= e(cfg('phone')) ?>">
        <?= icon('phone') ?><span><?= e(cfg('phone')) ?></span>
      </a>
      <a class="btn btn--primary btn--sm" href="<?= e(url('quote')) ?>"><span>Get a Free Quote</span></a>
      <button class="burger" type="button" id="burger" aria-expanded="false" aria-controls="primaryNav" aria-label="Open menu">
        <span></span><span></span><span></span>
      </button>
    </div>
  </div>
  <span class="head__progress" id="scrollProgress" aria-hidden="true"></span>
</header>

<?php if (!empty($flashes)): ?>
<div class="wrap">
  <?php foreach ($flashes as $flash): ?>
  <p class="flash flash--<?= e($flash['type']) ?>" role="status"><?= e($flash['message']) ?></p>
  <?php endforeach; ?>
</div>
<?php endif; ?>

<main id="main">

<?php if ($bannerTitle !== ''): ?>
<section class="banner">
  <div class="banner__grid" aria-hidden="true"></div>
  <div class="banner__arch" aria-hidden="true"></div>
  <div class="wrap banner__inner">
    <?php if (!empty($trail)) { breadcrumbs($trail); } ?>
    <?php if ($bannerEyebrow !== ''): ?><p class="eyebrow eyebrow--light reveal"><?= e($bannerEyebrow) ?></p><?php endif; ?>
    <h1 class="banner__title reveal"><?= $bannerTitle ?></h1>
    <?= ornament('light') ?>
    <?php if ($bannerLede !== ''): ?><p class="banner__lede reveal"><?= $bannerLede ?></p><?php endif; ?>
    <?php if ($bannerCtas !== ''): ?><div class="cta-row reveal"><?= $bannerCtas ?></div><?php endif; ?>
  </div>
</section>
<?php endif; ?>
