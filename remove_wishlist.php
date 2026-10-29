<?php
include "config/db.php";

$wid = $_POST['wid'] ?? 0;

if ($wid) {
    $del = mysqli_query($conn, "DELETE FROM wishlist WHERE id='$wid'");
    if (!$del) {
        echo "Error: " . mysqli_error($conn);
        exit;
    }

    echo "success";
}
