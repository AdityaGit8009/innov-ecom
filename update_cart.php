<?php
session_start();

include "config/db.php";
require_once "ecom_pricing.php";

header('Content-Type: application/json');

$user_id = (int)($_SESSION['user_id'] ?? 0);

$cart_id = (int)($_POST['cart_id'] ?? 0);

$type = $_POST['type'] ?? '';

if ($user_id <= 0) {

    echo json_encode([
        'status' => 'error',
        'message' => 'Login required'
    ]);
    exit;
}

if ($cart_id <= 0) {

    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid cart'
    ]);
    exit;
}

/* =========================
   CHECK CART ITEM
========================= */

$checkQ = mysqli_query($conn, "
    SELECT
        c.qty,
        c.item_type,
        IF(c.item_type='project', 999999, p.stock) AS stock,
        IF(c.item_type='project', pr.price, p.price) AS price,
        IF(c.item_type='project', pr.base_price, p.base_price) AS base_price,
        IF(c.item_type='project', pr.default_tax_percentage, p.default_tax_percentage) AS default_tax_percentage,
        IF(c.item_type='project', pr.tax_percentage, p.tax_percentage) AS tax_percentage,
        IF(c.item_type='project', pr.old_price, p.old_price) AS old_price,
        IF(c.item_type='project', 0, COALESCE(p.delivery_charge,0)) AS delivery_charge
    FROM cart c
    LEFT JOIN products p ON p.id = c.product_id AND c.item_type = 'product'
    LEFT JOIN projects pr ON pr.id = c.product_id AND c.item_type = 'project'
    WHERE c.id = '$cart_id'
    AND c.user_id = '$user_id'
");

if (!$checkQ || mysqli_num_rows($checkQ) == 0) {

    echo json_encode([
        'status' => 'error',
        'message' => 'Cart item not found'
    ]);
    exit;
}

$item = mysqli_fetch_assoc($checkQ);

$currentQty = (int)$item['qty'];

/* =========================
   NEW QTY
========================= */

$newQty = $currentQty;

if ($type === 'plus') {

    if ($currentQty >= (int)$item['stock']) {
        echo json_encode([
            'status' => 'error',
            'message' => '❌ Product out of stock'
        ]);
        exit;
    }
    $newQty++;
} elseif ($type === 'minus') {

    if ($currentQty > 1) {
        $newQty--;
    }
} else {

    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid action'
    ]);
    exit;
}

/* =========================
   UPDATE CART
========================= */

mysqli_query($conn, "

    UPDATE cart

    SET qty = '$newQty'

    WHERE id = '$cart_id'
    AND user_id = '$user_id'

");

/* =========================
   ITEM TOTAL
========================= */
$priceInfo = ecom_product_price($item);
$itemTotal = $priceInfo['sale'] * $newQty;

/* =========================
   FULL CART SUMMARY
========================= */
$summaryQ = mysqli_query($conn, "
    SELECT
        c.qty,
        c.item_type,
        IF(c.item_type='project', pr.price, p.price) AS price,
        IF(c.item_type='project', pr.base_price, p.base_price) AS base_price,
        IF(c.item_type='project', pr.default_tax_percentage, p.default_tax_percentage) AS default_tax_percentage,
        IF(c.item_type='project', pr.tax_percentage, p.tax_percentage) AS tax_percentage,
        IF(c.item_type='project', pr.old_price, p.old_price) AS old_price,
        IF(c.item_type='project', 0, COALESCE(p.delivery_charge,0)) AS delivery_charge
    FROM cart c
    LEFT JOIN products p ON p.id = c.product_id AND c.item_type = 'product'
    LEFT JOIN projects pr ON pr.id = c.product_id AND c.item_type = 'project'
    WHERE c.user_id = '$user_id'
");
$cartRows = [];
while ($r = mysqli_fetch_assoc($summaryQ)) { $cartRows[] = $r; }
$totals = ecom_cart_totals($cartRows);

$subTotal = $totals['subtotal'];
$taxTotal = $totals['tax'];
$deliveryTotal = $totals['delivery_total'];
$grandTotal = $subTotal + $taxTotal + $deliveryTotal;

/* =========================
   RESPONSE
========================= */
echo json_encode([
    'status' => 'success',
    'qty' => $newQty,
    'item_total' => round($itemTotal, 2),
    'sub_total' => round($subTotal, 2),
    'tax_total' => round($taxTotal, 2),
    'delivery_charge' => round($deliveryTotal, 2),
    'grand_total' => round($grandTotal, 2)
]);

exit;
