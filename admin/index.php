<?php
/** Dashboard: what needs attention today, and the numbers behind the business. */

require_once __DIR__ . '/includes/admin.php';

$admin = require_admin();

$stats = [
    'new_orders'       => (int) db_value("SELECT COUNT(*) FROM orders WHERE status = 'new'"),
    'open_orders'      => (int) db_value("SELECT COUNT(*) FROM orders WHERE status IN ('new','confirmed','paid')"),
    'new_leads'        => (int) db_value("SELECT COUNT(*) FROM leads WHERE status = 'new'"),
    'new_training'     => (int) db_value("SELECT COUNT(*) FROM leads WHERE form = 'training' AND status = 'new'"),
    'revenue_month'    => (float) db_value("SELECT COALESCE(SUM(total),0) FROM orders
                                            WHERE status IN ('paid','dispatched','delivered')
                                            AND created_at >= DATE_FORMAT(NOW(), '%Y-%m-01')"),
    'out_of_stock'     => (int) db_value("SELECT COUNT(*) FROM products WHERE in_stock = 0 AND published = 1"),
    'projects'         => (int) db_value("SELECT COUNT(*) FROM projects WHERE published = 1"),
    'products'         => (int) db_value("SELECT COUNT(*) FROM products WHERE published = 1"),
];

$recentOrders = db_rows("SELECT id, reference, customer_name, company_name, channel, total, status, created_at
                         FROM orders ORDER BY created_at DESC, id DESC LIMIT 6");
$recentLeads  = db_rows("SELECT id, form, name, phone, location, status, created_at
                         FROM leads ORDER BY created_at DESC, id DESC LIMIT 6");

admin_page_start('Dashboard', 'dashboard', $admin);
?>

<section class="admin-stats">
  <a class="admin-stat" href="<?= e(admin_url('orders')) ?>?status=new">
    <span class="admin-stat__num"><?= $stats['new_orders'] ?></span>
    <span class="admin-stat__label">New orders</span>
  </a>
  <a class="admin-stat" href="<?= e(admin_url('leads')) ?>?status=new">
    <span class="admin-stat__num"><?= $stats['new_leads'] ?></span>
    <span class="admin-stat__label">New enquiries</span>
  </a>
  <div class="admin-stat">
    <span class="admin-stat__num"><?= e(admin_money($stats['revenue_month'])) ?></span>
    <span class="admin-stat__label">Paid or dispatched this month</span>
  </div>
  <a class="admin-stat" href="<?= e(admin_url('leads')) ?>?form=training">
    <span class="admin-stat__num"><?= $stats['new_training'] ?></span>
    <span class="admin-stat__label">New training registrations</span>
  </a>
  <a class="admin-stat <?= $stats['out_of_stock'] > 0 ? 'is-warn' : '' ?>" href="<?= e(admin_url('list')) ?>?e=products">
    <span class="admin-stat__num"><?= $stats['out_of_stock'] ?></span>
    <span class="admin-stat__label">Products out of stock</span>
  </a>
</section>

<section class="admin-two">
  <div class="admin-card">
    <header class="admin-card__head">
      <h2>Latest orders</h2>
      <a href="<?= e(admin_url('orders')) ?>">All orders &rarr;</a>
    </header>
    <?php if ($recentOrders === []): ?>
      <p class="admin-empty">No orders yet. They appear here as soon as a customer places one.</p>
    <?php else: ?>
    <table class="admin-table">
      <thead><tr><th>Reference</th><th>Customer</th><th>Total</th><th>Status</th></tr></thead>
      <tbody>
        <?php foreach ($recentOrders as $order): ?>
        <tr>
          <td><a href="<?= e(admin_url('order')) ?>?id=<?= (int) $order['id'] ?>"><?= e($order['reference']) ?></a></td>
          <td><?= e($order['company_name'] !== '' ? $order['company_name'] : $order['customer_name']) ?>
            <span class="admin-muted"><?= e(ucfirst($order['channel'])) ?></span></td>
          <td><?= e(admin_money($order['total'])) ?></td>
          <td><?= admin_status_badge('order', $order['status']) ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <?php endif; ?>
  </div>

  <div class="admin-card">
    <header class="admin-card__head">
      <h2>Latest enquiries</h2>
      <a href="<?= e(admin_url('leads')) ?>">All enquiries &rarr;</a>
    </header>
    <?php if ($recentLeads === []): ?>
      <p class="admin-empty">No enquiries yet.</p>
    <?php else: ?>
    <table class="admin-table">
      <thead><tr><th>From</th><th>Form</th><th>When</th><th>Status</th></tr></thead>
      <tbody>
        <?php foreach ($recentLeads as $lead): ?>
        <tr>
          <td><a href="<?= e(admin_url('lead')) ?>?id=<?= (int) $lead['id'] ?>"><?= e($lead['name'] !== '' ? $lead['name'] : $lead['phone']) ?></a>
            <span class="admin-muted"><?= e($lead['location']) ?></span></td>
          <td><?= e(ucfirst($lead['form'])) ?></td>
          <td><?= e(admin_date($lead['created_at'])) ?></td>
          <td><?= admin_status_badge('lead', $lead['status']) ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <?php endif; ?>
  </div>
</section>

<section class="admin-card">
  <header class="admin-card__head"><h2>Edit the website</h2></header>
  <div class="admin-quick">
    <a href="<?= e(admin_url('list')) ?>?e=projects"><strong><?= $stats['projects'] ?></strong> published projects</a>
    <a href="<?= e(admin_url('list')) ?>?e=products"><strong><?= $stats['products'] ?></strong> products for sale</a>
    <a href="<?= e(admin_url('list')) ?>?e=testimonials">Testimonials</a>
    <a href="<?= e(admin_url('list')) ?>?e=faqs">FAQs</a>
    <a href="<?= e(admin_url('list')) ?>?e=articles">Blog articles</a>
    <a href="<?= e(admin_url('leads')) ?>?form=training">Training registrations</a>
    <a href="<?= e(admin_url('list')) ?>?e=jobs">Job openings</a>
    <a href="<?= e(admin_url('settings')) ?>">Company details, prices and bank account</a>
  </div>
</section>

<?php admin_page_end(); ?>
