<?php
require_once '../check_admin.php';

$order_id = (int)($_POST['order_id'] ?? 0);
$status = mysqli_real_escape_string($conn, trim($_POST['status'] ?? ''));

if ($order_id <= 0 || empty($status)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid data.']);
    exit;
}

if (!in_array($status, ['Requested', 'Approved', 'Rejected', 'Refunded'])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid status.']);
    exit;
}

$update = mysqli_query($conn, "UPDATE orders SET refund_status = '$status' WHERE id = $order_id");

if ($update) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Database error.']);
}
?>
