<?php
/**
 * Search engine and social metadata.
 *
 * Three jobs:
 *  1. Per-page title, description and indexing controls, with a database
 *     override (SEO admin) on top of the defaults each page sets itself.
 *  2. The head tags: robots, canonical, Open Graph, Twitter, geo and language.
 *  3. Structured data (JSON-LD) built from the same config and content the
 *     visible page uses, so the markup never describes something the page does not say.
 *
 * Rules this file follows on purpose:
 *  - No star ratings or review markup unless the reviews are real and on the page.
 *  - Pages with thin or unfinished content are noindex, not published as spam.
 *  - Private pages (cart, checkout, admin, order confirmation) are never indexed.
 */

declare(strict_types=1);

/** The database override for this page, or null. Failure is silent: defaults apply. */
function seo_override(string $path): ?array
{
    static $cache = [];
    if (array_key_exists($path, $cache)) {
        return $cache[$path];
    }
    try {
        $row = db_row('SELECT title, description, keywords, noindex FROM seo_pages WHERE path = ?', [$path]);
    } catch (Throwable $e) {
        $row = null;
    }
    return $cache[$path] = $row;
}

/** Google, Bing and other verification tokens, from Settings or config. */
function seo_verification_tags(): string
{
    $out = '';
    foreach (['google_site_verification' => 'google-site-verification', 'bing_site_verification' => 'msvalidate.01'] as $key => $name) {
        $token = trim((string) cfg($key));
        if ($token !== '') {
            $out .= '<meta name="' . $name . '" content="' . e($token) . '">' . "\n";
        }
    }
    return $out;
}

/**
 * The full head metadata block for one page.
 *
 * $seo keys: title, description, canonical (path), noindex (bool), image (absolute URL),
 *            type ('website'|'article'|'product'), keywords (optional).
 */
function seo_head_tags(array $seo): string
{
    $title       = $seo['title'];
    $description = excerpt($seo['description']);
    $canonicalPath = $seo['canonical'] ?? current_path();
    // The services overview is served with a trailing slash; the canonical must match it.
    if ($canonicalPath === 'services') {
        $canonicalPath = 'services/';
    }
    $canonical   = absolute_url($canonicalPath);
    $image       = $seo['image'] ?? absolute_url('assets/img/social-card.png');
    $type        = $seo['type'] ?? 'website';
    $robots      = !empty($seo['noindex'])
        ? 'noindex, nofollow'
        : 'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1';
    $siteName    = cfg('company_name');
    $locale      = 'en_NG';

    $lat = (string) cfg('geo_lat');
    $lng = (string) cfg('geo_lng');

    $out  = '<meta name="description" content="' . e($description) . '">' . "\n";
    if (!empty($seo['keywords'])) {
        $out .= '<meta name="keywords" content="' . e($seo['keywords']) . '">' . "\n";
    }
    $out .= '<meta name="robots" content="' . e($robots) . '">' . "\n";
    $out .= '<meta name="googlebot" content="' . e($robots) . '">' . "\n";
    $out .= '<meta name="bingbot" content="' . e($robots) . '">' . "\n";
    $out .= '<link rel="canonical" href="' . e($canonical) . '">' . "\n";

    // Language and region. en-NG is the market; x-default covers everyone else.
    $out .= '<link rel="alternate" hreflang="en-NG" href="' . e($canonical) . '">' . "\n";
    $out .= '<link rel="alternate" hreflang="x-default" href="' . e($canonical) . '">' . "\n";

    // Local signals for Nigeria and the service area.
    $out .= '<meta name="geo.region" content="NG-LA">' . "\n";
    $out .= '<meta name="geo.placename" content="' . e(cfg('city') . ', ' . cfg('state')) . '">' . "\n";
    if ($lat !== '' && $lng !== '') {
        $out .= '<meta name="geo.position" content="' . e($lat . ';' . $lng) . '">' . "\n";
        $out .= '<meta name="ICBM" content="' . e($lat . ', ' . $lng) . '">' . "\n";
    }

    // Open Graph: how the page looks when shared on WhatsApp, Facebook, LinkedIn.
    $out .= '<meta property="og:type" content="' . e($type) . '">' . "\n";
    $out .= '<meta property="og:site_name" content="' . e($siteName) . '">' . "\n";
    $out .= '<meta property="og:locale" content="' . $locale . '">' . "\n";
    $out .= '<meta property="og:title" content="' . e($title) . '">' . "\n";
    $out .= '<meta property="og:description" content="' . e($description) . '">' . "\n";
    $out .= '<meta property="og:url" content="' . e($canonical) . '">' . "\n";
    $out .= '<meta property="og:image" content="' . e($image) . '">' . "\n";
    $out .= '<meta property="og:image:width" content="1200">' . "\n";
    $out .= '<meta property="og:image:height" content="630">' . "\n";

    // Twitter / X card.
    $out .= '<meta name="twitter:card" content="summary_large_image">' . "\n";
    $out .= '<meta name="twitter:title" content="' . e($title) . '">' . "\n";
    $out .= '<meta name="twitter:description" content="' . e($description) . '">' . "\n";
    $out .= '<meta name="twitter:image" content="' . e($image) . '">' . "\n";

    $out .= seo_verification_tags();
    return $out;
}

// --- Structured data ----------------------------------------------------------

/** Renders one JSON-LD block. Each node is a schema.org object. */
function jsonld(array ...$nodes): string
{
    $out = '';
    foreach ($nodes as $node) {
        if ($node === []) {
            continue;
        }
        $out .= '<script type="application/ld+json">'
              . json_encode(['@context' => 'https://schema.org'] + $node, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG)
              . '</script>' . "\n";
    }
    return $out;
}

/**
 * The business itself. Used once per page, on every page, so search engines
 * connect the site to one local entity: name, address, phone, area served.
 */
function schema_local_business(): array
{
    $geo = (string) cfg('geo_lat') !== '' && (string) cfg('geo_lng') !== ''
        ? ['@type' => 'GeoCoordinates', 'latitude' => (float) cfg('geo_lat'), 'longitude' => (float) cfg('geo_lng')]
        : null;

    $node = [
        '@type'       => ['HomeAndConstructionBusiness', 'LocalBusiness'],
        '@id'         => absolute_url() . '#business',
        'name'        => cfg('company_name'),
        'alternateName' => cfg('company_short'),
        'description' => 'Epoxy flooring installer and epoxy product supplier in ' . cfg('city') . ', Nigeria: garage, warehouse, commercial and decorative floors, plus retail and wholesale epoxy for sale.',
        'url'         => absolute_url(),
        'telephone'   => cfg('phone'),
        'email'       => cfg('email'),
        'image'       => absolute_url('assets/img/social-card.png'),
        'logo'        => absolute_url('assets/img/logo-square.png'),
        'foundingDate' => (string) cfg('founding_year'),
        'address'     => [
            '@type'           => 'PostalAddress',
            'streetAddress'   => cfg('office_address'),
            'addressLocality' => cfg('city'),
            'addressRegion'   => cfg('state'),
            'addressCountry'  => 'NG',
        ],
        'areaServed'  => array_map(
            static fn (string $place): array => ['@type' => 'City', 'name' => $place],
            array_values(array_filter((array) cfg('service_cities', [])))
        ),
        'priceRange'  => cfg('currency') . cfg('price_garage_1_low') . ' - ' . cfg('currency') . cfg('price_garage_1_high'),
        'openingHoursSpecification' => [
            ['@type' => 'OpeningHoursSpecification', 'dayOfWeek' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'], 'opens' => '08:00', 'closes' => '18:00'],
            ['@type' => 'OpeningHoursSpecification', 'dayOfWeek' => 'Saturday', 'opens' => '09:00', 'closes' => '14:00'],
        ],
        'hasOfferCatalog' => [
            '@type' => 'OfferCatalog',
            'name'  => 'Epoxy flooring and epoxy products',
            'url'   => absolute_url('shop'),
        ],
    ];
    if ($geo !== null) {
        $node['geo'] = $geo;
    }

    $sameAs = array_values(array_filter(array_map('trim', (array) cfg('social', []))));
    if ($sameAs !== []) {
        $node['sameAs'] = array_map(static fn (string $u): string => $u, $sameAs);
    }
    return $node;
}

function schema_website(): array
{
    return [
        '@type'     => 'WebSite',
        '@id'       => absolute_url() . '#website',
        'url'       => absolute_url(),
        'name'      => cfg('company_name'),
        'inLanguage' => 'en-NG',
        'publisher' => ['@id' => absolute_url() . '#business'],
    ];
}

/** A product page entry for one pack. Prices are read from the database, so the markup always matches the shop. */
function schema_product(array $product): array
{
    $node = [
        '@type'       => 'Product',
        'name'        => $product['name'] . ' (' . $product['pack_label'] . ')',
        'description' => (string) $product['description'],
        'brand'       => ['@type' => 'Brand', 'name' => cfg('company_name')],
        'offers'      => [
            '@type'           => 'Offer',
            'url'             => absolute_url('shop') . '#product-' . (int) $product['id'],
            'priceCurrency'   => 'NGN',
            'price'           => number_format((float) $product['unit_price'], 2, '.', ''),
            'availability'    => (bool) $product['in_stock']
                ? 'https://schema.org/InStock'
                : 'https://schema.org/OutOfStock',
            'itemCondition'   => 'https://schema.org/NewCondition',
            'seller'          => ['@id' => absolute_url() . '#business'],
        ],
    ];
    if (!empty($product['image_url'])) {
        $node['image'] = absolute_url($product['image_url']);
    }
    return $node;
}

/** A video that is on the page. Only call this for videos that are actually embedded or linked there. */
function schema_video(array $media): array
{
    $node = [
        '@type'        => 'VideoObject',
        'name'         => $media['title'],
        'description'  => $media['caption'] !== '' ? $media['caption'] : $media['title'],
        'uploadDate'   => date('c', strtotime((string) $media['created_at'])),
        'thumbnailUrl' => absolute_url((string) ($media['thumbnail'] ?? 'assets/img/social-card.png')),
        'contentUrl'   => absolute_url((string) ($media['file_path'] ?? '')) ,
    ];
    if (!empty($media['video_embed_url'])) {
        $node['embedUrl'] = $media['video_embed_url'];
    }
    if (empty($media['file_path'])) {
        unset($node['contentUrl']);
    }
    return $node;
}

/** The three steps a visitor can follow, as HowTo, for the process page. */
function schema_howto(string $name, string $description, array $steps): array
{
    $list = [];
    foreach (array_values($steps) as $i => $step) {
        $list[] = ['@type' => 'HowToStep', 'position' => $i + 1, 'name' => $step[0], 'text' => $step[1]];
    }
    return [
        '@type'       => 'HowTo',
        'name'        => $name,
        'description' => $description,
        'step'        => $list,
    ];
}

function schema_article(string $headline, string $description, string $path, string $published): array
{
    return [
        '@type'         => 'Article',
        'headline'      => $headline,
        'description'   => excerpt($description),
        'mainEntityOfPage' => absolute_url($path),
        'url'           => absolute_url($path),
        'datePublished' => $published,
        'dateModified'  => date('c'),
        'inLanguage'    => 'en-NG',
        'author'        => ['@type' => 'Organization', 'name' => cfg('company_name')],
        'publisher'     => ['@id' => absolute_url() . '#business'],
        'image'         => absolute_url('assets/img/social-card.png'),
    ];
}

/** FAQPage from [question, answer] pairs. Text is plain, as on the page. */
function schema_faq(array $pairs): array
{
    $entities = [];
    foreach ($pairs as $pair) {
        $entities[] = [
            '@type' => 'Question',
            'name'  => html_entity_decode(strip_tags((string) $pair[0]), ENT_QUOTES | ENT_HTML5, 'UTF-8'),
            'acceptedAnswer' => [
                '@type' => 'Answer',
                'text'  => html_entity_decode(strip_tags((string) $pair[1]), ENT_QUOTES | ENT_HTML5, 'UTF-8'),
            ],
        ];
    }
    return ['@type' => 'FAQPage', 'mainEntity' => $entities];
}

function schema_breadcrumbs(array $items): array
{
    $list = [];
    foreach (array_values($items) as $i => [$name, $path]) {
        $list[] = ['@type' => 'ListItem', 'position' => $i + 1, 'name' => $name, 'item' => absolute_url($path)];
    }
    return ['@type' => 'BreadcrumbList', 'itemListElement' => $list];
}

/**
 * A job opening, as Google's JobPosting schema. Salary is only included when
 * one is set in the admin -- never estimated or invented. All current roles
 * are based in Lagos, so the location uses the company's real address.
 */
function schema_job_posting(array $job): array
{
    $employmentType = match ($job['employment_type']) {
        'Part-time'  => 'PART_TIME',
        'Contract'   => 'CONTRACTOR',
        'Internship' => 'INTERN',
        default      => 'FULL_TIME',
    };

    $description = $job['summary'];
    if (!empty($job['responsibilities'])) {
        $description .= ' Responsibilities: ' . str_replace("
", '; ', $job['responsibilities']) . '.';
    }
    if (!empty($job['requirements'])) {
        $description .= ' What we are looking for: ' . str_replace("
", '; ', $job['requirements']) . '.';
    }

    $node = [
        '@type'            => 'JobPosting',
        'title'            => $job['title'],
        'description'      => $description,
        'identifier'       => ['@type' => 'PropertyValue', 'name' => cfg('company_name'), 'value' => (string) $job['id']],
        'datePosted'       => date('Y-m-d', strtotime((string) $job['created_at'])),
        'employmentType'   => $employmentType,
        'hiringOrganization' => ['@id' => absolute_url() . '#business'],
        'jobLocation'      => [
            '@type'   => 'Place',
            'address' => [
                '@type'           => 'PostalAddress',
                'addressLocality' => cfg('city'),
                'addressRegion'   => cfg('state'),
                'addressCountry'  => 'NG',
            ],
        ],
        'directApply' => true,
    ];

    if (trim((string) $job['salary_range']) !== '') {
        $node['baseSalary'] = [
            '@type'    => 'MonetaryAmount',
            'currency' => 'NGN',
            'value'    => ['@type' => 'QuantitativeValue', 'value' => $job['salary_range'], 'unitText' => 'MONTH'],
        ];
    }

    return $node;
}

/** Every 'Image' object for the gallery page. */
function schema_image(array $media): array
{
    return [
        '@type'      => 'ImageObject',
        'contentUrl' => absolute_url((string) $media['file_path']),
        'url'        => absolute_url((string) $media['file_path']),
        'name'       => $media['title'],
        'caption'    => $media['caption'] !== '' ? $media['caption'] : $media['alt_text'],
        'description' => $media['alt_text'],
        'width'      => (int) $media['width'],
        'height'     => (int) $media['height'],
    ];
}
