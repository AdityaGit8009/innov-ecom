<?php
session_start();
header('Content-Type: application/json');
include 'config/db.php';
require_once 'ecom_pricing.php';
$user_id = (int)($_SESSION['user_id'] ?? 0);
$pincode = preg_replace('/\D/', '', $_GET['pincode'] ?? $_POST['pincode'] ?? '');
$rows = [];
$cart = mysqli_query($conn, "SELECT c.qty, p.price, p.base_price, p.default_tax_percentage, p.tax_percentage FROM cart c JOIN products p ON p.id=c.product_id WHERE c.user_id='$user_id' AND p.status=1");
while ($cart && $r = mysqli_fetch_assoc($cart)) {
    $rows[] = $r;
}
$totals = ecom_cart_totals($rows);
$quote = ecom_delivery_quote($conn, $pincode, $totals['items_total'], count($rows));
if (strlen($pincode) === 6) {
    $api = @file_get_contents('https://api.postalpincode.in/pincode/' . $pincode);
    if ($api) {
        $decoded = json_decode($api, true);
        if (!empty($decoded[0]['Status']) && $decoded[0]['Status'] === 'Success') {
            $quote['city'] = $decoded[0]['PostOffice'][0]['District'] ?? '';
            $quote['state'] = $decoded[0]['PostOffice'][0]['State'] ?? '';
        } else {
            $quote['serviceable'] = false;
            $quote['message'] = 'Pincode not serviceable';
        }
    }
}
echo json_encode(['status' => 'success', 'totals' => $totals, 'delivery' => $quote]);
