<?php
/** Change the signed-in admin's own password. */

require_once __DIR__ . '/includes/admin.php';

$admin = require_admin();
$errors = [];

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    require_admin_post();

    $current = (string) ($_POST['current'] ?? '');
    $new     = (string) ($_POST['new'] ?? '');
    $confirm = (string) ($_POST['confirm'] ?? '');

    $row = db_row('SELECT password_hash FROM admins WHERE id = ?', [$admin['id']]);
    if ($row === null || !password_verify($current, $row['password_hash'])) {
        $errors['current'] = 'That is not your current password.';
    }
    if (strlen($new) < 10) {
        $errors['new'] = 'Use at least 10 characters.';
    }
    if ($new !== $confirm) {
        $errors['confirm'] = 'The two new passwords do not match.';
    }
    if ($new !== '' && $new === $current) {
        $errors['new'] = 'Choose a password different from the current one.';
    }

    if ($errors === []) {
        db_update('admins', (int) $admin['id'], ['password_hash' => password_hash($new, PASSWORD_DEFAULT)]);
        session_regenerate_id(true);
        flash('success', 'Password changed.');
        admin_redirect('dashboard');
    }
}

admin_page_start('My password', 'password', $admin);
?>

<form method="post" action="<?= e(admin_url('password')) ?>" class="admin-card admin-form" style="max-width:520px">
  <?= csrf_field() ?>
  <?php foreach ([
      'current' => 'Current password',
      'new'     => 'New password',
      'confirm' => 'Repeat new password',
  ] as $name => $label): $error = $errors[$name] ?? null; ?>
  <div class="admin-field<?= $error ? ' has-error' : '' ?>">
    <label for="p-<?= e($name) ?>"><?= e($label) ?></label>
    <input type="password" id="p-<?= e($name) ?>" name="<?= e($name) ?>" required autocomplete="<?= $name === 'current' ? 'current-password' : 'new-password' ?>">
    <?php if ($error): ?><span class="admin-error"><?= e($error) ?></span><?php endif; ?>
  </div>
  <?php endforeach; ?>
  <p class="admin-help">At least 10 characters. A passphrase of four or five unrelated words is a good choice.</p>
  <button class="admin-btn admin-btn--primary" type="submit">Change password</button>
</form>

<?php admin_page_end(); ?>
