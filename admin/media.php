<?php
/** Media library: upload photos and videos, paste video links, attach them, remove them. */

require_once __DIR__ . '/includes/admin.php';

$admin = require_admin();

// --- Actions ---------------------------------------------------------------

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    require_admin_post();
    $action = (string) ($_POST['action'] ?? '');

    if ($action === 'delete') {
        media_delete((int) ($_POST['id'] ?? 0));
        flash('success', 'Removed from the library.');
        admin_redirect('media');
    }

    if ($action === 'upload' || $action === 'link') {
        // The target is one dropdown: 'gallery', 'project:12' or 'product:7'.
        $target = (string) ($_POST['target'] ?? 'gallery');
        $attachTo = 'gallery';
        $attachId = 0;
        if (preg_match('/^(project|product):(\d+)$/', $target, $m)) {
            $attachTo = $m[1];
            $attachId = (int) $m[2];
        }
        $title = trim((string) ($_POST['title'] ?? ''));
        $meta = [
            'title'     => $title !== '' ? mb_substr($title, 0, 160) : 'Untitled',
            'alt_text'  => mb_substr(trim((string) ($_POST['alt_text'] ?? '')), 0, 255),
            'caption'   => mb_substr(trim((string) ($_POST['caption'] ?? '')), 0, 400),
            'attach_to' => $attachTo,
            'attach_id' => $attachId,
            'sort'      => (int) ($_POST['sort'] ?? 0),
        ];

        try {
            if ($action === 'link') {
                media_store_link((string) ($_POST['video_url'] ?? ''), $meta);
            } else {
                if (!isset($_FILES['file'])) {
                    throw new InvalidArgumentException('Choose a file to upload.');
                }
                media_store_upload($_FILES['file'], $meta);
            }
            flash('success', $action === 'link' ? 'Video linked.' : 'Uploaded.');
        } catch (InvalidArgumentException $e) {
            flash('error', $e->getMessage());
        } catch (Throwable $e) {
            error_log('[iris] media upload failed: ' . $e->getMessage());
            flash('error', 'The file could not be saved. Please try again.');
        }
        admin_redirect('media');
    }

    admin_redirect('media');
}

// --- Page ------------------------------------------------------------------

$items = db_rows('SELECT * FROM media ORDER BY attach_to, sort, id DESC');
$projects = db_rows('SELECT id, name FROM projects ORDER BY sort, id');
$products = db_rows("SELECT id, name, pack_label FROM products ORDER BY channel, sort, id");

// Pre-select the target dropdown when arriving from a product edit page (?target=product:7)
$preselect = (string) ($_GET['target'] ?? 'gallery');
if (!preg_match('/^(gallery|project:\d+|product:\d+)$/', $preselect)) {
    $preselect = 'gallery';
}

/** Where an item is shown, as a short label for the list. */
function media_attach_label(array $item, array $projects, array $products): string
{
    if ($item['attach_to'] === 'gallery') {
        return 'Gallery';
    }
    if ($item['attach_to'] === 'project') {
        foreach ($projects as $p) {
            if ((int) $p['id'] === (int) $item['attach_id']) {
                return 'Project: ' . $p['name'];
            }
        }
        return 'Project (removed)';
    }
    foreach ($products as $p) {
        if ((int) $p['id'] === (int) $item['attach_id']) {
            return 'Product: ' . $p['name'] . ' (' . $p['pack_label'] . ')';
        }
    }
    return 'Product (removed)';
}

admin_page_start('Media library', 'media', $admin);
?>

<section class="admin-two">
  <div class="admin-card">
    <header class="admin-card__head"><h2>Upload a photo or video</h2></header>
    <form method="post" action="<?= e(admin_url('media')) ?>" enctype="multipart/form-data" class="admin-form">
      <?= csrf_field() ?>
      <input type="hidden" name="action" value="upload">
      <div class="admin-field">
        <label for="file">File</label>
        <input type="file" id="file" name="file" accept="image/jpeg,image/png,image/webp,video/mp4,video/webm" required>
        <small class="admin-help">Photos: JPEG, PNG or WebP up to 8 MB (saved as WebP). Videos: MP4 or WebM up to 40 MB. For longer films, use the link form.</small>
      </div>
      <?= media_form_fields($projects, $products, $preselect) ?>
      <button class="admin-btn admin-btn--primary" type="submit">Upload</button>
    </form>
  </div>

  <div class="admin-card">
    <header class="admin-card__head"><h2>Or link a YouTube or Vimeo video</h2></header>
    <form method="post" action="<?= e(admin_url('media')) ?>" class="admin-form">
      <?= csrf_field() ?>
      <input type="hidden" name="action" value="link">
      <div class="admin-field">
        <label for="video_url">Video link</label>
        <input type="url" id="video_url" name="video_url" placeholder="https://www.youtube.com/watch?v=..." required>
        <small class="admin-help">Hosting the film on YouTube keeps the site fast and the video searchable.</small>
      </div>
      <?= media_form_fields($projects, $products, $preselect) ?>
      <button class="admin-btn admin-btn--primary" type="submit">Link video</button>
    </form>
  </div>
</section>

<section class="admin-card">
  <header class="admin-card__head"><h2>Library (<?= count($items) ?>)</h2></header>
  <?php if ($items === []): ?>
    <p class="admin-empty">Nothing uploaded yet. Add a photo or link a video above.</p>
  <?php else: ?>
  <div class="admin-table-wrap">
    <table class="admin-table">
      <thead><tr><th>Preview</th><th>Title</th><th>Type</th><th>Shown on</th><th>Order</th><th><span class="visually-hidden">Actions</span></th></tr></thead>
      <tbody>
        <?php foreach ($items as $item): ?>
        <tr>
          <td class="admin-thumb">
            <?php if ($item['kind'] === 'image' && $item['file_path']): ?>
              <img src="<?= e(url($item['file_path'])) ?>" alt="" width="72" height="54" loading="lazy">
            <?php elseif ($item['kind'] === 'video' && $item['file_path']): ?>
              <span class="admin-pill">MP4 / WebM</span>
            <?php else: ?>
              <span class="admin-pill is-progress">Video link</span>
            <?php endif; ?>
          </td>
          <td>
            <strong><?= e($item['title']) ?></strong>
            <div class="admin-muted"><?= e($item['alt_text'] !== '' ? 'Alt: ' . $item['alt_text'] : 'No alt text yet') ?></div>
          </td>
          <td><?= e(ucfirst($item['kind'])) ?></td>
          <td><?= e(media_attach_label($item, $projects, $products)) ?></td>
          <td><?= (int) $item['sort'] ?></td>
          <td class="admin-actions">
            <?= admin_post_form('media', ['action' => 'delete', 'id' => $item['id']], 'Remove', 'admin-btn--danger admin-btn--sm',
                  'Remove this from the library? Its file is deleted too.') ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
</section>

<?php admin_page_end(); ?>
