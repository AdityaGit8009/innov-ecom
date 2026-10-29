<?php
require 'config/db.php';

function addColumnIfNotExists($conn, $table, $column, $definition) {
    $result = mysqli_query($conn, "SHOW COLUMNS FROM `$table` LIKE '$column'");
    if (mysqli_num_rows($result) == 0) {
        $sql = "ALTER TABLE `$table` ADD COLUMN `$column` $definition";
        if (mysqli_query($conn, $sql)) {
            echo "Success: Added $column to $table\n";
        } else {
            echo "Error adding $column to $table: " . mysqli_error($conn) . "\n";
        }
    } else {
        echo "Info: $column already exists in $table\n";
    }
}

// 1. Orders table updates for Refund System
addColumnIfNotExists($conn, 'orders', 'refund_status', "ENUM('None', 'Requested', 'Approved', 'Rejected', 'Refunded') DEFAULT 'None'");
addColumnIfNotExists($conn, 'orders', 'refund_reason', "TEXT NULL");

// 2. Products table updates for SEO
addColumnIfNotExists($conn, 'products', 'meta_title', "VARCHAR(255) NULL");
addColumnIfNotExists($conn, 'products', 'meta_description', "TEXT NULL");
addColumnIfNotExists($conn, 'products', 'seo_url', "VARCHAR(255) NULL");

// 3. Projects table updates for SEO
addColumnIfNotExists($conn, 'projects', 'meta_title', "VARCHAR(255) NULL");
addColumnIfNotExists($conn, 'projects', 'meta_description', "TEXT NULL");
addColumnIfNotExists($conn, 'projects', 'seo_url', "VARCHAR(255) NULL");

// 4. Categories table updates for SEO
addColumnIfNotExists($conn, 'categories', 'meta_title', "VARCHAR(255) NULL");
addColumnIfNotExists($conn, 'categories', 'meta_description', "TEXT NULL");
addColumnIfNotExists($conn, 'categories', 'seo_url', "VARCHAR(255) NULL");

echo "\nCompleted!\n";
?>
