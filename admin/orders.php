<?php
/** Orders, newest first, filterable by status. */

require_once __DIR__ . '/includes/admin.php';

$admin  = require_admin();
$filter = (string) ($_GET['status'] ?? '');
$valid  = array_keys(admin_order_statuses());
if (!in_array($filter, $valid, true)) {
    $filter = '';
}

$rows = db_rows(
    'SELECT id, reference, channel, buyer_type, company_name, customer_name, phone, city, total, status, created_at
     FROM orders' . ($filter !== '' ? ' WHERE status = ?' : '') . ' ORDER BY created_at DESC, id DESC LIMIT 300',
    $filter !== '' ? [$filter] : []
);

$counts = [];
foreach (db_rows('SELECT status, COUNT(*) AS n FROM orders GROUP BY status') as $row) {
    $counts[$row['status']] = (int) $row['n'];
}

admin_page_start('Orders', 'orders', $admin);
?>

<nav class="admin-tabs" aria-label="Filter orders by status">
  <a href="<?= e(admin_url('orders')) ?>" class="<?= $filter === '' ? 'is-active' : '' ?>">All <span><?= array_sum($counts) ?></span></a>
  <?php foreach (admin_order_statuses() as $value => $label): ?>
  <a href="<?= e(admin_url('orders')) ?>?status=<?= e($value) ?>" class="<?= $filter === $value ? 'is-active' : '' ?>">
    <?= e($label) ?> <span><?= $counts[$value] ?? 0 ?></span>
  </a>
  <?php endforeach; ?>
</nav>

<section class="admin-card">
  <?php if ($rows === []): ?>
    <p class="admin-empty">No orders<?= $filter !== '' ? ' with that status' : '' ?> yet.</p>
  <?php else: ?>
  <div class="admin-table-wrap">
    <table class="admin-table">
      <thead>
        <tr><th>Reference</th><th>Placed</th><th>Customer</th><th>Channel</th><th>Total</th><th>Status</th></tr>
      </thead>
      <tbody>
        <?php foreach ($rows as $order): ?>
        <tr>
          <td><a href="<?= e(admin_url('order')) ?>?id=<?= (int) $order['id'] ?>"><strong><?= e($order['reference']) ?></strong></a></td>
          <td><?= e(admin_date($order['created_at'])) ?></td>
          <td>
            <?= e($order['company_name'] !== '' ? $order['company_name'] : $order['customer_name']) ?>
            <div class="admin-muted"><?= e($order['phone']) ?> &middot; <?= e($order['city']) ?></div>
          </td>
          <td><?= e(ucfirst($order['channel'])) ?></td>
          <td><?= e(admin_money($order['total'])) ?></td>
          <td><?= admin_status_badge('order', $order['status']) ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
</section>

<?php admin_page_end(); ?>
