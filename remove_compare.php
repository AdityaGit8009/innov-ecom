<?php
session_start();
include "config/db.php";

$user_id = $_SESSION['user_id'] ?? 0;
$compare_id = $_POST['compare_id'] ?? 0;

if ($user_id > 0 && $compare_id > 0) {

  mysqli_query(
    $conn,
    "DELETE FROM compare 
    WHERE id='$compare_id' AND user_id='$user_id'"
  );

  echo "success";
} else {
  echo "error";
}
