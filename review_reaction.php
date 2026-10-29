<?php

session_start();

include 'config/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
  echo json_encode([
    'status' => 'error',
    'message' => 'Login required'
  ]);
  exit;
}

$user_id = (int)$_SESSION['user_id'];

$review_id = (int)($_POST['review_id'] ?? 0);

$type = $_POST['type'] ?? '';

if (!$review_id || !in_array($type, ['like', 'dislike'])) {
  echo json_encode([
    'status' => 'error'
  ]);
  exit;
}

$check = mysqli_query(
  $conn,
  "SELECT id FROM review_reactions
     WHERE review_id='$review_id'
     AND user_id='$user_id'"
);

if (mysqli_num_rows($check)) {
  mysqli_query(
    $conn,
    "UPDATE review_reactions
         SET reaction='$type'
         WHERE review_id='$review_id'
         AND user_id='$user_id'"
  );
} else {
  mysqli_query(
    $conn,
    "INSERT INTO review_reactions
        (review_id,user_id,reaction)
        VALUES
        ('$review_id','$user_id','$type')"
  );
}

$likes = mysqli_num_rows(mysqli_query(
  $conn,
  "SELECT id FROM review_reactions
     WHERE review_id='$review_id'
     AND reaction='like'"
));

$dislikes = mysqli_num_rows(mysqli_query(
  $conn,
  "SELECT id FROM review_reactions
     WHERE review_id='$review_id'
     AND reaction='dislike'"
));

echo json_encode([

  'status' => 'success',

  'likes' => $likes,

  'dislikes' => $dislikes

]);
