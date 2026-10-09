<?php
/** Admin sign-in. Standalone page, no public chrome. */

require_once __DIR__ . '/includes/admin.php';

start_session_if_needed();

if (admin_current() !== null) {
    admin_redirect('dashboard');
}

$error = null;
$username = '';

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    if (!csrf_valid($_POST['csrf'] ?? null)) {
        $error = 'Your session expired. Please try again.';
    } else {
        $username = trim((string) ($_POST['username'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');
        if ($username === '' || $password === '') {
            $error = 'Enter your username and password.';
        } else {
            $error = admin_sign_in($username, $password);
            if ($error === null) {
                admin_redirect('dashboard');
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<title>Sign in | Iris Epoxy Admin</title>
<link rel="stylesheet" href="<?= e(asset('css/admin.css')) ?>?v=1">
</head>
<body class="admin admin--login">
<main class="login-card">
  <a class="admin-brand admin-brand--stack" href="<?= e(url()) ?>">
    <?= brand_mark('admin-brand__mark') ?>
    <span><strong>Iris Epoxy Service</strong><small>Admin sign-in</small></span>
  </a>

  <?php if ($error !== null): ?>
  <p class="admin-flash admin-flash--error" role="alert"><?= e($error) ?></p>
  <?php endif; ?>

  <form method="post" action="<?= e(admin_url('login')) ?>" class="admin-form">
    <?= csrf_field() ?>
    <div class="admin-field">
      <label for="username">Username</label>
      <input type="text" id="username" name="username" value="<?= e($username) ?>" required autocomplete="username" autofocus>
    </div>
    <div class="admin-field">
      <label for="password">Password</label>
      <input type="password" id="password" name="password" required autocomplete="current-password">
    </div>
    <button class="admin-btn admin-btn--primary admin-btn--block" type="submit">Sign in</button>
  </form>
  <p class="admin-help" style="text-align:center;margin-top:1.2rem"><a href="<?= e(url()) ?>">Back to the website</a></p>
</main>
</body>
</html>
