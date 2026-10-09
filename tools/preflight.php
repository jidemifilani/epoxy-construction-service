<?php
/**
 * Pre-launch check. Reads the configuration, the database and the server, and reports
 * what must be fixed before the site takes real customers.
 *
 *   php tools/preflight.php
 *
 * Each line is PASS, INFO, WARN or FAIL. The command exits with 1 if anything is FAIL,
 * so it can also be used in a deployment script.
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/notify.php';

$results = [];
function report(string $level, string $message, string $fix = ''): void
{
    global $results;
    $results[] = [$level, $message, $fix];
}

$origin = site_origin();
$base   = base_path();
$live   = !str_contains($origin, 'localhost') && !str_contains($origin, '127.0.0.1');

// --- Addresses ---------------------------------------------------------------
if (str_starts_with($origin, 'https://')) {
    report('PASS', 'SITE_ORIGIN uses https: ' . $origin);
} elseif ($live) {
    report('FAIL', 'SITE_ORIGIN is not https: ' . $origin, 'Set SITE_ORIGIN in config/config.php to the https:// address.');
} else {
    report('INFO', 'SITE_ORIGIN is ' . $origin . ' (local). Set the live https:// address before launch.');
}
if ($live && $base !== '') {
    report('INFO', 'BASE_URL is "' . $base . '". If the site lives at the domain root, set it to an empty string and update .htaccess (see GO-LIVE.md).');
}

// --- Database ------------------------------------------------------------------
try {
    $pdo = db_connect();
    report('PASS', 'Database connection works.');
} catch (Throwable $e) {
    report('FAIL', 'Cannot connect to the database: ' . $e->getMessage(), 'Check the db block in config/config.php.');
    $pdo = null;
}

$dbCfg = config()['db'];
if ($dbCfg['user'] === 'root' && (string) $dbCfg['password'] === '') {
    report('WARN', 'The database user is root with no password.', 'Create a dedicated database user with a strong password, and grant it access to this database only.');
}

if ($pdo !== null) {
    $pending = [];
    $applied = [];
    try {
        $applied = $pdo->query('SELECT name FROM schema_migrations')->fetchAll(PDO::FETCH_COLUMN);
    } catch (Throwable $e) {
        // No migrations table: the database was never installed.
    }
    foreach (glob(__DIR__ . '/../database/migrations/*.sql') ?: [] as $file) {
        if (!in_array(basename($file), $applied, true)) {
            $pending[] = basename($file);
        }
    }
    if ($pending === []) {
        report('PASS', 'All database migrations are applied.');
    } else {
        report('FAIL', 'Migrations not applied: ' . implode(', ', $pending), 'Run: php database/migrate.php');
    }

    $admins = (int) db_value('SELECT COUNT(*) FROM admins');
    if ($admins > 0) {
        report('PASS', $admins . ' admin account(s) exist.');
    } else {
        report('FAIL', 'No admin account exists.', 'Run database/install.php with --user and --password.');
    }

    $products = (int) db_value('SELECT COUNT(*) FROM products WHERE published = 1');
    $free = (int) db_value('SELECT COUNT(*) FROM products WHERE published = 1 AND unit_price <= 0');
    if ($products === 0) {
        report('WARN', 'The shop has no published products.', 'Add products in Products, or switch the shop off.');
    } elseif ($free > 0) {
        report('FAIL', $free . ' published product(s) have a price of zero.', 'Set a real price for each product in Products.');
    } else {
        report('WARN', $products . ' published products. Confirm every price and stock level is current.', 'Review Products in the admin.');
    }

    $samples = (int) db_value("SELECT COUNT(*) FROM testimonials WHERE published = 1 AND name IN ('Adaeze N.','Tunde O.','Ifeoma E.','Samuel A.','Grace M.','Chidi U.')");
    if ($samples > 0) {
        report('WARN', $samples . ' sample review(s) are still published.', 'Replace them with real reviews, or unpublish them. Do not publish made-up reviews.');
    } else {
        report('PASS', 'No sample reviews are published.');
    }
}

// --- Business details still at their placeholder values ---------------------
$phone = (string) cfg('phone');
if (str_contains($phone, '801 234 5678')) {
    report('WARN', 'The phone number is still the placeholder ' . $phone . '.', 'Set the real number in Settings > Contact.');
}
if (str_contains((string) cfg('email'), 'irisepoxyservice.com')) {
    report('WARN', 'Email addresses use irisepoxyservice.com. Check that you own this domain.', 'Set the real addresses in Settings > Contact.');
}
if ((string) cfg('bank_account_number') === '0000000000' || (string) cfg('bank_account_number') === '') {
    report('FAIL', 'The bank account number is still a placeholder. Customers would be sent to the wrong account.', 'Set the real account in Settings > Shop and payment.');
} else {
    report('PASS', 'Bank account number is set.');
}
if ((string) cfg('geo_lat') === '6.4474' && (string) cfg('geo_lng') === '3.4726') {
    report('WARN', 'The map coordinates are the approximate default for Lekki.', 'Set the exact office point from the Google Business Profile pin in Settings > Search and advertising.');
}
if ((string) cfg('google_review_url') === '') {
    report('INFO', 'No Google review link is set, so the review-request button will not appear on orders.', 'Set it in Settings > Shop and payment when the profile exists.');
}
$analytics = trim((string) cfg('ga4_measurement_id')) . trim((string) cfg('google_ads_id')) . trim((string) cfg('meta_pixel_id'));
report($analytics === '' ? 'INFO' : 'PASS', $analytics === '' ? 'No analytics or ad tags are set. Enter the IDs in Settings once the accounts exist.' : 'Analytics or ad tags are configured.');
report(whatsapp_configured() ? 'PASS' : 'INFO', whatsapp_configured() ? 'WhatsApp alerts are configured.' : 'WhatsApp alerts are not set up (see SEO-IMPLEMENTATION-STATUS.md).');

// --- Security -----------------------------------------------------------------
$firstPassword = __DIR__ . '/../storage/.first-admin-password.txt';
if (is_file($firstPassword)) {
    report('FAIL', 'storage/.first-admin-password.txt still exists. It holds the first admin password in plain text.', 'Sign in, change the password under My password, then delete that file.');
} else {
    report('PASS', 'No plain-text admin password file is left in storage.');
}

if (ini_get('display_errors')) {
    report($live ? 'FAIL' : 'WARN', 'PHP display_errors is on. Visitors would see PHP warnings and file paths.', 'Set display_errors = Off in php.ini (or the host\'s PHP settings).');
} else {
    report('PASS', 'PHP display_errors is off.');
}

// Internal folders must not be reachable by URL.
foreach (['config/config.php', 'includes/db.php', 'storage/.first-admin-password.txt', 'database/schema.sql'] as $internal) {
    $url = $origin . $base . '/' . $internal;
    $ch = curl_init($url);
    curl_setopt_array($ch, [CURLOPT_NOBODY => true, CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 6, CURLOPT_FOLLOWLOCATION => false]);
    curl_exec($ch);
    $status = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
    if ($status === 0) {
        report('INFO', 'Could not reach ' . $url . ' to test it.');
    } elseif ($status === 200) {
        report('FAIL', $internal . ' can be opened in a browser.', 'Check the RedirectMatch line in .htaccess.');
    } else {
        report('PASS', $internal . ' is not reachable by URL (HTTP ' . $status . ').');
    }
}

// --- Environment ----------------------------------------------------------------
foreach (['pdo_mysql', 'gd', 'fileinfo', 'mbstring', 'curl', 'openssl'] as $ext) {
    extension_loaded($ext)
        ? report('PASS', 'PHP extension ' . $ext . ' is enabled.')
        : report('FAIL', 'PHP extension ' . $ext . ' is not enabled.', 'Enable ' . $ext . ' in php.ini.');
}
$upload = ini_get('upload_max_filesize');
$uploadBytes = (int) $upload * (str_ends_with(strtoupper($upload), 'G') ? 1073741824 : (str_ends_with(strtoupper($upload), 'M') ? 1048576 : 1));
$uploadBytes >= 40 * 1048576
    ? report('PASS', 'upload_max_filesize is ' . $upload . ' (videos up to 40 MB).')
    : report('WARN', 'upload_max_filesize is ' . $upload . '. Videos up to 40 MB will fail.', 'Raise upload_max_filesize and post_max_size to 40M or more.');

foreach (['assets/uploads/media', 'storage', 'storage/backups'] as $dir) {
    $full = __DIR__ . '/../' . $dir;
    if (!is_dir($full)) {
        @mkdir($full, 0775, true);
    }
    is_writable($full)
        ? report('PASS', $dir . ' is writable.')
        : report('FAIL', $dir . ' is not writable by the web server.', 'Give the web server write access to ' . $dir . '.');
}

// --- Backups ------------------------------------------------------------------------
$backups = glob(__DIR__ . '/../storage/backups/*.sql') ?: [];
if ($backups === []) {
    report('WARN', 'No database backup exists yet.', 'Run php tools/backup-db.php, then schedule it weekly.');
} else {
    rsort($backups);
    $ageDays = (time() - filemtime($backups[0])) / 86400;
    $ageDays <= 7
        ? report('PASS', 'Latest backup is ' . (int) floor($ageDays) . ' day(s) old.')
        : report('WARN', 'Latest backup is ' . (int) floor($ageDays) . ' days old.', 'Schedule tools/backup-db.php weekly.');
}

// --- Report ------------------------------------------------------------------------------
$counts = ['PASS' => 0, 'INFO' => 0, 'WARN' => 0, 'FAIL' => 0];
foreach ($results as [$level, $message, $fix]) {
    $counts[$level]++;
    fwrite(STDOUT, '[' . str_pad($level, 4) . '] ' . $message . PHP_EOL);
    if ($fix !== '' && $level !== 'PASS') {
        fwrite(STDOUT, '       -> ' . $fix . PHP_EOL);
    }
}
fwrite(STDOUT, PHP_EOL . sprintf('%d passed, %d to note, %d to review, %d to fix.', $counts['PASS'], $counts['INFO'], $counts['WARN'], $counts['FAIL']) . PHP_EOL);
exit($counts['FAIL'] > 0 ? 1 : 0);
