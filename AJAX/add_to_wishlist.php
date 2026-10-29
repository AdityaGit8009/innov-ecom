<?php
session_start();
include "../config/db.php";
require_once "../jwt.php";
require_once "../secure_helpers.php";
$user_id = ecom_user_id();
if ($user_id <= 0) { echo "login_required"; exit; }
$product_id = (int)($_POST['product_id'] ?? 0);
if ($product_id <= 0) { echo "invalid"; exit; }
mysqli_query($conn, "INSERT IGNORE INTO wishlist (user_id, product_id) VALUES ('$user_id', '$product_id')");
echo "success";
