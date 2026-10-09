<?php
/**
 * Epoxy sales: the cart, pricing and placing orders.
 *
 * The cart lives in the session as product id => quantity. Prices are never
 * read from the request -- every price is looked up from the products table
 * when the cart is shown and again when the order is placed.
 */

declare(strict_types=1);

require_once __DIR__ . '/leads.php';   // posted() and friends, used by checkout
require_once __DIR__ . '/tracking.php';

const CART_MAX_QTY = 500;     // per line, in packs
const CART_MAX_LINES = 40;

/** Retail and wholesale are shown on separate sections of the shop. */
const SHOP_CHANNELS = ['retail', 'wholesale'];

function cart_raw(): array
{
    start_session_if_needed();
    return is_array($_SESSION['cart'] ?? null) ? $_SESSION['cart'] : [];
}

function cart_save(array $cart): void
{
    start_session_if_needed();
    $_SESSION['cart'] = $cart;
}

/** Packs in the cart, across all products. Shown in the header. */
function cart_count(): int
{
    return array_sum(cart_raw());
}

/** Add packs to the cart. Returns an error message, or null on success. */
function cart_add(int $productId, int $qty): ?string
{
    $product = product_by_id($productId);
    if ($product === null) {
        return 'That product is no longer available.';
    }
    if (!(bool) $product['in_stock']) {
        return $product['name'] . ' is out of stock at the moment.';
    }

    $cart = cart_raw();
    $new = ($cart[$productId] ?? 0) + max($qty, (int) $product['min_qty']);
    if ($new > CART_MAX_QTY) {
        return 'Please order no more than ' . CART_MAX_QTY . ' packs of one item at a time. For larger orders, call us.';
    }
    if (!isset($cart[$productId]) && count($cart) >= CART_MAX_LINES) {
        return 'Your cart is full. Please place this order and start another.';
    }

    $cart[$productId] = $new;
    cart_save($cart);
    return null;
}

/** Change one line's quantity. A quantity of 0 removes the line. */
function cart_set(int $productId, int $qty): ?string
{
    $cart = cart_raw();
    if (!isset($cart[$productId])) {
        return null;
    }
    if ($qty <= 0) {
        unset($cart[$productId]);
        cart_save($cart);
        return null;
    }

    $product = product_by_id($productId);
    if ($product === null) {
        unset($cart[$productId]);
        cart_save($cart);
        return 'One item was removed because it is no longer sold.';
    }
    if ($qty < (int) $product['min_qty']) {
        return $product['name'] . ' (' . $product['pack_label'] . ') needs at least ' . $product['min_qty'] . ' pack(s).';
    }
    if ($qty > CART_MAX_QTY) {
        return 'Please order no more than ' . CART_MAX_QTY . ' packs of one item at a time.';
    }

    $cart[$productId] = $qty;
    cart_save($cart);
    return null;
}

function cart_clear(): void
{
    cart_save([]);
}

function product_by_id(int $id): ?array
{
    return db_row('SELECT id, name, channel, pack_label, unit_price, min_qty, in_stock
                   FROM products WHERE id = ? AND published = 1', [$id]);
}

/**
 * The cart as priced lines, from the database. Any product that has been
 * unpublished or removed since it was added is dropped here, so the visitor
 * never pays for something we no longer sell.
 */
function cart_lines(): array
{
    $cart = cart_raw();
    if ($cart === []) {
        return [];
    }

    $ids = array_map('intval', array_keys($cart));
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $rows = db_rows("SELECT id, name, channel, pack_label, unit_price, min_qty, in_stock
                     FROM products WHERE published = 1 AND id IN ($placeholders)", $ids);

    $lines = [];
    $kept = [];
    foreach ($rows as $product) {
        $qty = (int) $cart[$product['id']];
        $unit = (float) $product['unit_price'];
        $lines[] = [
            'product_id' => (int) $product['id'],
            'name'       => $product['name'],
            'channel'    => $product['channel'],
            'pack_label' => $product['pack_label'],
            'unit_price' => $unit,
            'qty'        => $qty,
            'line_total' => $unit * $qty,
            'in_stock'   => (bool) $product['in_stock'],
        ];
        $kept[(int) $product['id']] = $qty;
    }

    if (count($kept) !== count($cart)) {
        cart_save($kept);
    }
    return $lines;
}

/** Subtotal, delivery and total for a set of lines. Amounts in naira. */
function cart_totals(array $lines): array
{
    $subtotal = array_sum(array_column($lines, 'line_total'));
    $threshold = (float) cfg('free_delivery_above', 0);
    $delivery = ($subtotal <= 0 || ($threshold > 0 && $subtotal >= $threshold))
        ? 0.0
        : (float) cfg('delivery_fee', 0);

    return [
        'subtotal' => $subtotal,
        'delivery' => $delivery,
        'total'    => $subtotal + $delivery,
        'free_delivery' => $delivery === 0.0 && $subtotal > 0,
    ];
}

/** 'retail', 'wholesale' or 'mixed', from the lines in the cart. */
function cart_channel(array $lines): string
{
    $channels = array_unique(array_column($lines, 'channel'));
    return count($channels) === 1 ? $channels[0] : 'mixed';
}

/** A money amount for display: N12,500 */
function naira($amount): string
{
    return cfg('currency', 'N') . number_format((float) $amount, 0);
}

/**
 * Place the order from the current cart. Runs in a transaction so an order
 * is never half-saved. Returns the new order's reference.
 */
function place_order(array $customer, array $lines): string
{
    $totals = cart_totals($lines);
    $pdo = db();
    $pdo->beginTransaction();
    try {
        $reference = unique_order_reference();
        $orderId = db_insert('orders', [
            'reference'     => $reference,
            'channel'       => cart_channel($lines),
            'buyer_type'    => $customer['buyer_type'],
            'company_name'  => $customer['company_name'],
            'customer_name' => $customer['customer_name'],
            'email'         => $customer['email'],
            'phone'         => $customer['phone'],
            'address'       => $customer['address'],
            'city'          => $customer['city'],
            'notes'         => $customer['notes'],
            'subtotal'      => $totals['subtotal'],
            'delivery_fee'  => $totals['delivery'],
            'total'         => $totals['total'],
            'status'        => 'new',
            'admin_note'    => tracking_summary() ?: null,
            'ip_address'    => substr((string) ($_SERVER['REMOTE_ADDR'] ?? ''), 0, 45),
        ]);

        foreach ($lines as $line) {
            db_insert('order_items', [
                'order_id'     => $orderId,
                'product_id'   => $line['product_id'],
                'product_name' => $line['name'],
                'pack_label'   => $line['pack_label'],
                'unit_price'   => $line['unit_price'],
                'qty'          => $line['qty'],
                'line_total'   => $line['line_total'],
            ]);
        }

        $pdo->commit();
    } catch (Throwable $e) {
        $pdo->rollBack();
        throw $e;
    }

    notify_owner_whatsapp(
        'order (' . cart_channel($lines) . ')',
        (string) $customer['customer_name'],
        (string) $customer['phone'],
        whatsapp_detail_line([
            count($lines) . ' item' . (count($lines) === 1 ? '' : 's'),
            naira($totals['total']),
            $customer['city'] ?? '',
        ]),
        $reference
    );

    return $reference;
}

/** A short reference a customer can quote on the phone, e.g. IES-7K2M9Q. */
function unique_order_reference(): string
{
    for ($attempt = 0; $attempt < 5; $attempt++) {
        $reference = 'IES-' . strtoupper(substr(bin2hex(random_bytes(4)), 0, 6));
        if (db_value('SELECT id FROM orders WHERE reference = ?', [$reference]) === null) {
            return $reference;
        }
    }
    throw new RuntimeException('Could not allocate an order reference.');
}

/** Validate the checkout form. Returns [customer data, errors]. */
function validate_checkout(): array
{
    $data = [];
    $errors = [];

    $data['buyer_type'] = in_array($_POST['buyer_type'] ?? '', ['individual', 'business'], true)
        ? (string) $_POST['buyer_type'] : 'individual';

    $required = [
        'customer_name' => ['Your name', 120],
        'email'         => ['Email', 160],
        'phone'         => ['Phone / WhatsApp', 40],
        'address'       => ['Delivery address', 255],
        'city'          => ['City', 120],
    ];
    foreach ($required as $field => [$label, $max]) {
        $value = posted($field);
        if ($value === '') {
            $errors[$field] = $label . ' is required.';
        } elseif (mb_strlen($value) > $max) {
            $errors[$field] = $label . ' is too long.';
        }
        $data[$field] = $value;
    }

    $data['company_name'] = posted('company_name');
    if ($data['buyer_type'] === 'business' && $data['company_name'] === '') {
        $errors['company_name'] = 'Enter the company name for a business order.';
    }
    if ($data['email'] !== '' && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Enter a valid email address.';
    }
    if ($data['phone'] !== '' && preg_match_all('/[0-9]/', $data['phone']) < 7) {
        $errors['phone'] = 'Enter a phone or WhatsApp number we can reach you on.';
    }

    $data['notes'] = posted_text('notes');
    if (mb_strlen($data['notes']) > 1000) {
        $errors['notes'] = 'Notes must be 1000 characters or fewer.';
    }

    return [$data, $errors];
}
