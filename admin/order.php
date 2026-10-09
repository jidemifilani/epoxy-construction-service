<?php
/** One order: the customer, the lines, and the status the admin moves it through. */

require_once __DIR__ . '/includes/admin.php';

$admin = require_admin();
$id    = (int) ($_GET['id'] ?? ($_POST['id'] ?? 0));

$order = $id > 0 ? db_row('SELECT * FROM orders WHERE id = ?', [$id]) : null;
if ($order === null) {
    flash('error', 'That order could not be found.');
    admin_redirect('orders');
}

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    require_admin_post();

    $status = (string) ($_POST['status'] ?? '');
    if (!array_key_exists($status, admin_order_statuses())) {
        flash('error', 'Choose a valid status.');
    } else {
        $note = trim((string) ($_POST['admin_note'] ?? ''));
        db_update('orders', $id, [
            'status'     => $status,
            'admin_note' => mb_substr($note, 0, 2000),
        ]);
        flash('success', 'Order updated. Status: ' . admin_order_statuses()[$status] . '.');
    }
    admin_redirect('order?id=' . $id);
}

$items = db_rows('SELECT product_name, pack_label, unit_price, qty, line_total
                  FROM order_items WHERE order_id = ? ORDER BY id', [$id]);

admin_page_start('Order ' . $order['reference'], 'orders', $admin);
?>

<p class="admin-crumbs"><a href="<?= e(admin_url('orders')) ?>">&larr; All orders</a></p>

<section class="admin-two">
  <div class="admin-card">
    <header class="admin-card__head">
      <h2>Items</h2>
      <?= admin_status_badge('order', $order['status']) ?>
    </header>
    <div class="admin-table-wrap">
      <table class="admin-table">
        <thead><tr><th>Product</th><th>Pack</th><th>Price</th><th>Packs</th><th>Line total</th></tr></thead>
        <tbody>
          <?php foreach ($items as $item): ?>
          <tr>
            <td><?= e($item['product_name']) ?></td>
            <td><?= e($item['pack_label']) ?></td>
            <td><?= e(admin_money($item['unit_price'])) ?></td>
            <td><?= (int) $item['qty'] ?></td>
            <td><?= e(admin_money($item['line_total'])) ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
        <tfoot>
          <tr><th colspan="4">Subtotal</th><td><?= e(admin_money($order['subtotal'])) ?></td></tr>
          <tr><th colspan="4">Delivery</th><td><?= (float) $order['delivery_fee'] > 0 ? e(admin_money($order['delivery_fee'])) : 'Free' ?></td></tr>
          <tr><th colspan="4">Total</th><td><strong><?= e(admin_money($order['total'])) ?></strong></td></tr>
        </tfoot>
      </table>
    </div>
    <?php if ($order['notes'] !== null && $order['notes'] !== ''): ?>
    <h3 class="admin-h3">Customer's notes</h3>
    <p class="admin-quote"><?= nl2br(e($order['notes'])) ?></p>
    <?php endif; ?>
    <p class="admin-muted">Placed <?= e(admin_date($order['created_at'])) ?> from <?= e($order['ip_address']) ?></p>
    <?php if (in_array($order['status'], ['delivered', 'dispatched'], true) && cfg('google_review_url') !== ''):
        $firstName = explode(' ', trim((string) $order['customer_name']))[0];
        $message = 'Hello ' . $firstName . ', thank you for your order ' . $order['reference']
                 . ' from ' . cfg('company_name') . '. If you were happy with it, a short Google review helps other customers find us: '
                 . cfg('google_review_url');
        $phoneDigits = preg_replace('/[^0-9]/', '', (string) $order['phone']);
    ?>
    <p class="admin-review-request">
      <a class="admin-btn admin-btn--primary admin-btn--sm" target="_blank" rel="noopener"
         href="https://wa.me/<?= e($phoneDigits) ?>?text=<?= rawurlencode($message) ?>">Send review request on WhatsApp</a>
    </p>
    <?php endif; ?>
  </div>

  <div>
    <div class="admin-card">
      <header class="admin-card__head"><h2>Customer</h2></header>
      <dl class="admin-dl">
        <dt>Name</dt><dd><?= e($order['customer_name']) ?></dd>
        <?php if ($order['company_name'] !== ''): ?><dt>Company</dt><dd><?= e($order['company_name']) ?></dd><?php endif; ?>
        <dt>Phone</dt><dd><a href="tel:<?= e(preg_replace('/[^0-9+]/', '', $order['phone'])) ?>"><?= e($order['phone']) ?></a>
          &middot; <a href="https://wa.me/<?= e(preg_replace('/[^0-9]/', '', $order['phone'])) ?>" target="_blank" rel="noopener">WhatsApp</a></dd>
        <dt>Email</dt><dd><a href="mailto:<?= e($order['email']) ?>"><?= e($order['email']) ?></a></dd>
        <dt>Deliver to</dt><dd><?= e($order['address']) ?><?= $order['city'] !== '' ? ', ' . e($order['city']) : '' ?></dd>
        <dt>Channel</dt><dd><?= e(ucfirst($order['channel'])) ?> &middot; <?= e(ucfirst($order['buyer_type'])) ?></dd>
      </dl>
    </div>

    <form method="post" action="<?= e(admin_url('order')) ?>?id=<?= $id ?>" class="admin-card admin-form">
      <?= csrf_field() ?>
      <input type="hidden" name="id" value="<?= $id ?>">
      <header class="admin-card__head"><h2>Status</h2></header>
      <div class="admin-field">
        <label for="status">Order status</label>
        <select id="status" name="status">
          <?php foreach (admin_order_statuses() as $value => $label): ?>
          <option value="<?= e($value) ?>"<?= $order['status'] === $value ? ' selected' : '' ?>><?= e($label) ?></option>
          <?php endforeach; ?>
        </select>
        <small class="admin-help">Move it along as the order progresses. Customers are not emailed automatically.</small>
      </div>
      <div class="admin-field">
        <label for="admin_note">Internal note</label>
        <textarea id="admin_note" name="admin_note" rows="4" placeholder="Visible to staff only"><?= e((string) $order['admin_note']) ?></textarea>
      </div>
      <button class="admin-btn admin-btn--primary" type="submit">Save status</button>
    </form>
  </div>
</section>

<?php admin_page_end(); ?>
