<?php
require 'config/db.php';
mysqli_query($conn, "DELETE FROM cart WHERE user_id='' OR user_id=0");
mysqli_query($conn, "DELETE FROM wishlist WHERE user_id='' OR user_id=0");
mysqli_query($conn, "DELETE FROM compare WHERE user_id='' OR user_id=0");
echo 'Cleaned up';
