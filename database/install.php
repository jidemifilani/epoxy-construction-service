<?php
/**
 * Installer. Run from the command line, once, on a new server:
 *
 *   php database/install.php --user=admin --password="choose-a-strong-one" --name="Site Admin"
 *
 * It creates the database, applies schema.sql, copies the starter content
 * into any table that is still empty, and creates the first admin account.
 * Re-running it is safe: existing content is never overwritten, and an
 * existing admin's password is only changed when you pass --password again.
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require_once __DIR__ . '/../includes/functions.php';

$options = getopt('', ['user:', 'password:', 'name::']);

function say(string $line): void
{
    fwrite(STDOUT, $line . PHP_EOL);
}

function fail(string $message): void
{
    fwrite(STDERR, 'Error: ' . $message . PHP_EOL);
    exit(1);
}

$cfg = config()['db'];

// --- 1. Create the database ------------------------------------------------
try {
    $server = new PDO(
        sprintf('mysql:host=%s;port=%d;charset=%s', $cfg['host'], (int) $cfg['port'], $cfg['charset']),
        $cfg['user'], $cfg['password'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    fail('cannot reach MySQL at ' . $cfg['host'] . ':' . $cfg['port'] . ' - is it running? (' . $e->getMessage() . ')');
}

$server->exec(sprintf(
    'CREATE DATABASE IF NOT EXISTS `%s` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci',
    $cfg['name']
));
say('Database ready: ' . $cfg['name']);

// --- 2. Apply the schema ----------------------------------------------------
$pdo = db_connect();
$sql = file_get_contents(__DIR__ . '/schema.sql');
// Drop whole-line comments, then run each statement on its own.
$sql = preg_replace('/^\s*--.*$/m', '', $sql) ?? '';
foreach (array_filter(array_map('trim', explode(';', $sql))) as $statement) {
    $pdo->exec($statement);
}
say('Schema applied.');

// Migrations bring in the later tables (media, SEO), article text and new products.
require_once __DIR__ . '/migrate.php';
$migrated = run_migrations();
say($migrated === [] ? 'Migrations: already up to date.' : 'Migrations applied: ' . implode(', ', $migrated));

// --- 3. Seed starter content into empty tables -----------------------------
/** The seed text was written with HTML entities; store it as plain text. */
function plain(string $text): string
{
    return html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

function table_is_empty(string $table): bool
{
    return (int) db_value("SELECT COUNT(*) FROM `$table`") === 0;
}

if (table_is_empty('projects')) {
    foreach (default_projects() as $index => $project) {
        $project = array_map('plain', $project);
        db_insert('projects', $project + ['sort' => ($index + 1) * 10, 'published' => 1]);
    }
    say('Seeded projects.');
}

if (table_is_empty('testimonials')) {
    foreach (default_testimonials() as $index => $review) {
        db_insert('testimonials', [
            'quote'     => plain($review['quote']),
            'name'      => plain($review['name']),
            'role'      => plain($review['role']),
            'group_key' => $review['group'],
            'project'   => plain($review['project']),
            'sort'      => ($index + 1) * 10,
            'published' => 1,
        ]);
    }
    say('Seeded testimonials.');
}

if (table_is_empty('faqs')) {
    $order = 0;
    foreach (default_faq_groups() as $groupName => $pairs) {
        $groupSort = ($order += 10);
        foreach ($pairs as $i => [$question, $answer]) {
            db_insert('faqs', [
                'group_name'   => plain($groupName),
                'group_sort'   => $groupSort,
                'question'     => plain($question),
                'answer'       => plain($answer),
                'show_on_home' => 0,
                'sort'         => ($i + 1) * 10,
                'published'    => 1,
            ]);
        }
    }
    // The home page shows four of these as a short list. They sit in their own
    // group so they can be edited like everything else.
    foreach (default_faq_snippet() as $i => [$question, $answer]) {
        db_insert('faqs', [
            'group_name'   => 'Common questions',
            'group_sort'   => 999,
            'question'     => plain($question),
            'answer'       => plain($answer),
            'show_on_home' => 1,
            'sort'         => ($i + 1) * 10,
            'published'    => 1,
        ]);
    }
    say('Seeded FAQs.');
}

if (table_is_empty('articles')) {
    foreach (default_articles() as $index => [$slug, $title, $category, $purpose, $outline]) {
        db_insert('articles', [
            'slug'      => $slug,
            'title'     => plain($title),
            'category'  => $category,
            'purpose'   => plain($purpose),
            'outline'   => implode("\n", array_map('plain', $outline)),
            'sort'      => ($index + 1) * 10,
            'published' => 1,
        ]);
    }
    say('Seeded articles.');
}

if (table_is_empty('products')) {
    // Retail packs start at 1 kg. Wholesale is sold by the drum.
    // Every price here is a placeholder - set the real ones in the admin.
    $products = [
        ['Solvent-Free Epoxy Floor Coat', 'retail',    '1 kg tin',       9500,    1, 'Two-part epoxy for garages, workshops and light commercial floors.'],
        ['Solvent-Free Epoxy Floor Coat', 'retail',    '5 kg tin',       44000,   1, 'Two-part epoxy for garages, workshops and light commercial floors.'],
        ['Solvent-Free Epoxy Floor Coat', 'retail',    '20 kg pail',     165000,  1, 'Two-part epoxy for garages, workshops and light commercial floors.'],
        ['Penetrating Epoxy Primer',      'retail',    '1 kg tin',       8000,    1, 'Low-viscosity primer that locks into the concrete profile before the coat.'],
        ['Penetrating Epoxy Primer',      'retail',    '5 kg tin',       37000,   1, 'Low-viscosity primer that locks into the concrete profile before the coat.'],
        ['Anti-Slip Quartz Aggregate',    'retail',    '1 kg bag',       2500,    1, 'Broadcast into the wet coat for grip on wet and outdoor areas.'],
        ['Anti-Slip Quartz Aggregate',    'retail',    '5 kg bag',       11000,   1, 'Broadcast into the wet coat for grip on wet and outdoor areas.'],
        ['Solvent-Free Epoxy Floor Coat', 'wholesale', 'Drum (25 kg)',   240000,  1, 'Bulk supply for contractors and facilities. Priced per drum.'],
        ['Solvent-Free Epoxy Floor Coat', 'wholesale', 'Drum (200 kg)',  1850000, 1, 'Bulk supply for contractors and facilities. Priced per drum.'],
        ['Penetrating Epoxy Primer',      'wholesale', 'Drum (25 kg)',   205000,  1, 'Bulk primer for contractors. Priced per drum.'],
        ['Anti-Slip Quartz Aggregate',    'wholesale', 'Drum (25 kg)',   85000,   1, 'Bulk aggregate for contractors. Priced per drum.'],
    ];
    foreach ($products as $index => [$name, $channel, $pack, $price, $min, $description]) {
        db_insert('products', [
            'name'        => $name,
            'channel'     => $channel,
            'pack_label'  => $pack,
            'unit_price'  => $price,
            'min_qty'     => $min,
            'description' => $description,
            'in_stock'    => 1,
            'sort'        => ($index + 1) * 10,
            'published'   => 1,
        ]);
    }
    say('Seeded shop products (placeholder prices - set real ones in the admin).');
}

// --- 4. The first admin account -------------------------------------------
if (isset($options['user']) || isset($options['password'])) {
    $username = trim((string) ($options['user'] ?? ''));
    $password = (string) ($options['password'] ?? '');
    if ($username === '' || !preg_match('/^[A-Za-z0-9_.-]{3,64}$/', $username)) {
        fail('--user must be 3-64 letters, digits, dots, dashes or underscores.');
    }
    if (strlen($password) < 10) {
        fail('--password must be at least 10 characters.');
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);
    $existing = db_row('SELECT id FROM admins WHERE username = ?', [$username]);
    if ($existing) {
        db_exec('UPDATE admins SET password_hash = ?, display_name = ? WHERE id = ?',
            [$hash, (string) ($options['name'] ?? ''), $existing['id']]);
        say('Admin "' . $username . '" updated.');
    } else {
        db_insert('admins', [
            'username'      => $username,
            'display_name'  => (string) ($options['name'] ?? ''),
            'password_hash' => $hash,
        ]);
        say('Admin "' . $username . '" created.');
    }
} elseif ((int) db_value('SELECT COUNT(*) FROM admins') === 0) {
    say('No admin account yet. Re-run with --user=... --password=... to create one.');
}

say('');
say('Done. Public site: ' . SITE_ORIGIN . BASE_URL . '/');
say('Admin dashboard:   ' . SITE_ORIGIN . BASE_URL . '/admin/');
