<?php
session_start();
include "../config/db.php";
require_once "../jwt.php";
require_once "../secure_helpers.php";

$user_id = ecom_user_id();
if ($user_id <= 0) { 
    echo json_encode(['status' => 'error', 'message' => 'Not logged in.']);
    exit; 
}

$order_id = (int)($_POST['order_id'] ?? 0);
$reason = mysqli_real_escape_string($conn, trim($_POST['reason'] ?? ''));

if ($order_id <= 0 || empty($reason)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid data.']);
    exit;
}

$q = mysqli_query($conn, "SELECT id, refund_status FROM orders WHERE id = $order_id AND user_id = $user_id LIMIT 1");
$order = mysqli_fetch_assoc($q);

if (!$order) {
    echo json_encode(['status' => 'error', 'message' => 'Order not found.']);
    exit;
}

if (($order['refund_status'] ?? 'None') !== 'None') {
    echo json_encode(['status' => 'error', 'message' => 'Refund already requested.']);
    exit;
}

$update = mysqli_query($conn, "UPDATE orders SET refund_status = 'Requested', refund_reason = '$reason' WHERE id = $order_id");

if ($update) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Database error.']);
}
