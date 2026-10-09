<?php
/** Sign out. POST only, so a link in an email or a cached page cannot sign someone out. */

require_once __DIR__ . '/includes/admin.php';

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST' && csrf_valid($_POST['csrf'] ?? null)) {
    admin_sign_out();
    flash('success', 'You have signed out.');
    admin_redirect('login');
}

// A plain GET shows a confirmation with a button, not an immediate sign-out.
$admin = require_admin();
admin_page_start('Sign out', 'logout', $admin);
?>
<section class="admin-card">
  <p>Sign out of the admin dashboard?</p>
  <?= admin_post_form('logout', [], 'Sign out', 'admin-btn--primary') ?>
</section>
<?php admin_page_end();
