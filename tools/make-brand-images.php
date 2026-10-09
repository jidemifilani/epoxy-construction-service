<?php
/**
 * Generates the PNG images that social platforms and search engines need:
 *   assets/img/social-card.png   1200 x 630, shown when the site is shared
 *   assets/img/logo-square.png   512 x 512, used in structured data
 *
 * Run once, from the command line, and again whenever the brand changes:
 *   php tools/make-brand-images.php
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}
if (!extension_loaded('gd')) {
    fwrite(STDERR, "The GD extension is required.\n");
    exit(1);
}

$out = __DIR__ . '/../assets/img/';

function hex(GdImage $img, string $hex, int $alpha = 0): int
{
    [$r, $g, $b] = sscanf($hex, '#%02x%02x%02x');
    return imagecolorallocatealpha($img, $r, $g, $b, $alpha);
}

/** A serif font if the system has one, otherwise null (text is then skipped rather than broken). */
function serif_font(bool $bold = true): ?string
{
    $candidates = $bold
        ? ['C:/Windows/Fonts/georgiab.ttf', 'C:/Windows/Fonts/timesbd.ttf', '/usr/share/fonts/truetype/dejavu/DejaVuSerif-Bold.ttf']
        : ['C:/Windows/Fonts/georgia.ttf', 'C:/Windows/Fonts/times.ttf', '/usr/share/fonts/truetype/dejavu/DejaVuSerif.ttf'];
    foreach ($candidates as $font) {
        if (is_file($font)) {
            return $font;
        }
    }
    return null;
}

function sans_font(): ?string
{
    foreach (['C:/Windows/Fonts/arial.ttf', 'C:/Windows/Fonts/segoeui.ttf', '/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf'] as $font) {
        if (is_file($font)) {
            return $font;
        }
    }
    return null;
}

/** The brand mark: a ring, a mason's arch and a trowel, in the site's colours. */
function draw_mark(GdImage $img, int $cx, int $cy, int $size): void
{
    $s = $size / 48;
    $white = hex($img, '#ffffff');
    $green = hex($img, '#2bbd8f');
    $ring  = hex($img, '#ffffff', 80);

    imagesetthickness($img, max(1, (int) round(2.2 * $s)));
    imagearc($img, $cx, $cy, (int) (44 * $s), (int) (44 * $s), 0, 360, $ring);
    imagesetthickness($img, max(2, (int) round(3.2 * $s)));
    // Arch: a half ellipse above the centre line.
    imagearc($img, $cx, (int) ($cy + 2 * $s), (int) (32 * $s), (int) (32 * $s), 180, 360, $green);
    // Trowel blade.
    imagefilledpolygon($img, [
        (int) ($cx - 6 * $s), (int) ($cy + 13 * $s),
        (int) ($cx),          (int) ($cy - 9 * $s),
        (int) ($cx + 6 * $s), (int) ($cy + 13 * $s),
    ], 3, $white);
    imagesetthickness($img, max(2, (int) round(3 * $s)));
    imageline($img, (int) ($cx - 10 * $s), (int) ($cy + 17 * $s), (int) ($cx + 10 * $s), (int) ($cy + 17 * $s), $white);
}

// --- Social card -------------------------------------------------------------

$card = imagecreatetruecolor(1200, 630);
imagealphablending($card, true);
// A diagonal gradient from ink-blue to deep green, drawn as thin bands.
for ($x = 0; $x < 1200; $x++) {
    for ($y = 0; $y < 630; $y += 2) {
        $t = min(1, max(0, ($x * 0.6 + $y * 0.8) / 1100));
        $r = (int) (11 + (11 - 11) * $t);
        $g = (int) (37 + (99 - 37) * $t);
        $b = (int) (69 + (74 - 69) * $t);
        imagesetpixel($card, $x, $y, imagecolorallocate($card, $r, $g, $b));
        imagesetpixel($card, $x, $y + 1, imagecolorallocate($card, $r, $g, $b));
    }
}

// Blueprint grid, faint.
$grid = hex($card, '#ffffff', 118);
for ($x = 0; $x < 1200; $x += 48) { imageline($card, $x, 0, $x, 629, $grid); }
for ($y = 0; $y < 630; $y += 48) { imageline($card, 0, $y, 1199, $y, $grid); }

// Floor band at the bottom.
imagefilledrectangle($card, 0, 500, 1199, 629, hex($card, '#dceaf7', 40));
imagefilledrectangle($card, 0, 500, 1199, 505, hex($card, '#14a077'));

draw_mark($card, 130, 150, 110);

$serif = serif_font(true);
$sans  = sans_font();
$white = hex($card, '#ffffff');
$mint  = hex($card, '#9fe3c5');
if ($serif !== null) {
    imagettftext($card, 70, 0, 96, 340, $white, $serif, 'Iris Epoxy Service');
    imagettftext($card, 32, 0, 96, 420, hex($card, '#ffffff', 10), serif_font(false) ?? $serif, 'Epoxy floors that outlast the building around them.');
}
if ($sans !== null) {
    imagettftext($card, 24, 0, 96, 478, $mint, $sans, 'EPOXY FLOORING  |  EPOXY SALES  |  LAGOS, NIGERIA');
}
imagepng($card, $out . 'social-card.png', 6);
echo "Wrote social-card.png\n";

// --- Square logo -------------------------------------------------------------

$logo = imagecreatetruecolor(512, 512);
imagesavealpha($logo, true);
$clear = imagecolorallocatealpha($logo, 0, 0, 0, 127);
imagefill($logo, 0, 0, $clear);
imagefilledellipse($logo, 256, 256, 500, 500, hex($logo, '#0b2545'));
draw_mark($logo, 256, 256, 380);
imagepng($logo, $out . 'logo-square.png', 6);
echo "Wrote logo-square.png\n";
