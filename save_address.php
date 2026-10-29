<?php
include "config/db.php";
include "jwt.php";

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

// Get POST data
$name       = mysqli_real_escape_string($conn, $_POST['name'] ?? '');
$phone      = mysqli_real_escape_string($conn, $_POST['phone'] ?? '');
$city       = mysqli_real_escape_string($conn, $_POST['district'] ?? '');
$state      = mysqli_real_escape_string($conn, $_POST['state'] ?? '');
$pincode    = mysqli_real_escape_string($conn, $_POST['pincode'] ?? '');
$type       = mysqli_real_escape_string($conn, $_POST['type'] ?? 'Home');
$is_default = isset($_POST['is_default']) && $_POST['is_default'] == 1 ? 1 : 0;

// If new default, reset previous default
if ($is_default) {
    mysqli_query($conn, "UPDATE customer_addresses SET is_default=0 WHERE customer_id='$user_id'");
}

// Insert new address
$sql = "INSERT INTO customer_addresses (customer_id,name,phone,city,state,pincode,type,is_default,created_at)
        VALUES ('$user_id','$name','$phone','$city','$state','$pincode','$type','$is_default',NOW())";

if (mysqli_query($conn, $sql)) {
    echo json_encode(["status" => "success"]);
} else {
    echo json_encode(["status" => "error", "message" => mysqli_error($conn)]);
}
    