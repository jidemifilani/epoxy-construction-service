<?php
/**
 * Checkout. Shows the order summary and delivery form; on a valid POST it
 * places the order and sends the visitor to the confirmation page.
 */

require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/shop.php';

start_session_if_needed();

$lines = cart_lines();
if ($lines === []) {
    flash('error', 'Your cart is empty.');
    header('Location: ' . url('cart'), true, 303);
    exit;
}

$errors = [];
$old = [];

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    $old = $_POST;

    // Same CSRF, honeypot and throttle guards as the enquiry forms.
    $guard = submission_guard_failed();
    if ($guard !== null) {
        $errors['_form'] = $guard;
    }

    if ($errors === []) {
        [$customer, $errors] = validate_checkout();
    }

    $unavailable = array_filter($lines, static fn (array $line): bool => !$line['in_stock']);
    if ($errors === [] && $unavailable !== []) {
        $errors['_form'] = 'Some items in your cart are out of stock. Remove them to continue.';
    }

    if ($errors === []) {
        try {
            $reference = place_order($customer, $lines);
        } catch (Throwable $e) {
            error_log('[iris] order failed: ' . $e->getMessage());
            $errors['_form'] = 'We could not place your order just now. Please try again, or call ' . cfg('phone') . '.';
        }

        if ($errors === []) {
            $totals = cart_totals($lines);
            $_SESSION['last_order'] = [
                'reference' => $reference,
                'total'     => $totals['total'],
                'channel'   => cart_channel($lines),
            ];
            $_SESSION['last_submission'] = time();
            cart_clear();
            header('Location: ' . url('order-placed'), true, 303);
            exit;
        }
    }
}

$totals = cart_totals($lines);

$pageTitle       = 'Checkout | ' . cfg('company_name');
$pageDescription = 'Enter your delivery details to place your epoxy order.';
$noindex = true;   // private or transactional: never shown in search

$bannerEyebrow = 'Checkout';
$bannerTitle   = 'Delivery Details.';
$bannerLede    = 'Tell us where to deliver and how to reach you. We confirm the total and delivery slot by phone or WhatsApp before dispatch.';
$trail         = ['Epoxy for sale' => 'shop', 'Cart' => 'cart', 'Checkout' => null];

require __DIR__ . '/includes/header.php';
?>

<section class="sec">
  <div class="wrap split split--wide" style="align-items:start">

    <form class="form-card form reveal" method="post" action="<?= e(url('checkout')) ?>" novalidate>
      <?= csrf_field() ?>
      <div class="honeypot" aria-hidden="true">
        <label for="website">Website</label>
        <input type="text" id="website" name="website" tabindex="-1" autocomplete="off">
      </div>

      <?php if (!empty($errors['_form'])): ?>
      <p class="flash flash--error" role="alert"><?= e($errors['_form']) ?></p>
      <?php endif; ?>

      <fieldset class="fieldset">
        <legend>Who is this order for? <span class="req">*</span></legend>
        <div class="radios">
          <?php foreach (['individual' => 'Myself / my home', 'business' => 'A business or site'] as $value => $label):
              $checked = ($old['buyer_type'] ?? 'individual') === $value; ?>
          <label><input type="radio" name="buyer_type" value="<?= e($value) ?>"<?= $checked ? ' checked' : '' ?>><span><?= e($label) ?></span></label>
          <?php endforeach; ?>
        </div>
      </fieldset>

      <div class="field">
        <label for="company_name">Company name <span class="field__hint">(business orders)</span></label>
        <input type="text" id="company_name" name="company_name" value="<?= e(old($old, 'company_name')) ?>"
               <?= isset($errors['company_name']) ? 'aria-invalid="true"' : '' ?>>
        <?= field_error($errors, 'company_name') ?>
      </div>

      <div class="form__row form__row--2">
        <div class="field">
          <label for="customer_name">Your name <span class="req">*</span></label>
          <input type="text" id="customer_name" name="customer_name" required autocomplete="name"
                 value="<?= e(old($old, 'customer_name')) ?>" <?= isset($errors['customer_name']) ? 'aria-invalid="true"' : '' ?>>
          <?= field_error($errors, 'customer_name') ?>
        </div>
        <div class="field">
          <label for="phone">Phone / WhatsApp <span class="req">*</span></label>
          <input type="tel" id="phone" name="phone" required autocomplete="tel"
                 value="<?= e(old($old, 'phone')) ?>" <?= isset($errors['phone']) ? 'aria-invalid="true"' : '' ?>>
          <?= field_error($errors, 'phone') ?>
        </div>
      </div>

      <div class="field">
        <label for="email">Email <span class="req">*</span></label>
        <input type="email" id="email" name="email" required autocomplete="email"
               value="<?= e(old($old, 'email')) ?>" <?= isset($errors['email']) ? 'aria-invalid="true"' : '' ?>>
        <?= field_error($errors, 'email') ?>
      </div>

      <div class="field">
        <label for="address">Delivery address <span class="req">*</span></label>
        <input type="text" id="address" name="address" required autocomplete="street-address"
               value="<?= e(old($old, 'address')) ?>" <?= isset($errors['address']) ? 'aria-invalid="true"' : '' ?>>
        <?= field_error($errors, 'address') ?>
      </div>

      <div class="field">
        <label for="city">City <span class="req">*</span></label>
        <input type="text" id="city" name="city" required autocomplete="address-level2"
               value="<?= e(old($old, 'city')) ?>" <?= isset($errors['city']) ? 'aria-invalid="true"' : '' ?>>
        <?= field_error($errors, 'city') ?>
      </div>

      <div class="field">
        <label for="notes">Notes for us <span class="field__hint">(optional)</span></label>
        <textarea id="notes" name="notes" placeholder="Preferred delivery day, site access, anything we should know"><?= e(old($old, 'notes')) ?></textarea>
        <?= field_error($errors, 'notes') ?>
      </div>

      <div class="form__actions">
        <button class="btn btn--primary btn--lg" type="submit"><span>Place Order</span></button>
      </div>
      <p class="field__hint"><?= e(cfg('order_terms')) ?></p>
    </form>

    <aside class="rail">
      <div class="rail__card reveal">
        <h3>Your order</h3>
        <ul class="order-lines">
          <?php foreach ($lines as $line): ?>
          <li>
            <span><?= (int) $line['qty'] ?> &times; <?= e($line['name']) ?> <span class="field__hint">(<?= e($line['pack_label']) ?>)</span></span>
            <strong><?= e(naira($line['line_total'])) ?></strong>
          </li>
          <?php endforeach; ?>
        </ul>
        <dl class="spec" style="margin-top:1rem">
          <div class="spec__row"><dt>Subtotal</dt><dd><?= e(naira($totals['subtotal'])) ?></dd></div>
          <div class="spec__row"><dt>Delivery</dt><dd><?= $totals['free_delivery'] ? 'Free' : e(naira($totals['delivery'])) ?></dd></div>
          <div class="spec__row"><dt><strong>Total</strong></dt><dd><strong><?= e(naira($totals['total'])) ?></strong></dd></div>
        </dl>
        <p class="field__hint" style="margin-top:.8rem">Payment is by bank transfer. Bank details are shown on the next page.</p>
        <a class="link-arrow" href="<?= e(url('cart')) ?>">Edit cart<?= icon('arrow', 'link-arrow__icon') ?></a>
      </div>
    </aside>

  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
