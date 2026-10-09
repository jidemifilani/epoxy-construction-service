<?php
/**
 * Company details, prices, delivery and bank account. Saved to the settings
 * table, which overrides the defaults in config/config.php. Clearing a field
 * removes the override, so the site goes back to the default value.
 */

require_once __DIR__ . '/includes/admin.php';

$admin = require_admin();

/** The editable settings, grouped as the form shows them. */
$groups = [
    'Company' => [
        'company_name'  => ['Company name', 'text'],
        'company_short' => ['Short name', 'text'],
        'tagline'       => ['Tagline', 'text'],
        'founder_name'  => ['Founder', 'text'],
        'founding_year' => ['Founded (year)', 'number'],
    ],
    'Numbers shown on the site' => [
        'years'              => ['Years in business', 'number'],
        'projects_completed' => ['Projects completed', 'number'],
        'warranty_years'     => ['Warranty (years)', 'number'],
        'review_count'       => ['Number of reviews', 'number'],
    ],
    'Location' => [
        'city'           => ['City', 'text'],
        'state'          => ['State', 'text'],
        'service_area'   => ['Service area (sentence)', 'text'],
        'office_address' => ['Office address', 'text'],
        'service_radius' => ['Service radius (km)', 'number'],
    ],
    'Contact' => [
        'phone'            => ['Phone', 'text'],
        'whatsapp'         => ['WhatsApp number', 'text'],
        'email'            => ['General email', 'text'],
        'commercial_email' => ['Commercial email', 'text'],
        'trade_email'      => ['Trade email', 'text'],
        'hours_weekday'    => ['Weekday hours', 'text'],
        'hours_saturday'   => ['Saturday hours', 'text'],
        'hours_sunday'     => ['Sunday hours', 'text'],
        'hours_short'      => ['Short hours (header)', 'text'],
    ],
    'Credentials' => [
        'cert_1'          => ['Certification 1', 'text'],
        'cert_2'          => ['Certification 2', 'text'],
        'manufacturer_1'  => ['Manufacturer 1', 'text'],
        'manufacturer_2'  => ['Manufacturer 2', 'text'],
        'insurance_cover' => ['Public liability cover', 'text'],
        'jurisdiction'    => ['Licensed in', 'text'],
    ],
    'Prices shown on the site' => [
        'currency'            => ['Currency symbol', 'text'],
        'price_garage_1_low'  => ['1-car garage, from', 'text'],
        'price_garage_1_high' => ['1-car garage, to', 'text'],
        'price_garage_2_low'  => ['2-car garage, from', 'text'],
        'price_garage_2_high' => ['2-car garage, to', 'text'],
        'rate_basement'       => ['Basement, per m2', 'text'],
        'rate_patio'          => ['Patio / pool deck, per m2', 'text'],
        'rate_metallic'       => ['Metallic decorative, per m2', 'text'],
        'range_residential'   => ['Residential range (sentence)', 'text'],
        'range_commercial'    => ['Commercial range (sentence)', 'text'],
        'range_decorative'    => ['Decorative range (sentence)', 'text'],
        'night_shift_threshold' => ['Night shifts free above (m2)', 'number'],
    ],
    'Shop and payment' => [
        'delivery_fee'        => ['Delivery fee (naira)', 'number'],
        'free_delivery_above' => ['Free delivery from (naira, 0 = never)', 'number'],
        'bank_name'           => ['Bank', 'text'],
        'bank_account_name'   => ['Account name', 'text'],
        'bank_account_number' => ['Account number', 'text'],
        'order_terms'         => ['Order terms (shown at checkout)', 'textarea'],
        'google_review_url'   => ['Google review link (for post-delivery messages)', 'text'],
        'prices_reviewed_on'  => ['Prices last checked (YYYY-MM-DD)', 'text'],
    ],
    'Search and advertising' => [
        'geo_lat'                     => ['Office latitude', 'text'],
        'geo_lng'                     => ['Office longitude', 'text'],
        'ga4_measurement_id'          => ['Google Analytics 4 ID (G-...)', 'text'],
        'google_ads_id'               => ['Google Ads ID (AW-...)', 'text'],
        'google_ads_conversion_label' => ['Google Ads conversion label', 'text'],
        'meta_pixel_id'               => ['Meta Pixel ID', 'text'],
        'google_site_verification'    => ['Google Search Console token', 'text'],
        'bing_site_verification'      => ['Bing Webmaster token', 'text'],
    ],
    'Enquiry alerts' => [
        'whatsapp_alert_number' => ['My WhatsApp number (alerts)', 'text'],
        'notify'    => ['Email me each new enquiry', 'bool'],
        'notify_to' => ['Send alerts to', 'text'],
    ],
];

$allowed = [];
foreach ($groups as $fields) {
    foreach ($fields as $key => $_) {
        $allowed[$key] = true;
    }
}

$errors = [];

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    require_admin_post();

    $pdo = db();
    foreach (array_keys($allowed) as $key) {
        $type = null;
        foreach ($groups as $fields) {
            if (isset($fields[$key])) { $type = $fields[$key][1]; }
        }

        if ($type === 'bool') {
            $value = isset($_POST[$key]) ? '1' : '0';
        } else {
            $value = trim((string) ($_POST[$key] ?? ''));
        }

        if ($value !== '' && $type === 'number' && !preg_match('/^\d{1,9}$/', $value)) {
            $errors[$key] = 'Enter a whole number.';
            continue;
        }
        if (mb_strlen($value) > 2000) {
            $errors[$key] = 'Keep this under 2000 characters.';
            continue;
        }

        if ($value === '' && $type !== 'bool') {
            // Empty means "use the default in config.php": drop the override.
            db_exec('DELETE FROM settings WHERE `key` = ?', [$key]);
        } else {
            $pdo->prepare('INSERT INTO settings (`key`, `value`) VALUES (?, ?)
                           ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)')
                ->execute([$key, $value]);
        }
    }

    if ($errors === []) {
        flash('success', 'Settings saved. The site uses them straight away.');
        admin_redirect('settings');
    }
    flash('error', 'Some settings were not saved. See the messages beside them.');
}

// Current value = the override if one exists, otherwise the config default.
$defaults = config();

admin_page_start('Settings', 'settings', $admin);
?>

<?php if (whatsapp_configured()): ?>
<p class="admin-flash" style="margin-bottom:1rem">WhatsApp alerts are on. New quote requests, enquiries and orders are sent to <?= e(cfg('whatsapp_alert_number')) ?>.</p>
<?php else: ?>
<p class="admin-flash admin-flash--error" style="margin-bottom:1rem">WhatsApp alerts are not set up yet. Add your number above, and the access token and phone number ID in config/config.php.</p>
<?php endif; ?>

<p class="admin-muted" style="margin-bottom:1rem">
  Leave a field empty to go back to the default value. Changes apply to the public site immediately.
</p>

<form method="post" action="<?= e(admin_url('settings')) ?>" class="admin-form">
  <?= csrf_field() ?>

  <?php foreach ($groups as $groupName => $fields): ?>
  <section class="admin-card">
    <header class="admin-card__head"><h2><?= e($groupName) ?></h2></header>
    <div class="admin-grid">
      <?php foreach ($fields as $key => [$label, $type]):
          $current = cfg($key);
          $default = $defaults[$key] ?? '';
          $isBool = $type === 'bool';
          $value = $isBool ? (bool) (int) $current : (string) $current;
          $error = $errors[$key] ?? null;
      ?>
        <?php if ($isBool): ?>
        <div class="admin-field admin-field--check admin-grid__wide">
          <label><input type="checkbox" name="<?= e($key) ?>" value="1"<?= $value ? ' checked' : '' ?>> <?= e($label) ?></label>
        </div>
        <?php elseif ($type === 'textarea'): ?>
        <div class="admin-field admin-grid__wide<?= $error ? ' has-error' : '' ?>">
          <label for="s-<?= e($key) ?>"><?= e($label) ?></label>
          <textarea id="s-<?= e($key) ?>" name="<?= e($key) ?>" rows="3"><?= e($value) ?></textarea>
          <small class="admin-help">Default: <?= e((string) $default) ?></small>
          <?php if ($error): ?><span class="admin-error"><?= e($error) ?></span><?php endif; ?>
        </div>
        <?php else: ?>
        <div class="admin-field<?= $error ? ' has-error' : '' ?>">
          <label for="s-<?= e($key) ?>"><?= e($label) ?></label>
          <input type="<?= $type === 'number' ? 'number' : 'text' ?>" id="s-<?= e($key) ?>" name="<?= e($key) ?>"
                 value="<?= e($value) ?>" placeholder="<?= e((string) $default) ?>"
                 <?= $type === 'number' ? 'inputmode="numeric"' : '' ?>>
          <small class="admin-help">Default: <?= e((string) $default) ?></small>
          <?php if ($error): ?><span class="admin-error"><?= e($error) ?></span><?php endif; ?>
        </div>
        <?php endif; ?>
      <?php endforeach; ?>
    </div>
  </section>
  <?php endforeach; ?>

  <div class="admin-actions-bar">
    <button class="admin-btn admin-btn--primary" type="submit">Save settings</button>
  </div>
</form>

<?php admin_page_end(); ?>
