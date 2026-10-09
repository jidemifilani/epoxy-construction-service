<?php
/**
 * Order confirmation. Reads the order reference from the session, so no
 * customer details or order numbers ever appear in a URL.
 */

require_once __DIR__ . '/includes/functions.php';

start_session_if_needed();
$order = $_SESSION['last_order'] ?? null;
if (!is_array($order)) {
    header('Location: ' . url('shop'), true, 303);
    exit;
}

$pageTitle       = 'Order ' . $order['reference'] . ' Received | ' . cfg('company_name');
$pageDescription = 'Your epoxy order has been received. Bank transfer details and next steps.';
$noindex = true;   // private or transactional: never shown in search

$bannerEyebrow = 'Order received';
$bannerTitle   = 'Thank you. Your reference is ' . e($order['reference']) . '.';
$bannerLede    = 'We will confirm the total and delivery slot by phone or WhatsApp. Quote your reference in any message or on the transfer narration.';
$trail         = ['Epoxy for sale' => 'shop', 'Order received' => null];

require __DIR__ . '/includes/header.php';
?>

<section class="sec">
  <div class="wrap split split--wide" style="align-items:start">

    <div class="panel reveal">
      <p class="eyebrow">Pay by bank transfer</p>
      <h2 style="font-size:1.5rem">Amount due: <?= e(naira($order['total'])) ?></h2>
      <dl class="spec" style="margin-top:1rem">
        <div class="spec__row"><dt>Bank</dt><dd><?= e(cfg('bank_name')) ?></dd></div>
        <div class="spec__row"><dt>Account name</dt><dd><?= e(cfg('bank_account_name')) ?></dd></div>
        <div class="spec__row"><dt>Account number</dt><dd><?= e(cfg('bank_account_number')) ?></dd></div>
        <div class="spec__row"><dt>Narration</dt><dd><?= e($order['reference']) ?></dd></div>
      </dl>
      <p class="field__hint" style="margin-top:1rem"><?= e(cfg('order_terms')) ?></p>
    </div>

    <aside class="rail">
      <div class="rail__card reveal">
        <h3>What happens next</h3>
        <?php
        tick_list([
            'We call or WhatsApp you to confirm the order',
            'Pay by transfer and send us the receipt',
            'Delivery is scheduled once payment is received',
        ]);
        ?>
      </div>
      <div class="rail__card reveal">
        <h3>Questions about your order?</h3>
        <div class="cta-row" style="flex-direction:column;align-items:stretch;margin-top:.6rem">
          <?= button('WhatsApp Us', whatsapp_link('Hello, my order reference is ' . $order['reference'] . '.'), 'primary', ['target' => '_blank', 'rel' => 'noopener']) ?>
          <?= button('Call ' . cfg('phone'), tel_link(), 'ghost') ?>
        </div>
      </div>
    </aside>

  </div>
</section>

<?= tracking_conversion('purchase', (float) $order['total'], (string) $order['reference']) ?>
<?php require __DIR__ . '/includes/footer.php'; ?>
