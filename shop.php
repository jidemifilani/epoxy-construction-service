<?php
/** EPOXY SALES - the shop. Retail (1 kg and up) and wholesale (by the drum). */

require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/shop.php';

$pageTitle       = 'Buy Epoxy Resin Nigeria | 1 kg Retail, Wholesale Drums';
$pageDescription = 'Buy epoxy floor coat, primer and aggregate online. Retail packs from 1 kg, wholesale drums for contractors and facilities. Order in minutes, delivered across ' . cfg('service_area') . '.';
$pageKeywords    = 'buy epoxy resin Nigeria, epoxy for sale Nigeria, epoxy resin price Nigeria, epoxy primer 1kg Nigeria, epoxy drum wholesale Nigeria, epoxy floor coat Lagos, epoxy aggregate Nigeria';

$bannerEyebrow = 'Epoxy for sale';
$bannerTitle   = 'Epoxy by the Kilo. Epoxy by the Drum.';
$bannerLede    = 'Retail packs from <strong>1 kg</strong> for DIY and small jobs. <strong>Wholesale drums</strong> for contractors and facilities. Choose your pack, place the order, and we confirm payment and delivery by phone or WhatsApp.';
$trail         = ['Epoxy for sale' => null];
$bannerCtas    = button('Shop Retail', '#retail', 'primary')
               . button('Shop Wholesale', '#wholesale', 'ghost')
               . button('View cart (' . cart_count() . ')', url('cart'), 'secondary');

require __DIR__ . '/includes/header.php';

/**
 * One card per product name. The visitor picks a pack size from a dropdown,
 * so a product with a 1 kg, 5 kg and 20 kg tin is one card, not three.
 */
function render_product_cards(array $products): void
{
    $grouped = [];
    foreach ($products as $product) {
        $grouped[$product['name']][] = $product;
    }

    echo '<div class="grid grid--3">';
    foreach ($grouped as $name => $packs) {
        $first = $packs[0];
        $anyInStock = array_filter($packs, static fn (array $p): bool => (bool) $p['in_stock']) !== [];
        $minQty = max(array_map(static fn (array $p): int => (int) $p['min_qty'], $packs));

        echo '<article class="card shop-card reveal">';
        $photo = media_first('product', (int) $first['id']);
        if ($photo !== null) {
            echo '<div class="shop-card__photo">' . media_img($photo, '(min-width: 900px) 33vw, 100vw') . '</div>';
        }
        echo '<div class="shop-card__head">' . icon('flake', 'shop-card__icon') . '<h3>' . e($name) . '</h3></div>';
        echo '<p class="shop-card__desc">' . e((string) $first['description']) . '</p>';

        if (!$anyInStock) {
            echo '<p class="shop-card__stock">Out of stock. Call us for the next delivery date.</p>';
        } else {
            echo '<form class="shop-card__form" method="post" action="' . e(url('cart')) . '">';
            echo csrf_field();
            echo '<input type="hidden" name="action" value="add">';
            // Send the visitor back to the section they were shopping in.
            echo '<input type="hidden" name="back" value="shop">';
            echo '<input type="hidden" name="channel" value="' . e((string) $first['channel']) . '">';

            echo '<label class="field"><span>Pack size</span><select name="product_id" required>';
            foreach ($packs as $pack) {
                $label = $pack['pack_label'] . ' - ' . naira($pack['unit_price']);
                echo '<option value="' . (int) $pack['id'] . '"' . ($pack['in_stock'] ? '' : ' disabled') . '>'
                    . e($label) . ($pack['in_stock'] ? '' : ' (out of stock)') . '</option>';
            }
            echo '</select></label>';

            echo '<label class="field"><span>Quantity (packs)</span>'
                . '<input type="number" name="qty" value="' . $minQty . '" min="' . $minQty . '" max="' . CART_MAX_QTY . '" step="1" required></label>';

            echo '<button class="btn btn--primary" type="submit"><span>Add to cart</span></button>';
            echo '</form>';
        }
        echo corner_frame() . '</article>';
    }
    echo '</div>';
}
?>

<!-- ===== Retail ===== -->
<section class="sec" id="retail">
  <div class="wrap">
    <?php section_head('Retail', 'From 1 kg, for Homes and Small Jobs.',
        'Garage floors, a bathroom, a workshop bench. Buy the exact amount you need, in tins, pails and bags.'); ?>
    <?php render_product_cards(shop_products('retail')); ?>
  </div>
</section>

<!-- ===== Wholesale ===== -->
<section class="sec sec--paper" id="wholesale">
  <div class="wrap">
    <?php section_head('Wholesale', 'By the Drum, for Contractors and Facilities.',
        'Priced per drum. Trade accounts can ask about volume discounts and scheduled deliveries.'); ?>
    <?php render_product_cards(shop_products('wholesale')); ?>

    <div class="cta-row" style="justify-content:center;margin-top:2rem">
      <?= button('Ask About Trade Pricing', url('for-architects') . '#enquiry', 'secondary') ?>
    </div>
  </div>
</section>

<section class="sec">
  <div class="wrap split">
    <div>
      <?php section_head('How ordering works', 'Order Online. We Confirm by Phone.', '', 'left'); ?>
      <p>Place your order here and you get a reference number straight away. We then confirm the total and your delivery slot by phone or WhatsApp, and dispatch once payment is received.</p>
    </div>
    <?php
    step_list([
        ['Choose your packs', 'Pick the size and quantity. Add as many products as you need.'],
        ['Place the order', 'Enter your delivery details. You receive a reference number.'],
        ['Confirm and pay', 'We confirm by phone or WhatsApp, then you pay by bank transfer.'],
        ['Delivery', 'Delivered across ' . cfg('service_area') . '. Wholesale drums dispatch within 3 working days.'],
    ], true);
    ?>
  </div>
</section>

<?php cta_band('Not Sure How Much You Need?', 'Send us the room size and we will work out the quantity, primer and aggregate for you.', 'Ask Us to Calculate'); ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
