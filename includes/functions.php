<?php
/**
 * Shared helpers. Every page includes this first.
 */

declare(strict_types=1);

/**
 * functions.php is the single entry point for the whole helper layer. Pages
 * require only this file, which pulls in the content data, the artwork and the
 * render components -- all of which define functions and emit nothing. That
 * way a page can call button() or icon() while setting up its header
 * variables, before header.php has run.
 */
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/data.php';
require_once __DIR__ . '/artwork.php';
require_once __DIR__ . '/components.php';
require_once __DIR__ . '/media.php';
require_once __DIR__ . '/seo.php';
require_once __DIR__ . '/slideshow.php';
require_once __DIR__ . '/notify.php';
require_once __DIR__ . '/careers.php';

/** The config array, loaded once. */
function config(): array
{
    static $config = null;
    if ($config === null) {
        $config = require __DIR__ . '/../config/config.php';
    }
    return $config;
}

/**
 * Values edited in the admin Settings page, read from the settings table once
 * per request. If the database is not reachable (first install, or a broken
 * connection) this returns nothing and the config.php defaults apply, so the
 * public site never fails just because the overrides cannot be read.
 */
function setting_overrides(): array
{
    static $overrides = null;
    if ($overrides !== null) {
        return $overrides;
    }

    $overrides = [];
    try {
        foreach (db_connect()->query('SELECT `key`, `value` FROM settings')->fetchAll() as $row) {
            $overrides[$row['key']] = (string) $row['value'];
        }
    } catch (Throwable $e) {
        // Fall through to the defaults in config.php.
    }
    return $overrides;
}

/**
 * One config value. A value saved in the admin wins; otherwise the default
 * from config.php is used. Dot notation reaches into nested arrays, and only
 * top-level scalars can be overridden.
 * cfg('company_name'), cfg('social.Instagram')
 */
function cfg(string $key, $fallback = '')
{
    if (!str_contains($key, '.')) {
        $overrides = setting_overrides();
        if (array_key_exists($key, $overrides)) {
            return $overrides[$key];
        }
    }

    $value = config();
    foreach (explode('.', $key) as $segment) {
        if (!is_array($value) || !array_key_exists($segment, $value)) {
            return $fallback;
        }
        $value = $value[$segment];
    }
    return $value;
}

/** Escape for HTML output. */
function e($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** A number formatted the way the trust bar wants it. */
function num($value): string
{
    return number_format((float) $value);
}

/**
 * BASE_URL and SITE_ORIGIN are defined by config.php, which loads lazily on
 * the first cfg() call. Every read of them goes through one of these two
 * accessors, so no helper can touch an undefined constant just because it
 * happened to run before any config value was needed.
 */
function base_path(): string
{
    config();
    return BASE_URL;
}

function site_origin(): string
{
    config();
    return SITE_ORIGIN;
}

/**
 * A site URL. Paths are given extension-less ('about', 'services/residential')
 * and .htaccess maps them onto the .php file, so the markup never carries .php.
 */
function url(string $path = ''): string
{
    // A trailing slash the caller wrote deliberately is kept. That matters for
    // a path that is also a real directory -- 'services/' -- where Apache
    // would otherwise redirect the slash-less form onto the slashed one.
    $keepSlash = $path !== '' && str_ends_with($path, '/');
    $path = trim($path, '/');

    if ($path === '') {
        return base_path() . '/';
    }
    return base_path() . '/' . $path . ($keepSlash ? '/' : '');
}

/** An asset URL. */
function asset(string $path): string
{
    return base_path() . '/assets/' . ltrim($path, '/');
}

/** Fully-qualified URL, for schema / sitemap / Open Graph. */
function absolute_url(string $path = ''): string
{
    return rtrim(site_origin(), '/') . url($path);
}

/**
 * An email address with a break opportunity after the @, so a narrow column
 * wraps it as "hello@" / "example.com" instead of splitting the domain
 * mid-word. Returns escaped markup, so do not pass the result through e().
 */
function email_html(string $address): string
{
    $parts = explode('@', $address, 2);
    if (count($parts) !== 2) {
        return e($address);
    }
    return e($parts[0]) . '@<wbr>' . e($parts[1]);
}

/** Digits only, for tel: and wa.me links. */
function dial(string $number): string
{
    return preg_replace('/[^0-9]/', '', $number) ?? '';
}

function tel_link(): string
{
    return 'tel:+' . dial(cfg('phone'));
}

function whatsapp_link(string $message = ''): string
{
    $link = 'https://wa.me/' . dial(cfg('whatsapp'));
    if ($message !== '') {
        $link .= '?text=' . rawurlencode($message);
    }
    return $link;
}

/**
 * The current page as a clean, extension-less path relative to the site root.
 * '' for the home page. Both nav-state helpers derive from this one function,
 * so they can never disagree about what page is being viewed.
 */
function current_path(): string
{
    $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';
    $path = trim($path, '/');

    // Strip the install sub-directory, when the site is not at the domain root.
    $base = trim(base_path(), '/');
    if ($base !== '' && ($path === $base || str_starts_with($path, $base . '/'))) {
        $path = trim(substr($path, strlen($base)), '/');
    }

    // The .php form and the clean form are the same page; so are '' and 'index'.
    $path = preg_replace('/\.php$/', '', $path) ?? '';
    if ($path === 'index' || $path === 'services/index') {
        $path = $path === 'index' ? '' : 'services';
    }

    return $path;
}

/** True when $path is the page currently being viewed. */
function is_current(string $path): bool
{
    return current_path() === trim($path, '/');
}

/** True when the current page sits under $path (keeps a parent nav item lit). */
function is_section(string $path): bool
{
    $current = current_path();
    return $current !== '' && str_starts_with($current, trim($path, '/'));
}

/**
 * A URL-safe fragment id from a heading. Entities are decoded first so
 * 'Installation &amp; Timeline' and 'Installation & Timeline' slug the same.
 */
function slug(string $text): string
{
    $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
    $text = strtolower(trim(strip_tags($text)));
    $text = preg_replace('/[^a-z0-9]+/', '-', $text) ?? '';
    return trim($text, '-');
}

/** Trim copy to a meta-description length without cutting a word in half. */
function excerpt(string $text, int $limit = 158): string
{
    $text = trim(preg_replace('/\s+/', ' ', strip_tags($text)) ?? '');
    if (mb_strlen($text) <= $limit) {
        return $text;
    }
    $cut = mb_substr($text, 0, $limit - 1);
    $space = mb_strrpos($cut, ' ');
    return rtrim($space !== false ? mb_substr($cut, 0, $space) : $cut, " ,.;:-") . '...';
}

function start_session_if_needed(): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
}

// --- CSRF ---------------------------------------------------------------

function csrf_token(): string
{
    start_session_if_needed();
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf" value="' . e(csrf_token()) . '">';
}

function csrf_valid(?string $token): bool
{
    start_session_if_needed();
    return is_string($token)
        && !empty($_SESSION['csrf'])
        && hash_equals($_SESSION['csrf'], $token);
}

// --- Flash messages -----------------------------------------------------

function flash(string $type, string $message): void
{
    start_session_if_needed();
    $_SESSION['flashes'][] = ['type' => $type, 'message' => $message];
}

function take_flashes(): array
{
    start_session_if_needed();
    $flashes = $_SESSION['flashes'] ?? [];
    unset($_SESSION['flashes']);
    return $flashes;
}

// --- Navigation ---------------------------------------------------------

/**
 * The top nav exactly as the script specifies it, including both dropdowns.
 */
function nav_items(): array
{
    return [
        ['label' => 'Home',       'path' => ''],
        ['label' => 'About',      'path' => 'about'],
        ['label' => 'Services',   'path' => 'services/', 'children' => [
            ['label' => 'Residential',            'path' => 'services/residential'],
            ['label' => 'Commercial & Industrial','path' => 'services/commercial'],
            ['label' => 'Decorative & Designer',  'path' => 'services/decorative'],
        ]],
        ['label' => 'Our Process','path' => 'process'],
        ['label' => 'Portfolio',  'path' => 'portfolio'],
        ['label' => 'Epoxy Sales', 'path' => 'shop'],
        ['label' => 'For You',    'path' => 'for-homeowners', 'match' => 'for-', 'children' => [
            ['label' => 'Homeowners',             'path' => 'for-homeowners'],
            ['label' => 'Businesses',             'path' => 'for-businesses'],
            ['label' => 'Architects & Contractors','path' => 'for-architects'],
        ]],
        ['label' => 'FAQ',        'path' => 'faq'],
        ['label' => 'Contact',    'path' => 'contact'],
    ];
}
