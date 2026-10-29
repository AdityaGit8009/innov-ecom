<?php
session_start();
include "../config/db.php";
require_once "../jwt.php";
require_once "../secure_helpers.php";

$user_id = ecom_user_id();
if ($user_id <= 0) { echo "login_required"; exit; }
$product_id = (int)($_POST['product_id'] ?? 0);
$qty = max(1, (int)($_POST['qty'] ?? 1));
$item_type = mysqli_real_escape_string($conn, $_POST['item_type'] ?? 'product');

if (!in_array($item_type, ['product', 'project'])) { $item_type = 'product'; }
if ($product_id <= 0) { echo "invalid"; exit; }

if ($item_type === 'project') {
    $available_stock = 999999;
} else {
    $stock_query = mysqli_query($conn, "SELECT stock FROM products WHERE id='$product_id' LIMIT 1");
    if (!$stock_query || mysqli_num_rows($stock_query) == 0) {
        echo "invalid";
        exit;
    }
    $stock_row = mysqli_fetch_assoc($stock_query);
    $available_stock = (int)$stock_row['stock'];
}

$check = mysqli_query($conn, "SELECT id, qty FROM cart WHERE user_id='$user_id' AND product_id='$product_id' AND item_type='$item_type' LIMIT 1");
if ($check && mysqli_num_rows($check) > 0) {
    $row = mysqli_fetch_assoc($check);
    $new_qty = $row['qty'] + $qty;
    
    if ($new_qty > $available_stock) {
        echo "out_of_stock";
        exit;
    }
    
    mysqli_query($conn, "UPDATE cart SET qty = $new_qty WHERE user_id='$user_id' AND product_id='$product_id' AND item_type='$item_type'");
} else {
    $new_qty = $qty;
    
    if ($new_qty > $available_stock) {
        echo "out_of_stock";
        exit;
    }
    
    mysqli_query($conn, "INSERT INTO cart (user_id, product_id, item_type, qty) VALUES ('$user_id', '$product_id', '$item_type', '$new_qty')");
}

echo "success:" . $new_qty;
