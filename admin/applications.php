<?php
/** Job applications, newest first, filterable by status and by job. */

require_once __DIR__ . '/includes/admin.php';

$admin  = require_admin();
$filter = (string) ($_GET['status'] ?? '');
if (!array_key_exists($filter, application_statuses())) {
    $filter = '';
}
$jobFilter = (int) ($_GET['job'] ?? 0);

$where = [];
$params = [];
if ($filter !== '') {
    $where[] = 'status = ?';
    $params[] = $filter;
}
if ($jobFilter > 0) {
    $where[] = 'job_id = ?';
    $params[] = $jobFilter;
}
$sql = 'SELECT id, job_id, job_title, applicant_name, email, phone, status, created_at FROM job_applications';
if ($where !== []) {
    $sql .= ' WHERE ' . implode(' AND ', $where);
}
$sql .= ' ORDER BY created_at DESC, id DESC LIMIT 300';
$rows = db_rows($sql, $params);

$counts = [];
foreach (db_rows('SELECT status, COUNT(*) AS n FROM job_applications GROUP BY status') as $row) {
    $counts[$row['status']] = (int) $row['n'];
}
$jobs = db_rows('SELECT id, title FROM jobs ORDER BY sort, id');

admin_page_start('Applications', 'applications', $admin);
?>

<nav class="admin-tabs" aria-label="Filter applications by status">
  <a href="<?= e(admin_url('applications')) ?>" class="<?= $filter === '' ? 'is-active' : '' ?>">All <span><?= array_sum($counts) ?></span></a>
  <?php foreach (application_statuses() as $value => $label): ?>
  <a href="<?= e(admin_url('applications')) ?>?status=<?= e($value) ?>" class="<?= $filter === $value ? 'is-active' : '' ?>">
    <?= e($label) ?> <span><?= $counts[$value] ?? 0 ?></span>
  </a>
  <?php endforeach; ?>
</nav>

<section class="admin-card">
  <div class="admin-toolbar">
    <form method="get" action="<?= e(admin_url('applications')) ?>" class="admin-search">
      <?php if ($filter !== ''): ?><input type="hidden" name="status" value="<?= e($filter) ?>"><?php endif; ?>
      <label class="visually-hidden" for="job">Filter by job</label>
      <select id="job" name="job" onchange="this.form.submit()">
        <option value="0">All roles</option>
        <?php foreach ($jobs as $job): ?>
        <option value="<?= (int) $job['id'] ?>"<?= $jobFilter === (int) $job['id'] ? ' selected' : '' ?>><?= e($job['title']) ?></option>
        <?php endforeach; ?>
      </select>
      <noscript><button class="admin-btn admin-btn--ghost" type="submit">Filter</button></noscript>
    </form>
  </div>

  <?php if ($rows === []): ?>
    <p class="admin-empty">No applications<?= $filter !== '' || $jobFilter > 0 ? ' match this filter' : ' yet' ?>.</p>
  <?php else: ?>
  <div class="admin-table-wrap">
    <table class="admin-table">
      <thead><tr><th>Applicant</th><th>Role</th><th>Received</th><th>Status</th></tr></thead>
      <tbody>
        <?php foreach ($rows as $application): ?>
        <tr>
          <td>
            <a href="<?= e(admin_url('application')) ?>?id=<?= (int) $application['id'] ?>"><strong><?= e($application['applicant_name']) ?></strong></a>
            <div class="admin-muted"><?= e($application['phone']) ?></div>
          </td>
          <td><?= e($application['job_title']) ?></td>
          <td><?= e(admin_date($application['created_at'])) ?></td>
          <td><?= admin_status_badge('application', $application['status']) ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
</section>

<?php admin_page_end(); ?>
