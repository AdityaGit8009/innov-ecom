<?php
session_start();

header('Content-Type: application/json');

include 'config/db.php';
include 'config/razorpay.php';
require_once 'secure_helpers.php';
// require_once 'ecom_pricing.php';

/*
|--------------------------------------------------------------------------
| Razorpay Order Create API
|--------------------------------------------------------------------------
| Note:
| Localhost/XAMPP par SSL certificate issue aane par
| CURLOPT_SSL_VERIFYPEER => false
| CURLOPT_SSL_VERIFYHOST => 0
|
| Live server par inko true/2 kar dena.
*/

try {

    if (!function_exists('curl_init')) {
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'message' => 'PHP cURL extension is not enabled'
        ]);
        exit;
    }

    if (
        !defined('RAZORPAY_KEY_ID') ||
        !defined('RAZORPAY_KEY_SECRET') ||
        RAZORPAY_KEY_ID == '' ||
        RAZORPAY_KEY_SECRET == '' ||
        strpos(RAZORPAY_KEY_ID, 'REPLACE') !== false ||
        strpos(RAZORPAY_KEY_SECRET, 'REPLACE') !== false
    ) {
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'message' => 'Razorpay keys are not configured. Please update config/razorpay.php'
        ]);
        exit;
    }

    $user_id = ecom_user_id();

    if ($user_id <= 0) {
        http_response_code(401);
        echo json_encode([
            'status' => 'error',
            'message' => 'Login required'
        ]);
        exit;
    }

    $address_id = (int)($_SESSION['checkout_address_id'] ?? 0);

    if ($address_id <= 0) {
        http_response_code(400);
        echo json_encode([
            'status' => 'error',
            'message' => 'Address missing'
        ]);
        exit;
    }

    $user_id = (int)$user_id;

    $cart_sql = "
    SELECT 
        c.item_type,
        c.qty,

        IF(c.item_type='project', pr.id, p.id) AS product_id,

        IF(c.item_type='project', pr.price, p.price) AS price,

        IF(c.item_type='project', pr.base_price, p.base_price) AS base_price,

        IF(c.item_type='project', pr.default_tax_percentage, p.default_tax_percentage) AS default_tax_percentage,

        IF(c.item_type='project', pr.tax_percentage, p.tax_percentage) AS tax_percentage,

        IF(c.item_type='project', 0, COALESCE(p.delivery_charge,0)) AS delivery_charge,

        IF(c.item_type='project', 9999, p.stock) AS stock,

        IF(c.item_type='project', pr.status, p.status) AS status

    FROM cart c

    LEFT JOIN products p 
        ON p.id = c.product_id 
        AND c.item_type='product'

    LEFT JOIN projects pr 
        ON pr.id = c.product_id 
        AND c.item_type='project'

    WHERE c.user_id='$user_id'

    AND (
        (c.item_type='product' AND p.status=1)
        OR
        (c.item_type='project' AND pr.status=1)
    )
";

    $cart = mysqli_query($conn, $cart_sql);

    if (!$cart) {
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'message' => 'Cart query failed',
            'debug' => mysqli_error($conn)
        ]);
        exit;
    }

    if (mysqli_num_rows($cart) == 0) {
        http_response_code(400);
        echo json_encode([
            'status' => 'error',
            'message' => 'Cart is empty'
        ]);
        exit;
    }

    $subtotal = 0;
    $productDeliveryTotal = 0;

    while ($row = mysqli_fetch_assoc($cart)) {

        $qty   = (int)$row['qty'];
        $stock = (int)$row['stock'];

        // SAME PRICE AS PLACE ORDER PAGE
        $price = (float)$row['price'];

        if ($qty <= 0 || $stock < $qty) {

            http_response_code(400);

            echo json_encode([
                'status'  => 'error',
                'message' => 'Stock not available for one or more products'
            ]);

            exit;
        }

        $subtotal += ($price * $qty);

        $productDeliveryTotal +=
            ((float)$row['delivery_charge'] * $qty);
    }

    $addr = mysqli_fetch_assoc(mysqli_query($conn, "SELECT pincode FROM customer_addresses WHERE id='$address_id' AND customer_id='$user_id' LIMIT 1"));
    $quote = ['serviceable' => true, 'charge' => 0, 'partner' => 'Product Delivery Charge', 'message' => 'Product-wise delivery charge'];
    $delivery = (float)$productDeliveryTotal;
    $grand_total = $subtotal + $delivery;

    if ($grand_total <= 0) {
        http_response_code(400);
        echo json_encode([
            'status' => 'error',
            'message' => 'Invalid order amount'
        ]);
        exit;
    }

    $amount_paise = (int) round($grand_total * 100);
    $receipt = 'SIP_' . date('YmdHis') . '_' . $user_id;

    $payload_array = [
        'amount' => $amount_paise,
        'currency' => defined('RAZORPAY_CURRENCY') ? RAZORPAY_CURRENCY : 'INR',
        'receipt' => $receipt,
        'payment_capture' => 1
    ];

    $payload = json_encode($payload_array);

    $ch = curl_init('https://api.razorpay.com/v1/orders');

    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $payload,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json'
        ],
        CURLOPT_USERPWD => RAZORPAY_KEY_ID . ':' . RAZORPAY_KEY_SECRET,
        CURLOPT_TIMEOUT => 30,

        // Localhost/XAMPP testing only
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => 0
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    if ($error) {
        http_response_code(502);
        echo json_encode([
            'status' => 'error',
            'message' => 'Razorpay order creation failed',
            'debug' => $error
        ]);
        exit;
    }

    if ($httpCode < 200 || $httpCode >= 300) {
        http_response_code(502);
        echo json_encode([
            'status' => 'error',
            'message' => 'Razorpay order creation failed',
            'http_code' => $httpCode,
            'debug' => $response
        ]);
        exit;
    }

    $data = json_decode($response, true);

    if (!is_array($data) || empty($data['id'])) {
        http_response_code(502);
        echo json_encode([
            'status' => 'error',
            'message' => 'Invalid Razorpay response',
            'debug' => $response
        ]);
        exit;
    }

    $_SESSION['razorpay_order_id'] = $data['id'];
    $_SESSION['razorpay_amount'] = $grand_total;
    $_SESSION['razorpay_amount_paise'] = $amount_paise;

    echo json_encode([
        'status' => 'success',
        'key' => RAZORPAY_KEY_ID,
        'order_id' => $data['id'],
        'amount' => $amount_paise,
        'currency' => defined('RAZORPAY_CURRENCY') ? RAZORPAY_CURRENCY : 'INR',
        'store_name' => defined('RAZORPAY_STORE_NAME') ? RAZORPAY_STORE_NAME : 'SIP Innova'
    ]);
    exit;
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Server error while creating Razorpay order',
        'debug' => $e->getMessage()
    ]);
    exit;
}
