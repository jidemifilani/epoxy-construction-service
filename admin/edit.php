<?php
/** Create or edit one entry in any section defined in includes/entities.php. */

require_once __DIR__ . '/includes/admin.php';

$admin  = require_admin();
$key    = (string) ($_GET['e'] ?? ($_POST['e'] ?? ''));
$entity = admin_entity($key);
$id     = (int) ($_GET['id'] ?? ($_POST['id'] ?? 0));

$existing = $id > 0 ? entity_find($entity, $id) : null;
if ($id > 0 && $existing === null) {
    flash('error', 'That ' . $entity['singular'] . ' no longer exists.');
    admin_redirect('list?e=' . $key);
}

// Defaults for a new entry. Switches start on, so a new product is for sale
// as soon as it is saved, unless the admin turns it off.
$values = [];
foreach ($entity['fields'] as $name => $field) {
    $values[$name] = $existing[$name] ?? match ($field['type']) {
        'bool'  => in_array($name, ['published', 'in_stock'], true) ? 1 : 0,
        'number', 'decimal' => 0,
        default => '',
    };
}
if ($existing === null && isset($entity['fields']['art'])) {
    $values['art'] = 'metallic';
}
if ($existing === null && isset($entity['fields']['channel'])) {
    $values['channel'] = 'retail';
}
if ($existing === null && isset($entity['fields']['min_qty'])) {
    $values['min_qty'] = 1;
}

$errors = [];

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    require_admin_post();

    [$posted, $errors] = entity_read_post($entity, $id);
    if ($errors === []) {
        $savedId = entity_save($entity, $id, $posted);
        flash('success', ucfirst($entity['singular']) . ' saved.');
        admin_redirect('edit?e=' . $key . '&id=' . $savedId);
    }

    // Keep what was typed so nothing is lost on a validation error.
    $values = array_merge($values, $posted);
}

// The page title names the entry being edited, using its first field.
$firstField = array_key_first($entity['fields']);
$title = $id > 0
    ? 'Edit: ' . (string) ($values[$firstField] ?? $entity['singular'])
    : 'New ' . $entity['singular'];

admin_page_start($title, $key, $admin);
?>

<p class="admin-crumbs"><a href="<?= e(admin_url('list')) ?>?e=<?= e($key) ?>">&larr; All <?= e(strtolower($entity['label'])) ?></a></p>

<form method="post" action="<?= e(admin_url('edit')) ?>?e=<?= e($key) ?>&amp;id=<?= $id ?>" class="admin-card admin-form" novalidate>
  <?= csrf_field() ?>
  <input type="hidden" name="e" value="<?= e($key) ?>">
  <input type="hidden" name="id" value="<?= $id ?>">

  <?php if ($errors !== []): ?>
  <p class="admin-flash admin-flash--error" role="alert">Please fix the highlighted fields and save again.</p>
  <?php endif; ?>

  <div class="admin-grid">
    <?php foreach ($entity['fields'] as $name => $field):
        if ($field['type'] === 'bool') { continue; }
        $wide = in_array($field['type'], ['textarea'], true);
    ?>
    <div class="<?= $wide ? 'admin-grid__wide' : '' ?>">
      <?= admin_field($name, $field, $values[$name], $errors[$name] ?? null) ?>
    </div>
    <?php endforeach; ?>
  </div>

  <fieldset class="admin-switches">
    <legend>Visibility</legend>
    <?php foreach ($entity['fields'] as $name => $field):
        if ($field['type'] !== 'bool') { continue; }
        echo admin_field($name, $field, $values[$name], $errors[$name] ?? null);
    endforeach; ?>
  </fieldset>

  <div class="admin-actions-bar">
    <button class="admin-btn admin-btn--primary" type="submit">Save <?= e($entity['singular']) ?></button>
    <a class="admin-btn admin-btn--ghost" href="<?= e(admin_url('list')) ?>?e=<?= e($key) ?>">Cancel</a>
    <?php if ($id > 0): ?>
      <?= admin_post_form('delete', ['e' => $key, 'id' => $id], 'Delete', 'admin-btn--danger',
            'Delete this ' . $entity['singular'] . '? This cannot be undone.') ?>
    <?php endif; ?>
  </div>
</form>

<?php if ($key === 'products' && $id > 0):
    require_once __DIR__ . '/../includes/media.php';
    $productPhotos = db_rows(
        "SELECT * FROM media WHERE kind = 'image' AND attach_to = 'product' AND attach_id = ? ORDER BY sort, id",
        [$id]
    );
?>
<div class="admin-card" style="margin-top:1.5rem">
  <header class="admin-card__head">
    <h2>Product photos</h2>
    <a class="admin-btn admin-btn--primary admin-btn--sm"
       href="<?= e(admin_url('media')) ?>?target=product:<?= $id ?>">
      + Add / upload photos
    </a>
  </header>

  <?php if ($productPhotos === []): ?>
    <p class="admin-empty">No photos yet. Click <strong>Add / upload photos</strong> to attach images to this product. They appear in the shop automatically once published.</p>
  <?php else: ?>
  <div class="admin-media-grid">
    <?php foreach ($productPhotos as $photo): ?>
    <div class="admin-media-thumb">
      <?php if (!empty($photo['file_path'])): ?>
        <img src="<?= e(url((string) $photo['file_path'])) ?>" alt="<?= e($photo['alt_text'] ?: $photo['title']) ?>">
      <?php else: ?>
        <span class="admin-media-thumb__placeholder"><?= icon('gallery') ?></span>
      <?php endif; ?>
      <div class="admin-media-thumb__meta">
        <span><?= e($photo['title'] !== '' ? $photo['title'] : 'Untitled') ?></span>
        <?php if (!(bool) $photo['published']): ?>
          <span class="admin-badge admin-badge--draft">Draft</span>
        <?php endif; ?>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
  <p class="admin-muted" style="margin-top:.75rem;font-size:.8rem">
    <?= count($productPhotos) ?> photo<?= count($productPhotos) === 1 ? '' : 's' ?> attached.
    <a href="<?= e(admin_url('media')) ?>?target=product:<?= $id ?>">Manage in Photos &amp; Videos &rarr;</a>
  </p>
  <?php endif; ?>
</div>

<style>
.admin-media-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(120px,1fr));gap:.75rem;margin-top:.75rem}
.admin-media-thumb{border:1px solid var(--admin-border,#e2e8f0);border-radius:.375rem;overflow:hidden;background:var(--admin-bg2,#f8fafc)}
.admin-media-thumb img{width:100%;height:90px;object-fit:cover;display:block}
.admin-media-thumb__placeholder{display:flex;align-items:center;justify-content:center;height:90px;color:var(--admin-muted,#94a3b8)}
.admin-media-thumb__meta{padding:.35rem .5rem;font-size:.75rem;display:flex;justify-content:space-between;align-items:center;gap:.25rem}
.admin-media-thumb__meta span:first-child{overflow:hidden;white-space:nowrap;text-overflow:ellipsis}
</style>
<?php endif; ?>

<?php admin_page_end(); ?>
