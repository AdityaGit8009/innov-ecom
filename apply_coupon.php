<?php
session_start();
require_once __DIR__ . '/config/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Please login to apply coupons.']);
    exit;
}

$user_id = (int)$_SESSION['user_id'];
$code = mysqli_real_escape_string($conn, trim($_POST['code'] ?? ''));

if (empty($code)) {
    echo json_encode(['status' => 'error', 'message' => 'Please enter a coupon code.']);
    exit;
}

// Fetch coupon
$q = mysqli_query($conn, "SELECT * FROM coupons WHERE code = '$code' AND status = 1 LIMIT 1");
if ($c = mysqli_fetch_assoc($q)) {
    $min_order = (float)$c['min_order_amount'];
    $product_id = (int)$c['product_id'];
    $discount_type = $c['discount_type'];
    $discount_val = (float)$c['discount_value'];

    // Get cart items and join with products
    $cartQuery = mysqli_query($conn, "
        SELECT c.*, p.price
        FROM cart c 
        JOIN products p ON c.product_id = p.id 
        WHERE c.user_id = '$user_id'
    ");
    
    $cartSubtotal = 0;
    $eligibleSubtotal = 0;
    $hasEligibleProduct = false;

    while ($row = mysqli_fetch_assoc($cartQuery)) {
        $qty = (int)$row['qty'];
        // Assuming price in products table is the final display price
        $lineTotal = (float)$row['price'] * $qty;
        $cartSubtotal += $lineTotal;

        if ($product_id === 0 || $product_id == $row['product_id']) {
            $hasEligibleProduct = true;
            $eligibleSubtotal += $lineTotal;
        }
    }

    if ($cartSubtotal < $min_order) {
        echo json_encode(['status' => 'error', 'message' => "Minimum order of ₹" . $min_order . " required."]);
        exit;
    }

    if (!$hasEligibleProduct) {
        echo json_encode(['status' => 'error', 'message' => "This coupon is not applicable to the items in your cart."]);
        exit;
    }

    // Calculate discount
    $discount_amount = 0;
    if ($discount_type === 'percent') {
        $discount_amount = ($eligibleSubtotal * $discount_val) / 100;
    } else {
        $discount_amount = $discount_val; // Flat discount
    }

    if ($discount_amount > $cartSubtotal) {
        $discount_amount = $cartSubtotal;
    }

    // Save in session
    $_SESSION['applied_coupon'] = [
        'code' => $c['code'],
        'discount_amount' => $discount_amount
    ];

    echo json_encode([
        'status' => 'success', 
        'message' => 'Coupon applied successfully!',
        'discount_amount' => $discount_amount
    ]);
    exit;

} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid or expired coupon code.']);
    exit;
}
