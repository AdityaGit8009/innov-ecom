<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
header('Content-Type: application/json; charset=utf-8');
ini_set('display_errors', 0);
error_reporting(E_ALL);

require_once __DIR__ . '/../config/db.php';

try {
    $id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
    if ($id <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid product id.']);
        exit;
    }

    $sql = "SELECT p.id, p.category_id, p.sub_category_id, p.title, p.store, p.seller, p.base_price, p.default_tax_percentage, p.price, p.old_price, p.discount, p.tax_percentage, p.delivery_charge, p.rating, p.ratings_count, p.bought, p.stock, p.main_image, p.image1, p.image2, p.image3, p.image4, p.status, p.created_at, c.name AS category_name, s.name AS sub_category_name
            FROM products p
            LEFT JOIN categories c ON c.id = p.category_id
            LEFT JOIN sub_categories s ON s.id = p.sub_category_id
            WHERE p.id = ? AND p.status = 1
            LIMIT 1";

    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        echo json_encode(['status' => 'error', 'message' => 'Unable to load product.']);
        exit;
    }

    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);

    if (mysqli_stmt_num_rows($stmt) < 1) {
        echo json_encode(['status' => 'error', 'message' => 'Product not found.']);
        exit;
    }

    mysqli_stmt_bind_result(
        $stmt,
        $pid,
        $category_id,
        $sub_category_id,
        $title,
        $store,
        $seller,
        $base_price,
        $default_tax_percentage,
        $price,
        $old_price,
        $discount,
        $tax_percentage,
        $delivery_charge,
        $rating,
        $ratings_count,
        $bought,
        $stock,
        $main_image,
        $image1,
        $image2,
        $image3,
        $image4,
        $status,
        $created_at,
        $category_name,
        $sub_category_name
    );
    mysqli_stmt_fetch($stmt);

    $image = $main_image ?: ($image1 ?: 'no-image.png');
    $image_path = 'uploads/products/' . $image;
    if (!is_file(__DIR__ . '/../' . $image_path)) {
        $image_path = 'assets/images/no-image.png';
    }

    echo json_encode([
        'status' => 'success',
        'product' => [
            'id' => (int)$pid,
            'title' => (string)$title,
            'category' => (string)($category_name ?: $sub_category_name ?: 'Product'),
            'price' => number_format((float)$price, 2),
            'old_price' => (float)$old_price > 0 ? number_format((float)$old_price, 2) : '',
            'discount' => (float)$discount,
            'stock' => (int)$stock,
            'stock_text' => ((int)$stock > 0) ? 'In Stock (' . (int)$stock . ' left)' : 'Out of Stock',
            'rating' => (string)($rating ?: '5.0'),
            'bought' => (string)($bought ?: ''),
            'image' => $image_path,
            'url' => 'product.php?id=' . (int)$pid
        ]
    ]);
    exit;
} catch (Throwable $e) {
    error_log('Quick View Error: ' . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'Server error while loading product.']);
    exit;
}
