<?php
/**
 * The cart. GET shows it. POST changes it, with one of: add, update, remove, clear.
 * Every change redirects back (PRG), so a refresh never re-adds an item.
 */

require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/shop.php';

start_session_if_needed();

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    if (!csrf_valid($_POST['csrf'] ?? null)) {
        flash('error', 'Your session expired. Please try again.');
        header('Location: ' . url('cart'), true, 303);
        exit;
    }

    $action = (string) ($_POST['action'] ?? '');
    $error  = null;
    $cleared = false;

    switch ($action) {
        case 'add':
            $error = cart_add((int) ($_POST['product_id'] ?? 0), (int) ($_POST['qty'] ?? 0));
            flash($error ? 'error' : 'success', $error ?? 'Added to your cart.');
            $back = $_POST['back'] ?? '';
            $target = is_string($back) && $back === 'shop' ? url('shop') . '#' . ($_POST['channel'] === 'wholesale' ? 'wholesale' : 'retail') : url('cart');
            header('Location: ' . $target, true, 303);
            exit;

        case 'update':
            $quantities = $_POST['qty'] ?? [];
            if (is_array($quantities)) {
                foreach ($quantities as $productId => $qty) {
                    $result = cart_set((int) $productId, (int) $qty);
                    $error = $error ?? $result;
                }
            }
            break;

        case 'remove':
            cart_set((int) ($_POST['product_id'] ?? 0), 0);
            break;

        case 'clear':
            cart_clear();
            $cleared = true;
            break;
    }

    if ($error) {
        flash('error', $error);
    } elseif ($action === 'update') {
        flash('success', 'Cart updated.');
    } elseif ($cleared) {
        flash('success', 'Your cart has been emptied.');
    }
    header('Location: ' . url('cart'), true, 303);
    exit;
}

$lines  = cart_lines();
$totals = cart_totals($lines);

$pageTitle       = 'Your Cart | ' . cfg('company_name');
$pageDescription = 'Review your epoxy order before checkout.';
$noindex = true;   // private or transactional: never shown in search

$bannerEyebrow = 'Your order';
$bannerTitle   = 'Your Cart.';
$bannerLede    = $lines === []
    ? 'Nothing in the cart yet. Retail packs start at 1 kg; wholesale is sold by the drum.'
    : 'Check the quantities, then continue to delivery details. We confirm the final total by phone or WhatsApp.';
$trail         = ['Epoxy for sale' => 'shop', 'Cart' => null];

require __DIR__ . '/includes/header.php';
?>

<section class="sec">
  <div class="wrap">
    <?php if ($lines === []): ?>
      <div class="card reveal" style="max-width:640px;margin:0 auto;text-align:center">
        <h2 style="font-size:1.5rem">Your cart is empty.</h2>
        <p>Browse retail packs from 1 kg, or wholesale drums for bulk supply.</p>
        <div class="cta-row" style="justify-content:center"><?= button('Shop Epoxy', url('shop'), 'primary') ?></div>
      </div>
    <?php else: ?>
    <div class="split split--wide" style="align-items:start">

      <form method="post" action="<?= e(url('cart')) ?>" class="reveal">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="update">
        <div class="table-wrap">
          <table>
            <thead>
              <tr>
                <th scope="col">Product</th>
                <th scope="col">Pack</th>
                <th scope="col">Price</th>
                <th scope="col">Packs</th>
                <th scope="col">Total</th>
                <th scope="col"><span class="visually-hidden">Remove</span></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($lines as $line): ?>
              <tr>
                <td>
                  <strong><?= e($line['name']) ?></strong>
                  <div class="field__hint"><?= e(ucfirst($line['channel'])) ?></div>
                  <?php if (!$line['in_stock']): ?><div class="field__error">Out of stock - remove or call us.</div><?php endif; ?>
                </td>
                <td><?= e($line['pack_label']) ?></td>
                <td><?= e(naira($line['unit_price'])) ?></td>
                <td>
                  <input type="number" name="qty[<?= (int) $line['product_id'] ?>]" value="<?= (int) $line['qty'] ?>"
                         min="1" max="<?= CART_MAX_QTY ?>" step="1" aria-label="Packs of <?= e($line['name']) ?>"
                         style="width:90px">
                </td>
                <td><strong><?= e(naira($line['line_total'])) ?></strong></td>
                <td>
                  <button class="btn btn--ghost btn--sm" type="submit" form="remove-<?= (int) $line['product_id'] ?>"><span>Remove</span></button>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
        <div class="cta-row">
          <button class="btn btn--secondary" type="submit"><span>Update Quantities</span></button>
          <a class="btn btn--ghost" href="<?= e(url('shop')) ?>"><span>Keep Shopping</span></a>
        </div>
      </form>

      <?php foreach ($lines as $line): ?>
      <form id="remove-<?= (int) $line['product_id'] ?>" method="post" action="<?= e(url('cart')) ?>" hidden>
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="remove">
        <input type="hidden" name="product_id" value="<?= (int) $line['product_id'] ?>">
      </form>
      <?php endforeach; ?>

      <aside class="rail">
        <div class="rail__card reveal">
          <h3>Order summary</h3>
          <dl class="spec" style="margin-top:.6rem">
            <div class="spec__row"><dt>Subtotal</dt><dd><?= e(naira($totals['subtotal'])) ?></dd></div>
            <div class="spec__row"><dt>Delivery</dt><dd>
              <?= $totals['free_delivery'] ? 'Free' : e(naira($totals['delivery'])) ?>
            </dd></div>
            <div class="spec__row"><dt><strong>Total</strong></dt><dd><strong><?= e(naira($totals['total'])) ?></strong></dd></div>
          </dl>
          <?php if ((float) cfg('free_delivery_above', 0) > 0 && !$totals['free_delivery']): ?>
          <p class="field__hint" style="margin-top:.8rem">Add <?= e(naira((float) cfg('free_delivery_above') - $totals['subtotal'])) ?> more for free delivery.</p>
          <?php endif; ?>
          <div class="cta-row" style="flex-direction:column;align-items:stretch">
            <?= button('Continue to Checkout', url('checkout'), 'primary') ?>
          </div>
        </div>

        <div class="rail__card reveal">
          <h3>Need a different quantity?</h3>
          <p style="font-size:.93rem">For orders above <?= e(CART_MAX_QTY) ?> packs of one item, or a scheduled drum delivery, call us and we will quote directly.</p>
          <div class="cta-row" style="margin-top:.8rem"><?= button('Call ' . cfg('phone'), tel_link(), 'ghost') ?></div>
        </div>
      </aside>

    </div>
    <?php endif; ?>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
