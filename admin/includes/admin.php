<?php
/**
 * Admin dashboard core: sign-in, the page layout, and a generic
 * create / edit / delete engine driven by admin/includes/entities.php.
 *
 * Nothing here trusts the request for table or column names. Those always come
 * from the entity definitions, and values always go through prepared statements.
 */

declare(strict_types=1);

require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/shop.php';   // naira() for money display

const ADMIN_IDLE_SECONDS = 8 * 3600;   // sign out after eight idle hours
const ADMIN_MAX_FAILURES = 5;          // wrong passwords before a pause
const ADMIN_LOCK_SECONDS = 900;        // the pause: fifteen minutes

// --- URLs ------------------------------------------------------------------

function admin_url(string $path = ''): string
{
    $path = ltrim($path, '/');
    // The dashboard is the admin root itself, not a page of its own.
    if ($path === '' || $path === 'dashboard') {
        return url('admin/');
    }
    return url('admin/' . $path);
}

function admin_redirect(string $path): void
{
    header('Location: ' . admin_url($path), true, 303);
    exit;
}

// --- Sign-in ---------------------------------------------------------------

/** The signed-in admin, or null. Signs out after the idle limit. */
function admin_current(): ?array
{
    start_session_if_needed();
    $id = (int) ($_SESSION['admin_id'] ?? 0);
    if ($id === 0) {
        return null;
    }
    if (time() - (int) ($_SESSION['admin_seen'] ?? 0) > ADMIN_IDLE_SECONDS) {
        admin_sign_out();
        return null;
    }
    $_SESSION['admin_seen'] = time();
    return db_row('SELECT id, username, display_name FROM admins WHERE id = ?', [$id]);
}

/** Page guard: sends anyone not signed in to the login page. */
function require_admin(): array
{
    $admin = admin_current();
    if ($admin === null) {
        flash('error', 'Please sign in to continue.');
        admin_redirect('login');
    }
    return $admin;
}

/** Returns an error message, or null when the sign-in succeeds. */
function admin_sign_in(string $username, string $password): ?string
{
    start_session_if_needed();
    if (time() < (int) ($_SESSION['login_locked_until'] ?? 0)) {
        return 'Too many wrong attempts. Wait a few minutes and try again.';
    }

    $row = db_row('SELECT id, password_hash FROM admins WHERE username = ?', [$username]);
    if ($row === null || !password_verify($password, $row['password_hash'])) {
        $_SESSION['login_failures'] = (int) ($_SESSION['login_failures'] ?? 0) + 1;
        if ($_SESSION['login_failures'] >= ADMIN_MAX_FAILURES) {
            $_SESSION['login_locked_until'] = time() + ADMIN_LOCK_SECONDS;
            $_SESSION['login_failures'] = 0;
        }
        return 'That username and password do not match.';
    }

    // A new session id on sign-in prevents session fixation.
    session_regenerate_id(true);
    unset($_SESSION['login_failures'], $_SESSION['login_locked_until']);
    $_SESSION['admin_id'] = (int) $row['id'];
    $_SESSION['admin_seen'] = time();
    db_exec('UPDATE admins SET last_login_at = NOW() WHERE id = ?', [$row['id']]);
    return null;
}

function admin_sign_out(): void
{
    start_session_if_needed();
    unset($_SESSION['admin_id'], $_SESSION['admin_seen']);
    session_regenerate_id(true);
}

// --- Layout ----------------------------------------------------------------

/** The sidebar sections. 'key' is what each page marks as active. */
function admin_nav(): array
{
    $newOrders = (int) db_value("SELECT COUNT(*) FROM orders WHERE status = 'new'");
    $newLeads  = (int) db_value("SELECT COUNT(*) FROM leads WHERE status = 'new'");
    $newApplications = (int) db_value("SELECT COUNT(*) FROM job_applications WHERE status = 'new'");

    return [
        'Overview' => [
            ['key' => 'dashboard', 'label' => 'Dashboard', 'href' => 'index'],
        ],
        'Shop' => [
            ['key' => 'orders',   'label' => 'Orders',   'href' => 'orders',   'badge' => $newOrders],
            ['key' => 'products', 'label' => 'Products', 'href' => 'list?e=products'],
        ],
        'Careers' => [
            ['key' => 'applications', 'label' => 'Applications', 'href' => 'applications', 'badge' => $newApplications],
            ['key' => 'jobs',         'label' => 'Job postings', 'href' => 'list?e=jobs'],
        ],
        'Enquiries' => [
            ['key' => 'leads', 'label' => 'Leads', 'href' => 'leads', 'badge' => $newLeads],
        ],
        'Site content' => [
            ['key' => 'projects',     'label' => 'Projects',     'href' => 'list?e=projects'],
            ['key' => 'testimonials', 'label' => 'Testimonials', 'href' => 'list?e=testimonials'],
            ['key' => 'faqs',         'label' => 'FAQs',         'href' => 'list?e=faqs'],
            ['key' => 'articles',     'label' => 'Blog articles', 'href' => 'list?e=articles'],
        ],
        'Marketing' => [
            ['key' => 'media', 'label' => 'Photos & videos', 'href' => 'media'],
            ['key' => 'seo_pages', 'label' => 'SEO by page', 'href' => 'list?e=seo_pages'],
        ],
        'Business' => [
            ['key' => 'settings', 'label' => 'Settings', 'href' => 'settings'],
            ['key' => 'password', 'label' => 'My password', 'href' => 'password'],
        ],
    ];
}

/** Opens the admin page: head, sidebar and flash messages. */
function admin_page_start(string $title, string $active, array $admin): void
{
    $flashes = take_flashes();
    $nav = admin_nav();
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<title><?= e($title) ?> | Iris Epoxy Admin</title>
<link rel="stylesheet" href="<?= e(asset('css/admin.css')) ?>?v=1">
</head>
<body class="admin">
<a class="skip" href="#main">Skip to content</a>

<div class="admin-shell">
  <aside class="admin-side" aria-label="Admin navigation">
    <a class="admin-brand" href="<?= e(admin_url()) ?>">
      <?= brand_mark('admin-brand__mark') ?>
      <span><strong>Iris Epoxy</strong><small>Admin</small></span>
    </a>

    <nav>
      <?php foreach ($nav as $group => $items): ?>
      <p class="admin-side__group"><?= e($group) ?></p>
      <ul>
        <?php foreach ($items as $item): ?>
        <li>
          <a href="<?= e(admin_url($item['href'])) ?>" class="<?= $item['key'] === $active ? 'is-active' : '' ?>"
             <?= $item['key'] === $active ? 'aria-current="page"' : '' ?>>
            <span><?= e($item['label']) ?></span>
            <?php if (!empty($item['badge'])): ?><b class="admin-badge"><?= (int) $item['badge'] ?></b><?php endif; ?>
          </a>
        </li>
        <?php endforeach; ?>
      </ul>
      <?php endforeach; ?>
    </nav>

    <div class="admin-side__foot">
      <a href="<?= e(url()) ?>" target="_blank" rel="noopener">View the site &rarr;</a>
      <a href="<?= e(admin_url('logout')) ?>">Sign out</a>
      <span>Signed in as <?= e($admin['display_name'] !== '' ? $admin['display_name'] : $admin['username']) ?></span>
    </div>
  </aside>

  <main class="admin-main" id="main">
    <header class="admin-top">
      <h1><?= e($title) ?></h1>
    </header>

    <?php foreach ($flashes as $flash): ?>
    <p class="admin-flash admin-flash--<?= e($flash['type']) ?>" role="status"><?= e($flash['message']) ?></p>
    <?php endforeach; ?>
    <?php
}

function admin_page_end(): void
{
    ?>
  </main>
</div>
<script src="<?= e(asset('js/admin.js')) ?>?v=1" defer></script>
</body>
</html>
    <?php
}

/** The little form that posts an action (delete, toggle) without a page of its own. */
function admin_post_form(string $action, array $fields, string $label, string $class = 'admin-btn--ghost', ?string $confirm = null): string
{
    $html = '<form method="post" action="' . e(admin_url($action)) . '" class="admin-inline"'
          . ($confirm !== null ? ' data-confirm="' . e($confirm) . '"' : '') . '>';
    $html .= csrf_field();
    foreach ($fields as $name => $value) {
        $html .= '<input type="hidden" name="' . e($name) . '" value="' . e((string) $value) . '">';
    }
    $html .= '<button class="admin-btn ' . e($class) . '" type="submit">' . e($label) . '</button></form>';
    return $html;
}

/** Only accepts a POST whose CSRF token is valid. Stops here otherwise. */
function require_admin_post(): void
{
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST' || !csrf_valid($_POST['csrf'] ?? null)) {
        flash('error', 'That request could not be verified. Please try again.');
        admin_redirect('dashboard');
    }
}

// --- Generic entity engine ---------------------------------------------------

/** The section definitions, loaded once. */
function admin_entities(): array
{
    static $entities = null;
    $entities ??= require __DIR__ . '/entities.php';
    return $entities;
}

/** One section's definition. Unknown keys end the request with a 404. */
function admin_entity(string $key): array
{
    $all = admin_entities();
    if (!isset($all[$key])) {
        http_response_code(404);
        exit('Unknown section.');
    }
    return $all[$key];
}

function entity_rows(array $entity, string $search = ''): array
{
    $columns = ['id'];
    foreach ($entity['fields'] as $name => $_) {
        $columns[] = $name;
    }
    $select = implode(', ', array_map(static fn (string $c): string => '`' . $c . '`', $columns));

    $sql = 'SELECT ' . $select . ' FROM `' . $entity['table'] . '`';
    $params = [];
    if ($search !== '' && !empty($entity['search'])) {
        $parts = [];
        foreach ($entity['search'] as $column) {
            $parts[] = '`' . $column . '` LIKE ?';
            $params[] = '%' . $search . '%';
        }
        $sql .= ' WHERE ' . implode(' OR ', $parts);
    }
    $sql .= ' ORDER BY ' . $entity['order'];
    return db_rows($sql, $params);
}

function entity_find(array $entity, int $id): ?array
{
    return db_row('SELECT * FROM `' . $entity['table'] . '` WHERE id = ?', [$id]);
}

/**
 * Reads and checks the posted form for one entity. Returns [values, errors],
 * with values already converted to the column's type.
 */
function entity_read_post(array $entity, int $id): array
{
    $values = [];
    $errors = [];

    foreach ($entity['fields'] as $name => $field) {
        $type = $field['type'];
        $label = $field['label'];

        if ($type === 'bool') {
            $values[$name] = isset($_POST[$name]) ? 1 : 0;
            continue;
        }

        $raw = $_POST[$name] ?? '';
        $raw = is_string($raw) ? trim($raw) : '';
        if ($type !== 'textarea') {
            $raw = trim((string) preg_replace('/\s+/u', ' ', $raw));
        }

        if ($raw === '') {
            if (!empty($field['required'])) {
                $errors[$name] = $label . ' is required.';
            }
            $values[$name] = in_array($type, ['number', 'decimal'], true) ? 0 : '';
            continue;
        }

        switch ($type) {
            case 'number':
                if (!preg_match('/^-?\d{1,9}$/', $raw)) {
                    $errors[$name] = $label . ' must be a whole number.';
                }
                $values[$name] = (int) $raw;
                break;

            case 'decimal':
                if (!is_numeric($raw) || (float) $raw < 0) {
                    $errors[$name] = $label . ' must be a price, e.g. 12500.';
                }
                $values[$name] = round((float) $raw, 2);
                break;

            case 'select':
                if (!array_key_exists($raw, $field['options'])) {
                    $errors[$name] = 'Choose one of the options for ' . strtolower($label) . '.';
                }
                $values[$name] = $raw;
                break;

            case 'path':
                // A site path such as services/residential. Empty means the home page.
                $raw = strtolower(trim($raw, '/ '));
                if ($raw !== '' && !preg_match('#^[a-z0-9]+(-[a-z0-9]+)*(/[a-z0-9]+(-[a-z0-9]+)*)*$#', $raw)) {
                    $errors[$name] = $label . ' can only use lowercase letters, numbers, dashes and single slashes.';
                } else {
                    $clash = db_value('SELECT id FROM `' . $entity['table'] . '` WHERE `' . $name . '` = ? AND id <> ?', [$raw, $id]);
                    if ($clash !== null) {
                        $errors[$name] = 'Another entry already covers that page.';
                    }
                }
                $values[$name] = $raw;
                break;

            case 'slug':
                $raw = strtolower($raw);
                if (!preg_match('/^[a-z0-9]+(-[a-z0-9]+)*$/', $raw)) {
                    $errors[$name] = $label . ' can only use lowercase letters, numbers and single dashes.';
                } else {
                    $clash = db_value('SELECT id FROM `' . $entity['table'] . '` WHERE `' . $name . '` = ? AND id <> ?', [$raw, $id]);
                    if ($clash !== null) {
                        $errors[$name] = 'Another entry already uses that ' . strtolower($label) . '.';
                    }
                }
                $values[$name] = $raw;
                break;

            default: // text, textarea
                $max = $field['max'] ?? ($type === 'textarea' ? 5000 : 255);
                if (mb_strlen($raw) > $max) {
                    $errors[$name] = $label . ' must be ' . $max . ' characters or fewer.';
                }
                $values[$name] = $raw;
        }
    }

    return [$values, $errors];
}

/** Insert (id 0) or update. Returns the row id. */
function entity_save(array $entity, int $id, array $values): int
{
    if ($id === 0) {
        return db_insert($entity['table'], $values);
    }
    db_update($entity['table'], $id, $values);
    return $id;
}

function entity_delete(array $entity, int $id): void
{
    db_exec('DELETE FROM `' . $entity['table'] . '` WHERE id = ?', [$id]);
}

/**
 * Renders one form control. $value is the current value, already escaped when
 * it is written into the attribute.
 */
function admin_field(string $name, array $field, $value, ?string $error): string
{
    $id = 'f-' . $name;
    $required = !empty($field['required']);
    $invalid = $error !== null ? ' aria-invalid="true" aria-describedby="' . $id . '-err"' : '';
    $help = !empty($field['help']) ? '<small class="admin-help">' . e($field['help']) . '</small>' : '';
    $errorHtml = $error !== null ? '<span class="admin-error" id="' . $id . '-err">' . e($error) . '</span>' : '';
    $star = $required ? ' <span class="admin-req" aria-hidden="true">*</span>' : '';

    if ($field['type'] === 'bool') {
        return '<div class="admin-field admin-field--check">'
            . '<label><input type="checkbox" name="' . e($name) . '" value="1"' . ($value ? ' checked' : '') . '> '
            . e($field['label']) . '</label>' . $help . '</div>';
    }

    $label = '<label for="' . $id . '">' . e($field['label']) . $star . '</label>';
    $required_attr = $required ? ' required' : '';

    switch ($field['type']) {
        case 'textarea':
            $control = '<textarea id="' . $id . '" name="' . e($name) . '" rows="' . (int) ($field['rows'] ?? 4) . '"'
                     . $required_attr . $invalid . '>' . e((string) $value) . '</textarea>';
            break;

        case 'select':
            $control = '<select id="' . $id . '" name="' . e($name) . '"' . $required_attr . $invalid . '>';
            foreach ($field['options'] as $optValue => $optLabel) {
                $control .= '<option value="' . e((string) $optValue) . '"'
                          . ((string) $value === (string) $optValue ? ' selected' : '') . '>' . e($optLabel) . '</option>';
            }
            $control .= '</select>';
            break;

        case 'number':
        case 'decimal':
            $step = $field['type'] === 'decimal' ? '0.01' : '1';
            $control = '<input type="number" id="' . $id . '" name="' . e($name) . '" value="' . e((string) $value)
                     . '" step="' . $step . '" min="' . (int) ($field['min'] ?? 0) . '"' . $required_attr . $invalid . '>';
            break;

        default: // text, slug
            $control = '<input type="text" id="' . $id . '" name="' . e($name) . '" value="' . e((string) $value) . '"'
                     . (isset($field['placeholder']) ? ' placeholder="' . e($field['placeholder']) . '"' : '')
                     . $required_attr . $invalid . '>';
    }

    return '<div class="admin-field' . ($error !== null ? ' has-error' : '') . '">' . $label . $control . $help . $errorHtml . '</div>';
}

/**
 * The fields every media upload form shares: title, alt text, caption, where it
 * is shown, and order. Alt text is required for accessibility and for image search.
 */
function media_form_fields(array $projects, array $products, string $selected = 'gallery'): string
{
    $sel = static function (string $value) use ($selected): string {
        return $value === $selected ? ' selected' : '';
    };
    $options = '<option value="gallery"' . $sel('gallery') . '>Gallery (public photos and videos page)</option>';
    if ($projects !== []) {
        $options .= '<optgroup label="Project pages">';
        foreach ($projects as $p) {
            $v = 'project:' . (int) $p['id'];
            $options .= '<option value="' . $v . '"' . $sel($v) . '>' . e($p['name']) . '</option>';
        }
        $options .= '</optgroup>';
    }
    if ($products !== []) {
        $options .= '<optgroup label="Shop products">';
        foreach ($products as $p) {
            $v = 'product:' . (int) $p['id'];
            $options .= '<option value="' . $v . '"' . $sel($v) . '>' . e($p['name'] . ' (' . $p['pack_label'] . ')') . '</option>';
        }
        $options .= '</optgroup>';
    }

    return '<div class="admin-grid">'
        . '<div class="admin-field"><label for="title">Title</label><input type="text" id="title" name="title" maxlength="160" required></div>'
        . '<div class="admin-field"><label for="alt_text">Alt text (describe the picture)</label>'
        . '<input type="text" id="alt_text" name="alt_text" maxlength="255" placeholder="Metallic epoxy garage floor, Lekki, Lagos"></div>'
        . '<div class="admin-field admin-grid__wide"><label for="caption">Caption</label>'
        . '<input type="text" id="caption" name="caption" maxlength="400"></div>'
        . '<div class="admin-field"><label for="target">Shown on</label><select id="target" name="target">' . $options . '</select></div>'
        . '<div class="admin-field"><label for="sort">Order</label><input type="number" id="sort" name="sort" value="0" min="0"></div>'
        . '</div>';
}

/** Money for the admin tables and order views. */
function admin_money($amount): string
{
    return naira($amount);
}

/** A date in the server's timezone, for lists. */
function admin_date(?string $stamp): string
{
    return $stamp === null || $stamp === '' ? '-' : date('j M Y, H:i', strtotime($stamp));
}

/**
 * Order statuses in the order an order moves through them. Each has a meaning
 * the admin relies on: 'paid' means the transfer has arrived, 'dispatched'
 * means it has left the store.
 */
function admin_order_statuses(): array
{
    return [
        'new'        => 'New',
        'confirmed'  => 'Confirmed by phone',
        'paid'       => 'Paid',
        'dispatched' => 'Dispatched',
        'delivered'  => 'Delivered',
        'cancelled'  => 'Cancelled',
    ];
}

function admin_lead_statuses(): array
{
    return [
        'new'       => 'New',
        'contacted' => 'Contacted',
        'closed'    => 'Closed',
    ];
}

/** A coloured status pill. Colour is never the only cue: the label is always text. */
function admin_status_badge(string $kind, string $status): string
{
    $labels = match ($kind) {
        'order'       => admin_order_statuses(),
        'application' => application_statuses(),
        default       => admin_lead_statuses(),
    };
    $tone = match ($status) {
        'new'                                     => 'is-new',
        'paid', 'delivered', 'closed', 'hired'     => 'is-done',
        'cancelled', 'rejected'                    => 'is-off',
        default                                    => 'is-progress',
    };
    return '<span class="admin-pill ' . $tone . '">' . e($labels[$status] ?? $status) . '</span>';
}

/** How one list cell is displayed, from the field's 'format' setting. */
function admin_format_cell(array $field, $value, string $name, string $entityKey): string
{
    $format = $field['format'] ?? null;
    if ($format === 'money') {
        return e(admin_money($value));
    }
    if ($format === 'bool') {
        return (int) $value === 1
            ? '<span class="admin-pill is-done">Yes</span>'
            : '<span class="admin-pill is-off">No</span>';
    }
    if ($format === 'label' && isset($field['options'])) {
        return e($field['options'][(string) $value] ?? (string) $value);
    }
    return e(mb_strimwidth((string) $value, 0, 90, '...'));
}
