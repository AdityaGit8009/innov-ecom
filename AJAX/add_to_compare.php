<?php
session_start();
include "../config/db.php";
require_once "../jwt.php";
require_once "../secure_helpers.php";
$user_id = ecom_user_id();
if ($user_id <= 0) { echo "login_required"; exit; }
$product_id = (int)($_POST['product_id'] ?? 0);
if ($product_id <= 0) { echo "invalid"; exit; }
$count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS cnt FROM compare WHERE user_id='$user_id'"))['cnt'] ?? 0;
$exists = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM compare WHERE user_id='$user_id' AND product_id='$product_id' LIMIT 1"));
if (!$exists && $count >= 4) { echo "limit"; exit; }
mysqli_query($conn, "INSERT IGNORE INTO compare (user_id, product_id) VALUES ('$user_id', '$product_id')");
echo "success";
