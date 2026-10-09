<?php
/** Enquiries from the contact, quote, trade and training forms, newest first. */

require_once __DIR__ . '/includes/admin.php';

$admin  = require_admin();
$filter = (string) ($_GET['status'] ?? '');
if (!array_key_exists($filter, admin_lead_statuses())) {
    $filter = '';
}

// Allowed form types are discovered from the DB so new form names appear automatically.
$formFilter = (string) ($_GET['form'] ?? '');
$knownForms = array_column(db_rows('SELECT DISTINCT form FROM leads ORDER BY form'), 'form');
if ($formFilter !== '' && !in_array($formFilter, $knownForms, true)) {
    $formFilter = '';
}

$where  = [];
$params = [];
if ($filter !== '') {
    $where[]  = 'status = ?';
    $params[] = $filter;
}
if ($formFilter !== '') {
    $where[]  = 'form = ?';
    $params[] = $formFilter;
}
$sql = 'SELECT id, form, name, email, phone, location, status, created_at FROM leads';
if ($where !== []) {
    $sql .= ' WHERE ' . implode(' AND ', $where);
}
$sql .= ' ORDER BY created_at DESC, id DESC LIMIT 300';
$rows = db_rows($sql, $params);

$counts = [];
foreach (db_rows('SELECT status, COUNT(*) AS n FROM leads GROUP BY status') as $row) {
    $counts[$row['status']] = (int) $row['n'];
}

// Count per form type (unfiltered by status) for the form dropdown labels.
$formCounts = [];
foreach (db_rows('SELECT form, COUNT(*) AS n FROM leads GROUP BY form') as $row) {
    $formCounts[$row['form']] = (int) $row['n'];
}

/** Build a URL for the leads page preserving the other active filter. */
function leads_url(string $status = '', string $form = ''): string
{
    $params = [];
    if ($status !== '') { $params[] = 'status=' . urlencode($status); }
    if ($form   !== '') { $params[] = 'form='   . urlencode($form); }
    return admin_url('leads') . ($params !== [] ? '?' . implode('&', $params) : '');
}

admin_page_start('Enquiries', 'leads', $admin);
?>

<nav class="admin-tabs" aria-label="Filter enquiries by status">
  <a href="<?= e(leads_url('', $formFilter)) ?>" class="<?= $filter === '' ? 'is-active' : '' ?>">All <span><?= array_sum($counts) ?></span></a>
  <?php foreach (admin_lead_statuses() as $value => $label): ?>
  <a href="<?= e(leads_url($value, $formFilter)) ?>" class="<?= $filter === $value ? 'is-active' : '' ?>">
    <?= e($label) ?> <span><?= $counts[$value] ?? 0 ?></span>
  </a>
  <?php endforeach; ?>
</nav>

<section class="admin-card">
  <?php if ($knownForms !== []): ?>
  <div class="admin-toolbar">
    <form method="get" action="<?= e(admin_url('leads')) ?>" class="admin-search">
      <?php if ($filter !== ''): ?><input type="hidden" name="status" value="<?= e($filter) ?>"><?php endif; ?>
      <label class="visually-hidden" for="form-filter">Filter by form</label>
      <select id="form-filter" name="form" onchange="this.form.submit()">
        <option value="">All forms (<?= array_sum($formCounts) ?>)</option>
        <?php foreach ($knownForms as $f): ?>
        <option value="<?= e($f) ?>"<?= $formFilter === $f ? ' selected' : '' ?>><?= e(ucfirst($f)) ?> (<?= $formCounts[$f] ?? 0 ?>)</option>
        <?php endforeach; ?>
      </select>
      <noscript><button class="admin-btn admin-btn--ghost" type="submit">Filter</button></noscript>
    </form>
    <?php if ($formFilter !== '' || $filter !== ''): ?>
    <a class="admin-btn admin-btn--ghost admin-btn--sm" href="<?= e(admin_url('leads')) ?>">Clear filters</a>
    <?php endif; ?>
  </div>
  <?php endif; ?>

  <?php if ($rows === []): ?>
    <p class="admin-empty">No enquiries<?= $filter !== '' || $formFilter !== '' ? ' match this filter' : '' ?> yet.</p>
  <?php else: ?>
  <div class="admin-table-wrap">
    <table class="admin-table">
      <thead><tr><th>From</th><th>Form</th><th>Location</th><th>Received</th><th>Status</th></tr></thead>
      <tbody>
        <?php foreach ($rows as $lead): ?>
        <tr>
          <td>
            <a href="<?= e(admin_url('lead')) ?>?id=<?= (int) $lead['id'] ?>"><strong><?= e($lead['name'] !== '' ? $lead['name'] : '(no name)') ?></strong></a>
            <div class="admin-muted"><?= e($lead['phone']) ?><?= $lead['email'] !== '' ? ' &middot; ' . e($lead['email']) : '' ?></div>
          </td>
          <td>
            <a href="<?= e(leads_url($filter, $lead['form'])) ?>" class="admin-pill"><?= e(ucfirst($lead['form'])) ?></a>
          </td>
          <td><?= e($lead['location']) ?></td>
          <td><?= e(admin_date($lead['created_at'])) ?></td>
          <td><?= admin_status_badge('lead', $lead['status']) ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
</section>

<?php admin_page_end(); ?>
