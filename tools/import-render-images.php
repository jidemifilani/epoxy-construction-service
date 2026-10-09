<?php
/**
 * Imports the rendered product and project images into the media library, and
 * attaches each one to its product or project. Run after the PNGs exist in
 * assets/img/render-tmp/ (see tools/render-brand-art.php).
 *
 *   php tools/import-render-images.php
 *
 * Safe to run again: an image already attached to the same item is skipped.
 * Afterwards the render folder is removed.
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/media.php';

$dir      = __DIR__ . '/../assets/img/render-tmp/';
$manifest = json_decode((string) file_get_contents($dir . 'manifest.json'), true);
if (!is_array($manifest)) {
    fwrite(STDERR, "Run tools/render-brand-art.php first.\n");
    exit(1);
}

$imported = 0;
$skipped  = 0;
foreach ($manifest as $item) {
    $png = $dir . $item['png'];
    if (!is_file($png)) {
        fwrite(STDERR, 'Missing ' . $item['png'] . "\n");
        continue;
    }

    if ($item['kind'] === 'product') {
        $attachTo = 'product';
        $attachId = (int) $item['id'];
        $title    = $item['title'];
        $alt      = $item['title'] . ' packaging, Iris Epoxy Service';
        $caption  = 'Packaging for ' . $item['title'];
    } else {
        $project = db_row('SELECT id FROM projects WHERE slug = ?', [$item['slug']]);
        if ($project === null) {
            fwrite(STDERR, 'No project for ' . $item['slug'] . "\n");
            continue;
        }
        $attachTo = 'project';
        $attachId = (int) $project['id'];
        $title    = $item['title'];
        $alt      = 'Epoxy floor in ' . $item['title'] . ', Lagos';
        $caption  = $item['title'] . ' (illustration of the finished floor)';
    }

    $exists = db_value('SELECT id FROM media WHERE attach_to = ? AND attach_id = ? AND title = ?',
                       [$attachTo, $attachId, $title]);
    if ($exists !== null) {
        $skipped++;
        continue;
    }

    try {
        media_store_upload([
            'error'    => UPLOAD_ERR_OK,
            'tmp_name' => $png,
        ], [
            'title'     => $title,
            'alt_text'  => $alt,
            'caption'   => $caption,
            'attach_to' => $attachTo,
            'attach_id' => $attachId,
            'sort'      => 0,
        ]);
        $imported++;
    } catch (Throwable $e) {
        fwrite(STDERR, $item['png'] . ': ' . $e->getMessage() . "\n");
    }
}

fwrite(STDOUT, "Imported $imported, already present $skipped.\n");

// Tidy up: the renders are only needed for the import.
foreach (glob($dir . '*') ?: [] as $file) {
    unlink($file);
}
rmdir($dir);
