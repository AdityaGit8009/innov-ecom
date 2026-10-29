<?php
include "config/db.php";
$newProducts = mysqli_query($conn, "SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON c.id = p.category_id WHERE p.status = 1 ORDER BY p.created_at DESC LIMIT 16");
$count = 0;
while ($p = mysqli_fetch_assoc($newProducts)) {
    if ($p['stock'] <= 0) {
        $count++;
    }
}
echo "Out of stock products in New Products: " . $count . "\n";
