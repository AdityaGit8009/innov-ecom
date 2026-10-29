<?php
include "config/db.php";
include "jwt.php";

$id = intval($_GET['id'] ?? 0);
if (!$id) {
    echo json_encode(["status" => "error", "message" => "Invalid ID"]);
    exit;
}

// Decode JWT
$user = null;
if (isset($_COOKIE['auth_token'])) {
    $user = JWT::decode($_COOKIE['auth_token']);
}
$user_id = $user['login_id'] ?? 0;
if (!$user_id) {
    echo json_encode(["status" => "error", "message" => "User not logged in"]);
    exit;
}


// Delete only if address belongs to user
$res = mysqli_query($conn, "DELETE FROM customer_addresses WHERE id='$id' AND customer_id='$user_id'");
if ($res) {
    echo json_encode(["status" => "success"]);
} else {
    echo json_encode(["status" => "error", "message" => mysqli_error($conn)]);
}
