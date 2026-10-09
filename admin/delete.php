<?php
/** Deletes one entry. POST only, with a valid CSRF token. */

require_once __DIR__ . '/includes/admin.php';

$admin = require_admin();
require_admin_post();

$key    = (string) ($_POST['e'] ?? '');
$entity = admin_entity($key);
$id     = (int) ($_POST['id'] ?? 0);

if ($id > 0 && entity_find($entity, $id) !== null) {
    entity_delete($entity, $id);
    flash('success', ucfirst($entity['singular']) . ' deleted.');
} else {
    flash('error', 'Nothing was deleted; it may already have been removed.');
}

admin_redirect('list?e=' . $key);
