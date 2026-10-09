<?php
/**
 * Writes HTML pages for the product packaging illustrations and the project
 * floor scenes, ready for headless Chrome to screenshot into PNG files.
 *
 *   php tools/render-brand-art.php            writes the HTML into assets/img/render-tmp/
 *   (screenshot each page, then)
 *   php tools/import-render-images.php        imports the PNGs into the media library
 *
 * The output is illustration, not photography. Replace it with real photos in
 * the media library as they become available; the site then uses those instead.
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require_once __DIR__ . '/../includes/functions.php';

$dir = __DIR__ . '/../assets/img/render-tmp/';
if (!is_dir($dir)) {
    mkdir($dir, 0775, true);
}

/** Packaging colour by product family, from the site palette. */
function pack_colour(string $name): string
{
    return match (true) {
        str_contains($name, 'Primer')       => '#0f7b5f',
        str_contains($name, 'Aggregate')    => '#8c6239',
        str_contains($name, 'Flake')        => '#8c6239',
        str_contains($name, 'Topcoat')      => '#0b2545',
        str_contains($name, 'Mortar')       => '#5b6b7a',
        str_contains($name, 'Filler')       => '#5b6b7a',
        str_contains($name, 'Pigment')      => '#7a4ea3',
        str_contains($name, 'Cleaner')      => '#14a077',
        str_contains($name, 'Thinner')      => '#c0392b',
        str_contains($name, 'Self-Levelling') => '#15508c',
        default                             => '#15508c',
    };
}

/** The shape of the container, from the pack size text. */
function pack_shape(string $pack): string
{
    return match (true) {
        str_contains($pack, 'Drum')   => 'drum',
        str_contains($pack, 'pail')   => 'pail',
        str_contains($pack, 'bag')    => 'bag',
        str_contains($pack, 'litre')  => 'pail',
        default                       => 'tin',
    };
}

/** HTML for one pack: a container in the family colour, with the name and size on its label. */
function pack_html(array $product): string
{
    $colour = pack_colour($product['name']);
    $shape  = pack_shape($product['pack_label']);
    $name   = htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8');
    $pack   = htmlspecialchars($product['pack_label'], ENT_QUOTES, 'UTF-8');

    $body = match ($shape) {
        'drum' => '<div class="drum"><div class="top"></div><div class="body" style="background:' . $colour . '">'
                . '<div class="label"><b>' . $name . '</b><span>' . $pack . '</span></div></div><div class="bottom"></div></div>',
        'pail' => '<div class="pail" style="background:' . $colour . '"><div class="rim"></div>'
                . '<div class="label"><b>' . $name . '</b><span>' . $pack . '</span></div></div>',
        'bag'  => '<div class="bag" style="background:' . $colour . '"><div class="seal"></div>'
                . '<div class="label"><b>' . $name . '</b><span>' . $pack . '</span></div></div>',
        default => '<div class="tin" style="background:' . $colour . '"><div class="lid"></div>'
                . '<div class="label"><b>' . $name . '</b><span>' . $pack . '</span></div></div>',
    };

    return '<!doctype html><html><head><meta charset="utf-8"><style>
    html,body{margin:0;width:800px;height:600px;overflow:hidden}
    body{background:linear-gradient(160deg,#f3f8fc,#dceaf7);display:grid;place-items:center;font-family:Arial,Helvetica,sans-serif}
    .tin,.pail,.bag,.drum{position:relative;color:#fff;text-align:center;box-shadow:0 30px 40px rgba(11,37,69,.25)}
    .tin{width:300px;height:330px;border-radius:14px}
    .pail{width:300px;height:360px;border-radius:14px 14px 22px 22px}
    .bag{width:280px;height:380px;border-radius:12px 12px 22px 22px;clip-path:polygon(0 4%,100% 0,100% 100%,0 100%)}
    .drum{width:300px;height:400px}
    .drum .top,.drum .bottom{position:absolute;left:0;right:0;height:46px;border-radius:50%;background:#dfe7ef}
    .drum .top{top:-23px}.drum .bottom{bottom:-23px;background:#b8c6d4}
    .drum .body{position:absolute;top:23px;bottom:23px;left:0;right:0}
    .lid{position:absolute;top:-14px;left:0;right:0;height:28px;border-radius:10px;background:#c9d6e2}
    .rim{position:absolute;top:-10px;left:0;right:0;height:26px;border-radius:50%;background:#c9d6e2}
    .seal{position:absolute;top:0;left:0;right:0;height:14px;background:rgba(255,255,255,.35)}
    .label{position:absolute;left:24px;right:24px;top:50%;transform:translateY(-50%);background:#fff;color:#0b2545;border-radius:8px;padding:18px 12px}
    .label b{display:block;font-family:Georgia,serif;font-size:24px;line-height:1.15;margin-bottom:8px}
    .label span{display:inline-block;font-size:15px;letter-spacing:.12em;text-transform:uppercase;color:#15508c;font-weight:bold}
    .brand{position:absolute;bottom:26px;width:100%;text-align:center;color:#0b2545;font-size:13px;letter-spacing:.16em;text-transform:uppercase}
    </style></head><body>' . $body . '<div class="brand">Iris Epoxy Service</div></body></html>';
}

$products = db_rows('SELECT id, name, pack_label FROM products WHERE published = 1 ORDER BY channel, sort, id');
$manifest = [];
foreach ($products as $product) {
    $file = 'product-' . (int) $product['id'] . '.html';
    file_put_contents($dir . $file, pack_html($product));
    $manifest[] = ['file' => $file, 'png' => 'product-' . (int) $product['id'] . '.png', 'w' => 800, 'h' => 600,
                   'kind' => 'product', 'id' => (int) $product['id'], 'title' => $product['name'] . ' (' . $product['pack_label'] . ')'];
}

// One scene per project, using the artwork already on the site.
foreach (projects() as $project) {
    $html = '<!doctype html><html><head><meta charset="utf-8"><style>html,body{margin:0;width:600px;height:400px;overflow:hidden}svg{display:block;width:600px;height:400px}</style></head><body>'
          . project_art($project['art']) . '</body></html>';
    $file = 'project-' . $project['slug'] . '.html';
    file_put_contents($dir . $file, $html);
    $manifest[] = ['file' => $file, 'png' => 'project-' . $project['slug'] . '.png', 'w' => 600, 'h' => 400,
                   'kind' => 'project', 'slug' => $project['slug'], 'title' => $project['name']];
}

file_put_contents($dir . 'manifest.json', json_encode($manifest, JSON_PRETTY_PRINT));
fwrite(STDOUT, 'Wrote ' . count($manifest) . " pages to assets/img/render-tmp/\n");
