<?php
/**
 * Checks every internal link on every page listed in the sitemap, and reports
 * any that do not return 200. Run it after changing pages, and before launch.
 *
 *   php tools/check-links.php
 *
 * Only internal links are checked. External links (WhatsApp, YouTube, map
 * services) are not requested, so the check stays fast and polite.
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require_once __DIR__ . '/../includes/functions.php';

$origin = rtrim(site_origin(), '/');
$base   = rtrim(base_path(), '/');

/** Fetches a URL and returns [status, body]. Redirects are not followed, so a redirect is reported as such. */
function fetch(string $url): array
{
    $context = stream_context_create(['http' => [
        'method'          => 'GET',
        'timeout'         => 10,
        'ignore_errors'   => true,
        'follow_location' => 0,
        'header'          => "User-Agent: iris-link-check/1.0\r\n",
    ]]);
    $body = @file_get_contents($url, false, $context);
    $status = 0;
    foreach ($http_response_header ?? [] as $line) {
        if (preg_match('#^HTTP/\S+\s+(\d{3})#', $line, $m)) {
            $status = (int) $m[1];
        }
    }
    return [$status, $body === false ? '' : $body];
}

// 1. Every page the sitemap lists.
[$status, $sitemap] = fetch($origin . $base . '/sitemap.xml');
if ($status !== 200) {
    fwrite(STDERR, "Could not read the sitemap (HTTP $status).\n");
    exit(1);
}
preg_match_all('#<loc>([^<]+)</loc>#', $sitemap, $matches);
$pages = array_values(array_unique(array_filter($matches[1], static fn (string $u): bool => !str_contains($u, '/assets/'))));

// 2. Every internal link on those pages.
$links = [];   // target => [pages that link to it]
foreach ($pages as $page) {
    [$code, $html] = fetch($page);
    if ($code !== 200) {
        fwrite(STDOUT, "PAGE   $code  $page\n");
        continue;
    }
    preg_match_all('~\shref="([^"#]+)(#[^"]*)?"~', $html, $hrefs);
    foreach ($hrefs[1] as $href) {
        $href = html_entity_decode($href, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        if (preg_match('#^(mailto:|tel:|https?://(?!localhost))#i', $href)) {
            continue;
        }
        if (str_starts_with($href, 'http')) {
            $target = $href;
        } elseif (str_starts_with($href, '/')) {
            $target = $origin . $href;
        } else {
            continue;   // relative paths are not used on this site
        }
        if (!str_starts_with($target, $origin)) {
            continue;
        }
        $links[$target][] = $page;
    }
}

// 3. Check each distinct internal target once.
$broken = 0;
foreach (array_keys($links) as $target) {
    [$code] = fetch($target);
    if ($code !== 200) {
        $broken++;
        $from = array_values(array_unique($links[$target]));
        fwrite(STDOUT, "LINK   $code  $target\n       linked from: " . implode(', ', array_slice($from, 0, 3))
            . (count($from) > 3 ? ' (+' . (count($from) - 3) . ' more)' : '') . "\n");
    }
}

fwrite(STDOUT, "\nChecked " . count($pages) . ' pages and ' . count($links) . ' internal links. '
    . ($broken === 0 ? 'No broken links.' : "$broken broken.") . "\n");
exit($broken === 0 ? 0 : 1);
