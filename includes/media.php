<?php
/**
 * Photos and videos.
 *
 * Photos are uploaded through the admin, checked on the server (real image
 * data, size and type), and saved as WebP at a sensible maximum width, which
 * keeps pages light on Nigerian mobile connections. Videos are either uploaded
 * (MP4 or WebM) or linked from YouTube or Vimeo, so large files never have to
 * go through the site.
 */

declare(strict_types=1);

const MEDIA_DIR          = 'assets/uploads/media';
const MEDIA_MAX_IMAGE    = 8 * 1024 * 1024;     // bytes, before conversion
const MEDIA_MAX_VIDEO    = 40 * 1024 * 1024;    // bytes; matches PHP's upload limit
const MEDIA_MAX_WIDTH    = 1600;                // photos are scaled down to this
const MEDIA_ATTACH_TYPES = ['gallery', 'project', 'product'];

function media_disk_path(string $relative): string
{
    return __DIR__ . '/../' . $relative;
}

/** The first published photo attached to an item, or null. */
function media_first(string $attachTo, int $attachId): ?array
{
    return db_row("SELECT * FROM media WHERE kind = 'image' AND published = 1
                   AND attach_to = ? AND attach_id = ? ORDER BY sort, id LIMIT 1",
                   [$attachTo, $attachId]);
}

/** Every published item, optionally filtered by kind ('image' or 'video'). */
function media_list(?string $kind = null): array
{
    if ($kind === null) {
        return db_rows('SELECT * FROM media WHERE published = 1 ORDER BY sort, id');
    }
    return db_rows('SELECT * FROM media WHERE published = 1 AND kind = ? ORDER BY sort, id', [$kind]);
}

/**
 * The YouTube or Vimeo embed address for a link, or null if the link is not one of those.
 * YouTube uses the privacy-enhanced domain, so nothing is set on the visitor's device until they press play.
 */
function media_embed_url(string $link): ?string
{
    $link = trim($link);
    if (preg_match('~(?:youtube\.com/(?:watch\?(?:.*&)?v=|embed/|shorts/)|youtu\.be/)([A-Za-z0-9_-]{11})~', $link, $m)) {
        return 'https://www.youtube-nocookie.com/embed/' . $m[1];
    }
    if (preg_match('~vimeo\.com/(?:video/)?(\d+)~', $link, $m)) {
        return 'https://player.vimeo.com/video/' . $m[1];
    }
    return null;
}

/** Responsive image markup. The width and height prevent layout shift while it loads. */
function media_img(array $item, string $sizes = '(min-width: 900px) 33vw, 100vw', bool $eager = false, string $class = 'media-img'): string
{
    $src = e(url($item['file_path']));
    $alt = e($item['alt_text'] !== '' ? $item['alt_text'] : $item['title']);
    $w   = (int) $item['width'];
    $h   = (int) $item['height'];
    return '<img class="' . e($class) . '" src="' . $src . '" alt="' . $alt . '"'
         . ($w > 0 ? ' width="' . $w . '" height="' . $h . '"' : '')
         . ' sizes="' . e($sizes) . '" loading="' . ($eager ? 'eager' : 'lazy') . '" decoding="async">';
}

/**
 * A video. Uploaded files use the native player with metadata preloaded only;
 * linked videos use a facade-style iframe with a title for screen readers.
 */
function media_video(array $item): string
{
    if (!empty($item['file_path'])) {
        $poster = !empty($item['thumbnail']) ? ' poster="' . e(url($item['thumbnail'])) . '"' : '';
        return '<video class="media-video" controls preload="metadata" playsinline' . $poster . '>'
             . '<source src="' . e(url($item['file_path'])) . '" type="' . e($item['mime']) . '">'
             . 'Your browser cannot play this video.</video>';
    }
    $embed = media_embed_url((string) $item['video_url']);
    if ($embed === null) {
        return '<p class="field__hint">Video unavailable.</p>';
    }
    return '<div class="media-embed"><iframe src="' . e($embed) . '" title="' . e($item['title']) . '"'
         . ' loading="lazy" allow="accelerometer; encrypted-media; gyroscope; picture-in-picture" allowfullscreen'
         . ' referrerpolicy="strict-origin-when-cross-origin"></iframe></div>';
}

/**
 * The visual for a project card: the attached photo if there is one, otherwise
 * the drawn artwork that has always been there.
 */
function project_visual(array $project): string
{
    $photo = !empty($project['id']) ? media_first('project', (int) $project['id']) : null;
    if ($photo !== null) {
        return media_img($photo, '(min-width: 900px) 33vw, 100vw', false, 'art media-img');
    }
    return project_art($project['art']);
}

// --- Upload -----------------------------------------------------------------

/**
 * Validates and stores an uploaded photo or video. Returns the new row's id,
 * or throws InvalidArgumentException with a message the admin can read.
 */
function media_store_upload(array $file, array $meta): int
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        throw new InvalidArgumentException(media_upload_error((int) ($file['error'] ?? UPLOAD_ERR_NO_FILE)));
    }
    $tmp = (string) $file['tmp_name'];
    $size = (int) filesize($tmp);
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = (string) $finfo->file($tmp);

    $dir = media_disk_path(MEDIA_DIR);
    if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
        throw new RuntimeException('Could not create the uploads folder.');
    }

    $title = $meta['title'];
    $base  = bin2hex(random_bytes(10));

    if (in_array($mime, ['image/jpeg', 'image/png', 'image/webp'], true)) {
        if ($size > MEDIA_MAX_IMAGE) {
            throw new InvalidArgumentException('Photos must be 8 MB or smaller. Export a smaller JPEG and try again.');
        }
        $image = match ($mime) {
            'image/jpeg' => @imagecreatefromjpeg($tmp),
            'image/png'  => @imagecreatefrompng($tmp),
            default      => @imagecreatefromwebp($tmp),
        };
        if ($image === false) {
            throw new InvalidArgumentException('That file is not a readable photo.');
        }
        // Scale down, keeping the aspect ratio, then store as WebP.
        $w = imagesx($image);
        $h = imagesy($image);
        if ($w > MEDIA_MAX_WIDTH) {
            $ratio = MEDIA_MAX_WIDTH / $w;
            $scaled = imagescale($image, MEDIA_MAX_WIDTH, (int) round($h * $ratio));
            imagedestroy($image);
            $image = $scaled;
            $w = imagesx($image);
            $h = imagesy($image);
        }
        if (function_exists('imagewebp')) {
            $relative = MEDIA_DIR . '/' . $base . '.webp';
            imagewebp($image, media_disk_path($relative), 82);
            $mime = 'image/webp';
        } else {
            $relative = MEDIA_DIR . '/' . $base . ($mime === 'image/png' ? '.png' : '.jpg');
            $mime === 'image/png' ? imagepng($image, media_disk_path($relative)) : imagejpeg($image, media_disk_path($relative), 85);
        }
        imagedestroy($image);

        return db_insert('media', [
            'kind'      => 'image',
            'title'     => $title,
            'alt_text'  => $meta['alt_text'],
            'caption'   => $meta['caption'],
            'file_path' => $relative,
            'mime'      => $mime,
            'width'     => $w,
            'height'    => $h,
            'bytes'     => (int) filesize(media_disk_path($relative)),
            'attach_to' => $meta['attach_to'],
            'attach_id' => $meta['attach_id'],
            'sort'      => $meta['sort'],
            'published' => 1,
        ]);
    }

    if (in_array($mime, ['video/mp4', 'video/webm'], true)) {
        if ($size > MEDIA_MAX_VIDEO) {
            throw new InvalidArgumentException('Video files must be 40 MB or smaller. Link a YouTube or Vimeo video instead for longer films.');
        }
        $ext = $mime === 'video/webm' ? 'webm' : 'mp4';
        $relative = MEDIA_DIR . '/' . $base . '.' . $ext;
        if (!move_uploaded_file($tmp, media_disk_path($relative))) {
            throw new RuntimeException('The video could not be saved.');
        }
        return db_insert('media', [
            'kind'      => 'video',
            'title'     => $title,
            'alt_text'  => $meta['alt_text'],
            'caption'   => $meta['caption'],
            'file_path' => $relative,
            'mime'      => $mime,
            'bytes'     => $size,
            'attach_to' => $meta['attach_to'],
            'attach_id' => $meta['attach_id'],
            'sort'      => $meta['sort'],
            'published' => 1,
        ]);
    }

    throw new InvalidArgumentException('Upload a JPEG, PNG or WebP photo, or an MP4 or WebM video. For other videos, paste a YouTube or Vimeo link.');
}

/** Saves a linked (YouTube or Vimeo) video. Returns the new row id. */
function media_store_link(string $link, array $meta): int
{
    if (media_embed_url($link) === null) {
        throw new InvalidArgumentException('Paste a YouTube or Vimeo link, for example https://www.youtube.com/watch?v=...');
    }
    return db_insert('media', [
        'kind'       => 'video',
        'title'      => $meta['title'],
        'alt_text'   => $meta['alt_text'],
        'caption'    => $meta['caption'],
        'video_url'  => mb_substr(trim($link), 0, 255),
        'mime'       => 'text/html',
        'attach_to'  => $meta['attach_to'],
        'attach_id'  => $meta['attach_id'],
        'sort'       => $meta['sort'],
        'published'  => 1,
    ]);
}

/** Removes the row and its files from disk. */
function media_delete(int $id): void
{
    $item = db_row('SELECT file_path, thumbnail FROM media WHERE id = ?', [$id]);
    if ($item === null) {
        return;
    }
    foreach (['file_path', 'thumbnail'] as $column) {
        $relative = (string) ($item[$column] ?? '');
        // Only ever delete inside the uploads folder, whatever the database says.
        if ($relative !== '' && str_starts_with($relative, MEDIA_DIR . '/') && is_file(media_disk_path($relative))) {
            unlink(media_disk_path($relative));
        }
    }
    db_exec('DELETE FROM media WHERE id = ?', [$id]);
}

function media_upload_error(int $code): string
{
    return match ($code) {
        UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => 'That file is larger than the server allows (40 MB).',
        UPLOAD_ERR_PARTIAL                        => 'The upload was interrupted. Please try again.',
        UPLOAD_ERR_NO_FILE                        => 'Choose a file to upload, or paste a video link.',
        default                                   => 'The upload failed. Please try again.',
    };
}
