<?php
/** One job application: the applicant, their CV, and the status the hiring team moves it through. */

require_once __DIR__ . '/includes/admin.php';

$admin = require_admin();
$id    = (int) ($_GET['id'] ?? ($_POST['id'] ?? 0));

$application = $id > 0 ? db_row('SELECT * FROM job_applications WHERE id = ?', [$id]) : null;
if ($application === null) {
    flash('error', 'That application could not be found.');
    admin_redirect('applications');
}

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    require_admin_post();

    $status = (string) ($_POST['status'] ?? '');
    if (!array_key_exists($status, application_statuses())) {
        flash('error', 'Choose a valid status.');
    } else {
        db_update('job_applications', $id, [
            'status'     => $status,
            'admin_note' => mb_substr(trim((string) ($_POST['admin_note'] ?? '')), 0, 2000),
        ]);
        flash('success', 'Application updated.');
    }
    admin_redirect('application?id=' . $id);
}

$phoneDigits = preg_replace('/[^0-9]/', '', (string) $application['phone']);

admin_page_start('Application: ' . $application['applicant_name'], 'applications', $admin);
?>

<p class="admin-crumbs"><a href="<?= e(admin_url('applications')) ?>">&larr; All applications</a></p>

<section class="admin-two">
  <div class="admin-card">
    <header class="admin-card__head">
      <h2>Applying for: <?= e($application['job_title']) ?></h2>
      <span class="admin-muted"><?= e(admin_date($application['created_at'])) ?></span>
    </header>
    <dl class="admin-dl">
      <dt>Name</dt><dd><?= e($application['applicant_name']) ?></dd>
      <dt>Phone</dt><dd><a href="tel:<?= e($phoneDigits) ?>"><?= e($application['phone']) ?></a>
        &middot; <a href="https://wa.me/<?= e($phoneDigits) ?>" target="_blank" rel="noopener">WhatsApp</a></dd>
      <dt>Email</dt><dd><a href="mailto:<?= e($application['email']) ?>"><?= e($application['email']) ?></a></dd>
      <dt>CV</dt><dd>
        <a class="admin-btn admin-btn--primary admin-btn--sm" href="<?= e(admin_url('resume')) ?>?id=<?= (int) $application['id'] ?>">
          Download CV<?= $application['resume_original_name'] !== '' ? ' (' . e($application['resume_original_name']) . ')' : '' ?>
        </a>
      </dd>
    </dl>

    <?php if ((string) $application['cover_note'] !== ''): ?>
    <h3 class="admin-h3">Note from the applicant</h3>
    <p class="admin-quote"><?= nl2br(e($application['cover_note'])) ?></p>
    <?php else: ?>
    <p class="admin-muted">No additional note was left.</p>
    <?php endif; ?>

    <p class="admin-muted">Applied from <?= e($application['ip_address']) ?></p>
  </div>

  <form method="post" action="<?= e(admin_url('application')) ?>?id=<?= $id ?>" class="admin-card admin-form">
    <?= csrf_field() ?>
    <input type="hidden" name="id" value="<?= $id ?>">
    <header class="admin-card__head"><h2>Status</h2>
      <?= admin_status_badge('application', $application['status']) ?>
    </header>
    <div class="admin-field">
      <label for="status">Application status</label>
      <select id="status" name="status">
        <?php foreach (application_statuses() as $value => $label): ?>
        <option value="<?= e($value) ?>"<?= $application['status'] === $value ? ' selected' : '' ?>><?= e($label) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="admin-field">
      <label for="admin_note">Internal notes</label>
      <textarea id="admin_note" name="admin_note" rows="6" placeholder="Interview notes, references, next steps..."><?= e((string) $application['admin_note']) ?></textarea>
    </div>
    <button class="admin-btn admin-btn--primary" type="submit">Save</button>
  </form>
</section>

<?php admin_page_end(); ?>
