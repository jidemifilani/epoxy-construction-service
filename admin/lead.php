<?php
/** One enquiry: everything the visitor sent, and the status it has reached. */

require_once __DIR__ . '/includes/admin.php';

$admin = require_admin();
$id    = (int) ($_GET['id'] ?? ($_POST['id'] ?? 0));

$lead = $id > 0 ? db_row('SELECT * FROM leads WHERE id = ?', [$id]) : null;
if ($lead === null) {
    flash('error', 'That enquiry could not be found.');
    admin_redirect('leads');
}

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    require_admin_post();

    $status = (string) ($_POST['status'] ?? '');
    if (!array_key_exists($status, admin_lead_statuses())) {
        flash('error', 'Choose a valid status.');
    } else {
        db_update('leads', $id, [
            'status'     => $status,
            'admin_note' => mb_substr(trim((string) ($_POST['admin_note'] ?? '')), 0, 2000),
        ]);
        flash('success', 'Enquiry updated.');
    }
    admin_redirect('lead?id=' . $id);
}

$phoneDigits = preg_replace('/[^0-9]/', '', (string) $lead['phone']);

admin_page_start('Enquiry from ' . ($lead['name'] !== '' ? $lead['name'] : 'a visitor'), 'leads', $admin);
?>

<p class="admin-crumbs"><a href="<?= e(admin_url('leads')) ?>">&larr; All enquiries</a></p>

<section class="admin-two">
  <div class="admin-card">
    <header class="admin-card__head">
      <h2><?= e(ucfirst($lead['form'])) ?> enquiry</h2>
      <span class="admin-muted"><?= e(admin_date($lead['created_at'])) ?></span>
    </header>
    <dl class="admin-dl">
      <dt>Name</dt><dd><?= e($lead['name'] !== '' ? $lead['name'] : '-') ?></dd>
      <dt>Phone</dt><dd><a href="tel:<?= e($phoneDigits) ?>"><?= e($lead['phone']) ?></a>
        &middot; <a href="https://wa.me/<?= e($phoneDigits) ?>" target="_blank" rel="noopener">WhatsApp</a></dd>
      <dt>Email</dt><dd><?= $lead['email'] !== '' ? '<a href="mailto:' . e($lead['email']) . '">' . e($lead['email']) . '</a>' : '-' ?></dd>
      <dt>Location</dt><dd><?= e($lead['location'] !== '' ? $lead['location'] : '-') ?></dd>
    </dl>

    <h3 class="admin-h3">What they told us</h3>
    <?php if ((string) $lead['details'] !== ''): ?>
      <ul class="admin-lines">
        <?php foreach (explode("\n", (string) $lead['details']) as $line): ?>
        <li><?= e($line) ?></li>
        <?php endforeach; ?>
      </ul>
    <?php else: ?>
      <p class="admin-muted">No further details were given.</p>
    <?php endif; ?>
  </div>

  <form method="post" action="<?= e(admin_url('lead')) ?>?id=<?= $id ?>" class="admin-card admin-form">
    <?= csrf_field() ?>
    <input type="hidden" name="id" value="<?= $id ?>">
    <header class="admin-card__head"><h2>Follow-up</h2>
      <?= admin_status_badge('lead', $lead['status']) ?>
    </header>
    <div class="admin-field">
      <label for="status">Status</label>
      <select id="status" name="status">
        <?php foreach (admin_lead_statuses() as $value => $label): ?>
        <option value="<?= e($value) ?>"<?= $lead['status'] === $value ? ' selected' : '' ?>><?= e($label) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="admin-field">
      <label for="admin_note">Notes</label>
      <textarea id="admin_note" name="admin_note" rows="6" placeholder="Called on Tuesday, site visit booked for Friday..."><?= e((string) $lead['admin_note']) ?></textarea>
    </div>
    <button class="admin-btn admin-btn--primary" type="submit">Save</button>
  </form>
</section>

<?php admin_page_end(); ?>
