<?php
session_start();
include "config/db.php";
include "order_helpers.php";
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}
$user_id = (int)$_SESSION['user_id'];
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header("Location: orders.php");
  exit;
}
$order_id = (int)($_POST['order_id'] ?? 0);
$reason = mysqli_real_escape_string($conn, trim($_POST['cancel_reason'] ?? ''));
if ($order_id <= 0) {
  header("Location: orders.php?msg=invalid");
  exit;
}
$orderQuery = mysqli_query($conn, "SELECT * FROM orders WHERE id='$order_id' AND user_id='$user_id' LIMIT 1");
if (!$orderQuery || mysqli_num_rows($orderQuery) == 0) {
  header("Location: orders.php?msg=not_found");
  exit;
}
$order = mysqli_fetch_assoc($orderQuery);
$currentStatus = $order['order_status'] ?? 'Pending';
$paymentMethod = strtoupper(trim($order['payment_method'] ?? ''));
$paymentStatus = $order['payment_status'] ?? 'Pending';
if (!sip_can_customer_cancel($currentStatus)) {
  header("Location: order-details.php?order_id=" . $order_id . "&msg=cannot_cancel");
  exit;
}
$newPaymentStatus = $paymentStatus;
if ($paymentMethod === 'COD') {
  $newPaymentStatus = 'Pending';
} elseif ($paymentStatus === 'Paid') {
  $newPaymentStatus = 'Refunded';
}
$cancelNote = $reason !== '' ? "Cancelled by customer: " . $reason : "Cancelled by customer";
$cancelReason = mysqli_real_escape_string($conn, $reason ?: 'Cancelled by customer');
$update = mysqli_query($conn, "UPDATE orders SET order_status='Cancelled', payment_status='$newPaymentStatus', cancelled_by='Customer', cancel_reason='$cancelReason', cancelled_at=IFNULL(cancelled_at,NOW()) WHERE id='$order_id' AND user_id='$user_id'");
if ($update) {
  sip_restore_order_stock_once($conn, $order_id);
  mysqli_query($conn, "UPDATE transactions SET payment_status='$newPaymentStatus' WHERE order_id='$order_id'");
}
if (!$update) {
  die("Cancel failed: " . mysqli_error($conn));
}
sip_history_insert_once($conn, $order_id, 'Cancelled', $cancelNote);
header("Location: order-details.php?order_id=" . $order_id . "&msg=cancelled");
exit;
