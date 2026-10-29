<?php
include 'config/db.php';

$queries = [
    "ALTER TABLE projects 
     ADD COLUMN base_price DECIMAL(10,2) DEFAULT 0.00 AFTER description,
     ADD COLUMN default_tax_percentage DECIMAL(5,2) DEFAULT 2.00 AFTER base_price,
     ADD COLUMN tax_percentage DECIMAL(5,2) DEFAULT 0.00 AFTER default_tax_percentage,
     ADD COLUMN discount DECIMAL(5,2) DEFAULT 0.00 AFTER tax_percentage,
     ADD COLUMN old_price DECIMAL(10,2) DEFAULT 0.00 AFTER discount,
     ADD COLUMN price DECIMAL(10,2) DEFAULT 0.00 AFTER old_price;",
    "ALTER TABLE cart ADD COLUMN item_type VARCHAR(20) DEFAULT 'product' AFTER product_id;",
    "ALTER TABLE order_items ADD COLUMN item_type VARCHAR(20) DEFAULT 'product' AFTER product_id;"
];

foreach ($queries as $q) {
    if (mysqli_query($conn, $q)) {
        echo "Success: " . substr($q, 0, 50) . "...\n";
    } else {
        echo "Error or Already Exists: " . mysqli_error($conn) . " on query: " . $q . "\n";
    }
}
echo "Done.\n";
