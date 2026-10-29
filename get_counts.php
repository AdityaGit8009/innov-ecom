<?php
header('Content-Type: application/json');

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/jwt.php';
require_once __DIR__ . '/secure_helpers.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$user_id = ecom_user_id();

$cart_count = 0;
$wishlist_count = 0;
$compare_count = 0;

if ($user_id > 0) {

    $user_id = (int)$user_id;

    /* =========================
       CART COUNT
    ========================= */
    $cartRes = mysqli_query($conn, "
        SELECT COUNT(c.id) AS cnt
        FROM cart c
        INNER JOIN products p
        ON p.id = c.product_id
        WHERE c.user_id = '$user_id'
    ");

    if ($cartRes && mysqli_num_rows($cartRes) > 0) {
        $cart_count = (int)(mysqli_fetch_assoc($cartRes)['cnt'] ?? 0);
    }

    /* =========================
       WISHLIST COUNT
    ========================= */
    $wishRes = mysqli_query($conn, "
        SELECT COUNT(w.id) AS cnt
        FROM wishlist w
        INNER JOIN products p
        ON p.id = w.product_id
        WHERE w.user_id = '$user_id'
    ");

    if ($wishRes && mysqli_num_rows($wishRes) > 0) {
        $wishlist_count = (int)(mysqli_fetch_assoc($wishRes)['cnt'] ?? 0);
    }

    /* =========================
       COMPARE COUNT
    ========================= */
    $compRes = mysqli_query($conn, "
        SELECT COUNT(cmp.id) AS cnt
        FROM compare cmp
        INNER JOIN products p
        ON p.id = cmp.product_id
        WHERE cmp.user_id = '$user_id'
    ");

    if ($compRes && mysqli_num_rows($compRes) > 0) {
        $compare_count = (int)(mysqli_fetch_assoc($compRes)['cnt'] ?? 0);
    }
}

/* =========================
   JSON RESPONSE
========================= */
echo json_encode([
    'cart'     => $cart_count,
    'wishlist' => $wishlist_count,
    'compare'  => $compare_count
]);
