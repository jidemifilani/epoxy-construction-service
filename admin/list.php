<?php
/** Generic list for any section defined in includes/entities.php. */

require_once __DIR__ . '/includes/admin.php';

$admin  = require_admin();
$key    = (string) ($_GET['e'] ?? '');
$entity = admin_entity($key);
$search = trim((string) ($_GET['q'] ?? ''));

$rows = entity_rows($entity, $search);
$listFields = array_filter($entity['fields'], static fn (array $f): bool => !empty($f['list']));

admin_page_start($entity['label'], $key, $admin);
?>

<section class="admin-card">
  <div class="admin-toolbar">
    <form method="get" action="<?= e(admin_url('list')) ?>" class="admin-search" role="search">
      <input type="hidden" name="e" value="<?= e($key) ?>">
      <label class="visually-hidden" for="q">Search <?= e(strtolower($entity['label'])) ?></label>
      <input type="search" id="q" name="q" value="<?= e($search) ?>" placeholder="Search <?= e(strtolower($entity['label'])) ?>">
      <button class="admin-btn admin-btn--ghost" type="submit">Search</button>
      <?php if ($search !== ''): ?><a href="<?= e(admin_url('list')) ?>?e=<?= e($key) ?>">Clear</a><?php endif; ?>
    </form>
    <a class="admin-btn admin-btn--primary" href="<?= e(admin_url('edit')) ?>?e=<?= e($key) ?>&amp;id=0">
      New <?= e($entity['singular']) ?>
    </a>
  </div>

  <?php if ($rows === []): ?>
    <p class="admin-empty"><?= $search !== '' ? 'Nothing matches that search.' : 'Nothing here yet. Add the first one.' ?></p>
  <?php else: ?>
  <div class="admin-table-wrap">
    <table class="admin-table">
      <thead>
        <tr>
          <?php foreach ($listFields as $field): ?><th scope="col"><?= e($field['label']) ?></th><?php endforeach; ?>
          <th scope="col"><span class="visually-hidden">Actions</span></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($rows as $row): ?>
        <tr>
          <?php foreach ($listFields as $name => $field):
              $value = $row[$name] ?? '';
              $isFirst = $name === array_key_first($listFields);
          ?>
          <td>
            <?php if ($isFirst): ?>
              <a href="<?= e(admin_url('edit')) ?>?e=<?= e($key) ?>&amp;id=<?= (int) $row['id'] ?>"><strong><?= e((string) $value) ?></strong></a>
            <?php else: ?>
              <?= admin_format_cell($field, $value, $name, $key) ?>
            <?php endif; ?>
          </td>
          <?php endforeach; ?>
          <td class="admin-actions">
            <a class="admin-btn admin-btn--ghost admin-btn--sm" href="<?= e(admin_url('edit')) ?>?e=<?= e($key) ?>&amp;id=<?= (int) $row['id'] ?>">Edit</a>
            <?= admin_post_form('delete', ['e' => $key, 'id' => $row['id']], 'Delete', 'admin-btn--danger admin-btn--sm',
                  'Delete this ' . $entity['singular'] . '? This cannot be undone.') ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <p class="admin-muted"><?= count($rows) ?> <?= count($rows) === 1 ? 'entry' : 'entries' ?></p>
  <?php endif; ?>
</section>

<?php admin_page_end(); ?>
