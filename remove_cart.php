<?php
session_start();
include "config/db.php";
require_once "ecom_pricing.php";

header('Content-Type: application/json');

$user_id = (int)($_SESSION['user_id'] ?? 0);
$cart_id = (int)($_POST['cart_id'] ?? 0);

if ($user_id <= 0 || $user_id == 1) {
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

mysqli_query($conn, "DELETE FROM cart WHERE id = $cart_id AND user_id = $user_id");

/* Fresh cart total */
$cartQ = mysqli_query($conn, "
    SELECT 
        c.item_type,
        c.qty,
        IF(c.item_type='project', pr.price, p.price) AS price,
        IF(c.item_type='project', pr.base_price, p.base_price) AS base_price,
        IF(c.item_type='project', pr.default_tax_percentage, p.default_tax_percentage) AS default_tax_percentage,
        IF(c.item_type='project', pr.tax_percentage, p.tax_percentage) AS tax_percentage,
        IF(c.item_type='project', pr.old_price, p.old_price) AS old_price,
        IF(c.item_type='project', 0, COALESCE(p.delivery_charge,0)) AS delivery_charge
    FROM cart c
    LEFT JOIN products p ON p.id = c.product_id AND c.item_type = 'product'
    LEFT JOIN projects pr ON pr.id = c.product_id AND c.item_type = 'project'
    WHERE c.user_id = $user_id
");

$subTotal = 0;
$taxTotal = 0;
$productDeliveryTotal = 0;

while ($item = mysqli_fetch_assoc($cartQ)) {
  $priceInfo = ecom_product_price($item);
  $qty = (int)$item['qty'];

  $subTotal += $priceInfo['base'] * $qty;
  $taxTotal += $priceInfo['tax'] * $qty;
  $productDeliveryTotal += (float)($item['delivery_charge'] ?? 0) * $qty;
}

$deliveryQuote = ecom_delivery_quote($conn, '', $subTotal + $taxTotal, 1);
$deliveryCharge = $subTotal > 0 ? ($productDeliveryTotal > 0 ? $productDeliveryTotal : $deliveryQuote['charge']) : 0;
$grandTotal = $subTotal + $taxTotal + $deliveryCharge;

echo json_encode([
  'status' => 'success',
  'sub_total' => round($subTotal, 2),
  'tax_total' => round($taxTotal, 2),
  'delivery_charge' => round($deliveryCharge, 2),
  'grand_total' => round($grandTotal, 2)
]);
exit;
