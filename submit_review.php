<?php
session_start();

header('Content-Type: application/json');

include 'config/db.php';
require_once 'secure_helpers.php';

$user_id = (int)($_SESSION['user_id'] ?? 0);

if ($user_id <= 0) {

  echo json_encode([
    'status' => 'error',
    'message' => 'Login required'
  ]);

  exit;
}

$product_id = (int)($_POST['product_id'] ?? 0);

$rating = (int)($_POST['rating'] ?? 0);

$review = trim($_POST['review'] ?? '');

if ($product_id <= 0 || $rating < 1 || $rating > 5 || $review == '') {

  echo json_encode([
    'status' => 'error',
    'message' => 'Please fill all fields'
  ]);

  exit;
}

$user = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT name 
    FROM users 
    WHERE id='$user_id'
    LIMIT 1
"));

$customer_name = mysqli_real_escape_string(
  $conn,
  $user['name'] ?? 'Customer'
);

$review = mysqli_real_escape_string($conn, $review);

/*
|--------------------------------------------------------------------------
| CHECK ALREADY REVIEWED
|--------------------------------------------------------------------------
*/

$check = mysqli_query($conn, "
    SELECT id
    FROM product_reviews
    WHERE product_id='$product_id'
    AND user_id='$user_id'
    LIMIT 1
");

if (mysqli_num_rows($check)) {

  echo json_encode([
    'status' => 'error',
    'message' => 'You already reviewed this product'
  ]);

  exit;
}

/*
|--------------------------------------------------------------------------
| INSERT REVIEW
|--------------------------------------------------------------------------
*/

mysqli_query($conn, "
    INSERT INTO product_reviews
    (
        product_id,
        user_id,
        customer_name,
        rating,
        review,
        status,
        created_at
    )
    VALUES
    (
        '$product_id',
        '$user_id',
        '$customer_name',
        '$rating',
        '$review',
        1,
        NOW()
    )
");

/*
|--------------------------------------------------------------------------
| UPDATE PRODUCT RATING
|--------------------------------------------------------------------------
*/

$avgRes = mysqli_query($conn, "
    SELECT 
        AVG(rating) avg_rating,
        COUNT(*) total_reviews
    FROM product_reviews
    WHERE product_id='$product_id'
    AND status=1
");

$avgData = mysqli_fetch_assoc($avgRes);

$avg_rating = round((float)$avgData['avg_rating'], 1);

$total_reviews = (int)$avgData['total_reviews'];

mysqli_query($conn, "
    UPDATE products
    SET
        rating='$avg_rating',
        ratings_count='$total_reviews'
    WHERE id='$product_id'
");

echo json_encode([
  'status' => 'success',
  'message' => 'Review submitted successfully'
]);
